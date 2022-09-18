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

namespace qbank_customfields;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * Class qbank_customfields_customfield_testcase
 *
 * @package     qbank_customfields
 * @copyright   2021 Catalyst IT Australia Pty Ltd
 * @author      Matt Porritt <mattp@catalyst-au.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class customfield_test extends \advanced_testcase {

    /**
     * @var array Data object for generating a question.
     */
    protected $question1data;

    /**
     * @var array Data object for generating a question.
     */
    protected $question2data;

    /**
     * @var component_generator_base Question Generator.
     */
    protected $qgen;

    /**
     * @var core_course_category Course category.
     */
    protected $category;

    /**
     * @var stdClass Course object.
     */
    protected $course;

    /**
     * @var int Timestamp to use in tests.
     */
    protected $testnow = 1632278491;

    /**
     * Helper to assist with setting up custom fields.
     * This is creating custom field category and the fields, not adding instance field data.
     */
    protected function setup_custom_fields(): void {

        $dg = self::getDataGenerator();
        $data = new \stdClass();
        $data->component = 'qbank_customfields';
        $data->area = 'question';

        $catid = $dg->create_custom_field_category($data)->get('id');
        $dg->create_custom_field(['categoryid' => $catid, 'type' => 'text', 'shortname' => 'f1']);
        $dg->create_custom_field(['categoryid' => $catid, 'type' => 'checkbox', 'shortname' => 'f2']);
        $dg->create_custom_field(['categoryid' => $catid, 'type' => 'date', 'shortname' => 'f3',
                'configdata' => ['startyear' => 2000, 'endyear' => 3000, 'includetime' => 1]]);
        $dg->create_custom_field(['categoryid' => $catid, 'type' => 'select', 'shortname' => 'f4',
                'configdata' => ['options' => "a\nb\nc"]]);
        $dg->create_custom_field(['categoryid' => $catid, 'type' => 'textarea', 'shortname' => 'f5']);

    }

    /**
     * Helper to assist with setting up questions used in tests.
     */
    protected function setup_questions(): void {
        // Question initial set up.
        $this->category = $this->getDataGenerator()->create_category();
        $this->course = $this->getDataGenerator()->create_course(['category' => $this->category->id]);
        $context = \context_coursecat::instance($this->category->id);
        $this->qgen = $this->getDataGenerator()->get_plugin_generator('core_question');
        $qcat = $this->qgen->create_question_category(['contextid' => $context->id]);

        $this->question1data = [
                'category' => $qcat->id, 'idnumber' => 'q1',
                'customfield_f1' => 'some text', 'customfield_f2' => 1,
                'customfield_f3' => $this->testnow, 'customfield_f4' => 2,
                'customfield_f5_editor' => ['text' => 'test', 'format' => FORMAT_HTML]];

        $this->question2data = [
                'category' => $qcat->id, 'idnumber' => 'q2',
                'customfield_f1' => 'some more text', 'customfield_f2' => 0,
                'customfield_f3' => $this->testnow, 'customfield_f4' => 1,
                'customfield_f5_editor' => ['text' => 'test text', 'format' => FORMAT_HTML]];
    }

    /**
     * Makes a backup of the course.
     *
     * @param \stdClass $course The course object.
     * @return string Unique identifier for this backup.
     */
    protected function backup_course(\stdClass $course): string {
        global $CFG, $USER;

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = \backup::LOG_NONE;

        // Do backup with default settings. MODE_IMPORT means it will just
        // create the directory and not zip it.
        $bc = new \backup_controller(\backup::TYPE_1COURSE, $course->id,
                \backup::FORMAT_MOODLE, \backup::INTERACTIVE_NO, \backup::MODE_IMPORT,
                $USER->id);
        $backupid = $bc->get_backupid();
        $bc->execute_plan();
        $bc->destroy();

        return $backupid;
    }

    /**
     * Restores a backup that has been made earlier.
     *
     * @param string $backupid The unique identifier of the backup.
     * @param string $fullname Full name of the new course that is going to be created.
     * @param string $shortname Short name of the new course that is going to be created.
     * @param int $categoryid The course category the backup is going to be restored in.
     * @return int The new course id.
     */
    protected function restore_course(string $backupid, string $fullname, string $shortname, int $categoryid): int {
        global $CFG, $USER;

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = \backup::LOG_NONE;

        // Do restore to new course with default settings.
        $newcourseid = \restore_dbops::create_new_course($fullname, $shortname, $categoryid);
        $rc = new \restore_controller($backupid, $newcourseid,
                \backup::INTERACTIVE_NO, \backup::MODE_GENERAL, $USER->id,
                \backup::TARGET_NEW_COURSE);

        $rc->execute_precheck();
        $rc->execute_plan();
        $rc->destroy();

        return $newcourseid;
    }

    /**
     * Test creating questions with custom fields.
     */
    public function test_create_question(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->setup_custom_fields();
        $this->setup_questions();

        // Create 2 questions.
        $question1 = $this->qgen->create_question('shortanswer', null, $this->question1data);
        $question2 = $this->qgen->create_question('shortanswer', null, $this->question2data);

        // Explicitly save the custom field data for the questions, like a form would.
        $customfieldhandler = \qbank_customfields\customfield\question_handler::create();
        $this->question1data['id'] = $question1->id;
        $this->question2data['id'] = $question2->id;
        $customfieldhandler->instance_form_save((object)$this->question1data);
        $customfieldhandler->instance_form_save((object)$this->question2data);

        // Get the custom field data associated with these question ids.
        $q1cfdata = $customfieldhandler->export_instance_data_object($question1->id);
        $q2cfdata = $customfieldhandler->export_instance_data_object($question2->id);

        $this->assertEquals('some text', $q1cfdata->f1);
        $this->assertEquals('Yes', $q1cfdata->f2);
        $this->assertEquals(userdate($this->testnow, get_string('strftimedaydatetime')), $q1cfdata->f3);
        $this->assertEquals('b', $q1cfdata->f4);
        $this->assertEquals('test', $q1cfdata->f5);

        $this->assertEquals('some more text', $q2cfdata->f1);
        $this->assertEquals('No', $q2cfdata->f2);
        $this->assertEquals(userdate($this->testnow, get_string('strftimedaydatetime')), $q2cfdata->f3);
        $this->assertEquals('a', $q2cfdata->f4);
        $this->assertEquals('test text', $q2cfdata->f5);
    }

    /**
     * Test deleting questions with custom fields.
     */
    public function test_delete_question(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->setup_custom_fields();
        $this->setup_questions();

        // Create 2 questions.
        $question1 = $this->qgen->create_question('shortanswer', null, $this->question1data);
        $question2 = $this->qgen->create_question('shortanswer', null, $this->question2data);

        // Explicitly save the custom field data for the questions, like a form would.
        $customfieldhandler = \qbank_customfields\customfield\question_handler::create();
        $this->question1data['id'] = $question1->id;
        $this->question2data['id'] = $question2->id;
        $customfieldhandler->instance_form_save((object)$this->question1data);
        $customfieldhandler->instance_form_save((object)$this->question2data);

        // Get the custom field data associated with these question ids.
        $q1cfdata = $customfieldhandler->export_instance_data_object($question1->id);
        $q2cfdata = $customfieldhandler->export_instance_data_object($question2->id);

        // Quick check that we have data for the custom fields.
        $this->assertEquals('some text', $q1cfdata->f1);
        $this->assertEquals('some more text', $q2cfdata->f1);

        // Delete the questions.
        question_delete_question($question1->id);
        question_delete_question($question2->id);

        // Check the custom field data for the questions has also gone.
        $q1cfdata = $customfieldhandler->export_instance_data_object($question1->id);
        $q2cfdata = $customfieldhandler->export_instance_data_object($question2->id);

        $this->assertEmpty($q1cfdata->f1);
        $this->assertEmpty($q2cfdata->f1);
    }

    /**
     * Test custom fields attached to questions persist
     * across the backup and restore process.
     */
    public function test_backup_restore(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $this->setup_custom_fields();
        $this->setup_questions();

        $courseshortname = $this->course->shortname;
        $coursefullname = $this->course->fullname;

        // Create 2 questions.
        $question1 = $this->qgen->create_question('shortanswer', null, $this->question1data);
        $question2 = $this->qgen->create_question('shortanswer', null, $this->question2data);

        // Explicitly save the custom field data for the questions, like a form would.
        $customfieldhandler = \qbank_customfields\customfield\question_handler::create();
        $this->question1data['id'] = $question1->id;
        $this->question2data['id'] = $question2->id;
        $customfieldhandler->instance_form_save((object)$this->question1data);
        $customfieldhandler->instance_form_save((object)$this->question2data);

        // Create a quiz and the questions to that.
        $quiz = $this->getDataGenerator()->create_module(
                'quiz', ['course' => $this->course->id, 'name' => 'restored_quiz']);
        quiz_add_quiz_question($question1->id, $quiz);
        quiz_add_quiz_question($question2->id, $quiz);

        // Backup the course.
        $backupid = $this->backup_course($this->course);

        // Now delete everything.
        delete_course($this->course, false);
        question_delete_question($question1->id);
        question_delete_question($question2->id);

        // Check the custom field data for the questions has also gone.
        $q1cfdata = $customfieldhandler->export_instance_data_object($question1->id);
        $q2cfdata = $customfieldhandler->export_instance_data_object($question2->id);

        $this->assertEmpty($q1cfdata->f1);
        $this->assertEmpty($q2cfdata->f1);

        // Restore the backup we had made earlier into a new course.
        $newcategory = $this->getDataGenerator()->create_category();
        $this->restore_course($backupid, $coursefullname, $courseshortname . '_2', $newcategory->id);

        // The questions and their associated custom fields should have been restored.
        $sql = 'SELECT q.*
                 FROM {question} q
                 JOIN {question_versions} qv ON qv.questionid = q.id
                 JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                WHERE qbe.idnumber = ?';
        $newquestion1 = $DB->get_record_sql($sql, ['q1']);
        $newquestion1cfdata = $customfieldhandler->export_instance_data_object($newquestion1->id);
        $this->assertEquals('some text', $newquestion1cfdata->f1);
        $this->assertEquals('Yes', $newquestion1cfdata->f2);
        $this->assertEquals(userdate($this->testnow, get_string('strftimedaydatetime')), $newquestion1cfdata->f3);
        $this->assertEquals('b', $newquestion1cfdata->f4);
        $this->assertEquals('test', $newquestion1cfdata->f5);

        $newquestion2 = $DB->get_record_sql($sql, ['q2']);
        $newquestion2cfdata = $customfieldhandler->export_instance_data_object($newquestion2->id);
        $this->assertEquals('some more text', $newquestion2cfdata->f1);
        $this->assertEquals('No', $newquestion2cfdata->f2);
        $this->assertEquals(userdate($this->testnow, get_string('strftimedaydatetime')), $newquestion2cfdata->f3);
        $this->assertEquals('a', $newquestion2cfdata->f4);
        $this->assertEquals('test text', $newquestion2cfdata->f5);
    }
}
