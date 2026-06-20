<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $defaultLocale = Setting::get('default_locale', 'en');
        return view('admin.settings.index', compact('defaultLocale'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'default_locale' => 'required|in:en,vi',
        ]);

        Setting::set('default_locale', $validated['default_locale']);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings saved.');
    }
}
