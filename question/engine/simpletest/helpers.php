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
 * This file contains helper classes for testing the question engine.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../lib.php');


/**
 * Makes some protected methods of question_attempt public to facilitate testing.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_question_attempt extends question_attempt {
    public function add_step($step) {#
        parent::add_step($step);
    }
    public function set_min_fraction($fraction) {
        $this->minfraction = $fraction;
    }
    public function set_behaviour(question_behaviour $behaviour) {
        $this->behaviour = $behaviour;
    }
}


/**
 * Base class for question type test helpers.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class question_test_helper {
    /**
     * @return array of example question names that can be passed as the $which
     * argument of {@link test_question_maker::make_question} when $qtype is
     * this question type.
     */
    abstract public function get_test_questions();
}


/**
 * This class creates questions of various types, which can then be used when
 * testing.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_question_maker {
    const STANDARD_OVERALL_CORRECT_FEEDBACK = 'Well done!';
    const STANDARD_OVERALL_PARTIALLYCORRECT_FEEDBACK = 'Parts, but only parts, of your response are correct.';
    const STANDARD_OVERALL_INCORRECT_FEEDBACK = 'That is not right at all.';

    /** @var array qtype => qtype test helper class. */
    protected static $testhelpers = array();

    /**
     * Just make a question_attempt at a question. Useful for unit tests that
     * need to pass a $qa to methods that call format_text. Probably not safe
     * to use for anything beyond that.
     * @param question_definition $question a question.
     * @param number $maxmark the max mark to set.
     * @return question_attempt the question attempt.
     */
    public function get_a_qa($question, $maxmark = 3) {
        return new question_attempt($question, 13, null, $maxmark);
    }

    /**
     * Initialise the common fields of a question of any type.
     */
    public static function initialise_a_question($q) {
        global $USER;

        $q->id = 0;
        $q->category = 0;
        $q->parent = 0;
        $q->questiontextformat = FORMAT_HTML;
        $q->generalfeedbackformat = FORMAT_HTML;
        $q->defaultmark = 1;
        $q->penalty = 0.3333333;
        $q->length = 1;
        $q->stamp = make_unique_id_code();
        $q->version = make_unique_id_code();
        $q->hidden = 0;
        $q->timecreated = time();
        $q->timemodified = time();
        $q->createdby = $USER->id;
        $q->modifiedby = $USER->id;
    }

    /**
     * Get the test helper class for a particular question type.
     * @param $qtype the question type name, e.g. 'multichoice'.
     * @return question_test_helper the test helper class.
     */
    public static function get_test_helper($qtype) {
        if (array_key_exists($qtype, self::$testhelpers)) {
            return self::$testhelpers[$qtype];
        }

        $file = get_plugin_directory('qtype', $qtype) . '/simpletest/helper.php';
        if (!is_readable($file)) {
            throw new coding_exception('Question type ' . $qtype .
                    ' does not have test helper code.');
        }
        include_once($file);

        $class = 'qtype_' . $qtype . '_test_helper';
        if (!class_exists($class)) {
            throw new coding_exception('Class ' . $class . ' is not defined in ' . $file);
        }

        self::$testhelpers[$qtype] = new $class();
        return self::$testhelpers[$qtype];
    }

    public static function make_question($qtype, $which = null) {
        $helper = self::get_test_helper($qtype);

        $available = $helper->get_test_questions();

        if (is_null($which)) {
            $which = reset($available);
        } else if (!in_array($which, $available)) {
            throw new coding_exception('Example question ' . $which . ' of type ' .
                    $qtype . ' does not exist.');
        }

        $method = "make_{$qtype}_question_{$which}";
        if (!method_exists($helper, $method)) {
            throw new coding_exception('Method ' . $method . ' does not exist on the' .
                    $qtype . ' question type test helper class.');
        }

        return $helper->$method();
    }

    /**
     * Makes a truefalse question with correct answer true, defaultmark 1.
     * @return qtype_truefalse_question
     */
    public static function make_a_truefalse_question() {
        question_bank::load_question_definition_classes('truefalse');
        $tf = new qtype_truefalse_question();
        self::initialise_a_question($tf);
        $tf->name = 'True/false question';
        $tf->questiontext = 'The answer is true.';
        $tf->generalfeedback = 'You should have selected true.';
        $tf->penalty = 1;
        $tf->qtype = question_bank::get_qtype('truefalse');

        $tf->rightanswer = true;
        $tf->truefeedback = 'This is the right answer.';
        $tf->falsefeedback = 'This is the wrong answer.';
        $tf->truefeedbackformat = FORMAT_HTML;
        $tf->falsefeedbackformat = FORMAT_HTML;
        $tf->trueanswerid = 13;
        $tf->falseanswerid = 14;

        return $tf;
    }

    /**
     * Makes a multichoice question with choices 'A', 'B' and 'C' shuffled. 'A'
     * is correct, defaultmark 1.
     * @return qtype_multichoice_single_question
     */
    public static function make_a_multichoice_single_question() {
        question_bank::load_question_definition_classes('multichoice');
        $mc = new qtype_multichoice_single_question();
        self::initialise_a_question($mc);
        $mc->name = 'Multi-choice question, single response';
        $mc->questiontext = 'The answer is A.';
        $mc->generalfeedback = 'You should have selected A.';
        $mc->qtype = question_bank::get_qtype('multichoice');

        $mc->shuffleanswers = 1;
        $mc->answernumbering = 'abc';

        $mc->answers = array(
            13 => new question_answer(13, 'A', 1, 'A is right', FORMAT_HTML),
            14 => new question_answer(14, 'B', -0.3333333, 'B is wrong', FORMAT_HTML),
            15 => new question_answer(15, 'C', -0.3333333, 'C is wrong', FORMAT_HTML),
        );

        return $mc;
    }

    /**
     * Makes a multichoice question with choices 'A', 'B', 'C' and 'D' shuffled.
     * 'A' and 'C' is correct, defaultmark 1.
     * @return qtype_multichoice_multi_question
     */
    public static function make_a_multichoice_multi_question() {
        question_bank::load_question_definition_classes('multichoice');
        $mc = new qtype_multichoice_multi_question();
        self::initialise_a_question($mc);
        $mc->name = 'Multi-choice question, multiple response';
        $mc->questiontext = 'The answer is A and C.';
        $mc->generalfeedback = 'You should have selected A and C.';
        $mc->qtype = question_bank::get_qtype('multichoice');

        $mc->shuffleanswers = 1;
        $mc->answernumbering = 'abc';

        self::set_standard_combined_feedback_fields($mc);

        $mc->answers = array(
            13 => new question_answer(13, 'A', 0.5, 'A is part of the right answer', FORMAT_HTML),
            14 => new question_answer(14, 'B', -1, 'B is wrong', FORMAT_HTML),
            15 => new question_answer(15, 'C', 0.5, 'C is part of the right answer', FORMAT_HTML),
            16 => new question_answer(16, 'D', -1, 'D is wrong', FORMAT_HTML),
        );

        return $mc;
    }

    /**
     * Makes a matching question to classify 'Dog', 'Frog', 'Toad' and 'Cat' as
     * 'Mammal', 'Amphibian' or 'Insect'.
     * defaultmark 1. Stems are shuffled by default.
     * @return qtype_match_question
     */
    public static function make_a_matching_question() {
        question_bank::load_question_definition_classes('match');
        $match = new qtype_match_question();
        self::initialise_a_question($match);
        $match->name = 'Matching question';
        $match->questiontext = 'Classify the animals.';
        $match->generalfeedback = 'Frogs and toads are amphibians, the others are mammals.';
        $match->qtype = question_bank::get_qtype('match');

        $match->shufflestems = 1;

        self::set_standard_combined_feedback_fields($match);

        // Using unset to get 1-based arrays.
        $match->stems = array('', 'Dog', 'Frog', 'Toad', 'Cat');
        $match->stemformat = array('', FORMAT_HTML, FORMAT_HTML, FORMAT_HTML, FORMAT_HTML);
        $match->choices = array('', 'Mammal', 'Amphibian', 'Insect');
        $match->right = array('', 1, 2, 2, 1);
        unset($match->stems[0]);
        unset($match->stemformat[0]);
        unset($match->choices[0]);
        unset($match->right[0]);

        return $match;
    }

    /**
     * Makes a shortanswer question with correct ansewer 'frog', partially
     * correct answer 'toad' and defaultmark 1.
     * @return qtype_shortanswer_question
     */
    public static function make_a_shortanswer_question() {
        question_bank::load_question_definition_classes('shortanswer');
        $sa = new qtype_shortanswer_question();
        self::initialise_a_question($sa);
        $sa->name = 'Short answer question';
        $sa->questiontext = 'Name an amphibian: __________';
        $sa->generalfeedback = 'Generalfeedback: frog or toad would have been OK.';
        $sa->usecase = false;
        $sa->answers = array(
            13 => new question_answer(13, 'frog', 1.0, 'Frog is a very good answer.', FORMAT_HTML),
            14 => new question_answer(14, 'toad', 0.8, 'Toad is an OK good answer.', FORMAT_HTML),
            15 => new question_answer(15, '*', 0.0, 'That is a bad answer.', FORMAT_HTML),
        );
        $sa->qtype = question_bank::get_qtype('shortanswer');

        return $sa;
    }

    /**
     * Makes a truefalse question with correct ansewer true, defaultmark 1.
     * @return qtype_essay_question
     */
    public static function make_an_essay_question() {
        question_bank::load_question_definition_classes('essay');
        $essay = new qtype_essay_question();
        self::initialise_a_question($essay);
        $essay->name = 'Essay question';
        $essay->questiontext = 'Write an essay.';
        $essay->generalfeedback = 'I hope you wrote an interesting essay.';
        $essay->penalty = 0;
        $essay->qtype = question_bank::get_qtype('essay');

        $essay->responseformat = 'editor';
        $essay->responsefieldlines = 15;
        $essay->attachments = 0;
        $essay->graderinfo = '';
        $essay->graderinfoformat = FORMAT_MOODLE;

        return $essay;
    }

    /**
     * Makes a truefalse question with correct ansewer true, defaultmark 1.
     * @return question_truefalse
     */
    public static function make_a_description_question() {
        question_bank::load_question_definition_classes('description');
        $description = new qtype_description_question();
        self::initialise_a_question($description);
        $description->name = 'Description question';
        $description->questiontext = 'This text tells you a bit about the next few questions in this quiz.';
        $description->generalfeedback = 'This is what this section of the quiz should have taught you.';
        $description->qtype = question_bank::get_qtype('description');

        return $description;
    }

    /**
     * Add some standard overall feedback to a question. You need to use these
     * specific feedback strings for the corresponding contains_..._feedback
     * methods in {@link qbehaviour_walkthrough_test_base} to works.
     * @param question_definition $q the question to add the feedback to.
     */
    public static function set_standard_combined_feedback_fields($q) {
        $q->correctfeedback = self::STANDARD_OVERALL_CORRECT_FEEDBACK;
        $q->correctfeedbackformat = FORMAT_HTML;
        $q->partiallycorrectfeedback = self::STANDARD_OVERALL_PARTIALLYCORRECT_FEEDBACK;
        $q->partiallycorrectfeedbackformat = FORMAT_HTML;
        $q->shownumcorrect = true;
        $q->incorrectfeedback = self::STANDARD_OVERALL_INCORRECT_FEEDBACK;
        $q->incorrectfeedbackformat = FORMAT_HTML;
    }
}


/**
 * Helper for tests that need to simulate records loaded from the database.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class testing_db_record_builder {
    public static function build_db_records(array $table) {
        $columns = array_shift($table);
        $records = array();
        foreach ($table as $row) {
            if (count($row) != count($columns)) {
                throw new coding_exception("Row contains the wrong number of fields.");
            }
            $rec = new stdClass();
            foreach ($columns as $i => $name) {
                $rec->$name = $row[$i];
            }
            $records[] = $rec;
        }
        return $records;
    }
}


/**
 * Helper base class for tests that need to simulate records loaded from the
 * database.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_loading_method_test_base extends UnitTestCase {
    public function build_db_records(array $table) {
        return testing_db_record_builder::build_db_records($table);
    }
}


/**
 * Helper base class for tests that walk a question through a sequents of
 * interactions under the control of a particular behaviour.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_walkthrough_test_base extends UnitTestCase {
    /** @var question_display_options */
    protected $displayoptions;
    /** @var question_usage_by_activity */
    protected $quba;
    /** @var unknown_type integer */
    protected $slot;

    public function setUp() {
        $this->displayoptions = new question_display_options();
        $this->quba = question_engine::make_questions_usage_by_activity('unit_test',
                get_context_instance(CONTEXT_SYSTEM));
    }

    public function tearDown() {
        $this->displayoptions = null;
        $this->quba = null;
    }

    protected function start_attempt_at_question($question, $preferredbehaviour, $maxmark = null) {
        $this->quba->set_preferred_behaviour($preferredbehaviour);
        $this->slot = $this->quba->add_question($question, $maxmark);
        $this->quba->start_all_questions();
    }
    protected function process_submission($data) {
        $this->quba->process_action($this->slot, $data);
    }

    protected function manual_grade($comment, $mark) {
        $this->quba->manual_grade($this->slot, $comment, $mark);
    }

    protected function check_current_state($state) {
        $this->assertEqual($this->quba->get_question_state($this->slot), $state, 'Questions is in the wrong state: %s.');
    }

    protected function check_current_mark($mark) {
        if (is_null($mark)) {
            $this->assertNull($this->quba->get_question_mark($this->slot));
        } else {
            if ($mark == 0) {
                // PHP will think a null mark and a mark of 0 are equal,
                // so explicity check not null in this case.
                $this->assertNotNull($this->quba->get_question_mark($this->slot));
            }
            $this->assertWithinMargin($mark, $this->quba->get_question_mark($this->slot),
                    0.000001, 'Expected mark and actual mark differ: %s.');
        }
    }

    /**
     * @param $condition one or more Expectations. (users varargs).
     */
    protected function check_current_output() {
        $html = $this->quba->render_question($this->slot, $this->displayoptions);
        foreach (func_get_args() as $condition) {
            $this->assert($condition, $html);
        }
    }

    protected function get_question_attempt() {
        return $this->quba->get_question_attempt($this->slot);
    }

    protected function get_step_count() {
        return $this->get_question_attempt()->get_num_steps();
    }

    protected function check_step_count($expectednumsteps) {
        $this->assertEqual($expectednumsteps, $this->get_step_count());
    }

    protected function get_step($stepnum) {
        return $this->get_question_attempt()->get_step($stepnum);
    }

    protected function get_contains_question_text_expectation($question) {
        return new PatternExpectation('/' . preg_quote($question->questiontext) . '/');
    }

    protected function get_contains_general_feedback_expectation($question) {
        return new PatternExpectation('/' . preg_quote($question->generalfeedback) . '/');
    }

    protected function get_does_not_contain_correctness_expectation() {
        return new NoPatternExpectation('/class=\"correctness/');
    }

    protected function get_contains_correct_expectation() {
        return new PatternExpectation('/' . preg_quote(get_string('correct', 'question')) . '/');
    }

    protected function get_contains_partcorrect_expectation() {
        return new PatternExpectation('/' . preg_quote(get_string('partiallycorrect', 'question')) . '/');
    }

    protected function get_contains_incorrect_expectation() {
        return new PatternExpectation('/' . preg_quote(get_string('incorrect', 'question')) . '/');
    }

    protected function get_contains_standard_correct_combined_feedback_expectation() {
        return new PatternExpectation('/' . preg_quote(test_question_maker::STANDARD_OVERALL_CORRECT_FEEDBACK) . '/');
    }

    protected function get_contains_standard_partiallycorrect_combined_feedback_expectation() {
        return new PatternExpectation('/' . preg_quote(test_question_maker::STANDARD_OVERALL_PARTIALLYCORRECT_FEEDBACK) . '/');
    }

    protected function get_contains_standard_incorrect_combined_feedback_expectation() {
        return new PatternExpectation('/' . preg_quote(test_question_maker::STANDARD_OVERALL_INCORRECT_FEEDBACK) . '/');
    }

    protected function get_does_not_contain_feedback_expectation() {
        return new NoPatternExpectation('/class="feedback"/');
    }

    protected function get_does_not_contain_num_parts_correct() {
        return new NoPatternExpectation('/class="numpartscorrect"/');
    }

    protected function get_contains_num_parts_correct($num) {
        $a = new stdClass();
        $a->num = $num;
        return new PatternExpectation('/<div class="numpartscorrect">' .
                preg_quote(get_string('yougotnright', 'question', $a)) . '/');
    }

    protected function get_does_not_contain_specific_feedback_expectation() {
        return new NoPatternExpectation('/class="specificfeedback"/');
    }

    protected function get_contains_validation_error_expectation() {
        return new ContainsTagWithAttribute('div', 'class', 'validationerror');
    }

    protected function get_does_not_contain_validation_error_expectation() {
        return new NoPatternExpectation('/class="validationerror"/');
    }

    protected function get_contains_mark_summary($mark) {
        $a = new stdClass();
        $a->mark = format_float($mark, $this->displayoptions->markdp);
        $a->max = format_float($this->quba->get_question_max_mark($this->slot),
                $this->displayoptions->markdp);
        return new PatternExpectation('/' .
                preg_quote(get_string('markoutofmax', 'question', $a)) . '/');
    }

    protected function get_contains_marked_out_of_summary() {
        $max = format_float($this->quba->get_question_max_mark($this->slot),
                $this->displayoptions->markdp);
        return new PatternExpectation('/' .
                preg_quote(get_string('markedoutofmax', 'question', $max)) . '/');
    }

    protected function get_does_not_contain_mark_summary() {
        return new NoPatternExpectation('/<div class="grade">/');
    }

    protected function get_contains_checkbox_expectation($baseattr, $enabled, $checked) {
        $expectedattributes = $baseattr;
        $forbiddenattributes = array();
        $expectedattributes['type'] = 'checkbox';
        if ($enabled === true) {
            $forbiddenattributes['disabled'] = 'disabled';
        } else if ($enabled === false) {
            $expectedattributes['disabled'] = 'disabled';
        }
        if ($checked === true) {
            $expectedattributes['checked'] = 'checked';
        } else if ($checked === false) {
            $forbiddenattributes['checked'] = 'checked';
        }
        return new ContainsTagWithAttributes('input', $expectedattributes, $forbiddenattributes);
    }

    protected function get_contains_mc_checkbox_expectation($index, $enabled = null, $checked = null) {
        return $this->get_contains_checkbox_expectation(array(
                'name' => $this->quba->get_field_prefix($this->slot) . $index,
                'value' => 1,
                ), $enabled, $checked);
    }

    protected function get_contains_radio_expectation($baseattr, $enabled, $checked) {
        $expectedattributes = $baseattr;
        $forbiddenattributes = array();
        $expectedattributes['type'] = 'radio';
        if ($enabled === true) {
            $forbiddenattributes['disabled'] = 'disabled';
        } else if ($enabled === false) {
            $expectedattributes['disabled'] = 'disabled';
        }
        if ($checked === true) {
            $expectedattributes['checked'] = 'checked';
        } else if ($checked === false) {
            $forbiddenattributes['checked'] = 'checked';
        }
        return new ContainsTagWithAttributes('input', $expectedattributes, $forbiddenattributes);
    }

    protected function get_contains_mc_radio_expectation($index, $enabled = null, $checked = null) {
        return $this->get_contains_radio_expectation(array(
                'name' => $this->quba->get_field_prefix($this->slot) . 'answer',
                'value' => $index,
                ), $enabled, $checked);
    }

    protected function get_contains_hidden_expectation($name, $value = null) {
        $expectedattributes = array('type' => 'hidden', 'name' => s($name));
        if (!is_null($value)) {
            $expectedattributes['value'] = s($value);
        }
        return new ContainsTagWithAttributes('input', $expectedattributes);
    }

    protected function get_does_not_contain_hidden_expectation($name, $value = null) {
        $expectedattributes = array('type' => 'hidden', 'name' => s($name));
        if (!is_null($value)) {
            $expectedattributes['value'] = s($value);
        }
        return new DoesNotContainTagWithAttributes('input', $expectedattributes);
    }

    protected function get_contains_tf_true_radio_expectation($enabled = null, $checked = null) {
        return $this->get_contains_radio_expectation(array(
                'name' => $this->quba->get_field_prefix($this->slot) . 'answer',
                'value' => 1,
                ), $enabled, $checked);
    }

    protected function get_contains_tf_false_radio_expectation($enabled = null, $checked = null) {
        return $this->get_contains_radio_expectation(array(
                'name' => $this->quba->get_field_prefix($this->slot) . 'answer',
                'value' => 0,
                ), $enabled, $checked);
    }

    protected function get_contains_cbm_radio_expectation($certainty, $enabled = null, $checked = null) {
        return $this->get_contains_radio_expectation(array(
                'name' => $this->quba->get_field_prefix($this->slot) . '-certainty',
                'value' => $certainty,
                ), $enabled, $checked);
    }

    protected function get_contains_button_expectation($name, $value = null, $enabled = null) {
        $expectedattributes = array(
            'type' => 'submit',
            'name' => $name,
        );
        $forbiddenattributes = array();
        if (!is_null($value)) {
            $expectedattributes['value'] = $value;
        }
        if ($enabled === true) {
            $forbiddenattributes['disabled'] = 'disabled';
        } else if ($enabled === false) {
            $expectedattributes['disabled'] = 'disabled';
        }
        return new ContainsTagWithAttributes('input', $expectedattributes, $forbiddenattributes);
    }

    protected function get_contains_submit_button_expectation($enabled = null) {
        return $this->get_contains_button_expectation(
                $this->quba->get_field_prefix($this->slot) . '-submit', null, $enabled);
    }

    protected function get_tries_remaining_expectation($n) {
        return new PatternExpectation('/' . preg_quote(get_string('triesremaining', 'qbehaviour_interactive', $n)) . '/');
    }

    protected function get_invalid_answer_expectation() {
        return new PatternExpectation('/' . preg_quote(get_string('invalidanswer', 'question')) . '/');
    }

    protected function get_contains_try_again_button_expectation($enabled = null) {
        $expectedattributes = array(
            'type' => 'submit',
            'name' => $this->quba->get_field_prefix($this->slot) . '-tryagain',
        );
        $forbiddenattributes = array();
        if ($enabled === true) {
            $forbiddenattributes['disabled'] = 'disabled';
        } else if ($enabled === false) {
            $expectedattributes['disabled'] = 'disabled';
        }
        return new ContainsTagWithAttributes('input', $expectedattributes, $forbiddenattributes);
    }

    protected function get_does_not_contain_try_again_button_expectation() {
        return new NoPatternExpectation('/name="' .
                $this->quba->get_field_prefix($this->slot) . '-tryagain"/');
    }

    protected function get_contains_select_expectation($name, $choices,
            $selected = null, $enabled = null) {
        $fullname = $this->quba->get_field_prefix($this->slot) . $name;
        return new ContainsSelectExpectation($fullname, $choices, $selected, $enabled);
    }

    protected function get_mc_right_answer_index($mc) {
        $order = $mc->get_order($this->get_question_attempt());
        foreach ($order as $i => $ansid) {
            if ($mc->answers[$ansid]->fraction == 1) {
                return $i;
            }
        }
        $this->fail('This multiple choice question does not seem to have a right answer!');
    }

    protected function get_no_hint_visible_expectation() {
        return new NoPatternExpectation('/class="hint"/');
    }

    protected function get_contains_hint_expectation($hinttext) {
        // Does not currently verify hint text.
        return new ContainsTagWithAttribute('div', 'class', 'hint');
    }
}