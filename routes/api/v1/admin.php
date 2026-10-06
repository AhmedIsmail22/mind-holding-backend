<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\HomeContentController;
use App\Http\Controllers\Admin\HomeSectionController;
use App\Http\Controllers\Admin\IndustryController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ServiceFaqController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SolutionController;
use App\Http\Controllers\Admin\SolutionFaqController;
use App\Http\Controllers\Admin\TechnologyController;
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

Route::middleware(['auth:sanctum', 'permission:settings.manage'])->group(function () {
    Route::get('settings', [SettingsController::class, 'show']);
    // POST, not PUT: PHP does not populate $_FILES for PUT requests, and
    // this endpoint accepts a multipart logo upload.
    Route::post('settings', [SettingsController::class, 'update']);
});

Route::middleware(['auth:sanctum', 'permission:solution-industries.manage'])->group(function () {
    Route::apiResource('solution-industries', IndustryController::class);
});

Route::middleware(['auth:sanctum', 'permission:services.manage'])->group(function () {
    Route::apiResource('services', ServiceController::class);
});

Route::middleware(['auth:sanctum', 'permission:faqs.manage'])->group(function () {
    Route::apiResource('faqs', FaqController::class);
    Route::apiResource('services.faqs', ServiceFaqController::class);
    Route::apiResource('solutions.faqs', SolutionFaqController::class);
});

Route::middleware(['auth:sanctum', 'permission:solutions.manage'])->group(function () {
    Route::post('solutions/reorder', [SolutionController::class, 'reorder']);
    // apiResource's "update" is excluded: it accepts multipart mockup
    // uploads, so update uses POST (PHP doesn't populate $_FILES on PUT).
    Route::apiResource('solutions', SolutionController::class)->except(['update']);
    Route::post('solutions/{solution}', [SolutionController::class, 'update']);
});

Route::middleware(['auth:sanctum', 'permission:work.manage'])->group(function () {
    Route::apiResource('projects', ProjectController::class)->except(['update']);
    Route::post('projects/{project}', [ProjectController::class, 'update']);
});

Route::middleware(['auth:sanctum', 'permission:home-content.manage'])->group(function () {
    Route::get('home', [HomeContentController::class, 'show']);
    // POST, not PUT: the hero image upload needs multipart (see CLAUDE.md).
    Route::post('home', [HomeContentController::class, 'update']);

    Route::get('home/sections', [HomeSectionController::class, 'index']);
    Route::post('home/sections', [HomeSectionController::class, 'update']);

    Route::apiResource('technologies', TechnologyController::class)->except(['update']);
    Route::post('technologies/{technology}', [TechnologyController::class, 'update']);
});

Route::middleware(['auth:sanctum', 'permission:pages.manage'])->group(function () {
    Route::get('pages/{slug}', [PageController::class, 'show'])->whereIn('slug', ['about', 'privacy', 'terms']);
    Route::put('pages/{slug}', [PageController::class, 'update'])->whereIn('slug', ['about', 'privacy', 'terms']);
});
