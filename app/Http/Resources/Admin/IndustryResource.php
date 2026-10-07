<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndustryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslations('name'),
            'slug' => $this->slug,
            'slug_ar' => $this->slug_ar,
            'order' => $this->order,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
