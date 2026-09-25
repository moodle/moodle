<?php

declare(strict_types=1);

namespace OTPHP\Exception;

use InvalidArgumentException;
use Throwable;

/**
 * Exception thrown when a label or issuer format is invalid.
 * This includes violations of the Google Authenticator label specification.
 */
final class InvalidLabelException extends InvalidArgumentException implements OTPExceptionInterface
{
    public function __construct(
        string $message,
        public readonly string $labelName = '',
        public readonly mixed $labelValue = null,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
