<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class RefreshAndBackup extends Command
{
    protected $signature = 'dropbox:refresh-and-backup';
    protected $description = 'Refresh Dropbox token and run backup commands';

    public function handle()
    {
        Cache::put('command_status', ['status' => 'Running', 'message' => 'Refreshing Dropbox token...']);

        $this->info('Refreshing Dropbox token...');

        $response = Http::asForm()->post('https://api.dropboxapi.com/oauth2/token', [
            'refresh_token' => env('DROPBOX_REFRESH_TOKEN'),
            'grant_type' => 'refresh_token',
            'client_id' => env('DROPBOX_CLIENT_ID'),
            'client_secret' => env('DROPBOX_CLIENT_SECRET'),
        ]);

        if ($response->successful()) {
            $accessToken = $response->json('access_token');
            $this->updateEnvFile('DROPBOX_ACCESS_TOKEN', $accessToken);
            $this->info('Dropbox access token updated successfully.');
            Cache::put('command_status', ['status' => 'Running', 'message' => 'Dropbox access token updated successfully. Running backup...']);
        } else {
            $errorMessage = 'Failed to refresh Dropbox access token.';
            Cache::put('command_status', ['status' => 'Failed', 'message' => $errorMessage]);
            $this->error($errorMessage);
            return 1;
        }

        $backupErrors = [];

        $this->info('Running backup:run...');
        $exitRun = Artisan::call('backup:run');
        $this->info(Artisan::output());
        if ($exitRun !== 0) {
            $backupErrors[] = 'Backup run failed.';
        }

        $this->info('Starting manual cleanup...');
        try {
            $disk = Storage::disk('dropbox');
            $files = collect($disk->allFiles('/'))
                ->filter(function ($file) {
                    return str_contains($file, '.zip');
                })
                ->sort()
                ->values()
                ->toArray();

            if (count($files) > 3) {
                $filesToDelete = array_slice($files, 0, count($files) - 3);
                foreach ($filesToDelete as $file) {
                    $disk->delete($file);
                    $this->info("Deleted backup: {$file}");
                }
            }

            $this->info('Manual cleanup completed!');
        } catch (\Exception $e) {
            $backupErrors[] = 'Manual cleanup failed: ' . $e->getMessage();
        }

        if (!empty($backupErrors)) {
            $errorMessage = implode(' ', $backupErrors);
            Cache::put('backup_status', $errorMessage);
            Cache::forget('backup_info');
            Cache::put('command_status', ['status' => 'Failed', 'message' => $errorMessage]);
            $this->error($errorMessage);
        } else {
            Cache::forget('backup_status');

            // Get backup information using Storage facade
            $disk = Storage::disk('dropbox');
            $files = $disk->allFiles('/');

            // Find the latest backup file (they usually start with 'laravel-backup' or your app name)
            $latestBackup = collect($files)
                ->filter(function ($file) {
                    return str_contains($file, '.zip');
                })
                ->sort()
                ->last();

            if ($latestBackup) {
                $backupInfo = [
                    'date' => now()->format('Y-m-d H:i'),
                    'size' => $disk->size($latestBackup),
                    'formatted_size' => round($disk->size($latestBackup) / 1048576, 2) . ' MB',
                    'filename' => $latestBackup,
                    'disk' => 'dropbox'
                ];
                Cache::put('backup_info', $backupInfo);
                Cache::put('command_status', [
                    'status' => 'Success',
                    'message' => 'Backup completed successfully. Size: ' . $backupInfo['formatted_size']
                ]);

                // Get all backups from the last week
                $weeklyBackups = collect($files)
                    ->filter(function ($file) {
                        return str_contains($file, '.zip');
                    })
                    ->map(function ($file) use ($disk) {
                        return [
                            'date' => now()->format('Y-m-d H:i'),
                            'size' => $disk->size($file),
                            'formatted_size' => round($disk->size($file) / 1048576, 2) . ' MB',
                            'filename' => $file,
                        ];
                    })
                    ->values()
                    ->toArray();

                Cache::put('weekly_backups', $weeklyBackups);
            }

            $this->info('Backup process completed successfully.');
        }

        return 0;
    }

    protected function updateEnvFile($key, $value)
    {
        $envPath = base_path('.env');
        $contents = file_get_contents($envPath);
        $contents = preg_replace("/^$key=.*/m", "$key=$value", $contents);
        file_put_contents($envPath, $contents);
    }
}
