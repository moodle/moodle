<?php

/**
 +-----------------------------------------------------------------------+
 | This file is part of the Roundcube Webmail client                     |
 |                                                                       |
 | Copyright (C) The Roundcube Dev Team                                  |
 | Copyright (C) Kolab Systems AG                                        |
 |                                                                       |
 | Licensed under the GNU General Public License version 3 or            |
 | any later version with exceptions for skins & plugins.                |
 | See the README file for a full license statement.                     |
 |                                                                       |
 | PURPOSE:                                                              |
 |   MIME message parsing utilities                                      |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <roundcube@gmail.com>                        |
 | Author: Aleksander Machniak <alec@alec.pl>                            |
 +-----------------------------------------------------------------------+
*/

/**
 * Class for parsing MIME messages
 *
 * @package    Framework
 * @subpackage Storage
 */
class rcube_mime
{
    private static $default_charset;


    /**
     * Object constructor.
     */
    function __construct($default_charset = null)
    {
        self::$default_charset = $default_charset;
    }

    /**
     * Returns message/object character set name
     *
     * @return string Character set name
     */
    public static function get_charset()
    {
        if (self::$default_charset) {
            return self::$default_charset;
        }

        if ($charset = rcube::get_instance()->config->get('default_charset')) {
            return $charset;
        }

        return RCUBE_CHARSET;
    }

    /**
     * Parse the given raw message source and return a structure
     * of rcube_message_part objects.
     *
     * It makes use of the rcube_mime_decode library
     *
     * @param string $raw_body The message source
     *
     * @return object rcube_message_part The message structure
     */
    public static function parse_message($raw_body)
    {
        $conf = [
            'include_bodies'  => true,
            'decode_bodies'   => true,
            'decode_headers'  => false,
            'default_charset' => self::get_charset(),
        ];

        $mime = new rcube_mime_decode($conf);

        return $mime->decode($raw_body);
    }

    /**
     * Split an address list into a structured array list
     *
     * @param string|array $input    Input string (or list of strings)
     * @param int          $max      List only this number of addresses
     * @param bool         $decode   Decode address strings
     * @param string       $fallback Fallback charset if none specified
     * @param bool         $addronly Return flat array with e-mail addresses only
     *
     * @return array Indexed list of addresses
     */
    static function decode_address_list($input, $max = null, $decode = true, $fallback = null, $addronly = false)
    {
        // A common case when the same header is used many times in a mail message
        if (is_array($input)) {
            $input = implode(', ', $input);
        }

        $a   = self::parse_address_list((string) $input, $decode, $fallback);
        $out = [];
        $j   = 0;

        // Special chars as defined by RFC 822 need to in quoted string (or escaped).
        $special_chars = '[\(\)\<\>\\\.\[\]@,;:"]';

        if (!is_array($a)) {
            return $out;
        }

        foreach ($a as $val) {
            $j++;
            $address = trim($val['address']);

            if ($addronly) {
                $out[$j] = $address;
            }
            else {
                $name   = trim($val['name']);
                $string = '';

                if ($name && $address && $name != $address) {
                    $string = sprintf('%s <%s>', preg_match("/$special_chars/", $name) ? '"'.addcslashes($name, '"').'"' : $name, $address);
                }
                else if ($address) {
                    $string = $address;
                }
                else if ($name) {
                    $string = $name;
                }

                $out[$j] = ['name' => $name, 'mailto' => $address, 'string' => $string];
            }

            if ($max && $j == $max) {
                break;
            }
        }

        return $out;
    }

    /**
     * Decode a message header value
     *
     * @param string  $input    Header value
     * @param string  $fallback Fallback charset if none specified
     *
     * @return string Decoded string
     */
    public static function decode_header($input, $fallback = null)
    {
        $str = self::decode_mime_string((string)$input, $fallback);

        return $str;
    }

    /**
     * Decode a mime-encoded string to internal charset
     *
     * @param string $input    Header value
     * @param string $fallback Fallback charset if none specified
     *
     * @return string Decoded string
     */
    public static function decode_mime_string($input, $fallback = null)
    {
        $default_charset = $fallback ?: self::get_charset();

        // rfc: all line breaks or other characters not found
        // in the Base64 Alphabet must be ignored by decoding software
        // delete all blanks between MIME-lines, differently we can
        // receive unnecessary blanks and broken utf-8 symbols
        $input = preg_replace("/\?=\s+=\?/", '?==?', $input);

        // encoded-word regexp
        $re = '/=\?([^?]+)\?([BbQq])\?([^\n]*?)\?=/';

        // Find all RFC2047's encoded words
        if (preg_match_all($re, $input, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
            // Initialize variables
            $tmp   = [];
            $out   = '';
            $start = 0;

            foreach ($matches as $idx => $m) {
                $pos      = $m[0][1];
                $charset  = $m[1][0];
                $encoding = $m[2][0];
                $text     = $m[3][0];
                $length   = strlen($m[0][0]);

                // Append everything that is before the text to be decoded
                if ($start != $pos) {
                    $substr = substr($input, $start, $pos-$start);
                    $out   .= rcube_charset::convert($substr, $default_charset);
                    $start  = $pos;
                }
                $start += $length;

                // Per RFC2047, each string part "MUST represent an integral number
                // of characters . A multi-octet character may not be split across
                // adjacent encoded-words." However, some mailers break this, so we
                // try to handle characters spanned across parts anyway by iterating
                // through and aggregating sequential encoded parts with the same
                // character set and encoding, then perform the decoding on the
                // aggregation as a whole.

                $tmp[] = $text;
                if (!empty($matches[$idx+1]) && ($next_match = $matches[$idx+1])) {
                    if ($next_match[0][1] == $start
                        && $next_match[1][0] == $charset
                        && $next_match[2][0] == $encoding
                    ) {
                        continue;
                    }
                }

                $count = count($tmp);
                $text  = '';

                // Decode and join encoded-word's chunks
                if ($encoding == 'B' || $encoding == 'b') {
                    $rest  = '';
                    // base64 must be decoded a segment at a time.
                    // However, there are broken implementations that continue
                    // in the following word, we'll handle that (#6048)
                    for ($i=0; $i<$count; $i++) {
                        $chunk  = $rest . $tmp[$i];
                        $length = strlen($chunk);
                        if ($length % 4) {
                            $length = floor($length / 4) * 4;
                            $rest   = substr($chunk, $length);
                            $chunk  = substr($chunk, 0, $length);
                        }

                        $text .= base64_decode($chunk);
                    }
                }
                else { // if ($encoding == 'Q' || $encoding == 'q') {
                    // quoted printable can be combined and processed at once
                    for ($i=0; $i<$count; $i++) {
                        $text .= $tmp[$i];
                    }

                    $text = str_replace('_', ' ', $text);
                    $text = quoted_printable_decode($text);
                }

                $out .= rcube_charset::convert($text, $charset);
                $tmp = [];
            }

            // add the last part of the input string
            if ($start != strlen($input)) {
                $out .= rcube_charset::convert(substr($input, $start), $default_charset);
            }

            // return the results
            return $out;
        }

        // no encoding information, use fallback
        return rcube_charset::convert($input, $default_charset);
    }

    /**
     * Decode a mime part
     *
     * @param string $input    Input string
     * @param string $encoding Part encoding
     *
     * @return string Decoded string
     */
    public static function decode($input, $encoding = '7bit')
    {
        switch (strtolower($encoding)) {
        case 'quoted-printable':
            return quoted_printable_decode($input);
        case 'base64':
            return base64_decode($input);
        case 'x-uuencode':
        case 'x-uue':
        case 'uue':
        case 'uuencode':
            return convert_uudecode($input);
        case '7bit':
        default:
            return $input;
        }
    }

    /**
     * Split RFC822 header string into an associative array
     */
    public static function parse_headers($headers)
    {
        $result  = [];
        $headers = preg_replace('/\r?\n(\t| )+/', ' ', $headers);
        $lines   = explode("\n", $headers);
        $count   = count($lines);

        for ($i=0; $i<$count; $i++) {
            if ($p = strpos($lines[$i], ': ')) {
                $field = strtolower(substr($lines[$i], 0, $p));
                $value = trim(substr($lines[$i], $p+1));
                if (!empty($value)) {
                    $result[$field] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * E-mail address list parser
     */
    private static function parse_address_list($str, $decode = true, $fallback = null)
    {
        // remove any newlines and carriage returns before
        $str = preg_replace('/\r?\n(\s|\t)?/', ' ', $str);

        // extract list items, remove comments
        $str = self::explode_header_string(',;', $str, true);

        // simplified regexp, supporting quoted local part
        $email_rx = '([^\s:]+|("\s*(?:[^"\f\n\r\t\v\b\s]+\s*)+"))@\S+';

        $result = [];

        foreach ($str as $key => $val) {
            $name    = '';
            $address = '';
            $val     = trim($val);

            // First token might be a group name, ignore it
            $tokens = self::explode_header_string(' ', $val);
            if (isset($tokens[0]) && $tokens[0][strlen($tokens[0])-1] == ':') {
                $val = substr($val, strlen($tokens[0]));
            }

            if (preg_match('/(.*)<('.$email_rx.')$/', $val, $m)) {
                // Note: There are cases like "Test<test@domain.tld" with no closing bracket,
                // therefor we do not include it in the regexp above, but we have to
                // remove it later, because $email_rx will catch it (#8164)
                $address = rtrim($m[2], '>');
                $name    = trim($m[1]);
            }
            else if (preg_match('/^('.$email_rx.')$/', $val, $m)) {
                $address = $m[1];
                $name    = '';
            }
            // special case (#1489092)
            else if (preg_match('/(\s*<MAILER-DAEMON>)$/', $val, $m)) {
                $address = 'MAILER-DAEMON';
                $name    = substr($val, 0, -strlen($m[1]));
            }
            else if (preg_match('/('.$email_rx.')/', $val, $m)) {
                $name = $m[1];
            }
            else {
                $name = $val;
            }

            // unquote and/or decode name
            if ($name) {
                // An unquoted name ending with colon is a address group name, ignore it
                if ($name[strlen($name)-1] == ':') {
                    $name = '';
                }

                if (strlen($name) > 1 && $name[0] == '"' && $name[strlen($name)-1] == '"') {
                    $name = substr($name, 1, -1);
                    $name = stripslashes($name);
                }

                if ($decode) {
                    $name = self::decode_header($name, $fallback);
                    // some clients encode addressee name with quotes around it
                    if (strlen($name) > 1 && $name[0] == '"' && $name[strlen($name)-1] == '"') {
                        $name = substr($name, 1, -1);
                    }
                }
            }

            if (!$address && $name) {
                $address = $name;
                $name    = '';
            }

            if ($address) {
                $address      = self::fix_email($address);
                $result[$key] = ['name' => $name, 'address' => $address];
            }
        }

        return $result;
    }

    /**
     * Explodes header (e.g. address-list) string into array of strings
     * using specified separator characters with proper handling
     * of quoted-strings and comments (RFC2822)
     *
     * @param string $separator       String containing separator characters
     * @param string $str             Header string
     * @param bool   $remove_comments Enable to remove comments
     *
     * @return array Header items
     */
    public static function explode_header_string($separator, $str, $remove_comments = false)
    {
        $length  = strlen($str);
        $result  = [];
        $quoted  = false;
        $comment = 0;
        $out     = '';

        for ($i=0; $i<$length; $i++) {
            // we're inside a quoted string
            if ($quoted) {
                if ($str[$i] == '"') {
                    $quoted = false;
                }
                else if ($str[$i] == "\\") {
                    if ($comment <= 0) {
                        $out .= "\\";
                    }
                    $i++;
                }
            }
            // we are inside a comment string
            else if ($comment > 0) {
                if ($str[$i] == ')') {
                    $comment--;
                }
                else if ($str[$i] == '(') {
                    $comment++;
                }
                else if ($str[$i] == "\\") {
                    $i++;
                }
                continue;
            }
            // separator, add to result array
            else if (strpos($separator, $str[$i]) !== false) {
                if ($out) {
                    $result[] = $out;
                }
                $out = '';
                continue;
            }
            // start of quoted string
            else if ($str[$i] == '"') {
                $quoted = true;
            }
            // start of comment
            else if ($remove_comments && $str[$i] == '(') {
                $comment++;
            }

            if ($comment <= 0) {
                $out .= $str[$i];
            }
        }

        if ($out && $comment <= 0) {
            $result[] = $out;
        }

        return $result;
    }

    /**
     * Interpret a format=flowed message body according to RFC 2646
     *
     * @param string $text  Raw body formatted as flowed text
     * @param string $mark  Mark each flowed line with specified character
     * @param bool   $delsp Remove the trailing space of each flowed line
     *
     * @return string Interpreted text with unwrapped lines and stuffed space removed
     */
    public static function unfold_flowed($text, $mark = null, $delsp = false)
    {
        $text    = preg_split('/\r?\n/', $text);
        $last    = -1;
        $q_level = 0;
        $marks   = [];

        foreach ($text as $idx => $line) {
            if ($q = strspn($line, '>')) {
                // remove quote chars
                $line = substr($line, $q);
                // remove (optional) space-staffing
                if (isset($line[0]) && $line[0] === ' ') {
                    $line = substr($line, 1);
                }

                // The same paragraph (We join current line with the previous one) when:
                // - the same level of quoting
                // - previous line was flowed
                // - previous line contains more than only one single space (and quote char(s))
                if ($q == $q_level
                    && isset($text[$last]) && $text[$last][strlen($text[$last])-1] == ' '
                    && !preg_match('/^>+ {0,1}$/', $text[$last])
                ) {
                    if ($delsp) {
                        $text[$last] = substr($text[$last], 0, -1);
                    }
                    $text[$last] .= $line;
                    unset($text[$idx]);

                    if ($mark) {
                        $marks[$last] = true;
                    }
                }
                else {
                    $last = $idx;
                }
            }
            else {
                if ($line == '-- ') {
                    $last = $idx;
                }
                else {
                    // remove space-stuffing
                    if (isset($line[0]) && $line[0] === ' ') {
                        $line = substr($line, 1);
                    }

                    $last_len = isset($text[$last]) ? strlen($text[$last]) : 0;

                    if (
                        $last_len && $line && !$q_level && $text[$last] != '-- '
                        && isset($text[$last][$last_len-1]) && $text[$last][$last_len-1] == ' '
                    ) {
                        if ($delsp) {
                            $text[$last] = substr($text[$last], 0, -1);
                        }
                        $text[$last] .= $line;
                        unset($text[$idx]);

                        if ($mark) {
                            $marks[$last] = true;
                        }
                    }
                    else {
                        $text[$idx] = $line;
                        $last = $idx;
                    }
                }
            }
            $q_level = $q;
        }

        if (!empty($marks)) {
            foreach (array_keys($marks) as $mk) {
                $text[$mk] = $mark . $text[$mk];
            }
        }

        return implode("\r\n", $text);
    }

    /**
     * Wrap the given text to comply with RFC 2646
     *
     * @param string $text    Text to wrap
     * @param int    $length  Length
     * @param string $charset Character encoding of $text
     *
     * @return string Wrapped text
     */
    public static function format_flowed($text, $length = 72, $charset = null)
    {
        $text = preg_split('/\r?\n/', $text);

        foreach ($text as $idx => $line) {
            if ($line != '-- ') {
                if ($level = strspn($line, '>')) {
                    // remove quote chars
                    $line = substr($line, $level);
                    // remove (optional) space-staffing and spaces before the line end
                    $line = rtrim($line, ' ');
                    if (isset($line[0]) && $line[0] === ' ') {
                        $line = substr($line, 1);
                    }

                    $prefix = str_repeat('>', $level) . ' ';
                    $line   = $prefix . self::wordwrap($line, $length - $level - 2, " \r\n$prefix", false, $charset);
                }
                else if ($line) {
                    $line = self::wordwrap(rtrim($line), $length - 2, " \r\n", false, $charset);
                    // space-stuffing
                    $line = preg_replace('/(^|\r\n)(From| |>)/', '\\1 \\2', $line);
                }

                $text[$idx] = $line;
            }
        }

        return implode("\r\n", $text);
    }

    /**
     * Improved wordwrap function with multibyte support.
     * The code is based on Zend_Text_MultiByte::wordWrap().
     *
     * @param string $string      Text to wrap
     * @param int    $width       Line width
     * @param string $break       Line separator
     * @param bool   $cut         Enable to cut word
     * @param string $charset     Charset of $string
     * @param bool   $wrap_quoted When enabled quoted lines will not be wrapped
     *
     * @return string Text
     */
    public static function wordwrap($string, $width = 75, $break = "\n", $cut = false, $charset = null, $wrap_quoted = true)
    {
        // Note: Never try to use iconv instead of mbstring functions here
        //       Iconv's substr/strlen are 100x slower (#1489113)

        if ($charset && $charset != RCUBE_CHARSET) {
            $charset = rcube_charset::parse_charset($charset);
            mb_internal_encoding($charset);
        }

        // Convert \r\n to \n, this is our line-separator
        $string       = str_replace("\r\n", "\n", $string);
        $separator    = "\n"; // must be 1 character length
        $result       = [];

        while (($stringLength = mb_strlen($string)) > 0) {
            $breakPos = mb_strpos($string, $separator, 0);

            // quoted line (do not wrap)
            if ($wrap_quoted && $string[0] == '>') {
                if ($breakPos === $stringLength - 1 || $breakPos === false) {
                    $subString = $string;
                    $cutLength = null;
                }
                else {
                    $subString = mb_substr($string, 0, $breakPos);
                    $cutLength = $breakPos + 1;
                }
            }
            // next line found and current line is shorter than the limit
            else if ($breakPos !== false && $breakPos < $width) {
                if ($breakPos === $stringLength - 1) {
                    $subString = $string;
                    $cutLength = null;
                }
                else {
                    $subString = mb_substr($string, 0, $breakPos);
                    $cutLength = $breakPos + 1;
                }
            }
            else {
                $subString = mb_substr($string, 0, $width);

                // last line
                if ($breakPos === false && $subString === $string) {
                    $cutLength = null;
                }
                else {
                    $nextChar = mb_substr($string, $width, 1);

                    if ($nextChar === ' ' || $nextChar === $separator) {
                        $afterNextChar = mb_substr($string, $width + 1, 1);

                        // Note: mb_substr() does never return False
                        if ($afterNextChar === false || $afterNextChar === '') {
                            $subString .= $nextChar;
                        }

                        $cutLength = mb_strlen($subString) + 1;
                    }
                    else {
                        $spacePos = mb_strrpos($subString, ' ', 0);

                        if ($spacePos !== false) {
                            $subString = mb_substr($subString, 0, $spacePos);
                            $cutLength = $spacePos + 1;
                        }
                        else if ($cut === false) {
                            $spacePos = mb_strpos($string, ' ', 0);

                            if ($spacePos !== false && ($breakPos === false || $spacePos < $breakPos)) {
                                $subString = mb_substr($string, 0, $spacePos);
                                $cutLength = $spacePos + 1;
                            }
                            else if ($breakPos === false) {
                                $subString = $string;
                                $cutLength = null;
                            }
                            else {
                                $subString = mb_substr($string, 0, $breakPos);
                                $cutLength = $breakPos + 1;
                            }
                        }
                        else {
                            $cutLength = $width;
                        }
                    }
                }
            }

            $result[] = $subString;

            if ($cutLength !== null) {
                $string = mb_substr($string, $cutLength, ($stringLength - $cutLength));
            }
            else {
                break;
            }
        }

        if ($charset && $charset != RCUBE_CHARSET) {
            mb_internal_encoding(RCUBE_CHARSET);
        }

        return implode($break, $result);
    }

    /**
     * A method to guess the mime_type of an attachment.
     *
     * @param string  $path        Path to the file or file contents
     * @param string  $name        File name (with suffix)
     * @param string  $failover    Mime type supplied for failover
     * @param bool    $is_stream   Set to True if $path contains file contents
     * @param bool    $skip_suffix Set to True if the config/mimetypes.php map should be ignored
     *
     * @return string
     * @author Till Klampaeckel <till@php.net>
     * @see https://www.php.net/manual/en/ref.fileinfo.php
     * @see https://www.php.net/mime_content_type
     */
    public static function file_content_type($path, $name, $failover = 'application/octet-stream', $is_stream = false, $skip_suffix = false)
    {
        $mime_type = null;
        $config    = rcube::get_instance()->config;

        // Detect mimetype using filename extension
        if (!$skip_suffix) {
            $mime_type = self::file_ext_type($name);
        }

        // try fileinfo extension if available
        if (!$mime_type && function_exists('finfo_open')) {
            $mime_magic = $config->get('mime_magic');
            // null as a 2nd argument should be the same as no argument
            // this however is not true on all systems/versions
            if ($mime_magic) {
                $finfo = finfo_open(FILEINFO_MIME, $mime_magic);
            }
            else {
                $finfo = finfo_open(FILEINFO_MIME);
            }

            if ($finfo) {
                $func      = $is_stream ? 'finfo_buffer' : 'finfo_file';
                $mime_type = $func($finfo, $path, FILEINFO_MIME_TYPE);
                finfo_close($finfo);
            }
        }

        // try PHP's mime_content_type
        if (!$mime_type && !$is_stream && function_exists('mime_content_type')) {
            $mime_type = @mime_content_type($path);
        }

        // fall back to user-submitted string
        if (!$mime_type) {
            $mime_type = $failover;
        }

        return $mime_type;
    }

    /**
     * File type detection based on file name only.
     *
     * @param string $filename Path to the file or file contents
     *
     * @return string|null Mimetype label
     */
    public static function file_ext_type($filename)
    {
        static $mime_ext = [];

        if (empty($mime_ext)) {
            foreach (rcube::get_instance()->config->resolve_paths('mimetypes.php') as $fpath) {
                $mime_ext = array_merge($mime_ext, (array) @include($fpath));
            }
        }

        // use file name suffix with hard-coded mime-type map
        if (!empty($mime_ext) && $filename) {
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if ($ext && !empty($mime_ext[$ext])) {
                return $mime_ext[$ext];
            }
        }
    }

    /**
     * Get mimetype => file extension mapping
     *
     * @param string $mimetype Mime-Type to get extensions for
     *
     * @return array List of extensions matching the given mimetype or a hash array
     *               with ext -> mimetype mappings if $mimetype is not given
     */
    public static function get_mime_extensions($mimetype = null)
    {
        static $mime_types, $mime_extensions;

        // return cached data
        if (is_array($mime_types)) {
            return $mimetype ? (isset($mime_types[$mimetype]) ? $mime_types[$mimetype] : []) : $mime_extensions;
        }

        // load mapping file
        $file_paths = [];

        if ($mime_types = rcube::get_instance()->config->get('mime_types')) {
            $file_paths[] = $mime_types;
        }

        // try common locations
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
            $file_paths[] = 'C:/xampp/apache/conf/mime.types';
        }
        else {
            $file_paths[] = '/etc/mime.types';
            $file_paths[] = '/etc/httpd/mime.types';
            $file_paths[] = '/etc/httpd2/mime.types';
            $file_paths[] = '/etc/apache/mime.types';
            $file_paths[] = '/etc/apache2/mime.types';
            $file_paths[] = '/etc/nginx/mime.types';
            $file_paths[] = '/usr/local/etc/httpd/conf/mime.types';
            $file_paths[] = '/usr/local/etc/apache/conf/mime.types';
            $file_paths[] = '/usr/local/etc/apache24/mime.types';
        }

        $mime_types      = [];
        $mime_extensions = [];
        $lines = [];
        $regex = "/([\w\+\-\.\/]+)\s+([\w\s]+)/i";

        foreach ($file_paths as $fp) {
            if (@is_readable($fp)) {
                $lines = file($fp, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                break;
            }
        }

        foreach ($lines as $line) {
             // skip comments or mime types w/o any extensions
            if ($line[0] == '#' || !preg_match($regex, $line, $matches)) {
                continue;
            }

            $mime = $matches[1];

            foreach (explode(' ', $matches[2]) as $ext) {
                $ext = trim($ext);
                $mime_types[$mime][]   = $ext;
                $mime_extensions[$ext] = $mime;
            }
        }

        // fallback to some well-known types most important for daily emails
        if (empty($mime_types)) {
            foreach (rcube::get_instance()->config->resolve_paths('mimetypes.php') as $fpath) {
                $mime_extensions = array_merge($mime_extensions, (array) @include($fpath));
            }

            foreach ($mime_extensions as $ext => $mime) {
                $mime_types[$mime][] = $ext;
            }
        }

        // Add some known aliases that aren't included by some mime.types (#1488891)
        // the order is important here so standard extensions have higher prio
        $aliases = [
            'image/gif'      => ['gif'],
            'image/png'      => ['png'],
            'image/x-png'    => ['png'],
            'image/jpeg'     => ['jpg', 'jpeg', 'jpe'],
            'image/jpg'      => ['jpg', 'jpeg', 'jpe'],
            'image/pjpeg'    => ['jpg', 'jpeg', 'jpe'],
            'image/tiff'     => ['tif'],
            'image/bmp'      => ['bmp'],
            'image/x-ms-bmp' => ['bmp'],
            'message/rfc822' => ['eml'],
            'text/x-mail'    => ['eml'],
        ];

        foreach ($aliases as $mime => $exts) {
            if (isset($mime_types[$mime])) {
                $mime_types[$mime] = array_unique(array_merge((array) $mime_types[$mime], $exts));
            }
            else {
                $mime_types[$mime] = $exts;
            }

            foreach ($exts as $ext) {
                if (!isset($mime_extensions[$ext])) {
                    $mime_extensions[$ext] = $mime;
                }
            }
        }

        if ($mimetype) {
            return !empty($mime_types[$mimetype]) ? $mime_types[$mimetype] : [];
        }

        return $mime_extensions;
    }

    /**
     * Detect image type of the given binary data by checking magic numbers.
     *
     * @param string $data  Binary file content
     *
     * @return string Detected mime-type or jpeg as fallback
     */
    public static function image_content_type($data)
    {
        $type = 'jpeg';
        if      (preg_match('/^\x89\x50\x4E\x47/', $data)) $type = 'png';
        else if (preg_match('/^\x47\x49\x46\x38/', $data)) $type = 'gif';
        else if (preg_match('/^\x00\x00\x01\x00/', $data)) $type = 'ico';
    //  else if (preg_match('/^\xFF\xD8\xFF\xE0/', $data)) $type = 'jpeg';

        return 'image/' . $type;
    }

    /**
     * Try to fix invalid email addresses
     */
    public static function fix_email($email)
    {
        $parts = rcube_utils::explode_quoted_string('@', $email);

        foreach ($parts as $idx => $part) {
            // remove redundant quoting (#1490040)
            if (isset($part[0]) && $part[0] == '"' && preg_match('/^"([a-zA-Z0-9._+=-]+)"$/', $part, $m)) {
                $parts[$idx] = $m[1];
            }
        }

        return implode('@', $parts);
    }

    /**
     * Fix mimetype name.
     *
     * @param string $type Mimetype
     *
     * @return string Mimetype
     */
    public static function fix_mimetype($type)
    {
        $type    = strtolower(trim($type));
        $aliases = [
            'image/x-ms-bmp' => 'image/bmp',        // #4771
            'pdf'            => 'application/pdf',  // #6816
        ];

        if (!empty($aliases[$type])) {
            return $aliases[$type];
        }

        // Some versions of Outlook create garbage Content-Type:
        // application/pdf.A520491B_3BF7_494D_8855_7FAC2C6C0608
        if (preg_match('/^application\/pdf.+/', $type)) {
            return 'application/pdf';
        }

        // treat image/pjpeg (image/pjpg, image/jpg) as image/jpeg (#4196)
        if (preg_match('/^image\/p?jpe?g$/', $type)) {
            return 'image/jpeg';
        }

        return $type;
    }
}
