<?php
/**
 * Copyright 2012-2014 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (BSD). If you
 * did not receive this file, see http://www.horde.org/licenses/bsd.
 *
 * @category  Horde
 * @copyright 2012-2014 Horde LLC
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 */

/**
 * Object representation of a RFC 822 e-mail address.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2012-2014 Horde LLC
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 *
 * @property string $groupname  Groupname (UTF-8).
 * @property-read string $groupname_encoded  MIME encoded groupname (UTF-8).
 * @property-read string $label  The shorthand label for this group.
 * @property-read boolean $valid  Returns true if there is enough information
 *                                in object to create a valid address.
 */
class Horde_Mail_Rfc822_Group extends Horde_Mail_Rfc822_Object implements Countable
{
    /**
     * List of group e-mail address objects.
     *
     * @var Horde_Mail_Rfc822_GroupList
     */
    public $addresses;

    /**
     * Group name (MIME decoded).
     *
     * @var string
     */
    protected $_groupname = 'Group';

    /**
     * Constructor.
     *
     * @param string $groupname  If set, used as the group name.
     * @param mixed $addresses   If a GroupList object, used as the address
     *                           list. Any other non-null value is parsed and
     *                           used as the address list (addresses not
     *                           verified; sub-groups are ignored).
     */
    public function __construct($groupname = null, $addresses = null)
    {
        if (!is_null($groupname)) {
            $this->groupname = $groupname;
        }

        if (is_null($addresses)) {
            $this->addresses = new Horde_Mail_Rfc822_GroupList();
        } elseif ($addresses instanceof Horde_Mail_Rfc822_GroupList) {
            $this->addresses = clone $addresses;
        } else {
            $rfc822 = new Horde_Mail_Rfc822();
            $this->addresses = $rfc822->parseAddressList($addresses, array(
                'group' => true
            ));
        }
    }

    /**
     */
    public function __set($name, $value)
    {
        switch ($name) {
        case 'groupname':
            $this->_groupname = Horde_Mime::decode($value);
            break;
        }
    }

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'groupname':
        case 'label':
            return $this->_groupname;

        case 'groupname_encoded':
            return Horde_Mime::encode($this->_groupname);

        case 'valid':
            return (bool)strlen($this->_groupname);

        default:
            return null;
        }
    }

    /**
     */
    protected function _writeAddress($opts)
    {
        $addr = $this->addresses->writeAddress($opts);
        $groupname = $this->groupname;
        if (!empty($opts['encode'])) {
            $groupname = Horde_Mime::encode($groupname, $opts['encode']);
        }

        $rfc822 = new Horde_Mail_Rfc822();

        return $rfc822->encode($groupname, 'personal') . ':' .
            (strlen($addr) ? (' ' . $addr) : '') . ';';
    }

    /**
     */
    public function match($ob)
    {
        return $this->addresses->match($ob);
    }

    /* Countable methods. */

    /**
     * Address count.
     *
     * @return integer  The number of addresses.
     */
    public function count()
    {
        return count($this->addresses);
    }

}
