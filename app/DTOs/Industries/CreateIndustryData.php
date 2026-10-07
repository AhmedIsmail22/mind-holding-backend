<?php

namespace App\DTOs\Industries;

final readonly class CreateIndustryData
{
    public function __construct(
        public array $name,
        public string $slug,
        public ?string $slugAr,
        public int $order,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            slug: $data['slug'],
            slugAr: $data['slug_ar'] ?? null,
            order: $data['order'] ?? 0,
        );
    }
}
