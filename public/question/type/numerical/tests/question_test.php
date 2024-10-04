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

namespace qtype_numerical;

use qtype_numerical;
use qtype_numerical_answer_processor;
use question_attempt_step;
use question_classified_response;
use question_display_options;
use question_state;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the numerical question definition class.
 *
 * @package qtype_numerical
 * @copyright 2008 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class question_test extends \advanced_testcase {
    public function test_is_complete_response(): void {
        $question = \test_question_maker::make_question('numerical');

        $this->assertFalse($question->is_complete_response(array()));
        $this->assertTrue($question->is_complete_response(array('answer' => '0')));
        $this->assertTrue($question->is_complete_response(array('answer' => 0)));
        $this->assertFalse($question->is_complete_response(array('answer' => 'test')));
    }

    public function test_is_gradable_response(): void {
        $question = \test_question_maker::make_question('numerical');

        $this->assertFalse($question->is_gradable_response(array()));
        $this->assertTrue($question->is_gradable_response(array('answer' => '0')));
        $this->assertTrue($question->is_gradable_response(array('answer' => 0)));
        $this->assertTrue($question->is_gradable_response(array('answer' => 'test')));
    }

    public function test_grading(): void {
        $question = \test_question_maker::make_question('numerical');

        $this->assertEquals(array(0, question_state::$gradedwrong),
                $question->grade_response(array('answer' => '1.0')));
        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response(array('answer' => '3.14')));
    }

    public function test_grading_with_units(): void {
        $question = \test_question_maker::make_question('numerical');
        $question->unitgradingtype = qtype_numerical::UNITOPTIONAL;
        $question->ap = new qtype_numerical_answer_processor(
                array('m' => 1, 'cm' => 100), false, '.', ',');

        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response(array('answer' => '3.14 frogs')));
        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response(array('answer' => '3.14')));
        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response(array('answer' => '3.14 m')));
        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response(array('answer' => '314cm')));
        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response(array('answer' => '314000000x10^-8m')));
    }

    public function test_grading_with_units_graded(): void {
        $question = \test_question_maker::make_question('numerical');
        $question->unitgradingtype = qtype_numerical::UNITGRADED;
        $question->ap = new qtype_numerical_answer_processor(
                array('m' => 1, 'cm' => 100), false, '.', ',');

        $this->assertEquals(array(0.8, question_state::$gradedpartial),
                $question->grade_response(array('answer' => '3.14 frogs')));
        $this->assertEquals(array(0.8, question_state::$gradedpartial),
                $question->grade_response(array('answer' => '3.14')));
        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response(array('answer' => '3.14 m')));
        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response(array('answer' => '314cm')));
        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response(array('answer' => '314000000x10^-8m')));
        $this->assertEquals(array(0.8, question_state::$gradedpartial),
                $question->grade_response(array('answer' => '3.14 cm')));
        $this->assertEquals(array(0, question_state::$gradedwrong),
                $question->grade_response(array('answer' => '314 m')));
    }

    public function test_grading_unit(): void {
        $question = \test_question_maker::make_question('numerical', 'unit');

        $this->assertEquals(array(0, question_state::$gradedwrong),
                $question->grade_response(array('answer' => '2', 'unit' => 'm')));
        $this->assertEquals(array(0, question_state::$gradedwrong),
                $question->grade_response(array('answer' => '2', 'unit' => 'cm')));
        $this->assertEquals(array(0, question_state::$gradedwrong),
                $question->grade_response(array('answer' => '2', 'unit' => '')));

        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response(array('answer' => '1.25', 'unit' => 'm')));
        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response(array('answer' => '125', 'unit' => 'cm')));
        $this->assertEquals(array(0.5, question_state::$gradedpartial),
                $question->grade_response(array('answer' => '1.25', 'unit' => '')));

        $this->assertEquals(array(0.5, question_state::$gradedpartial),
                $question->grade_response(array('answer' => '1.23', 'unit' => 'm')));
        $this->assertEquals(array(0.5, question_state::$gradedpartial),
                $question->grade_response(array('answer' => '123', 'unit' => 'cm')));
        $this->assertEquals(array(0.25, question_state::$gradedpartial),
                $question->grade_response(array('answer' => '1.23', 'unit' => '')));
    }

    public function test_grading_currency(): void {
        $question = \test_question_maker::make_question('numerical', 'currency');

        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response(array('answer' => '$1332')));
        $this->assertEquals(array(1, question_state::$gradedright),
                $question->grade_response(array('answer' => '$ 1332')));
        $this->assertEquals(array(0.8, question_state::$gradedpartial),
                $question->grade_response(array('answer' => 'frog 1332')));
        $this->assertEquals(array(0.8, question_state::$gradedpartial),
                $question->grade_response(array('answer' => '1332')));
        $this->assertEquals(array(0.8, question_state::$gradedpartial),
                $question->grade_response(array('answer' => ' 1332')));
        $this->assertEquals(array(0, question_state::$gradedwrong),
                $question->grade_response(array('answer' => '1332 $')));
        $this->assertEquals(array(0, question_state::$gradedwrong),
                $question->grade_response(array('answer' => '1332 frogs')));
        $this->assertEquals(array(0, question_state::$gradedwrong),
                $question->grade_response(array('answer' => '$1')));
    }

    public function test_get_correct_response(): void {
        $question = \test_question_maker::make_question('numerical');

        $this->assertEquals(array('answer' => '3.14'),
                $question->get_correct_response());
    }

    public function test_get_correct_response_units(): void {
        $question = \test_question_maker::make_question('numerical', 'unit');

        $this->assertEquals(array('answer' => '1.25', 'unit' => 'm'),
                $question->get_correct_response());
    }

    public function test_get_correct_response_currency(): void {
        $question = \test_question_maker::make_question('numerical', 'currency');

        $this->assertEquals(array('answer' => '$ 1332'),
                $question->get_correct_response());
    }

    public function test_get_question_summary(): void {
        $num = \test_question_maker::make_question('numerical');
        $qsummary = $num->get_question_summary();
        $this->assertEquals('What is pi to two d.p.?', $qsummary);
    }

    public function test_summarise_response(): void {
        $num = \test_question_maker::make_question('numerical');
        $this->assertEquals('3.1', $num->summarise_response(array('answer' => '3.1')));
    }

    public function test_summarise_response_zero(): void {
        $num = \test_question_maker::make_question('numerical');
        $this->assertEquals('0', $num->summarise_response(array('answer' => '0')));
    }

    public function test_summarise_response_unit(): void {
        $num = \test_question_maker::make_question('numerical', 'unit');
        $this->assertEquals('3.1', $num->summarise_response(array('answer' => '3.1')));
        $this->assertEquals('3.1m', $num->summarise_response(array('answer' => '3.1m')));
        $this->assertEquals('3.1 cm', $num->summarise_response(array('answer' => '3.1 cm')));
    }

    public function test_summarise_response_currency(): void {
        $num = \test_question_maker::make_question('numerical', 'currency');
        $this->assertEquals('100', $num->summarise_response(array('answer' => '100')));
        $this->assertEquals('$100', $num->summarise_response(array('answer' => '$100')));
        $this->assertEquals('$ 100', $num->summarise_response(array('answer' => '$ 100')));
        $this->assertEquals('100 frogs', $num->summarise_response(array('answer' => '100 frogs')));
    }

    public function test_classify_response(): void {
        $num = \test_question_maker::make_question('numerical');
        $num->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(
                new question_classified_response(15, '3.1', 0.0)),
                $num->classify_response(array('answer' => '3.1')));
        $this->assertEquals(array(
                new question_classified_response(17, '42', 0.0)),
                $num->classify_response(array('answer' => '42')));
        $this->assertEquals(array(
                new question_classified_response(13, '3.14', 1.0)),
                $num->classify_response(array('answer' => '3.14')));
        // Invalid response.
        $this->assertEquals(array(
                new question_classified_response(null, 'abc', 0.0)),
                $num->classify_response(array('answer' => 'abc')));
    }

    public function test_classify_response_no_star(): void {
        $num = \test_question_maker::make_question('numerical');
        unset($num->answers[17]);
        $num->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(
                new question_classified_response(15, '3.1', 0.0)),
                $num->classify_response(array('answer' => '3.1')));
        $this->assertEquals(array(
                new question_classified_response(0, '42', 0.0)),
                $num->classify_response(array('answer' => '42')));
        // Invalid response.
        $this->assertEquals(array(
                new question_classified_response(null, 'abc', 0.0)),
                $num->classify_response(array('answer' => 'abc')));
    }

    public function test_classify_response_unit(): void {
        $num = \test_question_maker::make_question('numerical', 'unit');
        $num->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(
                new question_classified_response(13, '1.25', 0.5)),
                $num->classify_response(array('answer' => '1.25', 'unit' => '')));
        $this->assertEquals(array(
                new question_classified_response(13, '1.25 m', 1.0)),
                $num->classify_response(array('answer' => '1.25', 'unit' => 'm')));
        $this->assertEquals(array(
                new question_classified_response(13, '125 cm', 1.0)),
                $num->classify_response(array('answer' => '125', 'unit' => 'cm')));
        $this->assertEquals(array(
                new question_classified_response(14, '123 cm', 0.5)),
                $num->classify_response(array('answer' => '123', 'unit' => 'cm')));
        $this->assertEquals(array(
                new question_classified_response(14, '1.27 m', 0.5)),
                $num->classify_response(array('answer' => '1.27', 'unit' => 'm')));
        $this->assertEquals(array(
                new question_classified_response(17, '3.0 m', 0)),
                $num->classify_response(array('answer' => '3.0', 'unit' => 'm')));
        $this->assertEquals(array(
                question_classified_response::no_response()),
                $num->classify_response(array('answer' => '')));
        // Invalid response.
        $this->assertEquals(array(
                new question_classified_response(null, 'abc m', 0.0)),
                $num->classify_response(array('answer' => 'abc', 'unit' => 'm')));
    }

    public function test_classify_response_unit_no_star(): void {
        $num = \test_question_maker::make_question('numerical', 'unit');
        unset($num->answers[17]);
        $num->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(
                new question_classified_response(0, '42 cm', 0)),
                $num->classify_response(array('answer' => '42', 'unit' => 'cm')));
        $this->assertEquals(array(
                new question_classified_response(0, '3.0', 0)),
                $num->classify_response(array('answer' => '3.0', 'unit' => '')));
        $this->assertEquals(array(
                new question_classified_response(0, '3.0 m', 0)),
                $num->classify_response(array('answer' => '3.0', 'unit' => 'm')));
        $this->assertEquals(array(
                question_classified_response::no_response()),
                $num->classify_response(array('answer' => '', 'unit' => '')));
        // Invalid response.
        $this->assertEquals(array(
                            new question_classified_response(null, 'abc m', 0.0)),
                            $num->classify_response(array('answer' => 'abc', 'unit' => 'm')));
    }

    public function test_classify_response_currency(): void {
        $num = \test_question_maker::make_question('numerical', 'currency');
        $num->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(
                new question_classified_response(14, '$100', 0)),
                $num->classify_response(array('answer' => '$100')));
        $this->assertEquals(array(
                new question_classified_response(13, '1 332', 0.8)),
                $num->classify_response(array('answer' => '1 332')));
        // Invalid response.
        $this->assertEquals(array(
                new question_classified_response(null, '$abc', 0.0)),
                $num->classify_response(array('answer' => '$abc')));
    }

    /**
     * test_get_question_definition_for_external_rendering
     */
    public function test_get_question_definition_for_external_rendering(): void {
        $this->resetAfterTest();

        $question = \test_question_maker::make_question('numerical', 'unit');
        $question->start_attempt(new question_attempt_step(), 1);
        $qa = \test_question_maker::get_a_qa($question);
        $displayoptions = new question_display_options();

        $options = $question->get_question_definition_for_external_rendering($qa, $displayoptions);
        $this->assertNotEmpty($options);
        $this->assertEquals(1, $options['unitgradingtype']);
        $this->assertEquals(0.5, $options['unitpenalty']);
        $this->assertEquals(qtype_numerical::UNITSELECT, $options['unitdisplay']);
        $this->assertEmpty($options['unitsleft']);
    }
}
