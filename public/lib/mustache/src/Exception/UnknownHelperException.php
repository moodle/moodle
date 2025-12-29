<?php

/*
 * This file is part of Mustache.php.
 *
 * (c) 2010-2025 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mustache\Exception;

use Mustache\Exception;

/**
 * Unknown helper exception.
 */
class UnknownHelperException extends InvalidArgumentException implements Exception
{
    protected $helperName;

    /**
     * @param string    $helperName
     * @param Exception $previous
     */
    public function __construct($helperName, $previous = null)
    {
        $this->helperName = $helperName;
        $message = sprintf('Unknown helper: %s', $helperName);
        parent::__construct($message, 0, $previous);
    }

    public function getHelperName()
    {
        return $this->helperName;
    }
}
