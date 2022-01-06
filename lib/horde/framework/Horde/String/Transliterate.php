<?php
/**
 * Copyright 2014-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Util
 */

/**
 * Provides utility methods used to transliterate a string.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @author    Jan Schneider <jan@horde.org>
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Util
 * @since     2.4.0
 */
class Horde_String_Transliterate
{
    /**
     * Transliterate mapping cache.
     *
     * @var array
     */
    protected static $_map;

    /**
     * Transliterator instance.
     *
     * @var Transliterator
     */
    protected static $_transliterator;

    /**
     * Transliterates an UTF-8 string to ASCII, replacing non-English
     * characters to their English equivalents.
     *
     * Note: there is no guarantee that the output string will be ASCII-only,
     * since any non-ASCII character not in the transliteration list will
     * be ignored.
     *
     * @param string $str  Input string (UTF-8).
     *
     * @return string  Transliterated string (UTF-8).
     */
    public static function toAscii($str)
    {
        $methods = array(
            '_intlToAscii',
            '_iconvToAscii',
            '_fallbackToAscii'
        );

        foreach ($methods as $val) {
            if (($out = call_user_func(array(__CLASS__, $val), $str)) !== false) {
                return $out;
            }
        }

        return $str;
    }

    /**
     * Transliterate using the Transliterator package.
     *
     * @param string $str  Input string (UTF-8).
     *
     * @return mixed  Transliterated string (UTF-8), or false on error.
     */
    protected static function _intlToAscii($str)
    {
        if (class_exists('Transliterator')) {
            if (!isset(self::$_transliterator)) {
                self::$_transliterator = Transliterator::create(
                    'Any-Latin; Latin-ASCII'
                );
            }

            if (!is_null(self::$_transliterator)) {
                /* Returns false on error. */
                return self::$_transliterator->transliterate($str);
            }
        }

        return false;
    }

    /**
     * Transliterate using the iconv extension.
     *
     * @param string $str  Input string (UTF-8).
     *
     * @return mixed  Transliterated string (UTF-8), or false on error.
     */
    protected static function _iconvToAscii($str)
    {
        return extension_loaded('iconv')
            /* Returns false on error. */
            ? iconv('UTF-8', 'ASCII//TRANSLIT', $str)
            : false;
    }

    /**
     * Transliterate using a built-in ASCII mapping.
     *
     * @param string $str  Input string (UTF-8).
     *
     * @return string  Transliterated string (UTF-8).
     */
    protected static function _fallbackToAscii($str)
    {
        if (!isset(self::$_map)) {
            self::$_map = array(
                'À' => 'A',
                'Á' => 'A',
                'Â' => 'A',
                'Ã' => 'A',
                'Ä' => 'A',
                'Å' => 'A',
                'Æ' => 'AE',
                'à' => 'a',
                'á' => 'a',
                'â' => 'a',
                'ã' => 'a',
                'ä' => 'a',
                'å' => 'a',
                'æ' => 'ae',
                'Þ' => 'TH',
                'þ' => 'th',
                'Ç' => 'C',
                'ç' => 'c',
                'Ð' => 'D',
                'ð' => 'd',
                'È' => 'E',
                'É' => 'E',
                'Ê' => 'E',
                'Ë' => 'E',
                'è' => 'e',
                'é' => 'e',
                'ê' => 'e',
                'ë' => 'e',
                'ƒ' => 'f',
                'Ì' => 'I',
                'Í' => 'I',
                'Î' => 'I',
                'Ï' => 'I',
                'ì' => 'i',
                'í' => 'i',
                'î' => 'i',
                'ï' => 'i',
                'Ñ' => 'N',
                'ñ' => 'n',
                'Ò' => 'O',
                'Ó' => 'O',
                'Ô' => 'O',
                'Õ' => 'O',
                'Ö' => 'O',
                'Ø' => 'O',
                'ò' => 'o',
                'ó' => 'o',
                'ô' => 'o',
                'õ' => 'o',
                'ö' => 'o',
                'ø' => 'o',
                'Š' => 'S',
                'ẞ' => 'SS',
                'ß' => 'ss',
                'š' => 's',
                'ś' => 's',
                'Ù' => 'U',
                'Ú' => 'U',
                'Û' => 'U',
                'Ü' => 'U',
                'ù' => 'u',
                'ú' => 'u',
                'û' => 'u',
                'Ý' => 'Y',
                'ý' => 'y',
                'ÿ' => 'y',
                'Ž' => 'Z',
                'ž' => 'z'
            );
        }

        /* This should never return false. */
        return strtr(strval($str), self::$_map);
    }
}
