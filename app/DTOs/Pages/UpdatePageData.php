<?php

namespace App\DTOs\Pages;

final readonly class UpdatePageData
{
    public function __construct(
        public array $title,
        public array $body,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            body: $data['body'],
        );
    }
}
