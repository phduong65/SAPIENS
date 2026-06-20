<?php

namespace Database\Seeders;

use App\Models\TranslationString;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class TranslationSeeder extends Seeder
{
    public function run(): void
    {
        $groups  = ['ui', 'pages', 'emails'];
        $locales = ['en', 'vi'];

        foreach ($groups as $group) {
            foreach ($locales as $locale) {
                $path = lang_path("{$locale}/{$group}.php");
                if (!file_exists($path)) {
                    continue;
                }

                $flat = Arr::dot(require $path);

                foreach ($flat as $key => $value) {
                    TranslationString::updateOrCreate(
                        ['group' => $group, 'key' => $key, 'locale' => $locale],
                        ['value' => $value]
                    );
                }
            }
        }
    }
}
