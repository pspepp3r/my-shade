<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function(){
    Route::post('/register', [AuthController::class, 'register'])->name('api.v1.register');
    Route::post('/login', [AuthController::class, 'login'])->name('api.v1.register');

    Route::middleware('auth:sanctum')->group(function(){
        Route::apiResource('/products', ProductController::class);
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.v1.logout');
    });
});
