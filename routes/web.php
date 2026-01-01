<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/anu', function () {
//     dd('Dari web router');
// });
Route::get('/', function () {
    return redirect('/login');
});
Route::get('/login', function () {
    return Inertia::render('Auth/Login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
});

Route::get('/customers', function () {
    return Inertia::render('Customers');
});

Route::get('/debts', function () {
    return Inertia::render('Debts');
});

Route::get('/payments', function () {
    return Inertia::render('Payments');
});
