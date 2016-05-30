<?php
/**
 * Copyright 2013-2014 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2013-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * A null backend class for storing cached IMAP/POP data.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2013-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */
class Horde_Imap_Client_Cache_Backend_Null extends Horde_Imap_Client_Cache_Backend
{
    /**
     */
    public function get($mailbox, $uids, $fields, $uidvalid)
    {
        return array();
    }

    /**
     */
    public function getCachedUids($mailbox, $uidvalid)
    {
        return array();
    }

    /**
     */
    public function set($mailbox, $data, $uidvalid)
    {
    }

    /**
     */
    public function getMetaData($mailbox, $uidvalid, $entries)
    {
        return array(
            'uidvalid' => 0
        );
    }

    /**
     */
    public function setMetaData($mailbox, $data)
    {
    }

    /**
     */
    public function deleteMsgs($mailbox, $uids)
    {
    }

    /**
     */
    public function deleteMailbox($mailbox)
    {
    }

    /**
     */
    public function clear($lifetime)
    {
    }

}
