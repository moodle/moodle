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

namespace gradereport_grader\external;

use context_course;
use core_user_external;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use grade_report_grader;
use user_picture;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/course/externallib.php');
require_once($CFG->dirroot .'/user/externallib.php');
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot.'/grade/report/grader/lib.php');

/**
 * External grade report grader API
 *
 * @package    gradereport_grader
 * @copyright  2022 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_users_in_report extends external_api {
    /**
     * Describes the parameters for get_users_in_report
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters (
            [
                'courseid' => new external_value(PARAM_INT, 'Course ID', VALUE_REQUIRED)
            ]
        );
    }

    /**
     * Given a course ID find Fetch the grader report and add some fields to the returned users.
     *
     * @param int $courseid Course ID to fetch the grader report for.
     * @return array Users and warnings to pass back to the calling widget.
     */
    public static function execute(int $courseid): array {
        global $PAGE;

        self::validate_parameters(
            self::execute_parameters(),
            [
                'courseid' => $courseid,
            ]
        );

        $warnings = [];
        $context = context_course::instance($courseid);
        self::validate_context($context);

        require_capability('gradereport/grader:view', $context);

        // Return tracking object.
        $gpr = new \grade_plugin_return(
            [
                'type' => 'report',
                'plugin' => 'grader',
                'courseid' => $courseid
            ]
        );
        $report = new grade_report_grader($courseid, $gpr, $context);

        $userfieldsapi = \core_user\fields::for_identity($context, false)->with_userpic();
        $extrauserfields = $userfieldsapi->get_required_fields([\core_user\fields::PURPOSE_IDENTITY]);

        // For the returned users, Add a couple of extra fields that we need for the search module.
        $users = array_map(function ($user) use ($PAGE, $extrauserfields) {
            $userforselector = new \stdClass();
            $userforselector->id = $user->id;
            $userforselector->fullname = fullname($user);
            foreach (\core_user\fields::get_name_fields() as $field) {
                $userforselector->$field = $user->$field ?? null;
            }
            $userpicture = new user_picture($user);
            $userpicture->size = 1;
            $userforselector->profileimageurl = $userpicture->get_url($PAGE)->out(false);
            $userpicture->size = 0; // Size f2.
            $userforselector->profileimageurlsmall = $userpicture->get_url($PAGE)->out(false);
            foreach ($extrauserfields as $field) {
                $userforselector->$field = $user->$field ?? null;
            }
            return $userforselector;
        }, $report->load_users(true));
        sort($users);

        return [
            'users' => $users,
            'warnings' => $warnings,
        ];
    }

    /**
     * Returns description of what the users & warnings should return.
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
