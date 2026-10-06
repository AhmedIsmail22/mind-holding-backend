<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TechnologyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->getTranslations('category'),
            'order' => $this->order,
            'logo_url' => $this->getFirstMediaUrl('logo', 'webp') ?: null,
        ];
    }
}
