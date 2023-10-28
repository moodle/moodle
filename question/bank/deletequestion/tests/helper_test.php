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

namespace qbank_deletequestion;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');

/**
 * Class containing unit tests for the helper class
 *
 * @package qbank_deletequestion
 * @copyright 2023 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper_test extends \advanced_testcase {

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
     * Test get a confirmation message when deleting the question in the (question bank/history) page.
     *
     * @covers \qbank_deletequestion\helper::get_delete_confirmation_message
     */
    public function test_get_delete_confirmation_message(): void {
        $qcategory = $this->qgenerator->create_question_category(['contextid' => $this->context->id]);
        $question = $this->qgenerator->create_question('shortanswer', null, ['category' => $qcategory->id,
            'name' => 'Question 1']);
        $questionfirstversionid = $question->id;

        // Verify confirmation title and confirmation message with question not in use in question bank page.
        $deleteallversions = true;
        [$title1, $message1] = \qbank_deletequestion\helper::get_delete_confirmation_message([$questionfirstversionid],
            $deleteallversions);
        $this->assertEquals(['confirmtitle' => get_string('deletequestiontitle', 'question')],
            $title1);
        $this->assertEquals(get_string('deletequestioncheck', 'question',
            $question->name). ' v1' . '<br />', $message1);

        // Create a new version and adding it to a quiz.
        $question2 = $this->qgenerator->update_question($question, null, ['name' => 'Question 1']);
        $questionsecondversionid = $question2->id;

        // Verify confirmation title and confirmation message with question has multiple versions in question bank page.
        $listnameofquestionversion2 = $question->name . ' v1' . '<br />' . $question2->name . ' v2' .'<br />';
        [$title2, $message2] = \qbank_deletequestion\helper::get_delete_confirmation_message([$questionsecondversionid],
            $deleteallversions);
        $this->assertEquals(['confirmtitle' => get_string('deletequestiontitle', 'question')],
            $title2);
        $this->assertEquals(get_string('deletequestioncheck', 'question',
            $listnameofquestionversion2), $message2);

        // Verify confirmation title and confirmation message with multiple question selected.
        $listnameofquestionversion3 = $question->name . ' v1' . '<br />' . $question2->name . ' v2' .'<br />';
        [$title3, $message3] = \qbank_deletequestion\helper::get_delete_confirmation_message([$questionfirstversionid,
            $questionsecondversionid], $deleteallversions);
        $this->assertEquals(['confirmtitle' => get_string('deletequestiontitle_plural', 'question')],
            $title3);
        $this->assertEquals(get_string('deletequestionscheck', 'question',
            $listnameofquestionversion3), $message3);

        // Add second question version to the quiz to become question in use.
        quiz_add_quiz_question($questionsecondversionid, $this->quiz);

        // Verify confirmation message with question in use and has multiple versions in question bank page.
        $listnameofquestionversion4 = $question->name . ' v1' . '<br />' . '* ' . $question2->name . ' v2' . '<br />';
        $message4 = \qbank_deletequestion\helper::get_delete_confirmation_message([$questionsecondversionid],
            $deleteallversions)[1];
        $this->assertEquals(get_string('deletequestioncheck', 'question',
            $listnameofquestionversion4) . '<br />' . get_string('questionsinuse',
                'question'), $message4);

        // Verify confirmation title and confirmation message in history page with one question selected.
        $deleteallversions = false;
        [$title5, $message5] = \qbank_deletequestion\helper::get_delete_confirmation_message([$questionfirstversionid],
            $deleteallversions);
        $this->assertEquals(['confirmtitle' => get_string('deleteversiontitle', 'question')],
            $title5);
        $this->assertEquals(get_string('deleteselectedquestioncheck', 'question',
            $question->name) . '<br />', $message5);

        // Verify confirmation title and confirmation message in history page with multiple question selected.
        $listnameofquestionversion6 = 'Question 1<br />* Question 1<br />';
        [$title6, $message6] = \qbank_deletequestion\helper::get_delete_confirmation_message([$questionfirstversionid,
            $questionsecondversionid], $deleteallversions);
        $this->assertEquals(['confirmtitle' => get_string('deleteversiontitle_plural', 'question')],
            $title6);
        $this->assertEquals(get_string('deleteselectedquestioncheck', 'question',
            $listnameofquestionversion6) . '<br />'. get_string('questionsinuse', 'question'),
                $message6);
    }

    /**
     * Test delete questions have single/multiple version.
     *
     * @covers \qbank_deletequestion\helper::delete_questions
     */
    public function test_delete_question_has_multiple_version() {
        global $DB;
        $qcategory = $this->qgenerator->create_question_category(['contextid' => $this->context->id]);
        $question1 = $this->qgenerator->create_question('shortanswer', null, ['category' => $qcategory->id,
            'name' => 'Question 1 version 1']);
        $question1v1id = $question1->id;
        // Create a new version for question 1.
        $question1v2 = $this->qgenerator->update_question($question1, null, ['name' => 'Question 1 version 2']);
        $question1v2id = $question1v2->id;

        $question2 = $this->qgenerator->create_question('shortanswer', null, ['category' => $qcategory->id,
            'name' => 'Question 2 version 1']);
        $question2v1id = $question2->id;

        $question3 = $this->qgenerator->create_question('shortanswer', null, ['category' => $qcategory->id,
                'name' => 'Question 3 version 1']);
        $question3v1id = $question3->id;

        // Do.
        \qbank_deletequestion\helper::delete_questions([$question1v2id, $question2v1id], true);

        // All the versions of question1 will be deleted.
        $this->assertFalse($DB->record_exists('question', ['id' => $question1v1id]));
        $this->assertFalse($DB->record_exists('question', ['id' => $question1v2id]));

        // The question2 have single version will be deleted.
        $this->assertFalse($DB->record_exists('question', ['id' => $question2v1id]));

        // Check that we did not delete too much.
        $this->assertTrue($DB->record_exists('question', ['id' => $question3v1id]));
    }
}
