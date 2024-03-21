<?php

namespace Eduardokum\LaravelPix\Events\Cobv;

use Illuminate\Foundation\Events\Dispatchable;

class CobvUpdatedEvent
{
    use Dispatchable;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
