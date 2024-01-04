<?php

namespace Eduardokum\LaravelPix\Api;

use Illuminate\Support\Facades\Http;
use Eduardokum\LaravelPix\Api\Contracts\AuthenticatesPSPs;
use Eduardokum\LaravelPix\Providers\PixServiceProvider;

class Auth implements AuthenticatesPSPs
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $certificate;
    protected string $certificateKey;
    protected string $currentPspOauthEndpoint;
    protected ?string $certificatePassword;

    public function __construct(
        string $clientId,
        string $clientSecret,
        string $certificate,
        string $certificateKey,
        string $currentPspOauthEndpoint,
        ?string $certificatePassword
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->certificate = $certificate;
        $this->currentPspOauthEndpoint = $currentPspOauthEndpoint;
        $this->certificatePassword = $certificatePassword;
        $this->certificateKey = $certificateKey;
    }

    public function getToken(string $scopes = null)
    {
        $client = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Basic '.base64_encode("{$this->clientId}:{$this->clientSecret}"),
        ])->withOptions([
            'auth' => [$this->clientId, $this->clientSecret],
        ]);

        if ($this->shouldVerifySslCertificate()) {
            $client->withOptions(array_filter([
                'verify' => $this->certificate,
                'cert'   => $this->getCertificate(),
                'ssl_key'   => $this->certificateKey,
            ]));
        }

        return $client->post($this->getOauthEndpoint(), [
            'grant_type' => 'client_credentials',
            'scope'      => $scopes ?? '',
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

    public function getOauthEndpoint(): string
    {
        return $this->currentPspOauthEndpoint;
    }
}
