<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {
    Route::get('/', fn() => ['status' => 'success']);
});
