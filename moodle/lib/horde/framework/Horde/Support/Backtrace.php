<?php
/**
 * Wrapper around backtraces providing utility methods.
 *
 * Copyright 1999-2014 Horde LLC (http://www.horde.org/)
 *
 * @category   Horde
 * @package    Support
 * @license    http://www.horde.org/licenses/bsd
 */
class Horde_Support_Backtrace
{
    /**
     * Backtrace.
     *
     * @var array
     */
    public $backtrace;

    /**
     * Constructor.
     *
     * @param Exception|array $backtrace  The backtrace source. Either an
     *                                    exception or an existing backtrace.
     *                                    Defaults to the current stack.
     */
    public function __construct($backtrace = null)
    {
        if ($backtrace instanceof Exception) {
            $this->createFromException($backtrace);
        } elseif ($backtrace) {
            $this->createFromDebugBacktrace($backtrace);
        } else {
            $this->createFromDebugBacktrace(debug_backtrace(), 1);
        }
    }

    /**
     * Wraps the result of debug_backtrace().
     *
     * By specifying a non-zero $nestingLevel, levels of the backtrace can be
     * ignored. For instance, when Horde_Support_Backtrace creates a backtrace
     * for you, it ignores the Horde_Backtrace constructor in the wrapped
     * trace.
     *
     * @param array $backtrace       The debug_backtrace() result.
     * @param integer $nestingLevel  The number of levels of the backtrace to
     *                               ignore.
     */
    public function createFromDebugBacktrace($backtrace, $nestingLevel = 0)
    {
        while ($nestingLevel > 0) {
            array_shift($backtrace);
            --$nestingLevel;
        }

        $this->backtrace = $backtrace;
    }

    /**
     * Wraps an Exception object's backtrace.
     *
     * @param Exception $e  The exception to wrap.
     */
    public function createFromException(Exception $e)
    {
        $this->backtrace = $e->getTrace();
        if ($previous = $e->getPrevious()) {
            $backtrace = new self($previous);
            $this->backtrace = array_merge($backtrace->backtrace,
                                           $this->backtrace);
        }
    }

    /**
     * Returns the nesting level (number of calls deep) of the current context.
     *
     * @return integer  Nesting level.
     */
    public function getNestingLevel()
    {
        return count($this->backtrace);
    }

    /**
     * Returns the context at a specific nesting level.
     *
     * @param integer $nestingLevel  0 == current level, 1 == caller, and so on
     *
     * @return array  The requested context.
     */
    public function getContext($nestingLevel)
    {
        if (!isset($this->backtrace[$nestingLevel])) {
            throw new Horde_Exception('Unknown nesting level');
        }
        return $this->backtrace[$nestingLevel];
    }

    /**
     * Returns details about the routine where the exception occurred.
     *
     * @return array $caller
     */
    public function getCurrentContext()
    {
        return $this->getContext(0);
    }

    /**
     * Returns details about the caller of the routine where the exception
     * occurred.
     *
     * @return array $caller
     */
    public function getCallingContext()
    {
        return $this->getContext(1);
    }

    /**
     * Returns a simple, human-readable list of the complete backtrace.
     *
     * @return string  The backtrace map.
     */
    public function __toString()
    {
        $count = count($this->backtrace);
        $pad = strlen($count);
        $map = '';
        for ($i = $count - 1; $i >= 0; $i--) {
            $map .= str_pad($count - $i, $pad, ' ', STR_PAD_LEFT) . '. ';
            if (isset($this->backtrace[$i]['class'])) {
                $map .= $this->backtrace[$i]['class']
                    . $this->backtrace[$i]['type'];
            }
            $map .= $this->backtrace[$i]['function'] . '()';
            if (isset($this->backtrace[$i]['file'])) {
                $map .= ' ' . $this->backtrace[$i]['file']
                    . ':' . $this->backtrace[$i]['line'];
            }
            $map .= "\n";
        }
        return $map;
    }
}
