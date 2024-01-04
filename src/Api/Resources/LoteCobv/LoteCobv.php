<?php

namespace Eduardokum\LaravelPix\Api\Resources\LoteCobv;

use Illuminate\Http\Client\Response;
use Eduardokum\LaravelPix\Api\Api;
use Eduardokum\LaravelPix\Api\Contracts\ApplyApiFilters;
use Eduardokum\LaravelPix\Api\Contracts\ConsumesLoteCobvEndpoints;
use Eduardokum\LaravelPix\Api\Contracts\FilterApiRequests;
use Eduardokum\LaravelPix\Exceptions\ValidationException;
use Eduardokum\LaravelPix\Support\Endpoints;
use RuntimeException;

class LoteCobv extends Api implements ConsumesLoteCobvEndpoints, FilterApiRequests
{
    private array $filters = [];

    public function withFilters($filters): LoteCobv
    {
        if (!is_array($filters) && !$filters instanceof ApplyApiFilters) {
            throw new RuntimeException("Filters should be an instance of 'FilterApiRequests' or an array.");
        }

        $this->filters = $filters instanceof ApplyApiFilters
            ? $filters->toArray()
            : $filters;

        return $this;
    }

    public function createBatch(string $batchId, array $request): Response
    {
        $endpoint = $this->getEndpoint($this->baseUrl.$this->resolveEndpoint(Endpoints::CREATE_LOTE_COBV).$batchId);

        return $this->request()->put($endpoint, $request);
    }

    public function updateBatch(string $batchId, array $request): Response
    {
        $endpoint = $this->getEndpoint($this->baseUrl.$this->resolveEndpoint(Endpoints::UPDATE_LOTE_COBV).$batchId);

        return $this->request()->patch($endpoint, $request);
    }

    public function getByBatchId(string $batchId): Response
    {
        $endpoint = $this->getEndpoint($this->baseUrl.$this->resolveEndpoint(Endpoints::GET_LOTE_COBV).$batchId);

        return $this->request()->get($endpoint);
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

        $endpoint = $this->getEndpoint($this->baseUrl.$this->resolveEndpoint(Endpoints::GET_ALL_LOTE_COBV));

        return $this->request()
            ->get($endpoint, $this->filters);
    }
}
