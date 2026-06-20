<?php
namespace Tests\Unit;

use App\Models\TranslationString;
use App\Services\TranslationFileGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationFileGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_regenerate_writes_nested_array_php_file(): void
    {
        TranslationString::create(['group' => 'ui', 'key' => 'nav.home', 'locale' => 'en', 'value' => 'Home']);
        TranslationString::create(['group' => 'ui', 'key' => 'nav.story', 'locale' => 'en', 'value' => 'Story']);
        TranslationString::create(['group' => 'ui', 'key' => 'btn.book', 'locale' => 'en', 'value' => 'Book']);

        app(TranslationFileGenerator::class)->regenerate('ui');

        $path = lang_path('en/ui.php');
        $this->assertFileExists($path);

        $result = require $path;
        $this->assertSame('Home', $result['nav']['home']);
        $this->assertSame('Story', $result['nav']['story']);
        $this->assertSame('Book', $result['btn']['book']);
    }

    public function test_regenerate_handles_multiple_locales(): void
    {
        TranslationString::create(['group' => 'ui', 'key' => 'nav.home', 'locale' => 'en', 'value' => 'Home']);
        TranslationString::create(['group' => 'ui', 'key' => 'nav.home', 'locale' => 'vi', 'value' => 'Trang chủ']);

        app(TranslationFileGenerator::class)->regenerate('ui');

        $en = require lang_path('en/ui.php');
        $vi = require lang_path('vi/ui.php');

        $this->assertSame('Home', $en['nav']['home']);
        $this->assertSame('Trang chủ', $vi['nav']['home']);
    }
}
