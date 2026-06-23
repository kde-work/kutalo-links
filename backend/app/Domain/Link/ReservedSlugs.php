<?php

namespace App\Domain\Link;

/**
 * Зарезервированные slug, недоступные для коротких ссылок.
 */
final class ReservedSlugs
{
    /** @var array<int,string> */
    private const LIST = [
        'api',
        'login',
        'register',
        'sanctum',
        'assets',
        'storage',
        'admin',
        'spa',
        'up',
        'health',
    ];

    public static function isReserved(string $slug): bool
    {
        return in_array(strtolower($slug), self::LIST, true);
    }

    /** @return array<int,string> */
    public static function all(): array
    {
        return self::LIST;
    }
}
