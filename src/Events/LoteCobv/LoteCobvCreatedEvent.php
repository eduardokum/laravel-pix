<?php

namespace Eduardokum\LaravelPix\Events\Cob;

use Illuminate\Foundation\Events\Dispatchable;

class LoteCobvCreatedEvent
{
    use Dispatchable;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
