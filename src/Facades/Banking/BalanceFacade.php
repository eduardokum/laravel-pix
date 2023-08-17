<?php

namespace Junges\Pix\Facades;

use Illuminate\Support\Facades\Facade;
use Junges\Pix\Api\Resources\Banking\Balance;

/**
 * Class ApiConsumes.
 *
 * @method static Webhook baseUrl(string $baseUrl);
 * @method static Webhook clientId(string $clientId);
 * @method static Webhook clientSecret(string $clientSecret);
 * @method static Webhook certificate(string $certificate);
 * @method static mixed getOauth2Token();
 * @method static array getBalance(string $request);
 * @method static array all();
 */
class BalanceFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return Balance::class;
    }
}
