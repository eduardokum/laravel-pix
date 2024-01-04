<?php

namespace Eduardokum\LaravelPix\Exceptions;

class ParserException extends PixException
{
    public static function cantParsePixKey()
    {
        return new static("Your pix key couldn't be parsed");
    }
}
