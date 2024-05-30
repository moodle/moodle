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

namespace qtype_calculatedmulti;

use question_attempt_step;
use question_classified_response;
use question_state;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for qtype_calculatedmulti_definition.
 *
 * @package    qtype_calculatedmulti
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_test extends \advanced_testcase {
    public function test_is_complete_response(): void {
        $question = \test_question_maker::make_question('calculated');

        $this->assertFalse($question->is_complete_response(array()));
        $this->assertTrue($question->is_complete_response(array('answer' => '0')));
        $this->assertTrue($question->is_complete_response(array('answer' => 0)));
        $this->assertFalse($question->is_complete_response(array('answer' => 'test')));
    }

    public function test_is_gradable_response(): void {
        $question = \test_question_maker::make_question('calculated');

        $this->assertFalse($question->is_gradable_response(array()));
        $this->assertTrue($question->is_gradable_response(array('answer' => '0')));
        $this->assertTrue($question->is_gradable_response(array('answer' => 0)));
        $this->assertTrue($question->is_gradable_response(array('answer' => 'test')));
    }

    public function test_grading(): void {
        $question = \test_question_maker::make_question('calculated');
        $question->start_attempt(new question_attempt_step(), 1);
        $values = $question->vs->get_values();

        $this->assertEquals(array(0, question_state::$gradedwrong),
                $question->grade_response(array('answer' => $values['a'] - $values['b'])));
        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response(array('answer' => $values['a'] + $values['b'])));
    }

    public function test_get_correct_response(): void {
        $question = \test_question_maker::make_question('calculated');
        $question->start_attempt(new question_attempt_step(), 1);
        $values = $question->vs->get_values();

        $this->assertEquals(array('answer' => $values['a'] + $values['b']),
                $question->get_correct_response());
    }

    public function test_get_question_summary(): void {
        $question = \test_question_maker::make_question('calculated');
        $question->start_attempt(new question_attempt_step(), 1);
        $values = $question->vs->get_values();

        $qsummary = $question->get_question_summary();
        $this->assertEquals('What is ' . $values['a'] . ' + ' . $values['b'] . '?', $qsummary);
    }

    public function test_summarise_response(): void {
        $question = \test_question_maker::make_question('calculated');
        $question->start_attempt(new question_attempt_step(), 1);
        $values = $question->vs->get_values();

        $this->assertEquals('3.1', $question->summarise_response(array('answer' => '3.1')));
    }

    public function test_classify_response(): void {
        $question = \test_question_maker::make_question('calculated');
        $question->start_attempt(new question_attempt_step(), 1);
        $values = $question->vs->get_values();

        $this->assertEquals(array(
                new question_classified_response(13, $values['a'] + $values['b'], 1.0)),
                $question->classify_response(array('answer' => $values['a'] + $values['b'])));
        $this->assertEquals(array(
                new question_classified_response(14, $values['a'] - $values['b'], 0.0)),
                $question->classify_response(array('answer' => $values['a'] - $values['b'])));
        $this->assertEquals(array(
                new question_classified_response(17, 7 * $values['a'], 0.0)),
                $question->classify_response(array('answer' => 7 * $values['a'])));
        $this->assertEquals(array(
                question_classified_response::no_response()),
                $question->classify_response(array('answer' => '')));
    }
}
