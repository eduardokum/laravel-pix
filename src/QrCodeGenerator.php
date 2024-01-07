<?php

namespace Eduardokum\LaravelPix;

use SimpleSoftwareIO\QrCode\Generator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use BaconQrCode\Common\ErrorCorrectionLevel;
use Eduardokum\LaravelPix\Contracts\GeneratesQrCode;
use Eduardokum\LaravelPix\Contracts\PixPayloadContract;

class QrCodeGenerator implements GeneratesQrCode
{
    private $qrCode;

    public function __construct()
    {
        $this->qrCode = QrCode::size(config('laravel-pix.qr_code_size', 100))->margin(1)->format('png')->errorCorrection(ErrorCorrectionLevel::M());

        return $this;
    }

    public function getQrCodeObject(): Generator
    {
        return $this->qrCode;
    }

    public function withPayload(PixPayloadContract $payload): string
    {
        return 'data:image/png;base64,' . base64_encode($this->getQrCodeObject()->generate($payload->getPayload()));
    }
}
