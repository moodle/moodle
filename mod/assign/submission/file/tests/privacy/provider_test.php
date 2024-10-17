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

namespace assignsubmission_file\privacy;

/**
 * Unit tests for mod/assign/submission/file/classes/privacy/
 *
 * @package    assignsubmission_file
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \assignsubmission_file\privacy\provider
 */
final class provider_test extends \mod_assign\tests\provider_testcase {
    /**
     * Convenience function for creating feedback data.
     *
     * @param  object   $assign         assign object
     * @param  stdClass $student        user object
     * @param  string   $filename       filename for the file submission
     * @return array   Submission plugin object and the submission object.
     */
    protected function create_file_submission($assign, $student, $filename) {
        global $CFG;
        // Create a file submission with the test pdf.
        $submission = $assign->get_user_submission($student->id, true);

        $this->setUser($student->id);

        $fs = get_file_storage();
        $pdfsubmission = (object) array(
            'contextid' => $assign->get_context()->id,
            'component' => 'assignsubmission_file',
            'filearea' => ASSIGNSUBMISSION_FILE_FILEAREA,
            'itemid' => $submission->id,
            'filepath' => '/',
            'filename' => $filename
        );
        $sourcefile = $CFG->dirroot.'/mod/assign/feedback/editpdf/tests/fixtures/submission.pdf';
        $fi = $fs->create_file_from_pathname($pdfsubmission, $sourcefile);

        $data = new \stdClass();
        $plugin = $assign->get_submission_plugin_by_type('file');
        $plugin->save($submission, $data);

        return [$plugin, $submission];
    }

    /**
     * Quick test to make sure that get_metadata returns something.
     */
    public function test_get_metadata(): void {
        $collection = new \core_privacy\local\metadata\collection('assignsubmission_file');
        $collection = \assignsubmission_file\privacy\provider::get_metadata($collection);
        $this->assertNotEmpty($collection);
    }

    /**
     * Test that submission files are exported for a user.
     */
    public function test_export_submission_user_data(): void {
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

        $studentfilename = 'user1file.pdf';
        list($plugin, $submission) = $this->create_file_submission($assign, $user1, $studentfilename);

        $writer = \core_privacy\local\request\writer::with_context($context);
        $this->assertFalse($writer->has_any_data());

        // The student should have a file submission.
        $exportdata = new \mod_assign\privacy\assign_plugin_request_data($context, $assign, $submission, ['Attempt 1']);
        \assignsubmission_file\privacy\provider::export_submission_user_data($exportdata);
        // print_object($writer);
        $storedfile = $writer->get_files(['Attempt 1'])['user1file.pdf'];
        $this->assertInstanceOf('stored_file', $storedfile);
        $this->assertEquals($studentfilename, $storedfile->get_filename());
    }

    /**
     * Test that all submission files are deleted for this context.
     */
    public function test_delete_submission_for_context(): void {
        $this->resetAfterTest();
        // Create course, assignment, submission, and then a feedback comment.
        $course = $this->getDataGenerator()->create_course();
        // Student.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');

        $assign = $this->create_instance(['course' => $course]);

        $context = $assign->get_context();

        $studentfilename = 'user1file.pdf';
        list($plugin, $submission) = $this->create_file_submission($assign, $user1, $studentfilename);
        $student2filename = 'user2file.pdf';
        list($plugin2, $submission2) = $this->create_file_submission($assign, $user2, $studentfilename);

        // Only need the context and assign object in this plugin for this operation.
        $requestdata = new \mod_assign\privacy\assign_plugin_request_data($context, $assign);
        \assignsubmission_file\privacy\provider::delete_submission_for_context($requestdata);
        // This checks that there are no files in this submission.
        $this->assertTrue($plugin->is_empty($submission));
        $this->assertTrue($plugin2->is_empty($submission2));
    }

    /**
     * Test that the comments for a user are deleted.
     */
    public function test_delete_submission_for_userid(): void {
        $this->resetAfterTest();
        // Create course, assignment, submission, and then a feedback comment.
        $course = $this->getDataGenerator()->create_course();
        // Student.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');

        $assign = $this->create_instance(['course' => $course]);

        $context = $assign->get_context();

        $studentfilename = 'user1file.pdf';
        list($plugin, $submission) = $this->create_file_submission($assign, $user1, $studentfilename);
        $student2filename = 'user2file.pdf';
        list($plugin2, $submission2) = $this->create_file_submission($assign, $user2, $studentfilename);

        // Only need the context and assign object in this plugin for this operation.
        $requestdata = new \mod_assign\privacy\assign_plugin_request_data($context, $assign, $submission, [], $user1);
        \assignsubmission_file\privacy\provider::delete_submission_for_userid($requestdata);
        // This checks that there are no files in this submission.
        $this->assertTrue($plugin->is_empty($submission));
        // There should be files here.
        $this->assertFalse($plugin2->is_empty($submission2));
    }

    /**
     * Test deletion of bulk submissions for a context.
     */
    public function test_delete_submissions(): void {
        global $DB;

        $this->resetAfterTest();
        // Create course, assignment, submission, and then a feedback comment.
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

        $assign1 = $this->create_instance(['course' => $course]);
        $assign2 = $this->create_instance(['course' => $course]);

        $context1 = $assign1->get_context();
        $context2 = $assign2->get_context();

        $student1filename = 'user1file.pdf';
        list($plugin1, $submission1) = $this->create_file_submission($assign1, $user1, $student1filename);
        $student2filename = 'user2file.pdf';
        list($plugin2, $submission2) = $this->create_file_submission($assign1, $user2, $student2filename);
        $student3filename = 'user3file.pdf';
        list($plugin3, $submission3) = $this->create_file_submission($assign1, $user3, $student3filename);
        $student4filename = 'user4file.pdf';
        list($plugin4, $submission4) = $this->create_file_submission($assign2, $user4, $student4filename);
        $student5filename = 'user5file.pdf';
        list($plugin5, $submission5) = $this->create_file_submission($assign2, $user3, $student5filename);

        $submissionids = [
            $submission1->id,
            $submission3->id
        ];

        $userids = [
            $user1->id,
            $user3->id
        ];

        $data = $DB->get_records('files', ['contextid' => $context1->id, 'component' => 'assignsubmission_file']);
        $this->assertCount(6, $data);

        $data = $DB->get_records('assignsubmission_file', ['assignment' => $assign1->get_instance()->id]);
        $this->assertCount(3, $data);

        // Records in the second assignment (not being touched).
        $data = $DB->get_records('assignsubmission_file', ['assignment' => $assign2->get_instance()->id]);
        $this->assertCount(2, $data);

        $deletedata = new \mod_assign\privacy\assign_plugin_request_data($context1, $assign1);
        $deletedata->set_userids($userids);
        $deletedata->populate_submissions_and_grades();
        \assignsubmission_file\privacy\provider::delete_submissions($deletedata);
        $data = $DB->get_records('files', ['contextid' => $context1->id, 'component' => 'assignsubmission_file']);
        $this->assertCount(2, $data);

        // Submission 1 and 3 have been removed. We should be left with submission2.
        $data = $DB->get_records('assignsubmission_file', ['assignment' => $assign1->get_instance()->id]);
        $this->assertCount(1, $data);

        // This should be untouched.
        $data = $DB->get_records('assignsubmission_file', ['assignment' => $assign2->get_instance()->id]);
        $this->assertCount(2, $data);
    }
}
