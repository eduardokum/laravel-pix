<?php

namespace Eduardokum\LaravelPix\Facades;

use Illuminate\Support\Facades\Facade;
use Eduardokum\LaravelPix\Api\Features\Cobv\Cobv;
use Eduardokum\LaravelPix\Api\Features\Cobv\CobvRequest;
use Eduardokum\LaravelPix\Api\Features\Cobv\UpdateCobvRequest;

/**
 * Class ApiConsumes.
 *
 * @method static Cobv baseUrl(string $baseUrl);
 * @method static Cobv clientId(string $clientId);
 * @method static Cobv clientSecret(string $clientSecret);
 * @method static Cobv certificate(string $certificate);
 * @method static mixed getOauth2Token();
 * @method static Cobv withFilters($filters);
 * @method static array create(CobvRequest $request);
 * @method static array update(UpdateCobvRequest $request);
 * @method static array all();
 * @method static array getByTransactionId(string $transactionId);
 */
class CobvFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return Cobv::class;
    }
}
