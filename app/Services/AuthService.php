<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function attemptLogin(array $credentials): array|bool
    {
        if (!Auth::attempt($credentials)) {
            return false;
        }

        $user = User::where('email', $credentials['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }
    
    public function logoutCurrentUser(): void
    {
        $user = Auth::user();
        if ($user) {
            $user->tokens()->delete();
        }
    }
}
