<?php

namespace App\Application\Link\DTO;

class ClickDataDto
{
    public function __construct(
        public readonly ?string $ip,
        public readonly ?string $userAgent,
        public readonly ?string $referer,
    ) {
    }
}
