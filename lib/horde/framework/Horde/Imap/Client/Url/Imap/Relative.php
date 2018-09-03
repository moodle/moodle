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
 * Object representation of a relative IMAP (RFC 5092/5593) URL.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2008-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 * @since     2.25.0
 */
class Horde_Imap_Client_Url_Imap_Relative
extends Horde_Imap_Client_Url_Imap
{
    /**
     * Create a relative IMAP URL (RFC 5092/5593).
     *
     * @return string  A URL string.
     */
    public function __toString()
    {
        if ($out = $this->_toImapString()) {
            if (substr($out, 0, 2) === '/;') {
                $out = substr($out, 1);
            } elseif ($out[0] !== ';') {
                $out = '/' . $out;
            }
        }

        return $out;
    }

}
