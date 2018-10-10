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
 * Unit tests for assignfeedback_comments.
 *
 * @package    assignfeedback_comments
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once($CFG->dirroot . '/mod/assign/tests/privacy_test.php');

/**
 * Unit tests for mod/assign/feedback/comments/classes/privacy/
 *
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assignfeedback_comments_privacy_testcase extends \mod_assign\tests\mod_assign_privacy_testcase {

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

        $plugin = $assign->get_feedback_plugin_by_type('comments');
        $feedbackdata = new \stdClass();
        $feedbackdata->assignfeedbackcomments_editor = [
            'text' => $feedbacktext,
            'format' => 1
        ];

        $plugin->save($grade, $feedbackdata);
        return [$plugin, $grade];
    }

    /**
     * Quick test to make sure that get_metadata returns something.
     */
    public function test_get_metadata() {
        $collection = new \core_privacy\local\metadata\collection('assignfeedback_comments');
        $collection = \assignfeedback_comments\privacy\provider::get_metadata($collection);
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
        \assignfeedback_comments\privacy\provider::export_feedback_user_data($exportdata);
        $this->assertEquals($feedbacktext, $writer->get_data(['Feedback comments'])->commenttext);

        // The teacher should also be able to see the feedback that they provided.
        $exportdata = new \mod_assign\privacy\assign_plugin_request_data($context, $assign, $grade, [], $user2);
        \assignfeedback_comments\privacy\provider::export_feedback_user_data($exportdata);
        $this->assertEquals($feedbacktext, $writer->get_data(['Feedback comments'])->commenttext);
    }

    /**
     * Test that all feedback is deleted for a context.
     */
    public function test_delete_feedback_for_context() {
        $this->resetAfterTest();
        // Create course, assignment, submission, and then a feedback comment.
        $course = $this->getDataGenerator()->create_course();
        // Student.
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
        $feedbacktext = '<p>Comment for second student.</p>';
        list($plugin2, $grade2) = $this->create_feedback($assign, $user2, $user3, 'Submission text', $feedbacktext);

        // Check that we have data.
        $feedbackcomments = $plugin1->get_feedback_comments($grade1->id);
        $this->assertNotEmpty($feedbackcomments);
        $feedbackcomments = $plugin1->get_feedback_comments($grade2->id);
        $this->assertNotEmpty($feedbackcomments);

        // Delete all comments for this context.
        $requestdata = new \mod_assign\privacy\assign_plugin_request_data($context, $assign);
        assignfeedback_comments\privacy\provider::delete_feedback_for_context($requestdata);

        // Check that the data is now gone.
        $feedbackcomments = $plugin1->get_feedback_comments($grade1->id);
        $this->assertEmpty($feedbackcomments);
        $feedbackcomments = $plugin1->get_feedback_comments($grade2->id);
        $this->assertEmpty($feedbackcomments);
    }

    /**
     * Test that a grade item is deleted for a user.
     */
    public function test_delete_feedback_for_grade() {
        $this->resetAfterTest();
        // Create course, assignment, submission, and then a feedback comment.
        $course = $this->getDataGenerator()->create_course();
        // Student.
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
        $feedbacktext = '<p>Comment for second student.</p>';
        list($plugin2, $grade2) = $this->create_feedback($assign, $user2, $user3, 'Submission text', $feedbacktext);

        // Check that we have data.
        $feedbackcomments = $plugin1->get_feedback_comments($grade1->id);
        $this->assertNotEmpty($feedbackcomments);
        $feedbackcomments = $plugin1->get_feedback_comments($grade2->id);
        $this->assertNotEmpty($feedbackcomments);

        // Delete all comments for this grade object.
        $requestdata = new \mod_assign\privacy\assign_plugin_request_data($context, $assign, $grade1, [], $user1);
        assignfeedback_comments\privacy\provider::delete_feedback_for_grade($requestdata);

        // These comments should be empty.
        $feedbackcomments = $plugin1->get_feedback_comments($grade1->id);
        $this->assertEmpty($feedbackcomments);

        // These comments should not.
        $feedbackcomments = $plugin1->get_feedback_comments($grade2->id);
        $this->assertNotEmpty($feedbackcomments);
    }

    /**
     * Test that a grade item is deleted for a user.
     */
    public function test_delete_feedback_for_grades() {
        $this->resetAfterTest();
        // Create course, assignment, submission, and then a feedback comment.
        $course = $this->getDataGenerator()->create_course();
        // Student.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();
        // Teacher.
        $user5 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user3->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user4->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user5->id, $course->id, 'editingteacher');
        $assign1 = $this->create_instance(['course' => $course]);
        $assign2 = $this->create_instance(['course' => $course]);

        $feedbacktext = '<p>first comment for this test</p>';
        list($plugin1, $grade1) = $this->create_feedback($assign1, $user1, $user5, 'Submission text', $feedbacktext);
        $feedbacktext = '<p>Comment for second student.</p>';
        list($plugin2, $grade2) = $this->create_feedback($assign1, $user2, $user5, 'Submission text', $feedbacktext);
        $feedbacktext = '<p>Comment for third student.</p>';
        list($plugin3, $grade3) = $this->create_feedback($assign1, $user3, $user5, 'Submission text', $feedbacktext);
        $feedbacktext = '<p>Comment for third student in the second assignment.</p>';
        list($plugin4, $grade4) = $this->create_feedback($assign2, $user3, $user5, 'Submission text', $feedbacktext);
        $feedbacktext = '<p>Comment for fourth student in the second assignment.</p>';
        list($plugin5, $grade5) = $this->create_feedback($assign2, $user4, $user5, 'Submission text', $feedbacktext);

        // Check that we have data.
        $feedbackcomments = $plugin1->get_feedback_comments($grade1->id);
        $this->assertNotEmpty($feedbackcomments);
        $feedbackcomments = $plugin2->get_feedback_comments($grade2->id);
        $this->assertNotEmpty($feedbackcomments);
        $feedbackcomments = $plugin3->get_feedback_comments($grade3->id);
        $this->assertNotEmpty($feedbackcomments);
        $feedbackcomments = $plugin4->get_feedback_comments($grade4->id);
        $this->assertNotEmpty($feedbackcomments);
        $feedbackcomments = $plugin5->get_feedback_comments($grade5->id);
        $this->assertNotEmpty($feedbackcomments);

        $deletedata = new \mod_assign\privacy\assign_plugin_request_data($assign1->get_context(), $assign1);
        $deletedata->set_userids([$user1->id, $user3->id]);
        $deletedata->populate_submissions_and_grades();
        assignfeedback_comments\privacy\provider::delete_feedback_for_grades($deletedata);

        // Check that grade 1 and grade 3 have been removed.
        $feedbackcomments = $plugin1->get_feedback_comments($grade1->id);
        $this->assertEmpty($feedbackcomments);
        $feedbackcomments = $plugin2->get_feedback_comments($grade2->id);
        $this->assertNotEmpty($feedbackcomments);
        $feedbackcomments = $plugin3->get_feedback_comments($grade3->id);
        $this->assertEmpty($feedbackcomments);
        $feedbackcomments = $plugin4->get_feedback_comments($grade4->id);
        $this->assertNotEmpty($feedbackcomments);
        $feedbackcomments = $plugin5->get_feedback_comments($grade5->id);
        $this->assertNotEmpty($feedbackcomments);
    }
}
