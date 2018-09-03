<?php
/**
 * Copyright 1999-2017 Horde LLC (http://www.horde.org/)
 *
 * -----
 *
 * This file contains code adapted from PEAR's PHP_Compat library (v1.6.0a3).
 *
 *   http://pear.php.net/package/PHP_Compat
 *
 * This code was released under the LGPL 2.1
 *
 * -----
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2009-2017 Horde LLC
 * @copyright 2004-2007 Aidan Lister <aidan@php.net>, Arpad Ray <arpad@php.net>
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 */

/**
 * Class used to uudecode data.
 *
 * Needed because PHP's built-in uudecode() method is broken.
 *
 * @author    Chuck Hagenbuch <chuck@horde.org>
 * @author    Aidan Lister <aidan@php.net>
 * @author    Michael Slusarz <slusarz@horde.org>
 * @author    Michael Wallner <mike@php.net>
 * @category  Horde
 * @copyright 2009-2017 Horde LLC
 * @copyright 2004-2007 Aidan Lister <aidan@php.net>, Arpad Ray <arpad@php.net>
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 * @since     2.5.0
 */
class Horde_Mime_Uudecode implements Countable, IteratorAggregate
{
    const UUENCODE_REGEX = "/begin ([0-7]{3}) (.+)\r?\n(.+)\r?\nend/Us";

    /**
     * Uudecode data.
     *
     * A list of arrays, with each array corresponding to a file in the input
     * and containing the following keys:
     *   - data: (string) Unencoded data.
     *   - name: (string) Filename.
     *   - perms: (string) Octal permissions.
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Scans $input for uuencoded data and converts it to unencoded data.
     *
     * @param string $input  The input data
     */
    public function __construct($input)
    {
        /* Find all uuencoded sections. */
        if (preg_match_all(self::UUENCODE_REGEX, $input, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $v) {
                $this->_data[] = array(
                    'data' => $this->_uudecode($v[3]),
                    'name' => $v[2],
                    'perm' => $v[1]
                );
            }
        }
    }

    /**
     * PHP 5's built-in convert_uudecode() is broken. Need this wrapper.
     *
     * @param string $input  UUencoded input.
     *
     * @return string  Decoded string.
     */
    protected function _uudecode($input)
    {
        $decoded = '';

        foreach (explode("\n", $input) as $line) {
            $c = count($bytes = unpack('c*', substr(trim($line,"\r\n\t"), 1)));

            while ($c % 4) {
                $bytes[++$c] = 0;
            }

            foreach (array_chunk($bytes, 4) as $b) {
                $b0 = ($b[0] == 0x60) ? 0 : $b[0] - 0x20;
                $b1 = ($b[1] == 0x60) ? 0 : $b[1] - 0x20;
                $b2 = ($b[2] == 0x60) ? 0 : $b[2] - 0x20;
                $b3 = ($b[3] == 0x60) ? 0 : $b[3] - 0x20;

                $b0 <<= 2;
                $b0 |= ($b1 >> 4) & 0x03;
                $b1 <<= 4;
                $b1 |= ($b2 >> 2) & 0x0F;
                $b2 <<= 6;
                $b2 |= $b3 & 0x3F;

                $decoded .= pack('c*', $b0, $b1, $b2);
            }
        }

        return rtrim($decoded, "\0");
    }

    /* Countable method. */

    public function count()
    {
        return count($this->_data);
    }

    /* IteratorAggregate method. */

    public function getIterator()
    {
        return new ArrayIterator($this->_data);
    }

}
