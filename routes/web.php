<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Inclure les routes API
require __DIR__ . '/api.php';
