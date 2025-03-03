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
        $output = '';
        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];

            // Get Create Table Syntax
            $createTableSql = DB::select("SHOW CREATE TABLE `$tableName`");
            $output .= "\n\n" . array_values((array)$createTableSql[0])[1] . ";\n\n";

            // Get Table Data
            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $row = (array)$row;
                $columns = array_map(function ($value) {
                    return is_null($value) ? "NULL" : "'" . addslashes($value) . "'";
                }, $row);

                $output .= "INSERT INTO `$tableName` VALUES (" . implode(", ", $columns) . ");\n";
            }
        }

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
