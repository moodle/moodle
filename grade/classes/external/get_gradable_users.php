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

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/grade/lib.php');
require_once($CFG->dirroot . '/user/externallib.php');

use coding_exception;
use external_api;
use core_user;
use external_description;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use external_warnings;
use invalid_parameter_exception;
use moodle_exception;
use restricted_context_exception;
use user_picture;

/**
 * Get the gradable users in a course.
 *
 * @package    core_grades
 * @copyright  2023 Ilya Tregubov <ilya.a.tregubov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.1
 */
class get_gradable_users extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters (
            [
                'courseid' => new external_value(PARAM_INT, 'Course Id', VALUE_REQUIRED),
                'groupid' => new external_value(PARAM_INT, 'Group Id', VALUE_DEFAULT, 0),
                'onlyactive' => new external_value(PARAM_BOOL, 'Only active enrolment', VALUE_DEFAULT, false),
            ]
        );
    }

    /**
     * Given a course ID find the gradable users within a group.
     *
     * @param int $courseid
     * @param int|null $groupid
     * @param bool $onlyactive
     * @return array Users and warnings.
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @throws restricted_context_exception
     */
    public static function execute(int $courseid, ?int $groupid = 0, bool $onlyactive = false): array {
        global $DB, $PAGE;

        $params = self::validate_parameters(
            self::execute_parameters(),
            [
                'courseid' => $courseid,
                'groupid' => $groupid,
                'onlyactive' => $onlyactive,
            ]
        );

        $warnings = [];
        $coursecontext = \context_course::instance($params['courseid']);
        parent::validate_context($coursecontext);

        require_capability('moodle/course:viewparticipants', $coursecontext);

        $course = $DB->get_record('course', ['id' => $params['courseid']]);
        // Create a graded_users_iterator because it will properly check the groups etc.
        $onlyactive = $onlyactive || !has_capability('moodle/course:viewsuspendedusers', $coursecontext);

        $gui = new \graded_users_iterator($course, null, $params['groupid']);
        $gui->require_active_enrolment($onlyactive);
        $gui->init();

        // Flatten the users.
        $users = [];
        while ($user = $gui->next_user()) {
            $users[$user->user->id] = $user->user;
        }
        $gui->close();

        $users = array_map(function ($user) use ($PAGE) {
            $user->fullname = fullname($user);
            $userpicture = new user_picture($user);
            $userpicture->size = 1;
            $user->profileimage = $userpicture->get_url($PAGE)->out(false);
            return $user;
        }, $users);
        sort($users);

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
            'warnings' => new external_warnings(),
        ]);
    }

    /**
     * Create user return value description.
     *
     * @return external_description
     */
    public static function user_description(): external_description {
        $userfields = [
            'id'    => new external_value(core_user::get_property_type('id'), 'ID of the user'),
            'profileimage' => new external_value(
                PARAM_URL,
                'The location of the users larger image',
                VALUE_OPTIONAL
            ),
            'fullname' => new external_value(PARAM_TEXT, 'The full name of the user', VALUE_OPTIONAL),
            'firstname'   => new external_value(
                core_user::get_property_type('firstname'),
                'The first name(s) of the user',
                VALUE_OPTIONAL),
            'lastname'    => new external_value(
                core_user::get_property_type('lastname'),
                'The family name of the user',
                VALUE_OPTIONAL),
        ];
        return new external_single_structure($userfields);
    }

}
