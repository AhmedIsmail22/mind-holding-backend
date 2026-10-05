<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('public')->group(base_path('routes/api/v1/public.php'));
    Route::prefix('admin')->middleware(['auth:sanctum'])->group(base_path('routes/api/v1/admin.php'));
});
