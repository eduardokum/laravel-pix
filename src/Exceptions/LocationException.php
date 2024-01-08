<?php

namespace Eduardokum\LaravelPix\Exceptions;

use Exception;

class LocationException extends Exception
{
    public static function notFound(string $location): LocationException
    {
        return new static("Location `{$location}` Not Found");
    }

    public static function cannotBeDecoded(string $location): LocationException
    {
        return new static("Location `{$location}` cannot be decoded");
    }
}
