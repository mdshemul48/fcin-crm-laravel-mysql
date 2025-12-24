<?php

namespace App\Services;

use ZipArchive;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class DatabaseBackupService
{
    protected $disk;

    public function __construct()
    {
        $this->disk = Storage::disk('private');
    }

    public function createBackup()
    {
        try {
            Cache::put('command_status', ['status' => 'Running', 'message' => 'Starting backup process...']);

            // Create backup directory if it doesn't exist
            if (!$this->disk->exists('backup')) {
                $this->disk->makeDirectory('backup');
            }

            // Create backup files
            $filename = "backup-" . date('Y-m-d_H-i-s');
            $sqlFile = $filename . ".sql";
            $zipFile = $filename . ".zip";

            // Generate SQL backup
            $output = $this->generateSqlDump();
            $this->disk->put("backup/$sqlFile", $output);

            // Create ZIP file
            $zipPath = storage_path("app/private/backup/$zipFile");
            $this->createZipFile($zipPath, "backup/$sqlFile", $sqlFile);

            // Clean up old backups
            $this->cleanupOldBackups();

            // Send email
            $this->sendBackupEmail($zipFile);

            Cache::put('command_status', ['status' => 'Success', 'message' => 'Backup completed and sent via email']);

            return [
                'success' => true,
                'message' => 'Backup created and sent successfully',
                'file' => $zipPath
            ];
        } catch (\Exception $e) {
            Log::error('Backup failed: ' . $e->getMessage());
            Cache::put('command_status', ['status' => 'Failed', 'message' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    protected function generateSqlDump()
    {
        // Try using mysqldump first (most reliable)
        $mysqldumpOutput = $this->tryMysqldump();
        if ($mysqldumpOutput !== false) {
            return $mysqldumpOutput;
        }

        // Fallback to PHP-based dump
        return $this->generatePhpSqlDump();
    }

    protected function tryMysqldump()
    {
        $config = config('database.connections.mysql');
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? 3306;
        $database = $config['database'] ?? '';
        $username = $config['username'] ?? '';
        $password = $config['password'] ?? '';

        // Skip mysqldump if no database configured
        if (empty($database) || empty($username)) {
            return false;
        }

        // Check if mysqldump is available
        $mysqldumpPath = null;
        $paths = ['/usr/bin/mysqldump', '/usr/local/bin/mysqldump', '/opt/homebrew/bin/mysqldump', '/usr/local/mysql/bin/mysqldump'];

        foreach ($paths as $path) {
            if (file_exists($path) && is_executable($path)) {
                $mysqldumpPath = $path;
                break;
            }
        }

        // If not found in common paths, try 'which'
        if (!$mysqldumpPath) {
            $whichOutput = shell_exec('which mysqldump 2>/dev/null');
            if ($whichOutput && file_exists(trim($whichOutput))) {
                $mysqldumpPath = trim($whichOutput);
            }
        }

        if (!$mysqldumpPath) {
            return false;
        }

        // Create a temporary file for the output
        $tempFile = tempnam(sys_get_temp_dir(), 'mysqldump_');

        // Build mysqldump command - redirect stderr to /dev/null
        $command = sprintf(
            'MYSQL_PWD=%s %s --host=%s --port=%s --user=%s --single-transaction --routines --triggers --skip-lock-tables %s > %s 2>/dev/null',
            escapeshellarg($password),
            escapeshellarg($mysqldumpPath),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($tempFile)
        );

        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($tempFile)) {
            $dumpContent = file_get_contents($tempFile);
            unlink($tempFile);

            // Verify it's valid SQL (should start with -- or SET or contain CREATE TABLE)
            if (!empty($dumpContent) && (
                strpos($dumpContent, '-- ') === 0 ||
                strpos($dumpContent, 'SET') !== false ||
                strpos($dumpContent, 'CREATE TABLE') !== false
            )) {
                return $dumpContent;
            }
        }

        // Clean up temp file if it exists
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }

        return false;
    }

    protected function generatePhpSqlDump()
    {
        $pdo = DB::connection()->getPdo();

        $output = "-- Database backup generated on " . date('Y-m-d H:i:s') . "\n";
        $output .= "-- Laravel Database Backup (PHP Method)\n\n";
        $output .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $output .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $output .= "SET time_zone = \"+00:00\";\n";
        $output .= "SET NAMES utf8mb4;\n\n";

        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];

            // Add drop table statement
            $output .= "DROP TABLE IF EXISTS `$tableName`;\n";

            // Get Create Table Syntax
            $createTableSql = DB::select("SHOW CREATE TABLE `$tableName`");
            $createStatement = array_values((array)$createTableSql[0])[1];
            $output .= $createStatement . ";\n\n";

            // Get Table Data using raw PDO for better handling
            $stmt = $pdo->query("SELECT * FROM `$tableName`");
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (count($rows) > 0) {
                // Get column names from first row
                $columnNames = array_keys($rows[0]);
                $columnList = "`" . implode("`, `", $columnNames) . "`";

                // Batch inserts for better performance
                $batchSize = 100;
                $rowBatch = [];

                foreach ($rows as $row) {
                    $values = [];

                    foreach ($row as $value) {
                        if (is_null($value)) {
                            $values[] = "NULL";
                        } else {
                            // Always quote values using PDO - MySQL will handle type conversion
                            $values[] = $pdo->quote($value);
                        }
                    }

                    $rowBatch[] = "(" . implode(", ", $values) . ")";

                    // Write batch insert when we reach batch size
                    if (count($rowBatch) >= $batchSize) {
                        $output .= "INSERT INTO `$tableName` ($columnList) VALUES\n" . implode(",\n", $rowBatch) . ";\n";
                        $rowBatch = [];
                    }
                }

                // Write any remaining rows
                if (count($rowBatch) > 0) {
                    $output .= "INSERT INTO `$tableName` ($columnList) VALUES\n" . implode(",\n", $rowBatch) . ";\n";
                }

                $output .= "\n";
            }
        }

        $output .= "SET FOREIGN_KEY_CHECKS=1;\n";

        return $output;
    }

    protected function createZipFile($zipPath, $sqlPath, $sqlFile)
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $zip->addFile(storage_path("app/private/$sqlPath"), $sqlFile);
            $zip->close();

            // Delete the SQL file after zipping
            $this->disk->delete($sqlPath);
        }
    }

    protected function cleanupOldBackups()
    {
        $files = $this->disk->files('backup');
        rsort($files);

        foreach (array_slice($files, 3) as $oldFile) {
            $this->disk->delete($oldFile);
        }
    }

    protected function sendBackupEmail($zipFile)
    {
        $backupEmail = env('BACKUP_EMAIL', 'mdshemul480@gmail.com');

        Mail::raw('Latest database backup attached (compressed).', function ($message) use ($zipFile, $backupEmail) {
            $message->to($backupEmail)
                ->subject('Database Backup - ' . date('Y-m-d'))
                ->attach(storage_path("app/private/backup/$zipFile"));
        });
    }
}
