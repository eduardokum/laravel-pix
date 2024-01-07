<?php

namespace Eduardokum\LaravelPix;

use Illuminate\Support\Arr;
use Eduardokum\LaravelPix\Contracts\CanResolveEndpoints;
use Eduardokum\LaravelPix\Exceptions\Psp\InvalidPspException;

class Psp
{
    public static string $defaultPsp = 'default';

    public ?string $currentPsp = null;

    private ?array $onTheFlyPsp = null;

    private array $cachedConfigs = [];

    public static function getConfig(): Psp
    {
        return new static();
    }

    public static function defaultPsp($psp)
    {
        self::$defaultPsp = $psp;
    }

    public function onTheFlyPsp(array $configs = null): Psp
    {
        $this->currentPsp = 'on-the-fly';
        unset($this->cachedConfigs[$this->currentPsp]);
        $this->onTheFlyPsp = $configs;

        return $this;
    }

    public function currentPsp(string $psp = null): Psp
    {
        $psp = $psp ?? self::$defaultPsp;

        $this->currentPsp = $psp;

        return $this;
    }

    public function getCurrentPsp(): ?string
    {
        return $this->currentPsp ?? self::$defaultPsp;
    }

    public static function getDefaultPsp(): string
    {
        return self::$defaultPsp;
    }

    public static function availablePsps(): array
    {
        return array_keys(config('laravel-pix.psp'));
    }

    public function getCurrentConfig(string $config = null)
    {
        return Arr::get($this->getPspConfig($this->getCurrentPsp()), $config);
    }

    public function getPspConfig(string $psp)
    {
        if (isset($this->cachedConfigs[$psp])) {
            return $this->cachedConfigs[$psp];
        }
        if ($psp == 'on-the-fly') {
            $default = config('laravel-pix.psp.default');
            $onTheFly = Arr::only($this->onTheFlyPsp, array_keys(config('laravel-pix.psp.default')));
            $onTheFly['authentication_behavior'] =
                Arr::only(
                    Arr::get($this->onTheFlyPsp, 'authentication_behavior') + config('laravel-pix.psp.default.authentication_behavior'),
                    array_keys(config('laravel-pix.psp.default.authentication_behavior'))
                );

            return $this->cachedConfigs[$psp] = $onTheFly + $default;
        }

        throw_if(! $this->validatePsp($psp), InvalidPspException::pspNotFound($psp));

        return $this->cachedConfigs[$psp] = config("laravel-pix.psp.{$psp}");
    }

    public function getEndpointsResolver(): CanResolveEndpoints
    {
        return app($this->getCurrentConfig('resolve_endpoints_using'));
    }

    public function getCertificate()
    {
        if (! $this->getCurrentConfig('client_certificate')) {
            return null;
        }

        return $this->getCurrentConfig('client_certificate_password') ?? false
            ? [$this->getCurrentConfig('client_certificate'), $this->getCurrentConfig('client_certificate_password')]
            : $this->getCurrentConfig('client_certificate');
    }

    public function shouldVerifySslCertificate(): bool
    {
        return file_exists($this->getCurrentConfig('verify_certificate'));
    }

    private function validatePsp(string $psp): bool
    {
        return in_array($psp, $this->availablePsps());
    }
}
