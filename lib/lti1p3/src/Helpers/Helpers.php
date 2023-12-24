<?php

namespace Packback\Lti1p3\Helpers;

class Helpers
{
    public static function checkIfNullValue($value): bool
    {
        return !is_null($value);
    }
}
