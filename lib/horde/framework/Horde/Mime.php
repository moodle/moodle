<?php
/**
 * The Horde_Mime:: class provides methods for dealing with various MIME (see,
 * e.g., RFC 2045-2049; 2183; 2231) standards.
 *
 * -----
 *
 * This file contains code adapted from PEAR's Mail_mimeDecode library (v1.5).
 *
 *   http://pear.php.net/package/Mail_mime
 *
 * This code appears in Horde_Mime::decodeParam().
 *
 * This code was originally released under this license:
 *
 * LICENSE: This LICENSE is in the BSD license style.
 * Copyright (c) 2002-2003, Richard Heyes <richard@phpguru.org>
 * Copyright (c) 2003-2006, PEAR <pear-group@php.net>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or
 * without modification, are permitted provided that the following
 * conditions are met:
 *
 * - Redistributions of source code must retain the above copyright
 *   notice, this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright
 *   notice, this list of conditions and the following disclaimer in the
 *   documentation and/or other materials provided with the distribution.
 * - Neither the name of the authors, nor the names of its contributors
 *   may be used to endorse or promote products derived from this
 *   software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF
 * THE POSSIBILITY OF SUCH DAMAGE.
 *
 * -----
 *
 * This file contains code adapted from PEAR's PHP_Compat library (v1.6.0a3).
 *
 *   http://pear.php.net/package/PHP_Compat
 *
 * This code appears in Horde_Mime::_uudecode().
 *
 * This code was originally released under the LGPL 2.1
 *
 * -----
 *
 * Copyright 1999-2014 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Chuck Hagenbuch <chuck@horde.org>
 * @author   Michael Slusarz <slusarz@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Mime
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
     * The list of characters required to be quoted in MIME parameters
     * (regular expression).
     *
     * @since 2.1.0
     *
     * @var string
     */
    const MIME_PARAM_QUOTED = '/[\x01-\x20\x22\x28\x29\x2c\x2f\x3a-\x40\x5b-\x5d]/';

    /**
     * Attempt to work around non RFC 2231-compliant MUAs by generating both
     * a RFC 2047-like parameter name and also the correct RFC 2231
     * parameter.  See:
     * http://lists.horde.org/archives/dev/Week-of-Mon-20040426/014240.html
     *
     * @var boolean
     */
    static public $brokenRFC2231 = false;

    /**
     * Use windows-1252 charset when decoding ISO-8859-1 data?
     *
     * @var boolean
     */
    static public $decodeWindows1252 = false;

    /**
     * Determines if a string contains 8-bit (non US-ASCII) characters.
     *
     * @param string $string   The string to check.
     * @param string $charset  The charset of the string. Defaults to
     *                         US-ASCII.
     *
     * @return boolean  True if string contains non US-ASCII characters.
     */
    static public function is8bit($string, $charset = null)
    {
        return ($string != Horde_String::convertCharset($string, $charset, 'US-ASCII'));
    }

    /**
     * MIME encodes a string (RFC 2047).
     *
     * @param string $text     The text to encode (UTF-8).
     * @param string $charset  The character set to encode to.
     *
     * @return string  The MIME encoded string (US-ASCII).
     */
    static public function encode($text, $charset = 'UTF-8')
    {
        /* The null character is valid US-ASCII, but was removed from the
         * allowed e-mail header characters in RFC 2822. */
        if (!self::is8bit($text, 'UTF-8') && (strpos($text, null) === false)) {
            return $text;
        }

        $charset = Horde_String::lower($charset);
        $text = Horde_String::convertCharset($text, 'UTF-8', $charset);

        /* Get the list of elements in the string. */
        $size = preg_match_all('/([^\s]+)([\s]*)/', $text, $matches, PREG_SET_ORDER);

        $line = '';

        /* Return if nothing needs to be encoded. */
        foreach ($matches as $key => $val) {
            if (self::is8bit($val[1], $charset)) {
                if ((($key + 1) < $size) &&
                    self::is8bit($matches[$key + 1][1], $charset)) {
                    $line .= self::_encode($val[1] . $val[2], $charset) . ' ';
                } else {
                    $line .= self::_encode($val[1], $charset) . $val[2];
                }
            } else {
                $line .= $val[1] . $val[2];
            }
        }

        return rtrim($line);
    }

    /**
     * Internal helper function to MIME encode a string.
     *
     * @param string $text     The text to encode.
     * @param string $charset  The character set of the text.
     *
     * @return string  The MIME encoded text.
     */
    static protected function _encode($text, $charset)
    {
        $encoded = trim(base64_encode($text));
        $c_size = strlen($charset) + 7;

        if ((strlen($encoded) + $c_size) > 75) {
            $parts = explode(self::EOL, rtrim(chunk_split($encoded, intval((75 - $c_size) / 4) * 4)));
        } else {
            $parts[] = $encoded;
        }

        $p_size = count($parts);
        $out = '';

        foreach ($parts as $key => $val) {
            $out .= '=?' . $charset . '?b?' . $val . '?=';
            if ($p_size > $key + 1) {
                /* RFC 2047 [2]: no encoded word can be more than 75
                 * characters long. If longer, you must split the word with
                 * CRLF SPACE. */
                $out .= self::EOL . ' ';
            }
        }

        return $out;
    }

    /**
     * Encodes a line via quoted-printable encoding.
     *
     * @param string $text   The text to encode (UTF-8).
     * @param string $eol    The EOL sequence to use.
     * @param integer $wrap  Wrap a line at this many characters.
     *
     * @return string  The quoted-printable encoded string.
     */
    static public function quotedPrintableEncode($text, $eol = self::EOL,
                                                 $wrap = 76)
    {
        $curr_length = 0;
        $output = '';

        /* We need to go character by character through the data. */
        for ($i = 0, $length = strlen($text); $i < $length; ++$i) {
            $char = $text[$i];

            /* If we have reached the end of the line, reset counters. */
            if ($char == "\n") {
                $output .= $eol;
                $curr_length = 0;
                continue;
            } elseif ($char == "\r") {
                continue;
            }

            /* Spaces or tabs at the end of the line are NOT allowed. Also,
             * ASCII characters below 32 or above 126 AND 61 must be
             * encoded. */
            $ascii = ord($char);
            if ((($ascii === 32) &&
                 ($i + 1 != $length) &&
                 (($text[$i + 1] == "\n") || ($text[$i + 1] == "\r"))) ||
                (($ascii < 32) || ($ascii > 126) || ($ascii === 61))) {
                $char_len = 3;
                $char = '=' . Horde_String::upper(sprintf('%02s', dechex($ascii)));
            } else {
                $char_len = 1;
            }

            /* Lines must be $wrap characters or less. */
            $curr_length += $char_len;
            if ($curr_length > $wrap) {
                $output .= '=' . $eol;
                $curr_length = $char_len;
            }
            $output .= $char;
        }

        return $output;
    }

    /**
     * Decodes a MIME encoded (RFC 2047) string.
     *
     * @param string $string  The MIME encoded text.
     *
     * @return string  The decoded text.
     */
    static public function decode($string)
    {
        /* Take out any spaces between multiple encoded words. */
        $string = preg_replace('|\?=\s+=\?|', '?==?', $string);

        $out = '';
        $old_pos = 0;

        while (($pos = strpos($string, '=?', $old_pos)) !== false) {
            /* Save any preceding text. */
            $out .= substr($string, $old_pos, $pos - $old_pos);

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
                    preg_replace_callback(
                        '/=([0-9a-f]{2})/i',
                        function($ord) {
                            return chr(hexdec($ord[1]));
                        },
                        str_replace('_', ' ', $encoded_text)),
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

    /**
     * Encodes a MIME parameter string pursuant to RFC 2183 & 2231
     * (Content-Type and Content-Disposition headers).
     *
     * @param string $name  The parameter name.
     * @param string $val   The parameter value (UTF-8).
     * @param array $opts   Additional options:
     *   - charset: (string) The charset to encode to.
     *              DEFAULT: UTF-8
     *   - lang: (string) The language to use when encoding.
     *           DEFAULT: None specified
     *
     * @return array  The encoded parameter string (US-ASCII).
     */
    static public function encodeParam($name, $val, array $opts = array())
    {
        $curr = 0;
        $encode = $wrap = false;
        $output = array();

        $charset = isset($opts['charset'])
            ? $opts['charset']
            : 'UTF-8';

        // 2 = '=', ';'
        $pre_len = strlen($name) + 2;

        /* Several possibilities:
         *   - String is ASCII. Output as ASCII (duh).
         *   - Language information has been provided. We MUST encode output
         *     to include this information.
         *   - String is non-ASCII, but can losslessly translate to ASCII.
         *     Output as ASCII (most efficient).
         *   - String is in non-ASCII, but doesn't losslessly translate to
         *     ASCII. MUST encode output (duh). */
        if (empty($opts['lang']) && !self::is8bit($val, 'UTF-8')) {
            $string = $val;
        } else {
            $cval = Horde_String::convertCharset($val, 'UTF-8', $charset);
            $string = Horde_String::lower($charset) . '\'' . (empty($opts['lang']) ? '' : Horde_String::lower($opts['lang'])) . '\'' . rawurlencode($cval);
            $encode = true;
            /* Account for trailing '*'. */
            ++$pre_len;
        }

        if (($pre_len + strlen($string)) > 75) {
            /* Account for continuation '*'. */
            ++$pre_len;
            $wrap = true;

            while ($string) {
                $chunk = 75 - $pre_len - strlen($curr);
                $pos = min($chunk, strlen($string) - 1);

                /* Don't split in the middle of an encoded char. */
                if (($chunk == $pos) && ($pos > 2)) {
                    for ($i = 0; $i <= 2; ++$i) {
                        if ($string[$pos - $i] == '%') {
                            $pos -= $i + 1;
                            break;
                        }
                    }
                }

                $lines[] = substr($string, 0, $pos + 1);
                $string = substr($string, $pos + 1);
                ++$curr;
            }
        } else {
            $lines = array($string);
        }

        foreach ($lines as $i => $line) {
            $output[$name . (($wrap) ? ('*' . $i) : '') . (($encode) ? '*' : '')] = $line;
        }

        if (self::$brokenRFC2231 && !isset($output[$name])) {
            $output = array_merge(array(
                $name => self::encode($val, $charset)
            ), $output);
        }

        /* Escape certain characters in params (See RFC 2045 [Appendix A]).
         * Must be quoted-string if one of these exists.
         * Forbidden: SPACE, CTLs, ()<>@,;:\"/[]?= */
        foreach ($output as $k => $v) {
            if (preg_match(self::MIME_PARAM_QUOTED, $v)) {
                $output[$k] = '"' . addcslashes($v, '\\"') . '"';
            }
        }

        return $output;
    }

    /**
     * Decodes a MIME parameter string pursuant to RFC 2183 & 2231
     * (Content-Type and Content-Disposition headers).
     *
     * @param string $type  Either 'Content-Type' or 'Content-Disposition'
     *                      (case-insensitive).
     * @param mixed $data   The text of the header or an array of param name
     *                      => param values.
     *
     * @return array  An array with the following entries (all strings in
     *                UTF-8):
     *   - params: (array) The header's parameter values.
     *   - val: (string) The header's "base" value.
     */
    static public function decodeParam($type, $data)
    {
        $convert = array();
        $ret = array('params' => array(), 'val' => '');
        $splitRegex = '/([^;\'"]*[\'"]([^\'"]*([^\'"]*)*)[\'"][^;\'"]*|([^;]+))(;|$)/';
        $type = Horde_String::lower($type);

        if (is_array($data)) {
            // Use dummy base values
            $ret['val'] = ($type == 'content-type')
                ? 'text/plain'
                : 'attachment';
            $params = $data;
        } else {
            /* This code was adapted from PEAR's Mail_mimeDecode::. */
            if (($pos = strpos($data, ';')) === false) {
                $ret['val'] = trim($data);
                return $ret;
            }

            $ret['val'] = trim(substr($data, 0, $pos));
            $data = trim(substr($data, ++$pos));
            $params = $tmp = array();

            if (strlen($data) > 0) {
                /* This splits on a semi-colon, if there's no preceeding
                 * backslash. */
                preg_match_all($splitRegex, $data, $matches);

                for ($i = 0, $cnt = count($matches[0]); $i < $cnt; ++$i) {
                    $param = $matches[0][$i];
                    while (substr($param, -2) == '\;') {
                        $param .= $matches[0][++$i];
                    }
                    $tmp[] = $param;
                }

                for ($i = 0, $cnt = count($tmp); $i < $cnt; ++$i) {
                    $pos = strpos($tmp[$i], '=');
                    $p_name = trim(substr($tmp[$i], 0, $pos), "'\";\t\\ ");
                    $p_val = trim(str_replace('\;', ';', substr($tmp[$i], $pos + 1)), "'\";\t\\ ");
                    if (strlen($p_val) && ($p_val[0] == '"')) {
                        $p_val = substr($p_val, 1, -1);
                    }

                    $params[$p_name] = $p_val;
                }
            }
            /* End of code adapted from PEAR's Mail_mimeDecode::. */
        }

        /* Sort the params list. Prevents us from having to manually keep
         * track of continuation values below. */
        uksort($params, 'strnatcasecmp');

        foreach ($params as $name => $val) {
            /* Asterisk at end indicates encoded value. */
            if (substr($name, -1) == '*') {
                $name = substr($name, 0, -1);
                $encode = true;
            } else {
                $encode = false;
            }

            /* This asterisk indicates continuation parameter. */
            if (($pos = strrpos($name, '*')) !== false) {
                $name = substr($name, 0, $pos);
            }

            if (!isset($ret['params'][$name]) ||
                ($encode && !isset($convert[$name]))) {
                $ret['params'][$name] = '';
            }

            $ret['params'][$name] .= $val;

            if ($encode) {
                $convert[$name] = true;
            }
        }

        foreach (array_keys($convert) as $name) {
            $val = $ret['params'][$name];
            $quote = strpos($val, "'");
            $orig_charset = substr($val, 0, $quote);
            if (self::$decodeWindows1252 &&
                (Horde_String::lower($orig_charset) == 'iso-8859-1')) {
                $orig_charset = 'windows-1252';
            }
            /* Ignore language. */
            $quote = strpos($val, "'", $quote + 1);
            substr($val, $quote + 1);
            $ret['params'][$name] = Horde_String::convertCharset(urldecode(substr($val, $quote + 1)), $orig_charset, 'UTF-8');
        }

        /* MIME parameters are supposed to be encoded via RFC 2231, but many
         * mailers do RFC 2045 encoding instead. However, if we see at least
         * one RFC 2231 encoding, then assume the sending mailer knew what
         * it was doing. */
        if (empty($convert)) {
            foreach (array_diff(array_keys($ret['params']), array_keys($convert)) as $name) {
                $ret['params'][$name] = self::decode($ret['params'][$name]);
            }
        }

        return $ret;
    }

    /**
     * Generates a Message-ID string conforming to RFC 2822 [3.6.4] and the
     * standards outlined in 'draft-ietf-usefor-message-id-01.txt'.
     *
     * @param string  A message ID string.
     */
    static public function generateMessageId()
    {
        return '<' . strval(new Horde_Support_Guid(array('prefix' => 'Horde'))) . '>';
    }

    /**
     * Performs MIME ID "arithmetic" on a given ID.
     *
     * @param string $id      The MIME ID string.
     * @param string $action  One of the following:
     *   - down: ID of child. Note: down will first traverse to "$id.0" if
     *           given an ID *NOT* of the form "$id.0". If given an ID of the
     *           form "$id.0", down will traverse to "$id.1". This behavior
     *           can be avoided if 'norfc822' option is set.
     *   - next: ID of next sibling.
     *   - prev: ID of previous sibling.
     *   - up: ID of parent. Note: up will first traverse to "$id.0" if
     *         given an ID *NOT* of the form "$id.0". If given an ID of the
     *         form "$id.0", down will traverse to "$id". This behavior can be
     *         avoided if 'norfc822' option is set.
     * @param array $options  Additional options:
     *   - count: (integer) How many levels to traverse.
     *            DEFAULT: 1
     *   - norfc822: (boolean) Don't traverse rfc822 sub-levels
     *               DEFAULT: false
     *
     * @return mixed  The resulting ID string, or null if that ID can not
     *                exist.
     */
    static public function mimeIdArithmetic($id, $action, $options = array())
    {
        $pos = strrpos($id, '.');
        $end = ($pos === false) ? $id : substr($id, $pos + 1);

        switch ($action) {
        case 'down':
            if ($end == '0') {
                $id = ($pos === false) ? 1 : substr_replace($id, '1', $pos + 1);
            } else {
                $id .= empty($options['norfc822']) ? '.0' : '.1';
            }
            break;

        case 'next':
            ++$end;
            $id = ($pos === false) ? $end : substr_replace($id, $end, $pos + 1);
            break;

        case 'prev':
            if (($end == '0') ||
                (empty($options['norfc822']) && ($end == '1'))) {
                $id = null;
            } elseif ($pos === false) {
                $id = --$end;
            } else {
                $id = substr_replace($id, --$end, $pos + 1);
            }
            break;

        case 'up':
            if ($pos === false) {
                $id = ($end == '0') ? null : '0';
            } elseif (!empty($options['norfc822']) || ($end == '0')) {
                $id = substr($id, 0, $pos);
            } else {
                $id = substr_replace($id, '0', $pos + 1);
            }
            break;
        }

        return (!is_null($id) && !empty($options['count']) && --$options['count'])
            ? self::mimeIdArithmetic($id, $action, $options)
            : $id;
    }

    /**
     * Determines if a given MIME ID lives underneath a base ID.
     *
     * @param string $base  The base MIME ID.
     * @param string $id    The MIME ID to query.
     *
     * @return boolean  Whether $id lives underneath $base.
     */
    static public function isChild($base, $id)
    {
        $base = (substr($base, -2) == '.0')
            ? substr($base, 0, -1)
            : rtrim($base, '.') . '.';

        return ((($base == 0) && ($id != 0)) ||
                (strpos(strval($id), strval($base)) === 0));
    }

    /**
     * Scans $input for uuencoded data and converts it to unencoded data.
     *
     * @param string $input  The input data
     *
     * @return array  A list of arrays, with each array corresponding to
     *                a file in the input and containing the following keys:
     *   - data: (string) Unencoded data.
     *   - name: (string) Filename.
     *   - perms: (string) Octal permissions.
     */
    static public function uudecode($input)
    {
        $data = array();

        /* Find all uuencoded sections. */
        if (preg_match_all("/begin ([0-7]{3}) (.+)\r?\n(.+)\r?\nend/Us", $input, $matches, PREG_SET_ORDER)) {
            reset($matches);
            while (list(,$v) = each($matches)) {
                $data[] = array(
                    'data' => self::_uudecode($v[3]),
                    'name' => $v[2],
                    'perm' => $v[1]
                );
            }
        }

        return $data;
    }

    /**
     * PHP 5's built-in convert_uudecode() is broken. Need this wrapper.
     *
     * @param string $input  UUencoded input.
     *
     * @return string  Decoded string.
     */
    static protected function _uudecode($input)
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

}
