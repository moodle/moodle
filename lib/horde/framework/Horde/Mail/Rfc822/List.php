<?php
/**
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (BSD). If you
 * did not receive this file, see http://www.horde.org/licenses/bsd.
 *
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 */

/**
 * Container object for a collection of RFC 822 elements.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 *
 * @property-read array $addresses  The list of all addresses (address
 *                                  w/personal parts).
 * @property-read array $bare_addresses  The list of all addresses (mail@host).
 * @property-read array $bare_addresses_idn  The list of all addresses
 *                                           (mail@host; IDN encoded).
 *                                           (@since 2.1.0)
 * @property-read array $base_addresses  The list of ONLY base addresses
 *                                       (Address objects).
 * @property-read array $raw_addresses  The list of all addresses (Address
 *                                      objects).
 */
class Horde_Mail_Rfc822_List
    extends Horde_Mail_Rfc822_Object
    implements ArrayAccess, Countable, SeekableIterator, Serializable
{
    /** Filter masks. */
    const HIDE_GROUPS = 1;
    const BASE_ELEMENTS = 2;

    /**
     * List data.
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Current Iterator filter.
     *
     * @var array
     */
    protected $_filter = array();

    /**
     * Current Iterator pointer.
     *
     * @var array
     */
    protected $_ptr;

    /**
     * Constructor.
     *
     * @param mixed $obs  Address data to store in this object.
     */
    public function __construct($obs = null)
    {
        if (!is_null($obs)) {
            $this->add($obs);
        }
    }

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'addresses':
        case 'bare_addresses':
        case 'bare_addresses_idn':
        case 'base_addresses':
        case 'raw_addresses':
            $old = $this->_filter;
            $mask = ($name == 'base_addresses')
                ? self::BASE_ELEMENTS
                : self::HIDE_GROUPS;
            $this->setIteratorFilter($mask, empty($old['filter']) ? null : $old['filter']);

            $out = array();
            foreach ($this as $val) {
                switch ($name) {
                case 'addresses':
                    $out[] = strval($val);
                    break;

                case 'bare_addresses':
                    $out[] = $val->bare_address;
                    break;

                case 'bare_addresses_idn':
                    $out[] = $val->bare_address_idn;
                    break;

                case 'base_addresses':
                case 'raw_addresses':
                    $out[] = clone $val;
                    break;
                }
            }

            $this->_filter = $old;
            return $out;
        }
    }

    /**
     * Add objects to the container.
     *
     * @param mixed $obs  Address data to store in this object.
     */
    public function add($obs)
    {
        foreach ($this->_normalize($obs) as $val) {
            $this->_data[] = $val;
        }
    }

    /**
     * Remove addresses from the container. This method ignores Group objects.
     *
     * @param mixed $obs  Addresses to remove.
     */
    public function remove($obs)
    {
        $old = $this->_filter;
        $this->setIteratorFilter(self::HIDE_GROUPS | self::BASE_ELEMENTS);

        foreach ($this->_normalize($obs) as $val) {
            $remove = array();

            foreach ($this as $key => $val2) {
                if ($val2->match($val)) {
                    $remove[] = $key;
                }
            }

            foreach (array_reverse($remove) as $key) {
                unset($this[$key]);
            }
        }

        $this->_filter = $old;
    }

    /**
     * Removes duplicate addresses from list. This method ignores Group
     * objects.
     */
    public function unique()
    {
        $exist = $remove = array();

        $old = $this->_filter;
        $this->setIteratorFilter(self::HIDE_GROUPS | self::BASE_ELEMENTS);

        // For duplicates, we use the first address that contains personal
        // information.
        foreach ($this as $key => $val) {
            $bare = $val->bare_address;
            if (isset($exist[$bare])) {
                if (($exist[$bare] == -1) || is_null($val->personal)) {
                    $remove[] = $key;
                } else {
                    $remove[] = $exist[$bare];
                    $exist[$bare] = -1;
                }
            } else {
                $exist[$bare] = is_null($val->personal)
                    ? $key
                    : -1;
            }
        }

        foreach (array_reverse($remove) as $key) {
            unset($this[$key]);
        }

        $this->_filter = $old;
    }

    /**
     * Group count.
     *
     * @return integer  The number of groups in the list.
     */
    public function groupCount()
    {
        $ret = 0;

        foreach ($this->_data as $val) {
            if ($val instanceof Horde_Mail_Rfc822_Group) {
                ++$ret;
            }
        }

        return $ret;
    }

    /**
     * Set the Iterator filter.
     *
     * @param integer $mask  Filter masks.
     * @param mixed $filter  An e-mail, or as list of e-mails, to filter by.
     */
    public function setIteratorFilter($mask = 0, $filter = null)
    {
        $this->_filter = array();

        if ($mask) {
            $this->_filter['mask'] = $mask;
        }

        if (!is_null($filter)) {
            $rfc822 = new Horde_Mail_Rfc822();
            $this->_filter['filter'] = $rfc822->parseAddressList($filter);
        }
    }

    /**
     */
    protected function _writeAddress($opts)
    {
        $out = array();

        foreach ($this->_data as $val) {
            $out[] = $val->writeAddress($opts);
        }

        return implode(', ', $out);
    }

    /**
     */
    public function match($ob)
    {
        if (!($ob instanceof Horde_Mail_Rfc822_List)) {
            $ob = new Horde_Mail_Rfc822_List($ob);
        }

        $a = $this->bare_addresses;
        sort($a);
        $b = $ob->bare_addresses;
        sort($b);

        return ($a == $b);
    }

    /**
     * Does this list contain the given e-mail address?
     *
     * @param mixed $address  An e-mail address.
     *
     * @return boolean  True if the e-mail address is contained in the list.
     */
    public function contains($address)
    {
        $ob = new Horde_Mail_Rfc822_Address($address);

        foreach ($this->raw_addresses as $val) {
            if ($val->match($ob)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convenience method to return the first element in a list.
     *
     * Useful since it allows chaining; older PHP versions did not allow array
     * access dereferencing from the results of a function call.
     *
     * @since 2.5.0
     *
     * @return Horde_Mail_Rfc822_Object  Rfc822 object, or null if no object.
     */
    public function first()
    {
        return $this[0];
    }

    /**
     * Normalize objects to add to list.
     *
     * @param mixed $obs  Address data to store in this object.
     *
     * @return array  Entries to add.
     */
    protected function _normalize($obs)
    {
        $add = array();

        if (!($obs instanceof Horde_Mail_Rfc822_List) &&
            !is_array($obs)) {
            $obs = array($obs);
        }

        foreach ($obs as $val) {
            if (is_string($val)) {
                $rfc822 = new Horde_Mail_Rfc822();
                $val = $rfc822->parseAddressList($val);
            }

            if ($val instanceof Horde_Mail_Rfc822_List) {
                $val->setIteratorFilter(self::BASE_ELEMENTS);
                foreach ($val as $val2) {
                    $add[] = $val2;
                }
            } elseif ($val instanceof Horde_Mail_Rfc822_Object) {
                $add[] = $val;
            }
        }

        return $add;
    }

    /* ArrayAccess methods. */

    /**
     */
    public function offsetExists($offset)
    {
        return !is_null($this[$offset]);
    }

    /**
     */
    public function offsetGet($offset)
    {
        try {
            $this->seek($offset);
            return $this->current();
        } catch (OutOfBoundsException $e) {
            return null;
        }
    }

    /**
     */
    public function offsetSet($offset, $value)
    {
        if ($ob = $this[$offset]) {
            if (is_null($this->_ptr['subidx'])) {
                $tmp = $this->_normalize($value);
                if (isset($tmp[0])) {
                    $this->_data[$this->_ptr['idx']] = $tmp[0];
                }
            } else {
                $ob[$offset] = $value;
            }
            $this->_ptr = null;
        }
    }

    /**
     */
    public function offsetUnset($offset)
    {
        if ($ob = $this[$offset]) {
            if (is_null($this->_ptr['subidx'])) {
                unset($this->_data[$this->_ptr['idx']]);
                $this->_data = array_values($this->_data);
            } else {
                unset($ob->addresses[$this->_ptr['subidx']]);
            }
            $this->_ptr = null;
        }
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

    /* Iterator methods. */

    public function current()
    {
        if (!$this->valid()) {
            return null;
        }

        $ob = $this->_data[$this->_ptr['idx']];

        return is_null($this->_ptr['subidx'])
            ? $ob
            : $ob->addresses[$this->_ptr['subidx']];
    }

    public function key()
    {
        return $this->_ptr['key'];
    }

    public function next()
    {
        if (is_null($this->_ptr['subidx'])) {
            $curr = $this->current();
            if (($curr instanceof Horde_Mail_Rfc822_Group) && count($curr)) {
                $this->_ptr['subidx'] = 0;
            } else {
                ++$this->_ptr['idx'];
            }
            $curr = $this->current();
        } elseif (!($curr = $this->_data[$this->_ptr['idx']]->addresses[++$this->_ptr['subidx']])) {
            $this->_ptr['subidx'] = null;
            ++$this->_ptr['idx'];
            $curr = $this->current();
        }

        if (!is_null($curr)) {
            if (!empty($this->_filter) && $this->_iteratorFilter($curr)) {
                $this->next();
            } else {
                ++$this->_ptr['key'];
            }
        }
    }

    public function rewind()
    {
        $this->_ptr = array(
            'idx' => 0,
            'key' => 0,
            'subidx' => null
        );

        if ($this->valid() &&
            !empty($this->_filter) &&
            $this->_iteratorFilter($this->current())) {
            $this->next();
            $this->_ptr['key'] = 0;
        }
    }

    public function valid()
    {
        return (!empty($this->_ptr) && isset($this->_data[$this->_ptr['idx']]));
    }

    public function seek($position)
    {
        if (!$this->valid() ||
            ($position < $this->_ptr['key'])) {
            $this->rewind();
        }

        for ($i = $this->_ptr['key']; ; ++$i) {
            if ($i == $position) {
                return;
            }

            $this->next();
            if (!$this->valid()) {
                throw new OutOfBoundsException('Position not found.');
            }
        }
    }

    protected function _iteratorFilter($ob)
    {
        if (!empty($this->_filter['mask'])) {
            if (($this->_filter['mask'] & self::HIDE_GROUPS) &&
                ($ob instanceof Horde_Mail_Rfc822_Group)) {
                return true;
            }

            if (($this->_filter['mask'] & self::BASE_ELEMENTS) &&
                !is_null($this->_ptr['subidx'])) {
                return true;
            }
        }

        if (!empty($this->_filter['filter']) &&
            ($ob instanceof Horde_Mail_Rfc822_Address)) {
            foreach ($this->_filter['filter'] as $val) {
                if ($ob->match($val)) {
                    return true;
                }
            }
        }

        return false;
    }

    /* Serializable methods. */

    public function serialize()
    {
        return serialize($this->_data);
    }

    public function unserialize($data)
    {
        $this->_data = unserialize($data);
    }

}
