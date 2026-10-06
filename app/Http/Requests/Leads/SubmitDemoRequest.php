<?php

namespace App\Http\Requests\Leads;

class SubmitDemoRequest extends BaseLeadRequest
{
    protected function leadType(): string
    {
        return 'demo';
    }

    protected function typeRules(): array
    {
        return [
            'business_name' => ['nullable', 'string', 'max:255'],
            'solution_id' => ['required', 'integer', 'exists:solutions,id'],
            'preferred_contact_time' => ['nullable', 'string', 'max:100'],
        ];
    }
}
