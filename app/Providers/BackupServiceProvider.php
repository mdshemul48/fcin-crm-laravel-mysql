<?php

namespace App\Providers;

use App\Services\DatabaseBackupService;
use Illuminate\Support\ServiceProvider;

class BackupServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(DatabaseBackupService::class, function ($app) {
            return new DatabaseBackupService();
        });
    }
}
