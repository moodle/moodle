<?php
/**
 * Copyright 2008-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2008-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * Object representation of a a POP3 (RFC 2384) or IMAP (RFC 5092/5593) URL.
 *
 * @author     Michael Slusarz <slusarz@horde.org>
 * @category   Horde
 * @copyright  2008-2016 Horde LLC
 * @deprecated Use Horde_Imap_Client_Url_Base instead
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package    Imap_Client
 *
 * @property-read boolean $relative  Is this a relative URL?
 */
class Horde_Imap_Client_Url implements Serializable
{
    /**
     * The authentication method to use.
     *
     * @var string
     */
    public $auth = null;

    /**
     * The remote server (not present for relative URLs).
     *
     * @var string
     */
    public $hostspec = null;

    /**
     * The IMAP mailbox.
     *
     * @todo Make this a Horde_Imap_Client_Mailbox object.
     *
     * @var string
     */
    public $mailbox = null;

    /**
     * A byte range for use with IMAP FETCH.
     *
     * @var string
     */
    public $partial = null;

    /**
     * The remote port (not present for relative URLs).
     *
     * @var integer
     */
    public $port = null;

    /**
     * The protocol type. Either 'imap' or 'pop' (not present for relative
     * URLs).
     *
     * @var string
     */
    public $protocol = null;

    /**
     * A search query to be run with IMAP SEARCH.
     *
     * @var string
     */
    public $search = null;

    /**
     * A MIME part ID.
     *
     * @var string
     */
    public $section = null;

    /**
     * The username to use on the remote server.
     *
     * @var string
     */
    public $username = null;

    /**
     * The IMAP UID.
     *
     * @var string
     */
    public $uid = null;

    /**
     * The IMAP UIDVALIDITY for the given mailbox.
     *
     * @var integer
     */
    public $uidvalidity = null;

    /**
     * URLAUTH info (not parsed).
     *
     * @var string
     */
    public $urlauth = null;

    /**
     * Constructor.
     *
     * Absolute IMAP URLs takes one of the following forms:
     *   - imap://<iserver>[/]
     *   - imap://<iserver>/<enc-mailbox>[<uidvalidity>][?<enc-search>]
     *   - imap://<iserver>/<enc-mailbox>[<uidvalidity>]<iuid>[<isection>][<ipartial>][<iurlauth>]
     *
     * POP URLs take one of the following forms:
     *   - pop://<user>;auth=<auth>@<host>:<port>
     *
     * @param string $url  A URL string.
     */
    public function __construct($url = null)
    {
        if (!is_null($url)) {
            $this->_parse($url);
        }
    }

    /**
     * Create a POP3 (RFC 2384) or IMAP (RFC 5092/5593) URL.
     *
     * @return string  A URL string.
     */
    public function __toString()
    {
        $url = '';

        if (!is_null($this->protocol)) {
            $url = $this->protocol . '://';

            if (!is_null($this->username)) {
                $url .= $this->username;
                if (!is_null($this->auth)) {
                    $url .= ';AUTH=' . $this->auth;
                }
                $url .= '@';
            }

            $url .= $this->hostspec;

            if (!is_null($this->port)) {
                switch ($this->protocol) {
                case 'imap':
                    if ($this->port != 143) {
                        $url .= ':' . $this->port;
                    }
                    break;

                case 'pop':
                    if ($this->port != 110) {
                        $url .= ':' . $this->port;
                    }
                    break;
                }
            }
        }

        $url .= '/';

        if (is_null($this->protocol) || ($this->protocol == 'imap')) {
            $url .= rawurlencode($this->mailbox);

            if (!empty($this->uidvalidity)) {
                $url .= ';UIDVALIDITY=' . $this->uidvalidity;
            }

            if (!is_null($this->search)) {
                $url .= '?' . rawurlencode($this->search);
            } else {
                if (!is_null($this->uid)) {
                    $url .= '/;UID=' . $this->uid;
                }

                if (!is_null($this->section)) {
                    $url .= '/;SECTION=' . $this->section;
                }

                if (!is_null($this->partial)) {
                    $url .= '/;PARTIAL=' . $this->partial;
                }

                if (!is_null($this->urlauth)) {
                    $url .= '/;URLAUTH=' . $this->urlauth;
                }
            }
        }

        return $url;
    }

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'relative':
            return (is_null($this->hostspec) &&
                is_null($this->port) &&
                is_null($this->protocol));
        }
    }

    /**
     */
    protected function _parse($url)
    {
        $data = parse_url(trim($url));

        if (isset($data['scheme'])) {
            $protocol = Horde_String::lower($data['scheme']);
            if (!in_array($protocol, array('imap', 'pop'))) {
                return;
            }

            if (isset($data['host'])) {
                $this->hostspec = $data['host'];
            }
            $this->port = isset($data['port'])
                ? $data['port']
                : (($protocol === 'imap') ? 143 : 110);
            $this->protocol = $protocol;
        }

        /* Check for username/auth information. */
        if (isset($data['user'])) {
            if (($pos = stripos($data['user'], ';AUTH=')) !== false) {
                $auth = substr($data['user'], $pos + 6);
                if ($auth !== '*') {
                    $this->auth = $auth;
                }
                $data['user'] = substr($data['user'], 0, $pos);
            }

            if (strlen($data['user'])) {
                $this->username = $data['user'];
            }
        }

        /* IMAP-only information. */
        if (is_null($this->protocol) || ($this->protocol == 'imap')) {
            if (isset($data['path'])) {
                $data['path'] = ltrim($data['path'], '/');
                $parts = explode('/;', $data['path']);

                $mbox = array_shift($parts);
                if (($pos = stripos($mbox, ';UIDVALIDITY=')) !== false) {
                    $this->uidvalidity = intval(substr($mbox, $pos + 13));
                    $mbox = substr($mbox, 0, $pos);
                }
                $this->mailbox = rawurldecode($mbox);

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
                    $property = Horde_String::lower($k);
                    $this->$property = $v;
                }
            }
        }
    }

    /* Serializable methods. */

    /**
     */
    public function serialize()
    {
        return strval($this);
    }

    /**
     */
    public function unserialize($data)
    {
        $this->_parse($data);
    }

}
