<?php

namespace App\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->hide_client_name ? $this->generic_description : $this->client_name,
            'thumbnail_url' => $this->getFirstMediaUrl('images', 'thumb') ?: null,
        ];
    }
}
