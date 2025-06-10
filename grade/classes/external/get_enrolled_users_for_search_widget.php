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

namespace core_grades\external;

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;
use moodle_url;
use core_user;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/grade/lib.php');

/**
 * Get the enrolled users within and map some fields to the returned array of user objects.
 *
 * @package    core_grades
 * @copyright  2022 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.1
 */
class get_enrolled_users_for_search_widget extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters (
            [
                'courseid' => new external_value(PARAM_INT, 'Course Id', VALUE_REQUIRED),
                'actionbaseurl' => new external_value(PARAM_URL, 'The base URL for the user option', VALUE_REQUIRED),
                'groupid' => new external_value(PARAM_INT, 'Group Id', VALUE_DEFAULT, 0)
            ]
        );
    }

    /**
     * Given a course ID find the enrolled users within and map some fields to the returned array of user objects.
     *
     * @param int $courseid
     * @param string $actionbaseurl The base URL for the user option.
     * @param int|null $groupid
     * @return array Users and warnings to pass back to the calling widget.
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws restricted_context_exception
     */
    public static function execute(int $courseid, string $actionbaseurl, ?int $groupid = 0): array {
        global $DB, $PAGE;

        $params = self::validate_parameters(
            self::execute_parameters(),
            [
                'courseid' => $courseid,
                'actionbaseurl' => $actionbaseurl,
                'groupid' => $groupid
            ]
        );

        $warnings = [];
        $coursecontext = \context_course::instance($params['courseid']);
        parent::validate_context($coursecontext);

        require_capability('moodle/course:viewparticipants', $coursecontext);

        $course = $DB->get_record('course', ['id' => $params['courseid']]);
        // Create a graded_users_iterator because it will properly check the groups etc.
        $defaultgradeshowactiveenrol = !empty($CFG->grade_report_showonlyactiveenrol);
        $showonlyactiveenrol = get_user_preferences('grade_report_showonlyactiveenrol', $defaultgradeshowactiveenrol);
        $showonlyactiveenrol = $showonlyactiveenrol || !has_capability('moodle/course:viewsuspendedusers', $coursecontext);

        $gui = new \graded_users_iterator($course, null, $params['groupid']);
        $gui->require_active_enrolment($showonlyactiveenrol);
        $gui->init();

        $users = [];

        while ($userdata = $gui->next_user()) {
            $guiuser = $userdata->user;
            $user = new \stdClass();
            $user->fullname = fullname($guiuser);
            $user->id = $guiuser->id;
            $user->url = (new moodle_url($actionbaseurl, ['id' => $courseid, 'userid' => $guiuser->id]))->out(false);
            $userpicture = new \user_picture($guiuser);
            $userpicture->size = 1;
            $user->profileimage = $userpicture->get_url($PAGE)->out(false);
            $user->email = $guiuser->email;
            $user->active = false; // @TODO MDL-76246

            $users[] = $user;
        }
        $gui->close();

        return [
            'users' => $users,
            'warnings' => $warnings,
        ];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'users' => new external_multiple_structure(self::user_description()),
            'warnings' => new \external_warnings(),
        ]);
    }

    /**
     * Create user return value description.
     *
     * @return \external_description
     */
    public static function user_description(): \external_description {
        $userfields = [
            'id'    => new external_value(core_user::get_property_type('id'), 'ID of the user'),
            'profileimage' => new external_value(
                PARAM_URL,
                'The location of the users larger image',
                VALUE_OPTIONAL
            ),
            'url' => new external_value(
                PARAM_URL,
                'The link to the user report',
                VALUE_OPTIONAL
            ),
            'fullname' => new external_value(PARAM_TEXT, 'The full name of the user', VALUE_OPTIONAL),
            'email' => new external_value(
                core_user::get_property_type('email'),
                'An email address - allow email as root@localhost',
                VALUE_OPTIONAL),
            'active' => new external_value(PARAM_BOOL, 'Are we currently on this item?', VALUE_REQUIRED)
        ];
        return new external_single_structure($userfields);
    }
}
