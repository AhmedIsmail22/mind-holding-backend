<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\HomeContentResource;
use App\Services\HomeContent\HomeContentService;
use Illuminate\Http\JsonResponse;

class HomeContentController extends Controller
{
    public function __construct(
        private readonly HomeContentService $homeContentService,
    ) {}

    public function show(): JsonResponse
    {
        return $this->success(new HomeContentResource($this->homeContentService->current()));
    }
}
