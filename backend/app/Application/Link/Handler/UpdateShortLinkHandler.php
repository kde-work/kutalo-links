<?php

namespace App\Application\Link\Handler;

use App\Domain\Link\Exception\ShortLinkNotFoundException;
use App\Domain\Link\ShortLink;
use App\Domain\Link\ShortLinkRepositoryInterface;
use DateTimeImmutable;

class UpdateShortLinkHandler
{
    public function __construct(
        private ShortLinkRepositoryInterface $repository,
    ) {
    }

    public function handle(int $id, int $userId, ?string $title, ?bool $isActive): ShortLink
    {
        $link = $this->findOwnedLink($id, $userId);
        $updated = $link;
        $now = new DateTimeImmutable();

        if ($title !== null) {
            $updated = $updated->withTitle($title === '' ? null : $title, $now);
        }

        if ($isActive !== null) {
            $updated = $updated->withActive($isActive, $now);
        }

        return $this->repository->save($updated);
    }

    private function findOwnedLink(int $id, int $userId): ShortLink
    {
        $link = $this->repository->findById($id);

        if ($link === null || $link->getUserId() !== $userId) {
            throw new ShortLinkNotFoundException();
        }

        return $link;
    }
}
