<?php

namespace Junges\Pix\Api\Resources\Banking;

use Illuminate\Http\Client\Response;
use Junges\Pix\Api\Api;
use Junges\Pix\Api\Contracts\ApplyApiFilters;
use Junges\Pix\Api\Contracts\ConsumesWebhookEndpoints;
use Junges\Pix\Api\Contracts\FilterApiRequests;
use Junges\Pix\Events\Webhooks\WebhookCreatedEvent;
use Junges\Pix\Events\Webhooks\WebhookDeletedEvent;
use Junges\Pix\Support\Endpoints;
use RuntimeException;

class Webhook extends Api implements ConsumesWebhookEndpoints
{
    public function createTransferWebhook(string $callbackUrl): Response
    {
        return $this->createWith(BankingEndpoints::CREATE_WEBHOOK_TRANSFER, $callbackUrl);
    }
    
    public function createReceiveWebhook(string $callbackUrl): Response
    {
        return $this->createWith(BankingEndpoints::CREATE_WEBHOOK_RECEIVE, $callbackUrl);
    }
    
    public function createRefundWebhook(string $callbackUrl): Response
    {
        return $this->createWith(BankingEndpoints::CREATE_WEBHOOK_REFUND, $callbackUrl);
    }
    
    public function createCashoutWebhook(string $callbackUrl): Response
    {
        return $this->createWith(BankingEndpoints::CREATE_WEBHOOK_CASHOUT, $callbackUrl);
    }
    
    public function createRejectWebhook(string $callbackUrl): Response
    {
        return $this->createWith(BankingEndpoints::CREATE_WEBHOOK_REJECT, $callbackUrl);
    }

    private function createWith(string $endpoint, string $callbackUrl): Response
    {
        $endpoint = $this->getEndpoint($this->baseUrl.$this->resolveBankingEndpoint($endpoint));
        
        $webhook = $this->request()->post($endpoint, ['uri' => $callbackUrl]);
        
        event(new WebhookCreatedEvent($webhook->json()));

        return $webhook;
    }

    public function all(): Response
    {
        $endpoint = $this->getEndpoint($this->baseUrl.$this->resolveBankingEndpoint(BankingEndpoints::GET_WEBHOOK));

        return $this->request()->get($endpoint);
    }
}
