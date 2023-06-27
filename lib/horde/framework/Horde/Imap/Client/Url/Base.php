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
 * Base object representation of a mail server URL.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2008-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 * @since     2.25.0
 *
 * @property string $auth  The authentication method.
 * @property string $host  The server.
 * @property integer $port  The port.
 * @property string $username  The username.
 */
abstract class Horde_Imap_Client_Url_Base implements Serializable
{
    /**
     * The authentication method to use.
     *
     * @var string
     */
    protected $_auth = null;

    /**
     * The server name.
     *
     * @var string
     */
    protected $_host = null;

    /**
     * The port.
     *
     * @var integer
     */
    protected $_port = null;

    /**
     * The username.
     *
     * @var string
     */
    protected $_username = null;

    /**
     * Constructor.
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
     */
    public function __get($name)
    {
        switch ($name) {
        case 'auth':
        case 'host':
        case 'port':
        case 'username':
            return $this->{'_' . $name};
        }
    }

    /**
     */
    public function __set($name, $value)
    {
        switch ($name) {
        case 'auth':
        case 'host':
        case 'port':
        case 'username':
            $this->{'_' . $name} = $value;
            break;
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

        if (!is_null($this->username)) {
            $url .= $this->username;
            if (!is_null($this->auth)) {
                $url .= ';AUTH=' . $this->auth;
            }
            $url .= '@';
        }

        $url .= $this->host;

        return $url;
    }

    /**
     */
    protected function _parse($url)
    {
        $data = parse_url(trim($url));

        if (isset($data['scheme'])) {
            if (isset($data['host'])) {
                $this->host = $data['host'];
            }
            if (isset($data['port'])) {
                $this->port = $data['port'];
            }
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

        $this->_parseUrl($data);
    }

    /**
     */
    abstract protected function _parseUrl(array $data);

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
