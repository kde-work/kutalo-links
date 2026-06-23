<?php

namespace App\Domain\Link;

interface LinkClickRepositoryInterface
{
    public function save(LinkClick $click): LinkClick;

    public function countByShortLinkId(int $shortLinkId): int;

    public function countByShortLinkIdSince(int $shortLinkId, \DateTimeImmutable $since): int;

    /**
     * @return array<int,array{date:string,count:int}>
     */
    public function countByDay(int $shortLinkId, int $days): array;

    /**
     * @return array<int,LinkClick>
     */
    public function findRecentByShortLinkId(int $shortLinkId, int $limit): array;
}
