<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\TechnologyGroupResource;
use App\Models\Technology;
use App\Services\Technologies\TechnologyService;
use App\Support\Translations\OptionalTranslation;
use Illuminate\Http\JsonResponse;

class TechnologyController extends Controller
{
    public function __construct(
        private readonly TechnologyService $technologyService,
    ) {}

    public function index(): JsonResponse
    {
        $groups = $this->technologyService->list()
            ->groupBy(fn (Technology $technology) => OptionalTranslation::text($technology, 'category'))
            ->map(fn ($items, $category) => ['category' => $category, 'items' => $items->values()])
            ->values();

        return $this->success(TechnologyGroupResource::collection($groups));
    }
}
