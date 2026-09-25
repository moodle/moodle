<?php

declare(strict_types=1);

namespace OTPHP\Exception;

use Throwable;

/**
 * Marker interface for all OTPHP exceptions.
 * This allows catching all OTPHP-specific exceptions while maintaining
 * backward compatibility with PHP's built-in exception types.
 */
interface OTPExceptionInterface extends Throwable
{
}
