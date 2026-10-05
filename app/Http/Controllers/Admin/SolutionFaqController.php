<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Faqs\CreateFaqRequest;
use App\Http\Requests\Faqs\UpdateFaqRequest;
use App\Http\Resources\Admin\FaqResource;
use App\Models\Faq;
use App\Models\Solution;
use App\Services\Faqs\FaqService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class SolutionFaqController extends Controller
{
    public function __construct(
        private readonly FaqService $faqService,
    ) {}

    public function index(Solution $solution): JsonResponse
    {
        return $this->success(FaqResource::collection($this->faqService->listForOwner($solution)));
    }

    public function store(CreateFaqRequest $request, Solution $solution): JsonResponse
    {
        $faq = $this->faqService->createForOwner($solution, $request->toDto());

        return $this->success(new FaqResource($faq), 'FAQ created.', 201);
    }

    public function show(Solution $solution, Faq $faq): JsonResponse
    {
        $this->ensureBelongsToSolution($solution, $faq);

        return $this->success(new FaqResource($faq));
    }

    public function update(UpdateFaqRequest $request, Solution $solution, Faq $faq): JsonResponse
    {
        $this->ensureBelongsToSolution($solution, $faq);

        $faq = $this->faqService->update($faq, $request->toDto());

        return $this->success(new FaqResource($faq), 'FAQ updated.');
    }

    public function destroy(Solution $solution, Faq $faq): JsonResponse
    {
        $this->ensureBelongsToSolution($solution, $faq);

        $this->faqService->delete($faq);

        return $this->success(null, 'FAQ deleted.');
    }

    private function ensureBelongsToSolution(Solution $solution, Faq $faq): void
    {
        if ($faq->faqable_type !== Solution::class || $faq->faqable_id !== $solution->id) {
            throw new ModelNotFoundException('FAQ not found for this solution.');
        }
    }
}
