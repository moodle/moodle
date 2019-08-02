<?php
/**
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (BSD). If you
 * did not receive this file, see http://www.horde.org/licenses/bsd.
 *
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 */

/**
 * Object representation of an RFC 822 element.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 */
abstract class Horde_Mail_Rfc822_Object
{
    /**
     * String representation of object.
     *
     * @return string  Returns the full e-mail address.
     */
    public function __toString()
    {
        return $this->writeAddress();
    }

    /**
     * Write an address given information in this part.
     *
     * @param mixed $opts  If boolean true, is equivalent to passing true for
     *                     both 'encode' and 'idn'. If an array, these
     *                     keys are supported:
     *   - comment: (boolean) If true, include comment(s) in output?
     *              @since 2.6.0
     *              DEFAULT: false
     *   - encode: (mixed) MIME encode the personal/groupname parts?
     *             If boolean true, encodes in 'UTF-8'.
     *             If a string, encodes using this charset.
     *             DEFAULT: false
     *   - idn: (boolean) If true, encodes IDN domain names (RFC 3490).
     *          DEFAULT: false
     *   - noquote: (boolean) If true, don't quote personal part. [@since
     *              2.4.0]
     *              DEFAULT: false
     *
     * @return string  The correctly escaped/quoted address.
     */
    public function writeAddress($opts = array())
    {
        if ($opts === true) {
            $opts = array(
                'encode' => 'UTF-8',
                'idn' => true
            );
        } elseif (!empty($opts['encode']) && ($opts['encode'] === true)) {
            $opts['encode'] = 'UTF-8';
        }

        return $this->_writeAddress($opts);
    }

    /**
     * Class-specific implementation of writeAddress().
     *
     * @see writeAddress()
     *
     * @param array $opts  See writeAddress().
     *
     * @return string  The correctly escaped/quoted address.
     */
    abstract protected function _writeAddress($opts);

    /**
     * Compare this object against other data.
     *
     * @param mixed $ob  Address data.
     *
     * @return boolean  True if the data reflects the same canonical address.
     */
    abstract public function match($ob);

}
