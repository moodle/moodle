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

use coding_exception;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use core_external\restricted_context_exception;
use core_user_external;
use invalid_parameter_exception;
use moodle_exception;
use user_picture;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/grade/lib.php');
require_once($CFG->dirroot . '/user/externallib.php');

/**
 * Get the gradable users in a course.
 *
 * @package    core_grades
 * @copyright  2023 Ilya Tregubov <ilya.a.tregubov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
     * @param int $courseid Course ID
     * @param int|null $groupid Group ID
     * @param bool $onlyactive Whether we should only return active enrolments.
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
        $onlyactive = $onlyactive || !has_capability('moodle/course:viewsuspendedusers', $coursecontext);

        $users = get_gradable_users($course->id, $params['groupid'], $onlyactive);
        $users = array_map(function ($user) use ($PAGE) {
            $user->fullname = fullname($user);
            $userpicture = new user_picture($user);
            $userpicture->size = 1;
            $user->profileimageurlsmall = $userpicture->get_url($PAGE)->out(false);
            $user->profileimageurl = $userpicture->get_url($PAGE)->out(false);
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
            'users' => new external_multiple_structure(core_user_external::user_description()),
            'warnings' => new external_warnings(),
        ]);
    }
}
