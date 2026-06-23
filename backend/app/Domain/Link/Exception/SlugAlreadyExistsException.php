<?php

namespace App\Domain\Link\Exception;

use RuntimeException;

class SlugAlreadyExistsException extends RuntimeException
{
    public function __construct(string $slug)
    {
        parent::__construct("Slug уже занят: {$slug}");
    }
}
