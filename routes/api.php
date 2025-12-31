<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
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
// webhook
Route::post('/webhook/tripay', [WebhookController::class, 'handleTripay']);

// Protected route with auth
Route::middleware('auth:sanctum')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Debts
    Route::get('/debts', [DebtController::class, 'index']);
    Route::get('/debts/{debt}', [DebtController::class, 'show']);
    Route::post('/debts', [DebtController::class, 'store']);
    Route::delete('/debts/{debt}', [DebtController::class, 'destroy']);

    // Customers
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::post('/customers', [CustomerController::class, 'store']);
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

});
