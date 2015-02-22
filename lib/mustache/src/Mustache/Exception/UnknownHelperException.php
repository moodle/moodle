<?php

/*
 * This file is part of Mustache.php.
 *
 * (c) 2010-2014 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Unknown helper exception.
 */
class Mustache_Exception_UnknownHelperException extends InvalidArgumentException implements Mustache_Exception
{
    protected $helperName;

    /**
     * @param string $helperName
     */
    public function __construct($helperName)
    {
        $this->helperName = $helperName;
        parent::__construct(sprintf('Unknown helper: %s', $helperName));
    }

    public function getHelperName()
    {
        return $this->helperName;
    }
}
