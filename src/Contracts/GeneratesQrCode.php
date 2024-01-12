<?php

namespace Eduardokum\LaravelPix\Contracts;

interface GeneratesQrCode
{
    public function withPayload($payload);

    public function getQrCodeObject();
}
