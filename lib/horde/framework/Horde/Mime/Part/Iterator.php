<?php
/**
 * Copyright 2015-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2015-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 */

/**
 * Recursive iterator for Horde_Mime_Part objects. This iterator is
 * self-contained and independent of all other iterators.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2015-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 * @since     2.9.0
 */
class Horde_Mime_Part_Iterator
implements Countable, Iterator
{
    /**
     * Include the base when iterating?
     *
     * @var boolean
     */
    protected $_includeBase;

    /**
     * Base part.
     *
     * @var Horde_Mime_Part
     */
    protected $_part;

    /**
     * State data.
     *
     * @var object
     */
    protected $_state;

    /**
     * Constructor.
     */
    public function __construct(Horde_Mime_Part $part, $base = false)
    {
        $this->_includeBase = (bool)$base;
        $this->_part = $part;
    }

    /* Countable methods. */

    /**
     * Returns the number of message parts.
     *
     * @return integer  Number of message parts.
     */
    #[ReturnTypeWillChange]
    public function count()
    {
        return count(iterator_to_array($this));
    }

    /* RecursiveIterator methods. */

    /**
     */
    #[ReturnTypeWillChange]
    public function current()
    {
        return $this->valid()
            ? $this->_state->current
            : null;
    }

    /**
     */
    #[ReturnTypeWillChange]
    public function key()
    {
        return ($curr = $this->current())
            ? $curr->getMimeId()
            : null;
    }

    /**
     */
    #[ReturnTypeWillChange]
    public function next()
    {
        if (!isset($this->_state)) {
            return;
        }

        $out = $this->_state->current->getPartByIndex($this->_state->index++);

        if ($out) {
            $this->_state->recurse[] = array(
                $this->_state->current,
                $this->_state->index
            );
            $this->_state->current = $out;
            $this->_state->index = 0;
        } elseif ($tmp = array_pop($this->_state->recurse)) {
            $this->_state->current = $tmp[0];
            $this->_state->index = $tmp[1];
            $this->next();
        } else {
            unset($this->_state);
        }
    }

    /**
     */
    #[ReturnTypeWillChange]
    public function rewind()
    {
        $this->_state = new stdClass;
        $this->_state->current = $this->_part;
        $this->_state->index = 0;
        $this->_state->recurse = array();

        if (!$this->_includeBase) {
            $this->next();
        }
    }

    /**
     */
    #[ReturnTypeWillChange]
    public function valid()
    {
        return !empty($this->_state);
    }

}
