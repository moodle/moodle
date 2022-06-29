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
 * @package   Mime
 */

/**
 * This class represents address fields (RFC 5322).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 * @since     2.5.0
 */
class Horde_Mime_Headers_Addresses
extends Horde_Mime_Headers_Element_Single
implements Horde_Mime_Headers_Element_Address
{
    /**
     * By default, if more than 1 address header is found, the addresses are
     * appended together into a single field.  Set this value to false to
     * ignore all but the *last* header.
     *
     * @var boolean
     */
    public $append_addr = true;

    /**
     */
    public function __clone()
    {
        $this->_values = clone $this->_values;
    }

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'full_value':
        case 'value':
        case 'value_single':
            return strval($this->_values);
        }

        return parent::__get($name);
    }

    /**
     */
    public function getAddressList($first = false)
    {
        return $first
            ? $this->_values
            : array($this->_values);
    }

    /**
     *
     * @throws Horde_Mime_Exception
     */
    protected function _setValue($value)
    {
        /* @todo Implement with traits */
        $rfc822 = new Horde_Mail_Rfc822();

        try {
            $addr_list = $rfc822->parseAddressList($value);
        } catch (Horde_Mail_Exception $e) {
            throw new Horde_Mime_Exception($e);
        }

        foreach ($addr_list as $ob) {
            if ($ob instanceof Horde_Mail_Rfc822_Group) {
                $ob->groupname = $this->_sanityCheck($ob->groupname);
            } else {
                $ob->personal = $this->_sanityCheck($ob->personal);
            }
        }

        switch (Horde_String::lower($this->name)) {
        case 'bcc':
        case 'cc':
        case 'from':
        case 'to':
            /* Catch malformed undisclosed-recipients entries. */
            if ((count($addr_list) == 1) &&
                preg_match("/^\s*undisclosed-recipients:?\s*$/i", $addr_list[0]->bare_address)) {
                $addr_list = new Horde_Mail_Rfc822_List(
                    'undisclosed-recipients:;'
                );
            }
            break;
        }

        if ($this->append_addr && $this->_values) {
            $this->_values->add($addr_list);
        } else {
            $this->_values = $addr_list;
        }
    }

    /**
     */
    public static function getHandles()
    {
        return array(
            // Mail: RFC 3798
            'disposition-notification-to',
            // Mail: RFC 5322 (Address)
            'from',
            'to',
            'cc',
            'bcc',
            'reply-to',
            'sender'
        );
    }

    /**
     * @param array $opts  See doSendEncode().
     */
    protected function _sendEncode($opts)
    {
        return self::doSendEncode($this->getAddressList(), $opts);
    }

    /**
     * Do send encoding for addresses.
     *
     * Needed as a static function because it is used by both single and
     * multiple address headers.
     *
     * @todo  Implement with traits.
     *
     * @param array $alist  An array of Horde_Mail_Rfc822_List objects.
     * @param array $opts   Additional options:
     *   - charset: (string) Encodes the headers using this charset.
     *              DEFAULT: UTF-8
     *   - defserver: (string) The default domain to append to mailboxes.
     *                DEFAULT: No default name.
     *   - idn: (boolean)  Encode IDN domain names (RFC 3490) if true.
     *           DEFAULT: true
     */
    public static function doSendEncode($alist, array $opts = array())
    {
        $out = array();
        $opts = array_merge(array('idn' => true), $opts);
        foreach ($alist as $ob) {
            if (!empty($opts['defserver'])) {
                foreach ($ob->raw_addresses as $ob2) {
                    if (is_null($ob2->host)) {
                        $ob2->host = $opts['defserver'];
                    }
                }
            }

            $out[] = $ob->writeAddress(array(
                'encode' => empty($opts['charset']) ? null : $opts['charset'],
                'idn' => $opts['idn']
            ));
        }

        return $out;
    }

}
