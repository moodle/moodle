<?php
/**
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (BSD). If you
 * did not receive this file, see http://www.horde.org/licenses/bsd.
 *
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 */

/**
 * Container object for a collection of group addresses.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 */
class Horde_Mail_Rfc822_GroupList extends Horde_Mail_Rfc822_List
{
    /**
     * Add objects to the container.
     *
     * @param mixed $obs  A RFC 822 object (or list of objects) to store in
     *                    this object.
     */
    public function add($obs)
    {
        if ($obs instanceof Horde_Mail_Rfc822_Object) {
            $obs = array($obs);
        }

        foreach ($obs as $val) {
            /* Only allow addresses. */
            if ($val instanceof Horde_Mail_Rfc822_Address) {
                parent::add($val);
            }
        }
    }

    /**
     * Group count.
     *
     * @return integer  The number of groups in the list.
     */
    public function groupCount()
    {
        return 0;
    }

}
