<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::prefix('/v1')->group(function(){
    Route::post('/register', [AuthController::class, 'register'])->name('api.v1.register');
    Route::post('/login', [AuthController::class, 'login'])->name('api.v1.register');
});
