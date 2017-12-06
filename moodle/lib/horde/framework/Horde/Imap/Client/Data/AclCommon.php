<?php
/**
 * Copyright 2011-2014 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2011-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * Provides common methods shared in all ACL classes (see RFC 2086/4314).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2011-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */
class Horde_Imap_Client_Data_AclCommon
{
    /** Constants for getString(). */
    const RFC_2086 = 1;
    const RFC_4314 = 2;

    /**
     * List of virtual rights (RFC 4314 [2.1.1]).
     *
     * @var array
     */
    protected $_virtual = array(
        Horde_Imap_Client::ACL_CREATE => array(
            Horde_Imap_Client::ACL_CREATEMBOX,
            Horde_Imap_Client::ACL_DELETEMBOX
        ),
        Horde_Imap_Client::ACL_DELETE => array(
            Horde_Imap_Client::ACL_DELETEMSGS,
            // Don't put this first - we do checks on the existence of the
            // first element in this array to determine the RFC type, and this
            // is duplicate of right contained in ACL_CREATE.
            Horde_Imap_Client::ACL_DELETEMBOX,
            Horde_Imap_Client::ACL_EXPUNGE
        )
    );

    /**
     * Returns the raw string to use in IMAP server calls.
     *
     * @param integer $type  The RFC type to use (RFC_* constant).
     *
     * @return string  The string representation of the ACL.
     */
    public function getString($type = self::RFC_4314)
    {
        $acl = strval($this);

        if ($type == self::RFC_2086) {
            foreach ($this->_virtual as $key => $val) {
                $acl = str_replace($val, '', $acl, $count);
                if ($count) {
                    $acl .= $key;
                }
            }
        }

        return $acl;
    }

}
