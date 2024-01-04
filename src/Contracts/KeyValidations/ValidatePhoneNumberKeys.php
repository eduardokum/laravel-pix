<?php

namespace Eduardokum\LaravelPix\Contracts\KeyValidations;

interface ValidatePhoneNumberKeys
{
    public static function validatePhoneNumber(string $phone): bool;
}
