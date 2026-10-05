<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Industries\CreateIndustryRequest;
use App\Http\Requests\Industries\UpdateIndustryRequest;
use App\Http\Resources\Admin\IndustryResource;
use App\Models\SolutionIndustry;
use App\Services\Industries\IndustryService;
use Illuminate\Http\JsonResponse;

class IndustryController extends Controller
{
    public function __construct(
        private readonly IndustryService $industryService,
    ) {}

    public function index(): JsonResponse
    {
        return $this->success(IndustryResource::collection($this->industryService->list()));
    }

    public function store(CreateIndustryRequest $request): JsonResponse
    {
        $industry = $this->industryService->create($request->toDto());

        return $this->success(new IndustryResource($industry), 'Industry created.', 201);
    }

    public function show(SolutionIndustry $solutionIndustry): JsonResponse
    {
        return $this->success(new IndustryResource($solutionIndustry));
    }

    public function update(UpdateIndustryRequest $request, SolutionIndustry $solutionIndustry): JsonResponse
    {
        $industry = $this->industryService->update($solutionIndustry, $request->toDto());

        return $this->success(new IndustryResource($industry), 'Industry updated.');
    }

    public function destroy(SolutionIndustry $solutionIndustry): JsonResponse
    {
        $this->industryService->delete($solutionIndustry);

        return $this->success(null, 'Industry deleted.');
    }
}
