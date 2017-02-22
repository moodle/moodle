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
 * Unit tests for the matching question definition classes.
 *
 * @package   qtype_match
 * @copyright 2009 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the matching question definition class.
 *
 * @copyright 2009 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_match_question_test extends advanced_testcase {

    public function test_get_expected_data() {
        $question = test_question_maker::make_a_matching_question();
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array('sub0' => PARAM_INT, 'sub1' => PARAM_INT,
                'sub2' => PARAM_INT, 'sub3' => PARAM_INT), $question->get_expected_data());
    }

    public function test_is_complete_response() {
        $question = test_question_maker::make_a_matching_question();
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertFalse($question->is_complete_response(array()));
        $this->assertFalse($question->is_complete_response(
                array('sub0' => '1', 'sub1' => '1', 'sub2' => '1', 'sub3' => '0')));
        $this->assertFalse($question->is_complete_response(array('sub1' => '1')));
        $this->assertTrue($question->is_complete_response(
                array('sub0' => '1', 'sub1' => '1', 'sub2' => '1', 'sub3' => '1')));
    }

    public function test_is_gradable_response() {
        $question = test_question_maker::make_a_matching_question();
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertFalse($question->is_gradable_response(array()));
        $this->assertFalse($question->is_gradable_response(
                array('sub0' => '0', 'sub1' => '0', 'sub2' => '0', 'sub3' => '0')));
        $this->assertTrue($question->is_gradable_response(
                array('sub0' => '1', 'sub1' => '0', 'sub2' => '0', 'sub3' => '0')));
        $this->assertTrue($question->is_gradable_response(array('sub1' => '1')));
        $this->assertTrue($question->is_gradable_response(
                array('sub0' => '1', 'sub1' => '1', 'sub2' => '3', 'sub3' => '1')));
    }

    public function test_is_same_response() {
        $question = test_question_maker::make_a_matching_question();
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertTrue($question->is_same_response(
                array(),
                array('sub0' => '0', 'sub1' => '0', 'sub2' => '0', 'sub3' => '0')));

        $this->assertTrue($question->is_same_response(
                array('sub0' => '0', 'sub1' => '0', 'sub2' => '0', 'sub3' => '0'),
                array('sub0' => '0', 'sub1' => '0', 'sub2' => '0', 'sub3' => '0')));

        $this->assertFalse($question->is_same_response(
                array('sub0' => '0', 'sub1' => '0', 'sub2' => '0', 'sub3' => '0'),
                array('sub0' => '1', 'sub1' => '2', 'sub2' => '3', 'sub3' => '1')));

        $this->assertTrue($question->is_same_response(
                array('sub0' => '1', 'sub1' => '2', 'sub2' => '3', 'sub3' => '1'),
                array('sub0' => '1', 'sub1' => '2', 'sub2' => '3', 'sub3' => '1')));

        $this->assertFalse($question->is_same_response(
                array('sub0' => '2', 'sub1' => '2', 'sub2' => '3', 'sub3' => '1'),
                array('sub0' => '1', 'sub1' => '2', 'sub2' => '3', 'sub3' => '1')));
    }

    public function test_grading() {
        $question = test_question_maker::make_a_matching_question();
        $question->start_attempt(new question_attempt_step(), 1);

        $correctresponse = $question->prepare_simulated_post_data(
                                                array('Dog' => 'Mammal',
                                                      'Frog' => 'Amphibian',
                                                      'Toad' => 'Amphibian',
                                                      'Cat' => 'Mammal'));
        $this->assertEquals(array(1, question_state::$gradedright), $question->grade_response($correctresponse));

        $partialresponse = $question->prepare_simulated_post_data(array('Dog' => 'Mammal'));
        $this->assertEquals(array(0.25, question_state::$gradedpartial), $question->grade_response($partialresponse));

        $partiallycorrectresponse = $question->prepare_simulated_post_data(
                                                array('Dog' => 'Mammal',
                                                      'Frog' => 'Insect',
                                                      'Toad' => 'Insect',
                                                      'Cat' => 'Amphibian'));
        $this->assertEquals(array(0.25, question_state::$gradedpartial), $question->grade_response($partiallycorrectresponse));

        $wrongresponse = $question->prepare_simulated_post_data(
                                                array('Dog' => 'Amphibian',
                                                      'Frog' => 'Insect',
                                                      'Toad' => 'Insect',
                                                      'Cat' => 'Amphibian'));
        $this->assertEquals(array(0, question_state::$gradedwrong), $question->grade_response($wrongresponse));
    }

    public function test_get_correct_response() {
        $question = test_question_maker::make_a_matching_question();
        $question->start_attempt(new question_attempt_step(), 1);

        $correct = $question->prepare_simulated_post_data(array('Dog' => 'Mammal',
                                                                'Frog' => 'Amphibian',
                                                                'Toad' => 'Amphibian',
                                                                'Cat' => 'Mammal'));
        $this->assertEquals($correct, $question->get_correct_response());
    }

    public function test_get_question_summary() {
        $match = test_question_maker::make_a_matching_question();
        $match->start_attempt(new question_attempt_step(), 1);
        $qsummary = $match->get_question_summary();
        $this->assertRegExp('/' . preg_quote($match->questiontext, '/') . '/', $qsummary);
        foreach ($match->stems as $stem) {
            $this->assertRegExp('/' . preg_quote($stem, '/') . '/', $qsummary);
        }
        foreach ($match->choices as $choice) {
            $this->assertRegExp('/' . preg_quote($choice, '/') . '/', $qsummary);
        }
    }

    public function test_summarise_response() {
        $match = test_question_maker::make_a_matching_question();
        $match->start_attempt(new question_attempt_step(), 1);

        $summary = $match->summarise_response($match->prepare_simulated_post_data(array('Dog' => 'Amphibian', 'Frog' => 'Mammal')));

        $this->assertRegExp('/Dog -> Amphibian/', $summary);
        $this->assertRegExp('/Frog -> Mammal/', $summary);
    }

    public function test_classify_response() {
        $match = test_question_maker::make_a_matching_question();
        $match->start_attempt(new question_attempt_step(), 1);

        $response = $match->prepare_simulated_post_data(array('Dog' => 'Amphibian', 'Frog' => 'Insect', 'Toad' => '', 'Cat' => ''));
        $this->assertEquals(array(
                    1 => new question_classified_response(2, 'Amphibian', 0),
                    2 => new question_classified_response(3, 'Insect', 0),
                    3 => question_classified_response::no_response(),
                    4 => question_classified_response::no_response(),
                ), $match->classify_response($response));

        $response = $match->prepare_simulated_post_data(array('Dog' => 'Mammal', 'Frog' => 'Amphibian',
                                                              'Toad' => 'Amphibian', 'Cat' => 'Mammal'));
        $this->assertEquals(array(
                    1 => new question_classified_response(1, 'Mammal', 0.25),
                    2 => new question_classified_response(2, 'Amphibian', 0.25),
                    3 => new question_classified_response(2, 'Amphibian', 0.25),
                    4 => new question_classified_response(1, 'Mammal', 0.25),
                ), $match->classify_response($response));
    }

    public function test_classify_response_choice_deleted_after_attempt() {
        $match = test_question_maker::make_a_matching_question();
        $firststep = new question_attempt_step();

        $match->start_attempt($firststep, 1);
        $response = $match->prepare_simulated_post_data(array(
                'Dog' => 'Amphibian', 'Frog' => 'Insect', 'Toad' => '', 'Cat' => 'Mammal'));

        $match = test_question_maker::make_a_matching_question();
        unset($match->stems[4]);
        unset($match->stemsformat[4]);
        unset($match->right[4]);
        $match->apply_attempt_state($firststep);

        $this->assertEquals(array(
                1 => new question_classified_response(2, 'Amphibian', 0),
                2 => new question_classified_response(3, 'Insect', 0),
                3 => question_classified_response::no_response(),
        ), $match->classify_response($response));
    }

    public function test_classify_response_choice_added_after_attempt() {
        $match = test_question_maker::make_a_matching_question();
        $firststep = new question_attempt_step();

        $match->start_attempt($firststep, 1);
        $response = $match->prepare_simulated_post_data(array(
                'Dog' => 'Amphibian', 'Frog' => 'Insect', 'Toad' => '', 'Cat' => 'Mammal'));

        $match = test_question_maker::make_a_matching_question();
        $match->stems[5] = "Snake";
        $match->stemsformat[5] = FORMAT_HTML;
        $match->choices[5] = "Reptile";
        $match->right[5] = 5;
        $match->apply_attempt_state($firststep);

        $this->assertEquals(array(
                1 => new question_classified_response(2, 'Amphibian', 0),
                2 => new question_classified_response(3, 'Insect', 0),
                3 => question_classified_response::no_response(),
                4 => new question_classified_response(1, 'Mammal', 0.20),
        ), $match->classify_response($response));
    }

    public function test_prepare_simulated_post_data() {
        $m = test_question_maker::make_a_matching_question();
        $m->start_attempt(new question_attempt_step(), 1);
        $postdata = $m->prepare_simulated_post_data(array('Dog' => 'Mammal', 'Frog' => 'Amphibian',
                                                          'Toad' => 'Amphibian', 'Cat' => 'Mammal'));
        $this->assertEquals(array(4, 4), $m->get_num_parts_right($postdata));
    }

}
