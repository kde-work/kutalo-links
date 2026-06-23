<?php

namespace App\Application\Link\Handler;

use App\Domain\Link\Exception\ShortLinkNotFoundException;
use App\Domain\Link\ShortLink;
use App\Domain\Link\ShortLinkRepositoryInterface;

class GetShortLinkHandler
{
    public function __construct(
        private ShortLinkRepositoryInterface $repository,
    ) {
    }

    public function handle(int $id, int $userId): ShortLink
    {
        $link = $this->repository->findById($id);

        if ($link === null || $link->getUserId() !== $userId) {
            throw new ShortLinkNotFoundException();
        }

        return $link;
    }
}
