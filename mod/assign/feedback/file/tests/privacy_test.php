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
 * Unit tests for assignfeedback_file.
 *
 * @package    assignfeedback_file
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once($CFG->dirroot . '/mod/assign/tests/privacy_test.php');

use mod_assign\privacy\assign_plugin_request_data;

/**
 * Unit tests for mod/assign/feedback/file/classes/privacy/
 *
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assignfeedback_file_privacy_testcase extends \mod_assign\tests\mod_assign_privacy_testcase {

    /**
     * Convenience function for creating feedback data.
     *
     * @param  object   $assign         assign object
     * @param  stdClass $student        user object
     * @param  stdClass $teacher        user object
     * @param  string   $submissiontext Submission text
     * @param  string   $feedbacktext   Feedback text
     * @return array   Feedback plugin object and the grade object.
     */
    protected function create_feedback($assign, $student, $teacher, $submissiontext, $feedbacktext) {

        $submission = new \stdClass();
        $submission->assignment = $assign->get_instance()->id;
        $submission->userid = $student->id;
        $submission->timecreated = time();
        $submission->onlinetext_editor = ['text' => $submissiontext,
                                         'format' => FORMAT_MOODLE];

        $this->setUser($student);
        $notices = [];
        $assign->save_submission($submission, $notices);

        $grade = $assign->get_user_grade($student->id, true);

        $this->setUser($teacher);

        $context = context_user::instance($teacher->id);

        $draftitemid = file_get_unused_draft_itemid();
        file_prepare_draft_area($draftitemid, $context->id, 'assignfeedback_file', 'feedback_files', 1);

        $dummy = array(
            'contextid' => $context->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => $draftitemid,
            'filepath' => '/',
            'filename' => 'feedback1.txt'
        );

        $fs = get_file_storage();
        $file = $fs->create_file_from_string($dummy, $feedbacktext);

        // Create formdata.
        $data = new stdClass();
        $data->{'files_' . $teacher->id . '_filemanager'} = $draftitemid;

        $plugin = $assign->get_feedback_plugin_by_type('file');
        // Save the feedback.
        $plugin->save($grade, $data);

        return [$plugin, $grade];
    }

    /**
     * Quick test to make sure that get_metadata returns something.
     */
    public function test_get_metadata() {
        $collection = new \core_privacy\local\metadata\collection('assignfeedback_file');
        $collection = \assignfeedback_file\privacy\provider::get_metadata($collection);
        $this->assertNotEmpty($collection);
    }

    /**
     * Test that feedback comments are exported for a user.
     */
    public function test_export_feedback_user_data() {
        $this->resetAfterTest();
        // Create course, assignment, submission, and then a feedback comment.
        $course = $this->getDataGenerator()->create_course();
        // Student.
        $user1 = $this->getDataGenerator()->create_user();
        // Teacher.
        $user2 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'editingteacher');
        $assign = $this->create_instance(['course' => $course]);

        $context = $assign->get_context();

        $feedbacktext = '<p>first comment for this test</p>';
        list($plugin, $grade) = $this->create_feedback($assign, $user1, $user2, 'Submission text', $feedbacktext);

        $writer = \core_privacy\local\request\writer::with_context($context);
        $this->assertFalse($writer->has_any_data());

        // The student should be able to see the teachers feedback.
        $exportdata = new \mod_assign\privacy\assign_plugin_request_data($context, $assign, $grade, [], $user1);
        \assignfeedback_file\privacy\provider::export_feedback_user_data($exportdata);
        $feedbackfile = $writer->get_files([get_string('privacy:path', 'assignfeedback_file')])['feedback1.txt'];
        // Check that we got a stored file.
        $this->assertInstanceOf('stored_file', $feedbackfile);
        $this->assertEquals('feedback1.txt', $feedbackfile->get_filename());
    }

    /**
     * Test that all feedback is deleted for a context.
     */
    public function test_delete_feedback_for_context() {
        $this->resetAfterTest();
        // Create course, assignment, submission, and then a feedback comment.
        $course = $this->getDataGenerator()->create_course();
        // Students.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        // Teacher.
        $user3 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user3->id, $course->id, 'editingteacher');
        $assign = $this->create_instance(['course' => $course]);

        $context = $assign->get_context();

        $feedbacktext = '<p>first comment for this test</p>';
        list($plugin1, $grade1) = $this->create_feedback($assign, $user1, $user3, 'Submission text', $feedbacktext);
        $feedbacktext = '<p>Comment for second submission.</p>';
        list($plugin2, $grade2) = $this->create_feedback($assign, $user2, $user3, 'Submission text', $feedbacktext);

        // Check that we have data.
        $this->assertFalse($plugin1->is_empty($grade1));
        $this->assertFalse($plugin2->is_empty($grade2));

        $requestdata = new assign_plugin_request_data($context, $assign);
        \assignfeedback_file\privacy\provider::delete_feedback_for_context($requestdata);

        // Check that we now have no data.
        $this->assertTrue($plugin1->is_empty($grade1));
        $this->assertTrue($plugin2->is_empty($grade2));
    }

    /**
     * Test that a grade item is deleted for a user.
     */
    public function test_delete_feedback_for_grade() {
        $this->resetAfterTest();
        // Create course, assignment, submission, and then a feedback comment.
        $course = $this->getDataGenerator()->create_course();
        // Students.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        // Teacher.
        $user3 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user3->id, $course->id, 'editingteacher');
        $assign = $this->create_instance(['course' => $course]);

        $context = $assign->get_context();

        $feedbacktext = '<p>first comment for this test</p>';
        list($plugin1, $grade1) = $this->create_feedback($assign, $user1, $user3, 'Submission text', $feedbacktext);
        $feedbacktext = '<p>Comment for second submission.</p>';
        list($plugin2, $grade2) = $this->create_feedback($assign, $user2, $user3, 'Submission text', $feedbacktext);

        // Check that we have data.
        $this->assertFalse($plugin1->is_empty($grade1));
        $this->assertFalse($plugin2->is_empty($grade2));

        $requestdata = new assign_plugin_request_data($context, $assign, $grade1, [], $user1);
        \assignfeedback_file\privacy\provider::delete_feedback_for_grade($requestdata);

        // Check that we now have no data.
        $this->assertTrue($plugin1->is_empty($grade1));
        // User 2's data should still be intact.
        $this->assertFalse($plugin2->is_empty($grade2));
    }
}
