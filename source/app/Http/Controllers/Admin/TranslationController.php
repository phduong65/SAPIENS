<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TranslationString;
use App\Services\TranslationFileGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TranslationController extends Controller
{
    public function index(Request $request): View
    {
        $group = in_array($request->get('group'), ['ui', 'pages', 'emails'], true)
            ? $request->get('group')
            : 'ui';
        $groups = ['ui', 'pages', 'emails'];

        $keys = TranslationString::where('group', $group)
            ->orderBy('key')
            ->get()
            ->groupBy('key')
            ->map(fn ($rows) => $rows->keyBy('locale'));

        $locales = TranslationString::where('group', $group)
            ->distinct()
            ->pluck('locale')
            ->toArray();

        // Ensure consistent locale order: en first, then vi, then any others
        usort($locales, function ($a, $b) {
            $order = ['en' => 0, 'vi' => 1];
            return ($order[$a] ?? 99) <=> ($order[$b] ?? 99);
        });

        return view('admin.translations.index', compact('keys', 'group', 'groups', 'locales'));
    }

    public function addKey(Request $request, TranslationFileGenerator $generator): RedirectResponse
    {
        $request->validate([
            'group'  => 'required|in:ui,pages,emails',
            'key'    => 'required|string|regex:/^[a-z0-9_.]+$/i|max:120',
            'values' => 'required|array',
        ]);

        $group = $request->input('group');
        $key   = strtolower(trim($request->input('key')));

        foreach ($request->input('values', []) as $locale => $value) {
            if (!in_array($locale, ['en', 'vi'], true)) {
                continue;
            }
            TranslationString::updateOrCreate(
                ['group' => $group, 'key' => $key, 'locale' => $locale],
                ['value' => $value ?? '']
            );
        }

        $generator->regenerate($group);

        return redirect()->route('admin.translations.index', ['group' => $group])
            ->with('success', "Key «{$key}» added and language files regenerated.");
    }

    public function update(Request $request, TranslationFileGenerator $generator): RedirectResponse
    {
        $group = in_array($request->input('group'), ['ui', 'pages', 'emails'], true)
            ? $request->input('group')
            : 'ui';
        $entries = $request->input('translations', []);

        foreach ($entries as $key => $locales) {
            foreach ($locales as $locale => $value) {
                TranslationString::updateOrCreate(
                    ['group' => $group, 'key' => $key, 'locale' => $locale],
                    ['value' => $value ?? '']
                );
            }
        }

        $generator->regenerate($group);

        return redirect()->route('admin.translations.index', ['group' => $group])
            ->with('success', 'Translations saved and language files regenerated.');
    }
}
