<?php

namespace App\Support\Leads;

final class WhatsappLink
{
    public static function to(string $number, ?string $message = null): string
    {
        $url = 'https://wa.me/'.MobileCountry::digits($number);

        return $message === null ? $url : $url.'?text='.rawurlencode($message);
    }
}
