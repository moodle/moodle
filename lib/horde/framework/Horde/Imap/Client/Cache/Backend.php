<?php
/**
 * Copyright 2013-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2013-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * The abstract backend class for storing IMAP cached data.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2013-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */
abstract class Horde_Imap_Client_Cache_Backend implements Serializable
{
    /**
     * Configuration paramters.
     * Values set by the base Cache object: hostspec, port, username
     *
     * @var array
     */
    protected $_params = array();

    /**
     * Constructor.
     *
     * @param array $params  Configuration parameters.
     */
    public function __construct(array $params = array())
    {
        $this->setParams($params);
        $this->_initOb();
    }

    /**
     * Initialization tasks.
     */
    protected function _initOb()
    {
    }

    /**
     * Add configuration parameters.
     *
     * @param array $params  Configuration parameters.
     */
    public function setParams(array $params = array())
    {
        $this->_params = array_merge($this->_params, $params);
    }

    /**
     * Get information from the cache for a set of UIDs.
     *
     * @param string $mailbox    An IMAP mailbox string.
     * @param array $uids        The list of message UIDs to retrieve
     *                           information for.
     * @param array $fields      An array of fields to retrieve. If empty,
     *                           returns all cached fields.
     * @param integer $uidvalid  The IMAP uidvalidity value of the mailbox.
     *
     * @return array  An array of arrays with the UID of the message as the
     *                key (if found) and the fields as values (will be
     *                undefined if not found).
     */
    abstract public function get($mailbox, $uids, $fields, $uidvalid);

    /**
     * Get the list of cached UIDs.
     *
     * @param string $mailbox    An IMAP mailbox string.
     * @param integer $uidvalid  The IMAP uidvalidity value of the mailbox.
     *
     * @return array  The (unsorted) list of cached UIDs.
     */
    abstract public function getCachedUids($mailbox, $uidvalid);

    /**
     * Store data in cache.
     *
     * @param string $mailbox    An IMAP mailbox string.
     * @param array $data        The list of data to save. The keys are the
     *                           UIDs, the values are an array of information
     *                           to save.
     * @param integer $uidvalid  The IMAP uidvalidity value of the mailbox.
     */
    abstract public function set($mailbox, $data, $uidvalid);

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
    abstract public function getMetaData($mailbox, $uidvalid, $entries);

    /**
     * Set metadata information for a mailbox.
     *
     * @param string $mailbox    An IMAP mailbox string.
     * @param array $data        The list of data to save. The keys are the
     *                           metadata IDs, the values are the associated
     *                           data. (If present, uidvalidity appears as
     *                           the 'uidvalid' key in $data.)
     */
    abstract public function setMetaData($mailbox, $data);

    /**
     * Delete messages in the cache.
     *
     * @param string $mailbox  An IMAP mailbox string.
     * @param array $uids      The list of message UIDs to delete.
     */
    abstract public function deleteMsgs($mailbox, $uids);

    /**
     * Delete a mailbox from the cache.
     *
     * @param string $mailbox  The mailbox to delete.
     */
    abstract public function deleteMailbox($mailbox);

    /**
     * Clear the cache.
     *
     * @param integer $lifetime  Only delete entries older than this (in
     *                           seconds). If null, deletes all entries.
     */
    abstract public function clear($lifetime);


    /* Serializable methods. */

    /**
     */
    public function serialize()
    {
        return serialize($this->_params);
    }

    /**
     */
    public function unserialize($data)
    {
        $this->_params = unserialize($data);
        $this->_initOb();
    }

}
