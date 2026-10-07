<?php

namespace App\Http\Resources\Public;

use App\Models\HomeContent;
use App\Support\Media\MediaAsset;
use App\Support\Translations\OptionalTranslation;
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
                'headline' => OptionalTranslation::text($this->resource, 'hero_headline'),
                'subheadline' => OptionalTranslation::text($this->resource, 'hero_subheadline'),
                'image_url' => $this->getFirstMediaUrl('hero', 'webp') ?: null,
                'image' => MediaAsset::present($this->getFirstMedia('hero'), 'webp'),
            ],
            'stats' => collect($this->stats)->map(fn ($stat) => [
                'label' => (string) ($stat['label'][$locale] ?? $stat['label']['en']),
                'value' => $stat['value'],
            ])->values(),
            'differentiators' => collect($this->differentiators)->map(fn ($item) => [
                'title' => (string) ($item['title'][$locale] ?? $item['title']['en']),
                'description' => (string) ($item['description'][$locale] ?? $item['description']['en']),
            ])->values(),
            'process_steps' => collect($this->process_steps)->map(fn ($item) => [
                'title' => (string) ($item['title'][$locale] ?? $item['title']['en']),
                'description' => (string) ($item['description'][$locale] ?? $item['description']['en']),
                'duration' => (string) ($item['duration'][$locale] ?? $item['duration']['en']),
            ])->values(),
            'closing_cta' => [
                'headline' => OptionalTranslation::text($this->resource, 'closing_cta_headline'),
                'subheadline' => OptionalTranslation::current($this->resource, 'closing_cta_subheadline'),
            ],
        ];
    }
}
