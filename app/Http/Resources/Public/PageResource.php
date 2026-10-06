<?php

namespace App\Http\Resources\Public;

use App\Support\Seo\SeoResolver;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'body' => $this->body,
            'seo' => SeoResolver::for($this->resource),
        ];
    }
}
