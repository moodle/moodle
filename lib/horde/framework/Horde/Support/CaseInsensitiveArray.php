<?php
/**
 * Copyright 2013-2014 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (BSD). If you
 * did not receive this file, see http://www.horde.org/licenses/bsd.
 *
 * @category  Horde
 * @copyright 2013-2014 Horde LLC
 * @license   http://www.horde.org/licenses/bsd BSD
 * @package   Support
 */

/**
 * An array implemented as an object that contains case-insensitive keys.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2013-2014 Horde LLC
 * @license   http://www.horde.org/licenses/bsd BSD
 * @package   Support
 */
class Horde_Support_CaseInsensitiveArray extends ArrayIterator
{
    /**
     */
    public function offsetGet($offset)
    {
        return (is_null($offset = $this->_getRealOffset($offset)))
            ? null
            : parent::offsetGet($offset);
    }

    /**
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($roffset = $this->_getRealOffset($offset))) {
            parent::offsetSet($offset, $value);
        } else {
            parent::offsetSet($roffset, $value);
        }
    }

    /**
     */
    public function offsetExists($offset)
    {
        return (is_null($offset = $this->_getRealOffset($offset)))
            ? false
            : parent::offsetExists($offset);
    }

    /**
     */
    public function offsetUnset($offset)
    {
        if (!is_null($offset = $this->_getRealOffset($offset))) {
            parent::offsetUnset($offset);
        }
    }

    /**
     * Determines the actual array offset given the input offset.
     *
     * @param string $offset  Input offset.
     *
     * @return string  Real offset or null.
     */
    protected function _getRealOffset($offset)
    {
        foreach (array_keys($this->getArrayCopy()) as $key) {
            if (strcasecmp($key, $offset) === 0) {
                return $key;
            }
        }

        return null;
    }

}
