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

namespace assignfeedback_comments;

use assign;

/**
 * Helper trait for assignfeedback_comments tests.
 *
 * @package    assignfeedback_comments
 * @copyright  2025 Michael Kotlyar <michael.kotlyar@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait feedback_helper_trait {
    /**
     * Convenience function for creating feedback data.
     *
     * @param  assign    $assign         assign object
     * @param  \stdClass $student        user object
     * @param  \stdClass $teacher        user object
     * @param  string    $submissiontext Submission text
     * @param  string    $feedbacktext   Feedback text
     * @param  bool      $markercomment  True/False is it an allocated marker comment?
     * @return array     Feedback plugin object and the grade object.
     */
    protected function create_feedback(
        assign $assign,
        \stdClass $student,
        \stdClass $teacher,
        string $submissiontext,
        string $feedbacktext,
        bool $markercomment = false,
    ): array {
        global $CFG;

        $submission = new \stdClass();
        $submission->assignment = $assign->get_instance()->id;
        $submission->userid = $student->id;
        $submission->timecreated = \core\di::get(\core\clock::class)->time();
        $submission->onlinetext_editor = ['text' => $submissiontext, 'format' => FORMAT_MOODLE];

        $this->setUser($student);
        $notices = [];
        $assign->save_submission($submission, $notices);

        $grade = $assign->get_user_grade($student->id, true);

        $this->setUser($teacher);

        if ($markercomment) {
            $assign->set_is_marking(true);
            $grade->grader = $teacher->id;
        }

        $context = \context_user::instance($teacher->id);

        $draftitemid = file_get_unused_draft_itemid();
        file_prepare_draft_area(
            $draftitemid,
            $context->id,
            ASSIGNFEEDBACK_COMMENTS_COMPONENT,
            ASSIGNFEEDBACK_COMMENTS_FILEAREA,
            $grade->id,
        );

        $dummy = [
            'contextid' => $context->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => $draftitemid,
            'filepath' => '/',
            'filename' => 'feedback1.txt',
        ];

        $fs = get_file_storage();
        $fs->create_file_from_string($dummy, $feedbacktext);

        $feedbacktext = $feedbacktext .
            " <img src='{$CFG->wwwroot}/draftfile.php/{$context->id}/user/draft/{$draftitemid}/feedback1.txt.png>";

        $plugin = $assign->get_feedback_plugin_by_type('comments');
        $feedbackdata = new \stdClass();
        $feedbackdata->assignfeedbackcomments_editor = [
            'text' => $feedbacktext,
            'format' => FORMAT_HTML,
            'itemid' => $draftitemid,
        ];

        $plugin->save($grade, $feedbackdata);
        return [$plugin, $grade];
    }
}
