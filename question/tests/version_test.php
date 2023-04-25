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

namespace core_question;

use core_question\local\bank\question_version_status;
use question_bank;

/**
 * Question version unit tests.
 *
 * @package    core_question
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Guillermo Gomez Arias <guillermogomez@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \question_bank
 */
class version_test extends \advanced_testcase {

    /**
     * @var \context_module module context.
     */
    protected $context;

    /**
     * @var \stdClass course object.
     */
    protected $course;

    /**
     * @var \component_generator_base question generator.
     */
    protected $qgenerator;

    /**
     * @var \stdClass quiz object.
     */
    protected $quiz;

    /**
     * Called before every test.
     */
    protected function setUp(): void {
        parent::setUp();
        self::setAdminUser();
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $this->course = $datagenerator->create_course();
        $this->quiz = $datagenerator->create_module('quiz', ['course' => $this->course->id]);
        $this->qgenerator = $datagenerator->get_plugin_generator('core_question');
        $this->context = \context_module::instance($this->quiz->cmid);
    }

    /**
     * Test if creating a question a new version and bank entry records are created.
     *
     * @covers ::load_question
     */
    public function test_make_question_create_version_and_bank_entry() {
        global $DB;

        $qcategory = $this->qgenerator->create_question_category(['contextid' => $this->context->id]);
        $question = $this->qgenerator->create_question('shortanswer', null, ['category' => $qcategory->id]);

        // Get the question object after creating a question.
        $questiondefinition = question_bank::load_question($question->id);

        // The version and bank entry in the object should be the same.
        $sql = "SELECT qv.id AS versionid, qv.questionbankentryid
                  FROM {question} q
                  JOIN {question_versions} qv ON qv.questionid = q.id
                  JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                 WHERE q.id = ?";
        $questionversion = $DB->get_record_sql($sql, [$questiondefinition->id]);
        $this->assertEquals($questionversion->versionid, $questiondefinition->versionid);
        $this->assertEquals($questionversion->questionbankentryid, $questiondefinition->questionbankentryid);

        // If a question is updated, a new version should be created.
        $question = $this->qgenerator->update_question($question, null, ['name' => 'This is a new version']);
        $newquestiondefinition = question_bank::load_question($question->id);
        // The version should be 2.
        $this->assertEquals('2', $newquestiondefinition->version);

        // Both versions should be in same bank entry.
        $this->assertEquals($questiondefinition->questionbankentryid, $newquestiondefinition->questionbankentryid);
    }

    /**
     * Test if deleting a question the related version and bank entry records are deleted.
     *
     * @covers ::load_question
     * @covers ::question_delete_question
     */
    public function test_delete_question_delete_versions() {
        global $DB;

        $qcategory = $this->qgenerator->create_question_category(['contextid' => $this->context->id]);
        $question = $this->qgenerator->create_question('shortanswer', null, ['category' => $qcategory->id]);
        $questionfirstversionid = $question->id;

        // Create a new version and try to remove it.
        $question = $this->qgenerator->update_question($question, null, ['name' => 'This is a new version']);

        // The new version and bank entry record should exist.
        $sql = "SELECT q.id, qv.id AS versionid, qv.questionbankentryid
                  FROM {question} q
                  JOIN {question_versions} qv ON qv.questionid = q.id
                  JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                 WHERE q.id = ?";
        $questionobject = $DB->get_records_sql($sql, [$question->id]);
        $this->assertCount(1, $questionobject);

        // Try to delete new version.
        question_delete_question($question->id);

        // The version record should not exist.
        $sql = "SELECT qv.*
                  FROM {question_versions} qv
                 WHERE qv.id = ?";
        $questionversion = $DB->get_record_sql($sql, [$questionobject[$question->id]->versionid]);
        $this->assertFalse($questionversion);

        // The bank entry record should exist because there is an older version.
        $sql = "SELECT qbe.*
                  FROM {question_bank_entries} qbe
                 WHERE qbe.id = ?";
        $questionbankentry = $DB->get_records_sql($sql, [$questionobject[$question->id]->questionbankentryid]);
        $this->assertCount(1, $questionbankentry);

        // Now remove the first version.
        question_delete_question($questionfirstversionid);
        $sql = "SELECT qbe.*
                  FROM {question_bank_entries} qbe
                 WHERE qbe.id = ?";
        $questionbankentry = $DB->get_record_sql($sql, [$questionobject[$question->id]->questionbankentryid]);
        // The bank entry record should not exist.
        $this->assertFalse($questionbankentry);
    }

    /**
     * Test if deleting a question will not break a quiz.
     *
     * @covers ::load_question
     * @covers ::quiz_add_quiz_question
     * @covers ::question_delete_question
     */
    public function test_delete_question_in_use() {
        global $DB;

        $qcategory = $this->qgenerator->create_question_category(['contextid' => $this->context->id]);
        $question = $this->qgenerator->create_question('shortanswer', null, ['category' => $qcategory->id]);
        $questionfirstversionid = $question->id;

        // Create a new version and try to remove it after adding it to a quiz.
        $question = $this->qgenerator->update_question($question, null, ['name' => 'This is a new version']);

        // Add it to the quiz.
        quiz_add_quiz_question($question->id, $this->quiz);

        // Try to delete new version.
        question_delete_question($question->id);
        // Try to delete old version.
        question_delete_question($questionfirstversionid);

        // The used question version should exist even after trying to remove it, but now hidden.
        $questionversion2 = question_bank::load_question($question->id);
        $this->assertEquals($question->id, $questionversion2->id);
        $this->assertEquals(question_version_status::QUESTION_STATUS_HIDDEN, $questionversion2->status);

        // The unused version should be completely gone.
        $this->assertFalse($DB->record_exists('question', ['id' => $questionfirstversionid]));
    }

    /**
     * Test if moving a category will not break a quiz.
     *
     * @covers ::load_question
     * @covers ::quiz_add_quiz_question
     */
    public function test_move_category_with_questions() {
        global $DB;

        $qcategory = $this->qgenerator->create_question_category(['contextid' => $this->context->id]);
        $qcategorychild = $this->qgenerator->create_question_category(['contextid' => $this->context->id,
            'parent' => $qcategory->id]);
        $systemcontext = \context_system::instance();
        $qcategorysys = $this->qgenerator->create_question_category(['contextid' => $systemcontext->id]);
        $question = $this->qgenerator->create_question('shortanswer', null, ['category' => $qcategorychild->id]);
        $questiondefinition = question_bank::load_question($question->id);

        // Add it to the quiz.
        quiz_add_quiz_question($question->id, $this->quiz);

        // Move the category to system context.
        $contexts = new \core_question\local\bank\question_edit_contexts($systemcontext);
        $qcobject = new \qbank_managecategories\question_category_object(null,
            new \moodle_url('/question/bank/managecategories/category.php', ['courseid' => SITEID]),
            $contexts->having_one_edit_tab_cap('categories'), 0, null, 0,
            $contexts->having_cap('moodle/question:add'));
        $qcobject->move_questions_and_delete_category($qcategorychild->id, $qcategorysys->id);

        // The bank entry record should point to the new category in order to not break quizzes.
        $sql = "SELECT qbe.questioncategoryid
                  FROM {question_bank_entries} qbe
                 WHERE qbe.id = ?";
        $questionbankentry = $DB->get_record_sql($sql, [$questiondefinition->questionbankentryid]);
        $this->assertEquals($qcategorysys->id, $questionbankentry->questioncategoryid);
    }

    /**
     * Test that all versions will have the same bank entry idnumber value.
     *
     * @covers ::load_question
     */
    public function test_id_number_in_bank_entry() {
        global $DB;

        $qcategory = $this->qgenerator->create_question_category(['contextid' => $this->context->id]);
        $question = $this->qgenerator->create_question('shortanswer', null,
            [
                'category' => $qcategory->id,
                'idnumber' => 'id1'
            ]);
        $questionid1 = $question->id;

        // Create a new version and try to remove it after adding it to a quiz.
        $question = $this->qgenerator->update_question($question, null, ['idnumber' => 'id2']);
        $questionid2 = $question->id;
        // Change the id number and get the question object.
        $question = $this->qgenerator->update_question($question, null, ['idnumber' => 'id3']);
        $questionid3 = $question->id;

        // The new version and bank entry record should exist.
        $questiondefinition = question_bank::load_question($question->id);
        $sql = "SELECT q.id AS questionid, qv.id AS versionid, qbe.id AS questionbankentryid, qbe.idnumber
                  FROM {question_bank_entries} qbe
                  JOIN {question_versions} qv ON qv.questionbankentryid = qbe.id
                  JOIN {question} q ON q.id = qv.questionid
                 WHERE qbe.id = ?";
        $questionbankentry = $DB->get_records_sql($sql, [$questiondefinition->questionbankentryid]);

        // We should have 3 versions and 1 question bank entry with the same idnumber.
        $this->assertCount(3, $questionbankentry);
        $this->assertEquals($questionbankentry[$questionid1]->idnumber, 'id3');
        $this->assertEquals($questionbankentry[$questionid2]->idnumber, 'id3');
        $this->assertEquals($questionbankentry[$questionid3]->idnumber, 'id3');
    }
}
