<?php
/**
 * Copyright 2014-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 */

/**
 * An interface to indicate that the header element represents e-mail address
 * data (RFC 5322).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 * @since     2.5.0
 */
interface Horde_Mime_Headers_Element_Address
{
    /**
     * Return the address list representation(s) for this header.
     *
     * @param boolean $first  If true, return only the first element rather
     *                        than the entire list.
     *
     * @return mixed  A Horde_Mail_Rfc822_List object (if $first is true) or
     *                an array of those objects.
     */
    public function getAddressList($first = false);

}
