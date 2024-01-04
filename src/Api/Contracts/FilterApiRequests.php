<?php

namespace Eduardokum\LaravelPix\Api\Contracts;

interface FilterApiRequests
{
    public function withFilters($filters): self;
}
