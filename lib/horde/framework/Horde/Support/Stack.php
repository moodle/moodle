<?php
/**
 * Simple class for using an array as a stack.
 *
 * Copyright 2008-2017 Horde LLC (http://www.horde.org/)
 *
 * @category   Horde
 * @package    Support
 * @license    http://www.horde.org/licenses/bsd
 */
class Horde_Support_Stack
{
    /**
     * @var array
     */
    protected $_stack = array();

    public function __construct($stack = array())
    {
        $this->_stack = $stack;
    }

    public function push($value)
    {
        $this->_stack[] = $value;
    }

    public function pop()
    {
        return array_pop($this->_stack);
    }

    public function peek($offset = 1)
    {
        if (isset($this->_stack[count($this->_stack) - $offset])) {
            return $this->_stack[count($this->_stack) - $offset];
        } else {
            return null;
        }
    }
}
