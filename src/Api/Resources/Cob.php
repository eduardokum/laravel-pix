<?php

namespace Eduardokum\LaravelPix\Api\Resources;

use RuntimeException;
use Eduardokum\LaravelPix\Api\Api;
use Illuminate\Http\Client\Response;
use Eduardokum\LaravelPix\Support\Endpoints;
use Eduardokum\LaravelPix\Events\Cob\CobCreatedEvent;
use Eduardokum\LaravelPix\Events\Cob\CobUpdatedEvent;
use Eduardokum\LaravelPix\Api\Contracts\ApplyApiFilters;
use Eduardokum\LaravelPix\Exceptions\ValidationException;
use Eduardokum\LaravelPix\Api\Contracts\FilterApiRequests;
use Eduardokum\LaravelPix\Api\Contracts\ConsumesCobEndpoints;

class Cob extends Api implements ConsumesCobEndpoints, FilterApiRequests
{
    private array $filters = [];

    public function withFilters($filters): Cob
    {
        if (! is_array($filters) && ! $filters instanceof ApplyApiFilters) {
            throw new RuntimeException("Filters should be an instance of 'FilterApiRequests' or an array.");
        }

        $this->filters = $filters instanceof ApplyApiFilters
                ? $filters->toArray()
                : $filters;

        return $this;
    }

    public function create(string $transactionId, array $request): Response
    {
        $endpoint = $this->getEndpoint($this->getPsp()->getConfig('base_url') . $this->resolveEndpoint(Endpoints::CREATE_COB) . $transactionId);

        $response = $this->request()->put($endpoint, $request);
        if ($response->successful()) {
            CobCreatedEvent::dispatch($response->json());
        }

        return $response;
    }

    public function createWithoutTransactionId(array $request): Response
    {
        $endpoint = $this->getEndpoint($this->getPsp()->getConfig('base_url') . $this->resolveEndpoint(Endpoints::CREATE_COB));

        $response = $this->request()->post($endpoint, $request);
        if ($response->successful()) {
            CobCreatedEvent::dispatch($response->json());
        }

        return $response;
    }

    public function getByTransactionId(string $transactionId): Response
    {
        $endpoint = $this->getEndpoint($this->getPsp()->getConfig('base_url') . $this->resolveEndpoint(Endpoints::GET_COB) . $transactionId);

        return $this->request()->get($endpoint);
    }

    public function updateByTransactionId(string $transactionId, array $request): Response
    {
        $endpoint = $this->getEndpoint($this->getPsp()->getConfig('base_url') . $this->resolveEndpoint(Endpoints::UPDATE_COB) . $transactionId);

        $response = $this->request()->patch($endpoint, $request);
        if ($response->successful()) {
            CobUpdatedEvent::dispatch($response->json());
        }

        return $response;
    }

    /**
     * @throws \Throwable
     */
    public function all(): Response
    {
        throw_if(
            empty($this->filters),
            ValidationException::filtersAreRequired()
        );

        $endpoint = $this->getEndpoint($this->getPsp()->getConfig('base_url') . $this->resolveEndpoint(Endpoints::GET_ALL_COBS));

        return $this->request()->get($endpoint, $this->filters);
    }
}
