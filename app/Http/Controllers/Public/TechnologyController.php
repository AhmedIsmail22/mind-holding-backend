<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\TechnologyResource;
use App\Services\Technologies\TechnologyService;
use Illuminate\Http\JsonResponse;

class TechnologyController extends Controller
{
    public function __construct(
        private readonly TechnologyService $technologyService,
    ) {}

    public function index(): JsonResponse
    {
        $technologies = TechnologyResource::collection($this->technologyService->list())->resolve();

        $grouped = collect($technologies)
            ->groupBy('category')
            ->map(fn ($items, $category) => [
                'category' => $category,
                'items' => $items->map(fn ($item) => [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'logo_url' => $item['logo_url'],
                ])->values(),
            ])
            ->values();

        return $this->success($grouped);
    }
}
