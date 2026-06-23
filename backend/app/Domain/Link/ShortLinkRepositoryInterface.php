<?php

namespace App\Domain\Link;

interface ShortLinkRepositoryInterface
{
    public function findById(int $id): ?ShortLink;

    public function findBySlug(string $slug): ?ShortLink;

    public function slugExists(string $slug, ?int $excludeId = null): bool;

    /** @return array<int,ShortLink> */
    public function findAllByUserId(int $userId): array;

    public function save(ShortLink $link): ShortLink;

    public function delete(int $id): void;
}
