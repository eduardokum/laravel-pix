<?php

namespace Eduardokum\LaravelPix\Api;

use Illuminate\Support\Arr;
use Eduardokum\LaravelPix\Psp;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Eduardokum\LaravelPix\Api\Contracts\ConsumesPixApi;
use Eduardokum\LaravelPix\Exceptions\TokenAbsentException;

class Api implements ConsumesPixApi
{
    protected array $additionalParams = [];

    protected array $additionalOptions = [];

    protected ?string $oauthToken = null;

    protected Psp $psp;

    public function __construct()
    {
        $this->psp = new Psp();
    }

    public function oauthToken(?string $oauthToken): Api
    {
        if (! $oauthToken) {
            $oauthToken = Arr::get($this->getOauth2Token()->json(), 'access_token');
        }

        $this->oauthToken = $oauthToken;

        return $this;
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
        ], $extraHeaders));

        $client->withOptions([
            'cert'    => $this->getPsp()->getCertificate(),
            'ssl_key' => $this->getPsp()->getCurrentConfig('client_certificate_key'),
            'verify'  => $this->getPsp()->shouldVerifySslCertificate() ? $this->getPsp()->getCurrentConfig('verify_certificate') : false,
        ] + $this->additionalOptions);

        throw_if(! $this->oauthToken, TokenAbsentException::uninformed());

        $client->withToken($this->oauthToken);

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
    }
}
