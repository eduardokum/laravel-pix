<?php

namespace Eduardokum\LaravelPix\Api;

use Eduardokum\LaravelPix\Psp;
use Illuminate\Support\Facades\Http;
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
        $this->scope = $scope ?? $this->getPsp()->getCurrentConfig('scope');
        $client = Http::withHeaders([
            'Content-Type'  => 'application/x-www-form-urlencoded',
            'Authorization' => str_contains($this->getPsp()->getCurrentConfig('authentication_behavior.auth'), 'BASIC_HEADER')
                ? 'Basic ' . base64_encode("{$this->getPsp()->getCurrentConfig('client_id')}:{$this->getPsp()->getCurrentConfig('client_secret')}")
                : null,
        ])->withOptions([
            'cert'    => $this->getPsp()->getCertificate(),
            'ssl_key' => $this->getPsp()->getCurrentConfig('client_certificate_key'),
            'verify'  => $this->getPsp()->shouldVerifySslCertificate() ? $this->getPsp()->getCurrentConfig('verify_certificate') : false,
        ]);

        return $client->asForm()->post($this->getOauthEndpoint(), array_filter([
            'client_id' => str_contains($this->getPsp()->getCurrentConfig('authentication_behavior.auth'), 'POST')
                ? $this->getPsp()->getCurrentConfig('client_id')
                : null,
            'client_secret' => str_contains($this->getPsp()->getCurrentConfig('authentication_behavior.auth'), 'POST')
                ? $this->getPsp()->getCurrentConfig('client_secret')
                : null,
            'grant_type' => str_contains($this->getPsp()->getCurrentConfig('authentication_behavior.grant_type'), 'POST')
                ? 'client_credentials'
                : null,
            'scope' => str_contains($this->getPsp()->getCurrentConfig('authentication_behavior.scope'), 'POST')
                ? $this->scope
                : null,
        ]));
    }

    public function getPsp(): Psp
    {
        return $this->psp;
    }

    public function getOauthEndpoint(): string
    {
        $url_parts = parse_url($this->getPsp()->getCurrentConfig('oauth_token_url'));
        $query = [];
        if (isset($url_parts['query'])) {
            parse_str($url_parts['query'], $query);
        }
        if (str_contains($this->getPsp()->getCurrentConfig('authentication_behavior.auth'), 'GET')) {
            $query['client_id'] = $this->getPsp()->getCurrentConfig('client_id');
            $query['client_secret'] = $this->getPsp()->getCurrentConfig('client_secret');
        }
        if (str_contains($this->getPsp()->getCurrentConfig('authentication_behavior.grant_type'), 'GET')) {
            $query['grant_type'] = 'client_credentials';
        }
        if (str_contains($this->getPsp()->getCurrentConfig('authentication_behavior.scope'), 'GET') && $this->scope) {
            $query['scope'] = $this->scope;
        }
        $url_parts['query'] = http_build_query($query);

        return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . '?' . $url_parts['query'];
    }
}
