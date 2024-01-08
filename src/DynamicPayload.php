<?php

namespace Eduardokum\LaravelPix;

use Illuminate\Support\Str;
use Eduardokum\LaravelPix\Concerns\DecodePayload;
use Eduardokum\LaravelPix\Contracts\PixPayloadContract;
use Eduardokum\LaravelPix\Concerns\InteractsWithDynamicPayload;
use Eduardokum\LaravelPix\Exceptions\InvalidTransactionIdException;

class DynamicPayload implements PixPayloadContract
{
    use InteractsWithDynamicPayload, DecodePayload;

    protected string $merchantName;

    protected string $merchantCity;

    protected string $transaction_id;

    private string $url;

    private bool $reusable;

    public function transactionId(string $transaction_id): DynamicPayload
    {
        throw_if(
            Str::length($transaction_id) < Pix::MIN_TRANSACTION_ID_LENGTH,
            InvalidTransactionIdException::invalidLengthForDynamicPayload()
        );

        $this->transaction_id = $transaction_id;

        return $this;
    }

    public function merchantName(string $merchantName): DynamicPayload
    {
        $this->merchantName = Str::length($merchantName) > Pix::MAX_MERCHANT_NAME_LENGTH
            ? substr($merchantName, 0, Pix::MAX_MERCHANT_NAME_LENGTH)
            : $merchantName;

        return $this;
    }

    public function merchantCity(string $merchantCity): DynamicPayload
    {
        $this->merchantCity = Str::length($merchantCity) > Pix::MAX_MERCHANT_CITY_LENGTH
            ? substr($merchantCity, 0, Pix::MAX_MERCHANT_CITY_LENGTH)
            : $merchantCity;

        return $this;
    }

    public function canBeReused(): DynamicPayload
    {
        $this->reusable = true;

        return $this;
    }

    public function mustBeUnique(): DynamicPayload
    {
        $this->reusable = false;

        return $this;
    }

    public function url(string $url): DynamicPayload
    {
        $this->url = $url;

        return $this;
    }

    public function getPayload(): string
    {
        return $this->buildPayload();
    }

    public static function decode($payload): array
    {
        return self::decodeRecursivePayload($payload);
    }
}
