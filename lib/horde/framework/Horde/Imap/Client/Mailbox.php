<?php
/**
 * Copyright 2011-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2011-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * An object that provides a way to switch between UTF7-IMAP and
 * human-readable representations of a mailbox name.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2011-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 *
 * @property-read string $list_escape  Escapes mailbox for use in LIST
 *                                     command (UTF-8).
 * @property-read string $utf7imap  Mailbox in UTF7-IMAP.
 * @property-read string $utf8  Mailbox in UTF-8.
 */
class Horde_Imap_Client_Mailbox implements Serializable
{
    /**
     * UTF7-IMAP representation of mailbox.
     * If boolean true, it is identical to UTF-8 representation.
     *
     * @var mixed
     */
    protected $_utf7imap;

    /**
     * UTF8 representation of mailbox.
     *
     * @var string
     */
    protected $_utf8;

    /**
     * Shortcut to obtaining mailbox object.
     *
     * @param string $mbox       The mailbox name.
     * @param boolean $utf7imap  Is mailbox UTF7-IMAP encoded? Otherwise,
     *                           mailbox is assumed to be UTF-8.
     *
     * @return Horde_Imap_Client_Mailbox  A mailbox object.
     */
    public static function get($mbox, $utf7imap = false)
    {
        return ($mbox instanceof Horde_Imap_Client_Mailbox)
            ? $mbox
            : new Horde_Imap_Client_Mailbox($mbox, $utf7imap);
    }

    /**
     * Constructor.
     *
     * @param string $mbox       The mailbox name.
     * @param boolean $utf7imap  Is mailbox UTF7-IMAP encoded (true).
     *                           Otherwise, mailbox is assumed to be UTF-8
     *                           encoded.
     */
    public function __construct($mbox, $utf7imap = false)
    {
        if (strcasecmp($mbox, 'INBOX') === 0) {
            $mbox = 'INBOX';
        }

        if ($utf7imap) {
            $this->_utf7imap = $mbox;
        } else {
            $this->_utf8 = $mbox;
        }
    }

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'list_escape':
            return preg_replace("/\*+/", '%', $this->utf8);

        case 'utf7imap':
            if (!isset($this->_utf7imap)) {
                $n = Horde_Imap_Client_Utf7imap::Utf8ToUtf7Imap($this->_utf8);
                $this->_utf7imap = ($n == $this->_utf8)
                    ? true
                    : $n;
            }

            return ($this->_utf7imap === true)
                ? $this->_utf8
                : $this->_utf7imap;

        case 'utf8':
            if (!isset($this->_utf8)) {
                $this->_utf8 = Horde_Imap_Client_Utf7imap::Utf7ImapToUtf8($this->_utf7imap);
                if ($this->_utf8 == $this->_utf7imap) {
                    $this->_utf7imap = true;
                }
            }
            return (string)$this->_utf8;
        }
    }

    /**
     */
    public function __toString()
    {
        return $this->utf8;
    }

    /**
     * Compares this mailbox to another mailbox string.
     *
     * @return boolean  True if the items are equal.
     */
    public function equals($mbox)
    {
        return ($this->utf8 == $mbox);
    }

    /* Serializable methods. */

    /**
     */
    public function serialize()
    {
        return json_encode(array($this->_utf7imap, $this->_utf8));
    }

    /**
     */
    public function unserialize($data)
    {
        list($this->_utf7imap, $this->_utf8) = json_decode($data, true);
    }

}
