<?php

namespace Junges\Pix\Support;

use InvalidArgumentException;
use Junges\Pix\Contracts\CanResolveEndpoints;

class BankingEndpoints implements CanResolveEndpoints
{
    const OAUTH_TOKEN = 'oauth_token';
    const CREATE_PIX_QR = 'create_pix_qr';
    const CREATE_PIX_MANUAL = 'create_pix_manual';
    const CREATE_PIX_DICT = 'create_pix_dict';
    const GET_PIX_E2E = 'get_pix_e2e';

    const GET_BALANCE = 'get_balance';

    const GET_WEBHOOKS = 'get_webhooks';
    const CREATE_WEBHOOK_TRANSFER = 'create_webhook_transfer';
    const CREATE_WEBHOOK_RECEIVE = 'create_webhook_receive';
    const CREATE_WEBHOOK_REFUND = 'create_webhook_refund';
    const CREATE_WEBHOOK_CASHOUT = 'create_webhook_cashout';
    const CREATE_WEBHOOK_REJECT = 'create_webhook_reject';

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

    public function setEndpoint(string $key, string $value): void
    {
        $this->endpoints[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    public function getEndpoint(string $key): string
    {
        if (!$endpoint = $this->endpoints[$key]) {
            throw new InvalidArgumentException("Endpoint does not exist: '{$key}'");
        }

        return $endpoint;
    }
}
