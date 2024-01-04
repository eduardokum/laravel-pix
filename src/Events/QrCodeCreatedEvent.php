<?php

namespace Eduardokum\LaravelPix\Events;

class QrCodeCreatedEvent
{
    public string $pixKey;

    public function __construct(string $pixKey)
    {
        $this->pixKey = $pixKey;
    }
}
