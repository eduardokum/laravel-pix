<?php

namespace Eduardokum\LaravelPix;

use Eduardokum\LaravelPix\Contracts\GeneratesQrCode;
use Eduardokum\LaravelPix\Contracts\PixPayloadContract;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use BaconQrCode\Common\ErrorCorrectionLevel;

class QrCodeGenerator implements GeneratesQrCode
{
    public function withPayload(PixPayloadContract $payload): string
    {
        $qrCode = QrCode::size(config('laravel-pix.qr_code_size', 100))->margin(1)->format('png')->errorCorrection(ErrorCorrectionLevel::M());
        return base64_encode($qrCode->generate($payload->getPayload()));
    }
}
