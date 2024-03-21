<?php

namespace Eduardokum\LaravelPix\Events\PayloadLocation;

use Illuminate\Foundation\Events\Dispatchable;

class PayloadLocationDetachEvent
{
    use Dispatchable;

    public $txid;

    public function __construct($txid)
    {
        $this->txid = $txid;
    }
}
