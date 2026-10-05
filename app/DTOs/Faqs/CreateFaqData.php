<?php

namespace App\DTOs\Faqs;

final readonly class CreateFaqData
{
    public function __construct(
        public array $question,
        public array $answer,
        public bool $isPublished,
        public int $order,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            question: $data['question'],
            answer: $data['answer'],
            isPublished: $data['is_published'] ?? true,
            order: $data['order'] ?? 0,
        );
    }
}
