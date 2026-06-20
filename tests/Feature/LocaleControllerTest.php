<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_switch_sets_cookie_and_redirects(): void
    {
        $response = $this->post('/locale', ['locale' => 'vi']);

        $response->assertRedirect();
        $response->assertCookie('app_locale', 'vi');
    }

    public function test_switch_rejects_unsupported_locale(): void
    {
        $response = $this->post('/locale', ['locale' => 'ja']);

        $response->assertRedirect();
        $response->assertCookieMissing('app_locale');
    }

    public function test_locale_middleware_sets_locale_from_cookie(): void
    {
        $response = $this->withCookie('app_locale', 'vi')->get('/');

        $this->assertSame('vi', app()->getLocale());
    }
}
