<?php

namespace App\Http\Requests\Solutions;

use App\DTOs\Solutions\UpdateSolutionData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSolutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $solution = $this->route('solution');

        return [
            'solution_industry_id' => ['required', 'integer', 'exists:solution_industries,id'],

            'name' => ['required', 'array'],
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],

            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('solutions', 'slug')->ignore($solution)],

            'target_audience' => ['required', 'array'],
            'target_audience.ar' => ['required', 'string', 'max:255'],
            'target_audience.en' => ['required', 'string', 'max:255'],

            'problem_points' => ['required', 'array', 'min:1'],
            'problem_points.*.ar' => ['required', 'string', 'max:500'],
            'problem_points.*.en' => ['required', 'string', 'max:500'],

            'features' => ['required', 'array'],
            'features.customer' => ['nullable', 'array'],
            'features.customer.*.ar' => ['required_with:features.customer', 'string', 'max:255'],
            'features.customer.*.en' => ['required_with:features.customer', 'string', 'max:255'],
            'features.business_owner' => ['nullable', 'array'],
            'features.business_owner.*.ar' => ['required_with:features.business_owner', 'string', 'max:255'],
            'features.business_owner.*.en' => ['required_with:features.business_owner', 'string', 'max:255'],
            'features.staff' => ['nullable', 'array'],
            'features.staff.*.ar' => ['required_with:features.staff', 'string', 'max:255'],
            'features.staff.*.en' => ['required_with:features.staff', 'string', 'max:255'],

            'deliverables' => ['required', 'array', 'min:1'],
            'deliverables.*.ar' => ['required', 'string', 'max:255'],
            'deliverables.*.en' => ['required', 'string', 'max:255'],

            'demo_url' => ['nullable', 'url', 'max:500'],
            'demo_credentials' => ['nullable', 'string', 'max:1000'],

            'is_flagship' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],

            'related_solution_ids' => ['nullable', 'array'],
            'related_solution_ids.*' => ['integer', 'exists:solutions,id'],

            'related_service_ids' => ['nullable', 'array'],
            'related_service_ids.*' => ['integer', 'exists:services,id'],

            'mockups' => ['nullable', 'array'],
            'mockups.*' => ['image', 'max:4096'],
        ];
    }

    public function toDto(): UpdateSolutionData
    {
        return UpdateSolutionData::fromArray($this->validated());
    }
}
