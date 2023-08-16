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
 * Unit tests for qbassignsubmission_codeblock.
 *
 * @package    qbassignsubmission_codeblock
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace qbassignsubmission_codeblock\privacy;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/qbassign/tests/privacy/provider_test.php');

/**
 * Unit tests for mod/qbassign/submission/codeblock/classes/privacy/
 *
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends \mod_qbassign\privacy\provider_test {

    /**
     * Convenience function for creating feedback data.
     *
     * @param  object   $qbassign         qbassign object
     * @param  stdClass $student        user object
     * @param  string   $text           Submission text.
     * @return array   Submission plugin object and the submission object.
     */
    protected function create_online_submission($qbassign, $student, $text) {
        global $CFG;

        $this->setUser($student->id);
        $submission = $qbassign->get_user_submission($student->id, true);
        $data = new \stdClass();
        $data->codeblock_editor = array(
            'itemid' => file_get_unused_draft_itemid(),
            'text' => $text,
            'format' => FORMAT_PLAIN
        );

        $submission = $qbassign->get_user_submission($student->id, true);

        $plugin = $qbassign->get_submission_plugin_by_type('codeblock');
        $plugin->save($submission, $data);

        return [$plugin, $submission];
    }

    /**
     * Quick test to make sure that get_metadata returns something.
     */
    public function test_get_metadata() {
        $collection = new \core_privacy\local\metadata\collection('qbassignsubmission_codeblock');
        $collection = \qbassignsubmission_codeblock\privacy\provider::get_metadata($collection);
        $this->assertNotEmpty($collection);
    }

    /**
     * Test that submission files and text are exported for a user.
     */
    public function test_export_submission_user_data() {
        $this->resetAfterTest();
        // Create course, qbassignment, submission, and then a feedback comment.
        $course = $this->getDataGenerator()->create_course();
        // Student.
        $user1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $qbassign = $this->create_instance(['course' => $course]);

        $context = $qbassign->get_context();

        $submissiontext = 'Just some text';
        list($plugin, $submission) = $this->create_online_submission($qbassign, $user1, $submissiontext);

        $writer = \core_privacy\local\request\writer::with_context($context);
        $this->assertFalse($writer->has_any_data());

        // The student should have some text submitted.
        $exportdata = new \mod_qbassign\privacy\qbassign_plugin_request_data($context, $qbassign, $submission, ['Attempt 1']);
        \qbassignsubmission_codeblock\privacy\provider::export_submission_user_data($exportdata);
        $this->assertEquals($submissiontext, $writer->get_data(['Attempt 1',
                get_string('privacy:path', 'qbassignsubmission_codeblock')])->text);
    }

    /**
     * Test that all submission files are deleted for this context.
     */
    public function test_delete_submission_for_context() {
        $this->resetAfterTest();
        // Create course, qbassignment, submission, and then a feedback comment.
        $course = $this->getDataGenerator()->create_course();
        // Student.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');

        $qbassign = $this->create_instance(['course' => $course]);

        $context = $qbassign->get_context();

        $studenttext = 'Student one\'s text.';
        list($plugin, $submission) = $this->create_online_submission($qbassign, $user1, $studenttext);
        $studenttext2 = 'Student two\'s text.';
        list($plugin2, $submission2) = $this->create_online_submission($qbassign, $user2, $studenttext2);

        // Only need the context and qbassign object in this plugin for this operation.
        $requestdata = new \mod_qbassign\privacy\qbassign_plugin_request_data($context, $qbassign);
        \qbassignsubmission_codeblock\privacy\provider::delete_submission_for_context($requestdata);
        // This checks that there is no content for these submissions.
        $this->assertTrue($plugin->is_empty($submission));
        $this->assertTrue($plugin2->is_empty($submission2));
    }

    /**
     * Test that the comments for a user are deleted.
     */
    public function test_delete_submission_for_userid() {
        $this->resetAfterTest();
        // Create course, qbassignment, submission, and then a feedback comment.
        $course = $this->getDataGenerator()->create_course();
        // Student.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');

        $qbassign = $this->create_instance(['course' => $course]);

        $context = $qbassign->get_context();

        $studenttext = 'Student one\'s text.';
        list($plugin, $submission) = $this->create_online_submission($qbassign, $user1, $studenttext);
        $studenttext2 = 'Student two\'s text.';
        list($plugin2, $submission2) = $this->create_online_submission($qbassign, $user2, $studenttext2);

        // Need more data for this operation.
        $requestdata = new \mod_qbassign\privacy\qbassign_plugin_request_data($context, $qbassign, $submission, [], $user1);
        \qbassignsubmission_codeblock\privacy\provider::delete_submission_for_userid($requestdata);
        // This checks that there is no content for the first submission.
        $this->assertTrue($plugin->is_empty($submission));
        // But there is for the second submission.
        $this->assertFalse($plugin2->is_empty($submission2));
    }

    public function test_delete_submissions() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        // Only makes submissions in the second qbassignment.
        $user4 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user3->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user4->id, $course->id, 'student');

        $qbassign1 = $this->create_instance(['course' => $course]);
        $qbassign2 = $this->create_instance(['course' => $course]);

        $context1 = $qbassign1->get_context();
        $context2 = $qbassign2->get_context();

        $student1text = 'Student one\'s text.';
        list($plugin1, $submission1) = $this->create_online_submission($qbassign1, $user1, $student1text);
        $student2text = 'Student two\'s text.';
        list($plugin2, $submission2) = $this->create_online_submission($qbassign1, $user2, $student2text);
        $student3text = 'Student two\'s text.';
        list($plugin3, $submission3) = $this->create_online_submission($qbassign1, $user3, $student3text);
        // Now for submissions in qbassignment two.
        $student3text2 = 'Student two\'s text for the second qbassignment.';
        list($plugin4, $submission4) = $this->create_online_submission($qbassign2, $user3, $student3text2);
        $student4text = 'Student four\'s text.';
        list($plugin5, $submission5) = $this->create_online_submission($qbassign2, $user4, $student4text);

        $data = $DB->get_records('qbassignsubmission_codeblock', ['qbassignment' => $qbassign1->get_instance()->id]);
        $this->assertCount(3, $data);
        // Delete the submissions for user 1 and 3.
        $requestdata = new \mod_qbassign\privacy\qbassign_plugin_request_data($context1, $qbassign1);
        $requestdata->set_userids([$user1->id, $user2->id]);
        $requestdata->populate_submissions_and_grades();
        \qbassignsubmission_codeblock\privacy\provider::delete_submissions($requestdata);

        // There should only be one record left for qbassignment one.
        $data = $DB->get_records('qbassignsubmission_codeblock', ['qbassignment' => $qbassign1->get_instance()->id]);
        $this->assertCount(1, $data);

        // Check that the second qbassignment has not been touched.
        $data = $DB->get_records('qbassignsubmission_codeblock', ['qbassignment' => $qbassign2->get_instance()->id]);
        $this->assertCount(2, $data);
    }
}
