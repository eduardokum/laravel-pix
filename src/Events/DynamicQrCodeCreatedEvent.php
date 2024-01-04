<?php

namespace Eduardokum\LaravelPix\Events;

class DynamicQrCodeCreatedEvent
{
    public string $pixKey;

    public function __construct(string $pixKey)
    {
        $this->pixKey = $pixKey;
    }
}
