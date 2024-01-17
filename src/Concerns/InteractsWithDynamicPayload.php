<?php

namespace Eduardokum\LaravelPix\Concerns;

use Eduardokum\LaravelPix\Pix;
use Eduardokum\LaravelPix\Exceptions\PixException;
use Eduardokum\LaravelPix\Exceptions\InvalidMerchantInformationException;

trait InteractsWithDynamicPayload
{
    use FormatPayloadValues, HasCR16;

    protected function getAdditionalDataFieldTemplate(): string
    {
        $transaction_id = $this->formatValue(Pix::ADDITIONAL_DATA_FIELD_TEMPLATE_TXID, '***');

        return $this->formatValue(Pix::ADDITIONAL_DATA_FIELD_TEMPLATE, $transaction_id);
    }

    protected function getMerchantAccountInformation(): string
    {
        $gui = $this->formatValue(Pix::MERCHANT_ACCOUNT_INFORMATION_GUI, config('laravel-pix.gui', 'br.gov.bcb.pix'));

        $url = $this->formatValue(
            Pix::MERCHANT_ACCOUNT_INFORMATION_URL,
            preg_replace('/^https?:\/\//', '', $this->url)
        );

        return $this->formatValue(Pix::MERCHANT_ACCOUNT_INFORMATION, $gui, $url);
    }

    protected function getTransactionCurrency(): string
    {
        return $this->formatValue(Pix::TRANSACTION_CURRENCY, config('laravel-pix.transaction_currency_code', '986'));
    }

    protected function getCountryCode(): string
    {
        return $this->formatValue(Pix::COUNTRY_CODE, config('laravel-pix.country_code', 'BR'));
    }

    /**
     * @throws PixException
     */
    protected function getMerchantName(): string
    {
        if (empty($this->merchantName)) {
            throw InvalidMerchantInformationException::merchantNameCantBeEmpty();
        }

        return $this->formatValue(Pix::MERCHANT_NAME, $this->merchantName);
    }

    /**
     * @throws PixException
     */
    protected function getMerchantCity(): string
    {
        if (empty($this->merchantName)) {
            throw InvalidMerchantInformationException::merchantCityCantBeEmpty();
        }

        return $this->formatValue(Pix::MERCHANT_CITY, $this->merchantCity);
    }

    protected function getMerchantCategoryCode(): string
    {
        return $this->formatValue(Pix::MERCHANT_CATEGORY_CODE, '0000');
    }

    protected function getPayloadFormat(): string
    {
        return $this->formatValue(Pix::PAYLOAD_FORMAT_INDICATOR, '01');
    }

    /**
     * @return string
     * @throws PixException
     */
    public function toStringWithoutCrc16(): string
    {
        return $this->getPayloadFormat()
            . $this->getMerchantAccountInformation()
            . $this->getMerchantCategoryCode()
            . $this->gettransactionCurrency()
            . $this->getCountryCode()
            . $this->getMerchantName()
            . $this->getMerchantCity()
            . $this->getAdditionalDataFieldTemplate();
    }

    /**
     * @return string
     * @throws PixException
     */
    protected function buildPayload(): string
    {
        return $this->toStringWithoutCrc16() . $this->getCRC16($this->toStringWithoutCrc16());
    }
}
