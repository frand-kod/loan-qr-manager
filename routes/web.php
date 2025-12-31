<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/anu', function () {
//     dd('Dari web router');
// });
Route::get('/login', function () {
    // dd('Dari web router');
    return Inertia::render('Auth/Login');
});
