<?php

namespace App\Application\Link\Handler;

use App\Application\Link\DTO\ClickDataDto;
use App\Domain\Link\Exception\ShortLinkNotFoundException;
use App\Domain\Link\LinkClick;
use App\Domain\Link\LinkClickRepositoryInterface;
use App\Domain\Link\ShortLink;
use App\Domain\Link\ShortLinkRepositoryInterface;
use DateTimeImmutable;

class RecordClickHandler
{
    public function __construct(
        private ShortLinkRepositoryInterface $shortLinkRepository,
        private LinkClickRepositoryInterface $clickRepository,
    ) {
    }

    public function handle(string $slug, ClickDataDto $clickData): ShortLink
    {
        $link = $this->shortLinkRepository->findBySlug($slug);

        if ($link === null || !$link->isActive()) {
            throw new ShortLinkNotFoundException('Ссылка не найдена или неактивна');
        }

        $click = new LinkClick(
            id: null,
            shortLinkId: (int) $link->getId(),
            clickedAt: new DateTimeImmutable(),
            ip: $clickData->ip,
            userAgent: $clickData->userAgent,
            referer: $clickData->referer,
        );

        $this->clickRepository->save($click);

        return $link;
    }
}
