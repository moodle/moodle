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
 * @package    moodlecore
 * @subpackage backup-factories
 * @copyright  2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Non instantiable helper class providing different restore checks
 *
 * This class contains various static methods available in order to easily
 * perform a bunch of restore architecture tests
 *
 * TODO: Finish phpdocs
 */
abstract class restore_check {

    public static function check_courseid($courseid) {
        global $DB;
        // id must exist in course table
        if (! $DB->record_exists('course', array('id' => $courseid))) {
            throw new restore_controller_exception('restore_check_course_not_exists', $courseid);
        }
        return true;
    }

    public static function check_user($userid) {
        global $DB;
        // userid must exist in user table
        if (! $DB->record_exists('user', array('id' => $userid))) {
            throw new restore_controller_exception('restore_check_user_not_exists', $userid);
        }
        return true;
    }

    public static function check_security($restore_controller, $apply) {

        debugging('TODO: Not applying security yet!', DEBUG_DEVELOPER); // TODO: Add once plan is complete
        return true;
    }
}
