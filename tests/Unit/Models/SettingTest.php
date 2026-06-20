<?php
namespace Tests\Unit\Models;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_returns_default_when_missing(): void
    {
        $this->assertSame('en', Setting::get('default_locale', 'en'));
    }

    public function test_set_and_get(): void
    {
        Setting::set('default_locale', 'vi');
        $this->assertSame('vi', Setting::get('default_locale', 'en'));
    }
}
