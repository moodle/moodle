<?php
/**
 * Copyright 2007-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (BSD). If you
 * did not receive this file, see http://www.horde.org/licenses/bsd.
 *
 * @category  Horde
 * @copyright 2007-2017 Horde LLC
 * @license   http://www.horde.org/licenses/bsd BSD
 * @package   Stream_Wrapper
 */

/**
 * Provides access to the StringStream stream wrapper.
 *
 * @author     Chuck Hagenbuch <chuck@horde.org>
 * @category   Horde
 * @copyright  2007-2016 Horde LLC
 * @deprecated Use Horde_Stream_Wrapper_String::getStream()
 * @license    http://www.horde.org/licenses/bsd BSD
 * @package    Stream_Wrapper
 */
interface Horde_Stream_Wrapper_StringStream
{
    /**
     * Return a reference to the wrapped string.
     *
     * @return string
     */
    public function &getString();
}
