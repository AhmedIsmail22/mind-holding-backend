<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\SeoResource;
use App\Services\Seo\SeoService;
use Illuminate\Http\JsonResponse;

class SeoController extends Controller
{
    public function __construct(
        private readonly SeoService $seoService,
    ) {}

    public function route(string $key): JsonResponse
    {
        return $this->success(new SeoResource($this->seoService->findRoute($key)));
    }
}
