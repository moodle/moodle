<?php

declare(strict_types=1);

namespace OTPHP\Exception;

use InvalidArgumentException;
use Throwable;

/**
 * Exception thrown when an OTP parameter has an invalid value.
 * This includes: secret, digits, algorithm, period, epoch, counter, secret size.
 */
final class InvalidParameterException extends InvalidArgumentException implements OTPExceptionInterface
{
    public function __construct(
        string $message,
        public readonly string $parameterName = '',
        public readonly mixed $parameterValue = null,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
