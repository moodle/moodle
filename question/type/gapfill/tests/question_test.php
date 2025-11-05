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
 * Unit tests for the gapfill question definition class.
 *
 * @package    qtype_gapfill
 * @copyright  2017 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_gapfill;

defined('MOODLE_INTERNAL') || die();

use \qtype_gapfill_test_helper as helper;

global $CFG;

require_once($CFG->dirroot . '/question/type/questionbase.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/gapfill/tests/helper.php');

require_once($CFG->dirroot . '/question/type/gapfill/question.php');
/**
 * Unit tests for the gapfill question definition class.
 *
 * @copyright  2012 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \question\type\gapfill\question
 */
class question_test extends \advanced_testcase {

    /**
     *
     * @var qtype_gapfill $gapfill
     * An instance of the question type
     */
    public $qtype;

    /**
     * Test value returned by questionid_column_name()
     *
     * @covers ::get_expected_data()
     */
    public function test_get_expected_data() {
        $question = helper::make_question();
        $expecteddata = array('p1' => 'raw_trimmed', 'p2' => 'raw_trimmed');
        $this->assertEquals($question->get_expected_data(), $expecteddata);
    }

    /**
     * Test value returned by get_size()
     * which is a count of charactes in correct answer
     * @covers ::get_size()
     */
    public function test_get_size() {
        $question = helper::make_question();
        $answer = "123|12345";
        $this->assertEquals($question->get_size($answer), 5);
    }

    /**
     * confirm wrong responses are cleared
     * @covers ::clear_wrong_from_response()
     */
    public function test_clear_wrong_from_response() {
        $question = helper::make_question();
        $response = ['p1' => 'cat', 'p2' => 'dog'];
        $result = $question->clear_wrong_from_response($response);
        $this->assertEquals($result['p2'], "");
    }
    /**
     * Returns string of place key value prepended with p
     * @covers ::field(int)
     */
    public function test_field() {
        $question = helper::make_question();
        $this->assertEquals($question->field('1'), 'p1');
    }

    /**
     * Value used for reports
     *
     * @covers ::summarise_response()
     */
    public function test_summarise_response() {
        $question = helper::make_question();
        $response = array('p1' => 'cat', 'p2' => 'dog');
        $this->assertEquals($question->summarise_response($response), " cat  dog ");
    }
    /**
     * Calculate grade and returns an array
     *
     * @covers ::grade_response(array)
     */
    public function test_grade_response() {
        $question = helper::make_question();

        $response = array('p1' => 'cat', 'p2' => 'dog');
        list($fraction, $state) = $question->grade_response($response);

        /* with two fields, if you have one wrong the score (fraction)
        will be .5. Fraction is always a a fractional part of one.*/
        $this->assertEquals($fraction, .5);

        $response = array('p1' => 'cat', 'p2' => 'mat');
        list($fraction, $state) = $question->grade_response($response);

        // If you have all correct score (fraction)
        // will be 1. Fraction is always a a fractional part of one.
        $this->assertEquals($fraction, 1);
    }
    /**
     * The complex final grade calculation
     *
     * @covers ::compute_final_grade()
     */
    public function test_compute_final_grade() {
        $question = \qtype_gapfill_test_helper::make_question();
        $responses = [
            0 => ['p1' => 'cat', 'p2' => 'cat'],
            1 => ['p1' => 'cat', 'p2' => 'cat'],
            2 => ['p1' => 'cat', 'p2' => 'cat'],
        ];
        $fraction = $question->compute_final_grade($responses, 3);
        /* With a default mark of 2 this would show a mark of 1
        This was compared with how the ddwtos question marked */
        $this->assertEquals($fraction, .5);
    }

    /**
     * confirm the splitting of delimiters into
     * left and right
     * @covers ::get_delimit_array()
     */
    public function test_get_delimit_array() {
        $delimchars = \qtype_gapfill::get_delimit_array('[]');
        $this->assertEquals($delimchars['l'], '[');
        $this->assertEquals($delimchars['r'], ']');
    }
    /**
     * When noduplicates is true,
     * discard any duplicate responses when
     * calculating how many responses were correct
     * Normally used with questions like
     * What are the olympic medals?
     * [gold|silve|bronze] [gold|silve|bronze] [gold|silve|bronze]
     * So students cannot get marks for [gold] [gold] [gold]
     *
     * @covers ::test_discard_duplicates()
     */
    public function test_discard_duplicates() {
        $options = [
            "noduplicates" => 1,
        ];
        $questiontext = 'The [cat] sat on the [cat]';
        $question = \qtype_gapfill_test_helper::make_question($questiontext, $options);
        // Give the same answer for each gap.
        $response = array('p1' => 'cat', 'p2' => 'cat');
        // Discard duplicates by putting hash in duplicate gaps.
        $ddresponse = $question->discard_duplicates($response);
        $numpartsright = $question->get_num_parts_right($ddresponse);
        // 0 element is numright element 1 is gapcount.
        $this->assertEquals($numpartsright[0], 1, 'Expected 1 response to be discarded so 1 right');
    }

    /**
     *
     * Has the user put something in every gap?
     *
     * @covers ::is_complete_response(array)
     */
    public function test_is_complete_response() {
        $question = helper::make_question();
        $response = array('p1' => 'cat', 'p2' => 'mat');

        $this->assertTrue($question->is_complete_response($response));

        $response = array('p1' => 'cat');
        $question->gapcount = 2;
        $this->assertFalse($question->is_complete_response($response));

        $this->assertFalse($question->is_complete_response(array()));
    }
    /**
     *
     * What would be the right answer for this gap
     * @covers ::get_correct_response()
     */
    public function test_get_correct_response() {
        $question = helper::make_question();
        $this->assertEquals($question->get_correct_response(), array('p1' => 'cat', 'p2' => 'mat'));
    }
    /**
     * Returns prompt asking for answer if none is provided
     *
     * @covers ::get_validateion_error()
     */
    public function test_get_validation_error() {
        $questiontext = 'The [cat] sat on the [mat]';
        $question = helper::make_question($questiontext);
        $question->gapcount = 2;
        $this->assertTrue(is_string($question->get_validation_error(array('p1' => ''))));
    }
    /**
     * Is the text correct for this gap
     * @covers ::is_correct_response()
     */
    public function test_is_correct_response() {
        $question = helper::make_question();
        $question->casesensitive = 0;
        $answergiven = 'CAT';
        $rightanswer = 'cat';
        $this->assertTrue($question->is_correct_response($answergiven, $rightanswer));

        $question->casesensitive = 1;
        $this->assertFalse($question->is_correct_response($answergiven, $rightanswer));

        $answergiven = 'dog';
        $rightanswer = 'cat';
        $this->assertFalse($question->is_correct_response($answergiven, $rightanswer));

        $answergiven = 'cat';
        $rightanswer = 'cat';
        $this->assertTrue($question->is_correct_response($answergiven, $rightanswer));
    }
    /**
     * What is the correct value for a gap
     *
     * @covers ::get_right_choice_for()
     */
    public function test_get_right_choice_for_place() {
        $question = helper::make_question();
        $this->assertEquals($question->get_right_choice_for(1), 'cat');
        $this->assertNotEquals($question->get_right_choice_for(2), 'cat');
    }
    /**
     * Don't change answer if it is the same
     * @covers ::is_same_response()
     */
    public function test_is_same_response() {
        $question = helper::make_question();
        $prevresponse = array();
        $newresponse = array('p1' => 'cat', 'p2' => 'mat');
        $this->assertFalse($question->is_same_response($prevresponse, $newresponse));
        $prevresponse = array('p1' => 'cat', 'p2' => 'mat');
        $newresponse = array('p1' => 'cat', 'p2' => 'mat');
        $this->assertTrue($question->is_same_response($prevresponse, $newresponse));
    }
    public function setUp(): void {
        $this->qtype = \question_bank::get_qtype('gapfill');
    }

    protected function tearDown(): void {
        $this->qtype = null;
    }
}
