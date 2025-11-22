<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Travel Request System API',
        'version' => '1.0',
        'endpoints' => [
            'documentation' => url('/api/documentation'),
            'register' => url('/api/v1/register'),
            'login' => url('/api/v1/login'),
        ]
    ]);
});
