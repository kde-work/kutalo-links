<?php

namespace App\Infrastructure\Link\Mapper;

use App\Domain\Link\ShortLink;
use App\Infrastructure\Link\Eloquent\ShortLinkModel;
use DateTimeImmutable;

class ShortLinkMapper
{
    public function toDomain(ShortLinkModel $model, int $clicksCount = 0): ShortLink
    {
        return new ShortLink(
            id: $model->id,
            slug: $model->slug,
            destinationUrl: $model->destination_url,
            title: $model->title,
            isActive: (bool) $model->is_active,
            userId: (int) $model->user_id,
            createdAt: DateTimeImmutable::createFromMutable($model->created_at),
            updatedAt: DateTimeImmutable::createFromMutable($model->updated_at),
            clicksCount: $clicksCount,
        );
    }

    public function fillModel(ShortLink $link, ShortLinkModel $model): ShortLinkModel
    {
        $model->slug = $link->getSlug();
        $model->destination_url = $link->getDestinationUrl();
        $model->title = $link->getTitle();
        $model->is_active = $link->isActive();
        $model->user_id = $link->getUserId();

        return $model;
    }
}
