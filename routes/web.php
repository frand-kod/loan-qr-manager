<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DebtController;
use App\Http\Controllers\Web\PaymentController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('webhook/tripay', [PaymentController::class, 'handleWebhook'])
    ->name('payments.webhook');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');

    // 3. MODUL DEBT (Hutang)
    Route::resource('debts', DebtController::class);
    Route::patch('debts/{debt}/status', [DebtController::class, 'updateStatus'])
        ->name('debts.update-status');

    Route::post('debts/{id}/send-reminder', [DebtController::class, 'sendReminder'])->name('debts.send-reminder');
    Route::post('whatsapp/test-connection', [DebtController::class, 'testWhatsapp'])->name('whatsapp.test');
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('debts/{debt}/pay-qris', [PaymentController::class, 'payQris'])->name('debts.pay-qris');
});
