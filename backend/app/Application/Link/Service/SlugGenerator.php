<?php

namespace App\Application\Link\Service;

use App\Domain\Link\ReservedSlugs;
use App\Domain\Link\ShortLinkRepositoryInterface;

/**
 * Генерация уникального slug для короткой ссылки.
 */
class SlugGenerator
{
    public function __construct(
        private ShortLinkRepositoryInterface $repository,
    ) {
    }

    public function generate(): string
    {
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $slug = $this->randomSlug();

            if (!ReservedSlugs::isReserved($slug) && !$this->repository->slugExists($slug)) {
                return $slug;
            }
        }

        return bin2hex(random_bytes(6));
    }

    public function validateCustom(string $slug): string
    {
        $slug = trim($slug);

        if (!preg_match('/^[a-zA-Z0-9_-]{2,32}$/', $slug)) {
            throw new \InvalidArgumentException('Slug должен содержать 2-32 символа: буквы, цифры, _ и -');
        }

        if (ReservedSlugs::isReserved($slug)) {
            throw new \InvalidArgumentException('Этот slug зарезервирован системой');
        }

        return $slug;
    }

    private function randomSlug(): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $length = 7;
        $slug = '';

        for ($i = 0; $i < $length; $i++) {
            $slug .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $slug;
    }
}
