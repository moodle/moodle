<?php
/**
 * Copyright 2013-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2013-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * A Horde_HashTable implementation for caching IMAP/POP data.
 * Requires the Horde_HashTable and Horde_Pack packages.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2013-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 * @since     2.17.0
 */
class Horde_Imap_Client_Cache_Backend_Hashtable
extends Horde_Imap_Client_Cache_Backend
{
    /** Separator for CID between mailbox and UID. */
    const CID_SEPARATOR = '|';

    /**
     * The working data for the current pageload. All changes take place to
     * this data.
     *
     * @var array
     */
    protected $_data = array();

    /**
     * HashTable object.
     *
     * @var Horde_HashTable
     */
    protected $_hash;

    /**
     * Mailbox level data.
     *
     * @var array
     */
    protected $_mbox = array();

    /**
     * Horde_Pack singleton object.
     *
     * @var Horde_Pack
     */
    protected $_pack;

    /**
     * List of mailbox/UIDs to update.
     * Keys are mailboxes. Values are arrays with three possible keys:
     * <pre>
     *   - d: UIDs to delete
     *   - m: Was metadata updated?
     *   - u: UIDs to update
     * </pre>
     *
     * @var array
     */
    protected $_update = array();

    /**
     * Constructor.
     *
     * @param array $params  Configuration parameters:
     * <pre>
     *   - REQUIRED parameters:
     *     - hashtable: (Horde_HashTable) A HashTable object.
     *
     *   - Optional Parameters:
     *     - lifetime: (integer) The lifetime of the cache data (in seconds).
     *                 DEFAULT: 604800 seconds (1 week) [@since 2.19.0]
     * </pre>
     */
    public function __construct(array $params = array())
    {
        if (!isset($params['hashtable'])) {
            throw new InvalidArgumentException('Missing hashtable parameter.');
        }

        parent::__construct(array_merge(array(
            'lifetime' => 604800
        ), $params));
    }

    /**
     */
    protected function _initOb()
    {
        $this->_hash = $this->_params['hashtable'];
        $this->_pack = new Horde_Pack();
        register_shutdown_function(array($this, 'save'));
    }

    /**
     */
    public function get($mailbox, $uids, $fields, $uidvalid)
    {
        $ret = array();

        if (empty($uids)) {
            return $ret;
        }

        $this->_loadUids($mailbox, $uids, $uidvalid);

        if (empty($this->_data[$mailbox])) {
            return $ret;
        }

        if (!empty($fields)) {
            $fields = array_flip($fields);
        }
        $ptr = &$this->_data[$mailbox];
        $to_delete = array();

        foreach ($uids as $val) {
            if (isset($ptr[$val])) {
                if (is_string($ptr[$val])) {
                    try {
                        $ptr[$val] = $this->_pack->unpack($ptr[$val]);
                    } catch (Horde_Pack_Exception $e) {
                        $to_delete[] = $val;
                        continue;
                    }
                }

                $ret[$val] = (empty($fields) || empty($ptr[$val]))
                    ? $ptr[$val]
                    : array_intersect_key($ptr[$val], $fields);
            } else {
                $to_delete[] = $val;
            }
        }

        $this->deleteMsgs($mailbox, $to_delete);

        return $ret;
    }

    /**
     */
    public function getCachedUids($mailbox, $uidvalid)
    {
        $this->_loadMailbox($mailbox, $uidvalid);
        return $this->_mbox[$mailbox]['u']->ids;
    }

    /**
     */
    public function set($mailbox, $data, $uidvalid)
    {
        $this->_loadUids($mailbox, array_keys($data), $uidvalid);

        $d = &$this->_data[$mailbox];
        $to_add = array();

        foreach ($data as $k => $v) {
            if (isset($d[$k]) && is_string($d[$k])) {
                try {
                    $d[$k] = $this->_pack->unpack($d[$k]);
                } catch (Horde_Pack_Exception $e) {
                    continue;
                }
            }

            $d[$k] = (isset($d[$k]) && is_array($d[$k]))
                ? array_merge($d[$k], $v)
                : $v;
            $this->_update[$mailbox]['u'][$k] = true;
            unset($this->_update[$mailbox]['d'][$k]);
            $to_add[] = $k;
        }

        if (!empty($to_add)) {
            $this->_mbox[$mailbox]['u']->add($to_add);
            $this->_update[$mailbox]['m'] = true;
        }
    }

    /**
     */
    public function getMetaData($mailbox, $uidvalid, $entries)
    {
        $this->_loadMailbox($mailbox, $uidvalid);

        return empty($entries)
            ? $this->_mbox[$mailbox]['d']
            : array_intersect_key($this->_mbox[$mailbox]['d'], array_flip($entries));
    }

    /**
     */
    public function setMetaData($mailbox, $data)
    {
        $this->_loadMailbox($mailbox, isset($data['uidvalid']) ? $data['uidvalid'] : null);

        $this->_mbox[$mailbox]['d'] = array_merge(
            $this->_mbox[$mailbox]['d'],
            $data
        );
        $this->_update[$mailbox]['m'] = true;
    }

    /**
     */
    public function deleteMsgs($mailbox, $uids)
    {
        if (empty($uids)) {
            return;
        }

        $this->_loadMailbox($mailbox);

        foreach ($uids as $val) {
            unset(
                $this->_data[$mailbox][$val],
                $this->_update[$mailbox]['u'][$val]
            );
            $this->_update[$mailbox]['d'][$val] = true;
        }

        $this->_mbox[$mailbox]['u']->remove($uids);
        $this->_update[$mailbox]['m'] = true;
    }

    /**
     */
    public function deleteMailbox($mailbox)
    {
        /* Do this action immediately, instead of at shutdown. Makes coding
         * simpler. */
        $this->_loadMailbox($mailbox);

        $this->_hash->delete(array_merge(
            array($this->_getCid($mailbox)),
            array_values($this->_getMsgCids($mailbox, $this->_mbox[$mailbox]['u']))
        ));

        unset(
            $this->_data[$mailbox],
            $this->_mbox[$mailbox],
            $this->_update[$mailbox]
        );
    }

    /**
     */
    public function clear($lifetime)
    {
        /* Only can clear mailboxes we know about. */
        foreach (array_keys($this->_mbox) as $val) {
            $this->deleteMailbox($val);
        }

        $this->_data = $this->_mbox = $this->_update = array();
    }

    /**
     * Updates the cache.
     */
    public function save()
    {
        foreach ($this->_update as $mbox => $val) {
            try {
                if (!empty($val['u'])) {
                     $ptr = &$this->_data[$mbox];
                     foreach ($this->_getMsgCids($mbox, array_keys($val['u'])) as $k2 => $v2) {
                         try {
                             $this->_hash->set(
                                 $v2,
                                 $this->_pack->pack($ptr[$k2]),
                                 array('expire' => $this->_params['lifetime'])
                             );
                         } catch (Horde_Pack_Exception $e) {
                             $this->deleteMsgs($mbox, array($v2));
                             $val['d'][] = $v2;
                         }
                     }
                 }

                 if (!empty($val['d'])) {
                     $this->_hash->delete(array_values(
                         $this->_getMsgCids($mbox, $val['d'])
                     ));
                 }

                 if (!empty($val['m'])) {
                     try {
                         $this->_hash->set(
                             $this->_getCid($mbox),
                             $this->_pack->pack($this->_mbox[$mbox]),
                             array('expire' => $this->_params['lifetime'])
                         );
                     } catch (Horde_Pack_Exception $e) {}
                 }
            } catch (Horde_Exception $e) {
            }
        }

        $this->_update = array();
    }

    /**
     * Loads basic mailbox information.
     *
     * @param string $mailbox    The mailbox to load.
     * @param integer $uidvalid  The IMAP uidvalidity value of the mailbox.
     */
    protected function _loadMailbox($mailbox, $uidvalid = null)
    {
        if (!isset($this->_mbox[$mailbox]) &&
            ($ob = $this->_hash->get($this->_getCid($mailbox)))) {
            try {
                $this->_mbox[$mailbox] = $this->_pack->unpack($ob);
            } catch (Horde_Pack_Exception $e) {}
        }

        if (isset($this->_mbox[$mailbox])) {
            if (is_null($uidvalid) ||
                ($uidvalid == $this->_mbox[$mailbox]['d']['uidvalid'])) {
                return;
            }
            $this->deleteMailbox($mailbox);
        }

        $this->_mbox[$mailbox] = array(
            // Metadata storage
            // By default includes UIDVALIDITY of mailbox.
            'd' => array('uidvalid' => $uidvalid),
            // List of UIDs
            'u' => new Horde_Imap_Client_Ids()
        );
    }

    /**
     * Load UIDs by regenerating from the cache.
     *
     * @param string $mailbox    The mailbox to load.
     * @param array $uids        The UIDs to load.
     * @param integer $uidvalid  The IMAP uidvalidity value of the mailbox.
     */
    protected function _loadUids($mailbox, $uids, $uidvalid = null)
    {
        if (!isset($this->_data[$mailbox])) {
            $this->_data[$mailbox] = array();
        }

        $this->_loadMailbox($mailbox, $uidvalid);

        if (empty($uids)) {
            return;
        }

        $ptr = &$this->_data[$mailbox];

        $load = array_flip(
            array_diff_key(
                $this->_getMsgCids(
                    $mailbox,
                    array_unique(array_intersect($this->_mbox[$mailbox]['u']->ids, $uids))
                ),
                $this->_data[$mailbox]
            )
        );

        foreach (array_filter($this->_hash->get(array_keys($load))) as $key => $val) {
            $ptr[$load[$key]] = $val;
        }
    }

    /**
     * Create the unique ID used to store the mailbox data in the cache.
     *
     * @param string $mailbox  The mailbox to cache.
     *
     * @return string  The cache ID.
     */
    protected function _getCid($mailbox)
    {
        return implode(self::CID_SEPARATOR, array(
            'horde_imap_client',
            $this->_params['username'],
            $mailbox,
            $this->_params['hostspec'],
            $this->_params['port']
        ));
    }

    /**
     * Return a list of cache IDs for mailbox/UID pairs.
     *
     * @param string $mailbox  The mailbox to cache.
     * @param array $ids       The UID list.
     *
     * @return array  List of UIDs => cache IDs.
     */
    protected function _getMsgCids($mailbox, $ids)
    {
        $cid = $this->_getCid($mailbox);
        $out = array();

        foreach ($ids as $val) {
            $out[$val] = $cid . self::CID_SEPARATOR . $val;
        }

        return $out;
    }

}
