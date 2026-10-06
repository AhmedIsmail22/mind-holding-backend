<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->getTranslations('title'),
            'body' => $this->getTranslations('body'),
            'is_draft' => $this->is_draft,
        ];
    }
}
