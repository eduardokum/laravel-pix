<?php

namespace Eduardokum\LaravelPix\Contracts;

interface PixPayloadContract
{
    public function getPayload(): string;

    public function __toString() : string;
}
