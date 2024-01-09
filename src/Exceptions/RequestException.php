<?php

namespace Eduardokum\LaravelPix\Exceptions;

class RequestException extends PixException
{
    public static function tokenNotGranted($message = null): PixException
    {
        return new static(trim("The request to obtain the token was unsuccessful: $message", ' :'));
    }

    public static function oauthUrlNotConfigured(): PixException
    {
        return new static('Authentication url not configured');
    }

    public static function credentialsNotConfigured(): PixException
    {
        return new static('Credentials not configured');
    }
}
