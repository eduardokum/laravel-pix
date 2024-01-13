<?php

namespace Eduardokum\LaravelPix;

use Illuminate\Support\Arr;
use Eduardokum\LaravelPix\Api\Auth;
use Illuminate\Support\Facades\Cache;
use Eduardokum\LaravelPix\Exceptions\RequestException;
use Eduardokum\LaravelPix\Contracts\CanResolveEndpoints;
use Eduardokum\LaravelPix\Exceptions\Psp\InvalidPspException;

class Psp
{
    private ?array $accessToken = null;

    public ?string $currentPsp = null;

    private ?array $onTheFly = null;

    private array $cachedConfigs = [];

    private $withoutCache = false;

    public function __construct(array $onTheFly = null)
    {
        $this->onTheFly($onTheFly);
    }

    public function onTheFly(array $onTheFly = null): Psp
    {
        if (! $onTheFly) {
            return $this;
        }
        $this->currentPsp = 'on-the-fly';
        $default = config('laravel-pix.psp.default');
        $onTheFly = Arr::only($onTheFly, array_keys(config('laravel-pix.psp.default')));
        $onTheFly['authentication_behavior'] =
            Arr::only(
                Arr::get($onTheFly, 'authentication_behavior', []) + config('laravel-pix.psp.default.authentication_behavior', []),
                array_keys(config('laravel-pix.psp.default.authentication_behavior', []))
            );
        $this->onTheFly = $this->cachedConfigs['on-the-fly'] = $onTheFly + $default;

        return $this;
    }

    public function currentPsp(string $psp = null): Psp
    {
        $psp = $psp ?? 'default';

        $this->currentPsp = $psp;

        return $this;
    }

    public function getConfig(string $config = null, $default = null)
    {
        return Arr::get($this->getPspConfig($this->currentPsp), $config, $default);
    }

    public function getEndpointsResolver(): CanResolveEndpoints
    {
        return app($this->getConfig('resolve_endpoints_using'));
    }

    public function withoutCache(): Psp
    {
        $this->withoutCache = false;

        return $this;
    }

    public function getCertificate()
    {
        if (! $this->getConfig('client_certificate')) {
            return null;
        }

        return $this->getConfig('client_certificate_password') ?? false
            ? [$this->getConfig('client_certificate'), $this->getConfig('client_certificate_password')]
            : $this->getConfig('client_certificate');
    }

    public function shouldVerifySslCertificate(): bool
    {
        return file_exists($this->getConfig('verify_certificate'));
    }

    public function getToken(): ?string
    {
        return Arr::get($this->accessToken, 'access_token');
    }

    public function getOauth2Token(string $scope = null): ?string
    {
        $keyCache = config('laravel-pix.cache') ? 'laravel-pix-' . md5($this->getConfig('client_id') . $this->getConfig('client_secret')) : null;

        if ($keyCache && ! $this->withoutCache && ($cache = Cache::get($keyCache))) {
            $this->accessToken = decrypt($cache);

            if (! Arr::get($this->accessToken ?: [], 'access_token')) {
                Cache::forget($keyCache);
            } else {
                return $this->accessToken['access_token'];
            }
        }

        $response = (new Auth($this))->getToken($scope);
        if (! $response->successful()) {
            RequestException::tokenNotGranted($response->body());
        }

        $this->accessToken = [
            'access_token' => $response->json('access_token'),
            'expires_in'   => $response->json('expires_in', 3600),
        ];

        if ($keyCache) {
            Cache::put($keyCache, encrypt($this->accessToken), carbon()->addSeconds($this->accessToken['expires_in']));
        }
        $this->withoutCache = false;

        return $this->accessToken['access_token'];
    }

    private function getPspConfig(string $psp)
    {
        if (isset($this->cachedConfigs[$psp])) {
            return $this->cachedConfigs[$psp];
        }
        if ($psp == 'on-the-fly') {
            $default = config('laravel-pix.psp.default');
            $onTheFly = Arr::only($this->onTheFly, array_keys(config('laravel-pix.psp.default')));
            $onTheFly['authentication_behavior'] =
                Arr::only(
                    Arr::get($this->onTheFly, 'authentication_behavior') + config('laravel-pix.psp.default.authentication_behavior'),
                    array_keys(config('laravel-pix.psp.default.authentication_behavior'))
                );

            return $this->cachedConfigs[$psp] = $onTheFly + $default;
        }

        throw_if(! in_array($psp, array_keys(config('laravel-pix.psp'))), InvalidPspException::pspNotFound($psp));

        return $this->cachedConfigs[$psp] = config("laravel-pix.psp.{$psp}");
    }
}
