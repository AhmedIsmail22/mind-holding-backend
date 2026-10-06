<?php

namespace App\Http\Requests\Leads;

use App\DTOs\Leads\LeadFilterData;
use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexLeadsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['nullable', Rule::in(Lead::TYPES)],
            'status' => ['nullable', Rule::in(Lead::STATUSES)],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
        ];
    }

    public function toDto(): LeadFilterData
    {
        return LeadFilterData::fromArray($this->validated());
    }
}
