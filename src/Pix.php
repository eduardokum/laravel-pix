<?php

namespace Eduardokum\LaravelPix;

use Eduardokum\LaravelPix\Api\Api;
use Eduardokum\LaravelPix\Api\Resources\Cob\Cob;
use Eduardokum\LaravelPix\Api\Resources\Cobv\Cobv;
use Eduardokum\LaravelPix\Api\Resources\Webhook\Webhook;
use Eduardokum\LaravelPix\Api\Resources\LoteCobv\LoteCobv;
use Eduardokum\LaravelPix\Api\Resources\ReceivedPix\ReceivedPix;
use Eduardokum\LaravelPix\Api\Resources\PayloadLocation\PayloadLocation;

/**
 * @method  Api api();
 * @method  Cob cob();
 * @method  Cobv cobv();
 * @method  LoteCobv loteCobv();
 * @method  Webhook webhook();
 * @method  PayloadLocation payloadLocation();
 * @method  ReceivedPix receivedPix();
 */
class Pix
{
    const PAYLOAD_FORMAT_INDICATOR = '00';
    const POINT_OF_INITIATION_METHOD = '01';
    const MERCHANT_ACCOUNT_INFORMATION = '26';
    const MERCHANT_ACCOUNT_INFORMATION_URL = '25';
    const MERCHANT_ACCOUNT_INFORMATION_GUI = '00';
    const MERCHANT_ACCOUNT_INFORMATION_KEY = '01';
    const MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION = '02';
    const MERCHANT_CATEGORY_CODE = '52';
    const TRANSACTION_CURRENCY = '53';
    const TRANSACTION_AMOUNT = '54';
    const COUNTRY_CODE = '58';
    const MERCHANT_NAME = '59';
    const MERCHANT_CITY = '60';
    const ADDITIONAL_DATA_FIELD_TEMPLATE = '62';
    const ADDITIONAL_DATA_FIELD_TEMPLATE_TXID = '05';
    const CRC16 = '63';
    const CRC16_LENGTH = '04';
    const MAX_DESCRIPTION_LENGTH = 40;
    const MAX_MERCHANT_NAME_LENGTH = 25;
    const MAX_MERCHANT_CITY_LENGTH = 15;
    const MAX_TRANSACTION_ID_LENGTH = 25;
    const MAX_AMOUNT_LENGTH = 13;
    const MIN_TRANSACTION_ID_LENGTH = 26;
    const RANDOM_KEY_TYPE = 'random';
    const CPF_KEY_TYPE = 'cpf';
    const CNPJ_KEY_TYPE = 'cnpj';
    const PHONE_NUMBER_KEY_TYPE = 'phone';
    const EMAIL_KEY_TYPE = 'email';

    private static $pspConfigs;

    const KEY_TYPES = [
        Pix::RANDOM_KEY_TYPE,
        Pix::CPF_KEY_TYPE,
        Pix::CNPJ_KEY_TYPE,
        Pix::PHONE_NUMBER_KEY_TYPE,
        Pix::EMAIL_KEY_TYPE,
    ];

    public static function usingOnTheFlyPsp($pspConfigs)
    {
        $s = (new static());
        $s::$pspConfigs = $pspConfigs;

        return $s;
    }

    public static function createQrCode(Payload $payload)
    {
        return (new QrCodeGenerator())->withPayload($payload);
    }

    /**
     * This method allows you to use only OAuth endpoints.
     *
     * @return Api
     */
    public static function api(): Api
    {
        $api = new Api();
        if (self::$pspConfigs) {
            $api->usingOnTheFlyPsp(self::$pspConfigs);
        }

        return $api;
    }

    /**
     * Manage instant charges.
     *
     * @return Cob
     */
    public static function cob(): Cob
    {
        $cob = new Cob();
        if (self::$pspConfigs) {
            $cob->usingOnTheFlyPsp(self::$pspConfigs);
        }

        return $cob;
    }

    /**
     * Manage charges with a due date.
     *
     * @return Cobv
     */
    public static function cobv(): Cobv
    {
        $cobv = new Cobv();
        if (self::$pspConfigs) {
            $cobv->usingOnTheFlyPsp(self::$pspConfigs);
        }

        return $cobv;
    }

    /**
     * Manage batch of charges with due date.
     *
     * @return LoteCobv
     */
    public static function loteCobv(): LoteCobv
    {
        $loteCobv = new LoteCobv();
        if (self::$pspConfigs) {
            $loteCobv->usingOnTheFlyPsp(self::$pspConfigs);
        }

        return $loteCobv;
    }

    /**
     * Manage pix key webhooks.
     *
     * @return Webhook
     */
    public static function webhook(): Webhook
    {
        $webhook = new Webhook();
        if (self::$pspConfigs) {
            $webhook->usingOnTheFlyPsp(self::$pspConfigs);
        }

        return $webhook;
    }

    /**
     * Manage location configuration to use with payloads.
     *
     * @return PayloadLocation
     */
    public static function payloadLocation(): PayloadLocation
    {
        $payloadLocation = new PayloadLocation();
        if (self::$pspConfigs) {
            $payloadLocation->usingOnTheFlyPsp(self::$pspConfigs);
        }

        return $payloadLocation;
    }

    /**
     * Manage received pix.
     *
     * @return ReceivedPix
     */
    public static function receivedPix(): ReceivedPix
    {
        $receivedPix = new ReceivedPix();
        if (self::$pspConfigs) {
            $receivedPix->usingOnTheFlyPsp(self::$pspConfigs);
        }

        return $receivedPix;
    }
}
