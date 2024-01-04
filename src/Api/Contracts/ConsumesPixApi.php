<?php

namespace Eduardokum\LaravelPix\Api\Contracts;

interface ConsumesPixApi
{
    public function getOauth2Token(string $scopes = null);
}
