<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    private const SUPPORTED = ['en', 'vi'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->cookie('app_locale');

        if (! $locale) {
            try {
                $locale = Setting::get('default_locale', config('app.locale', 'en'));
            } catch (\Throwable) {
                $locale = config('app.locale', 'en');
            }
        }

        if (! in_array($locale, self::SUPPORTED, true)) {
            $locale = 'en';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
