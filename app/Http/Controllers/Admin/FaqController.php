<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Faqs\CreateFaqRequest;
use App\Http\Requests\Faqs\UpdateFaqRequest;
use App\Http\Resources\Admin\FaqResource;
use App\Models\Faq;
use App\Services\Faqs\FaqService;
use Illuminate\Http\JsonResponse;

class FaqController extends Controller
{
    public function __construct(
        private readonly FaqService $faqService,
    ) {}

    public function index(): JsonResponse
    {
        return $this->success(FaqResource::collection($this->faqService->listGeneral()));
    }

    public function store(CreateFaqRequest $request): JsonResponse
    {
        $faq = $this->faqService->createGeneral($request->toDto());

        return $this->success(new FaqResource($faq), 'FAQ created.', 201);
    }

    public function show(Faq $faq): JsonResponse
    {
        return $this->success(new FaqResource($faq));
    }

    public function update(UpdateFaqRequest $request, Faq $faq): JsonResponse
    {
        $faq = $this->faqService->update($faq, $request->toDto());

        return $this->success(new FaqResource($faq), 'FAQ updated.');
    }

    public function destroy(Faq $faq): JsonResponse
    {
        $this->faqService->delete($faq);

        return $this->success(null, 'FAQ deleted.');
    }
}
