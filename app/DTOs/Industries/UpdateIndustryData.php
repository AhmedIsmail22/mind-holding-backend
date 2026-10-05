<?php

namespace App\DTOs\Industries;

final readonly class UpdateIndustryData
{
    public function __construct(
        public array $name,
        public string $slug,
        public int $order,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            slug: $data['slug'],
            order: $data['order'] ?? 0,
        );
    }
}
