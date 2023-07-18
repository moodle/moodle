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

namespace qtype_multianswer;

use question_attempt_step;
use question_display_options;
use question_state;
use question_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for qtype_multianswer_question.
 *
 * @package    qtype_multianswer
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \qtype_multianswer_question
 */
class question_test extends \advanced_testcase {
    public function test_get_expected_data() {
        $question = \test_question_maker::make_question('multianswer');
        $this->assertEquals(array('sub1_answer' => PARAM_RAW_TRIMMED,
                'sub2_answer' => PARAM_RAW), $question->get_expected_data());
    }

    public function test_is_complete_response() {
        $question = \test_question_maker::make_question('multianswer');

        $this->assertFalse($question->is_complete_response(array()));
        $this->assertTrue($question->is_complete_response(array('sub1_answer' => 'Owl',
                'sub2_answer' => '2')));
        $this->assertTrue($question->is_complete_response(array('sub1_answer' => '0',
                'sub2_answer' => 0)));
        $this->assertFalse($question->is_complete_response(array('sub1_answer' => 'Owl')));
    }

    public function test_is_gradable_response() {
        $question = \test_question_maker::make_question('multianswer');

        $this->assertFalse($question->is_gradable_response(array()));
        $this->assertTrue($question->is_gradable_response(array('sub1_answer' => 'Owl',
                'sub2_answer' => '2')));
        $this->assertTrue($question->is_gradable_response(array('sub1_answer' => '0',
                'sub2_answer' => 0)));
        $this->assertTrue($question->is_gradable_response(array('sub1_answer' => 'Owl')));
    }

    public function test_grading() {
        $question = \test_question_maker::make_question('multianswer');
        $question->start_attempt(new question_attempt_step(), 1);

        $rightchoice = $question->subquestions[2]->get_correct_response();

        $this->assertEquals(array(1, question_state::$gradedright), $question->grade_response(
                array('sub1_answer' => 'Owl', 'sub2_answer' => reset($rightchoice))));
        $this->assertEquals(array(0.5, question_state::$gradedpartial), $question->grade_response(
                array('sub1_answer' => 'Owl')));
        $this->assertEquals(array(0.5, question_state::$gradedpartial), $question->grade_response(
                array('sub1_answer' => 'Goat', 'sub2_answer' => reset($rightchoice))));
        $this->assertEquals(array(0, question_state::$gradedwrong), $question->grade_response(
                array('sub1_answer' => 'Dog')));
    }

    public function test_get_correct_response() {
        $question = \test_question_maker::make_question('multianswer');
        $question->start_attempt(new question_attempt_step(), 1);

        $rightchoice = $question->subquestions[2]->get_correct_response();

        $this->assertEquals(array('sub1_answer' => 'Owl', 'sub2_answer' => reset($rightchoice)),
                $question->get_correct_response());
    }

    public function test_get_question_summary() {
        $question = \test_question_maker::make_question('multianswer');

        // Bit of a hack to make testing easier.
        $question->subquestions[2]->shuffleanswers = false;

        $question->start_attempt(new question_attempt_step(), 1);

        $qsummary = $question->get_question_summary();
        $this->assertEquals('Complete this opening line of verse: "The _____ and the ' .
                '{Bow-wow; Wiggly worm; Pussy-cat} went to sea".', $qsummary);
    }

    public function test_summarise_response() {
        $question = \test_question_maker::make_question('multianswer');
        $question->start_attempt(new question_attempt_step(), 1);

        $rightchoice = $question->subquestions[2]->get_correct_response();

        $this->assertEquals(get_string('subqresponse', 'qtype_multianswer',
                array('i' => 1, 'response' => 'Owl')) . '; ' .
                get_string('subqresponse', 'qtype_multianswer',
                array('i' => 2, 'response' => 'Pussy-cat')), $question->summarise_response(
                array('sub1_answer' => 'Owl', 'sub2_answer' => reset($rightchoice))));
    }

    public function test_get_num_parts_right() {
        $question = \test_question_maker::make_question('multianswer');
        $question->start_attempt(new question_attempt_step(), 1);

        $rightchoice = $question->subquestions[2]->get_correct_response();
        $right = reset($rightchoice);

        $response = array('sub1_answer' => 'Frog', 'sub2_answer' => $right);
        list($numpartsright, $numparts) = $question->get_num_parts_right($response);
        $this->assertEquals(1, $numpartsright);
        $this->assertEquals(2, $numparts);
        $response = array('sub1_answer' => 'Owl', 'sub2_answer' => $right);
        list($numpartsright, $numparts) = $question->get_num_parts_right($response);
        $this->assertEquals(2, $numpartsright);
        $response = array('sub1_answer' => 'Dog', 'sub2_answer' => 3);
        list($numpartsright, $numparts) = $question->get_num_parts_right($response);
        $this->assertEquals(0, $numpartsright);
        $response = array('sub1_answer' => 'Owl');
        list($numpartsright, $numparts) = $question->get_num_parts_right($response);
        $this->assertEquals(1, $numpartsright);
        $response = array('sub1_answer' => 'Dog');
        list($numpartsright, $numparts) = $question->get_num_parts_right($response);
        $this->assertEquals(0, $numpartsright);
        $response = array('sub2_answer' => $right);
        list($numpartsright, $numparts) = $question->get_num_parts_right($response);
        $this->assertEquals(1, $numpartsright);
    }

    public function test_get_num_parts_right_fourmc() {
        // Create a multianswer question with four mcq.
        $question = \test_question_maker::make_question('multianswer', 'fourmc');
        $question->start_attempt(new question_attempt_step(), 1);

        $response = array('sub1_answer' => '1', 'sub2_answer' => '1',
                'sub3_answer' => '1', 'sub4_answer' => '1');
        list($numpartsright, $numparts) = $question->get_num_parts_right($response);
        $this->assertEquals(2, $numpartsright);
    }

    public function test_clear_wrong_from_response() {
        $question = \test_question_maker::make_question('multianswer');
        $question->start_attempt(new question_attempt_step(), 1);

        $rightchoice = $question->subquestions[2]->get_correct_response();
        $right = reset($rightchoice);

        $response = array('sub1_answer' => 'Frog', 'sub2_answer' => $right);
        $this->assertEquals($question->clear_wrong_from_response($response),
                array('sub1_answer' => '', 'sub2_answer' => $right));
        $response = array('sub1_answer' => 'Owl', 'sub2_answer' => $right);
        $this->assertEquals($question->clear_wrong_from_response($response),
                array('sub1_answer' => 'Owl', 'sub2_answer' => $right));
        $response = array('sub1_answer' => 'Dog', 'sub2_answer' => 3);
        $this->assertEquals($question->clear_wrong_from_response($response),
                array('sub1_answer' => '', 'sub2_answer' => ''));
        $response = array('sub1_answer' => 'Owl');
        $this->assertEquals($question->clear_wrong_from_response($response),
                array('sub1_answer' => 'Owl'));
        $response = array('sub2_answer' => $right);
        $this->assertEquals($question->clear_wrong_from_response($response),
                array('sub2_answer' => $right));
    }

    public function test_compute_final_grade() {
        $question = \test_question_maker::make_question('multianswer');
        // Set penalty to 0.2 to ease calculations.
        $question->penalty = 0.2;
        // Set subquestion 2 defaultmark to 2, to make it a better test,
        // even thought (at the moment) that never happens for real.
        $question->subquestions[2]->defaultmark = 2;

        $question->start_attempt(new question_attempt_step(), 1);

        // Compute right and wrong response for subquestion 2.
        $rightchoice = $question->subquestions[2]->get_correct_response();
        $right = reset($rightchoice);
        $wrong = ($right + 1) % 3;

        // Get subquestion 1 right at 2nd try and subquestion 2 right at 3rd try.
        $responses = array(0 => array('sub1_answer' => 'Dog', 'sub2_answer' => $wrong),
                           1 => array('sub1_answer' => 'Owl', 'sub2_answer' => $wrong),
                           2 => array('sub1_answer' => 'Owl', 'sub2_answer' => $right),
                          );
        $finalgrade = $question->compute_final_grade($responses, 1);
        $this->assertEqualsWithDelta(1 / 3 * (1 - 0.2) + 2 / 3 * (1 - 2 * 0.2), $finalgrade, question_testcase::GRADE_DELTA);

        // Get subquestion 1 right at 3rd try and subquestion 2 right at 2nd try.
        $responses = array(0 => array('sub1_answer' => 'Dog', 'sub2_answer' => $wrong),
                           1 => array('sub1_answer' => 'Cat', 'sub2_answer' => $right),
                           2 => array('sub1_answer' => 'Owl', 'sub2_answer' => $right),
                           3 => array('sub1_answer' => 'Owl', 'sub2_answer' => $right),
                          );
        $finalgrade = $question->compute_final_grade($responses, 1);
        $this->assertEqualsWithDelta(1 / 3 * (1 - 2 * 0.2) + 2 / 3 * (1 - 0.2), $finalgrade, question_testcase::GRADE_DELTA);

        // Get subquestion 1 right at 4th try and subquestion 2 right at 1st try.
        $responses = array(0 => array('sub1_answer' => 'Dog', 'sub2_answer' => $right),
                           1 => array('sub1_answer' => 'Dog', 'sub2_answer' => $right),
                           2 => array('sub1_answer' => 'Dog', 'sub2_answer' => $right),
                           3 => array('sub1_answer' => 'Owl', 'sub2_answer' => $right),
                          );
        $finalgrade = $question->compute_final_grade($responses, 1);
        $this->assertEqualsWithDelta(1 / 3 * (1 - 3 * 0.2) + 2 / 3, $finalgrade, question_testcase::GRADE_DELTA);

        // Get subquestion 1 right at 4th try and subquestion 2 right 3rd try.
        // Subquestion 2 was right at 1st try, but last change is at 3rd try.
        $responses = array(0 => array('sub1_answer' => 'Dog', 'sub2_answer' => $right),
                           1 => array('sub1_answer' => 'Cat', 'sub2_answer' => $wrong),
                           2 => array('sub1_answer' => 'Frog', 'sub2_answer' => $right),
                           3 => array('sub1_answer' => 'Owl', 'sub2_answer' => $right),
                          );
        $finalgrade = $question->compute_final_grade($responses, 1);
        $this->assertEqualsWithDelta(1 / 3 * (1 - 3 * 0.2) + 2 / 3 * (1 - 2 * 0.2), $finalgrade, question_testcase::GRADE_DELTA);

        // Incomplete responses. Subquestion 1 is right at 4th try and subquestion 2 at 3rd try.
        $responses = array(0 => array('sub1_answer' => 'Dog'),
                           1 => array('sub1_answer' => 'Cat'),
                           2 => array('sub1_answer' => 'Frog', 'sub2_answer' => $right),
                           3 => array('sub1_answer' => 'Owl', 'sub2_answer' => $right),
                          );
        $finalgrade = $question->compute_final_grade($responses, 1);
        $this->assertEqualsWithDelta(1 / 3 * (1 - 3 * 0.2) + 2 / 3 * (1 - 2 * 0.2), $finalgrade, question_testcase::GRADE_DELTA);
    }

    /**
     * test_get_question_definition_for_external_rendering
     */
    public function test_get_question_definition_for_external_rendering() {
        $this->resetAfterTest();

        $question = \test_question_maker::make_question('multianswer');
        $question->start_attempt(new question_attempt_step(), 1);
        $qa = \test_question_maker::get_a_qa($question);
        $displayoptions = new question_display_options();

        $options = $question->get_question_definition_for_external_rendering($qa, $displayoptions);
        $this->assertNull($options);
    }

    /**
     * Helper method to make a simulated second version of the standard multianswer test question.
     *
     * The key think is that all the answer ids are changed (increased by 20).
     *
     * @param \qtype_multianswer_question $question
     * @return \qtype_multianswer_question
     */
    protected function make_second_version(
            \qtype_multianswer_question $question): \qtype_multianswer_question {
        $newquestion = fullclone($question);

        $newquestion->subquestions[1]->answers = [
            36 => new \question_answer(16, 'Apple', 0.3333333,
                                      'Good', FORMAT_HTML),
            37 => new \question_answer(17, 'Burger', -0.5,
                                      '', FORMAT_HTML),
            38 => new \question_answer(18, 'Hot dog', -0.5,
                                      'Not a fruit', FORMAT_HTML),
            39 => new \question_answer(19, 'Pizza', -0.5,
                                      '', FORMAT_HTML),
            40 => new \question_answer(20, 'Orange', 0.3333333,
                                      'Correct', FORMAT_HTML),
            41 => new \question_answer(21, 'Banana', 0.3333333,
                                      '', FORMAT_HTML),
        ];

        $newquestion->subquestions[2]->answers = [
            42 => new \question_answer(22, 'Raddish', 0.5,
                                      'Good', FORMAT_HTML),
            43 => new \question_answer(23, 'Chocolate', -0.5,
                                      '', FORMAT_HTML),
            44 => new \question_answer(24, 'Biscuit', -0.5,
                                      'Not a vegetable', FORMAT_HTML),
            45 => new \question_answer(25, 'Cheese', -0.5,
                                      '', FORMAT_HTML),
            46 => new \question_answer(26, 'Carrot', 0.5,
                                      'Correct', FORMAT_HTML),
        ];

        return $newquestion;
    }

    public function test_validate_can_regrade_with_other_version_ok() {
        /** @var \qtype_multianswer_question $question */
        $question = \test_question_maker::make_question('multianswer', 'multiple');

        $newquestion = $this->make_second_version($question);

        $this->assertNull($newquestion->validate_can_regrade_with_other_version($question));
    }

    public function test_validate_can_regrade_with_other_version_wrong_subquestions() {
        /** @var \qtype_multianswer_question $question */
        $question = \test_question_maker::make_question('multianswer', 'multiple');

        $newquestion = $this->make_second_version($question);
        unset($newquestion->subquestions[2]);

        $this->assertEquals(
                get_string('regradeissuenumsubquestionschanged', 'qtype_multianswer'),
                $newquestion->validate_can_regrade_with_other_version($question));
    }

    public function test_validate_can_regrade_with_other_version_one_wrong_subquestion() {
        /** @var \qtype_multianswer_question $question */
        $question = \test_question_maker::make_question('multianswer', 'multiple');

        $newquestion = $this->make_second_version($question);
        unset($newquestion->subquestions[1]->answers[41]);

        $this->assertEquals(
                get_string('regradeissuenumchoiceschanged', 'qtype_multichoice'),
                $newquestion->validate_can_regrade_with_other_version($question));
    }

    public function test_update_attempt_state_date_from_old_version_ok() {
        /** @var \qtype_multianswer_question $question */
        $question = \test_question_maker::make_question('multianswer', 'multiple');

        $newquestion = $this->make_second_version($question);

        $oldstep = new question_attempt_step();
        $oldstep->set_qt_var('_sub1_order', '16,17,18,19,20,21');
        $oldstep->set_qt_var('_sub2_order', '22,23,24,25,26');

        $expected = [
            '_sub1_order' => '36,37,38,39,40,41',
            '_sub2_order' => '42,43,44,45,46',
        ];

        $this->assertEquals($expected,
                $newquestion->update_attempt_state_data_for_new_version($oldstep, $question));
    }

    /**
     * Test functions work with zero weight.
     * This is used for testing the MDL-77378 bug.
     */
    public function test_zeroweight() {
        $this->resetAfterTest();
        /** @var \qtype_multianswer_question $question */
        $question = \test_question_maker::make_question('multianswer', 'zeroweight');
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals([null, question_state::$gradedright], $question->grade_response(
            ['sub1_answer' => 'Something']));
        $this->assertEquals([null, question_state::$gradedwrong], $question->grade_response(
            ['sub1_answer' => 'Input box']));

        $this->assertEquals(1, $question->get_max_fraction());
        $this->assertEquals(0, $question->get_min_fraction());
    }

}
