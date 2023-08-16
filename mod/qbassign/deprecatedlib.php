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
 * List of deprecated mod_qbassign functions.
 *
 * @package   mod_qbassign
 * @copyright 2021 Jun Pataleta
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Obtains the automatic completion state for this module based on any conditions
 * in qbassign settings.
 *
 * @deprecated since Moodle 3.11
 * @todo MDL-71196 Final deprecation in Moodle 4.3
 * @see \mod_qbassign\completion\custom_completion
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not, $type if conditions not set.
 */
function qbassign_get_completion_state($course, $cm, $userid, $type) {
    global $CFG;

    // No need to call debugging here. Deprecation debugging notice already being called in \completion_info::internal_get_state().

    require_once($CFG->dirroot . '/mod/qbassign/locallib.php');

    $qbassign = new qbassign(null, $cm, $course);

    // If completion option is enabled, evaluate it and return true/false.
    if ($qbassign->get_instance()->completionsubmit) {
        if ($qbassign->get_instance()->teamsubmission) {
            $submission = $qbassign->get_group_submission($userid, 0, false);
        } else {
            $submission = $qbassign->get_user_submission($userid, false);
        }
        return $submission && $submission->status == qbassign_SUBMISSION_STATUS_SUBMITTED;
    } else {
        // Completion option is not enabled so just return $type.
        return $type;
    }
}
