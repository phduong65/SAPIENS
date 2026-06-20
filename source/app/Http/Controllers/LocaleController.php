<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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

    // Task 6: dictionary() method to be added here
}
