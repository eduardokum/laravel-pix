<?php

namespace Eduardokum\LaravelPix\Events\LoteCobv;

use Illuminate\Foundation\Events\Dispatchable;

class LoteCobvUpdatedEvent
{
    use Dispatchable;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
