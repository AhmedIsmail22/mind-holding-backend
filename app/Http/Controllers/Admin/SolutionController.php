<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Solutions\CreateSolutionRequest;
use App\Http\Requests\Solutions\ReorderSolutionsRequest;
use App\Http\Requests\Solutions\UpdateSolutionRequest;
use App\Http\Resources\Admin\SolutionResource;
use App\Models\Solution;
use App\Services\Solutions\SolutionService;
use Illuminate\Http\JsonResponse;

class SolutionController extends Controller
{
    public function __construct(
        private readonly SolutionService $solutionService,
    ) {}

    public function index(): JsonResponse
    {
        return $this->success(SolutionResource::collection($this->solutionService->list()));
    }

    public function store(CreateSolutionRequest $request): JsonResponse
    {
        $solution = $this->solutionService->create($request->toDto(), $request->file('mockups', []));

        return $this->success(new SolutionResource($solution), 'Solution created.', 201);
    }

    public function show(Solution $solution): JsonResponse
    {
        $solution->load(['industry', 'media', 'relatedSolutions', 'services']);

        return $this->success(new SolutionResource($solution));
    }

    public function update(UpdateSolutionRequest $request, Solution $solution): JsonResponse
    {
        $solution = $this->solutionService->update($solution, $request->toDto(), $request->file('mockups', []));

        return $this->success(new SolutionResource($solution), 'Solution updated.');
    }

    public function destroy(Solution $solution): JsonResponse
    {
        $this->solutionService->delete($solution);

        return $this->success(null, 'Solution deleted.');
    }

    public function reorder(ReorderSolutionsRequest $request): JsonResponse
    {
        $this->solutionService->reorder($request->validated('ordered_ids'));

        return $this->success(null, 'Solutions reordered.');
    }
}
