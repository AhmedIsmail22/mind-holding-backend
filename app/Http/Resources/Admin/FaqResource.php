<?php

namespace App\Http\Resources\Admin;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Faq
 */
class FaqResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'question' => $this->getTranslations('question'),
            'answer' => $this->getTranslations('answer'),
            'is_published' => $this->is_published,
            'order' => $this->order,
        ];
    }
}
