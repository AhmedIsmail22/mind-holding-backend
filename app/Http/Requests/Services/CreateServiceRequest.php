<?php

namespace App\Http\Requests\Services;

use App\DTOs\Services\CreateServiceData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group' => ['required', Rule::in(['software', 'marketing'])],

            'name' => ['required', 'array'],
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],

            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:services,slug'],

            'description' => ['required', 'array'],
            'description.ar' => ['required', 'string'],
            'description.en' => ['required', 'string'],

            'deliverables' => ['required', 'array', 'min:1'],
            'deliverables.*.ar' => ['required', 'string', 'max:255'],
            'deliverables.*.en' => ['required', 'string', 'max:255'],

            'delivery_steps' => ['required', 'array', 'min:1'],
            'delivery_steps.*.ar' => ['required', 'string', 'max:255'],
            'delivery_steps.*.en' => ['required', 'string', 'max:255'],

            'is_published' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function toDto(): CreateServiceData
    {
        return CreateServiceData::fromArray($this->validated());
    }
}
