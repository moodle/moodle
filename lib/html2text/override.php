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
 * Run time overrides for Html2Text
 *
 * This allows us to monkey patch the mb_* functions used in Html2Text to use Moodle's core_text functionality.
 *
 * @package    core
 * @copyright  2016 Andrew Nicols <andrew@nicols.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Html2Text;

/**
 * Set the encoding to be used by our monkey patched mb_ functions.
 *
 * When called with $encoding !== null, we set  the static $intenalencoding
 * variable, which is used for subsequent calls.
 *
 * When called with no value for $encoding, we return the previously defined
 * $internalencoding.
 *
 * This is necessary as we need to maintain the state of mb_internal_encoding
 * across calls to other mb_* functions. Check how it is used in the other mb_*
 * functions defined here - if no encoding is provided we fallback to what was
 * set here, otherwise we used the given encoding.
 *
 * @staticvar string $internalencoding The encoding to be used across mb_* calls.
 * @param     string $encoding When given, sets $internalencoding
 * @return    mixed
 */
function mb_internal_encoding($encoding = null) {
    static $internalencoding = 'utf-8';
    if ($encoding !== null) {
        $internalencoding = $encoding;
        return true;
    } else {
        return $internalencoding;
    }
}

/**
 * Performs a multi-byte safe substr() operation based on number of characters.
 * Position is counted from the beginning of str. First character's position is
 * 0. Second character position is 1, and so on.
 *
 * @param string $str      The string to extract the substring from.
 * @param int    $start    If start is non-negative, the returned string will
 *                         start at the start'th position in string, counting
 *                         from zero. For instance, in the string 'abcdef',
 *                         the character at position 0 is 'a', the character
 *                         at position 2 is 'c', and so forth.
 * @param int    $length   Maximum number of characters to use from str. If
 *                         omitted or NULL is passed, extract all characters
 *                         to the end of the string.
 * @param string $encoding The encoding parameter is the character encoding.
 *                         If it is omitted, the internal character encoding
 *                         value will be used.
 *
 * @return string The portion of str specified by the start and length parameters.
 */
function mb_substr($str, $start, $length = null, $encoding = null) {
    if ($encoding === null) {
        $encoding = mb_internal_encoding();
    }
    return \core_text::substr($str, $start, $length, $encoding);
}

/**
 * Gets the length of a string.
 *
 * @param string $str      The string being checked for length.
 * @param string $encoding The encoding parameter is the character encoding.
 *                         If it is omitted, the internal character encoding
 *                         value will be used.
 *
 * @return int The number of characters in str having character encoding $encoding.
 *             A multibyte character is counted as 1.
 */
function mb_strlen($str, $encoding = null) {
    if ($encoding === null) {
        $encoding = mb_internal_encoding();
    }
    return \core_text::strlen($str, $encoding);
}

/**
 * Returns $str with all alphabetic chatacters converted to lowercase.
 *
 * @param string $str      The string being lowercased.
 * @param string $encoding The encoding parameter is the character encoding.
 *                         If it is omitted, the internal character encoding
 *                         value will be used.
 *
 * @return string The string with all alphabetic characters converted to lowercase.
 */
function mb_strtolower($str, $encoding = null) {
    if ($encoding === null) {
        $encoding = mb_internal_encoding();
    }
    return \core_text::strtolower($str, $encoding);
}

/**
 *
 * @param string The string being uppercased
 * @param string $encoding The encoding parameter is the character encoding.
 *                         If it is omitted, the internal character encoding
 *                         value will be used.
 *
 * @return string The string with all alphabetic characters converted to uppercase.
 */
function mb_strtoupper($str, $encoding = null) {
    if ($encoding === null) {
        $encoding = mb_internal_encoding();
    }
    return \core_text::strtoupper($str, $encoding);
}
