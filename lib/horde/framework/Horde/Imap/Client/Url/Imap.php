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
 * Object representation of an IMAP (RFC 5092/5593) URL.
 *
 * Absolute IMAP URLs takes one of the following forms:
 *   - imap://<iserver>[/]
 *   - imap://<iserver>/<enc-mailbox>[<uidvalidity>][?<enc-search>]
 *   - imap://<iserver>/<enc-mailbox>[<uidvalidity>]<iuid>[<isection>][<ipartial>][<iurlauth>]
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2008-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 * @since     2.25.0
 *
 * @property Horde_Imap_Client_Mailbox $mailbox  IMAP Mailbox.
 * @property string $partial  Byte range for use with IMAP FETCH command.
 * @property string $search  Search query to be run with IMAP SEARCH.
 * @property string $section  MIME part ID.
 * @property string $uid  IMAP UID.
 * @property string $uidvalidity  IMAP UIDVALIDITY for the mailbox.
 * @property string $urlauth  URLAUTH info.
 */
class Horde_Imap_Client_Url_Imap extends Horde_Imap_Client_Url_Base
{
    /**
     * IMAP mailbox.
     *
     * @var Horde_Imap_Client_Mailbox
     */
    protected $_mailbox;

    /**
     * Byte range for use with IMAP FETCH command.
     *
     * @var string
     */
    protected $_partial;

    /**
     * Search query to be run with IMAP SEARCH.
     *
     * @var string
     */
    protected $_search;

    /**
     * MIME part ID.
     *
     * @var string
     */
    protected $_section;

    /**
     * IMAP UID.
     *
     * @var string
     */
    protected $_uid;

    /**
     * IMAP UIDVALIDITY for the given mailbox.
     *
     * @var integer
     */
    protected $_uidvalidity;

    /**
     * URLAUTH info (not parsed).
     *
     * @var string
     */
    protected $_urlauth;

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'mailbox':
            return $this->_mailbox;

        case 'partial':
        case 'search':
        case 'section':
        case 'uid':
        case 'uidvalidity':
        case 'urlauth':
            return isset($this->{'_' . $name})
                ? $this->{'_' . $name}
                : null;

        case 'port':
            return parent::__get($name) ?: 143;

        default:
            return parent::__get($name);
        }
    }

    /**
     */
    public function __set($name, $value)
    {
        switch ($name) {
        case 'mailbox':
            $this->_mailbox = Horde_Imap_Client_Mailbox::get($value);
            break;

        case 'partial':
        case 'search':
        case 'section':
        case 'uid':
        case 'uidvalidity':
        case 'urlauth':
            $this->{'_' . $name} = $value;
            break;

        default:
            parent::__set($name, $value);
            break;
        }
    }

    /**
     * Create an IMAP URL (RFC 5092/5593).
     *
     * @return string  A URL string.
     */
    public function __toString()
    {
        $url = 'imap://' . parent::__toString();

        if (($port = $this->port) != 143) {
            $url .= ':' . $port;
        }

        return $url . '/' . $this->_toImapString();
    }

    /**
     */
    protected function _toImapString()
    {
        $url = '';

        if ($mbox = $this->mailbox) {
            $url .= rawurlencode($mbox->utf7imap);
        }

        if ($uidvalid = $this->uidvalidity) {
            $url .= ';UIDVALIDITY=' . $uidvalid;
        }

        if ($search = $this->search) {
            $url .= '?' . rawurlencode($search);
        } else {
            if ($uid = $this->uid) {
                $url .= '/;UID=' . $uid;
            }

            if ($section = $this->section) {
                $url .= '/;SECTION=' . $section;
            }

            if ($partial = $this->partial) {
                $url .= '/;PARTIAL=' . $partial;
            }

            if ($urlauth = $this->urlauth) {
                $url .= '/;URLAUTH=' . $urlauth;
            }
        }

        return $url;
    }

    /**
     */
    protected function _parseUrl(array $data)
    {
        if (isset($data['path']) &&
            strlen($path = ltrim($data['path'], '/'))) {
            $parts = explode('/;', $path);

            $mbox = array_shift($parts);
            if (($pos = stripos($mbox, ';UIDVALIDITY=')) !== false) {
                $this->uidvalidity = intval(substr($mbox, $pos + 13));
                $mbox = substr($mbox, 0, $pos);
            }

            if ($mbox[0] === ';') {
                array_unshift($parts, substr($mbox, 1));
            } elseif (strlen($mbox)) {
                $this->_mailbox = Horde_Imap_Client_Mailbox::get(
                    rawurldecode($mbox),
                    true
                );
            }

            if (isset($data['query'])) {
                $this->search = rawurldecode($data['query']);
                $parts = array();
            }
        } else {
            $parts = array();
        }

       if (count($parts)) {
            foreach ($parts as $val) {
                list($k, $v) = explode('=', $val);
                $this->{Horde_String::lower($k)} = $v;
            }
        }
    }

}
