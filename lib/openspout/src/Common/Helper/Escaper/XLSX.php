<?php

namespace OpenSpout\Common\Helper\Escaper;

/**
 * Provides functions to escape and unescape data for XLSX files.
 */
class XLSX implements EscaperInterface
{
    /** @var bool Whether the escaper has already been initialized */
    private $isAlreadyInitialized = false;

    /** @var string Regex pattern to detect control characters that need to be escaped */
    private $escapableControlCharactersPattern;

    /** @var string[] Map containing control characters to be escaped (key) and their escaped value (value) */
    private $controlCharactersEscapingMap;

    /** @var string[] Map containing control characters to be escaped (value) and their escaped value (key) */
    private $controlCharactersEscapingReverseMap;

    /**
     * Escapes the given string to make it compatible with XLSX.
     *
     * @param string $string The string to escape
     *
     * @return string The escaped string
     */
    public function escape($string)
    {
        $this->initIfNeeded();

        $escapedString = $this->escapeControlCharacters($string);
        // @NOTE: Using ENT_QUOTES as XML entities ('<', '>', '&') as well as
        //        single/double quotes (for XML attributes) need to be encoded.
        return htmlspecialchars($escapedString, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Unescapes the given string to make it compatible with XLSX.
     *
     * @param string $string The string to unescape
     *
     * @return string The unescaped string
     */
    public function unescape($string)
    {
        $this->initIfNeeded();

        // ==============
        // =   WARNING  =
        // ==============
        // It is assumed that the given string has already had its XML entities decoded.
        // This is true if the string is coming from a DOMNode (as DOMNode already decode XML entities on creation).
        // Therefore there is no need to call "htmlspecialchars_decode()".
        return $this->unescapeControlCharacters($string);
    }

    /**
     * Initializes the control characters if not already done.
     */
    protected function initIfNeeded()
    {
        if (!$this->isAlreadyInitialized) {
            $this->escapableControlCharactersPattern = $this->getEscapableControlCharactersPattern();
            $this->controlCharactersEscapingMap = $this->getControlCharactersEscapingMap();
            $this->controlCharactersEscapingReverseMap = array_flip($this->controlCharactersEscapingMap);

            $this->isAlreadyInitialized = true;
        }
    }

    /**
     * @return string Regex pattern containing all escapable control characters
     */
    protected function getEscapableControlCharactersPattern()
    {
        // control characters values are from 0 to 1F (hex values) in the ASCII table
        // some characters should not be escaped though: "\t", "\r" and "\n".
        return '[\x00-\x08'.
                // skipping "\t" (0x9) and "\n" (0xA)
                '\x0B-\x0C'.
                // skipping "\r" (0xD)
                '\x0E-\x1F]';
    }

    /**
     * Builds the map containing control characters to be escaped
     * mapped to their escaped values.
     * "\t", "\r" and "\n" don't need to be escaped.
     *
     * NOTE: the logic has been adapted from the XlsxWriter library (BSD License)
     *
     * @see https://github.com/jmcnamara/XlsxWriter/blob/f1e610f29/xlsxwriter/sharedstrings.py#L89
     *
     * @return string[]
     */
    protected function getControlCharactersEscapingMap()
    {
        $controlCharactersEscapingMap = [];

        // control characters values are from 0 to 1F (hex values) in the ASCII table
        for ($charValue = 0x00; $charValue <= 0x1F; ++$charValue) {
            $character = \chr($charValue);
            if (preg_match("/{$this->escapableControlCharactersPattern}/", $character)) {
                $charHexValue = dechex($charValue);
                $escapedChar = '_x'.sprintf('%04s', strtoupper($charHexValue)).'_';
                $controlCharactersEscapingMap[$escapedChar] = $character;
            }
        }

        return $controlCharactersEscapingMap;
    }

    /**
     * Converts PHP control characters from the given string to OpenXML escaped control characters.
     *
     * Excel escapes control characters with _xHHHH_ and also escapes any
     * literal strings of that type by encoding the leading underscore.
     * So "\0" -> _x0000_ and "_x0000_" -> _x005F_x0000_.
     *
     * NOTE: the logic has been adapted from the XlsxWriter library (BSD License)
     *
     * @see https://github.com/jmcnamara/XlsxWriter/blob/f1e610f29/xlsxwriter/sharedstrings.py#L89
     *
     * @param string $string String to escape
     *
     * @return string
     */
    protected function escapeControlCharacters($string)
    {
        $escapedString = $this->escapeEscapeCharacter($string);

        // if no control characters
        if (!preg_match("/{$this->escapableControlCharactersPattern}/", $escapedString)) {
            return $escapedString;
        }

        return preg_replace_callback("/({$this->escapableControlCharactersPattern})/", function ($matches) {
            return $this->controlCharactersEscapingReverseMap[$matches[0]];
        }, $escapedString);
    }

    /**
     * Escapes the escape character: "_x0000_" -> "_x005F_x0000_".
     *
     * @param string $string String to escape
     *
     * @return string The escaped string
     */
    protected function escapeEscapeCharacter($string)
    {
        return preg_replace('/_(x[\dA-F]{4})_/', '_x005F_$1_', $string);
    }

    /**
     * Converts OpenXML escaped control characters from the given string to PHP control characters.
     *
     * Excel escapes control characters with _xHHHH_ and also escapes any
     * literal strings of that type by encoding the leading underscore.
     * So "_x0000_" -> "\0" and "_x005F_x0000_" -> "_x0000_"
     *
     * NOTE: the logic has been adapted from the XlsxWriter library (BSD License)
     *
     * @see https://github.com/jmcnamara/XlsxWriter/blob/f1e610f29/xlsxwriter/sharedstrings.py#L89
     *
     * @param string $string String to unescape
     *
     * @return string
     */
    protected function unescapeControlCharacters($string)
    {
        $unescapedString = $string;

        foreach ($this->controlCharactersEscapingMap as $escapedCharValue => $charValue) {
            // only unescape characters that don't contain the escaped escape character for now
            $unescapedString = preg_replace("/(?<!_x005F)({$escapedCharValue})/", $charValue, $unescapedString);
        }

        return $this->unescapeEscapeCharacter($unescapedString);
    }

    /**
     * Unecapes the escape character: "_x005F_x0000_" => "_x0000_".
     *
     * @param string $string String to unescape
     *
     * @return string The unescaped string
     */
    protected function unescapeEscapeCharacter($string)
    {
        return preg_replace('/_x005F(_x[\dA-F]{4}_)/', '$1', $string);
    }
}
