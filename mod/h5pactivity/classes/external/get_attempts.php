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
 * This is the external method for getting the information needed to present an attempts report.
 *
 * @package    mod_h5pactivity
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

use mod_h5pactivity\local\manager;
use mod_h5pactivity\local\attempt;
use mod_h5pactivity\local\report\attempts as report_attempts;
use external_api;
use external_function_parameters;
use external_value;
use external_multiple_structure;
use external_single_structure;
use external_warnings;
use moodle_exception;
use context_module;
use stdClass;

/**
 * This is the external method for getting the information needed to present an attempts report.
 *
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_attempts extends external_api {

    /**
     * Webservice parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'h5pactivityid' => new external_value(PARAM_INT, 'h5p activity instance id'),
                'userids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'The user ids to get attempts (null means only current user)', VALUE_DEFAULT),
                    'User ids', VALUE_DEFAULT, []
                ),
            ]
        );
    }

    /**
     * Return user attempts information in a h5p activity.
     *
     * @throws  moodle_exception if the user cannot see the report
     * @param  int $h5pactivityid The h5p activity id
     * @param  int[]|null $userids The user ids (if no provided $USER will be used)
     * @return stdClass report data
     */
    public static function execute(int $h5pactivityid, ?array $userids = []): stdClass {
        global $USER;

        $params = external_api::validate_parameters(self::execute_parameters(), [
            'h5pactivityid' => $h5pactivityid,
            'userids' => $userids,
        ]);
        $h5pactivityid = $params['h5pactivityid'];
        $userids = $params['userids'];

        if (empty($userids)) {
            $userids = [$USER->id];
        }

        $warnings = [];

        // Request and permission validation.
        list ($course, $cm) = get_course_and_cm_from_instance($h5pactivityid, 'h5pactivity');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        $manager = manager::create_from_coursemodule($cm);

        $instance = $manager->get_instance();

        $usersattempts = [];
        foreach ($userids as $userid) {
            $report = $manager->get_report($userid);
            if ($report && $report instanceof report_attempts) {
                $usersattempts[] = self::export_user_attempts($report, $userid);
            } else {
                $warnings[] = [
                    'item' => 'user',
                    'itemid' => $userid,
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
     * Export attempts data for a specific user.
     *
     * @param report_attempts $report the report attempts object
     * @param int $userid the user id
     * @return stdClass
     */
    public static function export_user_attempts(report_attempts $report, int $userid): stdClass {

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
    public static function get_user_attempts_returns(): external_single_structure {
        $structure = [
            'userid' => new external_value(PARAM_INT, 'The user id'),
            'attempts' => new external_multiple_structure(self::get_attempt_returns(), 'The complete attempts list'),
            'scored' => new external_single_structure([
                'title' => new external_value(PARAM_NOTAGS, 'Scored attempts title'),
                'grademethod' => new external_value(PARAM_NOTAGS, 'Scored attempts title'),
                'attempts' => new external_multiple_structure(self::get_attempt_returns(), 'List of the grading attempts'),
            ], 'Attempts used to grade the activity', VALUE_OPTIONAL),
        ];
        return new external_single_structure($structure);
    }

    /**
     * Return the external structure of an attempt.
     *
     * @return external_single_structure
     */
    private static function get_attempt_returns(): external_single_structure {

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
