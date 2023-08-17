<?php

namespace Junges\Pix\Api\Resources\Banking;

use RuntimeException;
use Junges\Pix\Api\BankingApi;
use Junges\Pix\Support\Endpoints;
use Illuminate\Http\Client\Response;
use Junges\Pix\Support\BankingEndpoints;
use Junges\Pix\Api\Contracts\ApplyApiFilters;
use Junges\Pix\Exceptions\ValidationException;
use Junges\Pix\Api\Contracts\FilterApiRequests;

class Balance extends BankingApi implements FilterApiRequests
{
    private array $filters = [];

    public function withFilters($filters): Balance
    {
        if (!is_array($filters) && !$filters instanceof ApplyApiFilters) {
            throw new RuntimeException("Filters should be an instance of 'FilterApiRequests' or an array.");
        }

        $this->filters = $filters instanceof ApplyApiFilters
                ? $filters->toArray()
                : $filters;

        return $this;
    }

    public function getBalance(): Response
    {
        $endpoint = $this->getEndpoint($this->baseUrl.$this->resolveBankingEndpoint(BankingEndpoints::GET_BALANCE));

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

        $endpoint = $this->getEndpoint($this->baseUrl.$this->resolveBankingEndpoint(BankingEndpoints::GET_BALANCE));

        return $this->request()->get($endpoint, $this->filters);
    }
}
