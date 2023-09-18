<?php

namespace Packback\Lti1p3\Interfaces;

/** @internal */
interface IMessageValidator
{
    public static function getMessageType(): string;

    public static function canValidate(array $jwtBody): bool;

    public static function validate(array $jwtBody): void;
}
