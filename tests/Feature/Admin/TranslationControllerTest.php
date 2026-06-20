<?php

namespace Tests\Feature\Admin;

use App\Models\TranslationString;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): static
    {
        return $this->actingAs(User::factory()->create());
    }

    public function test_index_shows_translation_keys(): void
    {
        TranslationString::create(['group' => 'ui', 'key' => 'nav.home', 'locale' => 'en', 'value' => 'Home']);
        TranslationString::create(['group' => 'ui', 'key' => 'nav.home', 'locale' => 'vi', 'value' => 'Trang chủ']);

        $this->actingAsAdmin()
             ->get('/admin/translations?group=ui')
             ->assertOk()
             ->assertSee('nav.home')
             ->assertSee('Home')
             ->assertSee('Trang chủ');
    }

    public function test_update_saves_translations_and_regenerates_files(): void
    {
        TranslationString::create(['group' => 'ui', 'key' => 'nav.home', 'locale' => 'en', 'value' => 'Home']);
        TranslationString::create(['group' => 'ui', 'key' => 'nav.home', 'locale' => 'vi', 'value' => 'Trang chủ']);

        $this->actingAsAdmin()->post('/admin/translations', [
            'group' => 'ui',
            'translations' => [
                'nav.home' => ['en' => 'Home Updated', 'vi' => 'Trang chủ mới'],
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('translation_strings', [
            'key' => 'nav.home', 'locale' => 'en', 'value' => 'Home Updated',
        ]);

        $file = lang_path('en/ui.php');
        $this->assertFileExists($file);
        $result = require $file;
        $this->assertSame('Home Updated', $result['nav']['home']);
    }
}
