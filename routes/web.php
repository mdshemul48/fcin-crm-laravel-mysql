<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view("dashboard");
})->middleware("auth")->name("dashboard");

Route::get('login', [AuthenticatedSessionController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'store']);
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');



require __DIR__ . '/auth.php';
