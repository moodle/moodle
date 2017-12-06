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
 * Externallib.php file for attendance plugin.
 *
 * @package    mod_attendance
 * @copyright  2015 Caio Bressan Doneda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");
require_once(dirname(__FILE__).'/classes/attendance_webservices_handler.php');

/**
 * Class mod_wsattendance_external
 * @copyright  2015 Caio Bressan Doneda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_wsattendance_external extends external_api {

    /**
     * Get parameter list.
     * @return external_function_parameters
     */
    public static function get_courses_with_today_sessions_parameters() {
        return new external_function_parameters (
                    array('userid' => new external_value(PARAM_INT, 'User id.',  VALUE_DEFAULT, 0)));
    }

    /**
     * Get list of courses with active sessions for today.
     * @param int $userid
     * @return array
     */
    public static function get_courses_with_today_sessions($userid) {
        return attendance_handler::get_courses_with_today_sessions($userid);
    }

    /**
     * Get structure of an attendance session.
     *
     * @return array
     */
    private static function get_session_structure() {
        $session = array('id' => new external_value(PARAM_INT, 'Session id.'),
                         'attendanceid' => new external_value(PARAM_INT, 'Attendance id.'),
                         'groupid' => new external_value(PARAM_INT, 'Group id.'),
                         'sessdate' => new external_value(PARAM_INT, 'Session date.'),
                         'duration' => new external_value(PARAM_INT, 'Session duration.'),
                         'lasttaken' => new external_value(PARAM_INT, 'Session last taken time.'),
                         'lasttakenby' => new external_value(PARAM_INT, 'ID of the last user that took this session.'),
                         'timemodified' => new external_value(PARAM_INT, 'Time modified.'),
                         'description' => new external_value(PARAM_TEXT, 'Session description.'),
                         'descriptionformat' => new external_value(PARAM_INT, 'Session description format.'),
                         'studentscanmark' => new external_value(PARAM_INT, 'Students can mark their own presence.'),
                         'statusset' => new external_value(PARAM_INT, 'Session statusset.'));

        return $session;
    }

    /**
     * Show structure of return.
     * @return external_multiple_structure
     */
    public static function get_courses_with_today_sessions_returns() {
        $todaysessions = self::get_session_structure();

        $attendanceinstances = array('name' => new external_value(PARAM_TEXT, 'Attendance name.'),
                                      'today_sessions' => new external_multiple_structure(
                                                          new external_single_structure($todaysessions)));

        $courses = array('shortname' => new external_value(PARAM_TEXT, 'short name of a moodle course.'),
                         'fullname' => new external_value(PARAM_TEXT, 'full name of a moodle course.'),
                         'attendance_instances' => new external_multiple_structure(
                                                   new external_single_structure($attendanceinstances)));

        return new external_multiple_structure(new external_single_structure(($courses)));
    }

    /**
     * Get session params.
     *
     * @return external_function_parameters
     */
    public static function get_session_parameters() {
        return new external_function_parameters (
                    array('sessionid' => new external_value(PARAM_INT, 'session id')));
    }

    /**
     * Get session.
     *
     * @param int $sessionid
     * @return mixed
     */
    public static function get_session($sessionid) {
        return attendance_handler::get_session($sessionid);
    }

    /**
     * Show return values of get_session.
     *
     * @return external_single_structure
     */
    public static function get_session_returns() {
        $statuses = array('id' => new external_value(PARAM_INT, 'Status id.'),
                          'attendanceid' => new external_value(PARAM_INT, 'Attendance id.'),
                          'acronym' => new external_value(PARAM_TEXT, 'Status acronym.'),
                          'description' => new external_value(PARAM_TEXT, 'Status description.'),
                          'grade' => new external_value(PARAM_FLOAT, 'Status grade.'),
                          'visible' => new external_value(PARAM_INT, 'Status visibility.'),
                          'deleted' => new external_value(PARAM_INT, 'informs if this session was deleted.'),
                          'setnumber' => new external_value(PARAM_INT, 'Set number.'));

        $users = array('id' => new external_value(PARAM_INT, 'User id.'),
                       'firstname' => new external_value(PARAM_TEXT, 'User first name.'),
                       'lastname' => new external_value(PARAM_TEXT, 'User last name.'));

        $attendancelog = array('studentid' => new external_value(PARAM_INT, 'Student id.'),
                                'statusid' => new external_value(PARAM_TEXT, 'Status id (last time).'),
                                'remarks' => new external_value(PARAM_TEXT, 'Last remark.'),
                                'id' => new external_value(PARAM_TEXT, 'log id.'));

        $session = self::get_session_structure();
        $session['courseid'] = new external_value(PARAM_INT, 'Course moodle id.');
        $session['statuses'] = new external_multiple_structure(new external_single_structure($statuses));
        $session['attendance_log'] = new external_multiple_structure(new external_single_structure($attendancelog));
        $session['users'] = new external_multiple_structure(new external_single_structure($users));

        return new external_single_structure($session);
    }

    /**
     * Update user status params.
     *
     * @return external_function_parameters
     */
    public static function update_user_status_parameters() {
        return new external_function_parameters(
                    array('sessionid' => new external_value(PARAM_INT, 'Session id'),
                          'studentid' => new external_value(PARAM_INT, 'Student id'),
                          'takenbyid' => new external_value(PARAM_INT, 'Id of the user who took this session'),
                          'statusid' => new external_value(PARAM_INT, 'Status id'),
                          'statusset' => new external_value(PARAM_TEXT, 'Status set of session')));
    }

    /**
     * Update user status.
     *
     * @param int $sessionid
     * @param int $studentid
     * @param int $takenbyid
     * @param int $statusid
     * @param int $statusset
     */
    public static function update_user_status($sessionid, $studentid, $takenbyid, $statusid, $statusset) {
        return attendance_handler::update_user_status($sessionid, $studentid, $takenbyid, $statusid, $statusset);
    }

    /**
     * Show return values.
     * @return external_value
     */
    public static function update_user_status_returns() {
        return new external_value(PARAM_TEXT, 'Http code');
    }
}
