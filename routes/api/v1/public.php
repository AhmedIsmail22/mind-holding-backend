<?php

use App\Http\Controllers\Public\FaqController;
use App\Http\Controllers\Public\IndustryController;
use App\Http\Controllers\Public\ProjectController;
use App\Http\Controllers\Public\ServiceController;
use App\Http\Controllers\Public\SettingsController;
use App\Http\Controllers\Public\SolutionController;
use Illuminate\Support\Facades\Route;

// Public, unauthenticated, locale-resolved routes. Each module appends its
// own public routes here as it's built.

Route::get('settings', [SettingsController::class, 'show']);

Route::get('solution-industries', [IndustryController::class, 'index']);

Route::get('services', [ServiceController::class, 'index']);
Route::get('services/{slug}', [ServiceController::class, 'show']);

Route::get('faqs', [FaqController::class, 'index']);

Route::get('solutions', [SolutionController::class, 'index']);
Route::get('solutions/{slug}', [SolutionController::class, 'show']);

Route::get('projects', [ProjectController::class, 'index']);
Route::get('projects/{project}', [ProjectController::class, 'show']);
