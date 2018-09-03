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
 * A scheduled task.
 *
 * @package    assignfeedback_editpdf
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace assignfeedback_editpdf\task;

use core\task\scheduled_task;
use assignfeedback_editpdf\document_services;
use assignfeedback_editpdf\combined_document;
use context_module;
use assign;

/**
 * Simple task to convert submissions to pdf in the background.
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class convert_submissions extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('preparesubmissionsforannotation', 'assignfeedback_editpdf');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/mod/assign/locallib.php');

        $records = $DB->get_records('assignfeedback_editpdf_queue');

        $assignmentcache = array();

        foreach ($records as $record) {
            $submissionid = $record->submissionid;
            $submission = $DB->get_record('assign_submission', array('id' => $submissionid), '*', IGNORE_MISSING);
            if (!$submission) {
                // Submission no longer exists.
                $DB->delete_records('assignfeedback_editpdf_queue', array('id' => $record->id));
                continue;
            }

            $assignmentid = $submission->assignment;
            $attemptnumber = $record->submissionattempt;

            if (empty($assignmentcache[$assignmentid])) {
                $cm = get_coursemodule_from_instance('assign', $assignmentid, 0, false, MUST_EXIST);
                $context = context_module::instance($cm->id);

                $assignment = new assign($context, null, null);
                $assignmentcache[$assignmentid] = $assignment;
            } else {
                $assignment = $assignmentcache[$assignmentid];
            }

            $users = array();
            if ($submission->userid) {
                array_push($users, $submission->userid);
            } else {
                $members = $assignment->get_submission_group_members($submission->groupid, true);

                foreach ($members as $member) {
                    array_push($users, $member->id);
                }
            }

            mtrace('Convert ' . count($users) . ' submission attempt(s) for assignment ' . $assignmentid);

            foreach ($users as $userid) {
                try {
                    $combineddocument = document_services::get_combined_pdf_for_attempt($assignment, $userid, $attemptnumber);
                    $status = $combineddocument->get_status();

                    switch ($combineddocument->get_status()) {
                        case combined_document::STATUS_READY:
                        case combined_document::STATUS_PENDING_INPUT:
                            // The document has not been converted yet or is somehow still ready.
                            continue;
                    }
                    document_services::get_page_images_for_attempt(
                            $assignment,
                            $userid,
                            $attemptnumber,
                            false
                        );
                    document_services::get_page_images_for_attempt(
                            $assignment,
                            $userid,
                            $attemptnumber,
                            true
                        );
                } catch (\moodle_exception $e) {
                    mtrace('Conversion failed with error:' . $e->errorcode);
                }
            }

            // Remove from queue.
            $DB->delete_records('assignfeedback_editpdf_queue', array('id' => $record->id));

        }
    }

}
