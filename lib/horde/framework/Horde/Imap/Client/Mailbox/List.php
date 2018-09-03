<?php
/**
 * Copyright 2004-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2004-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * Container of IMAP mailboxes.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2004-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */
class Horde_Imap_Client_Mailbox_List implements Countable, IteratorAggregate
{
    /**
     * The delimiter character to use.
     *
     * @var string
     */
    protected $_delimiter;

    /**
     * Mailbox list.
     *
     * @var array
     */
    protected $_mboxes = array();

    /**
     * Should we sort with INBOX at the front of the list?
     *
     * @var boolean
     */
    protected $_sortinbox;

    /**
     * Constructor.
     *
     * @param mixed $mboxes  A mailbox or list of mailboxes.
     */
    public function __construct($mboxes)
    {
        $this->_mboxes = is_array($mboxes)
            ? $mboxes
            : array($mboxes);
    }

    /**
     * Sort the list of mailboxes.
     *
     * @param array $opts  Options:
     *   - delimiter: (string) The delimiter to use.
     *                DEFAULT: '.'
     *   - inbox: (boolean) Always put INBOX at the head of the list?
     *            DEFAULT: Yes
     *   - noupdate: (boolean) Do not update the object's mailbox list?
     *               DEFAULT: true
     *
     * @return array  List of sorted mailboxes (index association is kept).
     */
    public function sort(array $opts = array())
    {
        $this->_delimiter = isset($opts['delimiter'])
            ? $opts['delimiter']
            : '.';
        $this->_sortinbox = (!isset($opts['inbox']) || !empty($opts['inbox']));

        if (empty($opts['noupdate'])) {
            $mboxes = &$this->_mboxes;
        } else {
            $mboxes = $this->_mboxes;
        }

        uasort($mboxes, array($this, '_mboxCompare'));

        return $mboxes;
    }

    /**
     * Hierarchical folder sorting function (used with usort()).
     *
     * @param string $a  Comparison item 1.
     * @param string $b  Comparison item 2.
     *
     * @return integer  See usort().
     */
    final protected function _mboxCompare($a, $b)
    {
        /* Always return INBOX as "smaller". */
        if ($this->_sortinbox) {
            if (strcasecmp($a, 'INBOX') === 0) {
                return -1;
            } elseif (strcasecmp($b, 'INBOX') === 0) {
                return 1;
            }
        }

        $a_parts = explode($this->_delimiter, $a);
        $b_parts = explode($this->_delimiter, $b);

        $a_count = count($a_parts);
        $b_count = count($b_parts);

        for ($i = 0, $iMax = min($a_count, $b_count); $i < $iMax; ++$i) {
            if ($a_parts[$i] != $b_parts[$i]) {
                /* If only one of the folders is under INBOX, return it as
                 * "smaller". */
                if ($this->_sortinbox && ($i === 0)) {
                    $a_base = (strcasecmp($a_parts[0], 'INBOX') === 0);
                    $b_base = (strcasecmp($b_parts[0], 'INBOX') === 0);
                    if ($a_base && !$b_base) {
                        return -1;
                    } elseif (!$a_base && $b_base) {
                        return 1;
                    }
                }

                $cmp = strnatcasecmp($a_parts[$i], $b_parts[$i]);
                return ($cmp === 0)
                    ? strcmp($a_parts[$i], $b_parts[$i])
                    : $cmp;
            } elseif ($a_parts[$i] !== $b_parts[$i]) {
                return strlen($a_parts[$i]) - strlen($b_parts[$i]);
            }
        }

        return ($a_count - $b_count);
    }

    /* Countable methods. */

    /**
     */
    public function count()
    {
        return count($this->_mboxes);
    }

    /* IteratorAggregate methods. */

    /**
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_mboxes);
    }

}
