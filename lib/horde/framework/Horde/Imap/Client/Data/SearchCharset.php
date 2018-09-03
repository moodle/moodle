<?php
/**
 * Copyright 2014-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * Query the search charsets available on a server.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 * @since     2.24.0
 *
 * @property-read array $charsets  The list of valid charsets that have been
 *                                 discovered on the server.
 */
class Horde_Imap_Client_Data_SearchCharset
implements Serializable, SplSubject
{
    /**
     * Base client object.
     *
     * @var Horde_Imap_Client_Base
     */
    protected $_baseob;

    /**
     * Charset data.
     *
     * @var array
     */
    protected $_charsets = array(
        'US-ASCII' => true
    );

    /**
     * Observers.
     *
     * @var array
     */
    protected $_observers = array();

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'charsets':
            return array_keys(array_filter($this->_charsets));
        }
    }

    /**
     */
    public function setBaseOb(Horde_Imap_Client_Base $ob)
    {
        $this->_baseob = $ob;
    }

    /**
     * Query the validity of a charset.
     *
     * @param string $charset  The charset to query.
     * @param boolean $cached  If true, only query cached values.
     *
     * @return boolean  True if the charset is valid for searching.
     */
    public function query($charset, $cached = false)
    {
        $charset = Horde_String::upper($charset);

        if (isset($this->_charsets[$charset])) {
            return $this->_charsets[$charset];
        } elseif ($cached) {
            return null;
        }

        if (!$this->_baseob) {
            throw new RuntimeException(
                'Base object needs to be defined to query for charset.'
            );
        }

        /* Use a dummy search query and search for BADCHARSET response. */
        $query = new Horde_Imap_Client_Search_Query();
        $query->charset($charset, false);
        $query->ids($this->_baseob->getIdsOb(1, true));
        $query->text('a');
        try {
            $this->_baseob->search('INBOX', $query, array(
                'nocache' => true,
                'sequence' => true
            ));
            $this->_charsets[$charset] = true;
        } catch (Horde_Imap_Client_Exception $e) {
            $this->_charsets[$charset] = ($e->getCode() !== Horde_Imap_Client_Exception::BADCHARSET);
        }

        $this->notify();

        return $this->_charsets[$charset];
    }

    /**
     * Set the validity of a given charset.
     *
     * @param string $charset  The charset.
     * @param boolean $valid   Is charset valid?
     */
    public function setValid($charset, $valid = true)
    {
        $charset = Horde_String::upper($charset);
        $valid = (bool)$valid;

        if (!isset($this->_charsets[$charset]) ||
            ($this->_charsets[$charset] !== $valid)) {
            $this->_charsets[$charset] = $valid;
            $this->notify();
        }
    }

    /* SplSubject methods. */

    /**
     */
    public function attach(SplObserver $observer)
    {
        $this->detach($observer);
        $this->_observers[] = $observer;
    }

    /**
     */
    public function detach(SplObserver $observer)
    {
        if (($key = array_search($observer, $this->_observers, true)) !== false) {
            unset($this->_observers[$key]);
        }
    }

    /**
     * Notification is triggered internally whenever the object's internal
     * data storage is altered.
     */
    public function notify()
    {
        foreach ($this->_observers as $val) {
            $val->update($this);
        }
    }

    /* Serializable methods. */

    /**
     */
    public function serialize()
    {
        return json_encode($this->_charsets);
    }

    /**
     */
    public function unserialize($data)
    {
        $this->_charsets = json_decode($data, true);
    }

}
