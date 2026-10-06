<?php

namespace App\Support\Leads;

final class MobileCountry
{
    /**
     * ISO code for a mobile number such as "+971503365403". Falls back to EG.
     */
    public static function iso(string $mobile): string
    {
        $digits = ltrim($mobile, '+');

        foreach (config('leads.mobile_prefixes') as $prefix => $iso) {
            if (str_starts_with($digits, (string) $prefix)) {
                return $iso;
            }
        }

        return 'EG';
    }

    public static function digits(string $mobile): string
    {
        return preg_replace('/\D/', '', $mobile);
    }
}
