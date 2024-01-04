<?php

namespace Eduardokum\LaravelPix\Api\Resources\Cobv;

use Illuminate\Http\Client\Response;
use Eduardokum\LaravelPix\Api\Api;
use Eduardokum\LaravelPix\Api\Contracts\ApplyApiFilters;
use Eduardokum\LaravelPix\Api\Contracts\ConsumesCobvEndpoints;
use Eduardokum\LaravelPix\Api\Contracts\FilterApiRequests;
use Eduardokum\LaravelPix\Exceptions\ValidationException;
use Eduardokum\LaravelPix\Support\Endpoints;
use RuntimeException;

class Cobv extends Api implements FilterApiRequests, ConsumesCobvEndpoints
{
    private array $filters = [];

    public function withFilters($filters): Cobv
    {
        if (!is_array($filters) && !$filters instanceof ApplyApiFilters) {
            throw new RuntimeException("Filters should be an instance of 'FilterApiRequests' or an array.");
        }

        $this->filters = $filters instanceof ApplyApiFilters
            ? $filters->toArray()
            : $filters;

        return $this;
    }

    public function createWithTransactionId(string $transactionId, array $request): Response
    {
        $endpoint = $this->getEndpoint($this->baseUrl.$this->resolveEndpoint(Endpoints::CREATE_COBV).$transactionId);

        return $this->request()->put($endpoint, $request);
    }

    public function updateWithTransactionId(string $transactionId, array $request): Response
    {
        $endpoint = $this->getEndpoint($this->baseUrl.$this->resolveEndpoint(Endpoints::CREATE_COBV).$transactionId);

        return $this->request()->patch($endpoint, $request);
    }

    public function getByTransactionId(string $transactionId): Response
    {
        $endpoint = $this->getEndpoint($this->baseUrl.$this->resolveEndpoint(Endpoints::GET_COBV).$transactionId);

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

        $endpoint = $this->getEndpoint($this->baseUrl.$this->resolveEndpoint(Endpoints::GET_ALL_COBV));

        return $this->request()->get($endpoint, $this->filters);
    }
}
