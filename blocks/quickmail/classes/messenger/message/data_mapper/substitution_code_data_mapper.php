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

namespace block_quickmail\messenger\message\data_mapper;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\messenger\message\data_mapper\maps_user_data;
use block_quickmail\messenger\message\data_mapper\maps_course_data;
use block_quickmail\messenger\message\data_mapper\maps_activity_data;
use block_quickmail_string;

class substitution_code_data_mapper {

    use maps_user_data,
        maps_course_data,
        maps_activity_data;

    public static $dateformat = 'F j, Y';

    public $codes;
    public $user;
    public $course;

    /**
     * Constructs substitution_code_data_mapper
     *
     * @param array   $codes  an array of substitution codes to map
     * @param object  $user
     * @param object  $course  (optional, defaults to null)
     */
    public function __construct($codes, $user, $course = null) {
        $this->codes = $codes;
        $this->user = $user;
        $this->course = $course;
    }

    /**
     * Returns an associative array of code => value
     *
     * @param  array   $codes
     * @param  object  $user
     * @param  object  $course
     * @return array
     */
    public static function map_codes($codes, $user, $course = null) {
        $mapper = new self($codes, $user, $course);

        $mapped = [];

        foreach ($mapper->codes as $code) {
            $mapped[$code] = $mapper->get_code_data($code);
        }

        return $mapped;
    }

    /**
     * Returns a calculated value from a given code, default to empty string
     *
     * @param  string  $code
     * @return string
     */
    public function get_code_data($code) {
        $func = 'get_data_' . $code;

        $data = $this->$func();

        return $data ?: '';
    }

    /**
     * Returns a human readable date from the given timestamp
     *
     * @param  int  $timestamp
     * @return string
     */
    public function format_mapped_date($timestamp = 0) {
        if (!$timestamp) {
            return block_quickmail_string::get('never');
        }

        return date(self::$dateformat, $timestamp);
    }

}
