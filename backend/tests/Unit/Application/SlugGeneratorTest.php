<?php

namespace Tests\Unit\Application;

use App\Application\Link\Service\SlugGenerator;
use App\Domain\Link\ShortLinkRepositoryInterface;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Проверяет генерацию и валидацию slug без обращения к БД.
 */
class SlugGeneratorTest extends TestCase
{
    private ShortLinkRepositoryInterface&MockInterface $repository;

    private SlugGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(ShortLinkRepositoryInterface::class);
        $this->generator = new SlugGenerator($this->repository);
    }

    public function test_generate_returns_unique_non_reserved_slug(): void
    {
        $this->repository
            ->shouldReceive('slugExists')
            ->once()
            ->andReturn(false);

        $slug = $this->generator->generate();

        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9_-]{7,12}$/', $slug);
        $this->assertFalse(\App\Domain\Link\ReservedSlugs::isReserved($slug));
    }

    public function test_validate_custom_accepts_valid_slug(): void
    {
        $this->assertSame('my-link_1', $this->generator->validateCustom('my-link_1'));
    }

    public function test_validate_custom_accepts_single_character_slug(): void
    {
        $this->assertSame('1', $this->generator->validateCustom('1'));
    }

    public function test_validate_custom_rejects_invalid_format(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('1-32 символа');

        $this->generator->validateCustom('bad slug');
    }

    public function test_validate_custom_rejects_reserved_slug(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('зарезервирован');

        $this->generator->validateCustom('api');
    }
}
