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
 * Available ACL rights for a mailbox/identifier (see RFC 2086/4314).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2011-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */
class Horde_Imap_Client_Data_AclRights extends Horde_Imap_Client_Data_AclCommon implements ArrayAccess, Iterator, Serializable
{
    /**
     * ACL optional rights.
     *
     * @var array
     */
    protected $_optional = array();

    /**
     * ACL required rights.
     *
     * @var array
     */
    protected $_required = array();

    /**
     * Constructor.
     *
     * @param array $required  The required rights (see RFC 4314 [2.1]).
     * @param array $optional  The optional rights (see RFC 4314 [2.1]).
     */
    public function __construct(array $required = array(),
                                array $optional = array())
    {
        $this->_required = $required;

        foreach ($optional as $val) {
            foreach (str_split($val) as $right) {
                $this->_optional[$right] = $val;
            }
        }

        $this->_normalize();
    }

    /**
     * String representation of the ACL.
     *
     * @return string  String representation (RFC 4314 compliant).
     *
     */
    public function __toString()
    {
        return implode('', array_keys(array_flip(array_merge(array_values($this->_required), array_keys($this->_optional)))));
    }

    /**
     * Normalize virtual rights (see RFC 4314 [2.1.1]).
     */
    protected function _normalize()
    {
        /* Clients conforming to RFC 4314 MUST ignore the virtual ACL_CREATE
         * and ACL_DELETE rights. See RFC 4314 [2.1]. However, we still need
         * to handle these rights when dealing with RFC 2086 servers since
         * we are abstracting out use of ACL_CREATE/ACL_DELETE to their
         * component RFC 4314 rights. */
        foreach ($this->_virtual as $key => $val) {
            if (isset($this->_optional[$key])) {
                unset($this->_optional[$key]);
                foreach ($val as $val2) {
                    $this->_optional[$val2] = implode('', $val);
                }
            } elseif (($pos = array_search($key, $this->_required)) !== false) {
                unset($this->_required[$pos]);
                $this->_required = array_unique(array_merge($this->_required, $val));
            }
        }
    }

    /* ArrayAccess methods. */

    /**
     */
    #[ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return (bool)$this[$offset];
    }

    /**
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if (isset($this->_optional[$offset])) {
            return $this->_optional[$offset];
        }

        $pos = array_search($offset, $this->_required);

        return ($pos === false)
            ? null
            : $this->_required[$pos];
    }

    /**
     */
    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->_optional[$offset] = $value;
        $this->_normalize();
    }

    /**
     */
    #[ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->_optional[$offset]);
        $this->_required = array_values(array_diff($this->_required, array($offset)));

        if (isset($this->_virtual[$offset])) {
            foreach ($this->_virtual[$offset] as $val) {
                unset($this[$val]);
            }
        }
    }

    /* Iterator methods. */

    /**
     */
    #[ReturnTypeWillChange]
    public function current()
    {
        $val = current($this->_required);
        return is_null($val)
            ? current($this->_optional)
            : $val;
    }

    /**
     */
    #[ReturnTypeWillChange]
    public function key()
    {
        $key = key($this->_required);
        return is_null($key)
            ? key($this->_optional)
            : $key;
    }

    /**
     */
    #[ReturnTypeWillChange]
    public function next()
    {
        if (key($this->_required) === null) {
            next($this->_optional);
        } else {
            next($this->_required);
        }
    }

    /**
     */
    #[ReturnTypeWillChange]
    public function rewind()
    {
        reset($this->_required);
        reset($this->_optional);
    }

    /**
     */
    #[ReturnTypeWillChange]
    public function valid()
    {
        return ((key($this->_required) !== null) ||
                (key($this->_optional) !== null));

    }

    /* Serializable methods. */

    /**
     */
    public function serialize()
    {
        return serialize($this->__serialize());
    }

    /**
     */
    public function unserialize($data)
    {
        $data = @unserialize($data);
        if (!is_array($data)) {
            throw new Exception('Cache version changed.');
        }
        $this->__unserialize($data);
    }

    /**
     * @return array
     */
    public function __serialize()
    {
        return [$this->_required, $this->_optional];
    }

    public function __unserialize(array $data)
    {
        list($this->_required, $this->_optional) = $data;
    }

}
