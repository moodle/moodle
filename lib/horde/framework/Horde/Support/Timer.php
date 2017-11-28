<?php
/**
 * Simple interface for timing operations.
 *
 * <code>
 *  $t = new Horde_Support_Timer;
 *  $t->push();
 *  $elapsed = $t->pop();
 * </code>
 *
 * Copyright 1999-2017 Horde LLC (http://www.horde.org/)
 *
 * @category   Horde
 * @package    Support
 * @license    http://www.horde.org/licenses/bsd
 */
class Horde_Support_Timer
{
    /**
     * Holds the starting timestamp.
     *
     * @var array
     */
    protected $_start = array();

    /**
     * Current index for stacked timers.
     *
     * @var integer
     */
    protected $_idx = 0;

    /**
     * Push a new timer start on the stack.
     */
    public function push()
    {
        $start = $this->_start[$this->_idx++] = microtime(true);
        return $start;
    }

    /**
     * Pop the latest timer start and return the difference with the current
     * time.
     *
     * @return float The amount of time passed.
     */
    public function pop()
    {
        $etime = microtime(true);

        if (! ($this->_idx > 0)) {
            throw new Exception('No timers have been started');
        }

        return $etime - $this->_start[--$this->_idx];
    }

}
