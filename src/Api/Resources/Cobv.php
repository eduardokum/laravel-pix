<?php

namespace Eduardokum\LaravelPix\Api\Resources;

use RuntimeException;
use Eduardokum\LaravelPix\Api\Api;
use Illuminate\Http\Client\Response;
use Eduardokum\LaravelPix\Support\Endpoints;
use Eduardokum\LaravelPix\Events\Cobv\CobvCreatedEvent;
use Eduardokum\LaravelPix\Events\Cobv\CobvUpdatedEvent;
use Eduardokum\LaravelPix\Api\Contracts\ApplyApiFilters;
use Eduardokum\LaravelPix\Exceptions\ValidationException;
use Eduardokum\LaravelPix\Api\Contracts\FilterApiRequests;
use Eduardokum\LaravelPix\Api\Contracts\ConsumesCobvEndpoints;

class Cobv extends Api implements FilterApiRequests, ConsumesCobvEndpoints
{
    private array $filters = [];

    public function withFilters($filters): Cobv
    {
        if (! is_array($filters) && ! $filters instanceof ApplyApiFilters) {
            throw new RuntimeException("Filters should be an instance of 'FilterApiRequests' or an array.");
        }

        $this->filters = $filters instanceof ApplyApiFilters
            ? $filters->toArray()
            : $filters;

        return $this;
    }

    public function createWithTransactionId(string $transactionId, array $request): Response
    {
        $endpoint = $this->getEndpoint($this->getPsp()->getConfig('base_url') . $this->resolveEndpoint(Endpoints::CREATE_COBV) . $transactionId);

        $response = $this->request()->put($endpoint, $request);
        if ($response->successful()) {
            CobvCreatedEvent::dispatch($response->json());
        }

        return $response;
    }

    public function updateWithTransactionId(string $transactionId, array $request): Response
    {
        $endpoint = $this->getEndpoint($this->getPsp()->getConfig('base_url') . $this->resolveEndpoint(Endpoints::CREATE_COBV) . $transactionId);

        $response = $this->request()->patch($endpoint, $request);
        if ($response->successful()) {
            CobvUpdatedEvent::dispatch($response->json());
        }

        return $response;
    }

    public function getByTransactionId(string $transactionId): Response
    {
        $endpoint = $this->getEndpoint($this->getPsp()->getConfig('base_url') . $this->resolveEndpoint(Endpoints::GET_COBV) . $transactionId);

        return $this->request()->get($endpoint, $this->filters);
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

        $endpoint = $this->getEndpoint($this->getPsp()->getConfig('base_url') . $this->resolveEndpoint(Endpoints::GET_ALL_COBV));

        return $this->request()->get($endpoint, $this->filters);
    }
}
