<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Technologies\CreateTechnologyRequest;
use App\Http\Requests\Technologies\UpdateTechnologyRequest;
use App\Http\Resources\Admin\TechnologyResource;
use App\Models\Technology;
use App\Services\Technologies\TechnologyService;
use Illuminate\Http\JsonResponse;

class TechnologyController extends Controller
{
    public function __construct(
        private readonly TechnologyService $technologyService,
    ) {}

    public function index(): JsonResponse
    {
        return $this->success(TechnologyResource::collection($this->technologyService->list()));
    }

    public function store(CreateTechnologyRequest $request): JsonResponse
    {
        $technology = $this->technologyService->create($request->toDto());

        return $this->success(new TechnologyResource($technology), 'Technology created.', 201);
    }

    public function show(Technology $technology): JsonResponse
    {
        return $this->success(new TechnologyResource($technology));
    }

    public function update(UpdateTechnologyRequest $request, Technology $technology): JsonResponse
    {
        $technology = $this->technologyService->update($technology, $request->toDto());

        return $this->success(new TechnologyResource($technology), 'Technology updated.');
    }

    public function destroy(Technology $technology): JsonResponse
    {
        $this->technologyService->delete($technology);

        return $this->success(null, 'Technology deleted.');
    }
}
