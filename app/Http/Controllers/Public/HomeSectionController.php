<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\HomeSectionResource;
use App\Services\HomeContent\HomeSectionService;
use Illuminate\Http\JsonResponse;

class HomeSectionController extends Controller
{
    public function __construct(
        private readonly HomeSectionService $homeSectionService,
    ) {}

    public function index(): JsonResponse
    {
        return $this->success(HomeSectionResource::collection($this->homeSectionService->listEnabledOrdered()));
    }
}
