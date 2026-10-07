<?php

namespace App\Http\Resources\Public;

use App\Models\Project;
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
            'alternates' => SlugAlternates::for($this->resource),
            'title' => $this->hide_client_name ? $this->generic_description : $this->client_name,
            'thumbnail_url' => $this->getFirstMediaUrl('images', 'thumb') ?: null,
        ];
    }
}
