<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Faqs\CreateFaqRequest;
use App\Http\Requests\Faqs\UpdateFaqRequest;
use App\Http\Resources\Admin\FaqResource;
use App\Models\Faq;
use App\Models\Service;
use App\Services\Faqs\FaqService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class ServiceFaqController extends Controller
{
    public function __construct(
        private readonly FaqService $faqService,
    ) {}

    public function index(Service $service): JsonResponse
    {
        return $this->success(FaqResource::collection($this->faqService->listForOwner($service)));
    }

    public function store(CreateFaqRequest $request, Service $service): JsonResponse
    {
        $faq = $this->faqService->createForOwner($service, $request->toDto());

        return $this->success(new FaqResource($faq), 'FAQ created.', 201);
    }

    public function show(Service $service, Faq $faq): JsonResponse
    {
        $this->ensureBelongsToService($service, $faq);

        return $this->success(new FaqResource($faq));
    }

    public function update(UpdateFaqRequest $request, Service $service, Faq $faq): JsonResponse
    {
        $this->ensureBelongsToService($service, $faq);

        $faq = $this->faqService->update($faq, $request->toDto());

        return $this->success(new FaqResource($faq), 'FAQ updated.');
    }

    public function destroy(Service $service, Faq $faq): JsonResponse
    {
        $this->ensureBelongsToService($service, $faq);

        $this->faqService->delete($faq);

        return $this->success(null, 'FAQ deleted.');
    }

    private function ensureBelongsToService(Service $service, Faq $faq): void
    {
        if ($faq->faqable_type !== Service::class || $faq->faqable_id !== $service->id) {
            throw new ModelNotFoundException('FAQ not found for this service.');
        }
    }
}
