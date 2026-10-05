<?php

namespace App\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
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
