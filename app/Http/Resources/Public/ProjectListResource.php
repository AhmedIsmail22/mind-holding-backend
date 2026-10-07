<?php

namespace App\Http\Resources\Public;

use App\Models\Project;
use App\Support\Media\MediaAsset;
use App\Support\Translations\OptionalTranslation;
use App\Support\Translations\SlugAlternates;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Project
 */
class ProjectListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'summary' => OptionalTranslation::current($this->resource, 'summary'),
            'alternates' => SlugAlternates::for($this->resource),
            'title' => $this->hide_client_name ? OptionalTranslation::text($this->resource, 'generic_description') : OptionalTranslation::text($this->resource, 'client_name'),
            'thumbnail_url' => $this->getFirstMediaUrl('images', 'thumb') ?: null,
            'thumbnail' => MediaAsset::present($this->getFirstMedia('images'), 'thumb'),
        ];
    }
}
