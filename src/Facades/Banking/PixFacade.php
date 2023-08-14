<?php

namespace Junges\Pix\Facades\Banking;

use Illuminate\Support\Facades\Facade;
use Junges\Pix\Api\Resources\Banking\Webhook;

/**
 * Class ApiConsumes.
 *
 * @method static Webhook baseUrl(string $baseUrl);
 * @method static Webhook clientId(string $clientId);
 * @method static Webhook clientSecret(string $clientSecret);
 * @method static Webhook certificate(string $certificate);
 * @method static mixed getOauth2Token();
 * @method static array createWithQr(string $request, string $idempotencyKey);
 * @method static array createWithDict(string $request, string $idempotencyKey);
 * @method static array createWithManual(string $request, string $idempotencyKey);
 * @method static array getBye2eid(string $e2eid);
 */
class WebhookFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return Webhook::class;
    }
}
