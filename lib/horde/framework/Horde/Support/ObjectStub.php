<?php
/**
 * Provides a wrapper around an object to return null for non-existent
 * properties (instead of throwing an error).
 *
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
 *
 * @author   Michael Slusarz <slusarz@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/bsd BSD
 * @package  Support
 */

/**
 * @author   Michael Slusarz <slusarz@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/bsd BSD
 * @package  Support
 */
class Horde_Support_ObjectStub
{
    /**
     * Original data object.
     *
     * @var array
     */
    protected $_data;

    /**
     * Constructor
     *
     * @param object $data  The original data object.
     */
    public function __construct($data)
    {
        $this->_data = $data;
    }

    /**
     */
    public function __get($name)
    {
        return isset($this->_data->$name)
            ? $this->_data->$name
            : null;
    }

    /**
     */
    public function __set($name, $value)
    {
        $this->_data->$name = $value;
    }

    /**
     */
    public function __isset($name)
    {
        return isset($this->_data->$name);
    }

    /**
     */
    public function __unset($name)
    {
        unset($this->_data->$name);
    }

}
