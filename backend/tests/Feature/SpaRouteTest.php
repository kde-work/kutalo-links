<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Проверяет, что зарезервированные пути и fallback отдают SPA, а не 404 редиректа.
 */
class SpaRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $spaDir = public_path('spa/browser');
        if (! is_dir($spaDir)) {
            mkdir($spaDir, 0777, true);
        }

        file_put_contents($spaDir.'/index.html', '<!doctype html><html><body>spa</body></html>');
    }

    public function test_login_path_serves_spa(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('spa', false);
    }

    public function test_deep_spa_path_uses_fallback(): void
    {
        $this->get('/links/new')
            ->assertOk()
            ->assertSee('spa', false);
    }
}
