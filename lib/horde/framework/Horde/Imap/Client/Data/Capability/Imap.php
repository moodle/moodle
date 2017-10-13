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
 * Query the capabilities of an IMAP server.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 * @since     2.24.0
 *
 * @property-read integer $cmdlength  Allowable command length (in octets).
 */
class Horde_Imap_Client_Data_Capability_Imap
extends Horde_Imap_Client_Data_Capability
{
    /**
     * The list of enabled extensions.
     *
     * @var array
     */
    protected $_enabled = array();

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'cmdlength':
            /* RFC 2683 [3.2.1.5] originally recommended that lines should
             * be limited to "approximately 1000 octets". However, servers
             * should allow a command line of at least "8000 octets".
             * RFC 7162 [4] updates the recommendation to 8192 octets.
             * As a compromise, assume all modern IMAP servers handle
             * ~2000 octets and, if CONDSTORE/QRESYNC is supported, assume
             * they can handle ~8000 octets. (Don't need dependency support
             * checks here - the simple presence of CONDSTORE/QRESYNC is
             * enough to trigger.) */
             return (isset($this->_data['CONDSTORE']) || isset($this->_data['QRESYNC']))
                 ? 8000
                 : 2000;
        }
    }

    /**
     */
    public function query($capability, $parameter = null)
    {
        if (parent::query($capability, $parameter)) {
            return true;
        }

        switch (Horde_String::upper($capability)) {
        case 'CONDSTORE':
        case 'ENABLE':
            /* RFC 7162 [3.2.3] - QRESYNC implies CONDSTORE and ENABLE. */
            return (is_null($parameter) && $this->query('QRESYNC'));

        case 'UTF8':
            /* RFC 6855 [3] - UTF8=ONLY implies UTF8=ACCEPT. */
            return ((Horde_String::upper($parameter) === 'ACCEPT') &&
                    $this->query('UTF8', 'ONLY'));
        }

        return false;
    }

    /**
     */
    public function isEnabled($capability = null)
    {
        return is_null($capability)
            ? $this->_enabled
            : in_array(Horde_String::upper($capability), $this->_enabled);
    }

    /**
     * Set a capability as enabled/disabled.
     *
     * @param array $capability  A capability (+ parameter).
     * @param boolean $enable    If true, enables the capability.
     */
    public function enable($capability, $enable = true)
    {
        $capability = Horde_String::upper($capability);
        $enabled = $this->isEnabled($capability);

        if ($enable && !$enabled) {
            switch ($capability) {
            case 'QRESYNC':
                /* RFC 7162 [3.2.3] - Enabling QRESYNC also implies enabling
                 * of CONDSTORE. */
                $this->enable('CONDSTORE');
                break;
            }

            $this->_enabled[] = $capability;
            $this->notify();
        } elseif (!$enable && $enabled) {
            $this->_enabled = array_diff($this->_enabled, array($capability));
            $this->notify();
        }
    }

}
