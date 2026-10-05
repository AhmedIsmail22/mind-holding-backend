<?php

use App\Http\Controllers\Public\FaqController;
use App\Http\Controllers\Public\IndustryController;
use App\Http\Controllers\Public\ServiceController;
use App\Http\Controllers\Public\SettingsController;
use Illuminate\Support\Facades\Route;

// Public, unauthenticated, locale-resolved routes. Each module appends its
// own public routes here as it's built.

Route::get('settings', [SettingsController::class, 'show']);

Route::get('solution-industries', [IndustryController::class, 'index']);

Route::get('services', [ServiceController::class, 'index']);
Route::get('services/{slug}', [ServiceController::class, 'show']);

Route::get('faqs', [FaqController::class, 'index']);
