<?php

declare(strict_types=1);

namespace SimpleSAML\Error;

use Throwable;

/**
 * Exception indicating user aborting the authentication process.
 *
 * @package SimpleSAMLphp
 */

class UserAborted extends Error
{
    /**
     * Create the error
     *
     * @param \Throwable|null $cause  The exception that caused this error.
     */
    public function __construct(Throwable $cause = null)
    {
        parent::__construct('USERABORTED', $cause);
    }
}
