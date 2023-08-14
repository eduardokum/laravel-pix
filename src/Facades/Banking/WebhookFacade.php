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
 * @method static array createTransferWebhook(string $callbackUrl);
 * @method static array createReceiveWebhook(string $callbackUrl);
 * @method static array createRefundWebhook(string $callbackUrl);
 * @method static array createCashoutWebhook(string $callbackUrl);
 * @method static array createRejectWebhook(string $callbackUrl);
 * @method static array all();
 */
class WebhookFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return Webhook::class;
    }
}
