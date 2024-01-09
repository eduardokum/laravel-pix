<?php

namespace Eduardokum\LaravelPix\Api\Contracts;

interface ConsumesLoteCobvEndpoints
{
    public function createBatch(string $batchId, array $request);

    public function updateBatch(string $batchId, array $request);

    public function getByBatchId(string $batchId);

    public function all();
}
