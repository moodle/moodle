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
 * Fetch query object for use with Horde_Imap_Client_Base#fetch().
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2011-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */
class Horde_Imap_Client_Fetch_Query implements ArrayAccess, Countable, Iterator
{
    /**
     * Internal data array.
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Get the full text of the message.
     *
     * @param array $opts  The following options are available:
     *   - length: (integer) The length of the substring to return.
     *             DEFAULT: The entire text is returned.
     *   - peek: (boolean) If set, does not set the '\Seen' flag on the
     *            message.
     *            DEFAULT: The seen flag is set.
     *   - start: (integer) If a portion of the full text is desired to be
     *            returned, the starting position is identified here.
     *            DEFAULT: The entire text is returned.
     */
    public function fullText(array $opts = array())
    {
        $this->_data[Horde_Imap_Client::FETCH_FULLMSG] = $opts;
    }

    /**
     * Return header text.
     *
     * Header text is defined only for the base RFC 2822 message or
     * message/rfc822 parts.
     *
     * @param array $opts  The following options are available:
     *   - id: (string) The MIME ID to obtain the header text for.
     *         DEFAULT: The header text for the base message will be
     *         returned.
     *   - length: (integer) The length of the substring to return.
     *             DEFAULT: The entire text is returned.
     *   - peek: (boolean) If set, does not set the '\Seen' flag on the
     *           message.
     *           DEFAULT: The seen flag is set.
     *   - start: (integer) If a portion of the full text is desired to be
     *            returned, the starting position is identified here.
     *            DEFAULT: The entire text is returned.
     */
    public function headerText(array $opts = array())
    {
        $id = isset($opts['id'])
            ? $opts['id']
            : 0;
        $this->_data[Horde_Imap_Client::FETCH_HEADERTEXT][$id] = $opts;
    }

    /**
     * Return body text.
     *
     * Body text is defined only for the base RFC 2822 message or
     * message/rfc822 parts.
     *
     * @param array $opts  The following options are available:
     *   - id: (string) The MIME ID to obtain the body text for.
     *         DEFAULT: The body text for the entire message will be
     *         returned.
     *   - length: (integer) The length of the substring to return.
     *             DEFAULT: The entire text is returned.
     *   - peek: (boolean) If set, does not set the '\Seen' flag on the
     *           message.
     *           DEFAULT: The seen flag is set.
     *   - start: (integer) If a portion of the full text is desired to be
     *            returned, the starting position is identified here.
     *            DEFAULT: The entire text is returned.
     */
    public function bodyText(array $opts = array())
    {
        $id = isset($opts['id'])
            ? $opts['id']
            : 0;
        $this->_data[Horde_Imap_Client::FETCH_BODYTEXT][$id] = $opts;
    }

    /**
     * Return MIME header text.
     *
     * MIME header text is defined only for non-RFC 2822 messages and
     * non-message/rfc822 parts.
     *
     * @param string $id   The MIME ID to obtain the MIME header text for.
     * @param array $opts  The following options are available:
     *   - length: (integer) The length of the substring to return.
     *             DEFAULT: The entire text is returned.
     *   - peek: (boolean) If set, does not set the '\Seen' flag on the
     *           message.
     *           DEFAULT: The seen flag is set.
     *   - start: (integer) If a portion of the full text is desired to be
     *            returned, the starting position is identified here.
     *            DEFAULT: The entire text is returned.
     */
    public function mimeHeader($id, array $opts = array())
    {
        $this->_data[Horde_Imap_Client::FETCH_MIMEHEADER][$id] = $opts;
    }

    /**
     * Return the body part data for a MIME ID.
     *
     * @param string $id   The MIME ID to obtain the body part text for.
     * @param array $opts  The following options are available:
     *   - decode: (boolean) Attempt to server-side decode the bodypart data
     *             if it is MIME transfer encoded.
     *             DEFAULT: false
     *   - length: (integer) The length of the substring to return.
     *             DEFAULT: The entire text is returned.
     *   - peek: (boolean) If set, does not set the '\Seen' flag on the
     *           message.
     *           DEFAULT: The seen flag is set.
     *   - start: (integer) If a portion of the full text is desired to be
     *            returned, the starting position is identified here.
     *            DEFAULT: The entire text is returned.
     */
    public function bodyPart($id, array $opts = array())
    {
        $this->_data[Horde_Imap_Client::FETCH_BODYPART][$id] = $opts;
    }

    /**
     * Returns the decoded body part size for a MIME ID.
     *
     * @param string $id  The MIME ID to obtain the decoded body part size
     *                    for.
     */
    public function bodyPartSize($id)
    {
        $this->_data[Horde_Imap_Client::FETCH_BODYPARTSIZE][$id] = true;
    }

    /**
     * Returns RFC 2822 header text that matches a search string.
     *
     * This header search work only with the base RFC 2822 message or
     * message/rfc822 parts.
     *
     * @param string $label  A unique label associated with this particular
     *                       search. This is how the results are stored.
     * @param array $search  The search string(s) (case-insensitive).
     * @param array $opts    The following options are available:
     *   - cache: (boolean) If true, and 'peek' is also true, will cache
     *            the result of this call.
     *            DEFAULT: false
     *   - id: (string) The MIME ID to search.
     *         DEFAULT: The base message part
     *   - length: (integer) The length of the substring to return.
     *             DEFAULT: The entire text is returned.
     *   - notsearch: (boolean) Do a 'NOT' search on the headers.
     *                DEFAULT: false
     *   - peek: (boolean) If set, does not set the '\Seen' flag on the
     *           message.
     *           DEFAULT: The seen flag is set.
     *   - start: (integer) If a portion of the full text is desired to be
     *            returned, the starting position is identified here.
     *            DEFAULT: The entire text is returned.
     */
    public function headers($label, $search, array $opts = array())
    {
        $this->_data[Horde_Imap_Client::FETCH_HEADERS][$label] = array_merge(
            $opts,
            array(
                'headers' => array_map('strval', $search)
            )
        );
    }

    /**
     * Return MIME structure information.
     */
    public function structure()
    {
        $this->_data[Horde_Imap_Client::FETCH_STRUCTURE] = true;
    }

    /**
     * Return envelope header data.
     */
    public function envelope()
    {
        $this->_data[Horde_Imap_Client::FETCH_ENVELOPE] = true;
    }

    /**
     * Return flags set for the message.
     */
    public function flags()
    {
        $this->_data[Horde_Imap_Client::FETCH_FLAGS] = true;
    }

    /**
     * Return the internal (IMAP) date of the message.
     */
    public function imapDate()
    {
        $this->_data[Horde_Imap_Client::FETCH_IMAPDATE] = true;
    }

    /**
     * Return the size (in bytes) of the message.
     */
    public function size()
    {
        $this->_data[Horde_Imap_Client::FETCH_SIZE] = true;
    }

    /**
     * Return the unique ID of the message.
     */
    public function uid()
    {
        $this->_data[Horde_Imap_Client::FETCH_UID] = true;
    }

    /**
     * Return the sequence number of the message.
     */
    public function seq()
    {
        $this->_data[Horde_Imap_Client::FETCH_SEQ] = true;
    }

    /**
     * Return the mod-sequence value for the message.
     *
     * The server must support the CONDSTORE IMAP extension, and the mailbox
     * must support mod-sequences.
     */
    public function modseq()
    {
        $this->_data[Horde_Imap_Client::FETCH_MODSEQ] = true;
    }

    /**
     * Does the query contain the given criteria?
     *
     * @param integer $criteria  The criteria to remove.
     *
     * @return boolean  True if the query contains the given criteria.
     */
    public function contains($criteria)
    {
        return isset($this->_data[$criteria]);
    }

    /**
     * Remove an entry under a given criteria.
     *
     * @param integer $criteria  Criteria ID.
     * @param string $key        The key to remove.
     */
    public function remove($criteria, $key)
    {
        if (isset($this->_data[$criteria]) &&
            is_array($this->_data[$criteria])) {
            unset($this->_data[$criteria][$key]);
            if (empty($this->_data[$criteria])) {
                unset($this->_data[$criteria]);
            }
        }
    }

    /**
     * Returns a hash of the current query object.
     *
     * @return string  Hash.
     */
    public function hash()
    {
        return hash('md5', serialize($this));
    }

    /* ArrayAccess methods. */

    /**
     */
    #[ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    /**
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return isset($this->_data[$offset])
            ? $this->_data[$offset]
            : null;
    }

    /**
     */
    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->_data[$offset] = $value;
    }

    /**
     */
    #[ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }

    /* Countable methods. */

    /**
     */
    #[ReturnTypeWillChange]
    public function count()
    {
        return count($this->_data);
    }

    /* Iterator methods. */

    /**
     */
    #[ReturnTypeWillChange]
    public function current()
    {
        $opts = current($this->_data);

        return (!empty($opts) && ($this->key() == Horde_Imap_Client::FETCH_BODYPARTSIZE))
            ? array_keys($opts)
            : $opts;
    }

    /**
     */
    #[ReturnTypeWillChange]
    public function key()
    {
        return key($this->_data);
    }

    /**
     */
    #[ReturnTypeWillChange]
    public function next()
    {
        next($this->_data);
    }

    /**
     */
    #[ReturnTypeWillChange]
    public function rewind()
    {
        reset($this->_data);
    }

    /**
     */
    #[ReturnTypeWillChange]
    public function valid()
    {
        return !is_null($this->key());
    }

}
