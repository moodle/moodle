<?php
/**
 * Copyright 2014-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 */

/**
 * Provides methods to manipulate/query MIME IDs.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 * @since     2.5.0
 */
class Horde_Mime_Id
{
    /* Constants for idArithmetic() method. */
    const ID_DOWN = 1;
    const ID_NEXT = 2;
    const ID_PREV = 3;
    const ID_UP = 4;

    /**
     * MIME ID.
     *
     * @var string
     */
    public $id;

    /**
     * Constructor.
     *
     * @param string $id  MIME ID.
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     */
    public function __toString()
    {
        return $this->id;
    }

    /**
     * Performs MIME ID "arithmetic".
     *
     * @param string $action  One of:
     *   - ID_DOWN: ID of child. Note: ID_DOWN will first traverse to "$id.0"
     *              if given an ID *NOT* of the form "$id.0". If given an ID of
     *              the form "$id.0", ID_DOWN will traverse to "$id.1". This
     *              behavior can be avoided if 'no_rfc822' option is set.
     *   - ID_NEXT: ID of next sibling.
     *   - ID_PREV: ID of previous sibling.
     *   - ID_UP: ID of parent. Note: ID_UP will first traverse to "$id.0" if
     *            given an ID *NOT* of the form "$id.0". If given an ID of the
     *            form "$id.0", ID_UP will traverse to "$id". This behavior can
     *            be avoided if 'no_rfc822' option is set.
     * @param array $options  Additional options:
     *   - count: (integer) How many levels to traverse.
     *            DEFAULT: 1
     *   - no_rfc822: (boolean) Don't traverse RFC 822 sub-levels.
     *                DEFAULT: false
     *
     * @return mixed  The resulting ID string, or null if that ID can not
     *                exist.
     */
    public function idArithmetic($action, array $options = array())
    {
        return $this->_idArithmetic($this->id, $action, array_merge(array(
            'count' => 1
        ), $options));
    }

    /**
     * @see idArithmetic()
     */
    protected function _idArithmetic($id, $action, $options)
    {
        $pos = strrpos($id, '.');
        $end = ($pos === false) ? $id : substr($id, $pos + 1);

        switch ($action) {
        case self::ID_DOWN:
            if ($end == '0') {
                $id = ($pos === false) ? 1 : substr_replace($id, '1', $pos + 1);
            } else {
                $id .= empty($options['no_rfc822']) ? '.0' : '.1';
            }
            break;

        case self::ID_NEXT:
            ++$end;
            $id = ($pos === false) ? $end : substr_replace($id, $end, $pos + 1);
            break;

        case self::ID_PREV:
            if (($end == '0') ||
                (empty($options['no_rfc822']) && ($end == '1'))) {
                $id = null;
            } elseif ($pos === false) {
                $id = --$end;
            } else {
                $id = substr_replace($id, --$end, $pos + 1);
            }
            break;

        case self::ID_UP:
            if ($pos === false) {
                $id = ($end == '0') ? null : '0';
            } elseif (!empty($options['no_rfc822']) || ($end == '0')) {
                $id = substr($id, 0, $pos);
            } else {
                $id = substr_replace($id, '0', $pos + 1);
            }
            break;
        }

        return (!is_null($id) && --$options['count'])
            ? $this->_idArithmetic($id, $action, $options)
            : $id;
    }

    /**
     * Determines if a given MIME ID lives underneath a base ID.
     *
     * @param string $id  The MIME ID to query.
     *
     * @return boolean  Whether $id lives under the base ID ($this->id).
     */
    public function isChild($id)
    {
        $base = (substr($this->id, -2) == '.0')
            ? substr($this->id, 0, -1)
            : rtrim($this->id, '.') . '.';

        return ((($base == 0) && ($id != 0)) ||
                (strpos(strval($id), strval($base)) === 0));
    }

}
