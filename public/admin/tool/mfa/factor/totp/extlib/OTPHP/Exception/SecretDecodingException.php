<?php

declare(strict_types=1);

namespace OTPHP\Exception;

use RuntimeException;

/**
 * Exception thrown when a secret cannot be decoded from Base32.
 */
final class SecretDecodingException extends RuntimeException implements OTPExceptionInterface
{
}
