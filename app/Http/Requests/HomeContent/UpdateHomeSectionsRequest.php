<?php

namespace App\Http\Requests\HomeContent;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHomeSectionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sections' => ['required', 'array', 'min:1'],
            'sections.*.key' => ['required', 'string', 'exists:home_sections,key'],
            'sections.*.is_enabled' => ['required', 'boolean'],
            'sections.*.order' => ['required', 'integer', 'min:0'],
        ];
    }
}
