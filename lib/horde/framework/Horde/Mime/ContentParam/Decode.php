<?php
/**
 * Copyright 2014-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * Decoding parsing code adapted from rfc822-parser.c (Dovecot 2.2.13)
 *   Original code released under LGPL-2.1
 *   Copyright (c) 2002-2014 Timo Sirainen <tss@iki.fi>
 *
 * @category  Horde
 * @copyright 2002-2015 Timo Sirainen
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 */

/**
 * Decode MIME content parameter data (RFC 2045; 2183; 2231).
 *
 * @author    Timo Sirainen <tss@iki.fi>
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2002-2015 Timo Sirainen
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 * @since     2.5.0
 */
class Horde_Mime_ContentParam_Decode extends Horde_Mail_Rfc822
{
    /**
     * Decode content parameter data.
     *
     * @param string $data  Parameter data.
     *
     * @return array  List of parameter key/value combinations.
     */
    public function decode($data)
    {
        $out = array();

        $this->_data = $data;
        $this->_datalen = strlen($data);
        $this->_ptr = 0;

        while ($this->_curr() !== false) {
            $this->_rfc822SkipLwsp();

            $this->_rfc822ParseMimeToken($param);

            if (is_null($param) || ($this->_curr() != '=')) {
                break;
            }

            ++$this->_ptr;
            $this->_rfc822SkipLwsp();

            $value = '';

            if ($this->_curr() == '"') {
                try {
                    $this->_rfc822ParseQuotedString($value);
                } catch (Horde_Mail_Exception $e) {
                    break;
                }
            } else {
                $this->_rfc822ParseMimeToken($value);
                if (is_null($value)) {
                    break;
                }
            }

            $out[$param] = $value;

            $this->_rfc822SkipLwsp();
            if ($this->_curr() != ';') {
                break;
            }

            ++$this->_ptr;
        }

        return $out;
    }

    /**
     * Determine if character is a non-escaped element in MIME parameter data
     * (See RFC 2045 [Appendix A]).
     *
     * @param string $c  Character to test.
     *
     * @return boolean  True if non-escaped character.
     */
    public static function isAtextNonTspecial($c)
    {
        switch ($ord = ord($c)) {
        case 34:
        case 40:
        case 41:
        case 44:
        case 47:
        case 58:
        case 59:
        case 60:
        case 61:
        case 62:
        case 63:
        case 64:
        case 91:
        case 92:
        case 93:
            /* "(),/:;<=>?@[\] */
            return false;

        default:
            /* CTLs, SPACE, DEL, non-ASCII */
            return (($ord > 32) && ($ord < 127));
        }
    }

    /**
     */
    protected function _rfc822ParseMimeToken(&$str)
    {
        for ($i = $this->_ptr, $size = strlen($this->_data); $i < $size; ++$i) {
            if (!self::isAtextNonTspecial($this->_data[$i])) {
                break;
            }
        }

        if ($i === $this->_ptr) {
            $str = null;
        } else {
            $str = substr($this->_data, $this->_ptr, $i - $this->_ptr);
            $this->_ptr += ($i - $this->_ptr);
            $this->_rfc822SkipLwsp();
        }
    }

}
