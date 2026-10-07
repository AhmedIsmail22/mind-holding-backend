<?php

namespace App\Http\Resources\Public;

use App\Models\Technology;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property array{category: string, items: iterable<int, Technology>} $resource
 */
class TechnologyGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'category' => (string) $this->resource['category'],
            'items' => TechnologyResource::collection($this->resource['items']),
        ];
    }
}
