<?php

namespace App\Http\Resources\Public;

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
        $locale = app()->getLocale();

        return [
            'hero' => [
                'headline' => $this->hero_headline,
                'subheadline' => $this->hero_subheadline,
                'image_url' => $this->getFirstMediaUrl('hero', 'webp') ?: null,
            ],
            'stats' => collect($this->stats)->map(fn ($stat) => [
                'label' => $stat['label'][$locale] ?? $stat['label']['en'],
                'value' => $stat['value'],
            ])->values(),
            'differentiators' => collect($this->differentiators)->map(fn ($item) => [
                'title' => $item['title'][$locale] ?? $item['title']['en'],
                'description' => $item['description'][$locale] ?? $item['description']['en'],
            ])->values(),
            'process_steps' => collect($this->process_steps)->map(fn ($item) => [
                'title' => $item['title'][$locale] ?? $item['title']['en'],
                'description' => $item['description'][$locale] ?? $item['description']['en'],
                'duration' => $item['duration'][$locale] ?? $item['duration']['en'],
            ])->values(),
            'closing_cta' => [
                'headline' => $this->closing_cta_headline,
                'subheadline' => $this->closing_cta_subheadline,
            ],
        ];
    }
}
