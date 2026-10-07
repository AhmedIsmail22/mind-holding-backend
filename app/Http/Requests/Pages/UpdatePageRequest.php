<?php

namespace App\Http\Requests\Pages;

use App\DTOs\Pages\UpdatePageData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slug_ar' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('pages', 'slug_ar')->ignore($this->route('slug'), 'slug')],
            'title' => ['required', 'array'],
            'title.ar' => ['required', 'string', 'max:255'],
            'title.en' => ['required', 'string', 'max:255'],

            'body' => ['required', 'array'],
            'body.ar' => ['required', 'string'],
            'body.en' => ['required', 'string'],
        ];
    }

    public function toDto(): UpdatePageData
    {
        return UpdatePageData::fromArray($this->validated());
    }
}
