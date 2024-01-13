<?php

namespace Eduardokum\LaravelPix\Api;

use Throwable;
use Eduardokum\LaravelPix\Psp;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Eduardokum\LaravelPix\Exceptions\TokenAbsentException;

abstract class Api
{
    protected array $additionalParams = [];

    protected array $additionalOptions = [];

    protected array $additionalHeaders = [];

    protected Psp $psp;

    public function __construct(Psp $psp)
    {
        $this->setPsp($psp);
    }

    public function setPsp($psp): Api
    {
        $this->psp = $psp;

        return $this;
    }

    public function withAdditionalParams(array $params): Api
    {
        $this->additionalParams = $params;

        return $this;
    }

    public function withOptions(array $options): Api
    {
        $this->additionalOptions = $options;

        return $this;
    }

    protected function request(array $extraHeaders = []): PendingRequest
    {
        $client = Http::withHeaders(array_merge([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'Cache-Control' => 'no-cache',
        ], $extraHeaders, $this->getPsp()->getConfig('additional_headers'), $this->additionalHeaders));

        $client->withOptions(array_merge([
            'cert'    => $this->getPsp()->getCertificate(),
            'ssl_key' => $this->getPsp()->getConfig('client_certificate_key'),
            'verify'  => $this->getPsp()->shouldVerifySslCertificate() ? $this->getPsp()->getConfig('verify_certificate') : false,
        ], $this->getPsp()->getConfig('additional_options'), $this->additionalOptions));

        throw_if(! $this->getPsp()->getToken(), TokenAbsentException::uninformed());

        $client->withToken($this->getPsp()->getToken());
        $client->retry(3, 200, function (Throwable $exception) {
            return $exception instanceof RequestException || $exception->response->status() !== 401;
        }, throw: false);

        return $client;
    }

    protected function resolveEndpoint(string $endpoint): string
    {
        return $this->getPsp()->getEndpointsResolver()->getEndpoint($endpoint);
    }

    protected function getEndpoint(string $endpoint): string
    {
        $params = $this->getPsp()->getConfig('additional_params') + $this->additionalParams;

        return $endpoint . ($params ? '?' . http_build_query($params) : null);
    }

    protected function getPsp(): Psp
    {
        return $this->psp;
    }
}
