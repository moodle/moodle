<?php
/**
 * Copyright 2014 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Util
 */

/**
 * Provides utility methods used to transliterate a string.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @author    Jan Schneider <jan@horde.org>
 * @category  Horde
 * @copyright 2014 Horde LLC
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
    static protected $_map;

    /**
     * Transliterator instance.
     *
     * @var Transliterator
     */
    static protected $_transliterator;

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
    static public function toAscii($str)
    {
        switch (true) {
        case class_exists('Transliterator'):
            return self::_intlToAscii($str);
        case extension_loaded('iconv'):
            return self::_iconvToAscii($str);
        default:
            return self::_fallbackToAscii($str);
        }
    }

    /**
     */
    static protected function _intlToAscii($str)
    {
        if (!isset(self::$_transliterator)) {
            self::$_transliterator = Transliterator::create(
                'Any-Latin; Latin-ASCII'
            );
        }
        return self::$_transliterator->transliterate($str);
    }

    /**
     */
    static protected function _iconvToAscii($str)
    {
        return iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    }

    /**
     */
    static protected function _fallbackToAscii($str)
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

        return strtr($str, self::$_map);
    }
}
