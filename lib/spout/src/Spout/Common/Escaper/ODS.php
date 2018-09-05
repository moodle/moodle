<?php

namespace Box\Spout\Common\Escaper;

use Box\Spout\Common\Singleton;

/**
 * Class ODS
 * Provides functions to escape and unescape data for ODS files
 *
 * @package Box\Spout\Common\Escaper
 */
class ODS implements EscaperInterface
{
    use Singleton;

    /**
     * Escapes the given string to make it compatible with XLSX
     *
     * @param string $string The string to escape
     * @return string The escaped string
     */
    public function escape($string)
    {
        if (defined('ENT_DISALLOWED')) {
            // 'ENT_DISALLOWED' ensures that invalid characters in the given document type are replaced.
            // Otherwise control characters like a vertical tab "\v" will make the XML document unreadable by the XML processor
            // @link https://github.com/box/spout/issues/329
            $replacedString = htmlspecialchars($string, ENT_NOQUOTES | ENT_DISALLOWED);
        } else {
            // We are on hhvm or any other engine that does not support ENT_DISALLOWED.
            //
            // @NOTE: Using ENT_NOQUOTES as only XML entities ('<', '>', '&') need to be encoded.
            //        Single and double quotes can be left as is.
            $escapedString =  htmlspecialchars($string, ENT_NOQUOTES);

            // control characters values are from 0 to 1F (hex values) in the ASCII table
            // some characters should not be escaped though: "\t", "\r" and "\n".
            $regexPattern = '[\x00-\x08' .
                            // skipping "\t" (0x9) and "\n" (0xA)
                            '\x0B-\x0C' .
                            // skipping "\r" (0xD)
                            '\x0E-\x1F]';
            $replacedString = preg_replace("/$regexPattern/", '�', $escapedString);
        }

        return $replacedString;
    }

    /**
     * Unescapes the given string to make it compatible with XLSX
     *
     * @param string $string The string to unescape
     * @return string The unescaped string
     */
    public function unescape($string)
    {
        // ==============
        // =   WARNING  =
        // ==============
        // It is assumed that the given string has already had its XML entities decoded.
        // This is true if the string is coming from a DOMNode (as DOMNode already decode XML entities on creation).
        // Therefore there is no need to call "htmlspecialchars_decode()".
        return $string;
    }
}
