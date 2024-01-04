<?php

namespace Eduardokum\LaravelPix\Contracts\KeyValidations;

interface ValidateCPFKey
{
    public static function validateCPF(string $cpf): bool;
}
