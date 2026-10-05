<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\IndustryResource;
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
}
