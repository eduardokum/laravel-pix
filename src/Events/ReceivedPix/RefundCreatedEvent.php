<?php

namespace Eduardokum\LaravelPix\Events\ReceivedPix;

use Illuminate\Foundation\Events\Dispatchable;

class RefundCreatedEvent
{
    use Dispatchable;

    public array $refund;

    public string $e2eid;

    public string $refundId;

    public function __construct(array $refund, string $e2eid, string $refundId)
    {
        $this->refund = $refund;
        $this->e2eid = $e2eid;
        $this->refundId = $refundId;
    }
}
