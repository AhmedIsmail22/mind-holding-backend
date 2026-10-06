<?php

namespace App\DTOs\Leads;

final readonly class LeadFilterData
{
    public function __construct(
        public ?string $type,
        public ?string $status,
        public ?string $from,
        public ?string $to,
        public ?int $serviceId,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'] ?? null,
            status: $data['status'] ?? null,
            from: $data['from'] ?? null,
            to: $data['to'] ?? null,
            serviceId: isset($data['service_id']) ? (int) $data['service_id'] : null,
        );
    }
}
