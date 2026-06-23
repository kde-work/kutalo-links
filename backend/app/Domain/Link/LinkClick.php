<?php

namespace App\Domain\Link;

use DateTimeImmutable;

/**
 * Доменная сущность перехода по короткой ссылке.
 */
class LinkClick
{
    public function __construct(
        private ?int $id,
        private int $shortLinkId,
        private DateTimeImmutable $clickedAt,
        private ?string $ip,
        private ?string $userAgent,
        private ?string $referer,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShortLinkId(): int
    {
        return $this->shortLinkId;
    }

    public function getClickedAt(): DateTimeImmutable
    {
        return $this->clickedAt;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function getReferer(): ?string
    {
        return $this->referer;
    }
}
