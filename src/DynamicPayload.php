<?php

namespace Eduardokum\LaravelPix;

use Illuminate\Support\Str;
use Eduardokum\LaravelPix\Concerns\DecodePayload;
use Eduardokum\LaravelPix\Contracts\PixPayloadContract;
use Eduardokum\LaravelPix\Concerns\InteractsWithDynamicPayload;

class DynamicPayload implements PixPayloadContract
{
    use InteractsWithDynamicPayload, DecodePayload;

    protected string $merchantName;

    protected string $merchantCity;

    private string $url;

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

    public function url(string $url): DynamicPayload
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     * @throws Exceptions\PixException
     */
    public function getPayload(): string
    {
        return $this->buildPayload();
    }
}
