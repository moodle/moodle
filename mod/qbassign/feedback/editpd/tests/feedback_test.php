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

namespace qbassignfeedback_editpd;

use mod_qbassign_test_generator;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/qbassign/tests/generator.php');

/**
 * Unit tests for qbassignfeedback_editpd\comments_quick_list
 *
 * @package    qbassignfeedback_editpd
 * @category   test
 * @copyright  2013 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class feedback_test extends \advanced_testcase {

    // Use the generator helper.
    use mod_qbassign_test_generator;

    /**
     * Ensure that GS is available.
     */
    protected function require_ghostscript() {
        // Skip this test if ghostscript is not supported.
        $result = pdf::test_gs_path(false);
        if ($result->status !== pdf::GSPATH_OK) {
            $this->markTestSkipped('Ghostscript not setup');
        }
    }

    /**
     * Helper method to add a file to a submission.
     *
     * @param stdClass $student Student submitting.
     * @param qbassign   $qbassign qbassignment being submitted.
     * @param bool     $textfile Use textfile fixture instead of pdf.
     */
    protected function add_file_submission($student, $qbassign, $textfile = false) {
        global $CFG;

        $this->setUser($student);

        // Create a file submission with the test pdf.
        $submission = $qbassign->get_user_submission($student->id, true);

        $fs = get_file_storage();
        $filerecord = (object) array(
            'contextid' => $qbassign->get_context()->id,
            'component' => 'qbassignsubmission_file',
            'filearea' => qbassignSUBMISSION_FILE_FILEAREA,
            'itemid' => $submission->id,
            'filepath' => '/',
            'filename' => $textfile ? 'submission.txt' : 'submission.pdf'
        );
        $sourcefile = $CFG->dirroot . '/mod/qbassign/feedback/editpd/tests/fixtures/submission.' . ($textfile ? 'txt' : 'pdf');
        $fs->create_file_from_pathname($filerecord, $sourcefile);

        $data = new \stdClass();
        $plugin = $qbassign->get_submission_plugin_by_type('file');
        $plugin->save($submission, $data);
    }

    public function test_comments_quick_list() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $this->setUser($teacher);

        $this->assertEmpty(comments_quick_list::get_comments());

        $comment = comments_quick_list::add_comment('test', 45, 'red');
        $comments = comments_quick_list::get_comments();
        $this->assertEquals(count($comments), 1);
        $first = reset($comments);
        $this->assertEquals($comment, $first);

        $commentbyid = comments_quick_list::get_comment($comment->id);
        $this->assertEquals($comment, $commentbyid);

        $this->assertTrue(comments_quick_list::remove_comment($comment->id));

        $comments = comments_quick_list::get_comments();
        $this->assertEmpty($comments);
    }

    public function test_page_editor() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $qbassign = $this->create_instance($course, [
                'qbassignsubmission_onlinetex_enabled' => 1,
                'qbassignsubmission_file_enabled' => 1,
                'qbassignsubmission_file_maxfiles' => 1,
                'qbassignfeedback_editpd_enabled' => 1,
                'qbassignsubmission_file_maxsizebytes' => 1000000,
            ]);

        // Add the standard submission.
        $this->add_file_submission($student, $qbassign);

        $this->setUser($teacher);

        $grade = $qbassign->get_user_grade($student->id, true);

        $notempty = page_editor::has_annotations_or_comments($grade->id, false);
        $this->assertFalse($notempty);

        $comment = new comment();
        $comment->rawtext = 'Comment text';
        $comment->width = 100;
        $comment->x = 100;
        $comment->y = 100;
        $comment->colour = 'red';

        $comment2 = new comment();
        $comment2->rawtext = 'Comment text 2';
        $comment2->width = 100;
        $comment2->x = 200;
        $comment2->y = 100;
        $comment2->colour = 'clear';

        page_editor::set_comments($grade->id, 0, array($comment, $comment2));

        $annotation = new annotation();
        $annotation->path = '';
        $annotation->x = 100;
        $annotation->y = 100;
        $annotation->endx = 200;
        $annotation->endy = 200;
        $annotation->type = 'line';
        $annotation->colour = 'red';

        $annotation2 = new annotation();
        $annotation2->path = '';
        $annotation2->x = 100;
        $annotation2->y = 100;
        $annotation2->endx = 200;
        $annotation2->endy = 200;
        $annotation2->type = 'rectangle';
        $annotation2->colour = 'yellow';

        page_editor::set_annotations($grade->id, 0, array($annotation, $annotation2));

        // Still empty because all edits are still drafts.
        $this->assertFalse(page_editor::has_annotations_or_comments($grade->id, false));

        $comments = page_editor::get_comments($grade->id, 0, false);
        $this->assertEmpty($comments);

        $comments = page_editor::get_comments($grade->id, 0, true);
        $this->assertEquals(count($comments), 2);

        $annotations = page_editor::get_annotations($grade->id, 0, false);
        $this->assertEmpty($annotations);

        $annotations = page_editor::get_annotations($grade->id, 0, true);
        $this->assertEquals(count($annotations), 2);

        $comment = reset($comments);
        $annotation = reset($annotations);

        page_editor::remove_comment($comment->id);
        page_editor::remove_annotation($annotation->id);

        $comments = page_editor::get_comments($grade->id, 0, true);
        $this->assertEquals(count($comments), 1);

        $annotations = page_editor::get_annotations($grade->id, 0, true);
        $this->assertEquals(count($annotations), 1);

        // Release the drafts.
        page_editor::release_drafts($grade->id);

        $notempty = page_editor::has_annotations_or_comments($grade->id, false);
        $this->assertTrue($notempty);

        // Unrelease the drafts.
        page_editor::unrelease_drafts($grade->id);

        $notempty = page_editor::has_annotations_or_comments($grade->id, false);
        $this->assertFalse($notempty);
    }

    public function test_document_services() {
        $this->require_ghostscript();
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $qbassign = $this->create_instance($course, [
                'qbassignsubmission_onlinetex_enabled' => 1,
                'qbassignsubmission_file_enabled' => 1,
                'qbassignsubmission_file_maxfiles' => 1,
                'qbassignfeedback_editpd_enabled' => 1,
                'qbassignsubmission_file_maxsizebytes' => 1000000,
            ]);

        // Add the standard submission.
        $this->add_file_submission($student, $qbassign);

        $this->setUser($teacher);

        $grade = $qbassign->get_user_grade($student->id, true);

        $contextid = $qbassign->get_context()->id;
        $component = 'qbassignfeedback_editpd';
        $filearea = document_services::COMBINED_PDF_FILEAREA;
        $itemid = $grade->id;
        $filepath = '/';
        $filename = document_services::COMBINED_PDF_FILENAME;
        $fs = \get_file_storage();

        // Generate a blank combined pdf.
        $record = new \stdClass();
        $record->contextid = $contextid;
        $record->component = $component;
        $record->filearea = $filearea;
        $record->itemid = $itemid;
        $record->filepath = $filepath;
        $record->filename = $filename;
        $fs->create_file_from_string($record, base64_decode(document_services::BLANK_PDF_BASE64));

        // Verify that the blank combined pdf has the expected hash.
        $combinedpdf = $fs->get_file($contextid, $component, $filearea, $itemid, $filepath, $filename);
        $this->assertEquals($combinedpdf->get_contenthash(), document_services::BLANK_PDF_HASH);

        // Generate page images and verify that the combined pdf has been replaced.
        document_services::get_page_images_for_attempt($qbassign, $student->id, -1);
        $combinedpdf = $fs->get_file($contextid, $component, $filearea, $itemid, $filepath, $filename);
        $this->assertNotEquals($combinedpdf->get_contenthash(), document_services::BLANK_PDF_HASH);

        $notempty = page_editor::has_annotations_or_comments($grade->id, false);
        $this->assertFalse($notempty);

        $comment = new comment();

        // Use some different charset in the comment text.
        $comment->rawtext = 'Testing example: בקלות ואמנות';
        $comment->width = 100;
        $comment->x = 100;
        $comment->y = 100;
        $comment->colour = 'red';

        page_editor::set_comments($grade->id, 0, array($comment));

        $annotations = array();

        $annotation = new annotation();
        $annotation->path = '';
        $annotation->x = 100;
        $annotation->y = 100;
        $annotation->endx = 200;
        $annotation->endy = 200;
        $annotation->type = 'line';
        $annotation->colour = 'red';
        array_push($annotations, $annotation);

        $annotation = new annotation();
        $annotation->path = '';
        $annotation->x = 100;
        $annotation->y = 100;
        $annotation->endx = 200;
        $annotation->endy = 200;
        $annotation->type = 'rectangle';
        $annotation->colour = 'yellow';
        array_push($annotations, $annotation);

        $annotation = new annotation();
        $annotation->path = '';
        $annotation->x = 100;
        $annotation->y = 100;
        $annotation->endx = 200;
        $annotation->endy = 200;
        $annotation->type = 'oval';
        $annotation->colour = 'green';
        array_push($annotations, $annotation);

        $annotation = new annotation();
        $annotation->path = '';
        $annotation->x = 100;
        $annotation->y = 100;
        $annotation->endx = 200;
        $annotation->endy = 116;
        $annotation->type = 'highlight';
        $annotation->colour = 'blue';
        array_push($annotations, $annotation);

        $annotation = new annotation();
        $annotation->path = '100,100:105,105:110,100';
        $annotation->x = 100;
        $annotation->y = 100;
        $annotation->endx = 110;
        $annotation->endy = 105;
        $annotation->type = 'pen';
        $annotation->colour = 'black';
        array_push($annotations, $annotation);
        page_editor::set_annotations($grade->id, 0, $annotations);

        page_editor::release_drafts($grade->id);

        $notempty = page_editor::has_annotations_or_comments($grade->id, false);

        $this->assertTrue($notempty);

        $file = document_services::generate_feedback_document($qbassign->get_instance()->id, $grade->userid, $grade->attemptnumber);
        $this->assertNotEmpty($file);

        $file2 = document_services::get_feedback_document($qbassign->get_instance()->id, $grade->userid, $grade->attemptnumber);

        $this->assertEquals($file, $file2);

        document_services::delete_feedback_document($qbassign->get_instance()->id, $grade->userid, $grade->attemptnumber);
        $file3 = document_services::get_feedback_document($qbassign->get_instance()->id, $grade->userid, $grade->attemptnumber);

        $this->assertEmpty($file3);
    }

    /**
     * Test Convert submission ad-hoc task.
     *
     * @covers \qbassignfeedback_editpd\task\convert_submission
     */
    public function test_conversion_task() {
        $this->require_ghostscript();
        $this->resetAfterTest();
        cron_setup_user();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $qbassignopts = [
            'qbassignsubmission_file_enabled' => 1,
            'qbassignsubmission_file_maxfiles' => 1,
            'qbassignfeedback_editpd_enabled' => 1,
            'qbassignsubmission_file_maxsizebytes' => 1000000,
        ];
        $qbassign = $this->create_instance($course, $qbassignopts);

        // Add the standard submission.
        $this->add_file_submission($student, $qbassign);

        // Run the conversion task.
        $task = \core\task\manager::get_next_adhoc_task(time());
        ob_start();
        $task->execute();
        \core\task\manager::adhoc_task_complete($task);
        $output = ob_get_clean();

        // Confirm, that submission has been converted and the task queue is now empty.
        $this->assertStringContainsString('Converting submission for user id ' . $student->id, $output);
        $this->assertStringContainsString('The document has been successfully converted', $output);
        $this->assertNull(\core\task\manager::get_next_adhoc_task(time()));

        // Trigger a re-queue by 'updating' a submission.
        $submission = $qbassign->get_user_submission($student->id, true);
        $plugin = $qbassign->get_submission_plugin_by_type('file');
        $plugin->save($submission, (new \stdClass));

        $task = \core\task\manager::get_next_adhoc_task(time());
        // Verify that queued a conversion task.
        $this->assertNotNull($task);

        ob_start();
        $task->execute();
        \core\task\manager::adhoc_task_complete($task);
        $output = ob_get_clean();

        // Confirm, that submission has been converted and the task queue is now empty.
        $this->assertStringContainsString('Converting submission for user id ' . $student->id, $output);
        $this->assertStringContainsString('The document has been successfully converted', $output);
        $this->assertNull(\core\task\manager::get_next_adhoc_task(time()));
    }

    /**
     * Test that modifying the annotated pdf form return true when modified
     * and false when not modified.
     */
    public function test_is_feedback_modified() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $qbassign = $this->create_instance($course, [
                'qbassignsubmission_onlinetex_enabled' => 1,
                'qbassignsubmission_file_enabled' => 1,
                'qbassignsubmission_file_maxfiles' => 1,
                'qbassignfeedback_editpd_enabled' => 1,
                'qbassignsubmission_file_maxsizebytes' => 1000000,
            ]);

        // Add the standard submission.
        $this->add_file_submission($student, $qbassign);

        $this->setUser($teacher);
        $grade = $qbassign->get_user_grade($student->id, true);

        $notempty = page_editor::has_annotations_or_comments($grade->id, false);
        $this->assertFalse($notempty);

        $comment = new comment();

        $comment->rawtext = 'Comment text';
        $comment->width = 100;
        $comment->x = 100;
        $comment->y = 100;
        $comment->colour = 'red';

        page_editor::set_comments($grade->id, 0, array($comment));

        $annotations = array();

        $annotation = new annotation();
        $annotation->path = '';
        $annotation->x = 100;
        $annotation->y = 100;
        $annotation->endx = 200;
        $annotation->endy = 200;
        $annotation->type = 'line';
        $annotation->colour = 'red';
        array_push($annotations, $annotation);

        page_editor::set_annotations($grade->id, 0, $annotations);

        $plugin = $qbassign->get_feedback_plugin_by_type('editpd');
        $data = new \stdClass();
        $data->editpd_source_userid = $student->id;
        $this->assertTrue($plugin->is_feedback_modified($grade, $data));
        $plugin->save($grade, $data);

        $annotation = new annotation();
        $annotation->gradeid = $grade->id;
        $annotation->pageno = 0;
        $annotation->path = '';
        $annotation->x = 100;
        $annotation->y = 100;
        $annotation->endx = 200;
        $annotation->endy = 200;
        $annotation->type = 'rectangle';
        $annotation->colour = 'yellow';

        $yellowannotationid = page_editor::add_annotation($annotation);

        // Add a comment as well.
        $comment = new comment();
        $comment->gradeid = $grade->id;
        $comment->pageno = 0;
        $comment->rawtext = 'Second Comment text';
        $comment->width = 100;
        $comment->x = 100;
        $comment->y = 100;
        $comment->colour = 'red';
        page_editor::add_comment($comment);

        $this->assertTrue($plugin->is_feedback_modified($grade, $data));
        $plugin->save($grade, $data);

        // We should have two annotations.
        $this->assertCount(2, page_editor::get_annotations($grade->id, 0, false));
        // And two comments.
        $this->assertCount(2, page_editor::get_comments($grade->id, 0, false));

        // Add one annotation and delete another.
        $annotation = new annotation();
        $annotation->gradeid = $grade->id;
        $annotation->pageno = 0;
        $annotation->path = '100,100:105,105:110,100';
        $annotation->x = 100;
        $annotation->y = 100;
        $annotation->endx = 110;
        $annotation->endy = 105;
        $annotation->type = 'pen';
        $annotation->colour = 'black';
        page_editor::add_annotation($annotation);

        $annotations = page_editor::get_annotations($grade->id, 0, true);
        page_editor::remove_annotation($yellowannotationid);
        $this->assertTrue($plugin->is_feedback_modified($grade, $data));
        $plugin->save($grade, $data);

        // We should have two annotations.
        $this->assertCount(2, page_editor::get_annotations($grade->id, 0, false));
        // And two comments.
        $this->assertCount(2, page_editor::get_comments($grade->id, 0, false));

        // Add a comment and then remove it. Should not be considered as modified.
        $comment = new comment();
        $comment->gradeid = $grade->id;
        $comment->pageno = 0;
        $comment->rawtext = 'Third Comment text';
        $comment->width = 400;
        $comment->x = 57;
        $comment->y = 205;
        $comment->colour = 'black';
        $comment->id = page_editor::add_comment($comment);

        // We should now have three comments.
        $this->assertCount(3, page_editor::get_comments($grade->id, 0, true));
        // Now delete the newest record.
        page_editor::remove_comment($comment->id);
        // Back to two comments.
        $this->assertCount(2, page_editor::get_comments($grade->id, 0, true));
        // No modification.
        $this->assertFalse($plugin->is_feedback_modified($grade, $data));
    }

    /**
     * Test that overwriting a submission file deletes any associated conversions.
     *
     * @covers \core_files\conversion::get_conversions_for_file
     */
    public function test_submission_file_overridden() {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $qbassign = $this->create_instance($course, [
            'qbassignsubmission_onlinetex_enabled' => 1,
            'qbassignsubmission_file_enabled' => 1,
            'qbassignsubmission_file_maxfiles' => 1,
            'qbassignfeedback_editpd_enabled' => 1,
            'qbassignsubmission_file_maxsizebytes' => 1000000,
        ]);

        $this->add_file_submission($student, $qbassign, true);
        $submission = $qbassign->get_user_submission($student->id, true);

        $fs = get_file_storage();
        $sourcefile = $fs->get_file(
            $qbassign->get_context()->id,
            'qbassignsubmission_file',
            qbassignSUBMISSION_FILE_FILEAREA,
            $submission->id,
            '/',
            'submission.txt'
        );

        $conversion = new \core_files\conversion(0, (object)[
            'sourcefileid' => $sourcefile->get_id(),
            'targetformat' => 'pdf'
        ]);
        $conversion->create();

        $conversions = \core_files\conversion::get_conversions_for_file($sourcefile, 'pdf');
        $this->assertCount(1, $conversions);

        $filerecord = (object)[
            'contextid' => $qbassign->get_context()->id,
            'component' => 'core',
            'filearea'  => 'unittest',
            'itemid'    => $submission->id,
            'filepath'  => '/',
            'filename'  => 'submission.txt'
        ];

        $fs = get_file_storage();
        $newfile = $fs->create_file_from_string($filerecord, 'something totally different');
        $sourcefile->replace_file_with($newfile);

        $conversions = \core_files\conversion::get_conversions_for_file($sourcefile, 'pdf');
        $this->assertCount(0, $conversions);
    }
}
