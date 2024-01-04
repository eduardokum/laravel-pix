<?php

namespace Eduardokum\LaravelPix\Api\Contracts;

interface ConsumesWebhookEndpoints extends ConsumesPixApi
{
    public function create(string $pixKey, string $callbackUrl);

    public function getByPixKey(string $pixKey);

    public function delete(string $pixKey);

    public function all();
}
