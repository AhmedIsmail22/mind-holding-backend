<?php

namespace App\Http\Resources\Public;

use App\Models\Technology;
use App\Support\Media\MediaAsset;
use App\Support\Translations\OptionalTranslation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Technology
 */
class TechnologyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => OptionalTranslation::text($this->resource, 'category'),
            'logo_url' => $this->getFirstMediaUrl('logo', 'webp') ?: null,
            'logo' => MediaAsset::present($this->getFirstMedia('logo'), 'webp'),
        ];
    }
}
