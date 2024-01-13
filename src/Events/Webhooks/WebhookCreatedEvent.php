<?php

namespace Eduardokum\LaravelPix\Events\Webhooks;

use Illuminate\Foundation\Events\Dispatchable;

class WebhookCreatedEvent
{
    use Dispatchable;

    public array $webhook;

    public function __construct(?array $webhook)
    {
        $this->webhook = $webhook;
    }
}
