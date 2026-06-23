<?php

namespace Tests\Unit\Domain;

use App\Domain\Link\ReservedSlugs;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Проверяет, что системные slug нельзя использовать для коротких ссылок.
 */
class ReservedSlugsTest extends TestCase
{
    #[DataProvider('reservedSlugProvider')]
    public function test_detects_reserved_slugs(string $slug): void
    {
        $this->assertTrue(ReservedSlugs::isReserved($slug));
    }

    public function test_allows_custom_slug(): void
    {
        $this->assertFalse(ReservedSlugs::isReserved('my-promo'));
    }

    /**
     * @return array<string,array{0:string}>
     */
    public static function reservedSlugProvider(): array
    {
        return [
            'api' => ['api'],
            'health' => ['health'],
            'регистр' => ['API'],
        ];
    }
}
