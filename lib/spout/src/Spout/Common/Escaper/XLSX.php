<?php

namespace Box\Spout\Common\Escaper;

/**
 * Class XLSX
 * Provides functions to escape and unescape data for XLSX files
 *
 * @package Box\Spout\Common\Escaper
 */
class XLSX implements EscaperInterface
{
    /** @var string[] Control characters to be escaped */
    protected $controlCharactersEscapingMap;

    /**
     *
     */
    public function __construct()
    {
        $this->controlCharactersEscapingMap = $this->getControlCharactersEscapingMap();
    }

    /**
     * Escapes the given string to make it compatible with XLSX
     *
     * @param string $string The string to escape
     * @return string The escaped string
     */
    public function escape($string)
    {
        $escapedString = $this->escapeControlCharacters($string);
        $escapedString = htmlspecialchars($escapedString, ENT_QUOTES);

        return $escapedString;
    }

    /**
     * Unescapes the given string to make it compatible with XLSX
     *
     * @param string $string The string to unescape
     * @return string The unescaped string
     */
    public function unescape($string)
    {
        $unescapedString = htmlspecialchars_decode($string, ENT_QUOTES);
        $unescapedString = $this->unescapeControlCharacters($unescapedString);

        return $unescapedString;
    }

    /**
     * Builds the map containing control characters to be escaped
     * mapped to their escaped values.
     * "\t", "\r" and "\n" don't need to be escaped.
     *
     * NOTE: the logic has been adapted from the XlsxWriter library (BSD License)
     * @link https://github.com/jmcnamara/XlsxWriter/blob/f1e610f29/xlsxwriter/sharedstrings.py#L89
     *
     * @return string[]
     */
    protected function getControlCharactersEscapingMap()
    {
        $controlCharactersEscapingMap = [];
        $whitelistedControlCharacters = ["\t", "\r", "\n"];

        // control characters values are from 0 to 1F (hex values) in the ASCII table
        for ($charValue = 0x0; $charValue <= 0x1F; $charValue++) {
            if (!in_array(chr($charValue), $whitelistedControlCharacters)) {
                $charHexValue = dechex($charValue);
                $escapedChar = '_x' . sprintf('%04s' , strtoupper($charHexValue)) . '_';
                $controlCharactersEscapingMap[$escapedChar] = chr($charValue);
            }
        }

        return $controlCharactersEscapingMap;
    }

    /**
     * Converts PHP control characters from the given string to OpenXML escaped control characters
     *
     * Excel escapes control characters with _xHHHH_ and also escapes any
     * literal strings of that type by encoding the leading underscore.
     * So "\0" -> _x0000_ and "_x0000_" -> _x005F_x0000_.
     *
     * NOTE: the logic has been adapted from the XlsxWriter library (BSD License)
     * @link https://github.com/jmcnamara/XlsxWriter/blob/f1e610f29/xlsxwriter/sharedstrings.py#L89
     *
     * @param string $string String to escape
     * @return string
     */
    protected function escapeControlCharacters($string)
    {
        $escapedString = $this->escapeEscapeCharacter($string);
        return str_replace(array_values($this->controlCharactersEscapingMap), array_keys($this->controlCharactersEscapingMap), $escapedString);
    }

    /**
     * Escapes the escape character: "_x0000_" -> "_x005F_x0000_"
     *
     * @param string $string String to escape
     * @return string The escaped string
     */
    protected function escapeEscapeCharacter($string)
    {
        return preg_replace('/_(x[\dA-F]{4})_/', '_x005F_$1_', $string);
    }

    /**
     * Converts OpenXML escaped control characters from the given string to PHP control characters
     *
     * Excel escapes control characters with _xHHHH_ and also escapes any
     * literal strings of that type by encoding the leading underscore.
     * So "_x0000_" -> "\0" and "_x005F_x0000_" -> "_x0000_"
     *
     * NOTE: the logic has been adapted from the XlsxWriter library (BSD License)
     * @link https://github.com/jmcnamara/XlsxWriter/blob/f1e610f29/xlsxwriter/sharedstrings.py#L89
     *
     * @param string $string String to unescape
     * @return string
     */
    protected function unescapeControlCharacters($string)
    {
        $unescapedString = $string;
        foreach ($this->controlCharactersEscapingMap as $escapedCharValue => $charValue) {
            // only unescape characters that don't contain the escaped escape character for now
            $unescapedString = preg_replace("/(?<!_x005F)($escapedCharValue)/", $charValue, $unescapedString);
        }

        return $this->unescapeEscapeCharacter($unescapedString);
    }

    /**
     * Unecapes the escape character: "_x005F_x0000_" => "_x0000_"
     *
     * @param string $string String to unescape
     * @return string The unescaped string
     */
    protected function unescapeEscapeCharacter($string)
    {
        return preg_replace('/_x005F(_x[\dA-F]{4}_)/', '$1', $string);
    }
}
