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
 * Unit tests for the REGEXP question definition class.
 *
 * @package    qtype_regexp
 * @copyright  2021 Joseph Rézeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_regexp;

defined('MOODLE_INTERNAL') || die();

use qtype_regexp_question;
use question_attempt_step;
use question_classified_response;
use question_state;
use test_question_maker;

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/regexp/question.php');

/**
 * Unit tests for the REGEXP question definition class.
 *
 * @copyright  2021 Joseph REZEAU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \question\type\regexp\question
 */
final class question_test extends \advanced_testcase {
    /**
     * Test compare_string_with_wildcard() method.
     * @covers ::compare_string_with_wildcard method
     */
    public function test_compare_string_with_wildcard(): void {
        // Test case.
        $ignorecase = true;
        $this->assertTrue((bool)qtype_regexp_question::compare_string_with_wildcard(
                'Blue, WHITE, red', "blue, white, red" , 1, $ignorecase));
        $ignorecase = false;
        $this->assertFalse((bool)qtype_regexp_question::compare_string_with_wildcard(
                'Blue, WHITE, red', "blue, white, red" , 0, $ignorecase));
        // Incorrect answers : grade = 0.
        // Detect missing word 'blue' :: True.
        $this->assertTrue((bool)qtype_regexp_question::compare_string_with_wildcard(
                'orange and black', '--.*blue.*', 0, $ignorecase));
        // Detect missing word 'blue' :: False.
        $this->assertFalse((bool)qtype_regexp_question::compare_string_with_wildcard(
                'orange and blue', '--.*blue.*', 0, $ignorecase));

        // Detect several missing words 'blue' AND 'red' AND 'white' :: True.
        $this->assertTrue((bool)qtype_regexp_question::compare_string_with_wildcard(
                'orange and black', '--.*(blue|red|white).*', 0, $ignorecase));
        // Detect several missing words 'blue' AND 'red' AND 'white' :: False.
        $this->assertFalse((bool)qtype_regexp_question::compare_string_with_wildcard(
                'orange and blue', '--.*(blue|red|white).*', 0, $ignorecase));

        // Detect missing words 'blue' OR 'red' OR 'white' :: True.
        $this->assertTrue((bool)qtype_regexp_question::compare_string_with_wildcard(
                'orange and black', '--.*(&&blue&&red&&white).*' , 0, $ignorecase));
        // Detect missing words 'blue' OR 'red' OR 'white' :: False.
        $this->assertFalse((bool)qtype_regexp_question::compare_string_with_wildcard(
                'orange blue white black red', '--.*(&&blue&&red&&white).*' , 0, $ignorecase));
    }

    /**
     * Test is_complete_response() method.
     * @covers ::is_complete_response
     */
    public function test_is_complete_response(): void {
        $question = \test_question_maker::make_question('regexp');

        $this->assertFalse($question->is_complete_response([]));
        $this->assertFalse($question->is_complete_response(['answer' => '']));
        $this->assertTrue($question->is_complete_response(['answer' => '0']));
        $this->assertTrue($question->is_complete_response(['answer' => '0.0']));
        $this->assertTrue($question->is_complete_response(['answer' => 'x']));
    }

    /**
     * Test is_gradable_response method.
     * @covers ::is_gradable_response
     */
    public function test_is_gradable_response(): void {
        $question = \test_question_maker::make_question('regexp');

        $this->assertFalse($question->is_gradable_response([]));
        $this->assertFalse($question->is_gradable_response(['answer' => '']));
        $this->assertTrue($question->is_gradable_response(['answer' => '0']));
        $this->assertTrue($question->is_gradable_response(['answer' => '0.0']));
        $this->assertTrue($question->is_gradable_response(['answer' => 'x']));
    }

    /**
     * Test test_grading method.
     * @covers ::grade_response
     */
    public function test_grading(): void {
        $question = \test_question_maker::make_question('regexp');

        $this->assertEquals([0, question_state::$gradedwrong], $question->grade_response(['answer' => 'x']));
        $this->assertEquals([1, question_state::$gradedright], $question->grade_response(['answer' => "it's blue, white and red"]));
        $this->assertEquals([0.8, question_state::$gradedpartial], $question->grade_response(['answer' => 'blue, white, red']));
    }

    /**
     * Test get_correct_response method.
     * @covers ::get_correct_response
     */
    public function test_get_correct_response(): void {
        $question = \test_question_maker::make_question('regexp');

        $this->assertEquals(['answer' => "it's blue, white and red"],
                $question->get_correct_response());
    }

    /**
     * Test get_question_summary method.
     * @covers ::get_question_summary
     */
    public function test_get_question_summary(): void {
        $question = \test_question_maker::make_question('regexp');
        $qsummary = $question->get_question_summary();
        $this->assertEquals('What are the colours of the French flag?', $qsummary);
    }

    /**
     * Test summarise_response method.
     * @covers ::summarise_response
     */
    public function test_summarise_response(): void {
        $question = \test_question_maker::make_question('regexp');
        $summary = $question->summarise_response(['answer' => "it's blue, white and red"]);
        $this->assertEquals("it's blue, white and red", $summary);
    }

    /**
     * Test classify_response method.
     * @covers ::classify_response
     */
    public function test_classify_response(): void {
        $question = \test_question_maker::make_question('regexp');
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(
            [new question_classified_response(13, "it's blue, white and red", 1.0)],
            $question->classify_response(['answer' => "it's blue, white and red"])
        );

        $this->assertEquals(
            [new question_classified_response(14, 'they are blue, white, red', 0.8)],
            $question->classify_response(['answer' => 'they are blue, white, red'])
        );

        $this->assertEquals(
            [new question_classified_response(14, 'it is blue, white, red', 0.8)],
            $question->classify_response(['answer' => 'it is blue, white, red'])
        );

        $this->assertEquals(
            [new question_classified_response(14, 'blue, white, red', 0.8)],
            $question->classify_response(['answer' => 'blue, white, red'])
        );

        $this->assertEquals(
            [new question_classified_response(16, 'red and white', 0.0)],
            $question->classify_response(['answer' => 'red and white'])
        );

        $this->assertEquals(
            [new question_classified_response(15, 'black', 0.0)],
            $question->classify_response(['answer' => 'black'])
        );

        $this->assertEquals(
            [question_classified_response::no_response()],
            $question->classify_response(['answer' => ''])
        );

    }
}
