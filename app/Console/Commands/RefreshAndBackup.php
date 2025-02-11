<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;

class RefreshAndBackup extends Command
{
    protected $signature = 'dropbox:refresh-and-backup';
    protected $description = 'Refresh Dropbox token and run backup commands';

    public function handle()
    {
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
        } else {
            $this->error('Failed to refresh Dropbox access token.');
            return 1;
        }

        // Run backup commands
        $this->info('Running backup:run...');
        Artisan::call('backup:run');
        $this->info(Artisan::output());

        $this->info('Running backup:clean...');
        Artisan::call('backup:clean');
        $this->info(Artisan::output());

        $this->info('Backup process completed successfully.');
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
