<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\ServiceListResource;
use App\Http\Resources\Public\ServiceResource;
use App\Services\Services\ServiceService;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    public function __construct(
        private readonly ServiceService $serviceService,
    ) {}

    public function index(): JsonResponse
    {
        $grouped = $this->serviceService->listPublishedGrouped();

        return $this->success([
            'software' => ServiceListResource::collection($grouped['software']),
            'marketing' => ServiceListResource::collection($grouped['marketing']),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $service = $this->serviceService->findPublishedBySlug($slug);
        $service->load(['faqs' => fn ($query) => $query->where('is_published', true)->orderBy('order')]);

        return $this->success(new ServiceResource($service));
    }
}
