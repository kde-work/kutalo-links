<?php

namespace App\Infrastructure\Link\Mapper;

use App\Domain\Link\LinkClick;
use App\Infrastructure\Link\Eloquent\LinkClickModel;
use DateTimeImmutable;

class LinkClickMapper
{
    public function toDomain(LinkClickModel $model): LinkClick
    {
        return new LinkClick(
            id: $model->id,
            shortLinkId: (int) $model->short_link_id,
            clickedAt: DateTimeImmutable::createFromMutable($model->clicked_at),
            ip: $model->ip,
            userAgent: $model->user_agent,
            referer: $model->referer,
        );
    }

    public function fillModel(LinkClick $click, LinkClickModel $model): LinkClickModel
    {
        $model->short_link_id = $click->getShortLinkId();
        $model->clicked_at = $click->getClickedAt();
        $model->ip = $click->getIp();
        $model->user_agent = $click->getUserAgent();
        $model->referer = $click->getReferer();

        return $model;
    }
}
