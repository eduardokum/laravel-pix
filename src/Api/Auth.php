<?php

namespace Eduardokum\LaravelPix\Api;

use Eduardokum\LaravelPix\Psp;
use Illuminate\Support\Facades\Http;
use Eduardokum\LaravelPix\Exceptions\RequestException;
use Eduardokum\LaravelPix\Api\Contracts\AuthenticatesPSPs;

class Auth implements AuthenticatesPSPs
{
    protected Psp $psp;

    protected ?string $scope;

    public function __construct(Psp $psp)
    {
        $this->psp = $psp;
    }

    public function getToken(string $scope = null)
    {
        $this->scope = $scope ?? $this->getPsp()->getConfig('scope');

        throw_if(! $this->getPsp()->getConfig('client_id') || ! $this->getPsp()->getConfig('client_secret'), RequestException::credentialsNotConfigured());

        $client = Http::withHeaders([
            'Content-Type'  => 'application/x-www-form-urlencoded',
            'Authorization' => str_contains($this->getPsp()->getConfig('authentication_behavior.auth'), 'BASIC_HEADER')
                ? 'Basic ' . base64_encode("{$this->getPsp()->getConfig('client_id')}:{$this->getPsp()->getConfig('client_secret')}")
                : null,
        ])->withOptions([
            'cert'    => $this->getPsp()->getCertificate(),
            'ssl_key' => $this->getPsp()->getConfig('client_certificate_key'),
            'verify'  => $this->getPsp()->shouldVerifySslCertificate() ? $this->getPsp()->getConfig('verify_certificate') : false,
        ]);

        return $client->asForm()->post($this->getOauthEndpoint(), array_filter([
            'client_id' => str_contains($this->getPsp()->getConfig('authentication_behavior.auth'), 'POST')
                ? $this->getPsp()->getConfig('client_id')
                : null,
            'client_secret' => str_contains($this->getPsp()->getConfig('authentication_behavior.auth'), 'POST')
                ? $this->getPsp()->getConfig('client_secret')
                : null,
            'grant_type' => str_contains($this->getPsp()->getConfig('authentication_behavior.grant_type'), 'POST')
                ? 'client_credentials'
                : null,
            'scope' => str_contains($this->getPsp()->getConfig('authentication_behavior.scope'), 'POST')
                ? $this->scope
                : null,
        ]));
    }

    public function getOauthEndpoint(): string
    {
        throw_if(! $this->getPsp()->getConfig('oauth_token_url'), RequestException::oauthUrlNotConfigured());

        $url_parts = parse_url($this->getPsp()->getConfig('oauth_token_url'));
        $query = [];
        if (isset($url_parts['query'])) {
            parse_str($url_parts['query'], $query);
        }
        if (str_contains($this->getPsp()->getConfig('authentication_behavior.auth'), 'GET')) {
            $query['client_id'] = $this->getPsp()->getConfig('client_id');
            $query['client_secret'] = $this->getPsp()->getConfig('client_secret');
        }
        if (str_contains($this->getPsp()->getConfig('authentication_behavior.grant_type'), 'GET')) {
            $query['grant_type'] = 'client_credentials';
        }
        if (str_contains($this->getPsp()->getConfig('authentication_behavior.scope'), 'GET') && $this->scope) {
            $query['scope'] = $this->scope;
        }
        $url_parts['query'] = http_build_query($query);

        return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . '?' . $url_parts['query'];
    }

    private function getPsp(): Psp
    {
        return $this->psp;
    }
}
