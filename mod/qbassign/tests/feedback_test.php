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

namespace mod_qbassign;

use qbassignfeedback_editpd\document_services;
use qbassignfeedback_editpd\combined_document;
use mod_qbassign_test_generator;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/qbassign/locallib.php');
require_once($CFG->dirroot . '/mod/qbassign/tests/generator.php');

/**
 * Provides the unit tests for feedback.
 *
 * @package     mod_qbassign
 * @category    test
 * @copyright   2019 Ilya Tregubov ilyatregubov@catalyst-au.net
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class feedback_test extends \advanced_testcase {

    // Use the generator helper.
    use mod_qbassign_test_generator;

    /**
     * Helper to create a stored file object with the given supplied content.
     *
     * @param   int $contextid context id for assigment
     * @param   int $itemid item id from assigment grade
     * @param   string $filearea File area
     * @param   int $timemodified Time modified
     * @param   string $filecontent The content of the mocked file
     * @param   string $filename The file name to use in the stored_file
     * @param   string $filerecord Any overrides to the filerecord
     * @return  stored_file
     */
    protected function create_stored_file($contextid, $itemid, $filearea, $timemodified,
                                          $filecontent = 'content', $filename = 'combined.pdf', $filerecord = []) {
        $filerecord = array_merge([
            'contextid' => $contextid,
            'component' => 'qbassignfeedback_editpd',
            'filearea'  => $filearea,
            'itemid'    => $itemid,
            'filepath'  => '/',
            'filename'  => $filename,
            'timemodified' => $timemodified,
        ], $filerecord);

        $fs = get_file_storage();
        $file = $fs->create_file_from_string($filerecord, $filecontent);

        return $file;
    }

    /**
     * Convenience function to create an instance of an qbassignment.
     *
     * @param array $params Array of parameters to pass to the generator
     * @return qbassign The qbassign class.
     */
    protected function create_instance($params = array()) {
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_qbassign');
        $instance = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('qbassign', $instance->id);
        $context = \context_module::instance($cm->id);
        return new \qbassign($context, $cm, $params['course']);
    }

    /**
     * Test fetching combined.pdf for state checking.
     */
    public function test_get_combined_document_for_attempt() {

        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();

        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'student');

        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');

        $qbassign = $this->create_instance([
            'course' => $course,
            'name' => 'qbassign 1',
            'attemptreopenmethod' => qbassign_ATTEMPT_REOPEN_METHOD_MANUAL,
            'maxattempts' => 3,
            'qbassignsubmission_onlinetex_enabled' => true,
            'qbassignfeedback_comments_enabled' => true
        ]);

        $submission = new \stdClass();
        $submission->qbassignment = $qbassign->get_instance()->id;
        $submission->userid = $user->id;
        $submission->timecreated = time();
        $submission->timemodified = time();
        $submission->onlinetex_editor = ['text' => 'Submission text',
            'format' => FORMAT_MOODLE];

        $this->setUser($user);
        $notices = [];
        $qbassign->save_submission($submission, $notices);

        $this->setUser($teacher);

        $grade = '3.14';
        $teachercommenttext = 'This is better. Thanks.';
        $data = new \stdClass();
        $data->attemptnumber = 1;
        $data->grade = $grade;
        $data->qbassignfeedbackcomments_editor = ['text' => $teachercommenttext, 'format' => FORMAT_MOODLE];

        // Give the submission a grade.
        $qbassign->save_grade($user->id, $data);

        $grade = $qbassign->get_user_grade($user->id, true, -1);

        $contextid = $qbassign->get_context()->id;
        $itemid = $grade->id;

        // Create combined document in combined area.
        $this->create_stored_file($contextid, $itemid, 'combined', time());

        $document = document_services::get_combined_document_for_attempt($qbassign, $user->id, -1);
        $status = $document->get_status();

        $this->assertEquals($status, combined_document::STATUS_COMPLETE);

        // Create orphaned combined document in partial area.
        $this->create_stored_file($contextid, $itemid, 'partial', time() - 3600);

        $document = document_services::get_combined_document_for_attempt($qbassign, $user->id, -1);
        $status = $document->get_status();

        $this->assertEquals($status, combined_document::STATUS_FAILED);
    }

}
