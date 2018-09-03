<?php
/**
 * Copyright 2005-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2005-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * An interface to cache data retrieved from the IMAP server.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2005-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */
class Horde_Imap_Client_Cache
{
    /**
     * Base client object.
     *
     * @var Horde_Imap_Client_Base
     */
    protected $_baseob;

    /**
     * Storage backend.
     *
     * @var Horde_Imap_Client_Cache_Backend
     */
    protected $_backend;

    /**
     * Debug output.
     *
     * @var Horde_Imap_Client_Base_Debug
     */
    protected $_debug = false;

    /**
     * The configuration params.
     *
     * @var array
     */
    protected $_params = array();

    /**
     * Constructor.
     *
     * @param array $params  Configuration parameters:
     * <pre>
     *   - REQUIRED Parameters:
     *     - backend: (Horde_Imap_Client_Cache_Backend) The cache backend.
     *     - baseob: (Horde_Imap_Client_Base) The base client object.
     *
     *   - Optional Parameters:
     *     - debug: (Horde_Imap_Client_Base_Debug) Debug object.
     *              DEFAULT: No debug output
     * </pre>
     */
    public function __construct(array $params = array())
    {
        $this->_backend = $params['backend'];
        $this->_baseob = $params['baseob'];

        $this->_backend->setParams(array(
            'hostspec' => $this->_baseob->getParam('hostspec'),
            'port' => $this->_baseob->getParam('port'),
            'username' => $this->_baseob->getParam('username')
        ));

        if (isset($params['debug']) &&
            ($params['debug'] instanceof Horde_Imap_Client_Base_Debug)) {
            $this->_debug = $params['debug'];
            $this->_debug->info(sprintf(
                'CACHE: Using the %s storage driver.',
                get_class($this->_backend)
            ));
        }
    }

    /**
     * Get information from the cache.
     *
     * @param string $mailbox    An IMAP mailbox string.
     * @param array $uids        The list of message UIDs to retrieve
     *                           information for. If empty, returns the list
     *                           of cached UIDs.
     * @param array $fields      An array of fields to retrieve. If empty,
     *                           returns all cached fields.
     * @param integer $uidvalid  The IMAP uidvalidity value of the mailbox.
     *
     * @return array  An array of arrays with the UID of the message as the
     *                key (if found) and the fields as values (will be
     *                undefined if not found). If $uids is empty, returns the
     *                full (unsorted) list of cached UIDs.
     */
    public function get($mailbox, array $uids = array(), $fields = array(),
                        $uidvalid = null)
    {
        $mailbox = strval($mailbox);

        if (empty($uids)) {
            $ret = $this->_backend->getCachedUids($mailbox, $uidvalid);
        } else {
            $ret = $this->_backend->get($mailbox, $uids, $fields, $uidvalid);

            if ($this->_debug && !empty($ret)) {
                $this->_debug->info(sprintf(
                    'CACHE: Retrieved messages (%s [%s; %s])',
                    empty($fields) ? 'ALL' : implode(',', $fields),
                    $mailbox,
                    $this->_baseob->getIdsOb(array_keys($ret))->tostring_sort
                ));
            }
        }

        return $ret;
    }

    /**
     * Store information in cache.
     *
     * @param string $mailbox    An IMAP mailbox string.
     * @param array $data        The list of data to save. The keys are the
     *                           UIDs, the values are an array of information
     *                           to save. If empty, do a check to make sure
     *                           the uidvalidity is still valid.
     * @param integer $uidvalid  The IMAP uidvalidity value of the mailbox.
     */
    public function set($mailbox, $data, $uidvalid)
    {
        $mailbox = strval($mailbox);

        if (empty($data)) {
            $this->_backend->getMetaData($mailbox, $uidvalid, array('uidvalid'));
        } else {
            $this->_backend->set($mailbox, $data, $uidvalid);

            if ($this->_debug) {
                $this->_debug->info(sprintf(
                    'CACHE: Stored messages [%s; %s]',
                    $mailbox,
                    $this->_baseob->getIdsOb(array_keys($data))->tostring_sort
                ));
            }
        }
    }

    /**
     * Get metadata information for a mailbox.
     *
     * @param string $mailbox    An IMAP mailbox string.
     * @param integer $uidvalid  The IMAP uidvalidity value of the mailbox.
     * @param array $entries     An array of entries to return. If empty,
     *                           returns all metadata.
     *
     * @return array  The requested metadata. Requested entries that do not
     *                exist will be undefined. The following entries are
     *                defaults and always present:
     *   - uidvalid: (integer) The UIDVALIDITY of the mailbox.
     */
    public function getMetaData($mailbox, $uidvalid = null,
                                array $entries = array())
    {
        return $this->_backend->getMetaData(strval($mailbox), $uidvalid, $entries);
    }

    /**
     * Set metadata information for a mailbox.
     *
     * @param string $mailbox    An IMAP mailbox string.
     * @param integer $uidvalid  The IMAP uidvalidity value of the mailbox.
     * @param array $data        The list of data to save. The keys are the
     *                           metadata IDs, the values are the associated
     *                           data. The following labels are reserved:
     *                           'uidvalid'.
     */
    public function setMetaData($mailbox, $uidvalid, array $data = array())
    {
        unset($data['uidvalid']);

        if (!empty($data)) {
            if (!empty($uidvalid)) {
                $data['uidvalid'] = $uidvalid;
            }
            $mailbox = strval($mailbox);

            $this->_backend->setMetaData($mailbox, $data);

            if ($this->_debug) {
                $this->_debug->info(sprintf(
                    'CACHE: Stored metadata (%s [%s])',
                    implode(',', array_keys($data)),
                    $mailbox
                ));
            }
        }
    }

    /**
     * Delete messages in the cache.
     *
     * @param string $mailbox  An IMAP mailbox string.
     * @param array $uids      The list of message UIDs to delete.
     */
    public function deleteMsgs($mailbox, $uids)
    {
        if (empty($uids)) {
            return;
        }

        $mailbox = strval($mailbox);

        $this->_backend->deleteMsgs($mailbox, $uids);

        if ($this->_debug) {
            $this->_debug->info(sprintf(
                'CACHE: Deleted messages [%s; %s]',
                $mailbox,
                $this->_baseob->getIdsOb($uids)->tostring_sort
            ));
        }
    }

    /**
     * Delete a mailbox from the cache.
     *
     * @param string $mbox  The mailbox to delete.
     */
    public function deleteMailbox($mbox)
    {
        $mbox = strval($mbox);
        $this->_backend->deleteMailbox($mbox);

        if ($this->_debug) {
            $this->_debug->info(sprintf(
                'CACHE: Deleted mailbox [%s]',
                $mbox
            ));
        }
    }

    /**
     * Clear the cache.
     *
     * @since 2.9.0
     *
     * @param integer $lifetime  Only delete entries older than this (in
     *                           seconds). If null, deletes all entries.
     */
    public function clear($lifetime = null)
    {
        $this->_backend->clear($lifetime);
    }

}
