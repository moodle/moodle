<?php
/**
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * Fetch results object for use with Horde_Imap_Client_Base#fetch().
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 *
 * @property-read integer $key_type  The key type (sequence or UID).
 */
class Horde_Imap_Client_Fetch_Results
implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Key type constants.
     */
    const SEQUENCE = 1;
    const UID = 2;

    /**
     * Internal data array.
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Key type.
     *
     * @var integer
     */
    protected $_keyType;

    /**
     * Class to use when creating a new fetch object.
     *
     * @var string
     */
    protected $_obClass;

    /**
     * Constructor.
     *
     * @param string $ob_class   Class to use when creating a new fetch
     *                           object.
     * @param integer $key_type  Key type.
     */
    public function __construct($ob_class = 'Horde_Imap_Client_Data_Fetch',
                                $key_type = self::UID)
    {
        $this->_obClass = $ob_class;
        $this->_keyType = $key_type;
    }

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'key_type':
            return $this->_keyType;
        }
    }

    /**
     * Return a fetch object, creating and storing an empty object in the
     * results set if it doesn't currently exist.
     *
     * @param string $key  The key to retrieve.
     *
     * @return Horde_Imap_Client_Data_Fetch  The fetch object.
     */
    public function get($key)
    {
        if (!isset($this->_data[$key])) {
            $this->_data[$key] = new $this->_obClass();
        }

        return $this->_data[$key];
    }

    /**
     * Return the list of IDs.
     *
     * @return array  ID list.
     */
    public function ids()
    {
        ksort($this->_data);
        return array_keys($this->_data);
    }

    /**
     * Return the first fetch object in the results, if there is only one
     * object.
     *
     * @return null|Horde_Imap_Client_Data_Fetch  The fetch object if there is
     *                                            only one object, or null.
     */
    public function first()
    {
        return (count($this->_data) === 1)
            ? reset($this->_data)
            : null;
    }

    /**
     * Clears all fetch results.
     *
     * @since 2.6.0
     */
    public function clear()
    {
        $this->_data = array();
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

    /* IteratorAggregate methods. */

    /**
     */
    #[ReturnTypeWillChange]
    public function getIterator()
    {
        ksort($this->_data);
        return new ArrayIterator($this->_data);
    }

}
