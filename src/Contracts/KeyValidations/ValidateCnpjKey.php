<?php

namespace Eduardokum\LaravelPix\Contracts\KeyValidations;

interface ValidateCnpjKey
{
    public static function validateCnpj(string $cnpj): bool;
}
