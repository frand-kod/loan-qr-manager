<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DebtController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

// check
Route::get('/ping', function () {
    return response()->json(['status' => 'ok']);
});

// Public route
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected route with auth
Route::middleware('auth:sanctum')->group(function () {

    // all with middleware
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/debts', [DebtController::class, 'index']);
    Route::post('/debts', [DebtController::class, 'store']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/webhook/tripay', [WebhookController::class, 'handleTripay']);
});
