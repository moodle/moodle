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
 * Object representation of an IMAP mailbox string allowed when UTF8=ACCEPT
 * is supported/enabled on the server (RFC 6855 [3]).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */
class Horde_Imap_Client_Data_Format_Mailbox_Utf8
extends Horde_Imap_Client_Data_Format_Mailbox
implements Horde_Imap_Client_Data_Format_String_Support_Nonascii
{
    /**
     */
    protected $_encoding = 'utf8';

    /**
     */
    public function __construct($data)
    {
        parent::__construct($data);

        /* RFC 3501 allows any US-ASCII character, except null (\0), in
         * mailbox data.
         * RFC 6855 [3] institutes additional limitations on valid mailbox
         * characters, to comply with RFC 5198 [2] (Net-Unicode Definition):
         * "MUST NOT contain control characters (U+0000-U+001F and
         * U+0080-U+009F), a delete character (U+007F), a line separator
         * (U+2028), or a paragraph separator (U+2029)." */
        if ($this->quoted() &&
            preg_match('/[\x00-\x1f\x7f\x80-\x9f\x{2028}\x{2029}]/u', strval($this))) {
            throw new Horde_Imap_Client_Data_Format_Exception(
                'Invalid character found in mailbox data.'
            );
        }

        if ($this->literal()) {
            $this->forceQuoted();
        }
    }

}
