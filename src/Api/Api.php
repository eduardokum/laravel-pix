<?php

namespace Eduardokum\LaravelPix\Api;

use Throwable;
use Illuminate\Support\Arr;
use Eduardokum\LaravelPix\Psp;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Eduardokum\LaravelPix\Api\Contracts\ConsumesPixApi;
use Eduardokum\LaravelPix\Exceptions\TokenAbsentException;

class Api implements ConsumesPixApi
{
    protected array $additionalParams = [];

    protected array $additionalOptions = [];

    protected array $additionalHeaders = [];

    protected ?string $oauthToken = null;

    protected Psp $psp;

    public function __construct()
    {
        $this->psp = new Psp();
    }

    public function setToken(?string $oauthToken = null): Api
    {
        if (! $oauthToken) {
            $oauthToken = Arr::get($this->getOauth2Token()->json(), 'access_token');
        }

        $this->oauthToken = $oauthToken;

        return $this;
    }

    public function getToken(): string
    {
        return $this->oauthToken;
    }

    public function usingOnTheFlyPsp(array $pspConfigs): Api
    {
        $this->psp->onTheFlyPsp($pspConfigs);
        $this->additionals();

        return $this;
    }

    public function usingPsp(string $psp): Api
    {
        $this->psp->currentPsp($psp);
        $this->additionals();

        return $this;
    }

    public function usingDefaultPsp(): Api
    {
        $this->psp->currentPsp(Psp::getDefaultPsp());

        return $this;
    }

    public function getPsp(): Psp
    {
        return $this->psp;
    }

    protected function request(array $extraHeaders = []): PendingRequest
    {
        $client = Http::withHeaders(array_merge([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'Cache-Control' => 'no-cache',
        ], $extraHeaders, $this->additionalHeaders));

        $client->withOptions(array_merge([
            'cert'    => $this->getPsp()->getCertificate(),
            'ssl_key' => $this->getPsp()->getCurrentConfig('client_certificate_key'),
            'verify'  => $this->getPsp()->shouldVerifySslCertificate() ? $this->getPsp()->getCurrentConfig('verify_certificate') : false,
        ], $this->additionalOptions));

        throw_if(! $this->oauthToken, TokenAbsentException::uninformed());

        $client->withToken($this->oauthToken);
        $client->retry(3, 200, function (Throwable $exception, PendingRequest $request) {
            if (! $exception instanceof RequestException || $exception->response->status() !== 401) {
                return false;
            }

            return true;
        }, throw: false);

        return $client;
    }

    public function getOauth2Token(string $scope = null)
    {
        return (new Auth($this->getPsp()))->getToken($scope);
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

    protected function resolveEndpoint(string $endpoint): string
    {
        return $this->getPsp()->getEndpointsResolver()->getEndpoint($endpoint);
    }

    protected function getEndpoint(string $endpoint): string
    {
        return $endpoint . '?' . http_build_query($this->additionalParams);
    }

    private function additionals()
    {
        if (is_array($this->psp->getCurrentConfig('additional_params')) && count($this->psp->getCurrentConfig('additional_params')) > 0) {
            $this->additionalParams = $this->psp->getCurrentConfig('additional_params');
        }
        if (is_array($this->psp->getCurrentConfig('additional_options')) && count($this->psp->getCurrentConfig('additional_options')) > 0) {
            $this->additionalOptions = $this->psp->getCurrentConfig('additional_options');
        }
        if (is_array($this->psp->getCurrentConfig('additional_headers')) && count($this->psp->getCurrentConfig('additional_headers')) > 0) {
            $this->additionalHeaders = $this->psp->getCurrentConfig('additional_headers');
        }
    }
}
