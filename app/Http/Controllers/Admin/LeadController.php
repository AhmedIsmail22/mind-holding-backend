<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Leads\AssignLeadRequest;
use App\Http\Requests\Leads\IndexLeadsRequest;
use App\Http\Requests\Leads\UpdateLeadStatusRequest;
use App\Http\Resources\Admin\LeadDashboardResource;
use App\Http\Resources\Admin\LeadResource;
use App\Models\Lead;
use App\Services\Leads\LeadAdminService;
use App\Services\Leads\LeadStatsService;
use Illuminate\Http\JsonResponse;

class LeadController extends Controller
{
    public function __construct(
        private readonly LeadAdminService $leadAdminService,
        private readonly LeadStatsService $leadStatsService,
    ) {}

    public function index(IndexLeadsRequest $request): JsonResponse
    {
        $leads = $this->leadAdminService->paginate($request->toDto());

        return $this->resourcePaginated(LeadResource::collection($leads));
    }

    public function dashboard(): JsonResponse
    {
        return $this->success(new LeadDashboardResource($this->leadStatsService->dashboard()));
    }

    public function show(Lead $lead): JsonResponse
    {
        return $this->success(new LeadResource($lead->load(['service', 'solution', 'assignee'])));
    }

    public function updateStatus(UpdateLeadStatusRequest $request, Lead $lead): JsonResponse
    {
        $lead = $this->leadAdminService->updateStatus($lead, $request->validated('status'));

        return $this->success(new LeadResource($lead), 'Lead status updated.');
    }

    public function assign(AssignLeadRequest $request, Lead $lead): JsonResponse
    {
        $lead = $this->leadAdminService->assign($lead, $request->validated('user_id'));

        return $this->success(new LeadResource($lead), 'Lead assigned.');
    }

    public function destroy(Lead $lead): JsonResponse
    {
        $this->leadAdminService->delete($lead);

        return $this->success(null, 'Lead deleted.');
    }
}
