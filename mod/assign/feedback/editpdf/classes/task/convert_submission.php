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

namespace assignfeedback_editpdf\task;

use core\task\adhoc_task;
use core\task\manager;
use assignfeedback_editpdf\document_services;
use assignfeedback_editpdf\combined_document;
use assignfeedback_editpdf\pdf;
use context_module;
use moodle_exception;
use assign;

/**
 * An adhoc task to convert submissions to pdf in the background.
 *
 * @copyright  2022 Mikhail Golenkov <mikhailgolenkov@catalyst-au.net>
 * @package    assignfeedback_editpdf
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class convert_submission extends adhoc_task {

    /**
     * Run the task.
     */
    public function execute() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');

        $data = $this->get_custom_data();

        $submission = $DB->get_record('assign_submission', ['id' => $data->submissionid], '*', IGNORE_MISSING);
        if (!$submission) {
            mtrace('Submission no longer exists');
            return;
        }

        // Early exit if ghostscript isn't correctly configured.
        $result = pdf::test_gs_path(false);
        if ($result->status !== pdf::GSPATH_OK) {
            $statusstring = get_string('test_' . $result->status, 'assignfeedback_editpdf');
            throw new moodle_exception('pathtogserror', 'assignfeedback_editpdf', '', $statusstring, $result->status);
        }

        $cm = get_coursemodule_from_instance('assign', $submission->assignment, 0, false, MUST_EXIST);
        $context = context_module::instance($cm->id);
        $assign = new assign($context, null, null);

        if ($submission->userid) {
            $users = [$submission->userid];
        } else {
            $users = [];
            $members = $assign->get_submission_group_members($submission->groupid, true);
            foreach ($members as $member) {
                $users[] = $member->id;
            }
        }

        $conversionrequirespolling = false;
        foreach ($users as $userid) {
            mtrace('Converting submission for user id ' . $userid);

            // If the assignment is not vieweable, we should not try to convert the documents
            // for this submission, as it will cause the adhoc task to fail with a permission
            // error.
            //
            // Comments on MDL-56810 indicate that submission conversion should not be attempted
            // if the submission is not viewable due to the user not being enrolled.
            if (!$assign->can_view_submission($userid)) {
                continue;
            }
            // Note: Before MDL-71468, the scheduled task version of this
            // task would stop attempting to poll the conversion after a
            // configured number of attempts were made to poll it, see:
            //
            // mod/assign/feedback/editpdf/classes/task/convert_submissions.php@MOODLE_400_STABLE
            //
            // This means that currently this adhoc task, if it fails, will retry forever. But
            // the fail-delay mechanism will ensure that it eventually only tries once per day.
            //
            // See MDL-75457 for details on re-implementing the conversionattemptlimit.
            //
            // Also note: This code must not be in a try/catch - an exception needs to be thrown to
            // allow the task API to mark the task as failed and update its faildelay. Using
            // manager::adhoc_task_failed in the catch block will not work, as the task API
            // will later assume the task completed successfully (as no exception was thrown) and
            // complete it (removing it from the adhoc task queue).
            $combineddocument = document_services::get_combined_pdf_for_attempt($assign, $userid, $data->submissionattempt);
            switch ($combineddocument->get_status()) {
                case combined_document::STATUS_READY:
                case combined_document::STATUS_READY_PARTIAL:
                case combined_document::STATUS_PENDING_INPUT:
                    // The document has not been converted yet or is somehow still ready.
                    $conversionrequirespolling = true;
                    continue 2;
                case combined_document::STATUS_FAILED:
                    // Although STATUS_FAILED indicates a "permanent error" it should be possible
                    // in some cases to fix the underlying problem, allowing the conversion to
                    // complete. So we throw an exception here, allowing the adhoc task to retry.
                    //
                    // Currently this can result in the task trying indefinitely (although it will
                    // settle on trying once per day due to the faildelay exponential backoff)
                    // however once the conversionattepmtlimit is re-implemented in MDL-75457 the
                    // task will eventually get dropped.
                    throw new \moodle_exception('documentcombinationfailed');
            }

            document_services::get_page_images_for_attempt($assign, $userid, $data->submissionattempt, false);
            document_services::get_page_images_for_attempt($assign, $userid, $data->submissionattempt, true);
        }

        if ($conversionrequirespolling) {
            mtrace('Conversion still in progress. Requeueing self to check again.');
            $task = new self;
            $task->set_custom_data($data);
            $task->set_next_run_time(time() + MINSECS);
            manager::queue_adhoc_task($task);
        } else {
            mtrace('The document has been successfully converted');
        }
    }
}
