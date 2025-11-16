<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Services\AuthService;
use App\Services\RegistrationService;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Response;
use PHPUnit\Metadata\Api\Groups;

class AuthController
{
    public function __construct()
    {
    }

    /**
     * Register
     *
     * Register a new user and generate an access token.
     */
    #[Groups(['Authentication'])]
    #[BodyParam(name: 'name', type: 'string', required: true, description: 'The user\'s full name.', example: 'Jane Doe')]
    #[BodyParam(name: 'email', type: 'string', required: true, description: 'The user\'s email address. Must be unique.', example: 'jane.doe@example.com')]
    #[BodyParam(name: 'password', type: 'string', required: true, description: 'The desired password (min 6 characters).', example: 'securePassword123')]
    #[BodyParam(name: 'password_confirmation', type: 'string', required: true, description: 'Confirmation of the password.', example: 'securePassword123')]
    #[Response(
        content: [
            'access_token' => '24|T1tD1yL6wE7bF8wY3zP0gC4kH9nM5vR2q',
            'token_type' => 'Bearer',
        ],
        status: 201,
        description: 'User successfully registered and token granted.'
    )]
    #[Response(
        content: ['message' => 'The given data was invalid.', 'errors' => ['email' => ['The email has already been taken.']]],
        status: 422,
        description: 'Validation failed.'
    )]
    public function register(RegistrationRequest $request, RegistrationService $registrationService)
    {
        $validatedData = $request->validated();

        $response = $registrationService->registerUser($validatedData);

        return response()->json($response, 201);
    }

    /**
     * Login
     *
     * Authenticate a user and return an access token.
     */
    #[Groups(['Authentication'])]
    #[BodyParam(name: 'email', type: 'string', required: true, description: 'The user\'s email address.', example: 'jade.doe@example.com')]
    #[BodyParam(name: 'password', type: 'string', required: true, description: 'The user\'s password.', example: 'securePassword123')]
    #[Response(
        content: [
            'access_token' => '51|A2pD3qJ4rT5uW6yZ7aB8cE9fG0hI1jK2',
            'token_type' => 'Bearer',
        ],
        status: 200,
        description: 'User successfully logged in and token granted.'
    )]
    #[Response(
        content: ['message' => 'Invalid login details'],
        status: 401,
        description: 'Authentication failed (Invalid credentials).'
    )]
    public function login(LoginRequest $request, AuthService $authService)
    {
        // 1. Get validated data
        $credentials = $request->validated();

        // 2. Delegate business logic to the service
        $response = $authService->attemptLogin($credentials);

        // 3. Handle service response
        if ($response === false) {
            // Service returns false if authentication fails
            return response()->json(['message' => 'Invalid login details'], 401);
        }

        // 4. Return success response
        return response()->json($response);
    }
}

