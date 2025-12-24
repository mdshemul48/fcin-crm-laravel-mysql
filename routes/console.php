<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\DatabaseBackupService;
use App\Services\BillingService;
use App\Models\GeneratedBill;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::call(function () {
    app(DatabaseBackupService::class)->createBackup();
})->dailyAt('01:20')
    ->sendOutputTo(storage_path('logs/backup.log'));

// Check daily if we need to generate bills
// Bills are generated on the 1st day of each month
Schedule::call(function () {
    $now = Carbon::now();
    $billingDate = $now->copy()->startOfMonth();
    
    // Only generate bills on the 1st day of the month
    if ($now->day != 1) {
        return;
    }
    
    // We no longer check globally if bills exist for this period
    // Instead, the BillingService will check for each client individually
    
    // Generate bills for all clients who don't have one yet
    // Using direct service call instead of Artisan
    app(BillingService::class)->generateMonthlyBills(1); // Using admin ID 1
    \Log::info('Monthly billing process initiated for ' . $billingDate->format('F Y') . ' at 00:05');
    
})->dailyAt('00:05')
    ->appendOutputTo(storage_path('logs/billing_check.log'));

// Additional check at 12:30 on the 1st to catch any clients that might have been missed
Schedule::call(function () {
    $now = Carbon::now();
    $billingDate = $now->copy()->startOfMonth();
    
    // Only generate bills on the 1st day of the month
    if ($now->day != 1) {
        return;
    }
    
    // Generate bills for all clients who don't have one yet
    // Using direct service call instead of Artisan
    app(BillingService::class)->generateMonthlyBills(1); // Using admin ID 1
    \Log::info('Monthly billing process initiated for ' . $billingDate->format('F Y') . ' at 12:30');
    
})->dailyAt('12:30')
    ->appendOutputTo(storage_path('logs/billing_check_noon.log'));
