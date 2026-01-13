<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Web\AuthService; // Import Service
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuthController extends Controller
{
    protected $authService;

    // Suntikkan Service ke dalam Controller
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Serahkan logika percobaan login ke Service
        $this->authService->login($credentials, $request->boolean('remember'));

        return redirect()->intended('dashboard');
    }

    public function showRegister()
    {
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Serahkan logika pembuatan user ke Service
        $this->authService->register($validated);

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        // Serahkan logika logout ke Service
        $this->authService->logout();

        return redirect('/login');
    }
}
