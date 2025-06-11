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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\messenger\message;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\messenger\message\substitution_code;
use block_quickmail_string;
use block_quickmail\exceptions\body_parser_exception;

class body_substitution_code_parser {

    public $body;
    public $codes = [];

    public function __construct($body) {
        $this->body = trim($body);
    }

    /**
     * Returns an array of substiution codes from the given body
     *
     * @return array
     */
    public static function get_codes($body) {
        $parser = new self($body);

        $parser->parse_codes();

        return $parser->codes;
    }

    /**
     * Validate the message body to make sure:
     *  - any substitution codes are formatted properly
     *  - any substitution codes are within the given code classes
     *
     * @param  string  $body         the message body to be validated
     * @param  array   $codeclasses  substitition code classes that are allowed to be used
     * @return array   any invalid code messages
     */
    public static function validate_body($body, $codeclasses = []) {
        $parser = new self($body);

        $allowedcodes = substitution_code::get($codeclasses);

        $unallowedcodes = $parser->validate_codes($allowedcodes);

        if (empty($unallowedcodes)) {
            return [];
        }

        $invalidmessages = array_map(function($code) {
            return block_quickmail_string::get('invalid_custom_data_key', $code);
        }, $unallowedcodes);

        return $invalidmessages;
    }

    /**
     * Parses through the body, returning an array of any found codes to the stack
     *
     * @return array
     */
    public function parse_codes() {
        // Make a copy of the message body for manipulation.
        $message = '_' . $this->body;

        // While there still exists a substitution code in the message body.
        while ($nextfirstdelimiter = strpos($message, substitution_code::first_delimiter())) {
            // Trim up until the delimiter.
            $message = substr($message, $nextfirstdelimiter + strlen(substitution_code::first_delimiter()));

            $nextlastdelimiter = strpos($message, substitution_code::last_delimiter());

            // Get the substitution code.
            $code = substr($message, 0, $nextlastdelimiter);

            // Add to the stack.
            $this->add_code($code);

            // Trim the value and ending delimiter out of the remaining message and continue.
            $message = '_' . substr($message, $nextlastdelimiter + strlen(substitution_code::last_delimiter()));
        }

        return $this->codes;
    }

    /**
     * Parses through the body and throws an exception if an error was found
     *
     * @param  array  $allowedcodes  substitution codes that are allowed to be present in body
     * @return array
     * @throws body_parser_exception(message) if codes formatted improperly
     * @throws body_parser_invalid_codes_exception(message, [codes]) if unsupported codes are found
     */
    public function validate_codes($allowedcodes = []) {
        if (empty($allowedcodes)) {
            $this->throw_parser_exception(block_quickmail_string::get('invalid_custom_data_not_allowed'));
        }

        // Make a copy of the message body for manipulation.
        $message = '_' . $this->body;

        // First, get the position of first delimiters.
        $firstfirstdelimiterpos = strpos($message, substitution_code::first_delimiter());
        $firstlastdelimiterpos = strpos($message, substitution_code::last_delimiter());

        // If a "last delimiter" was found.
        if ($firstlastdelimiterpos !== false) {
            // And a "first delimiter" was not found.
            if ($firstfirstdelimiterpos == false) {
                $this->throw_parser_exception(block_quickmail_string::get('invalid_custom_data_delimiters'));
                // Or the first "first delimiter" appears after the first "last delimiter".
            } else if ($firstfirstdelimiterpos > $firstlastdelimiterpos) {
                $this->throw_parser_exception(block_quickmail_string::get('invalid_custom_data_delimiters'));
            }
        }

        // While there still exists a substitution code in the message body.
        while ($nextfirstdelimiter = strpos($message, substitution_code::first_delimiter())) {
            // Trim up until the delimiter.
            $message = substr($message, $nextfirstdelimiter + strlen(substitution_code::first_delimiter()));

            // If no ending delimiter, no bueno.
            if (!$nextlastdelimiter = strpos($message, substitution_code::last_delimiter())) {
                $this->throw_parser_exception(block_quickmail_string::get('invalid_custom_data_delimiters'));
            }

            // Get the substitution code.
            $code = substr($message, 0, $nextlastdelimiter);

            if (strpos($code, ' ') !== false) {
                $this->throw_parser_exception(block_quickmail_string::get('invalid_custom_data_delimiters'));
            }

            // Add to the stack.
            $this->add_code($code);

            // Trim the value and ending delimiter out of the remaining message and continue.
            $message = '_' . substr($message, $nextlastdelimiter + strlen(substitution_code::last_delimiter()));
        }

        $unallowedcodes = [];

        foreach ($this->codes as $found) {
            if (!in_array($found, $allowedcodes)) {
                array_push($unallowedcodes, $found);
            }
        }

        return $unallowedcodes;
    }

    /**
     * Adds delimiters to the given code and adds to the code stack
     *
     * @param string $code
     */
    private function add_code($code) {
        $this->codes[] = $code;
    }

    private function throw_parser_exception($message) {
        throw new body_parser_exception($message);
    }

}
