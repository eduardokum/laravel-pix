<?php

namespace Junges\Pix\Api;

use Junges\Pix\Psp;
use Junges\Pix\Api\Api;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Junges\Pix\Api\Contracts\ConsumesPixApi;
use Junges\Pix\Providers\PixServiceProvider;
use Junges\Pix\Contracts\CanResolveEndpoints;

class BankingApi extends Api implements ConsumesPixApi
{
    public function __construct()
    {
        $this->psp = new Psp();

        $this->oauthToken($this->psp->getPspOauthBearerToken())
            ->certificate($this->psp->getBankingPspSSLCertificate())
            ->baseUrl($this->psp->getPspBankingBaseUrl())
            ->clientId($this->psp->getPspBankingClientId())
            ->clientSecret($this->psp->getPspBankingClientSecret());
    }

    public function usingPsp(string $psp): Api
    {
        $this->psp->currentPsp($psp);

        $this->oauthToken($this->psp->getPspOauthBearerToken())
            ->certificate($this->psp->getBankingPspSSLCertificate())
            ->baseUrl($this->psp->getPspBankingBaseUrl())
            ->clientId($this->psp->getPspBankingClientId())
            ->clientSecret($this->psp->getPspBankingClientSecret());

        return $this;
    }

    public function getBankingOauth2Token(string $scopes = null)
    {
        $authentication_class = $this->getPsp()->getBankingAuthenticationClass();

        return app($authentication_class, [
            'clientId'                       => $this->clientId,
            'clientSecret'                   => $this->clientSecret,
            'certificate'                    => $this->certificate,
            'certificatePassword'            => $this->certificatePassword,
            'currentBankingPspOauthEndpoint' => $this->psp->getBankingOauthTokenUrl(),
        ])->getBankingToken();
    }
}
