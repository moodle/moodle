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
 * Mailbox synchronization results.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 * @since     2.2.0
 *
 * @property-read Horde_Imap_Client_Ids $flagsuids  List of messages with flag
 *                                                  changes.
 * @property-read Horde_Imap_Client_Ids $newmsgsuids  List of new messages.
 * @property-read Horde_Imap_Client_Ids $vanisheduids  List of messages that
 *                                                     have vanished.
 */
class Horde_Imap_Client_Data_Sync
{
    /**
     * Mappings of status() values to sync keys.
     *
     * @since 2.8.0
     *
     * @var array
     */
    public static $map = array(
        'H' => 'highestmodseq',
        'M' => 'messages',
        'U' => 'uidnext',
        'V' => 'uidvalidity'
    );

    /**
     * Are there messages that have had flag changes?
     *
     * @var boolean
     */
    public $flags = null;

    /**
     * The previous value of HIGHESTMODSEQ.
     *
     * @since 2.8.0
     *
     * @var integer
     */
    public $highestmodseq = null;

    /**
     * The synchronized mailbox.
     *
     * @var Horde_Imap_Client_Mailbox
     */
    public $mailbox;

    /**
     * The previous number of messages in the mailbox.
     *
     * @since 2.8.0
     *
     * @var integer
     */
    public $messages = null;

    /**
     * Are there new messages?
     *
     * @var boolean
     */
    public $newmsgs = null;

    /**
     * The previous value of UIDNEXT.
     *
     * @since 2.8.0
     *
     * @var integer
     */
    public $uidnext = null;

    /**
     * The previous value of UIDVALIDITY.
     *
     * @since 2.8.0
     *
     * @var integer
     */
    public $uidvalidity = null;

    /**
     * The UIDs of messages that are guaranteed to have vanished. This list is
     * only guaranteed to be available if the server supports QRESYNC or a
     * list of known UIDs is passed to the sync() method.
     *
     * @var Horde_Imap_Client_Ids
     */
    public $vanished = null;

    /**
     * UIDs of messages that have had flag changes.
     *
     * @var Horde_Imap_Client_Ids
     */
    protected $_flagsuids;

    /**
     * UIDs of new messages.
     *
     * @var Horde_Imap_Client_Ids
     */
    protected $_newmsgsuids;

    /**
     * UIDs of messages that have vanished.
     *
     * @var Horde_Imap_Client_Ids
     */
    protected $_vanisheduids;

    /**
     * Constructor.
     *
     * @param Horde_Imap_Client_Base $base_ob  Base driver object.
     * @param mixed $mailbox                   Mailbox to sync.
     * @param array $sync                      Token sync data.
     * @param array $curr                      Current sync data.
     * @param integer $criteria                Mask of criteria to return.
     * @param Horde_Imap_Client_Ids $ids       List of known UIDs.
     *
     * @throws Horde_Imap_Client_Exception
     * @throws Horde_Imap_Client_Exception_Sync
     */
    public function __construct(Horde_Imap_Client_Base $base_ob, $mailbox,
                                $sync, $curr, $criteria, $ids)
    {
        foreach (self::$map as $key => $val) {
            if (isset($sync[$key])) {
                $this->$val = $sync[$key];
            }
        }

        /* Check uidvalidity. */
        if (!$this->uidvalidity || ($curr['V'] != $this->uidvalidity)) {
            throw new Horde_Imap_Client_Exception_Sync('UIDs in cached mailbox have changed.', Horde_Imap_Client_Exception_Sync::UIDVALIDITY_CHANGED);
        }

        $this->mailbox = $mailbox;

        /* This was a UIDVALIDITY check only. */
        if (!$criteria) {
            return;
        }

        $sync_all = ($criteria & Horde_Imap_Client::SYNC_ALL);

        /* New messages. */
        if ($sync_all ||
            ($criteria & Horde_Imap_Client::SYNC_NEWMSGS) ||
            ($criteria & Horde_Imap_Client::SYNC_NEWMSGSUIDS)) {
            $this->newmsgs = empty($this->uidnext)
                ? !empty($curr['U'])
                : (!empty($curr['U']) && ($curr['U'] > $this->uidnext));

            if ($this->newmsgs &&
                ($sync_all ||
                 ($criteria & Horde_Imap_Client::SYNC_NEWMSGSUIDS))) {
                $new_ids = empty($this->uidnext)
                    ? Horde_Imap_Client_Ids::ALL
                    : ($this->uidnext . ':' . $curr['U']);

                $squery = new Horde_Imap_Client_Search_Query();
                $squery->ids(new Horde_Imap_Client_Ids($new_ids));
                $sres = $base_ob->search($mailbox, $squery);

                $this->_newmsgsuids = $sres['match'];
            }
        }

        /* Do single status call to get all necessary data. */
        if ($this->highestmodseq &&
            ($sync_all ||
             ($criteria & Horde_Imap_Client::SYNC_FLAGS) ||
             ($criteria & Horde_Imap_Client::SYNC_FLAGSUIDS) ||
             ($criteria & Horde_Imap_Client::SYNC_VANISHED) ||
             ($criteria & Horde_Imap_Client::SYNC_VANISHEDUIDS))) {
            $status_sync = $base_ob->status($mailbox, Horde_Imap_Client::STATUS_SYNCMODSEQ | Horde_Imap_Client::STATUS_SYNCFLAGUIDS | Horde_Imap_Client::STATUS_SYNCVANISHED);

            if (!is_null($ids)) {
                $ids = $base_ob->resolveIds($mailbox, $ids);
            }
        }

        /* Flag changes. */
        if ($sync_all || ($criteria & Horde_Imap_Client::SYNC_FLAGS)) {
            $this->flags = $this->highestmodseq
                ? ($this->highestmodseq != $curr['H'])
                : true;
        }

        if ($sync_all || ($criteria & Horde_Imap_Client::SYNC_FLAGSUIDS)) {
            if ($this->highestmodseq) {
                if ($this->highestmodseq == $status_sync['syncmodseq']) {
                    $this->_flagsuids = is_null($ids)
                        ? $status_sync['syncflaguids']
                        : $base_ob->getIdsOb(array_intersect($ids->ids, $status_sync['syncflaguids']->ids));
                } else {
                    $squery = new Horde_Imap_Client_Search_Query();
                    $squery->modseq($this->highestmodseq + 1);
                    $sres = $base_ob->search($mailbox, $squery, array(
                        'ids' => $ids
                    ));
                    $this->_flagsuids = $sres['match'];
                }
            } else {
                /* Without MODSEQ, need to mark all FLAGS as changed. */
                $this->_flagsuids = $base_ob->resolveIds($mailbox, is_null($ids) ? $base_ob->getIdsOb(Horde_Imap_Client_Ids::ALL) : $ids);
            }
        }

        /* Vanished messages. */
        if ($sync_all ||
            ($criteria & Horde_Imap_Client::SYNC_VANISHED) ||
            ($criteria & Horde_Imap_Client::SYNC_VANISHEDUIDS)) {
            if ($this->highestmodseq &&
                ($this->highestmodseq == $status_sync['syncmodseq'])) {
                $vanished = is_null($ids)
                    ? $status_sync['syncvanished']
                    : $base_ob->getIdsOb(array_intersect($ids->ids, $status_sync['syncvanished']->ids));
            } else {
                $vanished = $base_ob->vanished($mailbox, $this->highestmodseq ? $this->highestmodseq : 1, array(
                    'ids' => $ids
                ));
            }

            $this->vanished = (bool)count($vanished);
            $this->_vanisheduids = $vanished;
        }
    }

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'flagsuids':
        case 'newmsgsuids':
        case 'vanisheduids':
            return empty($this->{'_' . $name})
                ? new Horde_Imap_Client_Ids()
                : $this->{'_' . $name};
        }
    }

}
