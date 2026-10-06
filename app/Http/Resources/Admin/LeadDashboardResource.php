<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadDashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'counts' => [
                'today' => $this->resource['today'],
                'this_week' => $this->resource['this_week'],
                'this_month' => $this->resource['this_month'],
            ],
            'recent' => LeadResource::collection($this->resource['recent']),
            'top_solutions' => $this->resource['top_solutions'],
            'top_services' => $this->resource['top_services'],
        ];
    }
}
