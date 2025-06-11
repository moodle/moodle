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
 * Unit tests for assignsubmission_onenote.
 *
 * @package    assignsubmission_onenote
 * @copyright  Microsoft, Inc.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignsubmission_onenote\privacy;

use core_privacy\local\metadata\collection;
use file_storage;
use local_onenote\api\base;
use mod_assign\privacy\assign_plugin_request_data;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once($CFG->dirroot . '/mod/assign/tests/privacy/provider_test.php');

/**
 * Unit tests for mod/assign/submission/onenote/classes/privacy/
 *
 * @copyright  Microsoft, Inc.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \assignsubmission_onenote\privacy\provider
 */
final class provider_test extends \mod_assign\tests\provider_testcase {
    /**
     * Quick test to make sure that get_metadata returns something.
     *
     * @covers \assignsubmission_onenote\privacy\provider::get_metadata
     */
    public function test_get_metadata(): void {
        $collection = new collection('assignsubmission_onenote');
        $collection = provider::get_metadata($collection);
        $this->assertNotEmpty($collection);
    }

    /**
     * Test that comments are exported for a user.
     *
     * @covers \assignsubmission_onenote\privacy\provider::export_submission_user_data
     */
    public function test_export_submission_user_data(): void {
        $this->resetAfterTest();
        // Create course, assignment, submission, and then a feedback comment.
        $course = $this->getDataGenerator()->create_course();
        // Student.
        $user1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $assign = $this->create_instance(['course' => $course]);

        $context = $assign->get_context();

        $submissiontext = 'Just some text';
        [$plugin, $submission] = $this->create_onenote_submission($assign, $user1, $submissiontext);

        $writer = \core_privacy\local\request\writer::with_context($context);
        $this->assertFalse($writer->has_any_data());

        // The student should have some text submitted.
        $exportdata = new assign_plugin_request_data($context, $assign, $submission, ['Attempt 1']);
        provider::export_submission_user_data($exportdata);
        $exporteddata = $writer->get_data(['Attempt 1', get_string('privacy:path', 'assignsubmission_onenote')]);
        $this->assertEquals($assign->get_instance()->id, $exporteddata->assignment);
        $this->assertEquals($submission->id, $exporteddata->submission);
        $this->assertEquals('1', $exporteddata->numfiles);

        $filespath = ['Attempt 1', get_string('privacy:path', 'assignsubmission_onenote')];
        $submissionfiles = $writer->get_files($filespath);
        $this->assertEquals(1, count($submissionfiles));
        $submissionfile = array_shift($submissionfiles);
        $this->assertInstanceOf('stored_file', $submissionfile);
        $this->assertTrue(strpos($submissionfile->get_filename(), 'OneNote_') === 0);
    }

    /**
     * Test that all comments are deleted for this context.
     *
     * @covers \assignsubmission_onenote\privacy\provider::delete_submission_for_context
     */
    public function test_delete_submission_for_context(): void {
        $this->resetAfterTest();
        // Create course, assignment, submission.
        $course = $this->getDataGenerator()->create_course();
        // Student.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');

        $assign = $this->create_instance(['course' => $course]);

        $context = $assign->get_context();

        $studenttext = 'Student one\'s text.';
        [$plugin, $submission] = $this->create_onenote_submission($assign, $user1, $studenttext);
        $studenttext2 = 'Student two\'s text.';
        [$plugin2, $submission2] = $this->create_onenote_submission($assign, $user2, $studenttext2);

        $fs = new file_storage();
        $files = $fs->get_area_files($assign->get_context()->id, 'assignsubmission_onenote',
            base::ASSIGNSUBMISSION_ONENOTE_FILEAREA);
        // 4 including directories.
        $this->assertEquals(4, count($files));

        // Only need the context and assign object in this plugin for this operation.
        $requestdata = new assign_plugin_request_data($context, $assign);
        provider::delete_submission_for_context($requestdata);
        // This checks that there is no content for these submissions.
        $this->assertTrue($plugin->is_empty($submission));
        $this->assertTrue($plugin2->is_empty($submission2));

        $fs = new file_storage();
        $files = $fs->get_area_files($assign->get_context()->id, 'assignsubmission_onenote',
            base::ASSIGNSUBMISSION_ONENOTE_FILEAREA);
        $this->assertEquals(0, count($files));
    }

    /**
     * Test that the comments for a user are deleted.
     * @covers \assignsubmission_onenote\privacy\provider::delete_submission_for_userid
     */
    public function test_delete_submission_for_userid(): void {
        $this->resetAfterTest();
        // Create course, assignment, submission.
        $course = $this->getDataGenerator()->create_course();
        // Student.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');

        $assign = $this->create_instance(['course' => $course]);

        $context = $assign->get_context();

        $studenttext = 'Student one\'s text.';
        [$plugin, $submission] = $this->create_onenote_submission($assign, $user1, $studenttext);
        $studenttext2 = 'Student two\'s text.';
        [$plugin2, $submission2] = $this->create_onenote_submission($assign, $user2, $studenttext2);

        $fs = new file_storage();
        $files = $fs->get_area_files($assign->get_context()->id, 'assignsubmission_onenote',
            base::ASSIGNSUBMISSION_ONENOTE_FILEAREA);
        // 4 including directories.
        $this->assertEquals(4, count($files));

        // Need more data for this operation.
        $requestdata = new assign_plugin_request_data($context, $assign, $submission, [], $user1);
        provider::delete_submission_for_userid($requestdata);
        // This checks that there is no content for the first submission.
        $this->assertTrue($plugin->is_empty($submission));
        // But there is for the second submission.
        $this->assertFalse($plugin2->is_empty($submission2));

        $fs = new file_storage();
        $files = $fs->get_area_files($assign->get_context()->id, 'assignsubmission_onenote',
            base::ASSIGNSUBMISSION_ONENOTE_FILEAREA);
        // 2 files that were not deleted.
        $this->assertEquals(2, count($files));
    }

    /**
     * Test deletion of all submissions for a context works.
     * @covers \assignsubmission_onenote\privacy\provider::delete_submissions
     */
    public function test_delete_submissions(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        // Only makes submissions in the second assignment.
        $user4 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user3->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user4->id, $course->id, 'student');

        $assign1 = $this->create_instance(['course' => $course]);
        $assign2 = $this->create_instance(['course' => $course]);

        $context1 = $assign1->get_context();
        $context2 = $assign2->get_context();

        $student1text = 'Student one\'s text.';
        [$plugin1, $submission1] = $this->create_onenote_submission($assign1, $user1, $student1text);
        $student2text = 'Student two\'s text.';
        [$plugin2, $submission2] = $this->create_onenote_submission($assign1, $user2, $student2text);
        $student3text = 'Student three\'s text.';
        [$plugin3, $submission3] = $this->create_onenote_submission($assign1, $user3, $student3text);
        // Now for submissions in assignment two.
        $student3text2 = 'Student two\'s text for the second assignment.';
        [$plugin4, $submission4] = $this->create_onenote_submission($assign2, $user3, $student3text2);
        $student4text = 'Student four\'s text.';
        [$plugin5, $submission5] = $this->create_onenote_submission($assign2, $user4, $student4text);

        $fs = new file_storage();
        // 6 including directories for assign 1.
        // 4 including directories for assign 2.
        $this->assertCount(6, $fs->get_area_files($assign1->get_context()->id, 'assignsubmission_onenote',
            base::ASSIGNSUBMISSION_ONENOTE_FILEAREA));
        $this->assertCount(4, $fs->get_area_files($assign2->get_context()->id, 'assignsubmission_onenote',
            base::ASSIGNSUBMISSION_ONENOTE_FILEAREA));

        $data = $DB->get_records('assignsubmission_onenote', ['assignment' => $assign1->get_instance()->id]);
        $this->assertCount(3, $data);
        // Delete the submissions for user 1 and 3.
        $requestdata = new assign_plugin_request_data($context1, $assign1);
        $requestdata->set_userids([$user1->id, $user2->id]);
        $requestdata->populate_submissions_and_grades();
        provider::delete_submissions($requestdata);

        // There should only be one record left for assignment one.
        $data = $DB->get_records('assignsubmission_onenote', ['assignment' => $assign1->get_instance()->id]);
        $this->assertCount(1, $data);

        // Check that the second assignment has not been touched.
        $data = $DB->get_records('assignsubmission_onenote', ['assignment' => $assign2->get_instance()->id]);
        $this->assertCount(2, $data);

        $fs = new file_storage();
        // 6 including directories for assign 1.
        // 4 including directories for assign 2.
        $this->assertCount(2, $fs->get_area_files($assign1->get_context()->id, 'assignsubmission_onenote',
            base::ASSIGNSUBMISSION_ONENOTE_FILEAREA));
        $this->assertCount(4, $fs->get_area_files($assign2->get_context()->id, 'assignsubmission_onenote',
            base::ASSIGNSUBMISSION_ONENOTE_FILEAREA));
    }

    /**
     * Convenience function for creating submission data.
     *
     * @param object $assign Assign object
     * @param stdClass $student User object
     * @param string $submissiontext
     * @return array Submission plugin object and the submission object.
     */
    protected function create_onenote_submission($assign, $student, $submissiontext) {
        global $DB;

        $this->setUser($student->id);
        $submission = $assign->get_user_submission($student->id, true);
        $plugin = $assign->get_submission_plugin_by_type('onenote');

        // Prepare file record object.
        $fs = get_file_storage();
        $fileinfo = [
            'contextid' => $assign->get_context()->id,
            'component' => 'assignsubmission_onenote',
            'filearea' => base::ASSIGNSUBMISSION_ONENOTE_FILEAREA,
            'itemid' => $submission->id,
            'filepath' => '/',
            'filename' => 'OneNote_' . time() . '.zip',
        ];
        // Save it.
        $fs->create_file_from_string($fileinfo, $submissiontext);

        $filesubmission = new stdClass();
        $filesubmission->numfiles = $this->count_files($assign, $submission->id);
        $filesubmission->submission = $submission->id;
        $filesubmission->assignment = $assign->get_instance()->id;
        $filesubmission->id = $DB->insert_record('assignsubmission_onenote', $filesubmission);

        return [$plugin, $submission];
    }

    /**
     * Count the number of submission OneNote files
     *
     * @param object $assign Assign object
     * @param int $submissionid
     * @return int
     */
    private function count_files($assign, $submissionid) {
        $fs = get_file_storage();
        $files = $fs->get_area_files($assign->get_context()->id, 'assignsubmission_onenote',
            base::ASSIGNSUBMISSION_ONENOTE_FILEAREA, $submissionid, 'id', false);
        return count($files);
    }
}
