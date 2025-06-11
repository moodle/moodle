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
 * External functions for returning course information.
 *
 * @package    local_remote_courses
 * @copyright  2015 Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

require_once("$CFG->dirroot/enrol/externallib.php");

/**
 * Returns a user's courses based on username.
 *
 * @package   local_remote_courses
 * @copyright 2015 Lafayette College ITS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_remote_courses_external extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_courses_by_username_parameters() {
        return new external_function_parameters(
                array(
                    'username' => new external_value(PARAM_USERNAME, 'username'),
                )
        );
    }

    /**
     * Get a user's enrolled courses.
     *
     * This is a wrapper of core_enrol_get_users_courses(). It accepts
     * the username instead of the id and does some optional filtering
     * logic on the idnumber.
     *
     * @param string $username
     * @return array
     */
    public static function get_courses_by_username($username) {
        global $DB;

        // Validate parameters passed from webservice.
        $params = self::validate_parameters(self::get_courses_by_username_parameters(), array('username' => $username));

        // Extract the userid from the username.
        $userid = $DB->get_field('user', 'id', array('username' => $username));

        // Get the courses.
        $courses = core_enrol_external::get_users_courses($userid);

        // Process results: apply term logic and drop enrollment counts.
        $result = array();
        $extracttermcode = get_config('local_remote_courses', 'extracttermcode');

        foreach ($courses as $course) {
            $roles = array(); // Reset roles for each course.

            $coursecontext = context_course::instance($course['id']);
            $userroles = get_user_roles($coursecontext, $userid, false);
            foreach ($userroles as $role) {
                $roles[] = $role->shortname;
            }
            // Apply term logic.
            if (empty($extracttermcode) || empty($course['idnumber'])) {
                $term = '';
            } else {
                preg_match($extracttermcode, $course['idnumber'], $term);
                if (!empty($term) && count($term) >= 2) {
                    $term = $term[1];
                } else {
                    $term = '';
                }
            }

            $result[] = array(
                'id' => $course['id'],
                'shortname' => $course['shortname'],
                'fullname' => $course['fullname'],
                'term' => $term,
                'visible' => $course['visible'],
                'roles' => $roles,
            );
        }

        // Sort courses by recent access.
        $courselist = self::get_recent_courses($userid);
        $unsorted = $result;
        $sorted = array();
        foreach ($result as $cid => $course) {
            $sort = array_search($course['id'], $courselist);
            if ($sort !== false) {
                $sorted[$sort] = $course;
                unset($unsorted[$cid]);
            }
        }

        ksort($sorted);
        $result = array_merge($sorted, $unsorted);
        return $result;
    }

    /**
     * Retrieves the courses viewed by the user.
     *
     * This function queries the active logstore for access information.
     *
     * @param int $userid
     * @return array
     */
    protected static function get_recent_courses($userid) {
        $manager = get_log_manager();
        $selectreaders = $manager->get_readers();
        if ($selectreaders) {
            $courses = array();
            $reader = reset($selectreaders);

            // Selection criteria.
            $joins = array(
                "userid = :userid",
                "courseid != 1",
                "eventname = :eventname"
            );
            $selector = implode(' AND ', $joins);
            $events = $reader->get_events_select($selector, array('userid' => $userid, 'eventname' => '\core\event\course_viewed'),
                    'timecreated DESC', 0, 0);
            foreach ($events as $event) {
                $courses[] = $event->get_data()['courseid'];
            }
            return $courses;

        } else {
            // No available log reader found.
            return array();
        }
    }

    /**
     * Returns description of get_courses_by_username_returns() result value.
     *
     * @return \core_external\external_description
     */
    public static function get_courses_by_username_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id'        => new external_value(PARAM_INT, 'id of course'),
                    'shortname' => new external_value(PARAM_RAW, 'short name of course'),
                    'fullname'  => new external_value(PARAM_RAW, 'long name of course'),
                    'term'      => new external_value(PARAM_RAW, 'the course term, if applicable'),
                    'visible'   => new external_value(PARAM_INT, '1 means visible, 0 means hidden course'),
                    'roles'     => new external_multiple_structure(
                        new external_value(PARAM_RAW, 'role shortname'), 'user roles in the course', VALUE_OPTIONAL),
                )
            )
        );
    }
}
