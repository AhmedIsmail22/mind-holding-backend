<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pages\UpdatePageRequest;
use App\Http\Resources\Admin\PageResource;
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

    public function update(UpdatePageRequest $request, string $slug): JsonResponse
    {
        $page = $this->pageService->findBySlug($slug);
        $page = $this->pageService->update($page, $request->toDto());

        return $this->success(new PageResource($page), 'Page updated.');
    }
}
