<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TravelRequestController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);

        Route::prefix('travel-requests')->group(function () {
            Route::get('/', [TravelRequestController::class, 'index']);
            Route::post('/', [TravelRequestController::class, 'store']);
            Route::get('/{id}', [TravelRequestController::class, 'show']);
            Route::patch('/{id}/status', [TravelRequestController::class, 'updateStatus']);
        });
    });
});
