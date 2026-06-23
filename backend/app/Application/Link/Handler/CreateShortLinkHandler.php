<?php

namespace App\Application\Link\Handler;

use App\Application\Link\DTO\CreateShortLinkDto;
use App\Application\Link\Service\SlugGenerator;
use App\Domain\Link\DestinationUrlValidator;
use App\Domain\Link\Exception\SlugAlreadyExistsException;
use App\Domain\Link\ShortLink;
use App\Domain\Link\ShortLinkRepositoryInterface;
use DateTimeImmutable;

class CreateShortLinkHandler
{
    public function __construct(
        private ShortLinkRepositoryInterface $repository,
        private SlugGenerator $slugGenerator,
    ) {
    }

    public function handle(CreateShortLinkDto $dto): ShortLink
    {
        $destinationUrl = DestinationUrlValidator::validate($dto->destinationUrl);

        $slug = $dto->slug !== null && $dto->slug !== ''
            ? $this->slugGenerator->validateCustom($dto->slug)
            : $this->slugGenerator->generate();

        if ($this->repository->slugExists($slug)) {
            throw new SlugAlreadyExistsException($slug);
        }

        $now = new DateTimeImmutable();

        $link = new ShortLink(
            id: null,
            slug: $slug,
            destinationUrl: $destinationUrl,
            title: $dto->title,
            isActive: true,
            userId: $dto->userId,
            createdAt: $now,
            updatedAt: $now,
        );

        return $this->repository->save($link);
    }
}
