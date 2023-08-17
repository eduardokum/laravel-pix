<?php

namespace Junges\Pix\Tests;

use Junges\Pix\Support\Endpoints;

class BankingEndpointsResolver extends Endpoints
{
    public array $endpoints = [
        self::OAUTH_TOKEN => '/oauth/token',
        self::CREATE_PIX_QR => '/pix/payments/qrc',
        self::CREATE_PIX_MANUAL => '/pix/payments/manu',
        self::CREATE_PIX_DICT => '/pix/payments/dict',
        self::GET_PIX_E2E => '/pix/payments/',

        self::GET_BALANCE => '/accounts/balances',

        self::GET_WEBHOOKS => '/webhooks/',
        self::CREATE_WEBHOOK_TRANSFER => '/webhooks/transfer',
        self::CREATE_WEBHOOK_RECEIVE => '/webhooks/receive',
        self::CREATE_WEBHOOK_REFUND => '/webhook/refund',
        self::CREATE_WEBHOOK_CASHOUT => '/webhook/cashout',
        self::CREATE_WEBHOOK_REJECT => '/webhook/reject',
    ];
}
