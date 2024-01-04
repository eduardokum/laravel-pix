<?php

namespace Eduardokum\LaravelPix\Contracts\KeyValidations;

interface ValidateRandomKeys
{
    public static function validateRandom(string $key): bool;
}
