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

namespace mod_h5pactivity\external;

use mod_h5pactivity\local\manager;
use mod_h5pactivity\local\attempt;
use mod_h5pactivity\local\report;
use mod_h5pactivity\local\report\attempts as report_attempts;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use moodle_exception;
use context_module;
use stdClass;

/**
 * This is the external method to return the information needed to list all enrolled user attempts.
 *
 * @package    mod_h5pactivity
 * @since      Moodle 3.11
 * @copyright  2020 Ilya Tregubov <ilya@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_user_attempts extends external_api {

    /**
     * Webservice parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'h5pactivityid' => new external_value(PARAM_INT, 'h5p activity instance id'),
                'sortorder' => new external_value(PARAM_TEXT,
                    'sort by either user id, firstname or lastname (with optional asc/desc)', VALUE_DEFAULT, 'id ASC'),
                'page' => new external_value(PARAM_INT, 'current page', VALUE_DEFAULT, -1),
                'perpage' => new external_value(PARAM_INT, 'items per page', VALUE_DEFAULT, 0),
                'firstinitial' => new external_value(PARAM_TEXT, 'Users whose first name ' .
                    'starts with $firstinitial', VALUE_DEFAULT, ''),
                'lastinitial' => new external_value(PARAM_TEXT, 'Users whose last name ' .
                    'starts with $lastinitial', VALUE_DEFAULT, ''),
            ]
        );
    }

    /**
     * Return user attempts information in a h5p activity.
     *
     * @throws  moodle_exception if the user cannot see the report
     * @param  int $h5pactivityid The h5p activity id
     * @param int $sortorder The sort order
     * @param int $page page number
     * @param int $perpage items per page
     * @param int $firstinitial Users whose first name starts with $firstinitial
     * @param int $lastinitial Users whose last name starts with $lastinitial
     * @return stdClass report data
     */
    public static function execute(int $h5pactivityid, $sortorder = 'id ASC', ?int $page = 0,
            ?int $perpage = 0, $firstinitial = '', $lastinitial = ''): stdClass {
        [
            'h5pactivityid' => $h5pactivityid,
            'sortorder' => $sortorder,
            'page' => $page,
            'perpage' => $perpage,
            'firstinitial' => $firstinitial,
            'lastinitial' => $lastinitial,
        ] = external_api::validate_parameters(self::execute_parameters(), [
            'h5pactivityid' => $h5pactivityid,
            'sortorder' => $sortorder,
            'page' => $page,
            'perpage' => $perpage,
            'firstinitial' => $firstinitial,
            'lastinitial' => $lastinitial,
        ]);

        $warnings = [];

        [$course, $cm] = get_course_and_cm_from_instance($h5pactivityid, 'h5pactivity');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        $manager = manager::create_from_coursemodule($cm);
        $instance = $manager->get_instance();
        if (!$manager->can_view_all_attempts()) {
            throw new moodle_exception('nopermissiontoviewattempts', 'error', '', null,
                'h5pactivity:reviewattempts required view attempts of all enrolled users.');
        }

        // Ensure sortorder parameter is safe to use. Fallback to default value of the parameter itself.
        $sortorderparts = explode(' ', $sortorder, 2);
        $sortorder = get_safe_orderby([
            'id' => 'u.id',
            'firstname' => 'u.firstname',
            'lastname' => 'u.lastname',
            'default' => 'u.id',
        ], $sortorderparts[0], $sortorderparts[1] ?? '');

        $users = self::get_active_users($manager, 'u.id, u.firstname, u.lastname',
            $sortorder, $page * $perpage, $perpage);

        $usersattempts = [];

        foreach ($users as $user) {

            if ($firstinitial) {
                if (strpos($user->firstname, $firstinitial) === false) {
                    continue;
                }
            }

            if ($lastinitial) {
                if (strpos($user->lastname, $lastinitial) === false) {
                    continue;
                }
            }

            $report = $manager->get_report($user->id);
            if ($report && $report instanceof report_attempts) {
                $usersattempts[] = self::export_user_attempts($report, $user->id);
            } else {
                $warnings[] = [
                    'item' => 'user',
                    'itemid' => $user->id,
                    'warningcode' => '1',
                    'message' => "Cannot access user attempts",
                ];
            }
        }

        $result = (object)[
            'activityid' => $instance->id,
            'usersattempts' => $usersattempts,
            'warnings' => $warnings,
        ];

        return $result;
    }

    /**
     * Generate the active users list
     *
     * @param manager $manager the h5pactivity manager
     * @param string $userfields the user fields to get
     * @param string $sortorder the SQL sortorder
     * @param int $limitfrom SQL limit from
     * @param int $limitnum SQL limit num
     */
    private static function get_active_users(
        manager $manager,
        string $userfields = 'u.*',
        string $sortorder = '',
        int $limitfrom = 0,
        int $limitnum = 0
    ): array {

        global $DB;

        $capjoin = $manager->get_active_users_join(true);

        // Final SQL.
        $sql = "SELECT DISTINCT {$userfields}
                  FROM {user} u {$capjoin->joins}
                 WHERE {$capjoin->wheres}
                       {$sortorder}";

        return $DB->get_records_sql($sql, $capjoin->params, $limitfrom, $limitnum);
    }

    /**
     * Export attempts data for a specific user.
     *
     * @param report $report the report attempts object
     * @param int $userid the user id
     * @return stdClass
     */
    private static function export_user_attempts(report $report, int $userid): stdClass {
        $scored = $report->get_scored();
        $attempts = $report->get_attempts();

        $result = (object)[
            'userid' => $userid,
            'attempts' => [],
        ];

        foreach ($attempts as $attempt) {
            $result->attempts[] = self::export_attempt($attempt);
        }

        if (!empty($scored)) {
            $result->scored = (object)[
                'title' => $scored->title,
                'grademethod' => $scored->grademethod,
                'attempts' => [self::export_attempt($scored->attempt)],
            ];
        }

        return $result;
    }

    /**
     * Return a data object from an attempt.
     *
     * @param attempt $attempt the attempt object
     * @return stdClass a WS compatible version of the attempt
     */
    private static function export_attempt(attempt $attempt): stdClass {
        $result = (object)[
            'id' => $attempt->get_id(),
            'h5pactivityid' => $attempt->get_h5pactivityid(),
            'userid' => $attempt->get_userid(),
            'timecreated' => $attempt->get_timecreated(),
            'timemodified' => $attempt->get_timemodified(),
            'attempt' => $attempt->get_attempt(),
            'rawscore' => $attempt->get_rawscore(),
            'maxscore' => $attempt->get_maxscore(),
            'duration' => $attempt->get_duration(),
            'scaled' => $attempt->get_scaled(),
        ];
        if ($attempt->get_completion() !== null) {
            $result->completion = $attempt->get_completion();
        }
        if ($attempt->get_success() !== null) {
            $result->success = $attempt->get_success();
        }
        return $result;
    }

    /**
     * Describes the get_h5pactivity_access_information return value.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'activityid' => new external_value(PARAM_INT, 'Activity course module ID'),
            'usersattempts' => new external_multiple_structure(
                self::get_user_attempts_returns(), 'The complete users attempts list'
            ),
            'warnings' => new external_warnings(),
        ], 'Activity attempts data');
    }

    /**
     * Describes the get_h5pactivity_access_information return value.
     *
     * @return external_single_structure
     */
    private static function get_user_attempts_returns(): external_single_structure {
        $structure = [
            'userid' => new external_value(PARAM_INT, 'The user id'),
            'attempts' => new external_multiple_structure(self::get_user_attempt_returns(), 'The complete attempts list'),
            'scored' => new external_single_structure([
                'title' => new external_value(PARAM_NOTAGS, 'Scored attempts title'),
                'grademethod' => new external_value(PARAM_NOTAGS, 'Grading method'),
                'attempts' => new external_multiple_structure(self::get_user_attempt_returns(), 'List of the grading attempts'),
            ], 'Attempts used to grade the activity', VALUE_OPTIONAL),
        ];
        return new external_single_structure($structure);
    }

    /**
     * Return the external structure of an attempt.
     *
     * @return external_single_structure
     */
    private static function get_user_attempt_returns(): external_single_structure {
        $result = new external_single_structure([
            'id' => new external_value(PARAM_INT, 'ID of the context'),
            'h5pactivityid' => new external_value(PARAM_INT, 'ID of the H5P activity'),
            'userid' => new external_value(PARAM_INT, 'ID of the user'),
            'timecreated' => new external_value(PARAM_INT, 'Attempt creation'),
            'timemodified' => new external_value(PARAM_INT, 'Attempt modified'),
            'attempt' => new external_value(PARAM_INT, 'Attempt number'),
            'rawscore' => new external_value(PARAM_INT, 'Attempt score value'),
            'maxscore' => new external_value(PARAM_INT, 'Attempt max score'),
            'duration' => new external_value(PARAM_INT, 'Attempt duration in seconds'),
            'completion' => new external_value(PARAM_INT, 'Attempt completion', VALUE_OPTIONAL),
            'success' => new external_value(PARAM_INT, 'Attempt success', VALUE_OPTIONAL),
            'scaled' => new external_value(PARAM_FLOAT, 'Attempt scaled'),
        ]);
        return $result;
    }
}
