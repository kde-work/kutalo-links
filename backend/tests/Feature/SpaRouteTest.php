<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Проверяет, что зарезервированные пути и fallback отдают SPA, а не 404 редиректа.
 */
class SpaRouteTest extends TestCase
{
    private const STUB_HTML = '<!doctype html><html><body>spa</body></html>';

    private ?string $originalSpaIndex = null;

    private bool $spaIndexExisted = false;

    protected function setUp(): void
    {
        parent::setUp();

        $spaIndex = public_path('spa/browser/index.html');
        $this->spaIndexExisted = is_file($spaIndex);
        if ($this->spaIndexExisted) {
            $this->originalSpaIndex = file_get_contents($spaIndex);
        }

        $spaDir = public_path('spa/browser');
        if (! is_dir($spaDir)) {
            mkdir($spaDir, 0777, true);
        }

        file_put_contents($spaIndex, self::STUB_HTML);
    }

    protected function tearDown(): void
    {
        $spaIndex = public_path('spa/browser/index.html');

        if ($this->spaIndexExisted && $this->originalSpaIndex !== null) {
            file_put_contents($spaIndex, $this->originalSpaIndex);
        } elseif (! $this->spaIndexExisted) {
            @unlink($spaIndex);
        }

        parent::tearDown();
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
