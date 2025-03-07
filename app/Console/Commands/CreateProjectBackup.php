<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZipArchive;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CreateProjectBackup extends Command
{
    protected $signature = 'project:backup';
    protected $description = 'Create a full project backup excluding .env and storage folder';

    public function handle()
    {
        // Create backups directory if it doesn't exist
        $backupDir = base_path('backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        // Clean up old backup files
        $this->cleanOldBackups($backupDir);

        $zip = new ZipArchive();
        $timestamp = date('Y-m-d-H-i-s');
        $zipFileName = $backupDir . "/backup-{$timestamp}.zip";

        // Delete existing backup if exists
        if (file_exists($zipFileName)) {
            unlink($zipFileName);
        }

        if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(base_path()),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            $excludePatterns = [
                '/\.env/',
                '/\/storage\//',
                '/\/backups\//',
                '/backup-.*\.zip$/',
            ];

            foreach ($files as $file) {
                if ($file->isDir()) {
                    continue;
                }

                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen(base_path()) + 1);

                // Check if file should be excluded
                $exclude = false;
                foreach ($excludePatterns as $pattern) {
                    if (preg_match($pattern, $filePath)) {
                        $exclude = true;
                        break;
                    }
                }

                if (!$exclude) {
                    $zip->addFile($filePath, $relativePath);
                }
            }

            $zip->close();
            $this->info("Project backup created successfully: {$zipFileName}");
        } else {
            $this->error('Failed to create backup zip file');
        }
    }

    private function cleanOldBackups($backupDir)
    {
        $files = glob($backupDir . '/backup-*.zip');
        if ($files) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            $this->info('Old backup files cleaned up.');
        }
    }
}
