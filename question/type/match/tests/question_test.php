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

namespace qtype_match;

use question_attempt_step;
use question_classified_response;
use question_display_options;
use question_state;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Unit tests for the matching question definition class.
 *
 * @package   qtype_match
 * @copyright 2009 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \qtype_match_question
 */
final class question_test extends \advanced_testcase {

    public function test_get_expected_data(): void {
        $question = \test_question_maker::make_question('match');
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertEquals(array('sub0' => PARAM_INT, 'sub1' => PARAM_INT,
                'sub2' => PARAM_INT, 'sub3' => PARAM_INT), $question->get_expected_data());
    }

    public function test_is_complete_response(): void {
        $question = \test_question_maker::make_question('match');
        $question->start_attempt(new question_attempt_step(), 1);

        $this->assertFalse($question->is_complete_response(array()));
        $this->assertFalse($question->is_complete_response(
                array('sub0' => '1', 'sub1' => '1', 'sub2' => '1', 'sub3' => '0')));
        $this->assertFalse($question->is_complete_response(array('sub1' => '1')));
        $this->assertTrue($question->is_complete_response(
                array('sub0' => '1', 'sub1' => '1', 'sub2' => '1', 'sub3' => '1')));
    }

    public function test_is_gradable_response(): void {
        $question = \test_question_maker::make_question('match');
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

    public function test_is_same_response(): void {
        $question = \test_question_maker::make_question('match');
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

    public function test_grading(): void {
        $question = \test_question_maker::make_question('match');
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

    public function test_get_correct_response(): void {
        $question = \test_question_maker::make_question('match');
        $question->start_attempt(new question_attempt_step(), 1);

        $correct = $question->prepare_simulated_post_data(array('Dog' => 'Mammal',
                                                                'Frog' => 'Amphibian',
                                                                'Toad' => 'Amphibian',
                                                                'Cat' => 'Mammal'));
        $this->assertEquals($correct, $question->get_correct_response());
    }

    public function test_get_question_summary(): void {
        $match = \test_question_maker::make_question('match');
        $match->start_attempt(new question_attempt_step(), 1);
        $qsummary = $match->get_question_summary();
        $this->assertMatchesRegularExpression('/' . preg_quote($match->questiontext, '/') . '/', $qsummary);
        foreach ($match->stems as $stem) {
            $this->assertMatchesRegularExpression('/' . preg_quote($stem, '/') . '/', $qsummary);
        }
        foreach ($match->choices as $choice) {
            $this->assertMatchesRegularExpression('/' . preg_quote($choice, '/') . '/', $qsummary);
        }
    }

    public function test_summarise_response(): void {
        $match = \test_question_maker::make_question('match');
        $match->start_attempt(new question_attempt_step(), 1);

        $summary = $match->summarise_response($match->prepare_simulated_post_data(array('Dog' => 'Amphibian', 'Frog' => 'Mammal')));

        $this->assertMatchesRegularExpression('/Dog -> Amphibian/', $summary);
        $this->assertMatchesRegularExpression('/Frog -> Mammal/', $summary);
    }

    public function test_classify_response(): void {
        $match = \test_question_maker::make_question('match');
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

    public function test_classify_response_choice_deleted_after_attempt(): void {
        $match = \test_question_maker::make_question('match');
        $firststep = new question_attempt_step();

        $match->start_attempt($firststep, 1);
        $response = $match->prepare_simulated_post_data(array(
                'Dog' => 'Amphibian', 'Frog' => 'Insect', 'Toad' => '', 'Cat' => 'Mammal'));

        $match = \test_question_maker::make_question('match');
        unset($match->stems[4]);
        unset($match->stemformat[4]);
        unset($match->right[4]);
        $match->apply_attempt_state($firststep);

        $this->assertEquals(array(
                1 => new question_classified_response(2, 'Amphibian', 0),
                2 => new question_classified_response(3, 'Insect', 0),
                3 => question_classified_response::no_response(),
        ), $match->classify_response($response));
    }

    public function test_classify_response_choice_added_after_attempt(): void {
        $match = \test_question_maker::make_question('match');
        $firststep = new question_attempt_step();

        $match->start_attempt($firststep, 1);
        $response = $match->prepare_simulated_post_data(array(
                'Dog' => 'Amphibian', 'Frog' => 'Insect', 'Toad' => '', 'Cat' => 'Mammal'));

        $match = \test_question_maker::make_question('match');
        $match->stems[5] = "Snake";
        $match->stemformat[5] = FORMAT_HTML;
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

    public function test_prepare_simulated_post_data(): void {
        $m = \test_question_maker::make_question('match');
        $m->start_attempt(new question_attempt_step(), 1);
        $postdata = $m->prepare_simulated_post_data(array('Dog' => 'Mammal', 'Frog' => 'Amphibian',
                                                          'Toad' => 'Amphibian', 'Cat' => 'Mammal'));
        $this->assertEquals(array(4, 4), $m->get_num_parts_right($postdata));
    }

    /**
     * test_get_question_definition_for_external_rendering
     */
    public function test_get_question_definition_for_external_rendering(): void {
        $question = \test_question_maker::make_question('match');
        $question->start_attempt(new question_attempt_step(), 1);
        $qa = \test_question_maker::get_a_qa($question);
        $displayoptions = new question_display_options();

        $options = $question->get_question_definition_for_external_rendering($qa, $displayoptions);
        $this->assertEquals(1, $options['shufflestems']);
    }

    public function test_validate_can_regrade_with_other_version_ok(): void {
        $m = \test_question_maker::make_question('match');

        $newm = clone($m);

        $this->assertNull($newm->validate_can_regrade_with_other_version($m));
    }

    public function test_validate_can_regrade_with_other_version_bad_stems(): void {
        $m = \test_question_maker::make_question('match');

        $newm = clone($m);
        unset($newm->stems[4]);

        $this->assertEquals(get_string('regradeissuenumstemschanged', 'qtype_match'),
                $newm->validate_can_regrade_with_other_version($m));
    }

    public function test_validate_can_regrade_with_other_version_bad_choices(): void {
        $m = \test_question_maker::make_question('match');

        $newm = clone($m);
        unset($newm->choices[3]);

        $this->assertEquals(get_string('regradeissuenumchoiceschanged', 'qtype_match'),
                $newm->validate_can_regrade_with_other_version($m));
    }

    public function test_update_attempt_state_date_from_old_version_bad(): void {
        $m = \test_question_maker::make_question('match');

        $newm = clone($m);
        $newm->stems = [11 => 'Dog', 12 => 'Frog', 13 => 'Toad', 14 => 'Cat', 15 => 'Hippopotamus'];
        $newm->stemformat = [11 => FORMAT_HTML, 12 => FORMAT_HTML, 13 => FORMAT_HTML, 14 => FORMAT_HTML, 15 => FORMAT_HTML];
        $newm->choices = [11 => 'Mammal', 12 => 'Amphibian', 13 => 'Insect'];
        $newm->right = [11 => 11, 12 => 12, 13 => 12, 14 => 11, 15 => 11];

        $oldstep = new question_attempt_step();
        $oldstep->set_qt_var('_stemorder', '4,1,3,2');
        $oldstep->set_qt_var('_choiceorder', '2,3,1');
        $this->expectExceptionMessage(get_string('regradeissuenumstemschanged', 'qtype_match'));
        $newm->update_attempt_state_data_for_new_version($oldstep, $m);
    }

    public function test_update_attempt_state_date_from_old_version_ok(): void {
        $m = \test_question_maker::make_question('match');

        $newm = clone($m);
        $newm->stems = [11 => 'Dog', 12 => 'Frog', 13 => 'Toad', 14 => 'Cat'];
        $newm->stemformat = [11 => FORMAT_HTML, 12 => FORMAT_HTML, 13 => FORMAT_HTML, 14 => FORMAT_HTML];
        $newm->choices = [11 => 'Mammal', 12 => 'Amphibian', 13 => 'Insect'];
        $newm->right = [11 => 11, 12 => 12, 13 => 12, 14 => 11];

        $oldstep = new question_attempt_step();
        $oldstep->set_qt_var('_stemorder', '4,1,3,2');
        $oldstep->set_qt_var('_choiceorder', '2,3,1');
        $this->assertEquals(['_stemorder' => '14,11,13,12', '_choiceorder' => '12,13,11'],
                $newm->update_attempt_state_data_for_new_version($oldstep, $m));
    }
}
