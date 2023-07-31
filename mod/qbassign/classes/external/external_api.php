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

namespace mod_qbassign\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/mod/qbassign/locallib.php");

/**
 * Extend the base external_api class with mod_qbassign utility methods.
 *
 * @package    mod_qbassign
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external_api extends \external_api {

    /**
     * Generate a warning in a standard structure for a known failure.
     *
     * @param int $qbassignmentid - The qbassignment
     * @param string $warningcode - The key for the warning message
     * @param string $detail - A description of the error
     * @return array - Warning structure containing item, itemid, warningcode, message
     */
    protected static function generate_warning(int $qbassignmentid, string $warningcode, string $detail): array {
        $warningmessages = [
            'couldnotlock' => 'Could not lock the submission for this user.',
            'couldnotunlock' => 'Could not unlock the submission for this user.',
            'couldnotsubmitforgrading' => 'Could not submit qbassignment for grading.',
            'couldnotrevealidentities' => 'Could not reveal identities.',
            'couldnotgrantextensions' => 'Could not grant submission date extensions.',
            'couldnotrevert' => 'Could not revert submission to draft.',
            'invalidparameters' => 'Invalid parameters.',
            'couldnotsavesubmission' => 'Could not save submission.',
            'couldnotsavegrade' => 'Could not save grade.',
            'couldnotstartsubmission' => 'Could not start submission with time limit.',
            'submissionnotopen' => 'This qbassignment is not open for submissions',
            'timelimitnotenabled' => 'Time limit is not enabled for qbassignment.',
            'opensubmissionexists' => 'Open qbassignment submission already exists.',
        ];

        $message = $warningmessages[$warningcode];
        if (empty($message)) {
            $message = 'Unknown warning type.';
        }

        return [
            'item' => s($detail),
            'itemid' => $qbassignmentid,
            'warningcode' => $warningcode,
            'message' => $message,
        ];
    }

    /**
     * Utility function for validating an qbassign.
     *
     * @param int $qbassignid qbassign instance id
     * @return array array containing the qbassign, course, context and course module objects
     * @since  Moodle 3.2
     */
    protected static function validate_qbassign(int $qbassignid): array {
        global $DB;

        // Request and permission validation.
        $qbassign = $DB->get_record('qbassign', ['id' => $qbassignid], 'id', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($qbassign, 'qbassign');

        $context = \context_module::instance($cm->id);
        // Please, note that is not required to check mod/qbassign:view because is done by validate_context->require_login.
        self::validate_context($context);
        $qbassign = new \qbassign($context, $cm, $course);

        return [$qbassign, $course, $cm, $context];
    }

    /**
     * Get a submission from an qbassignment for a user. Encapsulates checking whether it's a solo or team submission.
     *
     * @param \qbassign $qbassignment qbassignment object.
     * @param int|null $userid User id.
     * @param int $groupid Group id.
     * @param bool $create Whether a new submission should be created.
     * @param int $attemptnumber Attempt number. Use -1 for last attempt.
     * @return bool|\stdClass
     */
    protected static function get_user_or_group_submission(\qbassign $qbassignment, int $userid = null,
            int $groupid = 0, bool $create = false, int $attemptnumber = -1) {
        if ($qbassignment->get_instance($userid)->teamsubmission) {
            $submission = $qbassignment->get_group_submission($userid, $groupid, $create, $attemptnumber);
        } else {
            $submission = $qbassignment->get_user_submission($userid, $create, $attemptnumber);
        }
        return $submission;
    }
}
