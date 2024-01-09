<?php

namespace Eduardokum\LaravelPix;

use Illuminate\Support\Facades\Http;
use Eduardokum\LaravelPix\Api\Resources\Cob\Cob;
use Eduardokum\LaravelPix\Api\Resources\Cobv\Cobv;
use Eduardokum\LaravelPix\Exceptions\LocationException;
use Eduardokum\LaravelPix\Api\Resources\Webhook\Webhook;
use Eduardokum\LaravelPix\Api\Resources\LoteCobv\LoteCobv;
use Eduardokum\LaravelPix\Api\Resources\ReceivedPix\ReceivedPix;
use Eduardokum\LaravelPix\Api\Resources\PayloadLocation\PayloadLocation;

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
    const KEY_TYPES = [
        Pix::RANDOM_KEY_TYPE,
        Pix::CPF_KEY_TYPE,
        Pix::CNPJ_KEY_TYPE,
        Pix::PHONE_NUMBER_KEY_TYPE,
        Pix::EMAIL_KEY_TYPE,
    ];

    private $psp;

    private ?Cob $cob = null;

    private ?Cobv $cobv = null;

    private ?LoteCobv $loteCobv = null;

    private ?Webhook $webhook = null;

    private ?PayloadLocation $payloadLocation = null;

    private ?ReceivedPix $receivedPix = null;

    public function __construct(array $pspConfigs = null)
    {
        $this->psp = new Psp($pspConfigs);
    }

    /**
     * @param array|null $pspConfigs
     * @return static
     */
    public static function make(array $pspConfigs = null)
    {
        return new static($pspConfigs);
    }

    /**
     * Manage instant charges.
     *
     * @return Cob
     */
    public function cob(): Cob
    {
        if ($this->cob) {
            return $this->cob;
        }

        return $this->cob = new Cob($this->getPsp());
    }

    /**
     * Manage charges with a due date.
     *
     * @return Cobv
     */
    public function cobv(): Cobv
    {
        if ($this->cobv) {
            return $this->cobv;
        }

        return $this->cobv = new Cobv($this->getPsp());
    }

    /**
     * Manage batch of charges with due date.
     *
     * @return LoteCobv
     */
    public function loteCobv(): LoteCobv
    {
        if ($this->loteCobv) {
            return $this->loteCobv;
        }

        return $this->loteCobv = new LoteCobv($this->getPsp());
    }

    /**
     * Manage pix key webhooks.
     *
     * @return Webhook
     */
    public function webhook(): Webhook
    {
        if ($this->webhook) {
            return $this->webhook;
        }

        return $this->webhook = new Webhook($this->getPsp());
    }

    /**
     * Manage location configuration to use with payloads.
     *
     * @return PayloadLocation
     */
    public function payloadLocation(): PayloadLocation
    {
        if ($this->payloadLocation) {
            return $this->payloadLocation;
        }

        return $this->payloadLocation = new PayloadLocation($this->getPsp());
    }

    /**
     * Manage received pix.
     *
     * @return ReceivedPix
     */
    public function receivedPix(): ReceivedPix
    {
        if ($this->receivedPix) {
            return $this->receivedPix;
        }

        return $this->receivedPix = new ReceivedPix($this->getPsp());
    }

    public function getOauth2Token(string $scope = null): Pix
    {
        $this->getPsp()->getOauth2Token($scope);

        return $this;
    }

    public function setOnTheFly(array $pspConfigs): Pix
    {
        $this->getPsp()->onTheFly($pspConfigs);

        return $this->propagateChanges();
    }

    public function usingPsp(string $psp): Pix
    {
        $this->getPsp()->currentPsp($pspConfigs);

        return $this->propagateChanges();
    }

    public function usingDefaultPsp(): Pix
    {
        $this->getPsp()->currentPsp('default');

        return $this->propagateChanges();
    }

    public static function createQrCode(Payload $payload)
    {
        return (new QrCodeGenerator())->withPayload($payload);
    }

    public static function fetchLocation($location): array
    {
        $location = 'https://' . preg_replace('/^https?:\/\//', '', $location);
        $response = Http::retry(3, 200)->get($location);

        throw_if(! $response->successful(), LocationException::notFound($location));

        $fetch = $response->body();
        $data = explode('.', $fetch);

        throw_if(count($data) !== 3, LocationException::cannotBeDecoded($location));

        return [
            'fetch'   => $fetch,
            'header'  => json_decode(self::safeBase64Decode($data[0]), true),
            'payload' => json_decode(self::safeBase64Decode($data[1]), true),
        ];
    }

    private static function safeBase64Decode($base64)
    {
        $remainder = strlen($base64) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $base64 .= str_repeat('=', $padlen);
        }

        return base64_decode(strtr($base64, '-_', '+/'));
    }

    private function getPsp(): Psp
    {
        return $this->psp;
    }

    private function propagateChanges(): Pix
    {
        optional($this->cob)->setPsp($this->getPsp());
        optional($this->cobv)->setPsp($this->getPsp());
        optional($this->loteCobv)->setPsp($this->getPsp());
        optional($this->webhook)->setPsp($this->getPsp());
        optional($this->payloadLocation)->setPsp($this->getPsp());
        optional($this->receivedPix)->setPsp($this->getPsp());

        return $this;
    }
}
