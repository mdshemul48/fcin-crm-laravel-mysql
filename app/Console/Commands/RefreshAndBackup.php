<?php

namespace App\Console\Commands;

use ZipArchive;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class RefreshAndBackup extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Create and email database backup';

    protected function createDatabaseBackup()
    {
        // Create backup directory if it doesn't exist
        if (!Storage::disk('private')->exists('backup')) {
            Storage::disk('private')->makeDirectory('backup');
        }

        $tables = DB::select('SHOW TABLES');
        $output = '';

        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            $createTableSql = DB::select("SHOW CREATE TABLE `$tableName`");
            $output .= "\n\n" . array_values((array)$createTableSql[0])[1] . ";\n\n";

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

    public function handle()
    {
        Cache::put('command_status', ['status' => 'Running', 'message' => 'Starting backup process...']);

        try {
            // Create and compress database backup
            $filename = "backup-" . date('Y-m-d_H-i-s');
            $sqlFile = $filename . ".sql";
            $zipFile = $filename . ".zip";

            $this->info('Creating database backup...');
            $output = $this->createDatabaseBackup();
            Storage::disk('private')->put("backup/$sqlFile", $output);

            // Create ZIP file
            $zip = new ZipArchive();
            $zipPath = storage_path("app/private/backup/$zipFile");

            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                $zip->addFile(storage_path("app/private/backup/$sqlFile"), $sqlFile);
                $zip->close();
                Storage::disk('private')->delete("backup/$sqlFile");
            }

            // Send email
            $backupEmail = env('BACKUP_EMAIL', 'mdshemul480@gmail.com');
            Mail::raw('Latest database backup attached (compressed).', function ($message) use ($zipFile, $backupEmail) {
                $message->to($backupEmail)
                    ->subject('Database Backup - ' . date('Y-m-d'))
                    ->attach(storage_path("app/private/backup/$zipFile"));
            });

            // Cleanup old backups
            $localFiles = Storage::disk('private')->files('backup');
            rsort($localFiles);
            foreach (array_slice($localFiles, 3) as $oldFile) {
                Storage::disk('private')->delete($oldFile);
            }

            Cache::put('command_status', ['status' => 'Success', 'message' => 'Backup completed and sent via email']);
            $this->info('Backup process completed successfully.');
            return 0;
        } catch (\Exception $e) {
            Cache::put('command_status', ['status' => 'Failed', 'message' => $e->getMessage()]);
            $this->error($e->getMessage());
            return 1;
        }
    }
}
