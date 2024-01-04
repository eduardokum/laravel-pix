<?php

namespace Eduardokum\LaravelPix\Facades;

use Illuminate\Support\Facades\Facade;
use Eduardokum\LaravelPix\Api\Api;

/**
 * Class ApiConsumes.
 *
 * @method static Api baseUrl(string $baseUrl);
 * @method static Api clientId(string $clientId);
 * @method static Api clientSecret(string $clientSecret);
 * @method static Api certificate(string $certificate);
 * @method static Api certificateKey(string $certificate);
 * @method static Api certificatePassword(string $certificate);
 * @method static mixed getOauth2Token();
 */
class ApiFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return Api::class;
    }
}
