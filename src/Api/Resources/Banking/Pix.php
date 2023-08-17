<?php

namespace Junges\Pix\Api\Resources\Banking;

use RuntimeException;
use Junges\Pix\Api\BankingApi;
use Junges\Pix\Support\Endpoints;
use Illuminate\Http\Client\Response;
use Junges\Pix\Support\BankingEndpoints;
use Junges\Pix\Events\SendPix\PixSentEvent;
use Junges\Pix\Api\Contracts\ApplyApiFilters;
use Junges\Pix\Exceptions\ValidationException;

class Pix extends BankingApi
{
    public function createWithQr(array $request, string $idempotencyKey): Response
    {
        return $this->createWith(BankingEndpoints::CREATE_PIX_QR, $request, $idempotencyKey);
    }

    public function createWithDict(array $request, string $idempotencyKey): Response
    {
        return $this->createWith(BankingEndpoints::CREATE_PIX_DICT, $request, $idempotencyKey);
    }

    public function createWithManual(array $request, string $idempotencyKey): Response
    {
        return $this->createWith(BankingEndpoints::CREATE_PIX_MANUAL, $request, $idempotencyKey);
    }

    public function getBye2eid(string $e2eid): Response
    {
        $endpoint = $this->getEndpoint($this->baseUrl.$this->resolveBankingEndpoint(BankingEndpoints::GET_PIX_E2E).$e2eid);

        return $this->request()->get($endpoint);
    }

    protected function prepareIdempotency($idempotencyKey)
    {
        return [
            'x-idempotency-key' => $idempotencyKey
        ];
    }

    private function createWith(string $endpoint, array $request, string $idempotencyKey): Response
    {
        $endpoint = $this->getEndpoint($this->baseUrl.$this->resolveBankingEndpoint($endpoint));

        $response = $this->request($this->prepareIdempotency($idempotencyKey))->post($endpoint, $request);

        event(new PixSentEvent($request, $response->json(), $idempotencyKey));

        return $response;
    }
}
