<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DebtController;
use App\Http\Controllers\Api\PaymentController;
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
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);

    // Debts
    Route::get('/debts', [DebtController::class, 'index']);
    Route::get('/debts/{debt}', [DebtController::class, 'show']);
    Route::post('/debts', [DebtController::class, 'store']);
    Route::put('/debts/{debt}', [DebtController::class, 'update']);
    Route::delete('/debts/{debt}', [DebtController::class, 'destroy']);

    // Customers
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::post('/customers', [CustomerController::class, 'store']);
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Payments
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::get('/payments/statistics', [PaymentController::class, 'statistics']);
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::get('/payments/{payment}', [PaymentController::class, 'show']);
    Route::put('/payments/{payment}', [PaymentController::class, 'update']);
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy']);

});
