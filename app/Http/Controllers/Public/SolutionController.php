<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\SolutionListResource;
use App\Http\Resources\Public\SolutionResource;
use App\Services\Solutions\SolutionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SolutionController extends Controller
{
    public function __construct(
        private readonly SolutionService $solutionService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $solutions = $this->solutionService->listPublished($request->query('industry'));

        return $this->success(SolutionListResource::collection($solutions));
    }

    public function show(string $slug): JsonResponse
    {
        $solution = $this->solutionService->findPublishedBySlug($slug);
        $solution->load([
            'industry',
            'media',
            'faqs' => fn ($query) => $query->where('is_published', true)->orderBy('order'),
            'relatedSolutions' => fn ($query) => $query->where('is_published', true),
            'relatedSolutions.industry',
            'relatedSolutions.media',
        ]);

        return $this->success(new SolutionResource($solution));
    }
}
