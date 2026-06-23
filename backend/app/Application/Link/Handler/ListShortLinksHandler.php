<?php

namespace App\Application\Link\Handler;

use App\Domain\Link\Exception\ShortLinkNotFoundException;
use App\Domain\Link\ShortLink;
use App\Domain\Link\ShortLinkRepositoryInterface;

class ListShortLinksHandler
{
    public function __construct(
        private ShortLinkRepositoryInterface $repository,
    ) {
    }

    /**
     * @return array<int,ShortLink>
     */
    public function handle(int $userId): array
    {
        return $this->repository->findAllByUserId($userId);
    }
}
