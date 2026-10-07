<?php

namespace App\Http\Resources\Admin;

use App\Models\Solution;
use App\Support\Translations\OptionalTranslation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Solution
 */
class SolutionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'solution_industry_id' => $this->solution_industry_id,
            'name' => $this->getTranslations('name'),
            'slug' => $this->slug,
            'slug_ar' => $this->slug_ar,
            'audience' => $this->getTranslations('audience'),
            'summary' => OptionalTranslation::both($this->resource, 'summary'),
            'problem_points' => $this->problem_points,
            'features' => $this->features,
            'deliverables' => $this->deliverables,
            'demo_url' => $this->demo_url,
            'demo_credentials' => $this->demo_credentials,
            'is_flagship' => $this->is_flagship,
            'is_published' => $this->is_published,
            'is_draft' => $this->is_draft,
            'order' => $this->order,
            'mockups' => $this->getMedia('mockups')->map(fn ($media) => $media->getUrl('webp'))->values(),
            'related_solution_ids' => $this->relatedSolutions->pluck('id')->values(),
            'related_service_ids' => $this->services->pluck('id')->values(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
