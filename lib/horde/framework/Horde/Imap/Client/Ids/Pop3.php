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
 * Wrapper around Ids object that correctly handles POP3 UID strings.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2011-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */
class Horde_Imap_Client_Ids_Pop3 extends Horde_Imap_Client_Ids
{
    /**
     */
    protected function _sort(&$ids)
    {
        /* There is no guarantee of POP3 UIDL order - IDs need to be unique,
         * but there is no requirement they need be incrementing. RFC
         * 1939[7] */
    }

    /**
     * Create a POP3 message sequence string.
     *
     * Index Format: UID1[SPACE]UID2...
     *
     * @param boolean $sort  Not used in this class.
     *
     * @return string  The POP3 message sequence string.
     */
    protected function _toSequenceString($sort = true)
    {
        /* $sort is ignored - see _sort(). */

        /* Use space as delimiter as it is the only printable ASCII character
         * that is not allowed as part of the UID (RFC 1939 [7]). */
        return implode(' ', count($this->_ids) > 25000 ? array_unique($this->_ids) : array_keys(array_flip($this->_ids)));
    }

    /**
     * Parse a POP3 message sequence string into a list of indices.
     *
     * @param string $str  The POP3 message sequence string.
     *
     * @return array  An array of UIDs.
     */
    protected function _fromSequenceString($str)
    {
        return explode(' ', trim($str));
    }

}
