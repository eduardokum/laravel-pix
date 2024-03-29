<?php

namespace Eduardokum\LaravelPix;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Eduardokum\LaravelPix\Contracts\GeneratesQrCode;
use Eduardokum\LaravelPix\Contracts\PixPayloadContract;
use Eduardokum\LaravelPix\Exceptions\QrCodeGeneratorException;

class QrCodeGenerator implements GeneratesQrCode
{
    private $qrCode;

    const FORMAT_SVG = 'svg';
    const FORMAT_BMP = 'bmp';
    const FORMAT_GIF = 'gif';
    const FORMAT_JPG = 'jpg';
    const FORMAT_PNG = 'png';
    const FORMAT_WEBP = 'webp';

    public function __construct($format = 'svg')
    {
        if (! in_array($format, [self::FORMAT_SVG, self::FORMAT_BMP, self::FORMAT_GIF, self::FORMAT_JPG, self::FORMAT_PNG, self::FORMAT_WEBP])) {
            throw QrCodeGeneratorException::invalidFormat();
        }

        $options = new QROptions();
        $options->outputType = $format;
        $options->quality = 90;
        $options->scale = 20;
        $options->addQuietzone = true;
        $options->quietzoneSize = 1;
        $options->outputBase64 = true;
        $this->qrCode = new QRCode($options);

        return $this;
    }

    public function getQrCodeObject(): QRCode
    {
        return $this->qrCode;
    }

    public function withPayload($payload): string
    {
        if ($payload instanceof PixPayloadContract) {
            $payload = $payload->getPayload();
        }

        return $this->getQrCodeObject()->render($payload);
    }
}
