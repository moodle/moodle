<?php

declare(strict_types=1);

namespace SimpleSAML\Error;

/**
 * Exception to indicate that we cannot set a cookie.
 *
 * @author Jaime Pérez Crespo <jaime.perez@uninett.no>
 * @package SimpleSAMLphp
 */

class CannotSetCookie extends Exception
{
    /**
     * The exception was thrown for unknown reasons.
     *
     * @var int
     */
    public const UNKNOWN = 0;

    /**
     * The exception was due to the HTTP headers being already sent, and therefore we cannot send additional headers to
     * set the cookie.
     *
     * @var int
     */
    public const HEADERS_SENT = 1;

    /**
     * The exception was due to trying to set a secure cookie over an insecure channel.
     *
     * @var int
     */
    public const SECURE_COOKIE = 2;
}
