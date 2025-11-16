<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegistrationService
{
    public function registerUser(array $data): array
    {
        // 1. Business Logic: Create the user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            // Hash the password within the service
            'password' => Hash::make($data['password']),
        ]);

        // 2. Business Logic: Generate the token
        $token = $user->createToken('auth_token')->plainTextToken;

        // 3. Prepare the final output
        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }
}
