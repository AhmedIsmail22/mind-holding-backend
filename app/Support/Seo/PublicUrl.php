<?php

namespace App\Support\Seo;

/**
 * Absolute public URLs on the primary domain. Every canonical, hreflang and
 * sitemap URL is built here, so the legacy domain never appears in output.
 */
final class PublicUrl
{
    public static function base(): string
    {
        return rtrim((string) config('seo.site_url'), '/');
    }

    /** Absolute URL for a path in a locale, e.g. ("ar", "/services/x") → https://bitcodak.com/ar/services/x */
    public static function localized(string $locale, string $path = ''): string
    {
        $encoded = implode('/', array_map('rawurlencode', explode('/', ltrim($path, '/'))));

        return self::base().'/'.$locale.($encoded === '' ? '' : '/'.$encoded);
    }
}
