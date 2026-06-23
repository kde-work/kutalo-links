<?php

namespace App\Domain\Link\Exception;

use RuntimeException;

class InvalidDestinationUrlException extends RuntimeException
{
    public function __construct(string $message = 'Некорректный URL назначения')
    {
        parent::__construct($message);
    }
}
