<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomeContent\UpdateHomeContentRequest;
use App\Http\Resources\Admin\HomeContentResource;
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

    public function update(UpdateHomeContentRequest $request): JsonResponse
    {
        $content = $this->homeContentService->update($request->toDto());

        return $this->success(new HomeContentResource($content), 'Home content updated.');
    }
}
