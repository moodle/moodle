<?php
/**
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * Class containing deprecated Horde_Imap_Client_Base methods that will be
 * removed in version 3.0.
 *
 * NOTE: This class is NOT intended to be accessed outside of a Base object.
 * There is NO guarantees that the API of this class will not change across
 * versions.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @internal
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */
class Horde_Imap_Client_Base_Deprecated
{
    /**
     * Returns a unique identifier for the current mailbox status.
     *
     * @param Horde_Imap_Client_Base $base_ob  The base driver object.
     * @param mixed $mailbox                   A mailbox. Either a
     *                                         Horde_Imap_Client_Mailbox
     *                                         object or a string (UTF-8).
     * @param boolean $condstore               Is CONDSTORE enabled?
     * @param array $addl                      Additional cache info to add to
     *                                         the cache ID string.
     *
     * @return string  The cache ID string, which will change when the
     *                 composition of the mailbox changes. The uidvalidity
     *                 will always be the first element, and will be delimited
     *                 by the '|' character.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public static function getCacheId($base_ob, $mailbox, $condstore,
                                      array $addl = array())
    {
        $query = Horde_Imap_Client::STATUS_UIDVALIDITY | Horde_Imap_Client::STATUS_MESSAGES | Horde_Imap_Client::STATUS_UIDNEXT;

        /* Use MODSEQ as cache ID if CONDSTORE extension is available. */
        if ($condstore) {
            $query |= Horde_Imap_Client::STATUS_HIGHESTMODSEQ;
        } else {
            $query |= Horde_Imap_Client::STATUS_UIDNEXT_FORCE;
        }

        $status = $base_ob->status($mailbox, $query);

        if (empty($status['highestmodseq'])) {
            $parts = array(
                'V' . $status['uidvalidity'],
                'U' . $status['uidnext'],
                'M' . $status['messages']
            );
        } else {
            $parts = array(
                'V' . $status['uidvalidity'],
                'H' . $status['highestmodseq']
            );
        }

        return implode('|', array_merge($parts, $addl));
    }

    /**
     * Parses a cacheID created by getCacheId().
     *
     * @param string $id  The cache ID.
     *
     * @return array  An array with the following information:
     *   - highestmodseq: (integer)
     *   - messages: (integer)
     *   - uidnext: (integer)
     *   - uidvalidity: (integer) Always present
     */
    public static function parseCacheId($id)
    {
        $data = array(
            'H' => 'highestmodseq',
            'M' => 'messages',
            'U' => 'uidnext',
            'V' => 'uidvalidity'
        );
        $info = array();

        foreach (explode('|', $id) as $part) {
            if (isset($data[$part[0]])) {
                $info[$data[$part[0]]] = intval(substr($part, 1));
            }
        }

        return $info;
    }

}
