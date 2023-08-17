<?php

namespace Junges\Pix\Api\Contracts;

interface AuthenticatesBankingPSPs
{
    public function getBankingToken(string $scopes = null);

    public function getBankingOauthEndpoint(): string;
}
