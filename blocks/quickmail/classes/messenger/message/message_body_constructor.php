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

use block_quickmail\persistents\message;
use block_quickmail\messenger\message\body_substitution_code_parser;
use block_quickmail\messenger\message\data_mapper\substitution_code_data_mapper;
use block_quickmail\messenger\message\substitution_code;

class message_body_constructor {

    public $message;
    public $user;
    public $course;
    public $allowed_substitution_code_classes;

    public function __construct(message $message, $user, $course = null) {
        $this->message = $message;
        $this->user = $user;
        $this->course = $course;
        $this->set_allowed_substitution_code_classes();
    }

    /**
     * Returns a message body which has the given message's recipient user and course data injected into any substitution codes
     *
     * @param  message  $message
     * @return string
     */
    public static function get_formatted_body(message $message, $user, $course = null) {
        $constructor = new self($message, $user, $course);

        // Get all codes that appear in the message.
        $codes = body_substitution_code_parser::get_codes($constructor->get_raw_body());

        // Get an associative array of code => value.
        // TODO: Need to figure out how to inject "object" here.
        $mapped = substitution_code_data_mapper::map_codes($codes, $user, $constructor->get_course());

        $body = $constructor->get_raw_body();

        // Iterate through each code, replacing the value with a mapped value.
        foreach ($codes as $code) {
            $body = str_replace($constructor->delimit_code($code), $mapped[$code], $body);
        }

        return $body;
    }

    /**
     * Returns the given code delimited with delimiters
     *
     * @param  string  $code
     * @return string
     */
    private function delimit_code($code) {
        return implode('', [substitution_code::first_delimiter(), $code, substitution_code::last_delimiter()]);
    }

    /**
     * Sets an array of "allowed substitution codes" for this message body constructor instance
     */
    private function set_allowed_substitution_code_classes() {
        $this->allowed_substitution_code_classes = $this->message->get_substitution_code_classes();
    }

    /**
     * Helper function that returns the message course
     *
     * @return object
     */
    private function get_course() {
        return $this->message->get_message_scope() == 'compose'
            ? $this->course
            : null;
    }

    /**
     * Helper for returning this constructor's original message body
     *
     * @return string
     */
    private function get_raw_body() {
        return $this->message->get('body');
    }

}
