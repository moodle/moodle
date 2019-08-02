<?php
/**
 * Copyright 2009-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (BSD). If you
 * did not receive this file, see http://www.horde.org/licenses/bsd.
 *
 * @category  Horde
 * @copyright 2009-2017 Horde LLC
 * @license   http://www.horde.org/licenses/bsd BSD
 * @package   Stream_Wrapper
 */

/**
 * Provides access to the CombineStream stream wrapper.
 *
 * @author     Michael Slusarz <slusarz@horde.org>
 * @category   Horde
 * @deprecated Use Horde_Stream_Wrapper_Combine::getStream()
 * @copyright  2009-2016 Horde LLC
 * @license    http://www.horde.org/licenses/bsd BSD
 * @package    Stream_Wrapper
 */
interface Horde_Stream_Wrapper_CombineStream
{
    /**
     * Return a reference to the data.
     *
     * @return array
     */
    public function getData();
}

