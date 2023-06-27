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
 * This is the external method for getting the information needed to present a results report.
 *
 * @package    mod_h5pactivity
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

use mod_h5pactivity\local\manager;
use mod_h5pactivity\local\report\results as report_results;
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
 * This is the external method for getting the information needed to present a results report.
 *
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_results extends external_api {

    /**
     * Webservice parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'h5pactivityid' => new external_value(PARAM_INT, 'h5p activity instance id'),
                'attemptids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'The attempt id'),
                    'Attempt ids', VALUE_DEFAULT, []
                ),
            ]
        );
    }

    /**
     * Return user attempts results information in a h5p activity.
     *
     * In case an empty array of attempt ids is passed, the method will load all
     * activity attempts from the current user.
     *
     * @throws  moodle_exception if the user cannot see the report
     * @param  int $h5pactivityid The h5p activity id
     * @param  int[] $attemptids The attempt ids
     * @return stdClass report data
     */
    public static function execute(int $h5pactivityid, array $attemptids = []): stdClass {
        global $USER;

        $params = external_api::validate_parameters(self::execute_parameters(), [
            'h5pactivityid' => $h5pactivityid,
            'attemptids' => $attemptids,
        ]);
        $h5pactivityid = $params['h5pactivityid'];
        $attemptids = $params['attemptids'];

        $warnings = [];

        // Request and permission validation.
        list ($course, $cm) = get_course_and_cm_from_instance($h5pactivityid, 'h5pactivity');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        $manager = manager::create_from_coursemodule($cm);

        if (empty($attemptids)) {
            $attemptids = [];
            foreach ($manager->get_user_attempts($USER->id) as $attempt) {
                $attemptids[] = $attempt->get_id();
            }
        }

        $attempts = [];
        foreach ($attemptids as $attemptid) {
            $report = $manager->get_report(null, $attemptid);

            if ($report && $report instanceof report_results) {
                $attempts[] = self::export_attempt($report);
            } else {
                $warnings[] = [
                    'item' => 'h5pactivity_attempts',
                    'itemid' => $attemptid,
                    'warningcode' => '1',
                    'message' => "Cannot access attempt",
                ];
            }
        }

        $result = (object)[
            'activityid' => $h5pactivityid,
            'attempts' => $attempts,
            'warnings' => $warnings,
        ];

        return $result;
    }

    /**
     * Return a data object from an attempt.
     *
     * @param report_results $report the attempt data
     * @return stdClass a WS compatible version of the attempt
     */
    private static function export_attempt(report_results $report): stdClass {

        $data = $report->export_data_for_external();

        $attemptdata = $data->attempt;

        $attempt = (object)[
            'id' => $attemptdata->id,
            'h5pactivityid' => $attemptdata->h5pactivityid,
            'userid' => $attemptdata->userid,
            'timecreated' => $attemptdata->timecreated,
            'timemodified' => $attemptdata->timemodified,
            'attempt' => $attemptdata->attempt,
            'rawscore' => $attemptdata->rawscore,
            'maxscore' => $attemptdata->maxscore,
            'duration' => (empty($attemptdata->durationvalue)) ? 0 : $attemptdata->durationvalue,
            'scaled' => (empty($attemptdata->scaled)) ? 0 : $attemptdata->scaled,
            'results' => [],
        ];
        if (isset($attemptdata->completion) && $attemptdata->completion !== null) {
            $attempt->completion = $attemptdata->completion;
        }
        if (isset($attemptdata->success) && $attemptdata->success !== null) {
            $attempt->success = $attemptdata->success;
        }
        foreach ($data->results as $result) {
            $attempt->results[] = self::export_result($result);
        }
        return $attempt;
    }

    /**
     * Return a data object from a result.
     *
     * @param stdClass $data the result data
     * @return stdClass a WS compatible version of the result
     */
    private static function export_result(stdClass $data): stdClass {
        $result = (object)[
            'id' => $data->id,
            'attemptid' => $data->attemptid,
            'subcontent' => $data->subcontent,
            'timecreated' => $data->timecreated,
            'interactiontype' => $data->interactiontype,
            'description' => $data->description,
            'rawscore' => $data->rawscore,
            'maxscore' => $data->maxscore,
            'duration' => $data->duration,
            'optionslabel' => $data->optionslabel ?? get_string('choice', 'mod_h5pactivity'),
            'correctlabel' => $data->correctlabel ?? get_string('correct_answer', 'mod_h5pactivity'),
            'answerlabel' => $data->answerlabel ?? get_string('attempt_answer', 'mod_h5pactivity'),
            'track' => $data->track ?? false,
        ];
        if (isset($data->completion) && $data->completion !== null) {
            $result->completion = $data->completion;
        }
        if (isset($data->success) && $data->success !== null) {
            $result->success = $data->success;
        }
        if (isset($data->options)) {
            $result->options = $data->options;
        }
        if (isset($data->content)) {
            $result->content = $data->content;
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
            'attempts' => new external_multiple_structure(
                self::get_attempt_returns(), 'The complete attempts list'
            ),
            'warnings' => new external_warnings(),
        ], 'Activity attempts results data');
    }

    /**
     * Return the external structure of an attempt
     * @return external_single_structure
     */
    private static function get_attempt_returns(): external_single_structure {

        $result = new external_single_structure([
            'id'    => new external_value(PARAM_INT, 'ID of the context'),
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
            'results' => new external_multiple_structure(
                self::get_result_returns(),
                'The results of the attempt', VALUE_OPTIONAL
            ),
        ], 'The attempt general information');
        return $result;
    }

    /**
     * Return the external structure of a result
     * @return external_single_structure
     */
    private static function get_result_returns(): external_single_structure {

        $result = new external_single_structure([
            'id'    => new external_value(PARAM_INT, 'ID of the context'),
            'attemptid' => new external_value(PARAM_INT, 'ID of the H5P attempt'),
            'subcontent' => new external_value(PARAM_NOTAGS, 'Subcontent identifier'),
            'timecreated' => new external_value(PARAM_INT, 'Result creation'),
            'interactiontype' => new external_value(PARAM_NOTAGS, 'Interaction type'),
            'description' => new external_value(PARAM_RAW, 'Result description'),
            'content' => new external_value(PARAM_RAW, 'Result extra content', VALUE_OPTIONAL),
            'rawscore' => new external_value(PARAM_INT, 'Result score value'),
            'maxscore' => new external_value(PARAM_INT, 'Result max score'),
            'duration' => new external_value(PARAM_INT, 'Result duration in seconds', VALUE_OPTIONAL, 0),
            'completion' => new external_value(PARAM_INT, 'Result completion', VALUE_OPTIONAL),
            'success' => new external_value(PARAM_INT, 'Result success', VALUE_OPTIONAL),
            'optionslabel' => new external_value(PARAM_NOTAGS, 'Label used for result options', VALUE_OPTIONAL),
            'correctlabel' => new external_value(PARAM_NOTAGS, 'Label used for correct answers', VALUE_OPTIONAL),
            'answerlabel' => new external_value(PARAM_NOTAGS, 'Label used for user answers', VALUE_OPTIONAL),
            'track' => new external_value(PARAM_BOOL, 'If the result has valid track information', VALUE_OPTIONAL),
            'options' => new external_multiple_structure(
                new external_single_structure([
                    'description'    => new external_value(PARAM_RAW, 'Option description', VALUE_OPTIONAL),
                    'id' => new external_value(PARAM_TEXT, 'Option string identifier', VALUE_OPTIONAL),
                    'correctanswer' => self::get_answer_returns('The option correct answer', VALUE_OPTIONAL),
                    'useranswer' => self::get_answer_returns('The option user answer', VALUE_OPTIONAL),
                ]),
                'The statement options', VALUE_OPTIONAL
            ),
        ], 'A single result statement tracking information');
        return $result;
    }

    /**
     * Return the external structure of an answer or correctanswer
     *
     * @param string $description the return description
     * @param int $required the return required value
     * @return external_single_structure
     */
    private static function get_answer_returns(string $description, int $required = VALUE_REQUIRED): external_single_structure {

        $result = new external_single_structure([
            'answer' => new external_value(PARAM_NOTAGS, 'Option text value', VALUE_OPTIONAL),
            'correct' => new external_value(PARAM_BOOL, 'If has to be displayed as correct', VALUE_OPTIONAL),
            'incorrect' => new external_value(PARAM_BOOL, 'If has to be displayed as incorrect', VALUE_OPTIONAL),
            'text' => new external_value(PARAM_BOOL, 'If has to be displayed as simple text', VALUE_OPTIONAL),
            'checked' => new external_value(PARAM_BOOL, 'If has to be displayed as a checked option', VALUE_OPTIONAL),
            'unchecked' => new external_value(PARAM_BOOL, 'If has to be displayed as a unchecked option', VALUE_OPTIONAL),
            'pass' => new external_value(PARAM_BOOL, 'If has to be displayed as passed', VALUE_OPTIONAL),
            'fail' => new external_value(PARAM_BOOL, 'If has to be displayed as failed', VALUE_OPTIONAL),
        ], $description, $required);
        return $result;
    }
}
