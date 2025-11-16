<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * Attempts to log in a user and generate a token.
     *
     * @param array $credentials The login credentials (email, password).
     * @return array The access token and token type, or false on failure.
     */
    public function attemptLogin(array $credentials): array|bool
    {
        // 1. Attempt Authentication
        if (!Auth::attempt($credentials)) {
            return false;
        }

        // 2. Retrieve the User (Auth::attempt automatically gets the user, but we'll re-fetch for token creation)
        // Note: Auth::attempt is often sufficient, but for explicit token creation (Sanctum/Passport),
        // we often need the model instance. Using the passed email is efficient here.
        $user = User::where('email', $credentials['email'])->firstOrFail();

        // 3. Generate the token
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. Return the response data
        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }
}
