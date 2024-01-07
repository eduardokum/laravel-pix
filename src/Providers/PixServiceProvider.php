<?php

namespace Eduardokum\LaravelPix\Providers;

use Illuminate\Support\ServiceProvider;

class PixServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/laravel-pix.php' => config_path('laravel-pix.php'),
        ], 'laravel-pix-config');
    }
}
