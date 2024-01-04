<?php

namespace Eduardokum\LaravelPix\Events\Webhooks;

class WebhookCreatedEvent
{
    public array $webhook;

    public function __construct(array $webhook)
    {
        $this->webhook = $webhook;
    }
}
