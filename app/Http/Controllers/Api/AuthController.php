<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    public function __construct(
        protected AuthService $authService
    ) {}

    public function register(RegisterRequest $request)
    {
        // dd('REGISTER HIT', $request->all());
        // 1️⃣ buat user
        $user = $this->authService->register($request->validated());

        // 2️⃣ login-kan user tsb
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Register successfully',
            'token' => $token,

            // kirimkan data user di response register
            // 'user' => $user,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $token = $this->authService->login($request->validated());

        return response()->json([
            'message' => 'Login successfully',
            'token' => $token,
            // kirimkan data user di response login
            // 'user' => Auth::user(),
        ]);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return response()->json([
            'message' => 'Logout successfully',
        ]);
    }
}
