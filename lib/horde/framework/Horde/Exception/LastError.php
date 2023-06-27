<?php
/**
 * Copyright 2008-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL
 * @package  Exception
 */

/**
 * Horde exception class that accepts output of error_get_last() as $code and
 * mask itself as that error.
 *
 * @author    
 * @category  Horde
 * @copyright 2008-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL
 * @package   Exception
 */
class Horde_Exception_LastError extends Horde_Exception
{
    /**
     * Exception constructor
     *
     * If $lasterror is passed the return value of error_get_last() (or a
     * matching format), the exception will be rewritten to have its file and
     * line parameters match that of the array, and any message in the array
     * will be appended to $message.
     *
     * @param mixed $message             The exception message, a PEAR_Error
     *                                   object, or an Exception object.
     * @param mixed $code_or_lasterror   Either a numeric error code, or
     *                                   an array from error_get_last().
     */
    public function __construct($message = null, $code_or_lasterror = null)
    {
        if (is_array($code_or_lasterror)) {
            if ($message) {
                $message .= $code_or_lasterror['message'];
            } else {
                $message = $code_or_lasterror['message'];
            }
            parent::__construct($message, $code_or_lasterror['type']);
            $this->file = $code_or_lasterror['file'];
            $this->line = $code_or_lasterror['line'];
        } else {
            parent::__construct($message, $code_or_lasterror);
        }
    }

}
