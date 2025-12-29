<?php

/*
 * This file is part of Mustache.php.
 *
 * (c) 2010-2025 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mustache\Cache;

use Mustache\Cache;
use Mustache\Exception\InvalidArgumentException;
use Mustache\Logger;
use Psr\Log\LoggerInterface;

/**
 * Abstract Mustache Cache class.
 *
 * Provides logging support to child implementations.
 *
 * @abstract
 */
abstract class AbstractCache implements Cache
{
    private $logger = null;

    /**
     * Get the current logger instance.
     *
     * @return Logger|LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Set a logger instance.
     *
     * @param Logger|LoggerInterface $logger
     */
    public function setLogger($logger = null)
    {
        // n.b. this uses `is_a` to prevent a dependency on Psr\Log
        if ($logger !== null && !$logger instanceof Logger && !is_a($logger, 'Psr\\Log\\LoggerInterface')) {
            throw new InvalidArgumentException('Expected an instance of Mustache\\Logger or Psr\\Log\\LoggerInterface.');
        }

        $this->logger = $logger;
    }

    /**
     * Add a log record if logging is enabled.
     *
     * @param string $level   The logging level
     * @param string $message The log message
     * @param array  $context The log context
     */
    protected function log($level, $message, array $context = [])
    {
        if (isset($this->logger)) {
            $this->logger->log($level, $message, $context);
        }
    }
}
