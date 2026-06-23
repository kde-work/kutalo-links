<?php

namespace App\Domain\Link\Exception;

use RuntimeException;

class ShortLinkNotFoundException extends RuntimeException
{
    public function __construct(string $message = 'Короткая ссылка не найдена')
    {
        parent::__construct($message);
    }
}
