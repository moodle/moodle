<?php
/**
 * Simple interface for tracking memory consumption.
 *
 * <code>
 *  $t = new Horde_Support_Memory;
 *  $t->push();
 *  $used = $t->pop();
 * </code>
 *
 * Do not expect too much of this memory tracker. Profiling memory is not
 * trivial as your placement of the measurements may obscure important
 * information. As a trivial example: Assuming that your script used 20 MB of
 * memory befory you call push() the information you get when calling pop()
 * might only tell you that there was less than 20 MB of memory consumed in
 * between the two calls. Take the changes to internal memory handling of PHP in
 * between the different versions into account
 * (http://de3.php.net/manual/en/features.gc.performance-considerations.php) and
 * you should get an idea about why you might be cautious about the values you
 * get from this memory tracker.
 *
 * Copyright 2011-2017 Horde LLC (http://www.horde.org/)
 *
 * @category   Horde
 * @package    Support
 * @license    http://www.horde.org/licenses/bsd
 */
class Horde_Support_Memory
{
    /**
     * Holds the starting memory consumption.
     *
     * @var array
     */
    protected $_start = array();

    /**
     * Current index for stacked trackers.
     *
     * @var integer
     */
    protected $_idx = 0;

    /**
     * Push a new tracker on the stack.
     */
    public function push()
    {
        $start = $this->_start[$this->_idx++] = array(
            memory_get_usage(),
            memory_get_peak_usage(),
            memory_get_usage(true),
            memory_get_peak_usage(true)
        );
        return $start;
    }

    /**
     * Pop the latest tracker and return the difference with the current
     * memory situation.
     *
     * @return array The change in memory allocated via emalloc() in between the
     *               push() and the pop() call. The array holds four values: the
     *               first one indicates the change in current usage of memory
     *               while the second value indicates any changes in the peak
     *               amount of memory used. The third and fourth value show
     *               current and peak usage as well but indicate the real memory
     *               usage and not just the part allocated via emalloc(),
     */
    public function pop()
    {
        if (! ($this->_idx > 0)) {
            throw new Exception('No timers have been started');
        }
        $start = $this->_start[--$this->_idx];
        return array(
            memory_get_usage() - $start[0],
            memory_get_peak_usage() - $start[1],
            memory_get_usage(true) - $start[2],
            memory_get_peak_usage(true) - $start[3]
        );
    }

}
