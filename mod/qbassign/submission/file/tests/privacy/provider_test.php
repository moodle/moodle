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
 * Unit tests for qbassignsubmission_file.
 *
 * @package    qbassignsubmission_file
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace qbassignsubmission_file\privacy;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/qbassign/tests/privacy/provider_test.php');

/**
 * Unit tests for mod/qbassign/submission/file/classes/privacy/
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
     * @param  string   $filename       filename for the file submission
     * @return array   Submission plugin object and the submission object.
     */
    protected function create_file_submission($qbassign, $student, $filename) {
        global $CFG;
        // Create a file submission with the test pdf.
        $submission = $qbassign->get_user_submission($student->id, true);

        $this->setUser($student->id);

        $fs = get_file_storage();
        $pdfsubmission = (object) array(
            'contextid' => $qbassign->get_context()->id,
            'component' => 'qbassignsubmission_file',
            'filearea' => qbassignSUBMISSION_FILE_FILEAREA,
            'itemid' => $submission->id,
            'filepath' => '/',
            'filename' => $filename
        );
        $sourcefile = $CFG->dirroot.'/mod/qbassign/feedback/editpd/tests/fixtures/submission.pdf';
        $fi = $fs->create_file_from_pathname($pdfsubmission, $sourcefile);

        $data = new \stdClass();
        $plugin = $qbassign->get_submission_plugin_by_type('file');
        $plugin->save($submission, $data);

        return [$plugin, $submission];
    }

    /**
     * Quick test to make sure that get_metadata returns something.
     */
    public function test_get_metadata() {
        $collection = new \core_privacy\local\metadata\collection('qbassignsubmission_file');
        $collection = \qbassignsubmission_file\privacy\provider::get_metadata($collection);
        $this->assertNotEmpty($collection);
    }

    /**
     * Test that submission files are exported for a user.
     */
    public function test_export_submission_user_data() {
        $this->resetAfterTest();
        // Create course, qbassignment, submission, and then a feedback comment.
        $course = $this->getDataGenerator()->create_course();
        // Student.
        $user1 = $this->getDataGenerator()->create_user();
        // Teacher.
        $user2 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'editingteacher');
        $qbassign = $this->create_instance(['course' => $course]);

        $context = $qbassign->get_context();

        $studentfilename = 'user1file.pdf';
        list($plugin, $submission) = $this->create_file_submission($qbassign, $user1, $studentfilename);

        $writer = \core_privacy\local\request\writer::with_context($context);
        $this->assertFalse($writer->has_any_data());

        // The student should have a file submission.
        $exportdata = new \mod_qbassign\privacy\qbassign_plugin_request_data($context, $qbassign, $submission, ['Attempt 1']);
        \qbassignsubmission_file\privacy\provider::export_submission_user_data($exportdata);
        // print_object($writer);
        $storedfile = $writer->get_files(['Attempt 1'])['user1file.pdf'];
        $this->assertInstanceOf('stored_file', $storedfile);
        $this->assertEquals($studentfilename, $storedfile->get_filename());
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

        $studentfilename = 'user1file.pdf';
        list($plugin, $submission) = $this->create_file_submission($qbassign, $user1, $studentfilename);
        $student2filename = 'user2file.pdf';
        list($plugin2, $submission2) = $this->create_file_submission($qbassign, $user2, $studentfilename);

        // Only need the context and qbassign object in this plugin for this operation.
        $requestdata = new \mod_qbassign\privacy\qbassign_plugin_request_data($context, $qbassign);
        \qbassignsubmission_file\privacy\provider::delete_submission_for_context($requestdata);
        // This checks that there are no files in this submission.
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

        $studentfilename = 'user1file.pdf';
        list($plugin, $submission) = $this->create_file_submission($qbassign, $user1, $studentfilename);
        $student2filename = 'user2file.pdf';
        list($plugin2, $submission2) = $this->create_file_submission($qbassign, $user2, $studentfilename);

        // Only need the context and qbassign object in this plugin for this operation.
        $requestdata = new \mod_qbassign\privacy\qbassign_plugin_request_data($context, $qbassign, $submission, [], $user1);
        \qbassignsubmission_file\privacy\provider::delete_submission_for_userid($requestdata);
        // This checks that there are no files in this submission.
        $this->assertTrue($plugin->is_empty($submission));
        // There should be files here.
        $this->assertFalse($plugin2->is_empty($submission2));
    }

    /**
     * Test deletion of bulk submissions for a context.
     */
    public function test_delete_submissions() {
        global $DB;

        $this->resetAfterTest();
        // Create course, qbassignment, submission, and then a feedback comment.
        $course = $this->getDataGenerator()->create_course();
        // Student.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user3->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user4->id, $course->id, 'student');

        $qbassign1 = $this->create_instance(['course' => $course]);
        $qbassign2 = $this->create_instance(['course' => $course]);

        $context1 = $qbassign1->get_context();
        $context2 = $qbassign2->get_context();

        $student1filename = 'user1file.pdf';
        list($plugin1, $submission1) = $this->create_file_submission($qbassign1, $user1, $student1filename);
        $student2filename = 'user2file.pdf';
        list($plugin2, $submission2) = $this->create_file_submission($qbassign1, $user2, $student2filename);
        $student3filename = 'user3file.pdf';
        list($plugin3, $submission3) = $this->create_file_submission($qbassign1, $user3, $student3filename);
        $student4filename = 'user4file.pdf';
        list($plugin4, $submission4) = $this->create_file_submission($qbassign2, $user4, $student4filename);
        $student5filename = 'user5file.pdf';
        list($plugin5, $submission5) = $this->create_file_submission($qbassign2, $user3, $student5filename);

        $submissionids = [
            $submission1->id,
            $submission3->id
        ];

        $userids = [
            $user1->id,
            $user3->id
        ];

        $data = $DB->get_records('files', ['contextid' => $context1->id, 'component' => 'qbassignsubmission_file']);
        $this->assertCount(6, $data);

        $data = $DB->get_records('qbassignsubmission_file', ['qbassignment' => $qbassign1->get_instance()->id]);
        $this->assertCount(3, $data);

        // Records in the second qbassignment (not being touched).
        $data = $DB->get_records('qbassignsubmission_file', ['qbassignment' => $qbassign2->get_instance()->id]);
        $this->assertCount(2, $data);

        $deletedata = new \mod_qbassign\privacy\qbassign_plugin_request_data($context1, $qbassign1);
        $deletedata->set_userids($userids);
        $deletedata->populate_submissions_and_grades();
        \qbassignsubmission_file\privacy\provider::delete_submissions($deletedata);
        $data = $DB->get_records('files', ['contextid' => $context1->id, 'component' => 'qbassignsubmission_file']);
        $this->assertCount(2, $data);

        // Submission 1 and 3 have been removed. We should be left with submission2.
        $data = $DB->get_records('qbassignsubmission_file', ['qbassignment' => $qbassign1->get_instance()->id]);
        $this->assertCount(1, $data);

        // This should be untouched.
        $data = $DB->get_records('qbassignsubmission_file', ['qbassignment' => $qbassign2->get_instance()->id]);
        $this->assertCount(2, $data);
    }
}
