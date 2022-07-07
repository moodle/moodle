<?php
/**
 * Copyright 2014-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * Object representation of a a POP3 (RFC 2384) URL.
 *
 * POP3 URLs take one of the following forms:
 *   - pop://<user>;auth=<auth>@<host>:<port>
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 * @since     2.25.0
 */
class Horde_Imap_Client_Url_Pop3 extends Horde_Imap_Client_Url_Base
{
    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'port':
            return parent::__get($name) ?: 110;

        default:
            return parent::__get($name);
        }
    }

    /**
     * Create a POP3 URL (RFC 2384).
     *
     * @return string  A URL string.
     */
    public function __toString()
    {
        $url = 'pop://' . parent::__toString();

        if (($port = $this->port) != 110) {
            $url .= ':' . $port;
        }

        return $url . '/';
    }

    /**
     */
    protected function _parseUrl(array $data)
    {
    }

}
