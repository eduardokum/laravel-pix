<?php

namespace Eduardokum\LaravelPix\Events\Webhooks;

use Illuminate\Foundation\Events\Dispatchable;

class WebhookDeletedEvent
{
    use Dispatchable;

    public string $pixKey;

    public function __construct(string $pixKey)
    {
        $this->pixKey = $pixKey;
    }
}
