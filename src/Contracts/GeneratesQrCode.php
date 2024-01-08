<?php

namespace Eduardokum\LaravelPix\Contracts;

interface GeneratesQrCode
{
    public function withPayload(PixPayloadContract $payload);

    public function withStringPayload(string $payload);

    public function getQrCodeObject();
}
