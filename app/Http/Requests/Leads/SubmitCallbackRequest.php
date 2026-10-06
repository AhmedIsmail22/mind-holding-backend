<?php

namespace App\Http\Requests\Leads;

class SubmitCallbackRequest extends BaseLeadRequest
{
    protected function leadType(): string
    {
        return 'callback';
    }

    protected function typeRules(): array
    {
        return [
            'need' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
