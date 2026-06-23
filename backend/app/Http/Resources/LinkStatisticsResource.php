<?php

namespace App\Http\Resources;

use App\Application\Link\DTO\LinkStatisticsDto;
use App\Domain\Link\LinkClick;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin LinkStatisticsDto */
class LinkStatisticsResource extends JsonResource
{
    /**
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var LinkStatisticsDto $stats */
        $stats = $this->resource;

        return [
            'total' => $stats->total,
            'last_7_days' => $stats->last7Days,
            'by_day' => $stats->byDay,
            'recent_clicks' => array_map(static function (LinkClick $click) {
                return [
                    'id' => $click->getId(),
                    'clicked_at' => $click->getClickedAt()->format(DATE_ATOM),
                    'ip' => $click->getIp(),
                    'user_agent' => $click->getUserAgent(),
                    'referer' => $click->getReferer(),
                ];
            }, $stats->recentClicks),
        ];
    }
}
