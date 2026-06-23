<?php

namespace App\Http\Resources;

use App\Domain\Link\ShortLink;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ShortLink */
class ShortLinkResource extends JsonResource
{
    /**
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var ShortLink $link */
        $link = $this->resource;

        return [
            'id' => $link->getId(),
            'slug' => $link->getSlug(),
            'destination_url' => $link->getDestinationUrl(),
            'title' => $link->getTitle(),
            'is_active' => $link->isActive(),
            'clicks_count' => $link->getClicksCount(),
            'short_url' => rtrim((string) config('app.url'), '/') . '/' . $link->getSlug(),
            'created_at' => $link->getCreatedAt()->format(DATE_ATOM),
            'updated_at' => $link->getUpdatedAt()->format(DATE_ATOM),
        ];
    }
}
