<?php

namespace Eduardokum\LaravelPix\Api\Resources;

use RuntimeException;
use Eduardokum\LaravelPix\Api\Api;
use Illuminate\Http\Client\Response;
use Eduardokum\LaravelPix\Support\Endpoints;
use Eduardokum\LaravelPix\Api\Contracts\ApplyApiFilters;
use Eduardokum\LaravelPix\Api\Contracts\FilterApiRequests;
use Eduardokum\LaravelPix\Events\Webhooks\WebhookCreatedEvent;
use Eduardokum\LaravelPix\Events\Webhooks\WebhookDeletedEvent;
use Eduardokum\LaravelPix\Api\Contracts\ConsumesWebhookEndpoints;

class Webhook extends Api implements ConsumesWebhookEndpoints, FilterApiRequests
{
    private array $filters = [];

    public function withFilters($filters): Webhook
    {
        if (! is_array($filters) && ! $filters instanceof ApplyApiFilters) {
            throw new RuntimeException("Filters should be an instance of 'FilterApiRequests' or an array.");
        }

        $this->filters = $filters instanceof ApplyApiFilters
            ? $filters->toArray()
            : $filters;

        return $this;
    }

    public function create(string $pixKey, string $callbackUrl): Response
    {
        $endpoint = $this->getEndpoint($this->getPsp()->getConfig('base_url') . $this->resolveEndpoint(Endpoints::CREATE_WEBHOOK) . $pixKey);

        $webhook = $this->request()->put($endpoint, ['webhookUrl' => $callbackUrl]);

        if ($webhook->successful()) {
            WebhookCreatedEvent::dispatch($webhook->json());
        }

        return $webhook;
    }

    public function getByPixKey(string $pixKey): Response
    {
        $endpoint = $this->getEndpoint($this->getPsp()->getConfig('base_url') . $this->resolveEndpoint(Endpoints::GET_WEBHOOK) . $pixKey);

        return $this->request()->get($endpoint);
    }

    public function delete(string $pixKey): Response
    {
        $endpoint = $this->getEndpoint($this->getPsp()->getConfig('base_url') . $this->resolveEndpoint(Endpoints::DELETE_WEBHOOK) . $pixKey);

        $webhook = $this->request()->delete($endpoint);

        WebhookDeletedEvent::dispatch($pixKey);

        return $webhook;
    }

    public function all(): Response
    {
        $endpoint = $this->getEndpoint($this->getPsp()->getConfig('base_url') . $this->resolveEndpoint(Endpoints::GET_WEBHOOKS));

        return $this->request()->get($endpoint, $this->filters);
    }
}
