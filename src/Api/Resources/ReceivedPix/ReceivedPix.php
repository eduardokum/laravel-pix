<?php

namespace Eduardokum\LaravelPix\Api\Resources\ReceivedPix;

use RuntimeException;
use Eduardokum\LaravelPix\Api\Api;
use Illuminate\Http\Client\Response;
use Eduardokum\LaravelPix\Support\Endpoints;
use Eduardokum\LaravelPix\Api\Contracts\ApplyApiFilters;
use Eduardokum\LaravelPix\Exceptions\ValidationException;
use Eduardokum\LaravelPix\Api\Contracts\FilterApiRequests;
use Eduardokum\LaravelPix\Events\ReceivedPix\RefundRequestedEvent;
use Eduardokum\LaravelPix\Api\Contracts\ConsumesReceivedPixEndpoints;

class ReceivedPix extends Api implements FilterApiRequests, ConsumesReceivedPixEndpoints
{
    private array $filters = [];

    public function withFilters($filters): ReceivedPix
    {
        if (! is_array($filters) && ! $filters instanceof ApplyApiFilters) {
            throw new RuntimeException("Filters should be an instance of 'FilterApiRequests' or an array.");
        }

        $this->filters = $filters instanceof ApplyApiFilters
            ? $filters->toArray()
            : $filters;

        return $this;
    }

    public function getBye2eid(string $e2eid): Response
    {
        $endpoint = $this->getEndpoint($this->getPsp()->getConfig('base_url') . $this->resolveEndpoint(Endpoints::RECEIVED_PIX) . $e2eid);

        return $this->request()->get($endpoint);
    }

    public function refund(string $e2eid, string $refundId): Response
    {
        $endpoint = $this->getEndpoint(
            $this->getPsp()->getConfig('base_url')
            . $this->resolveEndpoint(Endpoints::RECEIVED_PIX)
            . $e2eid
            . $this->resolveEndpoint(Endpoints::RECEIVED_PIX_REFUND)
            . $refundId
        );

        $refund = $this->request()->put($endpoint);

        event(new RefundRequestedEvent($refund->json(), $e2eid, $refundId));

        return $refund;
    }

    public function consultRefund(string $e2eid, string $refundId): Response
    {
        $endpoint = $this->getEndpoint(
            $this->getPsp()->getConfig('base_url')
            . $this->resolveEndpoint(Endpoints::RECEIVED_PIX)
            . $e2eid
            . $this->resolveEndpoint(Endpoints::RECEIVED_PIX_REFUND)
            . $refundId
        );

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

        $endpoint = $this->getEndpoint($this->getPsp()->getConfig('base_url') . $this->resolveEndpoint(Endpoints::RECEIVED_PIX));

        return $this->request()->get($endpoint, $this->filters);
    }
}
