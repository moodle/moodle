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
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;

/**
 * External function to remove an assignment submission.
 *
 * @package     mod_assign
 * @category    external
 *
 * @copyright   2024 Daniel Ureña <durenadev@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since       Moodle 4.5
 */
class remove_submission extends external_api {
    /**
     * Describes the parameters for remove submission.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
                'userid' => new external_value(PARAM_INT, 'User id'),
                'assignid' => new external_value(PARAM_INT, 'Assignment instance id'),
            ]);
    }

    /**
     * Call to remove submission.
     *
     * @param int $userid User id to remove submission
     * @param int $assignid The id of the assignment
     * @return array
     */
    public static function execute(int $userid, int $assignid): array {
        // Initialize return variables.
        $warnings = [];
        $result   = [];
        $status   = false;

        [
            'userid' => $userid,
            'assignid'  => $assignid
        ] = self::validate_parameters(self::execute_parameters(), [
            'userid' => $userid,
            'assignid'  => $assignid,
        ]);

        // Validate and get the assign.
        [$assign, $course, $cm, $context] = self::validate_assign($assignid);

        // Get submission.
        $submission = $assign->get_user_submission($userid, false);
        if (
            !$submission ||
            $submission->status == ASSIGN_SUBMISSION_STATUS_NEW ||
            $submission->status == ASSIGN_SUBMISSION_STATUS_REOPENED
        ) {
            // No submission to remove.
            $warnings[] = self::generate_warning($assignid, 'submissionnotfoundtoremove', 'assign');
            return [
                'status'    => $status,
                'warnings' => $warnings,
            ];
        }

        if (!$status = $assign->remove_submission($userid)) {
            $errors = $assign->get_error_messages();
            foreach ($errors as $errormsg) {
                $warnings[] = self::generate_warning($assignid, 'couldnotremovesubmission', $errormsg);
            }
        }
        $result['status']   = $status;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the remove submissions return value.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'status' => new external_value(PARAM_BOOL, 'True if the submission was successfully removed and false if was not.'),
            'warnings' => new external_warnings(),
        ]);
    }
}
