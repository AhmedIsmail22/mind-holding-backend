<?php

namespace App\Http\Requests\Leads;

use Illuminate\Validation\Rule;

class SubmitQuoteRequest extends BaseLeadRequest
{
    protected function leadType(): string
    {
        return 'quote';
    }

    protected function typeRules(): array
    {
        return [
            'company' => ['nullable', 'string', 'max:255'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'budget' => ['nullable', 'string', Rule::in($this->optionLabels('budget_options'))],
            'start_timing' => ['nullable', 'string', Rule::in($this->optionLabels('start_timing_options'))],
            'project_details' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
