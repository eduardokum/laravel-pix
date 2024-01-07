<?php

namespace Eduardokum\LaravelPix\Exceptions;

class TokenAbsentException extends PixException
{
    public static function uninformed()
    {
        return new static('Token não informado na requisição');
    }
}
