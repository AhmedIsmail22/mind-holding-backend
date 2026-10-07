<?php

namespace App\Http\Resources\Public;

use App\Models\Service;
use App\Support\Translations\OptionalTranslation;
use App\Support\Translations\SlugAlternates;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Service
 */
class ServiceListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'group' => $this->group,
            'name' => $this->name,
            'summary' => OptionalTranslation::current($this->resource, 'summary'),
            'slug' => $this->slug,
            'alternates' => SlugAlternates::for($this->resource),
        ];
    }
}
