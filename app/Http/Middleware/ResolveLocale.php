<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveLocale
{
    private const SUPPORTED_LOCALES = ['ar', 'en'];

    private const DEFAULT_LOCALE = 'ar';

    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale($this->resolve($request));

        return $next($request);
    }

    private function resolve(Request $request): string
    {
        $header = $request->header('Accept-Language', '');

        foreach (explode(',', $header) as $part) {
            $locale = strtolower(trim(explode(';', $part)[0]));
            $locale = substr($locale, 0, 2);

            if (in_array($locale, self::SUPPORTED_LOCALES, true)) {
                return $locale;
            }
        }

        return self::DEFAULT_LOCALE;
    }
}
