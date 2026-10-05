<?php

namespace App\DTOs\Users;

final readonly class UpdateUserData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $role,
        public ?string $password,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            role: $data['role'],
            password: $data['password'] ?? null,
        );
    }
}
