<?php

namespace App\Http\Requests\Faqs;

use App\DTOs\Faqs\UpdateFaqData;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question' => ['required', 'array'],
            'question.ar' => ['required', 'string', 'max:500'],
            'question.en' => ['required', 'string', 'max:500'],

            'answer' => ['required', 'array'],
            'answer.ar' => ['required', 'string'],
            'answer.en' => ['required', 'string'],

            'is_published' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function toDto(): UpdateFaqData
    {
        return UpdateFaqData::fromArray($this->validated());
    }
}
