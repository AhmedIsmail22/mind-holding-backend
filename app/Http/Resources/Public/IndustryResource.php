<?php

namespace App\Http\Resources\Public;

use App\Models\SolutionIndustry;
use App\Support\Translations\SlugAlternates;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SolutionIndustry
 */
class IndustryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'alternates' => SlugAlternates::for($this->resource),
        ];
    }
}
