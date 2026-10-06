<?php

namespace App\Services\Leads;

use App\DTOs\Leads\LeadFilterData;
use App\Models\Lead;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LeadAdminService
{
    public function paginate(LeadFilterData $filters, int $perPage = 15): LengthAwarePaginator
    {
        return Lead::query()
            ->with(['service', 'solution', 'assignee'])
            ->when($filters->type, fn ($q, $type) => $q->where('type', $type))
            ->when($filters->status, fn ($q, $status) => $q->where('status', $status))
            ->when($filters->serviceId, fn ($q, $id) => $q->where('service_id', $id))
            ->when($filters->from, fn ($q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($filters->to, fn ($q, $to) => $q->whereDate('created_at', '<=', $to))
            ->latest('created_at')
            ->paginate($perPage);
    }

    public function updateStatus(Lead $lead, string $status): Lead
    {
        $lead->update(['status' => $status]);

        return $lead;
    }

    public function assign(Lead $lead, ?int $userId): Lead
    {
        $lead->update(['assigned_to' => $userId]);

        return $lead->load('assignee');
    }

    public function delete(Lead $lead): void
    {
        $lead->delete();
    }
}
