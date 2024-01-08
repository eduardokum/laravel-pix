<?php

namespace Eduardokum\LaravelPix\Api\Contracts;

interface ConsumesPixApi
{
    public function getOauth2Token(string $scope = null);

    public function setToken(?string $oauthToken = null);

    public function getToken();
}
