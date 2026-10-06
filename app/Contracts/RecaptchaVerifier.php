<?php

namespace App\Contracts;

interface RecaptchaVerifier
{
    public function verify(string $token, string $action, ?string $ip): bool;
}
