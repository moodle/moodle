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
 * External grading API
 *
 * @package    core_grading
 * @since      Moodle 2.5
 * @copyright  2013 Paul Charsley
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// NOTE: add any new core_grades_ classes to /lib/classes/ directory.

/**
 * core grading functions. Renamed to core_grading_external
 *
 * @since Moodle 2.5
 * @deprecated since 2.6 See MDL-30085. Please do not use this class any more.
 * @see core_grading_external
 */
class core_grade_external extends external_api {

    public static function get_definitions_parameters() {
        return core_grading_external::get_definitions_parameters();
    }

    public static function get_definitions($cmids, $areaname, $activeonly = false) {
        return core_grading_external::get_definitions($cmids, $areaname, $activeonly);
    }

    public static function get_definitions_returns() {
        return core_grading_external::get_definitions_returns();
    }

    /**
     * Marking the method as deprecated.
     *
     * @return bool
     */
    public static function get_definitions_is_deprecated() {
        return true;
    }
}
