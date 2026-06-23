<?php

namespace App\Application\Link\DTO;

use App\Domain\Link\LinkClick;

class LinkStatisticsDto
{
    /**
     * @param array<int,array{date:string,count:int}> $byDay
     * @param array<int,LinkClick> $recentClicks
     */
    public function __construct(
        public readonly int $total,
        public readonly int $last7Days,
        public readonly array $byDay,
        public readonly array $recentClicks,
    ) {
    }
}
