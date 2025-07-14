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

use core_user_external;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use core_external\restricted_context_exception;
use user_picture;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot .'/user/externallib.php');

/**
 * Get the enrolled users within and map some fields to the returned array of user objects.
 *
 * @package    core_grades
 * @copyright  2022 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.2
 */
class get_enrolled_users_for_selector extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters (
            [
                'courseid' => new external_value(PARAM_INT, 'Course Id', VALUE_REQUIRED),
                'groupid' => new external_value(PARAM_INT, 'Group Id', VALUE_DEFAULT, 0)
            ]
        );
    }

    /**
     * Given a course ID find the enrolled users within and map some fields to the returned array of user objects.
     *
     * @param int $courseid
     * @param int|null $groupid
     * @return array Users and warnings to pass back to the calling widget.
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws restricted_context_exception
     */
    public static function execute(int $courseid, ?int $groupid = 0): array {
        global $DB, $PAGE;

        $params = self::validate_parameters(
            self::execute_parameters(),
            [
                'courseid' => $courseid,
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

        $userfieldsapi = \core_user\fields::for_identity($coursecontext, false)->with_userpic();
        $extrauserfields = $userfieldsapi->get_required_fields([\core_user\fields::PURPOSE_IDENTITY]);

        while ($userdata = $gui->next_user()) {
            $userforselector = new \stdClass();
            $userforselector->id = $userdata->user->id;
            $userforselector->fullname = fullname($userdata->user);
            foreach (\core_user\fields::get_name_fields() as $field) {
                $userforselector->$field = $userdata->user->$field ?? null;
            }
            $userpicture = new user_picture($userdata->user);
            $userpicture->size = 1;
            $userforselector->profileimageurl = $userpicture->get_url($PAGE)->out(false);
            $userpicture->size = 0; // Size f2.
            $userforselector->profileimageurlsmall = $userpicture->get_url($PAGE)->out(false);
            foreach ($extrauserfields as $field) {
                $userforselector->$field = $userdata->user->$field ?? null;
            }

            $users[] = $userforselector;
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
            'users' => new external_multiple_structure(core_user_external::user_description()),
            'warnings' => new external_warnings(),
        ]);
    }
}
