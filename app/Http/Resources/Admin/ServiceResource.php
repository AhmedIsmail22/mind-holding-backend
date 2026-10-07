<?php

namespace App\Http\Resources\Admin;

use App\Models\Service;
use App\Support\Translations\OptionalTranslation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Service
 */
class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'group' => $this->group,
            'name' => $this->getTranslations('name'),
            'summary' => OptionalTranslation::both($this->resource, 'summary'),
            'slug' => $this->slug,
            'slug_ar' => $this->slug_ar,
            'description' => $this->getTranslations('description'),
            'deliverables' => $this->deliverables,
            'delivery_steps' => $this->delivery_steps,
            'is_published' => $this->is_published,
            'is_draft' => $this->is_draft,
            'order' => $this->order,
            'related_solution_ids' => $this->solutions->pluck('id')->values(),
            'related_project_ids' => $this->projects->pluck('id')->values(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
