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
 * Query the search charsets available on a server that supports the UTF-8
 * IMAP extension (RFC 6855).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 * @since     2.24.0
 */
class Horde_Imap_Client_Data_SearchCharset_Utf8
extends Horde_Imap_Client_Data_SearchCharset
{
    /**
     * Charset data.
     *
     * @var array
     */
    protected $_charsets = array(
        'US-ASCII' => true,
        'UTF-8' => true
    );

    /**
     */
    public function query($charset, $cached = false)
    {
        return isset($this->_charsets[Horde_String::upper($charset)]);
    }

    /**
     */
    public function setValid($charset, $valid = true)
    {
    }

    /* Serializable methods. */

    /**
     */
    public function serialize()
    {
        return '';
    }

    /**
     */
    public function unserialize($data)
    {
    }

}
