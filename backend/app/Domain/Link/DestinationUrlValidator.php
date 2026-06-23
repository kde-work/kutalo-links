<?php

namespace App\Domain\Link;

use App\Domain\Link\Exception\InvalidDestinationUrlException;

/**
 * Валидация URL назначения короткой ссылки.
 */
final class DestinationUrlValidator
{
    public static function validate(string $url): string
    {
        $url = trim($url);

        if ($url === '') {
            throw new InvalidDestinationUrlException('URL не может быть пустым');
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidDestinationUrlException('Некорректный формат URL');
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        if (!in_array($scheme, ['http', 'https'], true)) {
            throw new InvalidDestinationUrlException('Разрешены только http и https URL');
        }

        return $url;
    }
}
