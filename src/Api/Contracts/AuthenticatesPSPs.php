<?php

namespace Eduardokum\LaravelPix\Api\Contracts;

interface AuthenticatesPSPs
{
    public function getToken(string $scopes = null);

    public function getOauthEndpoint(): string;
}
