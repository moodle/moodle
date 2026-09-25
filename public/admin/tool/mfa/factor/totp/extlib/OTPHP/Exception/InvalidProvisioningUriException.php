<?php

declare(strict_types=1);

namespace OTPHP\Exception;

use InvalidArgumentException;

/**
 * Exception thrown when a provisioning URI cannot be parsed or is invalid.
 * This includes: invalid format, wrong scheme, missing required parameters, unsupported OTP type.
 */
final class InvalidProvisioningUriException extends InvalidArgumentException implements OTPExceptionInterface
{
}
