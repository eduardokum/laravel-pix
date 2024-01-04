<?php

namespace Eduardokum\LaravelPix\Contracts\KeyValidations;

interface ValidateEmailKeys
{
    public static function validateEmail(string $key): bool;
}
