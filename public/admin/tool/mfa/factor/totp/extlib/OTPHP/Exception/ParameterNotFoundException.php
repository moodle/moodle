<?php

declare(strict_types=1);

namespace OTPHP\Exception;

use InvalidArgumentException;
use Throwable;

/**
 * Exception thrown when attempting to access a parameter that doesn't exist.
 */
final class ParameterNotFoundException extends InvalidArgumentException implements OTPExceptionInterface
{
    public function __construct(
        string $message,
        public readonly string $parameterName = '',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
