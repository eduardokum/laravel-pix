<?php

namespace Eduardokum\LaravelPix\Api\Contracts;

interface AuthenticatesPSPs
{
    public function getToken(string $scope = null);

    public function getOauthEndpoint(): string;
}
