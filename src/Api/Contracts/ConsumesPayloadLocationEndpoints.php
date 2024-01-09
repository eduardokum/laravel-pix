<?php

namespace Eduardokum\LaravelPix\Api\Contracts;

interface ConsumesPayloadLocationEndpoints
{
    public function create(string $loc);

    public function all();

    public function getById(string $id);

    public function detachChargeFromLocation(string $id);
}
