<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seo\UpdateSeoRequest;
use App\Http\Resources\Admin\SeoMetaResource;
use App\Services\Seo\SeoService;
use Illuminate\Http\JsonResponse;

class SeoController extends Controller
{
    public function __construct(
        private readonly SeoService $seoService,
    ) {}

    public function show(string $type, string $key): JsonResponse
    {
        return $this->success(new SeoMetaResource($this->seoService->targetFor($type, $key)));
    }

    public function update(UpdateSeoRequest $request, string $type, string $key): JsonResponse
    {
        $meta = $this->seoService->update($this->seoService->targetFor($type, $key), $request->toDto());

        return $this->success(new SeoMetaResource($meta), 'SEO updated.');
    }
}
