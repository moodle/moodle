<?php

declare(strict_types=1);

namespace SimpleSAML\Logger;

use SimpleSAML\Configuration;

/**
 * The interface that must be implemented by any log handler.
 *
 * @author Jaime Perez Crespo, UNINETT AS.
 * @package SimpleSAMLphp
 */

interface LoggingHandlerInterface
{
    /**
     * Constructor for log handlers. It must accept receiving a \SimpleSAML\Configuration object.
     *
     * @param \SimpleSAML\Configuration $config The configuration to use in this log handler.
     */
    public function __construct(Configuration $config);


    /**
     * Log a message to its destination.
     *
     * @param int $level The log level.
     * @param string $string The message to log.
     * @return void
     */
    public function log($level, $string);


    /**
     * Set the format desired for the logs.
     *
     * @param string $format The format used for logs.
     * @return void
     */
    public function setLogFormat($format);
}
