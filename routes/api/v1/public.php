<?php

use App\Http\Controllers\Public\FaqController;
use App\Http\Controllers\Public\HomeContentController;
use App\Http\Controllers\Public\HomeSectionController;
use App\Http\Controllers\Public\IndustryController;
use App\Http\Controllers\Public\LeadController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\ProjectController;
use App\Http\Controllers\Public\RedirectController;
use App\Http\Controllers\Public\SeoController;
use App\Http\Controllers\Public\ServiceController;
use App\Http\Controllers\Public\SettingsController;
use App\Http\Controllers\Public\SitemapController;
use App\Http\Controllers\Public\SolutionController;
use App\Http\Controllers\Public\TechnologyController;
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

Route::get('home', [HomeContentController::class, 'show']);
Route::get('home/sections', [HomeSectionController::class, 'index']);

Route::get('technologies', [TechnologyController::class, 'index']);

Route::get('pages/{slug}', [PageController::class, 'show'])->where('slug', '[^/]+');

Route::middleware(['throttle:leads'])->prefix('leads')->group(function () {
    Route::post('quote', [LeadController::class, 'quote']);
    Route::post('demo', [LeadController::class, 'demo']);
    Route::post('callback', [LeadController::class, 'callback']);
});

Route::get('seo/{key}', [SeoController::class, 'route']);
Route::get('sitemap', [SitemapController::class, 'index']);
Route::get('redirects', [RedirectController::class, 'index']);
Route::get('redirects/resolve', [RedirectController::class, 'resolve']);
