<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SmsLogsController;
use App\Http\Controllers\SmsSettingsController;
use App\Http\Controllers\ExpenseController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware(['auth', 'check.user.status'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::middleware(['restrict.role:admin'])->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::delete('/payments/{id}', [PaymentController::class, 'revertPayment'])->name('payments.revert');
        Route::delete('/bills/{id}', [BillController::class, 'revertBill'])->name('bills.revert');
    });

    Route::resource('clients', ClientController::class);
    Route::resource('packages', PackageController::class)->middleware('restrict.role:admin');

    Route::resource('expenses', ExpenseController::class)->only(['index', 'store']);
    Route::resource('expenses', ExpenseController::class)
        ->only(['edit', 'update', 'destroy'])
        ->middleware('can.manage.expense');

    Route::post('/payments/{id}', [PaymentController::class, 'store'])->name('payments.store');
    Route::post('/bills/generate/{clientId}', [BillController::class, 'generate'])->name('bills.generate');
});

Route::middleware(['auth'])->group(function () {
    Route::prefix('sms')->group(function () {
        Route::get('/settings', [SmsSettingsController::class, 'index'])->name('sms.settings');
        Route::post('/settings', [SmsSettingsController::class, 'store'])->name('sms.settings.store');
        Route::post('/templates', [SmsSettingsController::class, 'storeTemplate'])->name('sms.templates.store');
        Route::put('/templates/{template}', [SmsSettingsController::class, 'updateTemplate'])->name('sms.templates.update');
        Route::delete('/templates/{template}', [SmsSettingsController::class, 'destroyTemplate'])->name('sms.templates.destroy');
        Route::get('/sms/logs', [SmsLogsController::class, 'index'])->name('sms.logs');
    });
});

require __DIR__ . '/auth.php';
