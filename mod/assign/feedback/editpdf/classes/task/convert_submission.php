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
use assignfeedback_editpdf\document_services;
use assignfeedback_editpdf\combined_document;
use context_module;
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

        foreach ($users as $userid) {
            mtrace('Converting submission for user id ' . $userid);
            $combineddocument = document_services::get_combined_pdf_for_attempt($assign, $userid, $data->submissionattempt);
            $status = $combineddocument->get_status();
            if ($status == combined_document::STATUS_COMPLETE) {
                document_services::get_page_images_for_attempt($assign, $userid, $data->submissionattempt, false);
                document_services::get_page_images_for_attempt($assign, $userid, $data->submissionattempt, true);

                mtrace('The document has been successfully converted');
            } else {
                throw new \coding_exception('Document conversion completed with status ' . $status);
            }
        }
    }
}
