<?php

namespace App\Domain\Link;

use DateTimeImmutable;

/**
 * Доменная сущность короткой ссылки.
 */
class ShortLink
{
    public function __construct(
        private ?int $id,
        private string $slug,
        private string $destinationUrl,
        private ?string $title,
        private bool $isActive,
        private int $userId,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
        private int $clicksCount = 0,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDestinationUrl(): string
    {
        return $this->destinationUrl;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getClicksCount(): int
    {
        return $this->clicksCount;
    }

    public function withDestinationUrl(string $destinationUrl, DateTimeImmutable $updatedAt): self
    {
        $clone = clone $this;
        $clone->destinationUrl = $destinationUrl;
        $clone->updatedAt = $updatedAt;

        return $clone;
    }

    public function withTitle(?string $title, DateTimeImmutable $updatedAt): self
    {
        $clone = clone $this;
        $clone->title = $title;
        $clone->updatedAt = $updatedAt;

        return $clone;
    }

    public function withActive(bool $isActive, DateTimeImmutable $updatedAt): self
    {
        $clone = clone $this;
        $clone->isActive = $isActive;
        $clone->updatedAt = $updatedAt;

        return $clone;
    }
}
