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
 * Unknown filter exception.
 */
class UnknownFilterException extends \UnexpectedValueException implements Exception
{
    protected $filterName;

    /**
     * @param string    $filterName
     * @param Exception $previous
     */
    public function __construct($filterName, $previous = null)
    {
        $this->filterName = $filterName;
        $message = sprintf('Unknown filter: %s', $filterName);
        parent::__construct($message, 0, $previous);
    }

    public function getFilterName()
    {
        return $this->filterName;
    }
}
