<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view("dashboard");
});




require __DIR__ . '/auth.php';
