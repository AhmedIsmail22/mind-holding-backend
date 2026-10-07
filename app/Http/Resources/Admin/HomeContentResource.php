<?php

namespace App\Http\Resources\Admin;

use App\Models\HomeContent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin HomeContent
 */
class HomeContentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'hero_headline' => $this->getTranslations('hero_headline'),
            'hero_subheadline' => $this->getTranslations('hero_subheadline'),
            'hero_image_url' => $this->getFirstMediaUrl('hero', 'webp') ?: null,
            'stats' => $this->stats,
            'differentiators' => $this->differentiators,
            'process_steps' => $this->process_steps,
            'closing_cta_headline' => $this->getTranslations('closing_cta_headline'),
            'closing_cta_subheadline' => $this->closing_cta_subheadline === null ? null : $this->getTranslations('closing_cta_subheadline'),
            'is_draft' => $this->is_draft,
        ];
    }
}
