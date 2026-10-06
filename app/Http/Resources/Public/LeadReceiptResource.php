<?php

namespace App\Http\Resources\Public;

use App\Models\Lead;
use App\Services\Leads\LeadService;
use App\Support\Leads\WhatsappLink;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadReceiptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Lead $lead */
        $lead = $this->resource;
        $locale = app()->getLocale();

        return [
            'expected_response_time' => config("leads.expected_response_time.{$locale}"),
            'whatsapp_url' => WhatsappLink::to(
                LeadService::companyWhatsappFor($lead->mobile),
                "Hello, I just submitted a {$lead->type} request ({$lead->name})."
            ),
        ];
    }
}
