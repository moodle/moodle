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

namespace qtype_calculated;

use qtype_calculated;
use question_attempt_step;
use question_bank;
use question_classified_response;
use question_display_options;
use question_state;
use qtype_numerical;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for qtype_calculated_definition.
 *
 * @package    qtype_calculated
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_test extends \advanced_testcase {
    /**
     * Test is complete response
     *
     * @covers \qtype_calculated
     */
    public function test_is_complete_response(): void {
        $question = \test_question_maker::make_question('calculated');

        $this->assertFalse($question->is_complete_response([]));
        $this->assertTrue($question->is_complete_response(['answer' => '0']));
        $this->assertTrue($question->is_complete_response(['answer' => 0]));
        $this->assertFalse($question->is_complete_response(['answer' => 'test']));
    }

    /**
     * Test is gradable response
     *
     * @covers \qtype_calculated
     */
    public function test_is_gradable_response(): void {
        $question = \test_question_maker::make_question('calculated');

        $this->assertFalse($question->is_gradable_response([]));
        $this->assertTrue($question->is_gradable_response(['answer' => '0']));
        $this->assertTrue($question->is_gradable_response(['answer' => 0]));
        $this->assertTrue($question->is_gradable_response(['answer' => 'test']));
    }

    /**
     * Test grading
     *
     * @covers \qtype_calculated
     */
    public function test_grading(): void {
        $question = \test_question_maker::make_question('calculated');
        $question->start_attempt(new question_attempt_step(), 1);
        $values = $question->vs->get_values();

        $this->assertEquals([0, question_state::$gradedwrong],
                $question->grade_response(['answer' => $values['a'] - $values['b']]));
        $this->assertEquals([1, question_state::$gradedright],
                $question->grade_response(['answer' => $values['a'] + $values['b']]));
    }

    /**
     * Test get correct response
     *
     * @covers \qtype_calculated
     */
    public function test_get_correct_response(): void {
        // Testing with 3.0 + 0.1416.
        $question = \test_question_maker::make_question('calculated');
        $question->start_attempt(new question_attempt_step(), 3);
        $values = $question->vs->get_values();
        $this->assertSame(['answer' => '3.01' ], $question->get_correct_response());
        foreach ($question->answers as $answer) {
            $answer->correctanswerlength = 2;
            $answer->correctanswerformat = 2;
        }
        $this->assertSame(['answer' => '3.0' ], $question->get_correct_response());

        // Testing with 1.0 + 5.0.
        $question = \test_question_maker::make_question('calculated');
        $question->start_attempt(new question_attempt_step(), 1);
        $values = $question->vs->get_values();
        $this->assertSame(['answer' => '6.00' ], $question->get_correct_response());

        foreach ($question->answers as $answer) {
            $answer->correctanswerlength = 2;
            $answer->correctanswerformat = 2;
        }
        $this->assertSame(['answer' => '6.0' ],
                $question->get_correct_response());
        // Testing with 31.0 + 0.01416 .
        $question = \test_question_maker::make_question('calculated');
        $question->start_attempt(new question_attempt_step(), 4);
        $values = $question->vs->get_values();
        $this->assertSame(['answer' => '31.01' ], $question->get_correct_response());

        foreach ($question->answers as $answer) {
            $answer->correctanswerlength = 3;
            $answer->correctanswerformat = 2;
        }
        $this->assertSame(['answer' => '31.0' ], $question->get_correct_response());

    }

    /**
     * Test get question summary
     *
     * @covers \qtype_calculated
     */
    public function test_get_question_summary(): void {
        $question = \test_question_maker::make_question('calculated');
        $question->start_attempt(new question_attempt_step(), 1);
        $values = $question->vs->get_values();

        $qsummary = $question->get_question_summary();
        $this->assertEquals('What is ' . $values['a'] . ' + ' . $values['b'] . '?', $qsummary);
    }

    /**
     * Test summarise response
     *
     * @covers \qtype_calculated
     */
    public function test_summarise_response(): void {
        $question = \test_question_maker::make_question('calculated');
        $question->start_attempt(new question_attempt_step(), 1);
        $values = $question->vs->get_values();

        $this->assertEquals('3.1', $question->summarise_response(['answer' => '3.1']));
    }

    /**
     * Test classify response
     *
     * @covers \qtype_calculated
     */
    public function test_classify_response(): void {
        $question = \test_question_maker::make_question('calculated');
        $question->start_attempt(new question_attempt_step(), 1);
        $values = $question->vs->get_values();

        $this->assertEquals([
                new question_classified_response(13, $values['a'] + $values['b'], 1.0)],
                $question->classify_response(['answer' => $values['a'] + $values['b']]));
        $this->assertEquals([
                new question_classified_response(14, $values['a'] - $values['b'], 0.0)],
                $question->classify_response(['answer' => $values['a'] - $values['b']]));
        $this->assertEquals([
                new question_classified_response(17, 7 * $values['a'], 0.0)],
                $question->classify_response(['answer' => 7 * $values['a']]));
        $this->assertEquals([
                question_classified_response::no_response()],
                $question->classify_response(['answer' => '']));
    }

    /**
     * Test classify response no star
     *
     * @covers \qtype_calculated
     */
    public function test_classify_response_no_star(): void {
        $question = \test_question_maker::make_question('calculated');
        unset($question->answers[17]);
        $question->start_attempt(new question_attempt_step(), 1);
        $values = $question->vs->get_values();

        $this->assertEquals([
                new question_classified_response(13, $values['a'] + $values['b'], 1.0)],
                $question->classify_response(['answer' => $values['a'] + $values['b']]));
        $this->assertEquals([
                new question_classified_response(14, $values['a'] - $values['b'], 0.0)],
                $question->classify_response(['answer' => $values['a'] - $values['b']]));
        $this->assertEquals([
                new question_classified_response(0, 7 * $values['a'], 0.0)],
                $question->classify_response(['answer' => 7 * $values['a']]));
        $this->assertEquals([
                question_classified_response::no_response()],
                $question->classify_response(['answer' => '']));
    }

    /**
     * Test get variants selection seed q not synchronised
     *
     * @covers \qtype_calculated
     */
    public function test_get_variants_selection_seed_q_not_synchronised(): void {
        $question = \test_question_maker::make_question('calculated');
        $this->assertEquals($question->stamp, $question->get_variants_selection_seed());
    }

    /**
     * Test get variants selection seed q synchronised datasets not
     *
     * @covers \qtype_calculated
     */
    public function test_get_variants_selection_seed_q_synchronised_datasets_not(): void {
        $question = \test_question_maker::make_question('calculated');
        $question->synchronised = true;
        $this->assertEquals($question->stamp, $question->get_variants_selection_seed());
    }

    /**
     * Test get variants selection seed q synchronised
     *
     * @covers \qtype_calculated
     */
    public function test_get_variants_selection_seed_q_synchronised(): void {
        $question = \test_question_maker::make_question('calculated');
        $question->synchronised = true;
        $question->datasetloader->set_are_synchronised($question->category, true);
        $this->assertEquals('category' . $question->category,
                $question->get_variants_selection_seed());
    }

    /**
     * Test get question definition for external rendering
     *
     * @covers \qtype_calculated
     */
    public function test_get_question_definition_for_external_rendering(): void {
        $this->resetAfterTest();

        $question = \test_question_maker::make_question('calculated');
        $question->start_attempt(new question_attempt_step(), 1);
        $qa = \test_question_maker::get_a_qa($question);
        $displayoptions = new question_display_options();

        $options = $question->get_question_definition_for_external_rendering($qa, $displayoptions);
        $this->assertNotEmpty($options);
        $this->assertEquals(0, $options['unitgradingtype']);
        $this->assertEquals(0, $options['unitpenalty']);
        $this->assertEquals(qtype_numerical::UNITNONE, $options['unitdisplay']);
        $this->assertEmpty($options['unitsleft']);
    }

    /**
     * Test that the grading of negative responses does not show false ERROR.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \qtype_calculated
     */
    public function test_grading_of_negative_responses(): void {
        global $DB;

        $this->resetAfterTest();

        // Create a question.
        $q = \test_question_maker::get_question_data('calculated', 'mult');
        $q->id = 99;

        // Add units for the question. The issue to test only applies if the answer contains a unit string.
        $units = [];
        $unit = new \stdClass();
        $unit->question = $q->id;
        $unit->multiplier = 1.0;
        $unit->unit = "cm";
        $units[] = $unit;
        $DB->insert_records("question_numerical_units", $units);

        $qtype = new qtype_calculated();
        $qtypeobj = question_bank::get_qtype($qtype->name());
        $fakedata = ["a" => "5.7", "b" => "3.3"];

        $result = $qtype->comment_on_datasetitems($qtypeobj, $q->id, $q->questiontext, $q->options->answers, $fakedata, 1);

        // Make sure "ERROR" is not part of the answers.
        foreach ($result->stranswers as $answer) {
            $this->assertFalse(strstr($answer, "ERROR"), "Assert that 'ERROR' is not part of the answer!");
        }
    }
}
