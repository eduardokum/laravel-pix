<?php

namespace Eduardokum\LaravelPix\Contracts;

interface PixPayloadContract
{
    public function getPayload(): string;

    public static function decode($payload): array;
}
