<?php

namespace App\Application\Link\DTO;

class CreateShortLinkDto
{
    public function __construct(
        public readonly int $userId,
        public readonly ?string $slug,
        public readonly string $destinationUrl,
        public readonly ?string $title,
    ) {
    }
}
