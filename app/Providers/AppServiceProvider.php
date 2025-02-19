<?php

namespace App\Providers;

use App\Services\BillingService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Services\SmsService;
use Illuminate\Support\Facades\Cache;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BillingService::class, function () {
            return new BillingService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        View::composer('*', function ($view) {
            $smsBalance = Cache::remember('sms_balance', 300, function () {
                $smsService = new SmsService();
                return $smsService->getBalance();
            });

            $view->with('smsBalance', $smsBalance);
        });
    }
}
