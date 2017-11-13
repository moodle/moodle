<?php
/**
 * Copyright 1999-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 1999-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 */

/**
 * Provide methods for dealing with MIME encoding (RFC 2045-2049);
 *
 * @author    Chuck Hagenbuch <chuck@horde.org>
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 1999-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 */
class Horde_Mime
{
    /**
     * The RFC defined EOL string.
     *
     * @var string
     */
    const EOL = "\r\n";

    /**
     * Use windows-1252 charset when decoding ISO-8859-1 data?
     * HTML 5 requires this behavior, so it is the default.
     *
     * @var boolean
     */
    public static $decodeWindows1252 = true;

    /**
     * Determines if a string contains 8-bit (non US-ASCII) characters.
     *
     * @param string $string   The string to check.
     * @param string $charset  The charset of the string. Defaults to
     *                         US-ASCII. (@deprecated)
     *
     * @return boolean  True if string contains non 7-bit characters.
     */
    public static function is8bit($string, $charset = null)
    {
        $string = strval($string);
        for ($i = 0, $len = strlen($string); $i < $len; ++$i) {
            if (ord($string[$i]) > 127) {
                return true;
            }
        }

        return false;
    }

    /**
     * MIME encodes a string (RFC 2047).
     *
     * @param string $text     The text to encode (UTF-8).
     * @param string $charset  The character set to encode to.
     *
     * @return string  The MIME encoded string (US-ASCII).
     */
    public static function encode($text, $charset = 'UTF-8')
    {
        $charset = Horde_String::lower($charset);
        $text = Horde_String::convertCharset($text, 'UTF-8', $charset);

        $encoded = $is_encoded = false;
        $lwsp = $word = null;
        $out = '';

        /* 0 = word unencoded
         * 1 = word encoded
         * 2 = spaces */
        $parts = array();

        /* Tokenize string. */
        for ($i = 0, $len = strlen($text); $i < $len; ++$i) {
            switch ($text[$i]) {
            case "\t":
            case "\r":
            case "\n":
                if (!is_null($word)) {
                    $parts[] = array(intval($encoded), $word, $i - $word);
                    $word = null;
                } elseif (!is_null($lwsp)) {
                    $parts[] = array(2, $lwsp, $i - $lwsp);
                    $lwsp = null;
                }

                $parts[] = array(0, $i, 1);
                break;

            case ' ':
                if (!is_null($word)) {
                    $parts[] = array(intval($encoded), $word, $i - $word);
                    $word = null;
                }
                if (is_null($lwsp)) {
                    $lwsp = $i;
                }
                break;

            default:
                if (is_null($word)) {
                    $encoded = false;
                    $word = $i;
                    if (!is_null($lwsp)) {
                        $parts[] = array(2, $lwsp, $i - $lwsp);
                        $lwsp = null;
                    }

                    /* Check for MIME encoding delimiter. Encode it if
                     * found. */
                    if (($text[$i] === '=') &&
                        (($i + 1) < $len) &&
                        ($text[$i +1] === '?')) {
                        ++$i;
                        $encoded = $is_encoded = true;
                    }
                }

                /* Check for 8-bit characters or control characters. */
                if (!$encoded) {
                    $c = ord($text[$i]);
                    if ($encoded = (($c & 0x80) || ($c < 32))) {
                        $is_encoded = true;
                    }
                }
                break;
            }
        }

        if (!$is_encoded) {
            return $text;
        }

        if (is_null($lwsp)) {
            $parts[] = array(intval($encoded), $word, $len);
        } else {
            $parts[] = array(2, $lwsp, $len);
        }

        /* Combine parts into MIME encoded string. */
        for ($i = 0, $cnt = count($parts); $i < $cnt; ++$i) {
            $val = $parts[$i];

            switch ($val[0]) {
            case 0:
            case 2:
                $out .= substr($text, $val[1], $val[2]);
                break;

            case 1:
                $j = $i;
                for ($k = $i + 1; $k < $cnt; ++$k) {
                    switch ($parts[$k][0]) {
                    case 0:
                        break 2;

                    case 1:
                        $i = $k;
                        break;
                    }
                }

                $encode = '';
                for (; $j <= $i; ++$j) {
                    $encode .= substr($text, $parts[$j][1], $parts[$j][2]);
                }

                $delim = '=?' . $charset . '?b?';
                $e_parts = explode(
                    self::EOL,
                    rtrim(
                        chunk_split(
                            base64_encode($encode),
                            /* strlen($delim) + 2 = space taken by MIME
                             * delimiter */
                            intval((75 - strlen($delim) + 2) / 4) * 4
                        )
                    )
                );

                $tmp = array();
                foreach ($e_parts as $val) {
                    $tmp[] = $delim . $val . '?=';
                }

                $out .= implode(' ', $tmp);
                break;
            }
        }

        return rtrim($out);
    }

    /**
     * Decodes a MIME encoded (RFC 2047) string.
     *
     * @param string $string  The MIME encoded text.
     *
     * @return string  The decoded text.
     */
    public static function decode($string)
    {
        $old_pos = 0;
        $out = '';

        while (($pos = strpos($string, '=?', $old_pos)) !== false) {
            /* Save any preceding text, if it is not LWSP between two
             * encoded words. */
            $pre = substr($string, $old_pos, $pos - $old_pos);
            if (!$old_pos ||
                (strspn($pre, " \t\n\r") != strlen($pre))) {
                $out .= $pre;
            }

            /* Search for first delimiting question mark (charset). */
            if (($d1 = strpos($string, '?', $pos + 2)) === false) {
                break;
            }

            $orig_charset = substr($string, $pos + 2, $d1 - $pos - 2);
            if (self::$decodeWindows1252 &&
                (Horde_String::lower($orig_charset) == 'iso-8859-1')) {
                $orig_charset = 'windows-1252';
            }

            /* Search for second delimiting question mark (encoding). */
            if (($d2 = strpos($string, '?', $d1 + 1)) === false) {
                break;
            }

            $encoding = substr($string, $d1 + 1, $d2 - $d1 - 1);

            /* Search for end of encoded data. */
            if (($end = strpos($string, '?=', $d2 + 1)) === false) {
                break;
            }

            $encoded_text = substr($string, $d2 + 1, $end - $d2 - 1);

            switch ($encoding) {
            case 'Q':
            case 'q':
                $out .= Horde_String::convertCharset(
                    quoted_printable_decode(
                        str_replace('_', ' ', $encoded_text)
                    ),
                    $orig_charset,
                    'UTF-8'
                );
            break;

            case 'B':
            case 'b':
                $out .= Horde_String::convertCharset(
                    base64_decode($encoded_text),
                    $orig_charset,
                    'UTF-8'
                );
            break;

            default:
                // Ignore unknown encoding.
                break;
            }

            $old_pos = $end + 2;
        }

        return $out . substr($string, $old_pos);
    }

    /* Deprecated methods. */

    /**
     * @deprecated  Use Horde_Mime_Headers_MessageId::create() instead.
     */
    public static function generateMessageId()
    {
        return Horde_Mime_Headers_MessageId::create()->value;
    }

    /**
     * @deprecated  Use Horde_Mime_Uudecode instead.
     */
    public static function uudecode($input)
    {
        $uudecode = new Horde_Mime_Uudecode($input);
        return iterator_to_array($uudecode);
    }

    /**
     * @deprecated
     */
    public static $brokenRFC2231 = false;

    /**
     * @deprecated
     */
    const MIME_PARAM_QUOTED = '/[\x01-\x20\x22\x28\x29\x2c\x2f\x3a-\x40\x5b-\x5d]/';

    /**
     * @deprecated  Use Horde_Mime_Headers_ContentParam#encode() instead.
     */
    public static function encodeParam($name, $val, array $opts = array())
    {
        $cp = new Horde_Mime_Headers_ContentParam(
            'UNUSED',
            array($name => $val)
        );

        return $cp->encode(array_merge(array(
            'broken_rfc2231' => self::$brokenRFC2231
        ), $opts));
    }

    /**
     * @deprecated  Use Horde_Mime_Headers_ELement_ContentParam instead.
     */
    public static function decodeParam($type, $data)
    {
        $cp = new Horde_Mime_Headers_ContentParam(
            'UNUSED',
            $data
        );

        if (strlen($cp->value)) {
            $val = $cp->value;
        } else {
            $val = (Horde_String::lower($type) == 'content-type')
                ? 'text/plain'
                : 'attachment';
        }

        return array(
            'params' => $cp->params,
            'val' => $val
        );
    }

    /**
     * @deprecated  Use Horde_Mime_Id instead.
     */
    public static function mimeIdArithmetic($id, $action, $options = array())
    {
        $id_ob = new Horde_Mime_Id($id);

        switch ($action) {
        case 'down':
            $action = $id_ob::ID_DOWN;
            break;

        case 'next':
            $action = $id_ob::ID_NEXT;
            break;

        case 'prev':
            $action = $id_ob::ID_PREV;
            break;

        case 'up':
            $action = $id_ob::ID_UP;
            break;
        }

        return $id_ob->idArithmetic($action, $options);
    }

    /**
     * @deprecated  Use Horde_Mime_Id instead.
     */
    public static function isChild($base, $id)
    {
        $id_ob = new Horde_Mime_Id($base);
        return $id_ob->isChild($id);
    }

    /**
     * @deprecated  Use Horde_Mime_QuotedPrintable instead.
     */
    public static function quotedPrintableEncode($text, $eol = self::EOL,
                                                 $wrap = 76)
    {
        return Horde_Mime_QuotedPrintable::encode($text, $eol, $wrap);
    }

}
