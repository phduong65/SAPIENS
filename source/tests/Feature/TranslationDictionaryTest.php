<?php

namespace Tests\Feature;

use App\Models\TranslationString;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationDictionaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_ui_group_as_flat_json(): void
    {
        TranslationString::create(['group' => 'ui', 'key' => 'nav.home', 'locale' => 'vi', 'value' => 'Trang chủ']);
        TranslationString::create(['group' => 'ui', 'key' => 'btn.book', 'locale' => 'vi', 'value' => 'Đặt bàn']);
        TranslationString::create(['group' => 'pages', 'key' => 'hero.title', 'locale' => 'vi', 'value' => 'Test']); // should NOT appear

        $response = $this->get('/translations/vi');

        $response->assertOk()
                 ->assertJsonStructure(['nav.home', 'btn.book'])
                 ->assertJsonMissing(['hero.title']);
    }

    public function test_rejects_unsupported_locale(): void
    {
        $this->get('/translations/ja')->assertStatus(400);
    }
}
