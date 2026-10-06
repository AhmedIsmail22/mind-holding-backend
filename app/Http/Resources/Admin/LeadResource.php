<?php

namespace App\Http\Resources\Admin;

use App\Support\Leads\MobileCountry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $digits = MobileCountry::digits($this->mobile);

        return [
            'id' => $this->id,
            'type' => $this->type,
            'status' => $this->status,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'company' => $this->company,
            'business_name' => $this->business_name,
            'service' => $this->whenLoaded('service', fn () => $this->service ? [
                'id' => $this->service->id,
                'name' => $this->service->getTranslations('name'),
            ] : null),
            'solution' => $this->whenLoaded('solution', fn () => $this->solution ? [
                'id' => $this->solution->id,
                'name' => $this->solution->getTranslations('name'),
            ] : null),
            'budget' => $this->budget,
            'start_timing' => $this->start_timing,
            'project_details' => $this->project_details,
            'preferred_contact_time' => $this->preferred_contact_time,
            'need' => $this->need,
            'language' => $this->language,
            'page_url' => $this->page_url,
            'utm' => $this->utm,
            'assigned_to' => $this->assigned_to,
            'assignee' => $this->whenLoaded('assignee', fn () => $this->assignee ? [
                'id' => $this->assignee->id,
                'name' => $this->assignee->name,
            ] : null),
            'call_url' => 'tel:+'.$digits,
            'whatsapp_url' => 'https://wa.me/'.$digits,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
