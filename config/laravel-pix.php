<?php

return [

    'transaction_currency_code' => 986,

    'country_code' => 'BR',

    /*
     | O PIX precisa definir seu GUI (Global Unique Identifier) para ser utilizado.
     */
    'gui' => 'br.gov.bcb.pix',

    'country_phone_prefix' => '+55',

    /*
     * Tamanho do QR code quer será gerado pelo gerador implementado no pacote, em pixels.
     */
    'qr_code_size' => 200,

    /*
     * Informações do Prestador de serviço de pagamento (PSP) que você está utilizando.
     * Você pode utilizar vários psps com este pacote, bastando adicionar um novo array com configurações.
     * base_url: URL base da API do seu PSP.
     * oauth_bearer_token: Você pode definir o seu Token
     */
    'psp' => [
        'default' => [
            'base_url'                    => env('LARAVEL_PIX_PSP_BASE_URL'),
            'oauth_token_url'             => env('LARAVEL_PIX_PSP_OAUTH_URL', false),
            'oauth_bearer_token'          => env('LARAVEL_PIX_OAUTH2_BEARER_TOKEN'),
            'verify_certificate'          => env('LARAVEL_PIX_PSP_VERIFY_CERTIFICATE'),
            'client_certificate'          => env('LARAVEL_PIX_PSP_CLIENT_CERTIFICATE'),
            'client_certificate_key'      => env('LARAVEL_PIX_PSP_CLIENT_CERTIFICATE_KEY'),
            'client_certificate_password' => env('LARAVEL_PIX_PSP_CLIENT_CERTIFICATE_PASSWORD'),
            'client_secret'               => env('LARAVEL_PIX_PSP_CLIENT_SECRET'),
            'client_id'                   => env('LARAVEL_PIX_PSP_CLIENT_ID'),
            'scope'                       => env('LARAVEL_PIX_PSP_SCOPE'),
            'authentication_behavior'     => [
                'auth'       => 'POST|BASIC_HEADER', // POST|GET|BASIC_HEADER
                'grant_type' => 'POST', // POST|GET
                'scope'      => 'POST', // POST|GET
            ],
            'resolve_endpoints_using' => Eduardokum\LaravelPix\Support\Endpoints::class,
            'additional_params'       => [],
            'additional_options'      => [],
        ],
    ],
];
