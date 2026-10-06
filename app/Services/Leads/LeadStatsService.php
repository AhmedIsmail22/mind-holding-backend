<?php

namespace App\Services\Leads;

use App\Models\Lead;
use App\Models\Service;
use App\Models\Solution;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LeadStatsService
{
    public function dashboard(): array
    {
        $now = Carbon::now();

        return [
            'today' => Lead::whereDate('created_at', $now->toDateString())->count(),
            'this_week' => Lead::where('created_at', '>=', $now->copy()->startOfWeek())->count(),
            'this_month' => Lead::where('created_at', '>=', $now->copy()->startOfMonth())->count(),
            'recent' => Lead::with(['service', 'solution'])->latest('created_at')->limit(10)->get(),
            'top_solutions' => $this->topRequested('solution_id', Solution::class),
            'top_services' => $this->topRequested('service_id', Service::class),
        ];
    }

    private function topRequested(string $column, string $modelClass): Collection
    {
        $counts = Lead::query()
            ->select($column, DB::raw('COUNT(*) as requests'))
            ->whereNotNull($column)
            ->where('status', '!=', 'spam')
            ->groupBy($column)
            ->orderByDesc('requests')
            ->limit(5)
            ->get();

        $models = $modelClass::whereIn('id', $counts->pluck($column))->get()->keyBy('id');
        $locale = app()->getLocale();

        return $counts->map(fn ($row) => [
            'id' => $row->{$column},
            'name' => $models[$row->{$column}]?->getTranslation('name', $locale),
            'requests' => (int) $row->requests,
        ])->values();
    }
}
