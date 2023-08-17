<?php

namespace Junges\Pix\Api;

use Illuminate\Support\Facades\Http;
use Junges\Pix\Api\Contracts\AuthenticatesBankingPSPs;
use Junges\Pix\Providers\PixServiceProvider;

class BankingAuth implements AuthenticatesBankingPSPs
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $certificate;
    protected string $currentBankingPspOauthEndpoint;
    protected ?string $certificatePassword;

    public function __construct(
        string $clientId,
        string $clientSecret,
        string $certificate,
        string $currentBankingPspOauthEndpoint,
        ?string $certificatePassword
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->certificate = $certificate;
        $this->currentBankingPspOauthEndpoint = $currentBankingPspOauthEndpoint;
        $this->certificatePassword = $certificatePassword;
    }

    public function getBankingToken(string $scopes = null)
    {
        $client = Http::withHeaders([
            'Content-Type'  => 'application/json'
        ])->withOptions([
            'auth' => [$this->clientId, $this->clientSecret],
        ]);

        if ($this->shouldVerifySslCertificate()) {
            $client->withOptions([
                'verify' => $this->certificate,
                'cert'   => $this->getCertificate(),
            ]);
        }

        return $client->post($this->getBankingOauthEndpoint(), [
            'clientId'     => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'grantType'    => 'client_credentials',
        ]);
    }

    protected function getCertificate()
    {
        return $this->certificatePassword ?? false
                ? [$this->certificate, $this->certificatePassword]
                : $this->certificate;
    }

    private function shouldVerifySslCertificate(): bool
    {
        return PixServiceProvider::$verifySslCertificate;
    }

    public function getBankingOauthEndpoint(): string
    {
        return $this->currentBankingPspOauthEndpoint;
    }
}
