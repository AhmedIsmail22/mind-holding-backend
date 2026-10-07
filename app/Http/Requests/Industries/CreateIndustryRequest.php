<?php

namespace App\Http\Requests\Industries;

use App\DTOs\Industries\CreateIndustryData;
use Illuminate\Foundation\Http\FormRequest;

class CreateIndustryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'array'],
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],
            'slug_ar' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:solution_industries,slug_ar'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:solution_industries,slug'],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function toDto(): CreateIndustryData
    {
        return CreateIndustryData::fromArray($this->validated());
    }
}
