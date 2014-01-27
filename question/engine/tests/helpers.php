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

global $CFG;
require_once(dirname(__FILE__) . '/../lib.php');


/**
 * Makes some protected methods of question_attempt public to facilitate testing.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_question_attempt extends question_attempt {
    public function add_step(question_attempt_step $step) {
        parent::add_step($step);
    }
    public function set_min_fraction($fraction) {
        $this->minfraction = $fraction;
    }
    public function set_max_fraction($fraction) {
        $this->maxfraction = $fraction;
    }
    public function set_behaviour(question_behaviour $behaviour) {
        $this->behaviour = $behaviour;
    }
}


/**
 * Test subclass to allow access to some protected data so that the correct
 * behaviour can be verified.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_question_engine_unit_of_work extends question_engine_unit_of_work {
    public function get_modified() {
        return $this->modified;
    }

    public function get_attempts_added() {
        return $this->attemptsadded;
    }

    public function get_attempts_modified() {
        return $this->attemptsmodified;
    }

    public function get_steps_added() {
        return $this->stepsadded;
    }

    public function get_steps_modified() {
        return $this->stepsmodified;
    }

    public function get_steps_deleted() {
        return $this->stepsdeleted;
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

    /**
     * Set up a form to create a question in $cat. This method also sets cat and contextid on $questiondata object.
     * @param object $cat the category
     * @param object $questiondata form initialisation requires question data.
     * @return moodleform
     */
    public static function get_question_editing_form($cat, $questiondata) {
        $catcontext = context::instance_by_id($cat->contextid, MUST_EXIST);
        $contexts = new question_edit_contexts($catcontext);
        $dataforformconstructor = new stdClass();
        $dataforformconstructor->qtype = $questiondata->qtype;
        $dataforformconstructor->contextid = $questiondata->contextid = $catcontext->id;
        $dataforformconstructor->category = $questiondata->category = $cat->id;
        $dataforformconstructor->formoptions = new stdClass();
        $dataforformconstructor->formoptions->canmove = true;
        $dataforformconstructor->formoptions->cansaveasnew = true;
        $dataforformconstructor->formoptions->canedit = true;
        $dataforformconstructor->formoptions->repeatelements = true;
        $qtype = question_bank::get_qtype($questiondata->qtype);
        return  $qtype->create_editing_form('question.php', $dataforformconstructor, $cat, $contexts, true);
    }
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
    const STANDARD_OVERALL_PARTIALLYCORRECT_FEEDBACK =
        'Parts, but only parts, of your response are correct.';
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
    public static function get_a_qa($question, $maxmark = 3) {
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

    public static function initialise_question_data($qdata) {
        global $USER;

        $qdata->id = 0;
        $qdata->category = 0;
        $qdata->contextid = 0;
        $qdata->parent = 0;
        $qdata->questiontextformat = FORMAT_HTML;
        $qdata->generalfeedbackformat = FORMAT_HTML;
        $qdata->defaultmark = 1;
        $qdata->penalty = 0.3333333;
        $qdata->length = 1;
        $qdata->stamp = make_unique_id_code();
        $qdata->version = make_unique_id_code();
        $qdata->hidden = 0;
        $qdata->timecreated = time();
        $qdata->timemodified = time();
        $qdata->createdby = $USER->id;
        $qdata->modifiedby = $USER->id;
        $qdata->hints = array();
    }

    /**
     * Get the test helper class for a particular question type.
     * @param $qtype the question type name, e.g. 'multichoice'.
     * @return question_test_helper the test helper class.
     */
    public static function get_test_helper($qtype) {
        global $CFG;

        if (array_key_exists($qtype, self::$testhelpers)) {
            return self::$testhelpers[$qtype];
        }

        $file = core_component::get_plugin_directory('qtype', $qtype) . '/tests/helper.php';
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

    /**
     * Call a method on a qtype_{$qtype}_test_helper class and return the result.
     *
     * @param string $methodtemplate e.g. 'make_{qtype}_question_{which}';
     * @param string $qtype the question type to get a test question for.
     * @param string $which one of the names returned by the get_test_questions
     *      method of the relevant qtype_{$qtype}_test_helper class.
     * @param unknown_type $which
     */
    protected static function call_question_helper_method($methodtemplate, $qtype, $which = null) {
        $helper = self::get_test_helper($qtype);

        $available = $helper->get_test_questions();

        if (is_null($which)) {
            $which = reset($available);
        } else if (!in_array($which, $available)) {
            throw new coding_exception('Example question ' . $which . ' of type ' .
                $qtype . ' does not exist.');
        }

        $method = str_replace(array('{qtype}', '{which}'),
            array($qtype,    $which), $methodtemplate);

        if (!method_exists($helper, $method)) {
            throw new coding_exception('Method ' . $method . ' does not exist on the ' .
                $qtype . ' question type test helper class.');
        }

        return $helper->$method();
    }

    /**
     * Question types can provide a number of test question defintions.
     * They do this by creating a qtype_{$qtype}_test_helper class that extends
     * question_test_helper. The get_test_questions method returns the list of
     * test questions available for this question type.
     *
     * @param string $qtype the question type to get a test question for.
     * @param string $which one of the names returned by the get_test_questions
     *      method of the relevant qtype_{$qtype}_test_helper class.
     * @return question_definition the requested question object.
     */
    public static function make_question($qtype, $which = null) {
        return self::call_question_helper_method('make_{qtype}_question_{which}',
            $qtype, $which);
    }

    /**
     * Like {@link make_question()} but returns the datastructure from
     * get_question_options instead of the question_definition object.
     *
     * @param string $qtype the question type to get a test question for.
     * @param string $which one of the names returned by the get_test_questions
     *      method of the relevant qtype_{$qtype}_test_helper class.
     * @return stdClass the requested question object.
     */
    public static function get_question_data($qtype, $which = null) {
        return self::call_question_helper_method('get_{qtype}_question_data_{which}',
            $qtype, $which);
    }

    /**
     * Like {@link make_question()} but returns the data what would be saved from
     * the question editing form instead of the question_definition object.
     *
     * @param string $qtype the question type to get a test question for.
     * @param string $which one of the names returned by the get_test_questions
     *      method of the relevant qtype_{$qtype}_test_helper class.
     * @return stdClass the requested question object.
     */
    public static function get_question_form_data($qtype, $which = null) {
        return self::call_question_helper_method('get_{qtype}_question_form_data_{which}',
            $qtype, $which);
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
        $essay->responserequired = 1;
        $essay->responsefieldlines = 15;
        $essay->attachments = 0;
        $essay->attachmentsrequired = 0;
        $essay->responsetemplate = '';
        $essay->responsetemplateformat = FORMAT_MOODLE;
        $essay->graderinfo = '';
        $essay->graderinfoformat = FORMAT_MOODLE;

        return $essay;
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

    /**
     * Add some standard overall feedback to a question's form data.
     */
    public static function set_standard_combined_feedback_form_data($form) {
        $form->correctfeedback = array('text' => self::STANDARD_OVERALL_CORRECT_FEEDBACK,
                                    'format' => FORMAT_HTML);
        $form->partiallycorrectfeedback = array('text' => self::STANDARD_OVERALL_PARTIALLYCORRECT_FEEDBACK,
                                             'format' => FORMAT_HTML);
        $form->shownumcorrect = true;
        $form->incorrectfeedback = array('text' => self::STANDARD_OVERALL_INCORRECT_FEEDBACK,
                                    'format' => FORMAT_HTML);
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
abstract class data_loading_method_test_base extends advanced_testcase {
    public function build_db_records(array $table) {
        return testing_db_record_builder::build_db_records($table);
    }
}


abstract class question_testcase extends advanced_testcase {

    public function assert($expectation, $compare, $notused = '') {

        if (get_class($expectation) === 'question_pattern_expectation') {
            $this->assertRegExp($expectation->pattern, $compare,
                    'Expected regex ' . $expectation->pattern . ' not found in ' . $compare);
            return;

        } else if (get_class($expectation) === 'question_no_pattern_expectation') {
            $this->assertNotRegExp($expectation->pattern, $compare,
                    'Unexpected regex ' . $expectation->pattern . ' found in ' . $compare);
            return;

        } else if (get_class($expectation) === 'question_contains_tag_with_attributes') {
            $this->assertTag(array('tag'=>$expectation->tag, 'attributes'=>$expectation->expectedvalues), $compare,
                    'Looking for a ' . $expectation->tag . ' with attributes ' . html_writer::attributes($expectation->expectedvalues) . ' in ' . $compare);
            foreach ($expectation->forbiddenvalues as $k=>$v) {
                $attr = $expectation->expectedvalues;
                $attr[$k] = $v;
                $this->assertNotTag(array('tag'=>$expectation->tag, 'attributes'=>$attr), $compare,
                        $expectation->tag . ' had a ' . $k . ' attribute that should not be there in ' . $compare);
            }
            return;

        } else if (get_class($expectation) === 'question_contains_tag_with_attribute') {
            $attr = array($expectation->attribute=>$expectation->value);
            $this->assertTag(array('tag'=>$expectation->tag, 'attributes'=>$attr), $compare,
                    'Looking for a ' . $expectation->tag . ' with attribute ' . html_writer::attributes($attr) . ' in ' . $compare);
            return;

        } else if (get_class($expectation) === 'question_does_not_contain_tag_with_attributes') {
            $this->assertNotTag(array('tag'=>$expectation->tag, 'attributes'=>$expectation->attributes), $compare,
                    'Unexpected ' . $expectation->tag . ' with attributes ' . html_writer::attributes($expectation->attributes) . ' found in ' . $compare);
            return;

        } else if (get_class($expectation) === 'question_contains_select_expectation') {
            $tag = array('tag'=>'select', 'attributes'=>array('name'=>$expectation->name),
                'children'=>array('count'=>count($expectation->choices)));
            if ($expectation->enabled === false) {
                $tag['attributes']['disabled'] = 'disabled';
            } else if ($expectation->enabled === true) {
                // TODO
            }
            foreach(array_keys($expectation->choices) as $value) {
                if ($expectation->selected === $value) {
                    $tag['child'] = array('tag'=>'option', 'attributes'=>array('value'=>$value, 'selected'=>'selected'));
                } else {
                    $tag['child'] = array('tag'=>'option', 'attributes'=>array('value'=>$value));
                }
            }

            $this->assertTag($tag, $compare, 'expected select not found in ' . $compare);
            return;

        } else if (get_class($expectation) === 'question_check_specified_fields_expectation') {
            $expect = (array)$expectation->expect;
            $compare = (array)$compare;
            foreach ($expect as $k=>$v) {
                if (!array_key_exists($k, $compare)) {
                    $this->fail("Property $k does not exist");
                }
                if ($v != $compare[$k]) {
                    $this->fail("Property $k is different");
                }
            }
            $this->assertTrue(true);
            return;

        } else if (get_class($expectation) === 'question_contains_tag_with_contents') {
            $this->assertTag(array('tag'=>$expectation->tag, 'content'=>$expectation->content), $compare,
                    'Looking for a ' . $expectation->tag . ' with content ' . $expectation->content . ' in ' . $compare);
            return;
        }

        throw new coding_exception('Unknown expectiontion:'.get_class($expectation));
    }
}


class question_contains_tag_with_contents {
    public $tag;
    public $content;
    public $message;

    public function __construct($tag, $content, $message = '') {
        $this->tag = $tag;
        $this->content = $content;
        $this->message = $message;
    }

}

class question_check_specified_fields_expectation {
    public $expect;
    public $message;

    function __construct($expected, $message = '') {
        $this->expect = $expected;
        $this->message = $message;
    }
}


class question_contains_select_expectation {
    public $name;
    public $choices;
    public $selected;
    public $enabled;
    public $message;

    public function __construct($name, $choices, $selected = null, $enabled = null, $message = '') {
        $this->name = $name;
        $this->choices = $choices;
        $this->selected = $selected;
        $this->enabled = $enabled;
        $this->message = $message;
    }
}


class question_does_not_contain_tag_with_attributes {
    public $tag;
    public $attributes;
    public $message;

    public function __construct($tag, $attributes, $message = '') {
        $this->tag = $tag;
        $this->attributes = $attributes;
        $this->message = $message;
    }
}


class question_contains_tag_with_attribute {
    public $tag;
    public $attribute;
    public $value;
    public $message;

    public function __construct($tag, $attribute, $value, $message = '') {
        $this->tag = $tag;
        $this->attribute = $attribute;
        $this->value = $value;
        $this->message = $message;
    }
}


class question_contains_tag_with_attributes {
    public $tag;
    public $expectedvalues = array();
    public $forbiddenvalues = array();
    public $message;

    public function __construct($tag, $expectedvalues, $forbiddenvalues=array(), $message = '') {
        $this->tag = $tag;
        $this->expectedvalues = $expectedvalues;
        $this->forbiddenvalues = $forbiddenvalues;
        $this->message = $message;
    }
}


class question_pattern_expectation {
    public $pattern;
    public $message;

    public function __construct($pattern, $message = '') {
        $this->pattern = $pattern;
        $this->message = $message;
    }
}


class question_no_pattern_expectation {
    public $pattern;
    public $message;

    public function __construct($pattern, $message = '') {
        $this->pattern = $pattern;
        $this->message = $message;
    }
}


/**
 * Helper base class for tests that walk a question through a sequents of
 * interactions under the control of a particular behaviour.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qbehaviour_walkthrough_test_base extends question_testcase {
    /** @var question_display_options */
    protected $displayoptions;
    /** @var question_usage_by_activity */
    protected $quba;
    /** @var integer */

    protected $slot;
    /**
     * @var string after {@link render()} has been called, this contains the
     * display of the question in its current state.
     */
    protected $currentoutput = '';

    protected function setUp() {
        parent::setUp();
        $this->resetAfterTest(true);

        $this->displayoptions = new question_display_options();
        $this->quba = question_engine::make_questions_usage_by_activity('unit_test',
            context_system::instance());
    }

    protected function tearDown() {
        $this->displayoptions = null;
        $this->quba = null;
        parent::tearDown();
    }

    protected function start_attempt_at_question($question, $preferredbehaviour,
                                                 $maxmark = null, $variant = 1) {
        $this->quba->set_preferred_behaviour($preferredbehaviour);
        $this->slot = $this->quba->add_question($question, $maxmark);
        $this->quba->start_question($this->slot, $variant);
    }

    /**
     * Convert an array of data destined for one question to the equivalent POST data.
     * @param array $data the data for the quetsion.
     * @return array the complete post data.
     */
    protected function response_data_to_post($data) {
        $prefix = $this->quba->get_field_prefix($this->slot);
        $fulldata = array(
            'slots' => $this->slot,
            $prefix . ':sequencecheck' => $this->get_question_attempt()->get_sequence_check_count(),
        );
        foreach ($data as $name => $value) {
            $fulldata[$prefix . $name] = $value;
        }
        return $fulldata;
    }

    protected function process_submission($data) {
        // Backwards compatibility.
        reset($data);
        if (count($data) == 1 && key($data) === '-finish') {
            $this->finish();
        }

        $this->quba->process_all_actions(time(), $this->response_data_to_post($data));
    }

    protected function process_autosave($data) {
        $this->quba->process_all_autosaves(null, $this->response_data_to_post($data));
    }

    protected function finish() {
        $this->quba->finish_all_questions();
    }

    protected function manual_grade($comment, $mark, $commentformat = null) {
        $this->quba->manual_grade($this->slot, $comment, $mark, $commentformat);
    }

    protected function save_quba(moodle_database $db = null) {
        question_engine::save_questions_usage_by_activity($this->quba, $db);
    }

    protected function load_quba(moodle_database $db = null) {
        $this->quba = question_engine::load_questions_usage_by_activity($this->quba->get_id(), $db);
    }

    protected function delete_quba() {
        question_engine::delete_questions_usage_by_activity($this->quba->get_id());
        $this->quba = null;
    }

    protected function check_current_state($state) {
        $this->assertEquals($state, $this->quba->get_question_state($this->slot),
            'Questions is in the wrong state.');
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
            $this->assertEquals($mark, $this->quba->get_question_mark($this->slot),
                'Expected mark and actual mark differ.', 0.000001);
        }
    }

    /**
     * Generate the HTML rendering of the question in its current state in
     * $this->currentoutput so that it can be verified.
     */
    protected function render() {
        $this->currentoutput = $this->quba->render_question($this->slot, $this->displayoptions);
    }

    protected function check_output_contains_text_input($name, $value = null, $enabled = true) {
        $attributes = array(
            'type' => 'text',
            'name' => $this->quba->get_field_prefix($this->slot) . $name,
        );
        if (!is_null($value)) {
            $attributes['value'] = $value;
        }
        if (!$enabled) {
            $attributes['readonly'] = 'readonly';
        }
        $matcher = $this->get_tag_matcher('input', $attributes);
        $this->assertTag($matcher, $this->currentoutput,
                'Looking for an input with attributes ' . html_writer::attributes($attributes) . ' in ' . $this->currentoutput);

        if ($enabled) {
            $matcher['attributes']['readonly'] = 'readonly';
            $this->assertNotTag($matcher, $this->currentoutput,
                    'input with attributes ' . html_writer::attributes($attributes) .
                    ' should not be read-only in ' . $this->currentoutput);
        }
    }

    protected function check_output_contains_text_input_with_class($name, $class = null) {
        $attributes = array(
            'type' => 'text',
            'name' => $this->quba->get_field_prefix($this->slot) . $name,
        );
        if (!is_null($class)) {
            $attributes['class'] = 'regexp:/\b' . $class . '\b/';
        }

        $matcher = $this->get_tag_matcher('input', $attributes);
        $this->assertTag($matcher, $this->currentoutput,
                'Looking for an input with attributes ' . html_writer::attributes($attributes) . ' in ' . $this->currentoutput);
    }

    protected function check_output_does_not_contain_text_input_with_class($name, $class = null) {
        $attributes = array(
            'type' => 'text',
            'name' => $this->quba->get_field_prefix($this->slot) . $name,
        );
        if (!is_null($class)) {
            $attributes['class'] = 'regexp:/\b' . $class . '\b/';
        }

        $matcher = $this->get_tag_matcher('input', $attributes);
        $this->assertNotTag($matcher, $this->currentoutput,
                'Unexpected input with attributes ' . html_writer::attributes($attributes) . ' found in ' . $this->currentoutput);
    }

    protected function check_output_contains_hidden_input($name, $value) {
        $attributes = array(
            'type' => 'hidden',
            'name' => $this->quba->get_field_prefix($this->slot) . $name,
            'value' => $value,
        );
        $this->assertTag($this->get_tag_matcher('input', $attributes), $this->currentoutput,
                'Looking for a hidden input with attributes ' . html_writer::attributes($attributes) . ' in ' . $this->currentoutput);
    }

    protected function check_output_contains($string) {
        $this->render();
        $this->assertContains($string, $this->currentoutput,
                'Expected string ' . $string . ' not found in ' . $this->currentoutput);
    }

    protected function check_output_does_not_contain($string) {
        $this->render();
        $this->assertNotContains($string, $this->currentoutput,
                'String ' . $string . ' unexpectedly found in ' . $this->currentoutput);
    }

    protected function check_output_contains_lang_string($identifier, $component = '', $a = null) {
        $this->check_output_contains(get_string($identifier, $component, $a));
    }

    protected function get_tag_matcher($tag, $attributes) {
        return array(
            'tag' => $tag,
            'attributes' => $attributes,
        );
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
        $this->assertEquals($expectednumsteps, $this->get_step_count());
    }

    protected function get_step($stepnum) {
        return $this->get_question_attempt()->get_step($stepnum);
    }

    protected function get_contains_question_text_expectation($question) {
        return new question_pattern_expectation('/' . preg_quote($question->questiontext, '/') . '/');
    }

    protected function get_contains_general_feedback_expectation($question) {
        return new question_pattern_expectation('/' . preg_quote($question->generalfeedback, '/') . '/');
    }

    protected function get_does_not_contain_correctness_expectation() {
        return new question_no_pattern_expectation('/class=\"correctness/');
    }

    protected function get_contains_correct_expectation() {
        return new question_pattern_expectation('/' . preg_quote(get_string('correct', 'question'), '/') . '/');
    }

    protected function get_contains_partcorrect_expectation() {
        return new question_pattern_expectation('/' .
            preg_quote(get_string('partiallycorrect', 'question'), '/') . '/');
    }

    protected function get_contains_incorrect_expectation() {
        return new question_pattern_expectation('/' . preg_quote(get_string('incorrect', 'question'), '/') . '/');
    }

    protected function get_contains_standard_correct_combined_feedback_expectation() {
        return new question_pattern_expectation('/' .
            preg_quote(test_question_maker::STANDARD_OVERALL_CORRECT_FEEDBACK, '/') . '/');
    }

    protected function get_contains_standard_partiallycorrect_combined_feedback_expectation() {
        return new question_pattern_expectation('/' .
            preg_quote(test_question_maker::STANDARD_OVERALL_PARTIALLYCORRECT_FEEDBACK, '/') . '/');
    }

    protected function get_contains_standard_incorrect_combined_feedback_expectation() {
        return new question_pattern_expectation('/' .
            preg_quote(test_question_maker::STANDARD_OVERALL_INCORRECT_FEEDBACK, '/') . '/');
    }

    protected function get_does_not_contain_feedback_expectation() {
        return new question_no_pattern_expectation('/class="feedback"/');
    }

    protected function get_does_not_contain_num_parts_correct() {
        return new question_no_pattern_expectation('/class="numpartscorrect"/');
    }

    protected function get_contains_num_parts_correct($num) {
        $a = new stdClass();
        $a->num = $num;
        return new question_pattern_expectation('/<div class="numpartscorrect">' .
            preg_quote(get_string('yougotnright', 'question', $a), '/') . '/');
    }

    protected function get_does_not_contain_specific_feedback_expectation() {
        return new question_no_pattern_expectation('/class="specificfeedback"/');
    }

    protected function get_contains_validation_error_expectation() {
        return new question_contains_tag_with_attribute('div', 'class', 'validationerror');
    }

    protected function get_does_not_contain_validation_error_expectation() {
        return new question_no_pattern_expectation('/class="validationerror"/');
    }

    protected function get_contains_mark_summary($mark) {
        $a = new stdClass();
        $a->mark = format_float($mark, $this->displayoptions->markdp);
        $a->max = format_float($this->quba->get_question_max_mark($this->slot),
            $this->displayoptions->markdp);
        return new question_pattern_expectation('/' .
            preg_quote(get_string('markoutofmax', 'question', $a), '/') . '/');
    }

    protected function get_contains_marked_out_of_summary() {
        $max = format_float($this->quba->get_question_max_mark($this->slot),
            $this->displayoptions->markdp);
        return new question_pattern_expectation('/' .
            preg_quote(get_string('markedoutofmax', 'question', $max), '/') . '/');
    }

    protected function get_does_not_contain_mark_summary() {
        return new question_no_pattern_expectation('/<div class="grade">/');
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
        return new question_contains_tag_with_attributes('input', $expectedattributes, $forbiddenattributes);
    }

    protected function get_contains_mc_checkbox_expectation($index, $enabled = null,
                                                            $checked = null) {
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
        return new question_contains_tag_with_attributes('input', $expectedattributes, $forbiddenattributes);
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
        return new question_contains_tag_with_attributes('input', $expectedattributes);
    }

    protected function get_does_not_contain_hidden_expectation($name, $value = null) {
        $expectedattributes = array('type' => 'hidden', 'name' => s($name));
        if (!is_null($value)) {
            $expectedattributes['value'] = s($value);
        }
        return new question_does_not_contain_tag_with_attributes('input', $expectedattributes);
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

    protected function get_contains_cbm_radio_expectation($certainty, $enabled = null,
                                                          $checked = null) {
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
        return new question_contains_tag_with_attributes('input', $expectedattributes, $forbiddenattributes);
    }

    protected function get_contains_submit_button_expectation($enabled = null) {
        return $this->get_contains_button_expectation(
            $this->quba->get_field_prefix($this->slot) . '-submit', null, $enabled);
    }

    protected function get_tries_remaining_expectation($n) {
        return new question_pattern_expectation('/' .
            preg_quote(get_string('triesremaining', 'qbehaviour_interactive', $n), '/') . '/');
    }

    protected function get_invalid_answer_expectation() {
        return new question_pattern_expectation('/' .
            preg_quote(get_string('invalidanswer', 'question'), '/') . '/');
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
        return new question_contains_tag_with_attributes('input', $expectedattributes, $forbiddenattributes);
    }

    protected function get_does_not_contain_try_again_button_expectation() {
        return new question_no_pattern_expectation('/name="' .
            $this->quba->get_field_prefix($this->slot) . '-tryagain"/');
    }

    protected function get_contains_select_expectation($name, $choices,
                                                       $selected = null, $enabled = null) {
        $fullname = $this->quba->get_field_prefix($this->slot) . $name;
        return new question_contains_select_expectation($fullname, $choices, $selected, $enabled);
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
        return new question_no_pattern_expectation('/class="hint"/');
    }

    protected function get_contains_hint_expectation($hinttext) {
        // Does not currently verify hint text.
        return new question_contains_tag_with_attribute('div', 'class', 'hint');
    }
}

/**
 * Simple class that implements the {@link moodle_recordset} API based on an
 * array of test data.
 *
 *  See the {@link question_attempt_step_db_test} class in
 *  question/engine/tests/testquestionattemptstep.php for an example of how
 *  this is used.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_test_recordset extends moodle_recordset {
    protected $records;

    /**
     * Constructor
     * @param $table as for {@link testing_db_record_builder::build_db_records()}
     *      but does not need a unique first column.
     */
    public function __construct(array $table) {
        $columns = array_shift($table);
        $this->records = array();
        foreach ($table as $row) {
            if (count($row) != count($columns)) {
                throw new coding_exception("Row contains the wrong number of fields.");
            }
            $rec = array();
            foreach ($columns as $i => $name) {
                $rec[$name] = $row[$i];
            }
            $this->records[] = $rec;
        }
        reset($this->records);
    }

    public function __destruct() {
        $this->close();
    }

    public function current() {
        return (object) current($this->records);
    }

    public function key() {
        if (is_null(key($this->records))) {
            return false;
        }
        $current = current($this->records);
        return reset($current);
    }

    public function next() {
        next($this->records);
    }

    public function valid() {
        return !is_null(key($this->records));
    }

    public function close() {
        $this->records = null;
    }
}
