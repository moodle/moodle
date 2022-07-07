<?php
/**
 * Copyright 2010-2017 Horde LLC (http://www.horde.org/)
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
 * Exception thrown if an object wasn't found.
 *
 * @author    
 * @category  Horde
 * @copyright 2010-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL
 * @package   Exception
 */
class Horde_Exception_NotFound extends Horde_Exception
{
    /**
     * Constructor.
     *
     * @see Horde_Exception::__construct()
     *
     * @param mixed $message           The exception message, a PEAR_Error
     *                                 object, or an Exception object.
     * @param integer $code            A numeric error code.
     */
    public function __construct($message = null, $code = null)
    {
        if (is_null($message)) {
            $message = Horde_Exception_Translation::t("Not Found");
        }
        parent::__construct($message, $code);
    }
}