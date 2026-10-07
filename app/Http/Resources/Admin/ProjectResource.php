<?php

namespace App\Http\Resources\Admin;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Project
 */
class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'slug_ar' => $this->slug_ar,
            'client_name' => $this->getTranslations('client_name'),
            'hide_client_name' => $this->hide_client_name,
            'generic_description' => $this->generic_description === null ? null : $this->getTranslations('generic_description'),
            'overview' => $this->getTranslations('overview'),
            'challenge' => $this->getTranslations('challenge'),
            'solution' => $this->getTranslations('solution'),
            'technologies' => $this->technologies,
            'live_url' => $this->live_url,
            'is_published' => $this->is_published,
            'order' => $this->order,
            'images' => $this->getMedia('images')->map(fn ($media) => $media->getUrl('webp'))->values(),
            'related_service_ids' => $this->services->pluck('id')->values(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
