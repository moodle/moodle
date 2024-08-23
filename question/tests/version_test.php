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
use core_question\output\question_version_info;
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

    protected function tearDown(): void {
        question_version_info::$pendingdefinitions = [];
        parent::tearDown();
    }

    /**
     * Test if creating a question a new version and bank entry records are created.
     *
     * @covers ::load_question
     */
    public function test_make_question_create_version_and_bank_entry(): void {
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
    public function test_delete_question_delete_versions(): void {
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
    public function test_delete_question_in_use(): void {
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
    public function test_move_category_with_questions(): void {
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
        $manager = new category_manager();
        $manager->move_questions_and_delete_category($qcategorychild->id, $qcategorysys->id);

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
    public function test_id_number_in_bank_entry(): void {
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

    /**
     * Test that all the versions are available from the method.
     *
     * @covers ::get_all_versions_of_question
     */
    public function test_get_all_versions_of_question(): void {
        $qcategory = $this->qgenerator->create_question_category(['contextid' => $this->context->id]);
        $question = $this->qgenerator->create_question('shortanswer', null,
            [
                'category' => $qcategory->id,
                'idnumber' => 'id1'
            ]);
        $questionid1 = $question->id;

        // Create a new version.
        $question = $this->qgenerator->update_question($question, null, ['idnumber' => 'id2']);
        $questionid2 = $question->id;
        // Change the id number and get the question object.
        $question = $this->qgenerator->update_question($question, null, ['idnumber' => 'id3']);
        $questionid3 = $question->id;

        $questiondefinition = question_bank::get_all_versions_of_question($question->id);

        // Test the versions are available.
        $this->assertEquals(array_slice($questiondefinition, 0, 1)[0]->questionid, $questionid3);
        $this->assertEquals(array_slice($questiondefinition, 1, 1)[0]->questionid, $questionid2);
        $this->assertEquals(array_slice($questiondefinition, 2, 1)[0]->questionid, $questionid1);
    }

    /**
     * Test that all the versions of questions are available from the method.
     *
     * @covers ::get_all_versions_of_questions
     */
    public function test_get_all_versions_of_questions(): void {
        global $DB;

        $questionversions = [];
        $qcategory = $this->qgenerator->create_question_category(['contextid' => $this->context->id]);
        $question = $this->qgenerator->create_question('shortanswer', null,
            [
                'category' => $qcategory->id,
                'idnumber' => 'id1'
            ]);
        $questionversions[1] = $question->id;

        // Create a new version.
        $question = $this->qgenerator->update_question($question, null, ['idnumber' => 'id2']);
        $questionversions[2] = $question->id;
        // Change the id number and get the question object.
        $question = $this->qgenerator->update_question($question, null, ['idnumber' => 'id3']);
        $questionversions[3] = $question->id;

        $questionbankentryid = $DB->get_record('question_versions', ['questionid' => $question->id], 'questionbankentryid');

        $questionversionsofquestions = question_bank::get_all_versions_of_questions([$question->id]);
        $questionbankentryids = array_keys($questionversionsofquestions)[0];
        $this->assertEquals($questionbankentryid->questionbankentryid, $questionbankentryids);
        $this->assertEquals($questionversions, $questionversionsofquestions[$questionbankentryids]);
    }

    /**
     * Test population of latestversion field in question_definition objects
     *
     * When an instance of question_definition is created, it is added to an array of pending definitions which
     * do not yet have the latestversion field populated. When one definition has its latestversion property accessed,
     * all pending definitions have their latestversion field populated at once.
     *
     * @covers \core_question\output\question_version_info::populate_latest_versions()
     * @return void
     */
    public function test_populate_definition_latestversions(): void {
        $qcategory = $this->qgenerator->create_question_category(['contextid' => $this->context->id]);
        $question1 = $this->qgenerator->create_question('shortanswer', null, ['category' => $qcategory->id]);
        $question2 = $this->qgenerator->create_question('shortanswer', null, ['category' => $qcategory->id]);
        $question3 = $this->qgenerator->update_question($question2, null, ['idnumber' => 'id2']);

        $latestversioninspector = new \ReflectionProperty('question_definition', 'latestversion');
        $this->assertEmpty(question_version_info::$pendingdefinitions);

        $questiondef1 = question_bank::load_question($question1->id);
        $questiondef2 = question_bank::load_question($question2->id);
        $questiondef3 = question_bank::load_question($question3->id);

        $this->assertContains($questiondef1, question_version_info::$pendingdefinitions);
        $this->assertContains($questiondef2, question_version_info::$pendingdefinitions);
        $this->assertContains($questiondef3, question_version_info::$pendingdefinitions);
        $this->assertNull($latestversioninspector->getValue($questiondef1));
        $this->assertNull($latestversioninspector->getValue($questiondef2));
        $this->assertNull($latestversioninspector->getValue($questiondef3));

        // Read latestversion from one definition. This should populate the field in all pending definitions.
        $latestversion1 = $questiondef1->latestversion;

        $this->assertEmpty(question_version_info::$pendingdefinitions);
        $this->assertNotNull($latestversioninspector->getValue($questiondef1));
        $this->assertNotNull($latestversioninspector->getValue($questiondef2));
        $this->assertNotNull($latestversioninspector->getValue($questiondef3));
        $this->assertEquals($latestversion1, $latestversioninspector->getValue($questiondef1));
        $this->assertEquals($questiondef1->version, $questiondef1->latestversion);
        $this->assertNotEquals($questiondef2->version, $questiondef2->latestversion);
        $this->assertEquals($questiondef3->version, $questiondef3->latestversion);
    }
}
