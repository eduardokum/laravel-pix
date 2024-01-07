<?php

namespace Eduardokum\LaravelPix\Events\SendPix;

class PixSentEvent
{
    public array $request;

    public array $response;

    public string $idempotencyKey;

    public function __construct(array $request, array $response, string $idempotencyKey)
    {
        $this->request = $request;
        $this->response = $response;
        $this->$idempotencyKey = $idempotencyKey;
    }
}
