<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\DatabaseBackupService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::call(function () {
    app(DatabaseBackupService::class)->createBackup();
})->dailyAt('21:11')
    ->sendOutputTo(storage_path('logs/backup.log'));
