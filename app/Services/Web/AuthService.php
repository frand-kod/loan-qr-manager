<?php

namespace App\Services\Web;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Menangani pendaftaran user baru.
     */
    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);

        return $user;
    }

    /**
     * Menangani proses login.
     */
    public function login(array $credentials, bool $remember = false): bool
    {
        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        session()->regenerate();

        return true;
    }

    /**
     * Menangani proses logout.
     */
    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }
}
