<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function login(array $data): string
    {
        if (! Auth::attempt($data)) {
            throw ValidationException::withMessages([
                'email' => ['Credentials invalid'],
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        // hapus token lama (1 device policy)
        $user->tokens()->delete();

        // buat token baru
        return $user->createToken('api-token')->plainTextToken;
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
