<?php

namespace App\Application\Link\Handler;

use App\Domain\Link\DestinationUrlValidator;
use App\Domain\Link\Exception\ShortLinkNotFoundException;
use App\Domain\Link\ShortLink;
use App\Domain\Link\ShortLinkRepositoryInterface;
use DateTimeImmutable;

class UpdateShortLinkDestinationHandler
{
    public function __construct(
        private ShortLinkRepositoryInterface $repository,
    ) {
    }

    public function handle(int $id, int $userId, string $destinationUrl): ShortLink
    {
        $link = $this->findOwnedLink($id, $userId);
        $validatedUrl = DestinationUrlValidator::validate($destinationUrl);
        $updated = $link->withDestinationUrl($validatedUrl, new DateTimeImmutable());

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
