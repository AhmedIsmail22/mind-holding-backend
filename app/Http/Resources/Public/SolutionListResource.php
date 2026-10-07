<?php

namespace App\Http\Resources\Public;

use App\Models\Solution;
use App\Support\Media\MediaAsset;
use App\Support\Translations\OptionalTranslation;
use App\Support\Translations\SlugAlternates;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Solution
 */
class SolutionListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => OptionalTranslation::text($this->resource, 'name'),
            'audience' => OptionalTranslation::text($this->resource, 'audience'),
            'summary' => OptionalTranslation::current($this->resource, 'summary'),
            'slug' => $this->slug,
            'alternates' => SlugAlternates::for($this->resource),
            'industry' => [
                'slug' => $this->industry->slug,
                'alternates' => SlugAlternates::for($this->industry),
                'name' => OptionalTranslation::text($this->industry, 'name'),
            ],
            'is_flagship' => $this->is_flagship,
            'has_demo' => ! empty($this->demo_url),
            'thumbnail_url' => $this->getFirstMediaUrl('mockups', 'thumb') ?: null,
            'thumbnail' => MediaAsset::present($this->getFirstMedia('mockups'), 'thumb'),
        ];
    }
}
