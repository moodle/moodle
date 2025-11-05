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
 * These tests walks a question through the interactive behaviour
 *
 * @package    qtype_gapfill
 * @copyright  2012 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace qtype_gapfill;

defined('MOODLE_INTERNAL') || die();

use \qtype_gapfill_test_helper as helper;

global $CFG;

require_once($CFG->dirroot . '/question/type/gapfill/tests/helper.php');

/**
 * Unit tests for the gapfill question type.
 *
 * @copyright  2012 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class walkthrough_test extends \qbehaviour_walkthrough_test_base {
    /**
     * This checks that the bug from line 130 of questiontype is fixed
     * that line should read
     * if (!in_array($a->answer, $question->allanswers,true)) {
     * Without the final true only one draggable will display
     *
     * @covers ::not_exctly_sure()
     */
    public function test_draggable_items() {
        $questiontext = "[1.0] [1.00]";
        $options = [
            'disableregex' => 1,
            'noduplicates' => 0,
            'delimitchars' => '[]',
            'optionsaftertext' => false,
            'singleuse' => false
        ];
        $gapfill = helper::make_question( $questiontext, $options);
        $maxmark = 2;
        $this->start_attempt_at_question($gapfill, 'deferredfeedback', $maxmark);
        $this->check_output_contains('1.0');
        $this->check_output_contains('1.00');
    }
    /**
     * Confirm data for mobile app is in page
     *
     * @covers ::app_connect()
     */
    public function test_app_connect() {
        global $PAGE;

        $questiontext = "The [cat] sat on the [mat]";
        $options = [
            'optionsaftertext' => true,
            'singleuse' => true
        ];
        $gapfill = helper::make_question( $questiontext, $options);
        $renderer = $gapfill->get_renderer($PAGE);
        $html = $renderer->app_connect($gapfill, 'randomstring');
        $this->assertStringContainsString('gapfill_singleuse', $html, ' missing gapfill singleuse tag');
    }
    /**
     * Confirm dropdowns appear
     *
     * @covers ::render_question()
     */
    public function test_dropdowns() {
        $questiontext = "The [cat] sat on the [mat]";
        $options = [
            'optionsaftertext' => true,
            'singleuse' => true,
            'answerdisplay' => 'dropdown'
        ];

        $gapfill = helper::make_question( $questiontext, $options);
        $this->start_attempt_at_question($gapfill, 'immediatefeedback');
        $html = $this->quba->render_question($this->slot, $this->displayoptions);
        $this->assertStringContainsString('select type', $html, ' missing select tags tag');
    }

    /**
     * Deferred feedback q behaviour
     *
     * @covers ::not_entirely_sure()
     */
    public function test_deferred_feedback_unanswered() {

        // Create a gapfill question.
        $gapfill = helper::make_question();
        $maxmark = 2;
        $this->start_attempt_at_question($gapfill, 'deferredfeedback', $maxmark);
        /* Check the initial state. */
        $this->check_current_state(\question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);

        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_no_hint_visible_expectation());

        // Save an  correct response.
        $this->process_submission(array('p1' => '', 'p2' => ''));
        $this->check_step_count(2);
        $this->check_current_state(\question_state::$todo);

        $this->quba->finish_all_questions();
        $this->check_step_count(3);
        $this->check_current_state(\question_state::$gaveup);
        $this->check_current_mark(null);
    }
    /**
     * Deferred q behaviour with correct response
     *
     * @covers ::no_idea()
     */
    public function test_deferred_with_correct() {
        // Create a gapfill question.
        $gapfill = helper::make_question();
        $maxmark = 2;
        $this->start_attempt_at_question($gapfill, 'deferredfeedback', $maxmark);
        // Check the initial state.
        $this->check_current_state(\question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);

        // Save an  correct response.
        $this->process_submission(array('p1' => 'cat', 'p2' => 'mat'));
        $this->check_step_count(2);
        $this->check_current_state(\question_state::$complete);

        $this->quba->finish_all_questions();
        $this->check_step_count(3);
        $this->check_current_state(\question_state::$gradedright);
        $this->check_current_mark(2);
        $this->quba->finish_all_questions();
    }
    /**
     * Deferred feedback q behaviour
     * with incorrect response
     *
     * @covers ::not_entirely_sure()
     */
    public function test_deferred_with_incorrect() {

        // Create a gapfill question.
        $gapfill = helper::make_question();
        $maxmark = 2;
        $this->start_attempt_at_question($gapfill, 'deferredfeedback', $maxmark);
        // Check the initial state.
        $this->check_current_state(\question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);

        // Save an  correct response.
        $this->process_submission(array('p1' => 'dog', 'p2' => 'cat'));
        $this->check_step_count(2);
        $this->check_current_state(\question_state::$complete);

        $this->quba->finish_all_questions();
        $this->check_step_count(3);
        $this->check_current_state(\question_state::$gradedwrong);
        $this->check_current_mark(0);
    }
    /**
     * Tests a feature introduced with Gapfill 1.8 where
     * the | operator will be recognised as a separator for
     * multiple options when regex is turned off. Useful where
     * the answers contain characters considered special by
     * the regex parser.
     * @covers ::not_entirely_sure()
     */
    public function test_no_regex_or() {
        $questiontext = "A programming question with multiple
                correct answers to a single field
                [getSize();| printSize();| 7/2]. The handling of
                | is done  with regex disabled so the (), ; and / cause
                no problems";

        $options = [
            'disableregex' => 1,
        ];

        $gapfill = helper::make_question( $questiontext, $options);
        $maxmark = 1;
            $this->start_attempt_at_question($gapfill, 'deferredfeedback', $maxmark);
        // Check the initial state.
        $this->check_current_state(\question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);
              $this->process_submission(array('p1' => 'getSize();'));
        $this->quba->finish_all_questions();
        $this->check_step_count(3);
        $this->check_current_state(\question_state::$gradedright);
        $this->check_current_mark(1);
    }
    /**
     * Deferred q behaviour with partially correct response
     *
     * @covers ::no_idea()
     */
    public function test_deferred_with_partially_correct() {

        // Create a gapfill question.
        $gapfill = helper::make_question();
        $maxmark = 2;
        $this->start_attempt_at_question($gapfill, 'deferredfeedback', $maxmark);
        // Check the initial state.
        $this->check_current_state(\question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);

        // Save an  correct response.
        $this->process_submission(array('p1' => 'cat', 'p2' => 'dog'));
        $this->check_step_count(2);
        $this->check_current_state(\question_state::$complete);

        $this->quba->finish_all_questions();
        $this->check_step_count(3);
        $this->check_current_state(\question_state::$gradedpartial);
        $this->check_current_mark(1);
    }
    /**
     * Deferred q behaviour with blank/empty gaps
     *
     * @covers ::no_idea()
     */
    public function test_deferred_with_blanks() {
        // Create a gapfill question.
        $questiontext = "The [cat] sat on the [mat]";
        $gapfill = helper::make_question( $questiontext);
        $maxmark = 2;
        $this->start_attempt_at_question($gapfill, 'deferredfeedback', $maxmark);
        // Check the initial state.
        $this->check_current_state(\question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);
        $this->process_submission(array('p1' => 'cat', 'p2' => ''));
        $this->quba->finish_all_questions();
        $this->check_step_count(3);
        $this->check_current_state(\question_state::$gradedpartial);
        $this->check_current_mark(1);
    }
    /**
     * Testing extended characters, including a word that ends with an accent
     * there were issues using strtolower which only works with plain ascci.
     * This has been replaced with Moodles own core_text::strtolower($answergiven,'UTF-8');
     * A full test would include regex on and off and case sensitivity on and off
     *
     * @covers  ::all_grading_methods()
     */
    public function test_extended_characters() {

        $questiontext = "Moscow is the capital of [Россия], The French word for boy is [garçon]. A word that
                ends with an accent is [andrà]  ";
        $gapfill = helper::make_question( $questiontext);
        $maxmark = 3;
        $this->start_attempt_at_question($gapfill, 'deferredfeedback', $maxmark);
        // Check the initial state.
        $this->check_current_state(\question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);
        $this->process_submission(array('p1' => 'Россия', 'p2' => 'garçon', 'p3' => 'andrà' ));
        $this->quba->finish_all_questions();
        $this->check_step_count(3);
        $this->check_current_state(\question_state::$gradedright);
        $this->check_current_mark(3);
    }
    /**
     * Interactive q behaviour with correct responses
     *
     * @covers ::no_idea()
     */
    public function test_interactive_with_correct() {

        // Create a gapfill question.
        $gapfill = helper::make_question();
        $maxmark = 2;
        $this->start_attempt_at_question($gapfill, 'interactive', $maxmark);

        // Check the initial state.
        $this->check_current_state(\question_state::$todo);
        $this->check_current_mark(null);

        $this->check_step_count(1);

        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_no_hint_visible_expectation());

        // Save a correct response.
        $this->process_submission(array('p0' => 'cat', 'p1' => 'mat'));
        $this->check_step_count(2);

        $this->check_current_state(\question_state::$todo);

        $this->check_current_output(
                $this->get_contains_marked_out_of_summary(),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_no_hint_visible_expectation());

        // Submit saved response.
        $this->process_submission(array('-submit' => 1, 'p1' => 'cat', 'p2' => 'mat'));
        $this->check_step_count(3);
        // Verify.
        $this->check_current_state(\question_state::$gradedright);

        $this->check_current_output(
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_no_hint_visible_expectation());

        $this->check_current_mark(2);
        // Finish the attempt.
        $this->quba->finish_all_questions();
        $this->check_current_state(\question_state::$gradedright);
    }
    /**
     * suppressing linting
     *
     * @covers ::no_idea()
     */
    public function test_interactive_wildcard_with_correct() {
        // Create a gapfill question.
        $questiontext = " The [cat|dog] sat on the [mat] ";
        $gapfill = helper::make_question($questiontext);
        $maxmark = 2;

        $this->start_attempt_at_question($gapfill, 'interactive', $maxmark);

        $this->check_current_output(
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_no_hint_visible_expectation());

        // Check the initial state.
        $this->check_current_state(\question_state::$todo);

        $this->check_step_count(1);

        // Save a  correct response.
        $this->process_submission(array('p0' => 'cat', 'p1' => 'mat'));
        $this->check_step_count(2);

        $this->check_current_output(
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_no_hint_visible_expectation());

        $this->check_current_state(\question_state::$todo);

        /*There was a word boundary bug in the regex previously that has been addressed by adding
        *a leading \b( and trailing )\b where the | character is found in the gap. This could be
        *checked further by processing adog as an answer. acat and doga would have been spotted previously
        *because of the leading ^ and trailing $ in the regex */
        $this->process_submission(array('-submit' => 1, 'p1' => 'catty', 'p2' => 'mat'));
        $this->check_step_count(3);

        // Verify.
        $this->quba->finish_all_questions();
        $this->check_current_state(\question_state::$gradedpartial);

        $this->check_current_mark(1);
        $this->start_attempt_at_question($gapfill, 'interactive', $maxmark);

        // Check the initial state.
        $this->check_current_state(\question_state::$todo);

        $this->check_step_count(1);
        // Submit correct resonse.
        $this->process_submission(array('-submit' => 1, 'p1' => 'cat', 'p2' => 'mat'));
        $this->check_step_count(2);

        // Verify.
        $this->quba->finish_all_questions();
        $this->check_current_state(\question_state::$gradedright);
        $this->check_current_mark(2);

        // Finish the attempt.
    }

    /**
     * suppressing linting
     *
     * @covers ::no_idea()
     */
    public function test_disableregex() {
        $questiontext = 'for([$i]=0;$<10;$i++)';
        $options = [
            'disableregex' => 1,
        ];
        $gapfill = helper::make_question($questiontext, $options);
        $gapstofill = count($gapfill->answers);
        $this->start_attempt_at_question($gapfill, 'interactive', $gapstofill);

        $this->check_current_state(\question_state::$todo);
        $this->check_current_mark(null);
        $this->check_step_count(1);
        $this->process_submission(array('-submit' => 1, 'p1' => '$i'));

        $this->check_step_count(2);

        $this->quba->finish_all_questions();

        $this->check_current_state(\question_state::$gradedright);
        $this->check_current_mark(1);
        $this->quba->finish_all_questions();
    }
    /**
     * suppressing linting
     *
     * @covers ::no_idea()
     */
    public function test_interactive_discard_duplicates() {
        /* this is for the scenario where you have multiple fields
         * and each field could take any value. The marking is designed
         * to asssure that the student cannot get credited more than once
         * for each value, i.e. so if the answer is gold,silver, bronze
         * they cannot get 3 marks by entereing gold, gold and gold
         */

        /* Create a gapfill question and set noduplicates to true */
        $questiontext = '
What are the colors of the Olympic medals?

[gold|silver|bronze]
[gold|silver|bronze]
[gold|silver|bronze]  ';

        $options = [
            'disableregex' => 1,
            'noduplicates' => 1,
        ];
        /* answer with duplicate values, only one of each duplicate should get a mark */
        $submission = array('-submit' => 1, 'p1' => 'gold', 'p2' => 'silver', 'p3' => 'silver');

        $gapfill = helper::make_question( $questiontext, $options);
        $gapstofill = count($gapfill->answers);

        $this->start_attempt_at_question($gapfill, 'interactive', $gapstofill);

        // Check the initial state.
        $this->check_current_state(\question_state::$todo);
        $this->check_step_count(1);

        $this->check_current_output(
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_no_hint_visible_expectation());

        $this->check_current_mark(null);

        // Save a correct response.
        $this->process_submission($submission);

        $this->quba->finish_all_questions();
        $this->check_current_state(\question_state::$gradedpartial);

        $this->check_current_mark(2);
        $this->check_step_count(2);
    }
    /**
     * suppressing linting
     *
     * @covers ::no_idea()
     */
    public function test_no_duplicate_draggables() {
        $qtext = 'Bicycles have [wheels]. Cars have [wheels|engines].';
        $gapfill = helper::make_question($qtext);
        $gapstofill = count($gapfill->answers);

        $this->start_attempt_at_question($gapfill, 'interactive', $gapstofill);
        // Confirm draggables are unique, i.e. wheels appears only once.
        $this->assertEquals(2, count($gapfill->allanswers));
    }
    /**
     * suppressing linting
     *
     * @covers ::no_idea()
     */
    public function test_get_letter_hints() {
        $gapfill = helper::make_question();
        $gapstofill = count($gapfill->answers);

        $gapfill->hints = [
            new \question_hint(1, 'This is the first hint.', FORMAT_HTML),
            new \question_hint(2, 'This is the second hint.', FORMAT_HTML),
        ];
        $this->start_attempt_at_question($gapfill, 'interactive', $gapstofill);
        $this->process_submission([ '-submit' => 1, 'p1' => 'cat', 'p2' => 'cat']);
        $this->process_submission(array('-tryagain' => 1));
        $qa = $this->quba->get_question_attempt($this->slot);
        /*normally lots of things in inputattributes */
        $inputattributes = [];
        $rightanswer = 'cat';
        $currentanswer = '';
        $hint = $gapfill->get_letter_hints($qa, $inputattributes, $rightanswer, $currentanswer);
        /*The hint is the first letter of the correct answer */
        $this->assertEquals($hint['value'], 'c');
        $this->process_submission([ '-submit' => 1, 'p1' => 'cat', 'p2' => 'cat']);
        $this->process_submission(array('-tryagain' => 1));
        $hint = $gapfill->get_letter_hints($qa, $inputattributes, $rightanswer, $currentanswer);
        /* add another letter of the correct answer */
        $this->assertEquals($hint['value'], 'ca');
    }
    /**
     * suppressing linting
     *
     * @covers ::no_idea()
     */
    public function test_interactive_grade_for_blank() {
        /* this is for the scenario where you have multiple fields
         * and each field could take any value. The marking is designed
         * to asssure that the student cannot get credited more than once
         * for each value, i.e. so if the answer is gold,silver, bronze
         * they cannot get 3 marks by entereing gold, gold and gold
         */

        /* Create a gapfill question that gives a mark where one response
         * is designed to be blank, i.e. [!!] */
        $questiontext = '
 [one] sat on the [two] [!!] ';

        $gapfill = helper::make_question( $questiontext);
        $gapstofill = count($gapfill->answers);

        $this->start_attempt_at_question($gapfill, 'interactive', $gapstofill);

        /* answer with duplicate values, only one of each duplicate should get a mark */
        /* save answer */
        $submission = array('p1' => 'one', 'p2' => 'two', 'p3' => '');

        // Check the initial state.
        $this->check_current_state(\question_state::$todo);
        $this->check_step_count(1);
        // Save a  correct response.
        $this->process_submission($submission);

        $this->check_current_output(
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_validation_error_expectation()
                , $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_no_hint_visible_expectation());

        $this->check_current_mark(null);

        // Save a  correct response.
        $submission = array('-submit' => 1, 'p1' => 'one', 'p2' => 'two', 'p3' => '');

        $this->process_submission($submission);
        $this->check_current_mark(3);
        $this->check_current_state(\question_state::$gradedright);

        /* start again but put a value in the field expecting a blank */
        $this->start_attempt_at_question($gapfill, 'interactive', $gapstofill);
        $submission = array('p1' => 'one', 'p2' => 'two', 'p3' => "three");
        $this->process_submission($submission);
        $this->check_step_count(2);

        $this->check_current_mark(null);
        $this->check_current_state(\question_state::$todo);

        $this->check_current_output(
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_no_hint_visible_expectation());

        $submission = array('-submit' => 1, 'p1' => 'one', 'p2' => 'two', 'p3' => "three");
        $this->process_submission($submission);
        $this->check_current_mark(2);
        $this->check_current_state(\question_state::$gradedpartial);

        $this->check_step_count(3);

        $this->quba->finish_all_questions();
    }
    /**
     * suppressing linting
     *
     * @covers ::no_idea()
     */
    public function test_matching_divs() {
        $questiontext = "The [cat] sat on the [mat]";
        // Defaults to optionsaftertext being false.
        $gapfill = helper::make_question($questiontext);
        $this->start_attempt_at_question($gapfill, 'immediatefeedback');
        $html = $this->quba->render_question($this->slot, $this->displayoptions);
        $divstarts = substr_count($html, "<div");
        $divends = substr_count($html, "</div>");
        $this->assertEquals($divstarts, $divends, ' Mismatch in opening and closing div tags');
        $options = [
            'disableregex' => 1,
            'optionsaftertext' => true,
        ];
        $gapfill = helper::make_question($questiontext, $options);
        $this->start_attempt_at_question($gapfill, 'immediatefeedback');
        $html = $this->quba->render_question($this->slot, $this->displayoptions);
        $divstarts = substr_count($html, "<div");
        $divends = substr_count($html, "</div>");
        $this->assertEquals($divstarts, $divends, ' Mismatch in opening and closing div tags');
    }
    /**
     * suppressing linting
     *
     * @covers ::no_idea()
     */
    public function test_get_aftergap_text() {
        $questiontext = "The [cat] sat on the [mat]";
        $gapfill = helper::make_question( $questiontext);
        $maxmark = 2;
        $this->start_attempt_at_question($gapfill, 'immediatefeedback', $maxmark);
        $this->process_submission(array('-submit' => 1, 'p1' => 'cat', 'p2' => 'dog'));
        $html = $this->quba->render_question($this->slot, $this->displayoptions);
        $this->assertStringContainsString("[mat]", $html );

        $gapfill = helper::make_question( $questiontext);
        $maxmark = 2;
        $this->start_attempt_at_question($gapfill, 'immediatefeedback', $maxmark);
        $this->process_submission(array('-submit' => 1, 'p1' => 'cat', 'p2' => 'mat'));
        $html = $this->quba->render_question($this->slot, $this->displayoptions);
        $this->assertStringNotContainsString("[mat]", $html );

    }
    /**
     * suppressing linting
     *
     * @covers ::no_idea()
     */
    public function test_deferred_grade_for_blank() {
        /* this is for the scenario where you have multiple fields
         * and each field could take any value. The marking is designed
         * to asssure that the student cannot get credited more than once
         * for each value, i.e. so if the answer is gold,silver, bronze
         * they cannot get 3 marks by entereing gold, gold and gold
         */

        /* Create a gapfill question that gives a mark where one response
         * is designed to be blank, i.e. [!!] */
        $questiontext = '
 [one] sat on the [two] [!!] ';

        $gapfill = helper::make_question( $questiontext);
        $gapstofill = count($gapfill->answers);

        $this->start_attempt_at_question($gapfill, 'deferredfeedback', $gapstofill);
        /* A mark for a blank submission where the gap is [!!] */
        $submission = array('p1' => 'one', 'p2' => 'two', 'p3' => '');

        // Check the initial state.
        $this->check_current_state(\question_state::$todo);
        $this->check_step_count(1);
        // Save a  correct response.
        $this->process_submission($submission);

        $this->check_current_output(
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_does_not_contain_feedback_expectation(),
                $this->get_does_not_contain_validation_error_expectation(),
                $this->get_does_not_contain_try_again_button_expectation(),
                $this->get_no_hint_visible_expectation());

        $this->process_submission(array('-finish' => 1));

        $this->check_current_mark(3);
        $this->check_current_state(\question_state::$gradedright);
        $this->quba->finish_all_questions();
    }
    /**
     * suppressing linting
     *
     * @covers ::no_idea()
     */
    public function test_immediatefeedback_with_correct() {

        // Create a gapfill question.
        $gapfill = helper::make_question();
        $maxmark = 2;

        $this->start_attempt_at_question($gapfill, 'immediatefeedback', $maxmark);

        // Check the initial state.
        $this->check_current_state(\question_state::$todo);

        $this->check_step_count(1);

        // Save a  correct response.
        $this->process_submission(array('p0' => 'cat', 'p1' => 'cat'));
        $this->check_step_count(2);
        $this->check_current_mark(null);

        $this->check_current_state(\question_state::$todo);
        // Submit saved response.
        $this->process_submission(array('-submit' => 1, 'p1' => 'cat', 'p2' => 'mat'));
        $this->check_step_count(3);
        // Verify.
        $this->quba->finish_all_questions();
        $this->check_current_state(\question_state::$gradedright);

        $this->check_current_mark(2);
        // Finish the attempt.
    }
    /**
     * suppressing linting
     *
     * @covers ::no_idea()
     */
    public function test_get_gapsize() {
        $gapfill = helper::make_question( "");
        $this->assertEquals($gapfill->get_size("one"), 3);
        $this->assertEquals($gapfill->get_size("one|twleve"), 6);
    }
}
