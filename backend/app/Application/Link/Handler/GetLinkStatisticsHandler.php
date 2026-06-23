<?php

namespace App\Application\Link\Handler;

use App\Application\Link\DTO\LinkStatisticsDto;
use App\Domain\Link\Exception\ShortLinkNotFoundException;
use App\Domain\Link\LinkClickRepositoryInterface;
use App\Domain\Link\ShortLinkRepositoryInterface;
use DateTimeImmutable;

class GetLinkStatisticsHandler
{
    public function __construct(
        private ShortLinkRepositoryInterface $shortLinkRepository,
        private LinkClickRepositoryInterface $clickRepository,
    ) {
    }

    public function handle(int $id, int $userId, int $days = 30): LinkStatisticsDto
    {
        $link = $this->shortLinkRepository->findById($id);

        if ($link === null || $link->getUserId() !== $userId) {
            throw new ShortLinkNotFoundException();
        }

        $linkId = (int) $link->getId();
        $since7Days = new DateTimeImmutable('-7 days');

        return new LinkStatisticsDto(
            total: $this->clickRepository->countByShortLinkId($linkId),
            last7Days: $this->clickRepository->countByShortLinkIdSince($linkId, $since7Days),
            byDay: $this->clickRepository->countByDay($linkId, $days),
            recentClicks: $this->clickRepository->findRecentByShortLinkId($linkId, 50),
        );
    }
}
