<?php

/**
 +-----------------------------------------------------------------------+
 | This file is part of the Roundcube Webmail client                     |
 |                                                                       |
 | Copyright (C) The Roundcube Dev Team                                  |
 | Copyright (C) Kolab Systems AG                                        |
 | Copyright (C) 2000 Edmund Grimley Evans <edmundo@rano.org>            |
 |                                                                       |
 | Licensed under the GNU General Public License version 3 or            |
 | any later version with exceptions for skins & plugins.                |
 | See the README file for a full license statement.                     |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Provide charset conversion functionality                            |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <roundcube@gmail.com>                        |
 | Author: Aleksander Machniak <alec@alec.pl>                            |
 | Author: Edmund Grimley Evans <edmundo@rano.org>                       |
 +-----------------------------------------------------------------------+
*/

/**
 * Character sets conversion functionality
 *
 * @package    Framework
 * @subpackage Core
 */
class rcube_charset
{
    /**
     * Character set aliases (some of them from HTML5 spec.)
     *
     * @var array
     */
    static public $aliases = [
        'USASCII'       => 'WINDOWS-1252',
        'ANSIX31101983' => 'WINDOWS-1252',
        'ANSIX341968'   => 'WINDOWS-1252',
        'UNKNOWN8BIT'   => 'ISO-8859-15',
        'UNKNOWN'       => 'ISO-8859-15',
        'USERDEFINED'   => 'ISO-8859-15',
        'KSC56011987'   => 'EUC-KR',
        'GB2312'        => 'GBK',
        'GB231280'      => 'GBK',
        'UNICODE'       => 'UTF-8',
        'UTF7IMAP'      => 'UTF7-IMAP',
        'TIS620'        => 'WINDOWS-874',
        'ISO88599'      => 'WINDOWS-1254',
        'ISO885911'     => 'WINDOWS-874',
        'MACROMAN'      => 'MACINTOSH',
        '77'            => 'MAC',
        '128'           => 'SHIFT-JIS',
        '129'           => 'CP949',
        '130'           => 'CP1361',
        '134'           => 'GBK',
        '136'           => 'BIG5',
        '161'           => 'WINDOWS-1253',
        '162'           => 'WINDOWS-1254',
        '163'           => 'WINDOWS-1258',
        '177'           => 'WINDOWS-1255',
        '178'           => 'WINDOWS-1256',
        '186'           => 'WINDOWS-1257',
        '204'           => 'WINDOWS-1251',
        '222'           => 'WINDOWS-874',
        '238'           => 'WINDOWS-1250',
        'MS950'         => 'CP950',
        'WINDOWS31J'    => 'CP932',
        'WINDOWS949'    => 'UHC',
        'WINDOWS1257'   => 'ISO-8859-13',
        'ISO2022JP'     => 'ISO-2022-JP-MS',
    ];

    /**
     * Windows codepages
     *
     * @var array
     */
    static public $windows_codepages = [
         37 => 'IBM037',    // IBM EBCDIC US-Canada
        437 => 'IBM437',    // OEM United States
        500 => 'IBM500',    // IBM EBCDIC International
        708 => 'ASMO-708',  // Arabic (ASMO 708)
        720 => 'DOS-720',   // Arabic (Transparent ASMO); Arabic (DOS)
        737 => 'IBM737',    // OEM Greek (formerly 437G); Greek (DOS)
        775 => 'IBM775',    // OEM Baltic; Baltic (DOS)
        850 => 'IBM850',    // OEM Multilingual Latin 1; Western European (DOS)
        852 => 'IBM852',    // OEM Latin 2; Central European (DOS)
        855 => 'IBM855',    // OEM Cyrillic (primarily Russian)
        857 => 'IBM857',    // OEM Turkish; Turkish (DOS)
        858 => 'IBM00858',  // OEM Multilingual Latin 1 + Euro symbol
        860 => 'IBM860',    // OEM Portuguese; Portuguese (DOS)
        861 => 'IBM861',    // OEM Icelandic; Icelandic (DOS)
        862 => 'DOS-862',   // OEM Hebrew; Hebrew (DOS)
        863 => 'IBM863',    // OEM French Canadian; French Canadian (DOS)
        864 => 'IBM864',    // OEM Arabic; Arabic (864)
        865 => 'IBM865',    // OEM Nordic; Nordic (DOS)
        866 => 'cp866',     // OEM Russian; Cyrillic (DOS)
        869 => 'IBM869',    // OEM Modern Greek; Greek, Modern (DOS)
        870 => 'IBM870',    // IBM EBCDIC Multilingual/ROECE (Latin 2); IBM EBCDIC Multilingual Latin 2
        874 => 'windows-874',  // ANSI/OEM Thai (ISO 8859-11); Thai (Windows)
        875 => 'cp875',     // IBM EBCDIC Greek Modern
        932 => 'shift_jis', // ANSI/OEM Japanese; Japanese (Shift-JIS)
        936 => 'gb2312',    // ANSI/OEM Simplified Chinese (PRC, Singapore); Chinese Simplified (GB2312)
        950 => 'big5',      // ANSI/OEM Traditional Chinese (Taiwan; Hong Kong SAR, PRC); Chinese Traditional (Big5)
        1026 => 'IBM1026',      // IBM EBCDIC Turkish (Latin 5)
        1047 => 'IBM01047',     // IBM EBCDIC Latin 1/Open System
        1140 => 'IBM01140',     // IBM EBCDIC US-Canada (037 + Euro symbol); IBM EBCDIC (US-Canada-Euro)
        1141 => 'IBM01141',     // IBM EBCDIC Germany (20273 + Euro symbol); IBM EBCDIC (Germany-Euro)
        1142 => 'IBM01142',     // IBM EBCDIC Denmark-Norway (20277 + Euro symbol); IBM EBCDIC (Denmark-Norway-Euro)
        1143 => 'IBM01143',     // IBM EBCDIC Finland-Sweden (20278 + Euro symbol); IBM EBCDIC (Finland-Sweden-Euro)
        1144 => 'IBM01144',     // IBM EBCDIC Italy (20280 + Euro symbol); IBM EBCDIC (Italy-Euro)
        1145 => 'IBM01145',     // IBM EBCDIC Latin America-Spain (20284 + Euro symbol); IBM EBCDIC (Spain-Euro)
        1146 => 'IBM01146',     // IBM EBCDIC United Kingdom (20285 + Euro symbol); IBM EBCDIC (UK-Euro)
        1147 => 'IBM01147',     // IBM EBCDIC France (20297 + Euro symbol); IBM EBCDIC (France-Euro)
        1148 => 'IBM01148',     // IBM EBCDIC International (500 + Euro symbol); IBM EBCDIC (International-Euro)
        1149 => 'IBM01149',     // IBM EBCDIC Icelandic (20871 + Euro symbol); IBM EBCDIC (Icelandic-Euro)
        1200 => 'UTF-16',       // Unicode UTF-16, little endian byte order (BMP of ISO 10646); available only to managed applications
        1201 => 'UTF-16BE',     // Unicode UTF-16, big endian byte order; available only to managed applications
        1250 => 'windows-1250', // ANSI Central European; Central European (Windows)
        1251 => 'windows-1251', // ANSI Cyrillic; Cyrillic (Windows)
        1252 => 'windows-1252', // ANSI Latin 1; Western European (Windows)
        1253 => 'windows-1253', // ANSI Greek; Greek (Windows)
        1254 => 'windows-1254', // ANSI Turkish; Turkish (Windows)
        1255 => 'windows-1255', // ANSI Hebrew; Hebrew (Windows)
        1256 => 'windows-1256', // ANSI Arabic; Arabic (Windows)
        1257 => 'windows-1257', // ANSI Baltic; Baltic (Windows)
        1258 => 'windows-1258', // ANSI/OEM Vietnamese; Vietnamese (Windows)
        10000 => 'macintosh',   // MAC Roman; Western European (Mac)
        12000 => 'UTF-32',      // Unicode UTF-32, little endian byte order; available only to managed applications
        12001 => 'UTF-32BE',    // Unicode UTF-32, big endian byte order; available only to managed applications
        20127 => 'US-ASCII',    // US-ASCII (7-bit)
        20273 => 'IBM273',      // IBM EBCDIC Germany
        20277 => 'IBM277',      // IBM EBCDIC Denmark-Norway
        20278 => 'IBM278',      // IBM EBCDIC Finland-Sweden
        20280 => 'IBM280',      // IBM EBCDIC Italy
        20284 => 'IBM284',      // IBM EBCDIC Latin America-Spain
        20285 => 'IBM285',      // IBM EBCDIC United Kingdom
        20290 => 'IBM290',      // IBM EBCDIC Japanese Katakana Extended
        20297 => 'IBM297',      // IBM EBCDIC France
        20420 => 'IBM420',      // IBM EBCDIC Arabic
        20423 => 'IBM423',      // IBM EBCDIC Greek
        20424 => 'IBM424',      // IBM EBCDIC Hebrew
        20838 => 'IBM-Thai',    // IBM EBCDIC Thai
        20866 => 'koi8-r',      // Russian (KOI8-R); Cyrillic (KOI8-R)
        20871 => 'IBM871',      // IBM EBCDIC Icelandic
        20880 => 'IBM880',      // IBM EBCDIC Cyrillic Russian
        20905 => 'IBM905',      // IBM EBCDIC Turkish
        20924 => 'IBM00924',    // IBM EBCDIC Latin 1/Open System (1047 + Euro symbol)
        20932 => 'EUC-JP',      // Japanese (JIS 0208-1990 and 0212-1990)
        20936 => 'cp20936',     // Simplified Chinese (GB2312); Chinese Simplified (GB2312-80)
        20949 => 'cp20949',     // Korean Wansung
        21025 => 'cp1025',      // IBM EBCDIC Cyrillic Serbian-Bulgarian
        21866 => 'koi8-u',      // Ukrainian (KOI8-U); Cyrillic (KOI8-U)
        28591 => 'iso-8859-1',  // ISO 8859-1 Latin 1; Western European (ISO)
        28592 => 'iso-8859-2',  // ISO 8859-2 Central European; Central European (ISO)
        28593 => 'iso-8859-3',  // ISO 8859-3 Latin 3
        28594 => 'iso-8859-4',  // ISO 8859-4 Baltic
        28595 => 'iso-8859-5',  // ISO 8859-5 Cyrillic
        28596 => 'iso-8859-6',  // ISO 8859-6 Arabic
        28597 => 'iso-8859-7',  // ISO 8859-7 Greek
        28598 => 'iso-8859-8',  // ISO 8859-8 Hebrew; Hebrew (ISO-Visual)
        28599 => 'iso-8859-9',  // ISO 8859-9 Turkish
        28603 => 'iso-8859-13', // ISO 8859-13 Estonian
        28605 => 'iso-8859-15', // ISO 8859-15 Latin 9
        38598 => 'iso-8859-8-i', // ISO 8859-8 Hebrew; Hebrew (ISO-Logical)
        50220 => 'iso-2022-jp', // ISO 2022 Japanese with no halfwidth Katakana; Japanese (JIS)
        50221 => 'csISO2022JP', // ISO 2022 Japanese with halfwidth Katakana; Japanese (JIS-Allow 1 byte Kana)
        50222 => 'iso-2022-jp', // ISO 2022 Japanese JIS X 0201-1989; Japanese (JIS-Allow 1 byte Kana - SO/SI)
        50225 => 'iso-2022-kr', // ISO 2022 Korean
        51932 => 'EUC-JP',      // EUC Japanese
        51936 => 'EUC-CN',      // EUC Simplified Chinese; Chinese Simplified (EUC)
        51949 => 'EUC-KR',      // EUC Korean
        52936 => 'hz-gb-2312',  // HZ-GB2312 Simplified Chinese; Chinese Simplified (HZ)
        54936 => 'GB18030',     // Windows XP and later: GB18030 Simplified Chinese (4 byte); Chinese Simplified (GB18030)
        65000 => 'UTF-7',
        65001 => 'UTF-8',
    ];

    /**
     * Validate character set identifier.
     *
     * @param string $input Character set identifier
     *
     * @return bool True if valid, False if not valid
     */
    public static function is_valid($input)
    {
        return is_string($input) && preg_match('|^[a-zA-Z0-9_./:#-]{2,32}$|', $input) > 0;
    }

    /**
     * Parse and validate charset name string.
     * Sometimes charset string is malformed, there are also charset aliases,
     * but we need strict names for charset conversion (specially utf8 class)
     *
     * @param string $input Input charset name
     *
     * @return string The validated charset name
     */
    public static function parse_charset($input)
    {
        static $charsets = [];

        $charset = strtoupper((string) $input);

        if (isset($charsets[$input])) {
            return $charsets[$input];
        }

        $charset = preg_replace([
            '/^[^0-9A-Z]+/',    // e.g. _ISO-8859-JP$SIO
            '/\$.*$/',          // e.g. _ISO-8859-JP$SIO
            '/UNICODE-1-1-*/',  // RFC1641/1642
            '/^X-/',            // X- prefix (e.g. X-ROMAN8 => ROMAN8)
            '/\*.*$/'           // lang code according to RFC 2231.5
        ], '', $charset);

        if ($charset == 'BINARY') {
            return $charsets[$input] = null;
        }

        // allow A-Z and 0-9 only
        $str = preg_replace('/[^A-Z0-9]/', '', $charset);

        $result = $charset;

        if (isset(self::$aliases[$str])) {
            $result = self::$aliases[$str];
        }
        // UTF
        else if (preg_match('/U[A-Z][A-Z](7|8|16|32)(BE|LE)*/', $str, $m)) {
            $result = 'UTF-' . $m[1] . (!empty($m[2]) ? $m[2] : '');
        }
        // ISO-8859
        else if (preg_match('/ISO8859([0-9]{0,2})/', $str, $m)) {
            $iso = 'ISO-8859-' . ($m[1] ?: 1);
            // some clients sends windows-1252 text as latin1,
            // it is safe to use windows-1252 for all latin1
            $result = $iso == 'ISO-8859-1' ? 'WINDOWS-1252' : $iso;
        }
        // handle broken charset names e.g. WINDOWS-1250HTTP-EQUIVCONTENT-TYPE
        else if (preg_match('/(WIN|WINDOWS)([0-9]+)/', $str, $m)) {
            $result = 'WINDOWS-' . $m[2];
        }
        // LATIN
        else if (preg_match('/LATIN(.*)/', $str, $m)) {
            $aliases = ['2' => 2, '3' => 3, '4' => 4, '5' => 9, '6' => 10,
                '7' => 13, '8' => 14, '9' => 15, '10' => 16,
                'ARABIC' => 6, 'CYRILLIC' => 5, 'GREEK' => 7, 'GREEK1' => 7, 'HEBREW' => 8
            ];

            // some clients sends windows-1252 text as latin1,
            // it is safe to use windows-1252 for all latin1
            if ($m[1] == 1) {
                $result = 'WINDOWS-1252';
            }
            // we need ISO labels
            else if (!empty($aliases[$m[1]])) {
                $result = 'ISO-8859-'.$aliases[$m[1]];
            }
        }

        $charsets[$input] = $result;

        return $result;
    }

    /**
     * Convert a string from one charset to another.
     *
     * @param string $str  Input string
     * @param string $from Suspected charset of the input string
     * @param string $to   Target charset to convert to; defaults to RCUBE_CHARSET
     *
     * @return string Converted string
     */
    public static function convert($str, $from, $to = null)
    {
        static $iconv_options;

        $to   = empty($to) ? RCUBE_CHARSET : self::parse_charset($to);
        $from = self::parse_charset($from);

        // It is a common case when UTF-16 charset is used with US-ASCII content (#1488654)
        // In that case we can just skip the conversion (use UTF-8)
        if ($from == 'UTF-16' && !preg_match('/[^\x00-\x7F]/', $str)) {
            $from = 'UTF-8';
        }

        if ($from == $to || empty($str) || empty($from)) {
            return $str;
        }

        $out = false;
        $error_handler = function() { throw new \Exception(); };

        // Ignore invalid characters
        $mbstring_sc = mb_substitute_character();
        mb_substitute_character('none');

        // If mbstring reports an illegal character in input via E_WARNING.
        // FIXME: Is this really true with substitute character 'none'?
        // A warning is thrown in PHP<8 also on unsupported encoding, in PHP>=8 ValueError
        // is thrown instead (therefore we catch Throwable below)
        set_error_handler($error_handler, E_WARNING);

        try {
            $out = mb_convert_encoding($str, $to, $from);
        }
        catch (Throwable $e) {
            $out = false;
        }

        restore_error_handler();
        mb_substitute_character($mbstring_sc);

        if ($out !== false) {
            return $out;
        }

        if ($iconv_options === null) {
            if (function_exists('iconv')) {
                // ignore characters not available in output charset
                $iconv_options = '//IGNORE';
                if (iconv('', $iconv_options, '') === false) {
                    // iconv implementation does not support options
                    $iconv_options = '';
                }
            }
            else {
                $iconv_options = false;
            }
        }

        // Fallback to iconv module, it is slower, but supports much more charsets than mbstring
        if ($iconv_options !== false && $from != 'UTF7-IMAP' && $to != 'UTF7-IMAP'
            && $from !== 'ISO-2022-JP'
        ) {
            // If iconv reports an illegal character in input it means that input string
            // has been truncated. It's reported as E_NOTICE.
            // PHP8 will also throw E_WARNING on unsupported encoding.
            set_error_handler($error_handler, E_NOTICE | E_WARNING);

            try {
                $out = iconv($from, $to . $iconv_options, $str);
            }
            catch (Throwable $e) {
                $out = false;
            }

            restore_error_handler();

            if ($out !== false) {
                return $out;
            }
        }

        // return the original string
        return $str;
    }

    /**
     * Check if the specified input string matches one of the provided charsets.
     * This includes UTF-32, UTF-16, RCUBE_CHARSET and default_charset.
     *
     * @param string $str      Input string
     * @param array  $charsets Suspected charsets of the input string
     *
     * @return string|null First matching charset
     */
    public static function check($str, $charsets = [])
    {
        $chunk = strlen($str) > 100 * 1024 ? substr($str, 0, 100 * 1024) : $str;

        // Add dehault charset, system charset and easily detectable charset to the list
        if (substr($chunk, 0, 4) == "\0\0\xFE\xFF") $charsets[] = 'UTF-32BE';
        if (substr($chunk, 0, 4) == "\xFF\xFE\0\0") $charsets[] = 'UTF-32LE';
        if (substr($chunk, 0, 2) == "\xFE\xFF")     $charsets[] = 'UTF-16BE';
        if (substr($chunk, 0, 2) == "\xFF\xFE")     $charsets[] = 'UTF-16LE';

        // heuristics
        if (preg_match('/\x00\x00\x00[^\x00]/', $chunk))    $charsets[] = 'UTF-32BE';
        if (preg_match('/[^\x00]\x00\x00\x00/', $chunk))    $charsets[] = 'UTF-32LE';
        if (preg_match('/\x00[^\x00]\x00[^\x00]/', $chunk)) $charsets[] = 'UTF-16BE';
        if (preg_match('/[^\x00]\x00[^\x00]\x00/', $chunk)) $charsets[] = 'UTF-16LE';

        $charsets[] = RCUBE_CHARSET;
        $charsets[] = (string) rcube::get_instance()->config->get('default_charset');

        $charsets = array_map(['rcube_charset', 'parse_charset'], $charsets);
        $charsets = array_unique(array_filter($charsets));

        foreach ($charsets as $charset) {
            $ret = self::convert($chunk, $charset);

            if ($ret === rcube_charset::clean($ret)) {
                return $charset;
            }
        }
    }

    /**
     * Converts string from standard UTF-7 (RFC 2152) to UTF-8.
     *
     * @param string $str Input string (UTF-7)
     *
     * @return string Converted string (UTF-8)
     * @deprecated use self::convert()
     */
    public static function utf7_to_utf8($str)
    {
        return self::convert($str, 'UTF-7', 'UTF-8');
    }

    /**
     * Converts string from UTF-16 to UTF-8 (helper for utf-7 to utf-8 conversion)
     *
     * @param string $str Input string
     *
     * @return string The converted string
     * @deprecated use self::convert()
     */
    public static function utf16_to_utf8($str)
    {
        return self::convert($str, 'UTF-16BE', 'UTF-8');
    }

    /**
     * Convert the data ($str) from RFC 2060's UTF-7 to UTF-8.
     * If input data is invalid, return the original input string.
     * RFC 2060 obviously intends the encoding to be unique (see
     * point 5 in section 5.1.3), so we reject any non-canonical
     * form, such as &ACY- (instead of &-) or &AMA-&AMA- (instead
     * of &AMAAwA-).
     *
     * @param string $str Input string (UTF7-IMAP)
     *
     * @return string Output string (UTF-8)
     * @deprecated use self::convert()
     */
    public static function utf7imap_to_utf8($str)
    {
        return self::convert($str, 'UTF7-IMAP', 'UTF-8');
    }

    /**
     * Convert the data ($str) from UTF-8 to RFC 2060's UTF-7.
     * Unicode characters above U+FFFF are replaced by U+FFFE.
     * If input data is invalid, return an empty string.
     *
     * @param string $str Input string (UTF-8)
     *
     * @return string Output string (UTF7-IMAP)
     * @deprecated use self::convert()
     */
    public static function utf8_to_utf7imap($str)
    {
        return self::convert($str, 'UTF-8', 'UTF7-IMAP');
    }

    /**
     * A method to guess character set of a string.
     *
     * @param string $string   String
     * @param string $failover Default result for failover
     * @param string $language User language
     *
     * @return string Charset name
     * @deprecated
     */
    public static function detect($string, $failover = null, $language = null)
    {
        if (substr($string, 0, 4) == "\0\0\xFE\xFF") return 'UTF-32BE';  // Big Endian
        if (substr($string, 0, 4) == "\xFF\xFE\0\0") return 'UTF-32LE';  // Little Endian
        if (substr($string, 0, 2) == "\xFE\xFF")     return 'UTF-16BE';  // Big Endian
        if (substr($string, 0, 2) == "\xFF\xFE")     return 'UTF-16LE';  // Little Endian
        if (substr($string, 0, 3) == "\xEF\xBB\xBF") return 'UTF-8';

        // heuristics
        if (strlen($string) >= 4) {
            if ($string[0] == "\0" && $string[1] == "\0" && $string[2] == "\0" && $string[3] != "\0") return 'UTF-32BE';
            if ($string[0] != "\0" && $string[1] == "\0" && $string[2] == "\0" && $string[3] == "\0") return 'UTF-32LE';
            if ($string[0] == "\0" && $string[1] != "\0" && $string[2] == "\0" && $string[3] != "\0") return 'UTF-16BE';
            if ($string[0] != "\0" && $string[1] == "\0" && $string[2] != "\0" && $string[3] == "\0") return 'UTF-16LE';
        }

        if (empty($language)) {
            $rcube    = rcube::get_instance();
            $language = $rcube->get_user_language();
        }

        // Prioritize charsets according to the current language (#1485669)
        $prio = null;
        switch ($language) {
        case 'ja_JP':
            $prio = ['ISO-2022-JP', 'JIS', 'UTF-8', 'EUC-JP', 'eucJP-win', 'SJIS'];
            break;

        case 'zh_CN':
        case 'zh_TW':
            $prio = ['UTF-8', 'BIG-5', 'EUC-TW', 'GB18030'];
            break;

        case 'ko_KR':
            $prio = ['UTF-8', 'EUC-KR', 'ISO-2022-KR'];
            break;

        case 'ru_RU':
            $prio = ['UTF-8', 'WINDOWS-1251', 'KOI8-R'];
            break;

        case 'tr_TR':
            $prio = ['UTF-8', 'ISO-8859-9', 'WINDOWS-1254'];
            break;
        }

        // mb_detect_encoding() is not reliable for some charsets (#1490135)
        // use mb_check_encoding() to make charset priority lists really working
        if (!empty($prio) && function_exists('mb_check_encoding')) {
            foreach ($prio as $encoding) {
                if (mb_check_encoding($string, $encoding)) {
                    return $encoding;
                }
            }
        }

        if (function_exists('mb_detect_encoding')) {
            $exclude = 'BASE64,UUENCODE,HTML-ENTITIES,Quoted-Printable,'
                . '7bit,8bit,pass,wchar,byte2be,byte2le,byte4be,byte4le,'
                . 'UCS-4,UCS-4BE,UCS-4LE,UCS-2,UCS-2BE,UCS-2LE';

            if (empty($prio)) {
                $prio = [
                    'UTF-8',
                    'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4',
                    'ISO-8859-5', 'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9',
                    'ISO-8859-10', 'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16',
                    'WINDOWS-1252', 'WINDOWS-1251', 'WINDOWS-1254',
                    'EUC-JP', 'EUC-TW', 'KOI8-R', 'BIG-5', 'ISO-2022-KR', 'ISO-2022-JP', 'GB18030',
                ];
            }

            // We have to remove unwanted/uncommon encodings from the list.
            // This is needed especially on PHP >= 8.1
            $all_encodings = array_diff(mb_list_encodings(), explode(',', $exclude));

            $encodings = array_unique(array_merge($prio, $all_encodings));

            if ($encoding = mb_detect_encoding($string, $encodings, true)) {
                return $encoding;
            }
        }

        // No match, check for UTF-8
        // from http://w3.org/International/questions/qa-forms-utf-8.html
        if (preg_match('/\A(
            [\x09\x0A\x0D\x20-\x7E]
            | [\xC2-\xDF][\x80-\xBF]
            | \xE0[\xA0-\xBF][\x80-\xBF]
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}
            | \xED[\x80-\x9F][\x80-\xBF]
            | \xF0[\x90-\xBF][\x80-\xBF]{2}
            | [\xF1-\xF3][\x80-\xBF]{3}
            | \xF4[\x80-\x8F][\x80-\xBF]{2}
            )*\z/xs', substr($string, 0, 2048))
        ) {
            return 'UTF-8';
        }

        return $failover;
    }

    /**
     * Removes non-unicode characters from input.
     * If the input is an array, both values and keys will be cleaned up.
     *
     * @param mixed $input String or array.
     *
     * @return mixed String or array
     */
    public static function clean($input)
    {
        // handle input of type array
        if (is_array($input)) {
            foreach (array_keys($input) as $key) {
                $k = is_string($key) ? self::clean($key) : $key;
                $v = self::clean($input[$key]);

                if ($k !== $key) {
                    unset($input[$key]);
                    if (!array_key_exists($k, $input)) {
                        $input[$k] = $v;
                    }
                }
                else {
                    $input[$k] = $v;
                }
            }
            return $input;
        }

        if (!is_string($input) || $input == '') {
            return $input;
        }

        $msch = mb_substitute_character();
        mb_substitute_character('none');
        $res = mb_convert_encoding($input, 'UTF-8', 'UTF-8');
        mb_substitute_character($msch);

        return $res;
    }
}
