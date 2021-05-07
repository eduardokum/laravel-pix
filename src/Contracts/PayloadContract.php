<?php

namespace Junges\Pix\Contracts;

interface PayloadContract
{
    public function pixKey(string $pixKey): self;

    public function description(string $description): self;

    public function merchantName(string $merchantName): self;

    public function transactionId(string $transaction_id): self;

    public function amount(string $amount): self;

    public function getPayload(): string;
}