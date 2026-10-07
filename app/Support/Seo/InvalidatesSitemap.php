<?php

namespace App\Support\Seo;

use Illuminate\Support\Facades\Cache;

/** Drops the cached sitemap when a record that appears in it is saved or deleted. */
trait InvalidatesSitemap
{
    protected static function bootInvalidatesSitemap(): void
    {
        static::saved(fn () => Cache::forget('sitemap.entries'));
        static::deleted(fn () => Cache::forget('sitemap.entries'));
    }
}
