<?php

namespace App\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SolutionListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'industry' => [
                'slug' => $this->industry->slug,
                'name' => $this->industry->name,
            ],
            'is_flagship' => $this->is_flagship,
            'has_demo' => ! empty($this->demo_url),
            'thumbnail_url' => $this->getFirstMediaUrl('mockups', 'thumb') ?: null,
        ];
    }
}
