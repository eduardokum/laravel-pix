<?php

namespace Eduardokum\LaravelPix\Events\Cobv;

use Illuminate\Foundation\Events\Dispatchable;

class CobvCreatedEvent
{
    use Dispatchable;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
