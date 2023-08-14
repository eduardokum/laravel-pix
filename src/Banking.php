<?php

namespace Junges\Pix;

use Junges\Pix\Api\Api;
use Junges\Pix\Api\Resources\Banking\Pix;
use Junges\Pix\Api\Resources\Banking\Balance;
use Junges\Pix\Api\Resources\Banking\Webhook;

class Banking
{
    /**
     * This method allows you to use only OAuth endpoints.
     *
     * @return Api
     */
    public static function api(): Api
    {
        return new Api();
    }

    /**
     * Manage balance.
     *
     * @return Webhook
     */
    public static function balance(): Balance
    {
        return new Balance();
    }

    /**
     * Manage pix send.
     *
     * @return Webhook
     */
    public static function pix(): Pix
    {
        return new Pix();
    }

    /**
     * Manage pix webhooks.
     *
     * @return Webhook
     */
    public static function webhook(): Webhook
    {
        return new Webhook();
    }
}
