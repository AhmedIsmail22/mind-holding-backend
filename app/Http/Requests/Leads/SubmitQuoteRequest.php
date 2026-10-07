<?php

namespace App\Http\Requests\Leads;

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
            'budget' => ['nullable', 'string', $this->optionRule('budget_options')],
            'start_timing' => ['nullable', 'string', $this->optionRule('start_timing_options')],
            'project_details' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
