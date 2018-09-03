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
 * Unit tests for assignsubmission_file.
 *
 * @package    assignsubmission_file
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/tests/privacy_test.php');

/**
 * Unit tests for mod/assign/submission/file/classes/privacy/
 *
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assignsubmission_file_privacy_testcase extends \mod_assign\tests\mod_assign_privacy_testcase {

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
    public function test_get_metadata() {
        $collection = new \core_privacy\local\metadata\collection('assignsubmission_file');
        $collection = \assignsubmission_file\privacy\provider::get_metadata($collection);
        $this->assertNotEmpty($collection);
    }

    /**
     * Test that submission files are exported for a user.
     */
    public function test_export_submission_user_data() {
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
    public function test_delete_submission_for_context() {
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
    public function test_delete_submission_for_userid() {
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
}
