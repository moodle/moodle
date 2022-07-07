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
 * Mlang PHP based on David Mudrak phpparser for local_amos.
 *
 * @package    tool_customlang
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_customlang\local\mlang;

use coding_exception;
use moodle_exception;

/**
 * Parser of Moodle strings defined as associative array.
 *
 * Moodle core just includes this file format directly as normal PHP code. However
 * for security reasons, we must not do this for files uploaded by anonymous users.
 * This parser reconstructs the associative $string array without actually including
 * the file.
 */
class phpparser {

    /** @var holds the singleton instance of self */
    private static $instance = null;

    /**
     * Prevents direct creation of object
     */
    private function __construct() {
    }

    /**
     * Prevent from cloning the instance
     */
    public function __clone() {
        throw new coding_exception('Cloning os singleton is not allowed');
    }

    /**
     * Get the singleton instance fo this class
     *
     * @return phpparser singleton instance of phpparser
     */
    public static function get_instance(): phpparser {
        if (is_null(self::$instance)) {
            self::$instance = new phpparser();
        }
        return self::$instance;
    }

    /**
     * Parses the given data in Moodle PHP string format
     *
     * Note: This method is adapted from local_amos as it is highly tested and robust.
     * The priority is keeping it similar to the original one to make it easier to mantain.
     *
     * @param string $data definition of the associative array
     * @param int $format the data format on the input, defaults to the one used since 2.0
     * @return langstring[] array of langstrings of this file
     */
    public function parse(string $data, int $format = 2): array {
        $result = [];
        $strings = $this->extract_strings($data);
        foreach ($strings as $id => $text) {
            $cleaned = clean_param($id, PARAM_STRINGID);
            if ($cleaned !== $id) {
                continue;
            }
            $text = langstring::fix_syntax($text, 2, $format);
            $result[] = new langstring($id, $text);
        }
        return $result;
    }

    /**
     * Low level parsing method
     *
     * Note: This method is adapted from local_amos as it is highly tested and robust.
     * The priority is keeping it similar to the original one to make it easier to mantain.
     *
     * @param string $data
     * @return string[] the data strings
     */
    protected function extract_strings(string $data): array {

        $strings = []; // To be returned.

        if (empty($data)) {
            return $strings;
        }

        // Tokenize data - we expect valid PHP code.
        $tokens = token_get_all($data);

        // Get rid of all non-relevant tokens.
        $rubbish = [T_WHITESPACE, T_INLINE_HTML, T_COMMENT, T_DOC_COMMENT, T_OPEN_TAG, T_CLOSE_TAG];
        foreach ($tokens as $i => $token) {
            if (is_array($token)) {
                if (in_array($token[0], $rubbish)) {
                    unset($tokens[$i]);
                }
            }
        }

        $id = null;
        $text = null;
        $line = 0;
        $expect = 'STRING_VAR'; // The first expected token is '$string'.

        // Iterate over tokens and look for valid $string array assignment patterns.
        foreach ($tokens as $token) {
            $foundtype = null;
            $founddata = null;
            if (is_array($token)) {
                $foundtype = $token[0];
                $founddata = $token[1];
                if (!empty($token[2])) {
                    $line = $token[2];
                }

            } else {
                $foundtype = 'char';
                $founddata = $token;
            }

            if ($expect == 'STRING_VAR') {
                if ($foundtype === T_VARIABLE and $founddata === '$string') {
                    $expect = 'LEFT_BRACKET';
                    continue;
                } else {
                    // Allow other code at the global level.
                    continue;
                }
            }

            if ($expect == 'LEFT_BRACKET') {
                if ($foundtype === 'char' and $founddata === '[') {
                    $expect = 'STRING_ID';
                    continue;
                } else {
                    throw new moodle_exception('Parsing error. Expected character [ at line '.$line);
                }
            }

            if ($expect == 'STRING_ID') {
                if ($foundtype === T_CONSTANT_ENCAPSED_STRING) {
                    $id = $this->decapsulate($founddata);
                    $expect = 'RIGHT_BRACKET';
                    continue;
                } else {
                    throw new moodle_exception('Parsing error. Expected T_CONSTANT_ENCAPSED_STRING array key at line '.$line);
                }
            }

            if ($expect == 'RIGHT_BRACKET') {
                if ($foundtype === 'char' and $founddata === ']') {
                    $expect = 'ASSIGNMENT';
                    continue;
                } else {
                    throw new moodle_exception('Parsing error. Expected character ] at line '.$line);
                }
            }

            if ($expect == 'ASSIGNMENT') {
                if ($foundtype === 'char' and $founddata === '=') {
                    $expect = 'STRING_TEXT';
                    continue;
                } else {
                    throw new moodle_exception('Parsing error. Expected character = at line '.$line);
                }
            }

            if ($expect == 'STRING_TEXT') {
                if ($foundtype === T_CONSTANT_ENCAPSED_STRING) {
                    $text = $this->decapsulate($founddata);
                    $expect = 'SEMICOLON';
                    continue;
                } else {
                    throw new moodle_exception(
                        'Parsing error. Expected T_CONSTANT_ENCAPSED_STRING array item value at line '.$line
                    );
                }
            }

            if ($expect == 'SEMICOLON') {
                if (is_null($id) or is_null($text)) {
                    throw new moodle_exception('Parsing error. NULL string id or value at line '.$line);
                }
                if ($foundtype === 'char' and $founddata === ';') {
                    if (!empty($id)) {
                        $strings[$id] = $text;
                    }
                    $id = null;
                    $text = null;
                    $expect = 'STRING_VAR';
                    continue;
                } else {
                    throw new moodle_exception('Parsing error. Expected character ; at line '.$line);
                }
            }

        }

        return $strings;
    }

    /**
     * Given one T_CONSTANT_ENCAPSED_STRING, return its value without quotes
     *
     * Also processes escaped quotes inside the text.
     *
     * Note: This method is taken directly from local_amos as it is highly tested and robust.
     *
     * @param string $text value obtained by token_get_all()
     * @return string value without quotes
     */
    protected function decapsulate(string $text): string {

        if (strlen($text) < 2) {
            throw new moodle_exception('Parsing error. Expected T_CONSTANT_ENCAPSED_STRING in decapsulate()');
        }

        if (substr($text, 0, 1) == "'" and substr($text, -1) == "'") {
            // Single quoted string.
            $text = trim($text, "'");
            $text = str_replace("\'", "'", $text);
            $text = str_replace('\\\\', '\\', $text);
            return $text;

        } else if (substr($text, 0, 1) == '"' and substr($text, -1) == '"') {
            // Double quoted string.
            $text = trim($text, '"');
            $text = str_replace('\"', '"', $text);
            $text = str_replace('\\\\', '\\', $text);
            return $text;

        } else {
            throw new moodle_exception(
                'Parsing error. Unexpected quotation in T_CONSTANT_ENCAPSED_STRING in decapsulate(): '.$text
            );
        }
    }
}
