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
 * Privacy test assignfeedback_onenote.
 *
 * @package    assignfeedback_onenote
 * @copyright  Microsoft, Inc.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignfeedback_onenote\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;
use file_storage;
use local_onenote\api\base;
use mod_assign\privacy\assign_plugin_request_data;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once($CFG->libdir . '/filestorage/file_storage.php');
require_once($CFG->dirroot . '/mod/assign/tests/privacy/provider_test.php');

/**
 * Unit tests for mod/assign/feedback/onenote/classes/privacy/
 *
 * @copyright  Microsoft, Inc.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \assignfeedback_onenote\privacy\provider
 */
final class provider_test extends \mod_assign\tests\provider_testcase {

    /**
     * Convenience function for creating feedback data.
     *
     * @param object $assign assign object
     * @param stdClass $student user object
     * @param stdClass $teacher user object
     * @return array   Feedback plugin object and the grade object.
     */
    protected function create_feedback($assign, $student, $teacher) {
        $this->setUser($teacher);
        $plugin = $assign->get_feedback_plugin_by_type('onenote');
        $grade = $assign->get_user_grade($student->id, true);

        // Prepare file record object.
        $fs = get_file_storage();
        $fileinfo = [
            'contextid' => $assign->get_context()->id,
            'component' => 'assignfeedback_onenote',
            'filearea' => base::ASSIGNFEEDBACK_ONENOTE_FILEAREA,
            'itemid' => $grade->id, 'filepath' => '/',
            'filename' => 'OneNote_' . time() . '.zip',
        ];

        // Save it.
        $fs->create_file_from_string($fileinfo, 'test123');
        $plugin->update_file_count($grade);

        return [$plugin, $grade];
    }

    /**
     * Quick test to make sure that get_metadata returns something.
     *
     * @covers \assignfeedback_onenote\privacy\provider::get_metadata
     */
    public function test_get_metadata(): void {
        $collection = new collection('assignfeedback_onenote');
        $collection = provider::get_metadata($collection);
        $this->assertNotEmpty($collection);
    }

    /**
     * Test that feedback comments are exported for a user.
     *
     * @covers \assignfeedback_onenote\privacy\provider::export_feedback_user_data
     */
    public function test_export_feedback_user_data(): void {
        $this->resetAfterTest();

        // Create course, assignment, submission, and then a feedback onenote.
        $course = $this->getDataGenerator()->create_course();
        // Student.
        $user1 = $this->getDataGenerator()->create_user();
        // Teacher.
        $user2 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'editingteacher');
        $assign = $this->create_instance(['course' => $course]);

        $context = $assign->get_context();

        [$plugin, $grade] = $this->create_feedback($assign, $user1, $user2);

        $writer = writer::with_context($context);
        $this->assertFalse($writer->has_any_data());

        // The student should be able to see the teachers' feedback.
        $exportdata = new assign_plugin_request_data($context, $assign, $grade, [], $user1);
        provider::export_feedback_user_data($exportdata);
        $exporteddata = $writer->get_data([get_string('privacy:path', 'assignfeedback_onenote')]);
        $this->assertEquals($grade->assignment, $exporteddata->assignment);
        $this->assertEquals($grade->id, $exporteddata->grade);
        $this->assertEquals('1', $exporteddata->numfiles);

        // The teacher should also be able to see the feedback that they provided.
        $exportdata = new assign_plugin_request_data($context, $assign, $grade, [], $user2);
        provider::export_feedback_user_data($exportdata);
        $exporteddata = $writer->get_data([get_string('privacy:path', 'assignfeedback_onenote')]);
        $this->assertEquals($grade->assignment, $exporteddata->assignment);
        $this->assertEquals($grade->id, $exporteddata->grade);
        $this->assertEquals('1', $exporteddata->numfiles);

        $filespath = [get_string('privacy:path', 'assignfeedback_onenote')];
        $feedbackfiles = $writer->get_files($filespath);
        $this->assertEquals(1, count($feedbackfiles));
        $feedbackfile = array_shift($feedbackfiles);
        $this->assertInstanceOf('stored_file', $feedbackfile);
        $this->assertTrue(strpos($feedbackfile->get_filename(), 'OneNote_') === 0);
    }

    /**
     * Test that all feedback is deleted for a context.
     *
     * @covers \assignfeedback_onenote\privacy\provider::delete_feedback_for_context
     */
    public function test_delete_feedback_for_context(): void {
        $this->resetAfterTest();
        // Create course, assignment, submission, and then a feedback onenote.
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

        [$plugin1, $grade1] = $this->create_feedback($assign, $user1, $user3);
        [$plugin2, $grade2] = $this->create_feedback($assign, $user2, $user3);

        // Check that we have data.
        $feedbackonenotes = $plugin1->get_onenote_feedback($grade1->id);
        $this->assertNotEmpty($feedbackonenotes);
        $feedbackonenotes = $plugin1->get_onenote_feedback($grade2->id);
        $this->assertNotEmpty($feedbackonenotes);

        $fs = new file_storage();
        $files = $fs->get_area_files($assign->get_context()->id, 'assignfeedback_onenote', base::ASSIGNFEEDBACK_ONENOTE_FILEAREA);
        // 4 including directories.
        $this->assertEquals(4, count($files));

        // Delete all entries for this context.
        $requestdata = new assign_plugin_request_data($context, $assign);
        provider::delete_feedback_for_context($requestdata);

        // Check that the data is now gone.
        $feedbackonenotes = $plugin1->get_onenote_feedback($grade1->id);
        $this->assertEmpty($feedbackonenotes);
        $feedbackonenotes = $plugin1->get_onenote_feedback($grade2->id);
        $this->assertEmpty($feedbackonenotes);

        $fs = new file_storage();
        $files = $fs->get_area_files($assign->get_context()->id, 'assignfeedback_onenote', base::ASSIGNFEEDBACK_ONENOTE_FILEAREA);
        $this->assertEquals(0, count($files));
    }

    /**
     * Test that a grade item is deleted for a user.
     *
     * @covers \assignfeedback_onenote\privacy\provider::delete_feedback_for_grade
     */
    public function test_delete_feedback_for_grade(): void {
        $this->resetAfterTest();
        // Create course, assignment, submission, and then a feedback onenote.
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

        [$plugin1, $grade1] = $this->create_feedback($assign, $user1, $user3);
        [$plugin2, $grade2] = $this->create_feedback($assign, $user2, $user3);

        // Check that we have data.
        $feedbackonenotes = $plugin1->get_onenote_feedback($grade1->id);
        $this->assertNotEmpty($feedbackonenotes);
        $feedbackonenotes = $plugin1->get_onenote_feedback($grade2->id);
        $this->assertNotEmpty($feedbackonenotes);

        $fs = new file_storage();
        $files = $fs->get_area_files($assign->get_context()->id, 'assignfeedback_onenote', base::ASSIGNFEEDBACK_ONENOTE_FILEAREA);
        // 4 including directories.
        $this->assertEquals(4, count($files));

        // Delete all entries for this grade object.
        $requestdata = new assign_plugin_request_data($context, $assign, $grade1, [], $user1);
        provider::delete_feedback_for_grade($requestdata);

        // These entries should be gone.
        $feedbackonenotes = $plugin1->get_onenote_feedback($grade1->id);
        $this->assertEmpty($feedbackonenotes);
        // These entries should not.
        $feedbackonenotes = $plugin1->get_onenote_feedback($grade2->id);
        $this->assertNotEmpty($feedbackonenotes);

        $fs = new file_storage();
        $files = $fs->get_area_files($assign->get_context()->id, 'assignfeedback_onenote', base::ASSIGNFEEDBACK_ONENOTE_FILEAREA);
        // 2 files that were not deleted.
        $this->assertEquals(2, count($files));

        array_shift($files);
        $file = array_shift($files);

        $this->assertInstanceOf('stored_file', $file);
        $this->assertTrue(strpos($file->get_filename(), 'OneNote_') === 0);
        $this->assertEquals($grade2->id, $file->get_itemid());
    }

    /**
     * Test that a grade item is deleted for a user.
     *
     * @covers \assignfeedback_onenote\privacy\provider::delete_feedback_for_grades
     */
    public function test_delete_feedback_for_grades(): void {
        $this->resetAfterTest();
        // Create course, assignment, submission, and then a feedback onenote.
        $course = $this->getDataGenerator()->create_course();
        // Students.
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

        $context = $assign1->get_context();

        [$plugin1, $grade1] = $this->create_feedback($assign1, $user1, $user5);
        [$plugin2, $grade2] = $this->create_feedback($assign1, $user2, $user5);
        [$plugin3, $grade3] = $this->create_feedback($assign1, $user3, $user5);
        [$plugin4, $grade4] = $this->create_feedback($assign2, $user3, $user5);
        [$plugin5, $grade5] = $this->create_feedback($assign2, $user4, $user5);

        // Check that we have data.
        $feedbackonenotes = $plugin1->get_onenote_feedback($grade1->id);
        $this->assertNotEmpty($feedbackonenotes);
        $feedbackonenotes = $plugin2->get_onenote_feedback($grade2->id);
        $this->assertNotEmpty($feedbackonenotes);
        $feedbackonenotes = $plugin3->get_onenote_feedback($grade3->id);
        $this->assertNotEmpty($feedbackonenotes);
        $feedbackonenotes = $plugin4->get_onenote_feedback($grade4->id);
        $this->assertNotEmpty($feedbackonenotes);
        $feedbackonenotes = $plugin5->get_onenote_feedback($grade5->id);
        $this->assertNotEmpty($feedbackonenotes);

        $fs = new file_storage();
        // 6 including directories for assign 1.
        // 4 including directories for assign 2.
        $this->assertCount(6, $fs->get_area_files($assign1->get_context()->id, 'assignfeedback_onenote',
            base::ASSIGNFEEDBACK_ONENOTE_FILEAREA));
        $this->assertCount(4, $fs->get_area_files($assign2->get_context()->id, 'assignfeedback_onenote',
            base::ASSIGNFEEDBACK_ONENOTE_FILEAREA));

        $deletedata = new assign_plugin_request_data($context, $assign1);
        $deletedata->set_userids([$user1->id, $user3->id]);
        $deletedata->populate_submissions_and_grades();
        provider::delete_feedback_for_grades($deletedata);

        // Check that grade 1 and grade 3 have been removed.
        $feedbackonenotes = $plugin1->get_onenote_feedback($grade1->id);
        $this->assertEmpty($feedbackonenotes);
        $feedbackonenotes = $plugin2->get_onenote_feedback($grade2->id);
        $this->assertNotEmpty($feedbackonenotes);
        $feedbackonenotes = $plugin3->get_onenote_feedback($grade3->id);
        $this->assertEmpty($feedbackonenotes);
        $feedbackonenotes = $plugin4->get_onenote_feedback($grade4->id);
        $this->assertNotEmpty($feedbackonenotes);
        $feedbackonenotes = $plugin5->get_onenote_feedback($grade5->id);
        $this->assertNotEmpty($feedbackonenotes);

        // We have deleted two from assign 1, and none from assign 2.
        // 2 including directories for assign 1.
        // 4 including directories for assign 2.
        $this->assertCount(2, $fs->get_area_files($assign1->get_context()->id, 'assignfeedback_onenote',
            base::ASSIGNFEEDBACK_ONENOTE_FILEAREA));
        $this->assertCount(4, $fs->get_area_files($assign2->get_context()->id, 'assignfeedback_onenote',
            base::ASSIGNFEEDBACK_ONENOTE_FILEAREA));
    }
}
