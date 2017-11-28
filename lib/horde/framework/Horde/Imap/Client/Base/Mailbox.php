<?php
/**
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * An object allowing management of mailbox state within a
 * Horde_Imap_Client_Base object.
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
class Horde_Imap_Client_Base_Mailbox
{
    /**
     * Mapping object.
     *
     * @var Horde_Imap_Client_Ids_Map
     */
    public $map;

    /**
     * Is mailbox opened?
     *
     * @var boolean
     */
    public $open;

    /**
     * Is mailbox sync'd with remote server (via CONDSTORE/QRESYNC)?
     *
     * @var boolean
     */
    public $sync;

    /**
     * Status information.
     *
     * @var array
     */
    protected $_status = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Get status information for the mailbox.
     *
     * @param integer $entry  STATUS_* constant.
     *
     * @return mixed  Status information.
     */
    public function getStatus($entry)
    {
        if (isset($this->_status[$entry])) {
            return $this->_status[$entry];
        }

        switch ($entry) {
        case Horde_Imap_Client::STATUS_FLAGS:
        case Horde_Imap_Client::STATUS_SYNCFLAGUIDS:
        case Horde_Imap_Client::STATUS_SYNCVANISHED:
            return array();

        case Horde_Imap_Client::STATUS_FIRSTUNSEEN:
            /* If we know there are no messages in the current mailbox, we
             * know there are no unseen messages. */
            return empty($this->_status[Horde_Imap_Client::STATUS_MESSAGES])
                ? false
                : null;

        case Horde_Imap_Client::STATUS_RECENT_TOTAL:
        case Horde_Imap_Client::STATUS_SYNCMODSEQ:
            return 0;

        case Horde_Imap_Client::STATUS_PERMFLAGS:
            /* If PERMFLAGS is not returned by server, must assume that all
             * flags can be changed permanently (RFC 3501 [6.3.1]). */
            $flags = isset($this->_status[Horde_Imap_Client::STATUS_FLAGS])
                ? $this->_status[Horde_Imap_Client::STATUS_FLAGS]
                : array();
            $flags[] = "\\*";
            return $flags;

        case Horde_Imap_Client::STATUS_UIDNOTSTICKY:
            /* In the absence of explicit uidnotsticky identification, assume
             * that UIDs are sticky. */
            return false;

        case Horde_Imap_Client::STATUS_UNSEEN:
            /* If we know there are no messages in the current mailbox, we
             * know there are no unseen messages . */
            return empty($this->_status[Horde_Imap_Client::STATUS_MESSAGES])
                ? 0
                : null;

        default:
            return null;
        }
    }

    /**
     * Set status information for the mailbox.
     *
     * @param integer $entry  STATUS_* constant.
     * @param mixed $value    Status information.
     */
    public function setStatus($entry, $value)
    {
        switch ($entry) {
        case Horde_Imap_Client::STATUS_FIRSTUNSEEN:
        case Horde_Imap_Client::STATUS_HIGHESTMODSEQ:
        case Horde_Imap_Client::STATUS_MESSAGES:
        case Horde_Imap_Client::STATUS_UNSEEN:
        case Horde_Imap_Client::STATUS_UIDNEXT:
        case Horde_Imap_Client::STATUS_UIDVALIDITY:
            $value = intval($value);
            break;

        case Horde_Imap_Client::STATUS_RECENT:
            /* Keep track of RECENT_TOTAL information. */
            $this->_status[Horde_Imap_Client::STATUS_RECENT_TOTAL] = isset($this->_status[Horde_Imap_Client::STATUS_RECENT_TOTAL])
                ? ($this->_status[Horde_Imap_Client::STATUS_RECENT_TOTAL] + $value)
                : intval($value);
            break;

        case Horde_Imap_Client::STATUS_SYNCMODSEQ:
            /* This is only set once per access. */
            if (isset($this->_status[$entry])) {
                return;
            }
            $value = intval($value);
            break;

        case Horde_Imap_Client::STATUS_SYNCFLAGUIDS:
        case Horde_Imap_Client::STATUS_SYNCVANISHED:
            if (!isset($this->_status[$entry])) {
                $this->_status[$entry] = array();
            }
            $this->_status[$entry] = array_merge($this->_status[$entry], $value);
            return;
        }

        $this->_status[$entry] = $value;
    }

    /**
     * Reset the mailbox information.
     */
    public function reset()
    {
        $keep = array(
            Horde_Imap_Client::STATUS_SYNCFLAGUIDS,
            Horde_Imap_Client::STATUS_SYNCMODSEQ,
            Horde_Imap_Client::STATUS_SYNCVANISHED
        );

        foreach (array_diff(array_keys($this->_status), $keep) as $val) {
            unset($this->_status[$val]);
        }

        $this->map = new Horde_Imap_Client_Ids_Map();
        $this->open = $this->sync = false;
    }

}
