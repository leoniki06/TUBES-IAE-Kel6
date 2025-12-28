<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => 'OK',
        'service' => 'Library Backend API',
    ]);
})->withoutMiddleware(['auth', 'auth:api']);
