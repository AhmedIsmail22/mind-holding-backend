<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\FaqResource;
use App\Services\Faqs\FaqService;
use Illuminate\Http\JsonResponse;

class FaqController extends Controller
{
    public function __construct(
        private readonly FaqService $faqService,
    ) {}

    public function index(): JsonResponse
    {
        return $this->success(FaqResource::collection($this->faqService->listPublishedGeneral()));
    }
}
