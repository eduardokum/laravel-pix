<?php

namespace Eduardokum\LaravelPix\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Eduardokum\LaravelPix\Api\Api;
use Eduardokum\LaravelPix\Api\Auth;
use Eduardokum\LaravelPix\Api\Resources\Cob\Cob;
use Eduardokum\LaravelPix\Api\Resources\Cobv\Cobv;
use Eduardokum\LaravelPix\Api\Resources\PayloadLocation\PayloadLocation;
use Eduardokum\LaravelPix\Api\Resources\ReceivedPix\ReceivedPix;
use Eduardokum\LaravelPix\Api\Resources\Webhook\Webhook;
use Eduardokum\LaravelPix\Facades\ApiFacade;
use Eduardokum\LaravelPix\Facades\CobFacade;
use Eduardokum\LaravelPix\Facades\CobvFacade;
use Eduardokum\LaravelPix\Facades\PayloadLocationFacade;
use Eduardokum\LaravelPix\Facades\ReceivedPixFacade;
use Eduardokum\LaravelPix\Facades\WebhookFacade;
use Eduardokum\LaravelPix\LaravelPix;
use Eduardokum\LaravelPix\QrCodeGenerator;

class PixServiceProvider extends ServiceProvider
{
    public static bool $verifySslCertificate = false;

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/laravel-pix.php' => config_path('laravel-pix.php'),
        ], 'laravel-pix-config');
    }

    public function register()
    {
        LaravelPix::generatesQrCodeUsing(QrCodeGenerator::class);
        LaravelPix::authenticatesUsing(Auth::class);
        LaravelPix::useAsDefaultPsp('default');
        $this->registerFacades();
    }

    private function registerFacades(): void
    {
        $this->app->bind(ApiFacade::class, Api::class);
        $this->app->bind(CobFacade::class, Cob::class);
        $this->app->bind(CobvFacade::class, Cobv::class);
        $this->app->bind(WebhookFacade::class, Webhook::class);
        $this->app->bind(PayloadLocationFacade::class, PayloadLocation::class);
        $this->app->bind(ReceivedPixFacade::class, ReceivedPix::class);
    }
}
