<?php

namespace App\Http\Requests\Industries;

use App\DTOs\Industries\UpdateIndustryData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIndustryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $industry = $this->route('solution_industry');

        return [
            'name' => ['required', 'array'],
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],
            'slug_ar' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('solution_industries', 'slug_ar')->ignore($industry)],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('solution_industries', 'slug')->ignore($industry)],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function toDto(): UpdateIndustryData
    {
        return UpdateIndustryData::fromArray($this->validated());
    }
}
