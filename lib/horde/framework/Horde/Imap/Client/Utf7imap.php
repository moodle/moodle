<?php
/**
 * Originally based on code:
 *
 *  Copyright (C) 2000 Edmund Grimley Evans <edmundo@rano.org>
 *  Released under the GPL (version 2)
 *
 *  Translated from C to PHP by Thomas Bruederli <roundcube@gmail.com>
 *  Code extracted from the RoundCube Webmail (http://roundcube.net) project,
 *    SVN revision 1757
 *  The RoundCube project is released under the GPL (version 2)
 *
 * Copyright 2008-2014 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2000 Edmund Grimley Evans <edmundo@rano.org>
 * @copyright 2008-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * Allows conversions between UTF-8 and UTF7-IMAP (RFC 3501 [5.1.3]).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2000 Edmund Grimley Evans <edmundo@rano.org>
 * @copyright 2008-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */
class Horde_Imap_Client_Utf7imap
{
    /**
     * Lookup table for conversion.
     *
     * @var array
     */
    private static $_index64 = array(
        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
        -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, 63, -1, -1, -1,
        52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -1, -1, -1, -1, -1, -1,
        -1,  0,  1,  2,  3,  4,  5,  6,  7,  8,  9, 10, 11, 12, 13, 14,
        15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1, -1, -1, -1,
        -1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
        41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1, -1, -1
    );

    /**
     * Lookup table for conversion.
     *
     * @var array
     */
    private static $_base64 = array(
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
        'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b',
        'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
        'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3',
        '4', '5', '6', '7', '8', '9', '+', ','
    );

    /**
     * Is mbstring extension available?
     *
     * @var array
     */
    private static $_mbstring = null;

    /**
     * Convert a string from UTF7-IMAP to UTF-8.
     *
     * @param string $str  The UTF7-IMAP string.
     *
     * @return string  The converted UTF-8 string.
     * @throws Horde_Imap_Client_Exception
     */
    public static function Utf7ImapToUtf8($str)
    {
        if ($str instanceof Horde_Imap_Client_Mailbox) {
            return $str->utf8;
        }

        $str = strval($str);

        /* Try mbstring, if available, which should be faster. Don't use the
         * IMAP utf7_* functions because they are known to be buggy. */
        if (is_null(self::$_mbstring)) {
            self::$_mbstring = extension_loaded('mbstring');
        }
        if (self::$_mbstring) {
            return @mb_convert_encoding($str, 'UTF-8', 'UTF7-IMAP');
        }

        $p = '';
        $ptr = &self::$_index64;

        for ($i = 0, $u7len = strlen($str); $u7len > 0; ++$i, --$u7len) {
            $u7 = $str[$i];
            if ($u7 === '&') {
                $u7 = $str[++$i];
                if (--$u7len && ($u7 === '-')) {
                    $p .= '&';
                    continue;
                }

                $ch = 0;
                $k = 10;
                for (; $u7len > 0; ++$i, --$u7len) {
                    $u7 = $str[$i];

                    if ((ord($u7) & 0x80) || ($b = $ptr[ord($u7)]) === -1) {
                        break;
                    }

                    if ($k > 0) {
                        $ch |= $b << $k;
                        $k -= 6;
                    } else {
                        $ch |= $b >> (-$k);
                        if ($ch < 0x80) {
                            /* Printable US-ASCII */
                            if ((0x20 <= $ch) && ($ch < 0x7f)) {
                                throw new Horde_Imap_Client_Exception(
                                    Horde_Imap_Client_Translation::r("Error converting UTF7-IMAP string."),
                                    Horde_Imap_Client_Exception::UTF7IMAP_CONVERSION
                                );
                            }
                            $p .= chr($ch);
                        } else if ($ch < 0x800) {
                            $p .= chr(0xc0 | ($ch >> 6)) .
                                  chr(0x80 | ($ch & 0x3f));
                        } else {
                            $p .= chr(0xe0 | ($ch >> 12)) .
                                  chr(0x80 | (($ch >> 6) & 0x3f)) .
                                  chr(0x80 | ($ch & 0x3f));
                        }

                        $ch = ($b << (16 + $k)) & 0xffff;
                        $k += 10;
                    }
                }

                /* Non-zero or too many extra bits -OR-
                 * Base64 not properly terminated -OR-
                 * Adjacent Base64 sections. */
                if (($ch || ($k < 6)) ||
                    (!$u7len || $u7 !== '-') ||
                    (($u7len > 2) &&
                     ($str[$i + 1] === '&') &&
                     ($str[$i + 2] !== '-'))) {
                    throw new Horde_Imap_Client_Exception(
                        Horde_Imap_Client_Translation::r("Error converting UTF7-IMAP string."),
                        Horde_Imap_Client_Exception::UTF7IMAP_CONVERSION
                    );
                }
            } elseif ((ord($u7) < 0x20) || (ord($u7) >= 0x7f)) {
                /* Not printable US-ASCII */
                throw new Horde_Imap_Client_Exception(
                    Horde_Imap_Client_Translation::r("Error converting UTF7-IMAP string."),
                    Horde_Imap_Client_Exception::UTF7IMAP_CONVERSION
                );
            } else {
                $p .= $u7;
            }
        }

        return $p;
    }

    /**
     * Convert a string from UTF-8 to UTF7-IMAP.
     *
     * @param string $str     The UTF-8 string.
     * @param boolean $force  Assume $str is UTF-8 (no-autodetection)? If
     *                        false, attempts to auto-detect if string is
     *                        already in UTF7-IMAP.
     *
     * @return string  The converted UTF7-IMAP string.
     * @throws Horde_Imap_Client_Exception
     */
    public static function Utf8ToUtf7Imap($str, $force = true)
    {
        if ($str instanceof Horde_Imap_Client_Mailbox) {
            return $str->utf7imap;
        }

        $str = strval($str);

        /* No need to do conversion if all chars are in US-ASCII range or if
         * no ampersand is present. But will assume that an already encoded
         * ampersand means string is in UTF7-IMAP already. */
        if (!$force &&
            !preg_match('/[\x80-\xff]|&$|&(?![,+A-Za-z0-9]*-)/', $str)) {
            return $str;
        }

        /* Try mbstring, if available, which should be faster. Don't use the
         * IMAP utf7_* functions because they are known to be buggy. */
        if (is_null(self::$_mbstring)) {
            self::$_mbstring = extension_loaded('mbstring');
        }
        if (self::$_mbstring) {
            return @mb_convert_encoding($str, 'UTF7-IMAP', 'UTF-8');
        }

        $u8len = strlen($str);
        $i = 0;
        $base64 = false;
        $p = '';
        $ptr = &self::$_base64;

        while ($u8len) {
            $u8 = $str[$i];
            $c = ord($u8);

            if ($c < 0x80) {
                $ch = $c;
                $n = 0;
            } elseif ($c < 0xc2) {
                throw new Horde_Imap_Client_Exception(
                    Horde_Imap_Client_Translation::r("Error converting UTF7-IMAP string."),
                    Horde_Imap_Client_Exception::UTF7IMAP_CONVERSION
                );
            } elseif ($c < 0xe0) {
                $ch = $c & 0x1f;
                $n = 1;
            } elseif ($c < 0xf0) {
                $ch = $c & 0x0f;
                $n = 2;
            } elseif ($c < 0xf8) {
                $ch = $c & 0x07;
                $n = 3;
            } elseif ($c < 0xfc) {
                $ch = $c & 0x03;
                $n = 4;
            } elseif ($c < 0xfe) {
                $ch = $c & 0x01;
                $n = 5;
            } else {
                throw new Horde_Imap_Client_Exception(
                    Horde_Imap_Client_Translation::r("Error converting UTF7-IMAP string."),
                    Horde_Imap_Client_Exception::UTF7IMAP_CONVERSION
                );
            }

            if ($n > --$u8len) {
                throw new Horde_Imap_Client_Exception(
                    Horde_Imap_Client_Translation::r("Error converting UTF7-IMAP string."),
                    Horde_Imap_Client_Exception::UTF7IMAP_CONVERSION
                );
            }

            ++$i;

            for ($j = 0; $j < $n; ++$j) {
                $o = ord($str[$i + $j]);
                if (($o & 0xc0) !== 0x80) {
                    throw new Horde_Imap_Client_Exception(
                        Horde_Imap_Client_Translation::r("Error converting UTF7-IMAP string."),
                        Horde_Imap_Client_Exception::UTF7IMAP_CONVERSION
                    );
                }
                $ch = ($ch << 6) | ($o & 0x3f);
            }

            if (($n > 1) && !($ch >> ($n * 5 + 1))) {
                throw new Horde_Imap_Client_Exception(
                    Horde_Imap_Client_Translation::r("Error converting UTF7-IMAP string."),
                    Horde_Imap_Client_Exception::UTF7IMAP_CONVERSION
                );
            }

            $i += $n;
            $u8len -= $n;

            if (($ch < 0x20) || ($ch >= 0x7f)) {
                if (!$base64) {
                    $p .= '&';
                    $base64 = true;
                    $b = 0;
                    $k = 10;
                }

                if ($ch & ~0xffff) {
                    $ch = 0xfffe;
                }

                $p .= $ptr[($b | $ch >> $k)];
                $k -= 6;
                for (; $k >= 0; $k -= 6) {
                    $p .= $ptr[(($ch >> $k) & 0x3f)];
                }

                $b = ($ch << (-$k)) & 0x3f;
                $k += 16;
            } else {
                if ($base64) {
                    if ($k > 10) {
                        $p .= $ptr[$b];
                    }
                    $p .= '-';
                    $base64 = false;
                }

                $p .= chr($ch);
                if (chr($ch) === '&') {
                    $p .= '-';
                }
            }
        }

        if ($base64) {
            if ($k > 10) {
                $p .= $ptr[$b];
            }
            $p .= '-';
        }

        return $p;
    }

}
