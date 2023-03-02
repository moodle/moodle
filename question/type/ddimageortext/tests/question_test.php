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

namespace qtype_ddimageortext;

use question_attempt_step;
use question_classified_response;
use question_state;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/ddimageortext/tests/helper.php');


/**
 * Unit tests for the matching question definition class.
 *
 * @package   qtype_ddimageortext
 * @copyright 2009 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers qtype_ddimageortext_question
 */
class question_test extends \basic_testcase {

    public function test_get_question_summary() {
        $dd = \test_question_maker::make_question('ddimageortext');
        $this->assertEquals('The quick brown fox jumped over the lazy dog.; '.
                '[[Drop zone 1]] -> {1. quick / 2. fox}; '.
                '[[Drop zone 2]] -> {1. quick / 2. fox}; '.
                '[[Drop zone 3]] -> {3. lazy / 4. dog}; '.
                '[[Drop zone 4]] -> {3. lazy / 4. dog}',
            $dd->get_question_summary());
    }

    public function test_get_question_summary_maths() {
        $dd = \test_question_maker::make_question('ddimageortext', 'maths');
        $this->assertEquals('Fill in the operators to make this equation work: '.
                '7 [[1]] 11 [[2]] 13 [[1]] 17 [[2]] 19 = 3; '.
                '[[Drop zone 1]] -> {1. + / 2. -}; '.
                '[[Drop zone 2]] -> {1. + / 2. -}; '.
                '[[Drop zone 3]] -> {1. + / 2. -}; '.
                '[[Drop zone 4]] -> {1. + / 2. -}',
            $dd->get_question_summary());
    }

    public function test_summarise_response() {
        $dd = \test_question_maker::make_question('ddimageortext');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals('Drop zone 1 -> {1. quick} '.
                'Drop zone 2 -> {1. quick} '.
                'Drop zone 3 -> {3. lazy} '.
                'Drop zone 4 -> {3. lazy}',
            $dd->summarise_response(array('p1' => '1', 'p2' => '1', 'p3' => '1', 'p4' => '1')));
    }

    public function test_summarise_response_maths() {
        $dd = \test_question_maker::make_question('ddimageortext', 'maths');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals('Drop zone 1 -> {1. +} '.
                'Drop zone 2 -> {2. -} '.
                'Drop zone 3 -> {1. +} '.
                'Drop zone 4 -> {2. -}',
            $dd->summarise_response(array('p1' => '1', 'p2' => '2', 'p3' => '1', 'p4' => '2')));
    }

    public function test_get_random_guess_score() {
        $dd = \test_question_maker::make_question('ddimageortext');
        $this->assertEquals(0.5, $dd->get_random_guess_score());
    }

    public function test_get_random_guess_score_maths() {
        $dd = \test_question_maker::make_question('ddimageortext', 'maths');
        $this->assertEquals(0.5, $dd->get_random_guess_score());
    }

    public function test_get_random_guess_score_broken_question() {
        // Before MDL-76298 was fixed, it was possible to create a question with
        // no drop zones, and that caused a fatal division by zero error. Because
        // people might have questions like that in their database, we need to
        // check the calculation is robust to it.
        /** @var \qtype_ddimageortext_question $dd */
        $dd = \test_question_maker::make_question('ddimageortext');
        $dd->places = [];
        $this->assertNull($dd->get_random_guess_score());
    }

    public function test_get_right_choice_for() {
        $dd = \test_question_maker::make_question('ddimageortext');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(1, $dd->get_right_choice_for(1));
        $this->assertEquals(2, $dd->get_right_choice_for(2));
    }

    public function test_get_right_choice_for_maths() {
        $dd = \test_question_maker::make_question('ddimageortext', 'maths');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(1, $dd->get_right_choice_for(1));
        $this->assertEquals(2, $dd->get_right_choice_for(2));
        $this->assertEquals(1, $dd->get_right_choice_for(3));
        $this->assertEquals(2, $dd->get_right_choice_for(4));
    }

    public function test_clear_wrong_from_response() {
        $dd = \test_question_maker::make_question('ddimageortext', 'maths');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $initialresponse = array('p1' => '1', 'p2' => '1', 'p3' => '1', 'p4' => '1');
        $this->assertEquals(array('p1' => '1', 'p2' => '', 'p3' => '1', 'p4' => ''),
            $dd->clear_wrong_from_response($initialresponse));
    }

    public function test_get_num_parts_right() {
        $dd = \test_question_maker::make_question('ddimageortext');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(2, 4),
            $dd->get_num_parts_right(array('p1' => '1', 'p2' => '1', 'p3' => '2', 'p4' => '2')));
        $this->assertEquals(array(4, 4),
            $dd->get_num_parts_right(array('p1' => '1', 'p2' => '2', 'p3' => '1', 'p4' => '2')));
    }

    public function test_get_num_parts_right_maths() {
        $dd = \test_question_maker::make_question('ddimageortext', 'maths');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(2, 4),
            $dd->get_num_parts_right(array(
                'p1' => '1', 'p2' => '1', 'p3' => '1', 'p4' => '1')));
    }

    public function test_get_expected_data() {
        $dd = \test_question_maker::make_question('ddimageortext');
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(
            array('p1' => PARAM_INT, 'p2' => PARAM_INT, 'p3' => PARAM_INT, 'p4' => PARAM_INT),
            $dd->get_expected_data()
        );
    }

    public function test_get_correct_response() {
        $dd = \test_question_maker::make_question('ddimageortext');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array('p1' => '1', 'p2' => '2', 'p3' => '1', 'p4' => '2'),
            $dd->get_correct_response());
    }

    public function test_get_correct_response_maths() {
        $dd = \test_question_maker::make_question('ddimageortext', 'maths');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array('p1' => '1', 'p2' => '2', 'p3' => '1', 'p4' => '2'),
            $dd->get_correct_response());
    }

    public function test_is_same_response() {
        $dd = \test_question_maker::make_question('ddimageortext');
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertTrue($dd->is_same_response(
            array(),
            array('p1' => '', 'p2' => '', 'p3' => '', 'p4' => '')));

        $this->assertFalse($dd->is_same_response(
            array(),
            array('p1' => '1', 'p2' => '', 'p3' => '', 'p4' => '')));

        $this->assertFalse($dd->is_same_response(
            array('p1' => '', 'p2' => '', 'p3' => '', 'p4' => ''),
            array('p1' => '1', 'p2' => '', 'p3' => '', 'p4' => '')));

        $this->assertTrue($dd->is_same_response(
            array('p1' => '1', 'p2' => '2', 'p3' => '3', 'p4' => '4'),
            array('p1' => '1', 'p2' => '2', 'p3' => '3', 'p4' => '4')));

        $this->assertFalse($dd->is_same_response(
            array('p1' => '1', 'p2' => '2', 'p3' => '3', 'p4' => '4'),
            array('p1' => '1', 'p2' => '2', 'p3' => '2', 'p4' => '4')));
    }
    public function test_is_complete_response() {
        $dd = \test_question_maker::make_question('ddimageortext');
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertFalse($dd->is_complete_response(array()));
        $this->assertFalse($dd->is_complete_response(
            array('p1' => '1', 'p2' => '1', 'p3' => '', 'p4' => '')));
        $this->assertFalse($dd->is_complete_response(array('p1' => '1')));
        $this->assertTrue($dd->is_complete_response(
            array('p1' => '1', 'p2' => '1', 'p3' => '1', 'p4' => '1')));
    }

    public function test_is_gradable_response() {
        $dd = \test_question_maker::make_question('ddimageortext');
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertFalse($dd->is_gradable_response(array()));
        $this->assertFalse($dd->is_gradable_response(
            array('p1' => '', 'p2' => '', 'p3' => '', 'p3' => '')));
        $this->assertTrue($dd->is_gradable_response(
            array('p1' => '1', 'p2' => '1', 'p3' => '')));
        $this->assertTrue($dd->is_gradable_response(array('p1' => '1')));
        $this->assertTrue($dd->is_gradable_response(
            array('p1' => '1', 'p2' => '1', 'p3' => '1')));
    }

    public function test_grading() {
        $dd = \test_question_maker::make_question('ddimageortext');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(1, question_state::$gradedright),
            $dd->grade_response(array('p1' => '1', 'p2' => '2', 'p3' => '1', 'p4' => '2')));
        $this->assertEquals(array(0.25, question_state::$gradedpartial),
            $dd->grade_response(array('p1' => '1')));
        $this->assertEquals(array(0, question_state::$gradedwrong),
            $dd->grade_response(array('p1' => '2', 'p2' => '1', 'p3' => '2', 'p4' => '1')));
    }

    public function test_grading_maths() {
        $dd = \test_question_maker::make_question('ddimageortext', 'maths');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(1, question_state::$gradedright),
            $dd->grade_response(array('p1' => '1', 'p2' => '2', 'p3' => '1', 'p4' => '2')));
        $this->assertEquals(array(0.5, question_state::$gradedpartial),
            $dd->grade_response(array('p1' => '1', 'p2' => '1', 'p3' => '1', 'p4' => '1')));
        $this->assertEquals(array(0, question_state::$gradedwrong),
            $dd->grade_response(array('p1' => '', 'p2' => '1', 'p3' => '2', 'p4' => '1')));
    }

    public function test_classify_response() {
        $dd = \test_question_maker::make_question('ddimageortext');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array(
            1 => new question_classified_response(1, '1. quick', 1),
            2 => new question_classified_response(2, '2. fox', 1),
            3 => new question_classified_response(3, '3. lazy', 1),
            4 => new question_classified_response(4, '4. dog', 1)
        ), $dd->classify_response(array('p1' => '1', 'p2' => '2', 'p3' => '1', 'p4' => '2')));
        $this->assertEquals(array(
            1 => question_classified_response::no_response(),
            2 => new question_classified_response(1, '1. quick', 0),
            3 => new question_classified_response(4, '4. dog', 0),
            4 => new question_classified_response(4, '4. dog', 1)
        ), $dd->classify_response(array('p1' => '', 'p2' => '1', 'p3' => '2', 'p4' => '2')));
    }

    public function test_summarise_response_choice_deleted() {
        /** @var qtype_ddtoimage_question_base $dd */
        $dd = \test_question_maker::make_question('ddimageortext');
        $dd->shufflechoices = false;
        $dd->start_attempt(new question_attempt_step(), 1);
        // Simulation of an instructor deleting 1 choice after an attempt has been made.
        unset($dd->choices[1][1]);
        $delquestionstr = get_string('deletedchoice', 'qtype_ddimageortext');
        $this->assertEquals("Drop zone 1 -> {{$delquestionstr}} ".
            "Drop zone 2 -> {{$delquestionstr}} ".
            'Drop zone 3 -> {3. lazy} '.
            'Drop zone 4 -> {3. lazy}',
            $dd->summarise_response(array('p1' => '1', 'p2' => '1', 'p3' => '1', 'p4' => '1')));
    }
}
