<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::get('/test', function () {
        return response()->json([
            'message' => 'Laravel est connecter'
        ]);
    });
});
