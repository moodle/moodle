<?php
/**
 * Copyright 2014 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * List of namespaces.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 * @since     2.21.0
 */
class Horde_Imap_Client_Namespace_List
implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * The list of namespace objects.
     *
     * @var array
     */
    protected $_ns = array();

    /**
     * Constructor.
     *
     * @param array $ns  The list of namespace objects.
     */
    public function __construct($ns = array())
    {
        foreach ($ns as $val) {
            $this->_ns[strval($val)] = $val;
        }
    }

    /**
     * Get namespace info for a full mailbox path.
     *
     * @param string $mbox       The mailbox path.
     * @param boolean $personal  If true, will return the empty namespace only
     *                           if it is a personal namespace.
     *
     * @return mixed  The Horde_Imap_Client_Data_Namespace object for the
     *                mailbox path, or null if the path doesn't exist.
     */
    public function getNamespace($mbox, $personal = false)
    {
        $mbox = strval($mbox);

        if ($ns = $this[$mbox]) {
            return $ns;
        }

        foreach ($this->_ns as $val) {
            $mbox = $mbox . $val->delimiter;
            if (strlen($val->name) && (strpos($mbox, $val->name) === 0)) {
                return $val;
            }
        }

        return (($ns = $this['']) && (!$personal || ($ns->type === $ns::NS_PERSONAL)))
            ? $ns
            : null;
    }

    /* ArrayAccess methods. */

    /**
     */
    public function offsetExists($offset)
    {
        return isset($this->_ns[strval($offset)]);
    }

    /**
     */
    public function offsetGet($offset)
    {
        $offset = strval($offset);

        return isset($this->_ns[$offset])
            ? $this->_ns[$offset]
            : null;
    }

    /**
     */
    public function offsetSet($offset, $value)
    {
        if ($value instanceof Horde_Imap_Client_Data_Namespace) {
            $this->_ns[strval($value)] = $value;
        }
    }

    /**
     */
    public function offsetUnset($offset)
    {
        unset($this->_ns[strval($offset)]);
    }

    /* Countable methods. */

    /**
     */
    public function count()
    {
        return count($this->_ns);
    }

    /* IteratorAggregate methods. */

    /**
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_ns);
    }

}
