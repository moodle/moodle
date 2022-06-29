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

namespace mod_assign\external;

/**
 * External function to notify Moodle that an assignment submission is starting.
 *
 * @package    mod_assign
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class start_submission extends external_api {

    /**
     * Describes the parameters for submission_start.
     *
     * @return \external_function_parameters
     * @since Moodle 4.0
     */
    public static function execute_parameters(): \external_function_parameters {
        return new \external_function_parameters ([
                'assignid' => new \external_value(PARAM_INT, 'Assignment instance id'),
            ]
        );
    }

    /**
     * Call to start an assignment submission.
     *
     * @param int $assignid Assignment ID.
     * @return array
     * @since Moodle 4.0
     */
    public static function execute(int $assignid): array {
        global $DB, $USER;

        $result = $warnings = [];
        $submission = null;

        [
            'assignid' => $assignid,
        ] = self::validate_parameters(self::execute_parameters(), [
            'assignid' => $assignid,
        ]);

        list($assignment, $course, $cm, $context) = self::validate_assign($assignid);

        $assignment->update_effective_access($USER->id);
        $latestsubmission = external_api::get_user_or_group_submission($assignment, $USER->id);
        if (!$assignment->submissions_open($USER->id)) {
            $warnings[] = self::generate_warning($assignid,
                'submissionnotopen',
                get_string('submissionnotopen', 'assign'));
        }

        if (!$assignment->is_time_limit_enabled()) {
            $warnings[] = self::generate_warning($assignid,
                'timelimitnotenabled',
                get_string('timelimitnotenabled', 'assign'));
        } else if ($assignment->is_attempt_in_progress()) {
            $warnings[] = self::generate_warning($assignid,
                'opensubmissionexists',
                get_string('opensubmissionexists', 'assign'));
        }

        if (empty($warnings)) {
            // If there is an open submission with no start time, use latest submission, otherwise create a new submission.
            if (!empty($latestsubmission)
                    && $latestsubmission->status !== ASSIGN_SUBMISSION_STATUS_SUBMITTED
                    && empty($latestsubmission->timestarted)) {
                $submission = $latestsubmission;
            } else {
                $submission = external_api::get_user_or_group_submission($assignment, $USER->id, 0, true);
            }

            // Set the start time of the submission.
            $submission->timestarted = time();
            $DB->update_record('assign_submission', $submission);
        }

        $result['submissionid'] = $submission ? $submission->id : 0;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the submission_start return value.
     *
     * @return \external_single_structure
     * @since Moodle 4.0
     */
    public static function execute_returns(): \external_single_structure {
        return new \external_single_structure([
            'submissionid' => new \external_value(PARAM_INT, 'New submission ID.'),
            'warnings' => new \external_warnings(),
        ]);
    }
}
