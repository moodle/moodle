<?php

declare(strict_types=1);

namespace SAML2\Exception;

use InvalidArgumentException as BuiltinInvalidArgumentException;

class InvalidArgumentException extends BuiltinInvalidArgumentException implements Throwable
{
    /**
     * @param string $expected description of expected type
     * @param mixed  $parameter the parameter that is not of the expected type.
     *
     * @return \SAML2\Exception\InvalidArgumentException
     */
    public static function invalidType(string $expected, $parameter) : InvalidArgumentException
    {
        $message = sprintf(
            'Invalid Argument type: "%s" expected, "%s" given',
            $expected,
            is_object($parameter) ? get_class($parameter) : gettype($parameter)
        );

        return new self($message);
    }
}
