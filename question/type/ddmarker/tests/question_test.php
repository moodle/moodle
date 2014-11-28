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
 * Unit tests for the drag-and-drop markers question definition class.
 *
 * @package   qtype_ddmarker
 * @copyright 2012 The Open University
 * @author    Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/ddmarker/tests/helper.php');


/**
 * Unit tests for the drag-and-drop markers question definition class.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddmarker_question_test extends basic_testcase {

    public function test_get_question_summary() {
        $dd = test_question_maker::make_question('ddmarker');
        $this->assertEquals('The quick brown fox jumped over the lazy dog.; '.
                            '[[Drop zone 1]] -> {quick / fox / lazy}; '.
                            '[[Drop zone 2]] -> {quick / fox / lazy}; '.
                            '[[Drop zone 3]] -> {quick / fox / lazy}',
                            $dd->get_question_summary());
    }

    public function test_get_question_summary_maths() {
        $dd = test_question_maker::make_question('ddmarker', 'maths');
        $this->assertEquals('Fill in the operators to make this equation work:; '.
                            '[[Drop zone 1]] -> {+ / - / * / /}; '.
                            '[[Drop zone 2]] -> {+ / - / * / /}; '.
                            '[[Drop zone 3]] -> {+ / - / * / /}',
                                    $dd->get_question_summary());
    }

    public function test_summarise_response() {
        $dd = test_question_maker::make_question('ddmarker');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals('{Drop zone 1 -> quick}, '.
                            '{Drop zone 2 -> fox}, '.
                            '{Drop zone 3 -> lazy}',
                $dd->summarise_response(array('c1' => '50,50',
                                                'c2' => '150,50',
                                                'c3' => '50,150')));
    }

    public function test_summarise_response_maths() {
        $dd = test_question_maker::make_question('ddmarker', 'maths');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals('{Drop zone 1 -> +}, '.
                            '{Drop zone 2 -> +}, '.
                            '{Drop zone 3 -> +}',
                $dd->summarise_response(array('c1' => '50,50;150,50;50,150',
                                                'c2' => '',
                                                'c3' => '')));
    }

    public function test_get_random_guess_score() {
        $dd = test_question_maker::make_question('ddmarker');
        $this->assertEquals(null, $dd->get_random_guess_score());
    }

    public function test_get_random_guess_score_maths() {
        $dd = test_question_maker::make_question('ddmarker', 'maths');
        $this->assertEquals(null, $dd->get_random_guess_score());
    }

    public function test_get_right_choice_for() {
        $dd = test_question_maker::make_question('ddmarker');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(1, $dd->get_right_choice_for(1));
        $this->assertEquals(2, $dd->get_right_choice_for(2));
        $this->assertEquals(3, $dd->get_right_choice_for(3));
    }

    public function test_get_right_choice_for_maths() {
        $dd = test_question_maker::make_question('ddmarker', 'maths');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(1, $dd->get_right_choice_for(1));
        $this->assertEquals(1, $dd->get_right_choice_for(2));
        $this->assertEquals(1, $dd->get_right_choice_for(3));
    }

    public function test_clear_wrong_from_response() {
        $dd = test_question_maker::make_question('ddmarker', 'maths');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $initialresponse = array('c1' => '50,50', 'c2' => '100,100', 'c3' => '100,100;200,200');
        $this->assertEquals(array('c1' => '50,50', 'c2' => '', 'c3' => ''),
                $dd->clear_wrong_from_response($initialresponse));
    }

    public function test_get_num_parts_right() {
        $dd = test_question_maker::make_question('ddmarker');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        // The second returned param in array is the max of correct choices or
        // the actual number of items dragged.
        $response1 = array('c1' => '50,50', 'c2' => '100,100', 'c3' => '100,100;200,200');
        $this->assertEquals(array(1, 4), $dd->get_num_parts_right($response1));
        $response2 = array('c1' => '50,50;150,50;50,150',
                            'c2' => '100,100',
                            'c3' => '100,100;200,200');
        $this->assertEquals(array(1, 6), $dd->get_num_parts_right($response2));
        $response3 = array('c1' => '50,50;150,50;50,150',
                            'c2' => '',
                            'c3' => '');
        $this->assertEquals(array(1, 3), $dd->get_num_parts_right($response3));
    }

    public function test_get_num_parts_right_maths() {
        $dd = test_question_maker::make_question('ddmarker', 'maths');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(3, 3),
                $dd->get_num_parts_right(array(
                        'c1' => '50,50;150,50;50,150', 'c2' => '', 'c3' => '')));
    }

    public function test_get_expected_data() {
        $dd = test_question_maker::make_question('ddmarker');
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(
            array('c1' => PARAM_NOTAGS, 'c2' => PARAM_NOTAGS, 'c3' => PARAM_NOTAGS),
            $dd->get_expected_data()
        );
    }

    public function test_get_correct_response() {
        $dd = test_question_maker::make_question('ddmarker');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array('c1' => '50,50', 'c2' => '150,50', 'c3' => '100,150'),
                            $dd->get_correct_response());
    }

    public function test_get_correct_response_maths() {
        $dd = test_question_maker::make_question('ddmarker', 'maths');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array('c1' => '50,50;150,50;50,150'), $dd->get_correct_response());
    }

    public function test_is_same_response() {
        $dd = test_question_maker::make_question('ddmarker');
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertTrue($dd->is_same_response(
                array(),
                array('c1' => '', 'c2' => '', 'c3' => '', 'c4' => '')));

        $this->assertFalse($dd->is_same_response(
                array(),
                array('c1' => '100,100', 'c2' => '', 'c3' => '', 'c4' => '')));

        $this->assertFalse($dd->is_same_response(
                array('c1' => '', 'c2' => '', 'c3' => '', 'c4' => ''),
                array('c1' => '100,100', 'c2' => '', 'c3' => '', 'c4' => '')));

        $this->assertTrue($dd->is_same_response(
                array('c1' => '100,100', 'c2' => '2', 'c3' => '3', 'c4' => '400,400'),
                array('c1' => '100,100', 'c2' => '2', 'c3' => '3', 'c4' => '400,400')));

        $this->assertFalse($dd->is_same_response(
                array('c1' => '100,100', 'c2' => '200,200', 'c3' => '300,300', 'c4' => '400,400'),
                array('c1' => '100,100', 'c2' => '200,200', 'c3' => '200,200', 'c4' => '400,400')));

        $this->assertTrue($dd->is_same_response(
                array('c1' => '100,100;200,200', 'c2' => '',
                        'c3' => '100,100;300,300', 'c4' => '400,400'),
                array('c1' => '200,200;100,100', 'c2' => '',
                        'c3' => '300,300;100,100', 'c4' => '400,400')));

        $this->assertFalse($dd->is_same_response(
                array('c1' => '100,100;200,200', 'c2' => '',
                        'c3' => '100,100;400,300', 'c4' => '400,400'),
                array('c1' => '200,200;100,100', 'c2' => '',
                        'c3' => '300,300;100,100', 'c4' => '400,400')));

        $this->assertTrue($dd->is_same_response(
                array('c1' => '100,100;100,100;200,200', 'c2' => '',
                        'c3' => '100,100;300,300', 'c4' => '400,400'),
                array('c1' => '200,200;100,100;100,100', 'c2' => '',
                        'c3' => '300,300;100,100', 'c4' => '400,400')));

        $this->assertFalse($dd->is_same_response(
                array('c1' => '100,100;100,100;200,200', 'c2' => '',
                        'c3' => '100,100;300,300', 'c4' => '400,400'),
                array('c1' => '200,200;100,100', 'c2' => '',
                        'c3' => '300,300;100,100', 'c4' => '400,400')));
    }
    public function test_is_complete_response() {
        $dd = test_question_maker::make_question('ddmarker');
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertFalse($dd->is_complete_response(array()));
        $this->assertFalse($dd->is_complete_response(
                array('c1' => '', 'c2' => '', 'c3' => '')));
        $this->assertFalse($dd->is_complete_response(array('c1' => '')));
        $this->assertTrue($dd->is_complete_response(
                array('c1' => '300,300', 'c2' => '300,300', 'c3' => '300,300')));
    }

    public function test_is_gradable_response() {
        $dd = test_question_maker::make_question('ddmarker');
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertFalse($dd->is_gradable_response(array()));
        $this->assertFalse($dd->is_gradable_response(
                array('c1' => '', 'c2' => '', 'c3' => '', 'c3' => '')));
        $this->assertTrue($dd->is_gradable_response(
                array('c1' => '300,300', 'c2' => '300,300', 'c3' => '')));
        $this->assertTrue($dd->is_gradable_response(array('c1' => '300,300')));
        $this->assertTrue($dd->is_gradable_response(
                array('c1' => '300,300', 'c2' => '300,300', 'c3' => '300,300')));
    }

    public function test_grading() {
        $dd = test_question_maker::make_question('ddmarker');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(1, question_state::$gradedright),
                $dd->grade_response(array('c1' => '50,50', 'c2' => '150,50', 'c3' => '100,150')));
        $this->assertEquals(array(2 / 3, question_state::$gradedpartial),
                $dd->grade_response(array('c1' => '50,50', 'c2' => '50,50', 'c3' => '100,150')));
        $this->assertEquals(array(0, question_state::$gradedwrong),
                $dd->grade_response(array('c1' => '150,50', 'c2' => '50,50', 'c3' => '100,50')));
    }

    public function test_grading_maths() {
        $dd = test_question_maker::make_question('ddmarker', 'maths');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(1, question_state::$gradedright),
                $dd->grade_response(array('c1' => '50,50;150,50;50,150', 'c2' => '', 'c3' => '')));
        $this->assertEquals(array(0.75, question_state::$gradedpartial),
                $dd->grade_response(array('c1' => '50,50;150,50;50,150',
                                            'c2' => '', 'c3' => '50,150')));
        $this->assertEquals(array(0, question_state::$gradedwrong),
                $dd->grade_response(array('c1' => '', 'c2' => '50,50;150,50', 'c3' => '100,50')));
        $this->assertEquals(array(0, question_state::$gradedwrong),
                            $dd->grade_response(array('c1' => '300,300',
                                                        'c2' => '50,50;150,50',
                                                        'c3' => '100,50')));
    }

    public function test_classify_response() {
        $dd = test_question_maker::make_question('ddmarker');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(
                                    1 => new question_classified_response(1, 'quick', 1 / 3),
                                    2 => new question_classified_response(2, 'fox', 1 / 3),
                                    3 => new question_classified_response(3, 'lazy', 1 / 3)),
            $dd->classify_response(array('c1' => '50,50', 'c2' => '150,50', 'c3' => '100,150')));

        $this->assertEquals(array(
                                    1 => new question_classified_response(1, 'quick', 1 / 3),
                                    2 => question_classified_response::no_response(),
                                    3 => question_classified_response::no_response()),
            $dd->classify_response(array('c1' => '50,50', 'c2' => '100,150', 'c3' => '150,50')));
    }
}
