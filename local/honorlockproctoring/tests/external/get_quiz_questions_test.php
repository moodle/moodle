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

namespace local_honorlockproctoring;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use local_honorlockproctoring\external\get_quiz_questions;

/**
 * Honorlock proctoring test for module.
 *
 * @package   local_honorlockproctoring
 * @copyright 2023 Honorlock (https://honorlock.com/)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_quiz_questions_test extends \externallib_advanced_testcase {
    /**
     * @var get_quiz_questions Keeps the get_quiz_questions Class.
     */
    protected $getquizquestions;

    /**
     * Setup test data.
     */
    protected function setUp(): void {
        $this->resetAfterTest();
        $this->get_quiz_questions = new get_quiz_questions();
    }

    /**
     * Test the class creation
     *
     * @covers \local_honorlockproctoring\external\get_quiz_questions
     */
    public function test_class_creation(): void {
        $getquizquestions = new get_quiz_questions();

        $this->assertInstanceOf(get_quiz_questions::class, $getquizquestions);
    }

    /**
     * Test the execute_parameters method
     *
     * @covers \local_honorlockproctoring\external\get_quiz_questions::execute_parameters
     */
    public function test_get_quiz_questions_parameters(): void {
        $result = $this->get_quiz_questions->execute_parameters();

        $this->assertIsObject($result);
    }

    /**
     * Test the execute_returns method
     *
     * @covers \local_honorlockproctoring\external\get_quiz_questions::execute_returns
     */
    public function test_get_quiz_questions_returns(): void {
        $result = $this->get_quiz_questions->execute_returns();

        $this->assertIsObject($result);
    }

    /**
     * Test the execute method
     *
     * @covers \local_honorlockproctoring\external\get_quiz_questions::execute
     */
    public function test_get_quiz_questions(): void {
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $question = $questiongenerator->create_question('shortanswer', null, ['category' => $cat->id]);
        quiz_add_quiz_question($question->id, $quiz);

        $this->setAdminUser();
        $result = $this->get_quiz_questions->execute($quiz->id);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
    }

    /**
     * Test the execute method returns false
     *
     * @covers \local_honorlockproctoring\external\get_quiz_questions::execute
     */
    public function test_get_quiz_questions_returns_false(): void {
        $nonexistentquizid = random_int(1, 5000);
        $expected = [
            'success' => false,
            'quizid' => $nonexistentquizid,
            'questions' => [],
        ];

        $result = $this->get_quiz_questions->execute($nonexistentquizid);

        $this->assertSame($expected, $result);
    }
}
