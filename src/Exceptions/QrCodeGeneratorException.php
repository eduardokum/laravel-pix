<?php

namespace Eduardokum\LaravelPix\Exceptions;

class QrCodeGeneratorException extends PixException
{
    public static function invalidFormat()
    {
        return new static('The format is invalid');
    }
}
