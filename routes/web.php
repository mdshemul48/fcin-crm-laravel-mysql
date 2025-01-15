<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view("dashboard");
    })->name('dashboard');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});



require __DIR__ . '/auth.php';
