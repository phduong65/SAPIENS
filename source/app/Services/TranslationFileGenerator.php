<?php
namespace App\Services;

use App\Models\TranslationString;
use Illuminate\Support\Arr;

class TranslationFileGenerator
{
    public function regenerate(string $group): void
    {
        $locales = TranslationString::where('group', $group)
            ->distinct()
            ->pluck('locale')
            ->toArray();

        foreach ($locales as $locale) {
            $rows = TranslationString::where('group', $group)
                ->where('locale', $locale)
                ->pluck('value', 'key');

            $nested = [];
            foreach ($rows as $dotKey => $value) {
                Arr::set($nested, $dotKey, $value);
            }

            $dir = lang_path($locale);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $content = "<?php\n\nreturn " . var_export($nested, true) . ";\n";
            file_put_contents("{$dir}/{$group}.php", $content);
        }
    }
}
