<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Leads\SubmitCallbackRequest;
use App\Http\Requests\Leads\SubmitDemoRequest;
use App\Http\Requests\Leads\SubmitQuoteRequest;
use App\Http\Resources\Public\LeadReceiptResource;
use App\Models\Lead;
use App\Services\Leads\LeadService;
use Illuminate\Http\JsonResponse;

class LeadController extends Controller
{
    public function __construct(
        private readonly LeadService $leadService,
    ) {}

    public function quote(SubmitQuoteRequest $request): JsonResponse
    {
        return $this->receipt($this->leadService->submit($request->toDto(), $request->ip()));
    }

    public function demo(SubmitDemoRequest $request): JsonResponse
    {
        return $this->receipt($this->leadService->submit($request->toDto(), $request->ip()));
    }

    public function callback(SubmitCallbackRequest $request): JsonResponse
    {
        return $this->receipt($this->leadService->submit($request->toDto(), $request->ip()));
    }

    private function receipt(Lead $lead): JsonResponse
    {
        return $this->success(new LeadReceiptResource($lead), 'Request received.', 201);
    }
}
