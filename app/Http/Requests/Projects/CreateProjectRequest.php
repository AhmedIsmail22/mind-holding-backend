<?php

namespace App\Http\Requests\Projects;

use App\DTOs\Projects\CreateProjectData;
use Illuminate\Foundation\Http\FormRequest;

class CreateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:projects,slug'],
            'slug_ar' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:projects,slug_ar'],
            'client_name' => ['required', 'array'],
            'client_name.ar' => ['required', 'string', 'max:255'],
            'client_name.en' => ['required', 'string', 'max:255'],

            'hide_client_name' => ['nullable', 'boolean'],

            'generic_description' => ['required_if:hide_client_name,true', 'nullable', 'array'],
            'generic_description.ar' => ['required_if:hide_client_name,true', 'nullable', 'string', 'max:255'],
            'generic_description.en' => ['required_if:hide_client_name,true', 'nullable', 'string', 'max:255'],

            'overview' => ['required', 'array'],
            'overview.ar' => ['required', 'string'],
            'overview.en' => ['required', 'string'],

            'challenge' => ['required', 'array'],
            'challenge.ar' => ['required', 'string'],
            'challenge.en' => ['required', 'string'],

            'solution' => ['required', 'array'],
            'solution.ar' => ['required', 'string'],
            'solution.en' => ['required', 'string'],

            'technologies' => ['required', 'array', 'min:1'],
            'technologies.*' => ['string', 'max:100'],

            'live_url' => ['nullable', 'url', 'max:500'],
            'is_published' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],

            'related_service_ids' => ['nullable', 'array'],
            'related_service_ids.*' => ['integer', 'exists:services,id'],

            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:4096'],
        ];
    }

    public function toDto(): CreateProjectData
    {
        return CreateProjectData::fromArray($this->validated());
    }
}
