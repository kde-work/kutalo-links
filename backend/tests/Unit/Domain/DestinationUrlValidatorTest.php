<?php

namespace Tests\Unit\Domain;

use App\Domain\Link\DestinationUrlValidator;
use App\Domain\Link\Exception\InvalidDestinationUrlException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Проверяет инварианты валидации URL назначения короткой ссылки.
 */
class DestinationUrlValidatorTest extends TestCase
{
    public function test_accepts_http_and_https_urls(): void
    {
        $this->assertSame(
            'https://example.com/path',
            DestinationUrlValidator::validate('https://example.com/path'),
        );
        $this->assertSame(
            'http://example.org',
            DestinationUrlValidator::validate('http://example.org'),
        );
    }

    public function test_trims_whitespace(): void
    {
        $this->assertSame(
            'https://example.com',
            DestinationUrlValidator::validate('  https://example.com  '),
        );
    }

    #[DataProvider('invalidUrlProvider')]
    public function test_rejects_invalid_urls(string $url, string $messagePart): void
    {
        $this->expectException(InvalidDestinationUrlException::class);
        $this->expectExceptionMessage($messagePart);

        DestinationUrlValidator::validate($url);
    }

    /**
     * @return array<string,array{0:string,1:string}>
     */
    public static function invalidUrlProvider(): array
    {
        return [
            'пустой' => ['', 'пустым'],
            'не url' => ['not-a-url', 'формат'],
            'ftp' => ['ftp://files.example.com', 'http и https'],
        ];
    }
}
