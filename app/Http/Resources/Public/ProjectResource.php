<?php

namespace App\Http\Resources\Public;

use App\Models\Project;
use App\Support\Translations\SlugAlternates;
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
            'alternates' => SlugAlternates::for($this->resource),
            'title' => $this->hide_client_name ? $this->generic_description : $this->client_name,
            'overview' => $this->overview,
            'challenge' => $this->challenge,
            'solution' => $this->solution,
            'technologies' => $this->technologies,
            'live_url' => $this->live_url,
            'images' => $this->getMedia('images')->map(fn ($media) => $media->getUrl('webp'))->values(),
        ];
    }
}
