<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomeContent\UpdateHomeSectionsRequest;
use App\Http\Resources\Admin\HomeSectionResource;
use App\Services\HomeContent\HomeSectionService;
use Illuminate\Http\JsonResponse;

class HomeSectionController extends Controller
{
    public function __construct(
        private readonly HomeSectionService $homeSectionService,
    ) {}

    public function index(): JsonResponse
    {
        return $this->success(HomeSectionResource::collection($this->homeSectionService->listOrdered()));
    }

    public function update(UpdateHomeSectionsRequest $request): JsonResponse
    {
        $this->homeSectionService->bulkUpdate($request->validated('sections'));

        return $this->success(HomeSectionResource::collection($this->homeSectionService->listOrdered()), 'Home sections updated.');
    }
}
