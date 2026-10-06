<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\PageResource;
use App\Services\Pages\PageService;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{
    public function __construct(
        private readonly PageService $pageService,
    ) {}

    public function show(string $slug): JsonResponse
    {
        return $this->success(new PageResource($this->pageService->findBySlug($slug)));
    }
}
