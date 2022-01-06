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
 * Unit tests for assignfeedback_editpdfplus\comments_quick_list
 *
 * @package    assignfeedback_editpdfplus
 * @category   phpunit
 * @copyright  2016 Université de Lausanne
 * The code is based on mod/assign/feedback/editpdf/tests/editpdfplus_test.php by Damyon Wiese.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \assignfeedback_editpdfplus\document_services;
use \assignfeedback_editpdfplus\page_editor;
use \assignfeedback_editpdfplus\pdf;
use \assignfeedback_editpdfplus\bdd\annotation;

global $CFG;
require_once($CFG->dirroot . '/mod/assign/tests/generator.php');

class assignfeedback_editpdfplus_testcase extends mod_assign_base_testcase {

    protected function setUp() {
        // Skip this test if ghostscript is not supported.
        if (!pdf::test_gs_path(false)) {
            $this->markTestSkipped('Ghostscript not setup');
            return;
        }
        parent::setUp();
    }

    protected function create_assign_and_submit_pdf() {
        global $CFG;
        $assign = $this->create_instance(array('assignsubmission_onlinetext_enabled' => 1,
                                               'assignsubmission_file_enabled' => 1,
                                               'assignsubmission_file_maxfiles' => 1,
                                               'assignfeedback_editpdfplus_enabled' => 1,
                                               'assignsubmission_file_maxsizebytes' => 1000000));

        $user = $this->students[0];
        $this->setUser($user);

        // Create a file submission with the test pdf.
        $submission = $assign->get_user_submission($user->id, true);

        $fs = get_file_storage();
        $pdfsubmission = (object) array(
            'contextid' => $assign->get_context()->id,
            'component' => 'assignsubmission_file',
            'filearea' => ASSIGNSUBMISSION_FILE_FILEAREA,
            'itemid' => $submission->id,
            'filepath' => '/',
            'filename' => 'submission.pdf'
        );
        $sourcefile = $CFG->dirroot.'/mod/assign/feedback/editpdfplus/tests/fixtures/submission.pdf';
        $fi = $fs->create_file_from_pathname($pdfsubmission, $sourcefile);

        $data = new stdClass();
        $plugin = $assign->get_submission_plugin_by_type('file');
        $plugin->save($submission, $data);

        return $assign;
    }

    public function test_page_editor() {

        $assign = $this->create_assign_and_submit_pdf();
        $this->setUser($this->teachers[0]);

        $grade = $assign->get_user_grade($this->students[0]->id, true);

        $notempty = page_editor::has_annotations_or_comments($grade->id, false);
        $this->assertFalse($notempty);

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

        $notempty = page_editor::has_annotations_or_comments($grade->id, false);
        // Still empty because all edits are still drafts.
        $this->assertFalse($notempty);

        $annotations = page_editor::get_annotations($grade->id, 0, false);

        $this->assertEmpty($annotations);

        $annotations = page_editor::get_annotations($grade->id, 0, true);

        $this->assertEquals(count($annotations), 2);

        $annotation = reset($annotations);

        page_editor::remove_annotation($annotation->id);

        $annotations = page_editor::get_annotations($grade->id, 0, true);

        $this->assertEquals(count($annotations), 1);

        page_editor::release_drafts($grade->id);

        $notempty = page_editor::has_annotations_or_comments($grade->id, false);

        $this->assertTrue($notempty);

        page_editor::unrelease_drafts($grade->id);

        $notempty = page_editor::has_annotations_or_comments($grade->id, false);

        $this->assertFalse($notempty);
    }

    public function test_document_services() {

        $assign = $this->create_assign_and_submit_pdf();
        $this->setUser($this->teachers[0]);

        $grade = $assign->get_user_grade($this->students[0]->id, true);

        $notempty = page_editor::has_annotations_or_comments($grade->id, false);
        $this->assertFalse($notempty);

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

        $file = document_services::generate_feedback_document($assign->get_instance()->id, $grade->userid, $grade->attemptnumber);
        $this->assertNotEmpty($file);

        $file2 = document_services::get_feedback_document($assign->get_instance()->id, $grade->userid, $grade->attemptnumber);

        $this->assertEquals($file, $file2);

        document_services::delete_feedback_document($assign->get_instance()->id, $grade->userid, $grade->attemptnumber);
        $file3 = document_services::get_feedback_document($assign->get_instance()->id, $grade->userid, $grade->attemptnumber);

        $this->assertEmpty($file3);
    }

    /**
     * Test that modifying the annotated pdf form return true when modified
     * and false when not modified.
     */
    public function test_is_feedback_modified() {
        global $DB;
        $assign = $this->create_assign_and_submit_pdf();
        $this->setUser($this->teachers[0]);

        $grade = $assign->get_user_grade($this->students[0]->id, true);

        $notempty = page_editor::has_annotations_or_comments($grade->id, false);
        $this->assertFalse($notempty);

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

        $plugin = $assign->get_feedback_plugin_by_type('editpdf');
        $data = new stdClass();
        $data->editpdfplus_source_userid = $this->students[0]->id;
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

        $this->assertTrue($plugin->is_feedback_modified($grade, $data));
        $plugin->save($grade, $data);

        // We should have two annotations.
        $this->assertCount(2, page_editor::get_annotations($grade->id, 0, false));

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
        // No modification.
        $this->assertFalse($plugin->is_feedback_modified($grade, $data));
    }
}
