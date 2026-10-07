<?php

namespace App\DTOs\Services;

final readonly class CreateServiceData
{
    public function __construct(
        public string $group,
        public array $name,
        public string $slug,
        public ?string $slugAr,
        public ?array $summary,
        public array $description,
        public array $deliverables,
        public array $deliverySteps,
        public bool $isPublished,
        public int $order,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            group: $data['group'],
            name: $data['name'],
            slug: $data['slug'],
            slugAr: $data['slug_ar'] ?? null,
            summary: $data['summary'] ?? null,
            description: $data['description'],
            deliverables: $data['deliverables'],
            deliverySteps: $data['delivery_steps'],
            isPublished: $data['is_published'] ?? false,
            order: $data['order'] ?? 0,
        );
    }
}
