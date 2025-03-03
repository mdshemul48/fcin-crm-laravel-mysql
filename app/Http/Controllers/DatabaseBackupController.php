<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class DatabaseBackupController extends Controller
{
    public function backup()
    {
        try {
            // Create backup directory if it doesn't exist
            if (!Storage::disk('private')->exists('backup')) {
                Storage::disk('private')->makeDirectory('backup');
            }

            $tables = DB::select('SHOW TABLES');
            $output = '';

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

            $filename = "backup-" . date('Y-m-d_H-i-s');
            $sqlFile = $filename . ".sql";
            $zipFile = $filename . ".zip";

            // Save SQL file
            Storage::disk('private')->put("backup/$sqlFile", $output);

            // Create ZIP file
            $zip = new ZipArchive();
            $zipPath = storage_path("app/private/backup/$zipFile");

            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                $zip->addFile(storage_path("app/private/backup/$sqlFile"), $sqlFile);
                $zip->close();

                // Delete the SQL file after zipping
                Storage::disk('private')->delete("backup/$sqlFile");
            }

            // Get all backup files and sort by creation time
            $files = Storage::disk('private')->files('backup');
            rsort($files);

            // Keep only the last 3 backups
            foreach (array_slice($files, 3) as $oldFile) {
                Storage::disk('private')->delete($oldFile);
            }

            // Send latest backup via email with error handling
            try {
                $backupEmail = env('BACKUP_EMAIL', 'mdshemul480@gmail.com');

                Mail::raw('Latest database backup attached (compressed).', function ($message) use ($zipFile, $backupEmail) {
                    $message->to($backupEmail)
                        ->subject('Database Backup - ' . date('Y-m-d'))
                        ->attach(storage_path("app/private/backup/$zipFile"));
                });
                $emailStatus = "Email sent successfully to {$backupEmail}";
            } catch (\Exception $emailError) {
                Log::error('Email sending failed: ' . $emailError->getMessage());
                $emailStatus = 'Email sending failed: ' . $emailError->getMessage();
            }

            return response()->json([
                'message' => 'Backup created and compressed successfully',
                'email_status' => $emailStatus,
                'file' => $zipPath,
                'kept_files' => array_slice($files, 0, 3)
            ]);
        } catch (\Exception $e) {
            Log::error('Backup failed: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
