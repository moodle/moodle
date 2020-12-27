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
 * An object that provides a way to identify a list of IMAP indices.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2011-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 *
 * @property-read boolean $all  Does this represent an ALL message set?
 * @property-read array $ids  The list of IDs.
 * @property-read boolean $largest  Does this represent the largest ID in use?
 * @property-read string $max  The largest ID (@since 2.20.0).
 * @property-read string $min  The smallest ID (@since 2.20.0).
 * @property-read string $range_string  Generates a range string consisting of
 *                                      all messages between begin and end of
 *                                      ID list.
 * @property-read boolean $search_res  Does this represent a search result?
 * @property-read boolean $sequence  Are these sequence IDs? If false, these
 *                                   are UIDs.
 * @property-read boolean $special  True if this is a "special" ID
 *                                  representation.
 * @property-read string $tostring  Return the non-sorted string
 *                                  representation.
 * @property-read string $tostring_sort  Return the sorted string
 *                                       representation.
 */
class Horde_Imap_Client_Ids implements Countable, Iterator, Serializable
{
    /**
     * "Special" representation constants.
     */
    const ALL = "\01";
    const SEARCH_RES = "\02";
    const LARGEST = "\03";

    /**
     * Allow duplicate IDs?
     *
     * @var boolean
     */
    public $duplicates = false;

    /**
     * List of IDs.
     *
     * @var mixed
     */
    protected $_ids = array();

    /**
     * Are IDs message sequence numbers?
     *
     * @var boolean
     */
    protected $_sequence = false;

    /**
     * Are IDs sorted?
     *
     * @var boolean
     */
    protected $_sorted = false;

    /**
     * Constructor.
     *
     * @param mixed $ids         See self::add().
     * @param boolean $sequence  Are $ids message sequence numbers?
     */
    public function __construct($ids = null, $sequence = false)
    {
        $this->add($ids);
        $this->_sequence = $sequence;
    }

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'all':
            return ($this->_ids === self::ALL);

        case 'ids':
            return is_array($this->_ids)
                ? $this->_ids
                : array();

        case 'largest':
            return ($this->_ids === self::LARGEST);

        case 'max':
            $this->sort();
            return end($this->_ids);

        case 'min':
            $this->sort();
            return reset($this->_ids);

        case 'range_string':
            if (!count($this)) {
                return '';
            }

            $min = $this->min;
            $max = $this->max;

            return ($min == $max)
                ? $min
                : $min . ':' . $max;

        case 'search_res':
            return ($this->_ids === self::SEARCH_RES);

        case 'sequence':
            return (bool)$this->_sequence;

        case 'special':
            return is_string($this->_ids);

        case 'tostring':
        case 'tostring_sort':
            if ($this->all) {
                return '1:*';
            } elseif ($this->largest) {
                return '*';
            } elseif ($this->search_res) {
                return '$';
            }
            return strval($this->_toSequenceString($name == 'tostring_sort'));
        }
    }

    /**
     */
    public function __toString()
    {
        return $this->tostring;
    }

    /**
     * Add IDs to the current object.
     *
     * @param mixed $ids  Either self::ALL, self::SEARCH_RES, self::LARGEST,
     *                    Horde_Imap_Client_Ids object, array, or sequence
     *                    string.
     */
    public function add($ids)
    {
        if (!is_null($ids)) {
            if (is_string($ids) &&
                in_array($ids, array(self::ALL, self::SEARCH_RES, self::LARGEST))) {
                $this->_ids = $ids;
            } elseif ($add = $this->_resolveIds($ids)) {
                if (is_array($this->_ids) && !empty($this->_ids)) {
                    foreach ($add as $val) {
                        $this->_ids[] = $val;
                    }
                } else {
                    $this->_ids = $add;
                }
                if (!$this->duplicates) {
                    $this->_ids = (count($this->_ids) > 25000)
                        ? array_unique($this->_ids)
                        : array_keys(array_flip($this->_ids));
                }
            }

            $this->_sorted = is_array($this->_ids) && (count($this->_ids) === 1);
        }
    }

    /**
     * Removed IDs from the current object.
     *
     * @since 2.17.0
     *
     * @param mixed $ids  Either Horde_Imap_Client_Ids object, array, or
     *                    sequence string.
     */
    public function remove($ids)
    {
        if (!$this->isEmpty() &&
            ($remove = $this->_resolveIds($ids))) {
            $this->_ids = array_diff($this->_ids, array_unique($remove));
        }
    }

    /**
     * Is this object empty (i.e. does not contain IDs)?
     *
     * @return boolean  True if object is empty.
     */
    public function isEmpty()
    {
        return (is_array($this->_ids) && !count($this->_ids));
    }

    /**
     * Reverses the order of the IDs.
     */
    public function reverse()
    {
        if (is_array($this->_ids)) {
            $this->_ids = array_reverse($this->_ids);
        }
    }

    /**
     * Sorts the IDs.
     */
    public function sort()
    {
        if (!$this->_sorted && is_array($this->_ids)) {
            $this->_sort($this->_ids);
            $this->_sorted = true;
        }
    }

    /**
     * Sorts the IDs numerically.
     *
     * @param array $ids  The array list.
     */
    protected function _sort(&$ids)
    {
        sort($ids, SORT_NUMERIC);
    }

    /**
     * Split the sequence string at an approximate length.
     *
     * @since 2.7.0
     *
     * @param integer $length  Length to split.
     *
     * @return array  A list containing individual sequence strings.
     */
    public function split($length)
    {
        $id = new Horde_Stream_Temp();
        $id->add($this->tostring_sort, true);

        $out = array();

        do {
            $out[] = $id->substring(0, $length) . $id->getToChar(',');
        } while (!$id->eof());

        return $out;
    }

    /**
     * Resolve the $ids input to add() and remove().
     *
     * @param mixed $ids  Either Horde_Imap_Client_Ids object, array, or
     *                    sequence string.
     *
     * @return array  An array of IDs.
     */
    protected function _resolveIds($ids)
    {
        if ($ids instanceof Horde_Imap_Client_Ids) {
            return $ids->ids;
        } elseif (is_array($ids)) {
            return $ids;
        } elseif (is_string($ids) || is_integer($ids)) {
            return is_numeric($ids)
                ? array($ids)
                : $this->_fromSequenceString($ids);
        }

        return array();
    }

    /**
     * Create an IMAP message sequence string from a list of indices.
     *
     * Index Format: range_start:range_end,uid,uid2,...
     *
     * @param boolean $sort  Numerically sort the IDs before creating the
     *                       range?
     *
     * @return string  The IMAP message sequence string.
     */
    protected function _toSequenceString($sort = true)
    {
        if (empty($this->_ids)) {
            return '';
        }

        $in = $this->_ids;

        if ($sort && !$this->_sorted) {
            $this->_sort($in);
        }

        $first = $last = array_shift($in);
        $i = count($in) - 1;
        $out = array();

        foreach ($in as $key => $val) {
            if (($last + 1) == $val) {
                $last = $val;
            }

            if (($i == $key) || ($last != $val)) {
                if ($last == $first) {
                    $out[] = $first;
                    if ($i == $key) {
                        $out[] = $val;
                    }
                } else {
                    $out[] = $first . ':' . $last;
                    if (($i == $key) && ($last != $val)) {
                        $out[] = $val;
                    }
                }
                $first = $last = $val;
            }
        }

        return empty($out)
            ? $first
            : implode(',', $out);
    }

    /**
     * Parse an IMAP message sequence string into a list of indices.
     *
     * @see _toSequenceString()
     *
     * @param string $str  The IMAP message sequence string.
     *
     * @return array  An array of indices.
     */
    protected function _fromSequenceString($str)
    {
        $ids = array();
        $str = trim($str);

        if (!strlen($str)) {
            return $ids;
        }

        $idarray = explode(',', $str);

        foreach ($idarray as $val) {
            $range = explode(':', $val);
            if (isset($range[1])) {
                for ($i = min($range), $j = max($range); $i <= $j; ++$i) {
                    $ids[] = $i;
                }
            } else {
                $ids[] = $val;
            }
        }

        return $ids;
    }

    /* Countable methods. */

    /**
     */
    public function count()
    {
        return is_array($this->_ids)
            ? count($this->_ids)
            : 0;
    }

    /* Iterator methods. */

    /**
     */
    public function current()
    {
        return is_array($this->_ids)
            ? current($this->_ids)
            : null;
    }

    /**
     */
    public function key()
    {
        return is_array($this->_ids)
            ? key($this->_ids)
            : null;
    }

    /**
     */
    public function next()
    {
        if (is_array($this->_ids)) {
            next($this->_ids);
        }
    }

    /**
     */
    public function rewind()
    {
        if (is_array($this->_ids)) {
            reset($this->_ids);
        }
    }

    /**
     */
    public function valid()
    {
        return !is_null($this->key());
    }

    /* Serializable methods. */

    /**
     */
    public function serialize()
    {
        $save = array();

        if ($this->duplicates) {
            $save['d'] = 1;
        }

        if ($this->_sequence) {
            $save['s'] = 1;
        }

        if ($this->_sorted) {
            $save['is'] = 1;
        }

        switch ($this->_ids) {
        case self::ALL:
            $save['a'] = true;
            break;

        case self::LARGEST:
            $save['l'] = true;
            break;

        case self::SEARCH_RES:
            $save['sr'] = true;
            break;

        default:
            $save['i'] = strval($this);
            break;
        }

        return serialize($save);
    }

    /**
     */
    public function unserialize($data)
    {
        $save = @unserialize($data);

        $this->duplicates = !empty($save['d']);
        $this->_sequence = !empty($save['s']);
        $this->_sorted = !empty($save['is']);

        if (isset($save['a'])) {
            $this->_ids = self::ALL;
        } elseif (isset($save['l'])) {
            $this->_ids = self::LARGEST;
        } elseif (isset($save['sr'])) {
            $this->_ids = self::SEARCH_RES;
        } elseif (isset($save['i'])) {
            $this->add($save['i']);
        }
    }

}
