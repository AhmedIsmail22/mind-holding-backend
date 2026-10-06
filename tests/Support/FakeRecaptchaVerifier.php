<?php

namespace Tests\Support;

use App\Contracts\RecaptchaVerifier;

class FakeRecaptchaVerifier implements RecaptchaVerifier
{
    public function __construct(private bool $passes = true) {}

    public function verify(string $token, string $action, ?string $ip): bool
    {
        return $this->passes;
    }
}
