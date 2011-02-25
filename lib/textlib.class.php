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
 * @package    core
 * @subpackage lib
 * @copyright  (C) 2001-3001 Eloy Lafuente (stronk7) {@link http://contiento.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * As we implement the singleton pattern to use this class (only one instance
 * is shared globally), we need this helper function
 *
 * IMPORTANT Note: Typo3 libraries always expect lowercase charsets to use 100%
 * its capabilities so, don't forget to make the conversion
 * from every wrapper function!
 *
 * @return textlib singleton instance of textlib
 */
function textlib_get_instance() {
    global $CFG;

    static $instance = null;

    if (!$instance) {
        /// initialisation is delayed because we do not want this on each page ;-)

    /// Required files
        require_once($CFG->libdir.'/typo3/class.t3lib_cs.php');
        require_once($CFG->libdir.'/typo3/class.t3lib_div.php');

    /// If ICONV is available, lets Typo3 library use it for convert
        if (extension_loaded('iconv')) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['t3lib_cs_convMethod'] = 'iconv';
        /// Else if mbstring is available, lets Typo3 library use it
        } else if (extension_loaded('mbstring')) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['t3lib_cs_convMethod'] = 'mbstring';
        /// Else if recode is available, lets Typo3 library use it
        } else if (extension_loaded('recode')) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['t3lib_cs_convMethod'] = 'recode';
        } else {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['t3lib_cs_convMethod'] = '';
        }

    /// If mbstring is available, lets Typo3 library use it for functions
        if (extension_loaded('mbstring')) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['t3lib_cs_utils'] = 'mbstring';
        } else {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['t3lib_cs_utils'] = '';
        }

    /// Tell Typo3 we are curl enabled always (mandatory since 2.0)
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['curlUse'] = '1';

    /// And this directory must exist to allow Typo to cache conversion
    /// tables when using internal functions
        make_upload_directory('temp/typo3temp/cs');

    /// Make sure typo is using our dir permissions
        $GLOBALS['TYPO3_CONF_VARS']['BE']['folderCreateMask'] = decoct($CFG->directorypermissions);

    /// Default mask for Typo
        $GLOBALS['TYPO3_CONF_VARS']['BE']['fileCreateMask'] = $CFG->directorypermissions;

    /// This full path constants must be defined too, transforming backslashes
    /// to forward slashed beacuse Typo3 requires it.
        define ('PATH_t3lib', str_replace('\\','/',$CFG->libdir.'/typo3/'));
        define ('PATH_typo3', str_replace('\\','/',$CFG->libdir.'/typo3/'));
        define ('PATH_site', str_replace('\\','/',$CFG->dataroot.'/temp/'));
        define ('TYPO3_OS', stristr(PHP_OS,'win')&&!stristr(PHP_OS,'darwin')?'WIN':'');

        $instance = new textlib();
    }
    return $instance;
}

/**
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
 * @package   moodlecore
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class textlib {

    var $typo3cs;

    /**
     * Standard constructor of the class. All it does is to instantiate
     * a new t3lib_cs object to have all their functions ready.
     *
     * Instead of istantiating a lot of objects of this class everytime
     * some of their functions is going to be used, you can invoke the:
     * textlib_get_instance() function, avoiding the creation of them
     * (following the singleton pattern)
     */
    function textlib() {
        /// Instantiate a conversor object some of the methods in typo3
        /// reference to $this and cannot be executed in a static context
        $this->typo3cs = new t3lib_cs();
    }

    /**
     * Converts the text between different encodings. It will use iconv, mbstring
     * or internal (typo3) methods to try such conversion. Returns false if fails.
     */
    function convert($text, $fromCS, $toCS='utf-8') {
    /// Normalize charsets
        $fromCS = $this->typo3cs->parse_charset($fromCS);
        $toCS   = $this->typo3cs->parse_charset($toCS);
    /// Avoid some notices from Typo3 code
        $oldlevel = error_reporting(E_PARSE);
    /// Call Typo3 conv() function. It will do all the work
        $result = $this->typo3cs->conv($text, $fromCS, $toCS);
    /// Restore original debug level
        error_reporting($oldlevel);
        return $result;
    }

    /**
     * Multibyte safe substr() function, uses mbstring if available.
     */
    function substr($text, $start, $len=null, $charset='utf-8') {
    /// Normalize charset
        $charset = $this->typo3cs->parse_charset($charset);
    /// Avoid some notices from Typo3 code
        $oldlevel = error_reporting(E_PARSE);
    /// Call Typo3 substr() function. It will do all the work
        $result = $this->typo3cs->substr($charset,$text,$start,$len);
    /// Restore original debug level
        error_reporting($oldlevel);
        return $result;
    }

    /**
     * Multibyte safe strlen() function, uses mbstring if available.
     */
    function strlen($text, $charset='utf-8') {
    /// Normalize charset
        $charset = $this->typo3cs->parse_charset($charset);
    /// Avoid some notices from Typo3 code
        $oldlevel = error_reporting(E_PARSE);
    /// Call Typo3 strlen() function. It will do all the work
        $result = $this->typo3cs->strlen($charset,$text);
    /// Restore original debug level
        error_reporting($oldlevel);
        return $result;
    }

    /**
     * Multibyte safe strtolower() function, uses mbstring if available.
     */
    function strtolower($text, $charset='utf-8') {
    /// Normalize charset
        $charset = $this->typo3cs->parse_charset($charset);
    /// Avoid some notices from Typo3 code
        $oldlevel = error_reporting(E_PARSE);
    /// Call Typo3 conv_case() function. It will do all the work
        $result = $this->typo3cs->conv_case($charset,$text,'toLower');
    /// Restore original debug level
        error_reporting($oldlevel);
        return $result;
    }

    /**
     * Multibyte safe strtoupper() function, uses mbstring if available.
     */
    function strtoupper($text, $charset='utf-8') {
    /// Normalize charset
        $charset = $this->typo3cs->parse_charset($charset);
    /// Avoid some notices from Typo3 code
        $oldlevel = error_reporting(E_PARSE);
    /// Call Typo3 conv_case() function. It will do all the work
        $result = $this->typo3cs->conv_case($charset,$text,'toUpper');
    /// Restore original debug level
        error_reporting($oldlevel);
        return $result;
    }

    /**
     * UTF-8 ONLY safe strpos() function, uses mbstring if available.
     */
    function strpos($haystack,$needle,$offset=0) {
    /// Call Typo3 utf8_strpos() function. It will do all the work
        return $this->typo3cs->utf8_strpos($haystack,$needle,$offset);
    }

    /**
     * UTF-8 ONLY safe strrpos() function, uses mbstring if available.
     */
    function strrpos($haystack,$needle) {
    /// Call Typo3 utf8_strrpos() function. It will do all the work
        return $this->typo3cs->utf8_strrpos($haystack,$needle);
    }

    /**
     * Try to convert upper unicode characters to plain ascii,
     * the returned string may cantain unconverted unicode characters.
     */
    function specialtoascii($text,$charset='utf-8') {
    /// Normalize charset
        $charset = $this->typo3cs->parse_charset($charset);
    /// Avoid some notices from Typo3 code
        $oldlevel = error_reporting(E_PARSE);
        $result = $this->typo3cs->specCharsToASCII($charset,$text);
    /// Restore original debug level
        error_reporting($oldlevel);
        return $result;
    }

    /**
     * Generate a correct base64 encoded header to be used in MIME mail messages.
     * This function seems to be 100% compliant with RFC1342. Credits go to:
     * paravoid (http://www.php.net/manual/en/function.mb-encode-mimeheader.php#60283).
     */
    function encode_mimeheader($text, $charset='utf-8') {
        if (empty($text)) {
            return (string)$text;
        }
    /// Normalize charset
        $charset = $this->typo3cs->parse_charset($charset);
    /// If the text is pure ASCII, we don't need to encode it
        if ($this->convert($text, $charset, 'ascii') == $text) {
            return $text;
        }
    /// Although RFC says that line feed should be \r\n, it seems that
    /// some mailers double convert \r, so we are going to use \n alone
        $linefeed="\n";
    /// Define start and end of every chunk
        $start = "=?$charset?B?";
        $end = "?=";
    /// Acumulate results
        $encoded = '';
    /// Max line length is 75 (including start and end)
        $length = 75 - strlen($start) - strlen($end);
    /// Multi-byte ratio
        $multilength = $this->strlen($text, $charset);
    /// Detect if strlen and friends supported
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
    /// Base64 ratio
        $magic = $avglength = floor(3 * $length * $ratio / 4);
    /// basic infinite loop protection
        $maxiterations = strlen($text)*2;
        $iteration = 0;
    /// Iterate over the string in magic chunks
        for ($i=0; $i <= $multilength; $i+=$magic) {
            if ($iteration++ > $maxiterations) {
                return false; // probably infinite loop
            }
            $magic = $avglength;
            $offset = 0;
        /// Ensure the chunk fits in length, reduding magic if necessary
            do {
                $magic -= $offset;
                $chunk = $this->substr($text, $i, $magic, $charset);
                $chunk = base64_encode($chunk);
                $offset++;
            } while (strlen($chunk) > $length);
        /// This chunk doen't break any multi-byte char. Use it.
            if ($chunk)
                $encoded .= ' '.$start.$chunk.$end.$linefeed;
        }
    /// Strip the first space and the last linefeed
        $encoded = substr($encoded, 1, -strlen($linefeed));

        return $encoded;
    }

    /**
     * Converts all the numeric entities &#nnnn; or &#xnnn; to UTF-8
     * Original from laurynas dot butkus at gmail at:
     * http://php.net/manual/en/function.html-entity-decode.php#75153
     * with some custom mods to provide more functionality
     *
     * @param    string    $str      input string
     * @param    boolean   $htmlent  convert also html entities (defaults to true)
     *
     * NOTE: we could have used typo3 entities_to_utf8() here
     *       but the direct alternative used runs 400% quicker
     *       and uses 0.5Mb less memory, so, let's use it
     *       (tested agains 10^6 conversions)
     */
    function entities_to_utf8($str, $htmlent=true) {

        static $trans_tbl; /// Going to use static translit table

    /// Replace numeric entities
        $result = preg_replace('~&#x([0-9a-f]+);~ei', 'textlib::code2utf8(hexdec("\\1"))', $str);
        $result = preg_replace('~&#([0-9]+);~e', 'textlib::code2utf8(\\1)', $result);

    /// Replace literal entities (if desired)
        if ($htmlent) {
        /// Generate/create $trans_tbl
            if (!isset($trans_tbl)) {
                $trans_tbl = array();
                foreach (get_html_translation_table(HTML_ENTITIES) as $val=>$key) {
                    $trans_tbl[$key] = utf8_encode($val);
                }
            }
            $result = strtr($result, $trans_tbl);
        }
    /// Return utf8-ised string
        return $result;
    }

    /**
     * Converts all Unicode chars > 127 to numeric entities &#nnnn; or &#xnnn;.
     *
     * @param    string         input string
     * @param    boolean        output decadic only number entities
     * @param    boolean        remove all nonumeric entities
     * @return   string         converted string
     */
    function utf8_to_entities($str, $dec=false, $nonnum=false) {
    /// Avoid some notices from Typo3 code
        $oldlevel = error_reporting(E_PARSE);
        if ($nonnum) {
            $str = $this->typo3cs->entities_to_utf8($str, true);
        }
        $result = $this->typo3cs->utf8_to_entities($str);
        if ($dec) {
            $result = preg_replace('/&#x([0-9a-f]+);/ie', "'&#'.hexdec('$1').';'", $result);
        }
    /// Restore original debug level
        error_reporting($oldlevel);
        return $result;
    }

    /**
     * Removes the BOM from unicode string - see http://unicode.org/faq/utf_bom.html
     */
    function trim_utf8_bom($str) {
        $bom = "\xef\xbb\xbf";
        if (strpos($str, $bom) === 0) {
            return substr($str, strlen($bom));
        }
        return $str;
    }

    /**
     * Returns encoding options for select boxes, utf-8 and platform encoding first
     * @return array encodings
     */
    function get_encodings() {
        $encodings = array();
        $encodings['UTF-8'] = 'UTF-8';
        $winenc = strtoupper(get_string('localewincharset', 'langconfig'));
        if ($winenc != '') {
            $encodings[$winenc] = $winenc;
        }
        $nixenc = strtoupper(get_string('oldcharset', 'langconfig'));
        $encodings[$nixenc] = $nixenc;

        foreach ($this->typo3cs->synonyms as $enc) {
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
    function code2utf8($num) {
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
     * Makes first letter of each word capital - words must be separated by spaces.
     * Use with care, this function does not work properly in many locales!!!
     * @param string $text
     * @return string
     */
    function strtotitle($text) {
        if (empty($text)) {
            return $text;
        }

        if (function_exists('mb_convert_case')) {
            return mb_convert_case($text, MB_CASE_TITLE,"UTF-8");
        }

        $text = $this->strtolower($text);
        $words = explode(' ', $text);
        foreach ($words as $i=>$word) {
            $length = $this->strlen($word);
            if (!$length) {
                continue;

            } else if ($length == 1) {
                $words[$i] = $this->strtoupper($word);

            } else {
                $letter = $this->substr($word, 0, 1);
                $letter = $this->strtoupper($letter);
                $rest   = $this->substr($word, 1);
                $words[$i] = $letter.$rest;
            }
        }
        return implode(' ', $words);
    }

    /**
     * Locale aware sorting, the key associations are kept, values are sorted alphabetically.
     * @param array $arr array to be sorted
     * @param string $lang moodle language
     * @return void, modifies parameter
     */
    function asort(array &$arr) {
        if (function_exists('collator_asort')) {
            if ($coll = collator_create(get_string('locale', 'langconfig'))) {
                collator_asort($coll, $arr);
                return;
            }
        }
        asort($arr, SORT_LOCALE_STRING);
    }
}
