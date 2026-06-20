<?php
namespace Tests\Unit\Models;

use App\Models\TranslationString;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationStringTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_translation_string(): void
    {
        $ts = TranslationString::create([
            'group'  => 'ui',
            'key'    => 'nav.home',
            'locale' => 'en',
            'value'  => 'Home',
        ]);
        $this->assertDatabaseHas('translation_strings', ['key' => 'nav.home', 'locale' => 'en']);
    }

    public function test_unique_constraint_on_group_key_locale(): void
    {
        TranslationString::create(['group' => 'ui', 'key' => 'nav.home', 'locale' => 'en', 'value' => 'Home']);
        $this->expectException(\Illuminate\Database\QueryException::class);
        TranslationString::create(['group' => 'ui', 'key' => 'nav.home', 'locale' => 'en', 'value' => 'Home2']);
    }
}
