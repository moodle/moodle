<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines string apis
 *
 * @package    core
 * @copyright  (C) 2001-3001 Eloy Lafuente (stronk7) {@link http://contiento.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * defines string api's for manipulating strings
 *
 * This class is used to manipulate strings under Moodle 1.6 an later. As
 * utf-8 text become mandatory a pool of safe functions under this encoding
 * become necessary. The name of the methods is exactly the
 * same than their PHP originals.
 *
 * A big part of this class acts as a wrapper over the Typo3 charset library,
 * really a cool group of utilities to handle texts and encoding conversion.
 *
 * Take a look to its own copyright and license details.
 *
 * IMPORTANT Note: Typo3 libraries always expect lowercase charsets to use 100%
 * its capabilities so, don't forget to make the conversion
 * from every wrapper function!
 *
 * @package   core
 * @category  string
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_text {
    /** @var string Byte order mark for UTF-8 */
    const UTF8_BOM = "\xef\xbb\xbf";

    /**
     * @var string[] Array of strings representing Unicode non-characters
     */
    protected static $noncharacters;

    /**
     * Return t3lib helper class, which is used for conversion between charsets
     *
     * @param bool $reset
     * @return t3lib_cs
     */
    protected static function typo3($reset = false) {
        static $typo3cs = null;

        if ($reset) {
            $typo3cs = null;
            return null;
        }

        if (isset($typo3cs)) {
            return $typo3cs;
        }

        global $CFG;

        // Required files
        require_once($CFG->libdir.'/typo3/class.t3lib_cs.php');
        require_once($CFG->libdir.'/typo3/class.t3lib_div.php');
        require_once($CFG->libdir.'/typo3/interface.t3lib_singleton.php');
        require_once($CFG->libdir.'/typo3/class.t3lib_l10n_locales.php');

        // do not use mbstring or recode because it may return invalid results in some corner cases
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['t3lib_cs_convMethod'] = 'iconv';
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['t3lib_cs_utils'] = 'iconv';

        // Tell Typo3 we are curl enabled always (mandatory since 2.0)
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['curlUse'] = '1';

        // And this directory must exist to allow Typo to cache conversion
        // tables when using internal functions
        make_temp_directory('typo3temp/cs');

        // Make sure typo is using our dir permissions
        $GLOBALS['TYPO3_CONF_VARS']['BE']['folderCreateMask'] = decoct($CFG->directorypermissions);

        // Default mask for Typo
        $GLOBALS['TYPO3_CONF_VARS']['BE']['fileCreateMask'] = decoct($CFG->filepermissions);

        // This full path constants must be defined too, transforming backslashes
        // to forward slashed because Typo3 requires it.
        if (!defined('PATH_t3lib')) {
            define('PATH_t3lib', str_replace('\\','/',$CFG->libdir.'/typo3/'));
            define('PATH_typo3', str_replace('\\','/',$CFG->libdir.'/typo3/'));
            define('PATH_site', str_replace('\\','/',$CFG->tempdir.'/'));
            define('TYPO3_OS', stristr(PHP_OS,'win')&&!stristr(PHP_OS,'darwin')?'WIN':'');
        }

        $typo3cs = new t3lib_cs();

        return $typo3cs;
    }

    /**
     * Reset internal textlib caches.
     * @static
     */
    public static function reset_caches() {
        self::typo3(true);
    }

    /**
     * Standardise charset name
     *
     * Please note it does not mean the returned charset is actually supported.
     *
     * @static
     * @param string $charset raw charset name
     * @return string normalised lowercase charset name
     */
    public static function parse_charset($charset) {
        $charset = strtolower($charset);

        // shortcuts so that we do not have to load typo3 on every page

        if ($charset === 'utf8' or $charset === 'utf-8') {
            return 'utf-8';
        }

        if (preg_match('/^(cp|win|windows)-?(12[0-9]{2})$/', $charset, $matches)) {
            return 'windows-'.$matches[2];
        }

        if (preg_match('/^iso-8859-[0-9]+$/', $charset, $matches)) {
            return $charset;
        }

        if ($charset === 'euc-jp') {
            return 'euc-jp';
        }
        if ($charset === 'iso-2022-jp') {
            return 'iso-2022-jp';
        }
        if ($charset === 'shift-jis' or $charset === 'shift_jis') {
            return 'shift_jis';
        }
        if ($charset === 'gb2312') {
            return 'gb2312';
        }
        if ($charset === 'gb18030') {
            return 'gb18030';
        }

        // fallback to typo3
        return self::typo3()->parse_charset($charset);
    }

    /**
     * Converts the text between different encodings. It uses iconv extension with //TRANSLIT parameter,
     * falls back to typo3. If both source and target are utf-8 it tries to fix invalid characters only.
     *
     * @param string $text
     * @param string $fromCS source encoding
     * @param string $toCS result encoding
     * @return string|bool converted string or false on error
     */
    public static function convert($text, $fromCS, $toCS='utf-8') {
        $fromCS = self::parse_charset($fromCS);
        $toCS   = self::parse_charset($toCS);

        $text = (string)$text; // we can work only with strings

        if ($text === '') {
            return '';
        }

        if ($fromCS === 'utf-8') {
            $text = fix_utf8($text);
            if ($toCS === 'utf-8') {
                return $text;
            }
        }

        if ($toCS === 'ascii') {
            // Try to normalize the conversion a bit.
            $text = self::specialtoascii($text, $fromCS);
        }

        // Prevent any error notices, do not use //IGNORE so that we get
        // consistent result from Typo3 if iconv fails.
        $result = @iconv($fromCS, $toCS.'//TRANSLIT', $text);

        if ($result === false or $result === '') {
            // note: iconv is prone to return empty string when invalid char encountered, or false if encoding unsupported
            $oldlevel = error_reporting(E_PARSE);
            $result = self::typo3()->conv((string)$text, $fromCS, $toCS);
            error_reporting($oldlevel);
        }

        return $result;
    }

    /**
     * Multibyte safe substr() function, uses mbstring or iconv for UTF-8, falls back to typo3.
     *
     * @param string $text string to truncate
     * @param int $start negative value means from end
     * @param int $len maximum length of characters beginning from start
     * @param string $charset encoding of the text
     * @return string portion of string specified by the $start and $len
     */
    public static function substr($text, $start, $len=null, $charset='utf-8') {
        $charset = self::parse_charset($charset);

        if ($charset === 'utf-8') {
            if (function_exists('mb_substr')) {
                // this is much faster than iconv - see MDL-31142
                if ($len === null) {
                    $oldcharset = mb_internal_encoding();
                    mb_internal_encoding('UTF-8');
                    $result = mb_substr($text, $start);
                    mb_internal_encoding($oldcharset);
                    return $result;
                } else {
                    return mb_substr($text, $start, $len, 'UTF-8');
                }

            } else {
                if ($len === null) {
                    $len = iconv_strlen($text, 'UTF-8');
                }
                return iconv_substr($text, $start, $len, 'UTF-8');
            }
        }

        $oldlevel = error_reporting(E_PARSE);
        if ($len === null) {
            $result = self::typo3()->substr($charset, (string)$text, $start);
        } else {
            $result = self::typo3()->substr($charset, (string)$text, $start, $len);
        }
        error_reporting($oldlevel);

        return $result;
    }

    /**
     * Truncates a string to no more than a certain number of bytes in a multi-byte safe manner.
     * UTF-8 only!
     *
     * Many of the other charsets we test for (like ISO-2022-JP and EUC-JP) are not supported
     * by typo3, and will give invalid results, so we are supporting UTF-8 only.
     *
     * @param string $string String to truncate
     * @param int $bytes Maximum length of bytes in the result
     * @return string Portion of string specified by $bytes
     * @since Moodle 3.1
     */
    public static function str_max_bytes($string, $bytes) {
        if (function_exists('mb_strcut')) {
            return mb_strcut($string, 0, $bytes, 'UTF-8');
        }

        $oldlevel = error_reporting(E_PARSE);
        $result = self::typo3()->strtrunc('utf-8', $string, $bytes);
        error_reporting($oldlevel);

        return $result;
    }

    /**
     * Finds the last occurrence of a character in a string within another.
     * UTF-8 ONLY safe mb_strrchr().
     *
     * @param string $haystack The string from which to get the last occurrence of needle.
     * @param string $needle The string to find in haystack.
     * @param boolean $part If true, returns the portion before needle, else return the portion after (including needle).
     * @return string|false False when not found.
     * @since Moodle 2.4.6, 2.5.2, 2.6
     */
    public static function strrchr($haystack, $needle, $part = false) {

        if (function_exists('mb_strrchr')) {
            return mb_strrchr($haystack, $needle, $part, 'UTF-8');
        }

        $pos = self::strrpos($haystack, $needle);
        if ($pos === false) {
            return false;
        }

        $length = null;
        if ($part) {
            $length = $pos;
            $pos = 0;
        }

        return self::substr($haystack, $pos, $length, 'utf-8');
    }

    /**
     * Multibyte safe strlen() function, uses mbstring or iconv for UTF-8, falls back to typo3.
     *
     * @param string $text input string
     * @param string $charset encoding of the text
     * @return int number of characters
     */
    public static function strlen($text, $charset='utf-8') {
        $charset = self::parse_charset($charset);

        if ($charset === 'utf-8') {
            if (function_exists('mb_strlen')) {
                return mb_strlen($text, 'UTF-8');
            } else {
                return iconv_strlen($text, 'UTF-8');
            }
        }

        $oldlevel = error_reporting(E_PARSE);
        $result = self::typo3()->strlen($charset, (string)$text);
        error_reporting($oldlevel);

        return $result;
    }

    /**
     * Multibyte safe strtolower() function, uses mbstring, falls back to typo3.
     *
     * @param string $text input string
     * @param string $charset encoding of the text (may not work for all encodings)
     * @return string lower case text
     */
    public static function strtolower($text, $charset='utf-8') {
        $charset = self::parse_charset($charset);

        if ($charset === 'utf-8' and function_exists('mb_strtolower')) {
            return mb_strtolower($text, 'UTF-8');
        }

        $oldlevel = error_reporting(E_PARSE);
        $result = self::typo3()->conv_case($charset, (string)$text, 'toLower');
        error_reporting($oldlevel);

        return $result;
    }

    /**
     * Multibyte safe strtoupper() function, uses mbstring, falls back to typo3.
     *
     * @param string $text input string
     * @param string $charset encoding of the text (may not work for all encodings)
     * @return string upper case text
     */
    public static function strtoupper($text, $charset='utf-8') {
        $charset = self::parse_charset($charset);

        if ($charset === 'utf-8' and function_exists('mb_strtoupper')) {
            return mb_strtoupper($text, 'UTF-8');
        }

        $oldlevel = error_reporting(E_PARSE);
        $result = self::typo3()->conv_case($charset, (string)$text, 'toUpper');
        error_reporting($oldlevel);

        return $result;
    }

    /**
     * Find the position of the first occurrence of a substring in a string.
     * UTF-8 ONLY safe strpos(), uses mbstring, falls back to iconv.
     *
     * @param string $haystack the string to search in
     * @param string $needle one or more charachters to search for
     * @param int $offset offset from begining of string
     * @return int the numeric position of the first occurrence of needle in haystack.
     */
    public static function strpos($haystack, $needle, $offset=0) {
        if (function_exists('mb_strpos')) {
            return mb_strpos($haystack, $needle, $offset, 'UTF-8');
        } else {
            return iconv_strpos($haystack, $needle, $offset, 'UTF-8');
        }
    }

    /**
     * Find the position of the last occurrence of a substring in a string
     * UTF-8 ONLY safe strrpos(), uses mbstring, falls back to iconv.
     *
     * @param string $haystack the string to search in
     * @param string $needle one or more charachters to search for
     * @return int the numeric position of the last occurrence of needle in haystack
     */
    public static function strrpos($haystack, $needle) {
        if (function_exists('mb_strrpos')) {
            return mb_strrpos($haystack, $needle, null, 'UTF-8');
        } else {
            return iconv_strrpos($haystack, $needle, 'UTF-8');
        }
    }

    /**
     * Reverse UTF-8 multibytes character sets (used for RTL languages)
     * (We only do this because there is no mb_strrev or iconv_strrev)
     *
     * @param string $str the multibyte string to reverse
     * @return string the reversed multi byte string
     */
    public static function strrev($str) {
        preg_match_all('/./us', $str, $ar);
        return join('', array_reverse($ar[0]));
    }

    /**
     * Try to convert upper unicode characters to plain ascii,
     * the returned string may contain unconverted unicode characters.
     *
     * @param string $text input string
     * @param string $charset encoding of the text
     * @return string converted ascii string
     */
    public static function specialtoascii($text, $charset='utf-8') {
        $charset = self::parse_charset($charset);
        $oldlevel = error_reporting(E_PARSE);
        $result = self::typo3()->specCharsToASCII($charset, (string)$text);
        error_reporting($oldlevel);
        return $result;
    }

    /**
     * Generate a correct base64 encoded header to be used in MIME mail messages.
     * This function seems to be 100% compliant with RFC1342. Credits go to:
     * paravoid (http://www.php.net/manual/en/function.mb-encode-mimeheader.php#60283).
     *
     * @param string $text input string
     * @param string $charset encoding of the text
     * @return string base64 encoded header
     */
    public static function encode_mimeheader($text, $charset='utf-8') {
        if (empty($text)) {
            return (string)$text;
        }
        // Normalize charset
        $charset = self::parse_charset($charset);
        // If the text is pure ASCII, we don't need to encode it
        if (self::convert($text, $charset, 'ascii') == $text) {
            return $text;
        }
        // Although RFC says that line feed should be \r\n, it seems that
        // some mailers double convert \r, so we are going to use \n alone
        $linefeed="\n";
        // Define start and end of every chunk
        $start = "=?$charset?B?";
        $end = "?=";
        // Accumulate results
        $encoded = '';
        // Max line length is 75 (including start and end)
        $length = 75 - strlen($start) - strlen($end);
        // Multi-byte ratio
        $multilength = self::strlen($text, $charset);
        // Detect if strlen and friends supported
        if ($multilength === false) {
            if ($charset == 'GB18030' or $charset == 'gb18030') {
                while (strlen($text)) {
                    // try to encode first 22 chars - we expect most chars are two bytes long
                    if (preg_match('/^(([\x00-\x7f])|([\x81-\xfe][\x40-\x7e])|([\x81-\xfe][\x80-\xfe])|([\x81-\xfe][\x30-\x39]..)){1,22}/m', $text, $matches)) {
                        $chunk = $matches[0];
                        $encchunk = base64_encode($chunk);
                        if (strlen($encchunk) > $length) {
                            // find first 11 chars - each char in 4 bytes - worst case scenario
                            preg_match('/^(([\x00-\x7f])|([\x81-\xfe][\x40-\x7e])|([\x81-\xfe][\x80-\xfe])|([\x81-\xfe][\x30-\x39]..)){1,11}/m', $text, $matches);
                            $chunk = $matches[0];
                            $encchunk = base64_encode($chunk);
                        }
                        $text = substr($text, strlen($chunk));
                        $encoded .= ' '.$start.$encchunk.$end.$linefeed;
                    } else {
                        break;
                    }
                }
                $encoded = trim($encoded);
                return $encoded;
            } else {
                return false;
            }
        }
        $ratio = $multilength / strlen($text);
        // Base64 ratio
        $magic = $avglength = floor(3 * $length * $ratio / 4);
        // basic infinite loop protection
        $maxiterations = strlen($text)*2;
        $iteration = 0;
        // Iterate over the string in magic chunks
        for ($i=0; $i <= $multilength; $i+=$magic) {
            if ($iteration++ > $maxiterations) {
                return false; // probably infinite loop
            }
            $magic = $avglength;
            $offset = 0;
            // Ensure the chunk fits in length, reducing magic if necessary
            do {
                $magic -= $offset;
                $chunk = self::substr($text, $i, $magic, $charset);
                $chunk = base64_encode($chunk);
                $offset++;
            } while (strlen($chunk) > $length);
            // This chunk doesn't break any multi-byte char. Use it.
            if ($chunk)
                $encoded .= ' '.$start.$chunk.$end.$linefeed;
        }
        // Strip the first space and the last linefeed
        $encoded = substr($encoded, 1, -strlen($linefeed));

        return $encoded;
    }

    /**
     * Returns HTML entity transliteration table.
     * @return array with (html entity => utf-8) elements
     */
    protected static function get_entities_table() {
        static $trans_tbl = null;

        // Generate/create $trans_tbl
        if (!isset($trans_tbl)) {
            if (version_compare(phpversion(), '5.3.4') < 0) {
                $trans_tbl = array();
                foreach (get_html_translation_table(HTML_ENTITIES) as $val=>$key) {
                    $trans_tbl[$key] = self::convert($val, 'ISO-8859-1', 'utf-8');
                }

            } else if (version_compare(phpversion(), '5.4.0') < 0) {
                $trans_tbl = get_html_translation_table(HTML_ENTITIES, ENT_COMPAT, 'UTF-8');
                $trans_tbl = array_flip($trans_tbl);

            } else {
                $trans_tbl = get_html_translation_table(HTML_ENTITIES, ENT_COMPAT | ENT_HTML401, 'UTF-8');
                $trans_tbl = array_flip($trans_tbl);
            }
        }

        return $trans_tbl;
    }

    /**
     * Converts all the numeric entities &#nnnn; or &#xnnn; to UTF-8
     * Original from laurynas dot butkus at gmail at:
     * http://php.net/manual/en/function.html-entity-decode.php#75153
     * with some custom mods to provide more functionality
     *
     * @param string $str input string
     * @param boolean $htmlent convert also html entities (defaults to true)
     * @return string encoded UTF-8 string
     */
    public static function entities_to_utf8($str, $htmlent=true) {
        static $callback1 = null ;
        static $callback2 = null ;

        if (!$callback1 or !$callback2) {
            $callback1 = function($matches) {
                return core_text::code2utf8(hexdec($matches[1]));
            };
            $callback2 = function($matches) {
                return core_text::code2utf8($matches[1]);
            };
        }

        $result = (string)$str;
        $result = preg_replace_callback('/&#x([0-9a-f]+);/i', $callback1, $result);
        $result = preg_replace_callback('/&#([0-9]+);/', $callback2, $result);

        // Replace literal entities (if desired)
        if ($htmlent) {
            $trans_tbl = self::get_entities_table();
            // It should be safe to search for ascii strings and replace them with utf-8 here.
            $result = strtr($result, $trans_tbl);
        }
        // Return utf8-ised string
        return $result;
    }

    /**
     * Converts all Unicode chars > 127 to numeric entities &#nnnn; or &#xnnn;.
     *
     * @param string $str input string
     * @param boolean $dec output decadic only number entities
     * @param boolean $nonnum remove all non-numeric entities
     * @return string converted string
     */
    public static function utf8_to_entities($str, $dec=false, $nonnum=false) {
        static $callback = null ;

        if ($nonnum) {
            $str = self::entities_to_utf8($str, true);
        }

        // Avoid some notices from Typo3 code
        $oldlevel = error_reporting(E_PARSE);
        $result = self::typo3()->utf8_to_entities((string)$str);
        error_reporting($oldlevel);

        if ($dec) {
            if (!$callback) {
                $callback = function($matches) {
                    return '&#' . hexdec($matches[1]) . ';';
                };
            }
            $result = preg_replace_callback('/&#x([0-9a-f]+);/i', $callback, $result);
        }

        return $result;
    }

    /**
     * Removes the BOM from unicode string {@link http://unicode.org/faq/utf_bom.html}
     *
     * @param string $str input string
     * @return string
     */
    public static function trim_utf8_bom($str) {
        $bom = self::UTF8_BOM;
        if (strpos($str, $bom) === 0) {
            return substr($str, strlen($bom));
        }
        return $str;
    }

    /**
     * There are a number of Unicode non-characters including the byte-order mark (which may appear
     * multiple times in a string) and also other ranges. These can cause problems for some
     * processing.
     *
     * This function removes the characters using string replace, so that the rest of the string
     * remains unchanged.
     *
     * @param string $value Input string
     * @return string Cleaned string value
     * @since Moodle 3.5
     */
    public static function remove_unicode_non_characters($value) {
        // Set up list of all Unicode non-characters for fast replacing.
        if (!self::$noncharacters) {
            self::$noncharacters = [];
            // This list of characters is based on the Unicode standard. It includes the last two
            // characters of each code planes 0-16 inclusive...
            for ($plane = 0; $plane <= 16; $plane++) {
                $base = ($plane === 0 ? '' : dechex($plane));
                self::$noncharacters[] = html_entity_decode('&#x' . $base . 'fffe;');
                self::$noncharacters[] = html_entity_decode('&#x' . $base . 'ffff;');
            }
            // ...And the character range U+FDD0 to U+FDEF.
            for ($char = 0xfdd0; $char <= 0xfdef; $char++) {
                self::$noncharacters[] = html_entity_decode('&#x' . dechex($char) . ';');
            }
        }

        // Do character replacement.
        return str_replace(self::$noncharacters, '', $value);
    }

    /**
     * Returns encoding options for select boxes, utf-8 and platform encoding first
     *
     * @return array encodings
     */
    public static function get_encodings() {
        $encodings = array();
        $encodings['UTF-8'] = 'UTF-8';
        $winenc = strtoupper(get_string('localewincharset', 'langconfig'));
        if ($winenc != '') {
            $encodings[$winenc] = $winenc;
        }
        $nixenc = strtoupper(get_string('oldcharset', 'langconfig'));
        $encodings[$nixenc] = $nixenc;

        foreach (self::typo3()->synonyms as $enc) {
            $enc = strtoupper($enc);
            $encodings[$enc] = $enc;
        }
        return $encodings;
    }

    /**
     * Returns the utf8 string corresponding to the unicode value
     * (from php.net, courtesy - romans@void.lv)
     *
     * @param  int    $num one unicode value
     * @return string the UTF-8 char corresponding to the unicode value
     */
    public static function code2utf8($num) {
        if ($num < 128) {
            return chr($num);
        }
        if ($num < 2048) {
            return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
        }
        if ($num < 65536) {
            return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
        }
        if ($num < 2097152) {
            return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
        }
        return '';
    }

    /**
     * Returns the code of the given UTF-8 character
     *
     * @param  string $utf8char one UTF-8 character
     * @return int    the code of the given character
     */
    public static function utf8ord($utf8char) {
        if ($utf8char == '') {
            return 0;
        }
        $ord0 = ord($utf8char[0]);
        if ($ord0 >= 0 && $ord0 <= 127) {
            return $ord0;
        }
        $ord1 = ord($utf8char[1]);
        if ($ord0 >= 192 && $ord0 <= 223) {
            return ($ord0 - 192) * 64 + ($ord1 - 128);
        }
        $ord2 = ord($utf8char[2]);
        if ($ord0 >= 224 && $ord0 <= 239) {
            return ($ord0 - 224) * 4096 + ($ord1 - 128) * 64 + ($ord2 - 128);
        }
        $ord3 = ord($utf8char[3]);
        if ($ord0 >= 240 && $ord0 <= 247) {
            return ($ord0 - 240) * 262144 + ($ord1 - 128 )* 4096 + ($ord2 - 128) * 64 + ($ord3 - 128);
        }
        return false;
    }

    /**
     * Makes first letter of each word capital - words must be separated by spaces.
     * Use with care, this function does not work properly in many locales!!!
     *
     * @param string $text input string
     * @return string
     */
    public static function strtotitle($text) {
        if (empty($text)) {
            return $text;
        }

        if (function_exists('mb_convert_case')) {
            return mb_convert_case($text, MB_CASE_TITLE, 'UTF-8');
        }

        $text = self::strtolower($text);
        $words = explode(' ', $text);
        foreach ($words as $i=>$word) {
            $length = self::strlen($word);
            if (!$length) {
                continue;

            } else if ($length == 1) {
                $words[$i] = self::strtoupper($word);

            } else {
                $letter = self::substr($word, 0, 1);
                $letter = self::strtoupper($letter);
                $rest   = self::substr($word, 1);
                $words[$i] = $letter.$rest;
            }
        }
        return implode(' ', $words);
    }
}
