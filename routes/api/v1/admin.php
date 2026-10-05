<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Admin routes. Auth is NOT applied blanket at the prefix level (see
// routes/api.php) because the login route itself must stay unauthenticated.
// Each module below wraps itself in ->middleware(['auth:sanctum']) and, where
// relevant, 'permission:xxx'.

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::middleware(['auth:sanctum', 'permission:users.manage'])->group(function () {
    Route::apiResource('users', UserController::class);
});
