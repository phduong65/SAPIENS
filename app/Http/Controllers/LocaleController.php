<?php

namespace App\Http\Controllers;

use App\Models\TranslationString;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LocaleController extends Controller
{
    private const SUPPORTED = ['en', 'vi'];

    public function switch(Request $request): RedirectResponse
    {
        $locale = $request->input('locale');

        if (! in_array($locale, self::SUPPORTED, true)) {
            return redirect()->back();
        }

        return redirect()->back()->withCookie(
            cookie()->forever('app_locale', $locale)
        );
    }

    public function dictionary(string $locale): JsonResponse
    {
        if (! in_array($locale, self::SUPPORTED, true)) {
            return response()->json(['error' => 'Unsupported locale'], 400);
        }

        $data = Cache::remember("translations.dict.{$locale}", 3600, function () use ($locale) {
            return TranslationString::where('group', 'ui')
                ->where('locale', $locale)
                ->pluck('value', 'key')
                ->toArray();
        });

        return response()->json($data)
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
