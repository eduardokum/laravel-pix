<?php

namespace Eduardokum\LaravelPix\Api\Resources;

use RuntimeException;
use Eduardokum\LaravelPix\Api\Api;
use Illuminate\Http\Client\Response;
use Eduardokum\LaravelPix\Support\Endpoints;
use Eduardokum\LaravelPix\Api\Contracts\ApplyApiFilters;
use Eduardokum\LaravelPix\Exceptions\ValidationException;
use Eduardokum\LaravelPix\Api\Contracts\FilterApiRequests;
use Eduardokum\LaravelPix\Api\Contracts\ConsumesPayloadLocationEndpoints;
use Eduardokum\LaravelPix\Events\PayloadLocation\PayloadLocationDetachEvent;
use Eduardokum\LaravelPix\Events\PayloadLocation\PayloadLocationCreatedEvent;

class PayloadLocation extends Api implements ConsumesPayloadLocationEndpoints, FilterApiRequests
{
    private array $filters = [];

    public function withFilters($filters): PayloadLocation
    {
        if (! is_array($filters) && ! $filters instanceof ApplyApiFilters) {
            throw new RuntimeException("Filters should be an instance of 'FilterApiRequests' or an array.");
        }

        $this->filters = $filters instanceof ApplyApiFilters
            ? $filters->toArray()
            : $filters;

        return $this;
    }

    public function create(string $loc): Response
    {
        $endpoint = $this->getEndpoint(
            $this->getPsp()->getConfig('base_url')
            . $this->resolveEndpoint(Endpoints::CREATE_PAYLOAD_LOCATION)
        );

        $response = $this->request()->post($endpoint, ['tipoCob' => $loc]);
        if ($response->successful()) {
            PayloadLocationCreatedEvent::dispatch($response->json());
        }

        return $response;
    }

    public function getById(string $id): Response
    {
        $endpoint = $this->getEndpoint($this->getPsp()->getConfig('base_url') . $this->resolveEndpoint(Endpoints::GET_PAYLOAD_LOCATION) . $id);

        return $this->request()->get($endpoint, $this->filters);
    }

    public function detachChargeFromLocation(string $id): Response
    {
        $endpoint = $this->getEndpoint(
            $this->getPsp()->getConfig('base_url') .
            $this->resolveEndpoint(Endpoints::DETACH_CHARGE_FROM_LOCATION)
            . $id
            . $this->resolveEndpoint(Endpoints::PAYLOAD_LOCATION_TXID)
        );

        $response = $this->request()->delete($endpoint);
        if ($response->successful()) {
            PayloadLocationDetachEvent::dispatch($id);
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

        $endpoint = $this->getEndpoint($this->getPsp()->getConfig('base_url') . $this->resolveEndpoint(Endpoints::GET_PAYLOAD_LOCATION));

        return $this->request()->get($endpoint, $this->filters);
    }
}
