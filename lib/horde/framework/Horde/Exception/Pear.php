<?php
/**
 * Horde exception class that converts PEAR errors to exceptions.
 *
 * Copyright 2008-2014 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category Horde
 * @package  Exception
 */
class Horde_Exception_Pear extends Horde_Exception
{
    /**
     * The class name for generated exceptions.
     *
     * @var string
     */
    static protected $_class = __CLASS__;

    /**
     * Exception constructor.
     *
     * @param PEAR_Error $error The PEAR error.
     */
    public function __construct(PEAR_Error $error)
    {
        parent::__construct($error->getMessage(), $error->getCode());
        $this->details = $this->_getPearTrace($error);
    }

    /**
     * Return a trace for the PEAR error.
     *
     * @param PEAR_Error $error The PEAR error.
     *
     * @return string The backtrace as a string.
     */
    private function _getPearTrace(PEAR_Error $error)
    {
        $pear_error = '';
        $backtrace = $error->getBacktrace();
        if (!empty($backtrace)) {
            $pear_error .= 'PEAR backtrace:' . "\n\n";
            foreach ($backtrace as $frame) {
                $pear_error .=
                      (isset($frame['class']) ? $frame['class'] : '')
                    . (isset($frame['type']) ? $frame['type'] : '')
                    . (isset($frame['function']) ? $frame['function'] : 'unkown') . ' '
                    . (isset($frame['file']) ? $frame['file'] : 'unkown') . ':'
                    . (isset($frame['line']) ? $frame['line'] : 'unkown') . "\n";
            }
        }
        $userinfo = $error->getUserInfo();
        if (!empty($userinfo)) {
            $pear_error .= "\n" . 'PEAR user info:' . "\n\n";
            if (is_string($userinfo)) {
                $pear_error .= $userinfo;
            } else {
                $pear_error .= print_r($userinfo, true);
            }
        }
        return $pear_error;
    }

    /**
     * Exception handling.
     *
     * @param mixed $result The result to be checked for a PEAR_Error.
     *
     * @return mixed Returns the original result if it was no PEAR_Error.
     *
     * @throws Horde_Exception_Pear In case the result was a PEAR_Error.
     */
    static public function catchError($result)
    {
        if ($result instanceOf PEAR_Error) {
            throw new self::$_class($result);
        }
        return $result;
    }
}
