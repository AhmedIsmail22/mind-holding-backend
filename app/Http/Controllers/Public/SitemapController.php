<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Seo\SitemapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function __construct(
        private readonly SitemapService $sitemapService,
    ) {}

    public function index(): JsonResponse
    {
        // Short cache: the sitemap is read by crawlers, not by visitors, so a
        // few minutes of staleness after an edit is acceptable.
        return $this->success(Cache::remember('sitemap.entries', 600, fn () => $this->sitemapService->entries()));
    }
}
