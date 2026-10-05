<?php

use App\Http\Controllers\Public\SettingsController;
use Illuminate\Support\Facades\Route;

// Public, unauthenticated, locale-resolved routes. Each module appends its
// own public routes here as it's built.

Route::get('settings', [SettingsController::class, 'show']);
