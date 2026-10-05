<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('public')->group(base_path('routes/api/v1/public.php'));

    // auth:sanctum is NOT applied blanket here: the login route lives under
    // this same "admin" prefix but must be reachable unauthenticated. Each
    // module's routes inside admin.php wraps itself in ->middleware(['auth:sanctum'])
    // (and 'permission:xxx' where relevant) except the login route itself.
    Route::prefix('admin')->group(base_path('routes/api/v1/admin.php'));
});
