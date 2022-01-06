<?php
/**
 * Copyright 2008-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL-2.1
 * @package  Exception
 */

/**
 * Horde base exception class.
 *
 * @author    
 * @category  Horde
 * @copyright 2008-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL-2.1
 * @package   Exception
 */
class Horde_Exception extends Exception
{
    /**
     * Error details that should not be part of the main exception message,
     * e.g. any additional debugging information.
     *
     * @var string
     */
    public $details;

    /**
     * Has this exception been logged?
     *
     * @var boolean
     */
    public $logged = false;

    /**
     * The log level to use. A Horde_Log constant.
     *
     * @var integer
     */
    protected $_logLevel = 0;

    /**
     * Get the log level.
     *
     * @return integer  The Horde_Log constant for the log level.
     */
    public function getLogLevel()
    {
        return $this->_logLevel;
    }

    /**
     * Sets the log level.
     *
     * @param mixed $level  The log level.
     */
    public function setLogLevel($level = 0)
    {
        if (is_string($level)) {
            $level = defined('Horde_Log::' . $level)
                ? constant('Horde_Log::' . $level)
                : 0;
        }

        $this->_logLevel = $level;
    }

}
