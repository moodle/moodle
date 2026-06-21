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

namespace assignfeedback_file;

use assignfeedback_file\feedback_helper_trait;
use mod_assign_test_generator;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/tests/generator.php');
require(__DIR__ . '/feedback_helper_trait.php');

/**
 * Unit tests for assignfeedback_file
 *
 * @package    assignfeedback_file
 * @copyright  2016 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \assign_feedback_file
 */
final class feedback_test extends \advanced_testcase {
    use feedback_helper_trait;
    use mod_assign_test_generator;

    /**
     * Test the is_feedback_modified() method for the file feedback.
     */
    public function test_is_feedback_modified(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $assign = $this->create_instance($course, [
                'assignsubmission_onlinetext_enabled' => 1,
                'assignfeedback_comments_enabled' => 1,
            ]);

        // Create an online text submission.
        $this->add_submission($student, $assign);

        $this->setUser($teacher);

        $fs = get_file_storage();
        $context = \context_user::instance($teacher->id);
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

        $file = $fs->create_file_from_string($dummy, 'This is the first feedback file');

        // Create formdata.
        $data = new \stdClass();
        $data->{'files_' . $student->id . '_filemanager'} = $draftitemid;

        $grade = $assign->get_user_grade($student->id, true);

        // This is the first time that we are submitting feedback, so it is modified.
        $plugin = $assign->get_feedback_plugin_by_type('file');
        $this->assertTrue($plugin->is_feedback_modified($grade, $data));
        // Save the feedback.
        $plugin->save($grade, $data);
        // Try again with the same data.
        $draftitemid = file_get_unused_draft_itemid();
        file_prepare_draft_area($draftitemid, $context->id, 'assignfeedback_file', 'feedback_files', 1);

        $dummy['itemid'] = $draftitemid;

        $file = $fs->create_file_from_string($dummy, 'This is the first feedback file');

        // Create formdata.
        $data = new \stdClass();
        $data->{'files_' . $student->id . '_filemanager'} = $draftitemid;

        $this->assertFalse($plugin->is_feedback_modified($grade, $data));

        // Same name for the file but different content.
        $draftitemid = file_get_unused_draft_itemid();
        file_prepare_draft_area($draftitemid, $context->id, 'assignfeedback_file', 'feedback_files', 1);

        $dummy['itemid'] = $draftitemid;

        $file = $fs->create_file_from_string($dummy, 'This is different feedback');

        // Create formdata.
        $data = new \stdClass();
        $data->{'files_' . $student->id . '_filemanager'} = $draftitemid;

        $this->assertTrue($plugin->is_feedback_modified($grade, $data));
        $plugin->save($grade, $data);

        // Add another file.
        $draftitemid = file_get_unused_draft_itemid();
        file_prepare_draft_area($draftitemid, $context->id, 'assignfeedback_file', 'feedback_files', 1);

        $dummy['itemid'] = $draftitemid;

        $file = $fs->create_file_from_string($dummy, 'This is different feedback');
        $dummy['filename'] = 'feedback2.txt';
        $file = $fs->create_file_from_string($dummy, 'A second feedback file');

        // Create formdata.
        $data = new \stdClass();
        $data->{'files_' . $student->id . '_filemanager'} = $draftitemid;

        $this->assertTrue($plugin->is_feedback_modified($grade, $data));
        $plugin->save($grade, $data);

        // Deleting a file.
        $draftitemid = file_get_unused_draft_itemid();
        file_prepare_draft_area($draftitemid, $context->id, 'assignfeedback_file', 'feedback_files', 1);

        $dummy['itemid'] = $draftitemid;

        $file = $fs->create_file_from_string($dummy, 'This is different feedback');

        // Create formdata.
        $data = new \stdClass();
        $data->{'files_' . $student->id . '_filemanager'} = $draftitemid;

        $this->assertTrue($plugin->is_feedback_modified($grade, $data));
        $plugin->save($grade, $data);

        // The file was moved to a folder.
        $draftitemid = file_get_unused_draft_itemid();
        file_prepare_draft_area($draftitemid, $context->id, 'assignfeedback_file', 'feedback_files', 1);

        $dummy['itemid'] = $draftitemid;
        $dummy['filepath'] = '/testdir/';

        $file = $fs->create_file_from_string($dummy, 'This is different feedback');

        // Create formdata.
        $data = new \stdClass();
        $data->{'files_' . $student->id . '_filemanager'} = $draftitemid;

        $this->assertTrue($plugin->is_feedback_modified($grade, $data));
        $plugin->save($grade, $data);

        // No modification to the file in the folder.
        $draftitemid = file_get_unused_draft_itemid();
        file_prepare_draft_area($draftitemid, $context->id, 'assignfeedback_file', 'feedback_files', 1);

        $dummy['itemid'] = $draftitemid;
        $dummy['filepath'] = '/testdir/';

        $file = $fs->create_file_from_string($dummy, 'This is different feedback');

        // Create formdata.
        $data = new \stdClass();
        $data->{'files_' . $student->id . '_filemanager'} = $draftitemid;

        $this->assertFalse($plugin->is_feedback_modified($grade, $data));
    }

    /**
     * Test feedback is in the mark.
     * @covers ::save
     */
    public function test_mark_feedback(): void {
        $this->resetAfterTest();

        // Create course and students.
        $course  = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create assignment.
        $assign = $this->create_instance($course, [
            'assignsubmission_onlinetext_enabled' => 1,
            'assignfeedback_file_enabled'         => 1,
            'markingworkflow'                     => 1,
            'markingallocation'                   => 1,
            'markercount'                         => 1,
            'multimarkmethod'                     => ASSIGN_MULTIMARKING_METHOD_MANUAL,
            'multimarkrounding'                   => null,
        ]);

        // Allocate teacher as marker.
        $assign->update_allocated_markers($student->id, [$teacher->id]);

        // Create feedback as mark.
        $commenttext = '<p>Comment for this test</p>';
        [$plugin, $grade, $file] = $this->create_feedback(
            $assign,
            $student,
            $teacher,
            'Submission text',
            $commenttext,
            true,
        );

        // Fetch values created from submitting marking feedback.
        $mark = $assign->get_mark($grade->id, $grade->grader);
        $filerecord = $plugin->get_file_feedback($grade->id, $mark->id);
        $fs = get_file_storage();
        $markfeedbackfile = $fs->get_file(
            $assign->get_context()->id,
            'assignfeedback_file',
            ASSIGNFEEDBACK_FILE_FILEAREA_MARKER,
            $filerecord->mark,
            $file->get_filepath(),
            $file->get_filename(),
        );

        // Test feedback values.
        $this->assertEquals($filerecord->numfiles, '1');
        $this->assertEquals(
            $markfeedbackfile->get_contenthash(),
            $file->get_contenthash(),
        );
    }
}
