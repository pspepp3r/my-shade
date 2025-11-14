<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/**
 * API Version 1
 *
 * @group API Version 1
 */
Route::prefix('/v1')->group(function () {
    /**
     * API Version 1 Index
     *
     * A simple endpoint to verify that you have reached the API.
     *
     * @response 200 scenario="Successful response" {"message": "You have hit API version 1"}
     */
    Route::get('/', fn() => ['message' => 'You have hit API version 1'])->name('api.vi.index');
});

/**
 * Healthcheck
 *
 * Check that the service is up. If everything is okay, you'll get a 200 OK response.
 *
 * Otherwise, the request will fail with a 400 error, and a response listing the failed services.
 *
 * @response 400 scenario="Service is unhealthy" {"status": "down", "services": {"database": "up", "redis": "down"}}
 * @responseField status The status of this API (`up` or `down`).
 * @responseField services Map of each downstream service and their status (`up` or `down`).
 */
Route::get('/healthcheck', fn() => [
    'status' => 'up',
    'services' => [
        'database' => 'up',
        'redis' => 'up',
    ],
]);
