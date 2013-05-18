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
 * This file defines the class {@link question_definition} and its subclasses.
 *
 * The type hierarchy is quite complex. Here is a summary:
 * - question_definition
 *   - question_information_item
 *   - question_with_responses implements question_manually_gradable
 *     - question_graded_automatically implements question_automatically_gradable
 *       - question_graded_automatically_with_countback implements question_automatically_gradable_with_countback
 *       - question_graded_by_strategy
 *
 * Other classes:
 * - question_classified_response
 * - question_answer
 * - question_hint
 *   - question_hint_with_parts
 * - question_first_matching_answer_grading_strategy implements question_grading_strategy
 *
 * Other interfaces:
 * - question_response_answer_comparer
 *
 * @package    moodlecore
 * @subpackage questiontypes
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * The definition of a question of a particular type.
 *
 * This class is a close match to the question table in the database.
 * Definitions of question of a particular type normally subclass one of the
 * more specific classes {@link question_with_responses},
 * {@link question_graded_automatically} or {@link question_information_item}.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class question_definition {
    /** @var integer id of the question in the datase, or null if this question
     * is not in the database. */
    public $id;

    /** @var integer question category id. */
    public $category;

    /** @var integer question context id. */
    public $contextid;

    /** @var integer parent question id. */
    public $parent = 0;

    /** @var question_type the question type this question is. */
    public $qtype;

    /** @var string question name. */
    public $name;

    /** @var string question text. */
    public $questiontext;

    /** @var integer question test format. */
    public $questiontextformat;

    /** @var string question general feedback. */
    public $generalfeedback;

    /** @var integer question test format. */
    public $generalfeedbackformat;

    /** @var number what this quetsion is marked out of, by default. */
    public $defaultmark = 1;

    /** @var integer How many question numbers this question consumes. */
    public $length = 1;

    /** @var number penalty factor of this question. */
    public $penalty = 0;

    /** @var string unique identifier of this question. */
    public $stamp;

    /** @var string unique identifier of this version of this question. */
    public $version;

    /** @var boolean whethre this question has been deleted/hidden in the question bank. */
    public $hidden = 0;

    /** @var integer timestamp when this question was created. */
    public $timecreated;

    /** @var integer timestamp when this question was modified. */
    public $timemodified;

    /** @var integer userid of the use who created this question. */
    public $createdby;

    /** @var integer userid of the use who modified this question. */
    public $modifiedby;

    /** @var array of question_hints. */
    public $hints = array();

    /**
     * Constructor. Normally to get a question, you call
     * {@link question_bank::load_question()}, but questions can be created
     * directly, for example in unit test code.
     * @return unknown_type
     */
    public function __construct() {
    }

    /**
     * @return the name of the question type (for example multichoice) that this
     * question is.
     */
    public function get_type_name() {
        return $this->qtype->name();
    }

    /**
     * Creat the appropriate behaviour for an attempt at this quetsion,
     * given the desired (archetypal) behaviour.
     *
     * This default implementation will suit most normal graded questions.
     *
     * If your question is of a patricular type, then it may need to do something
     * different. For example, if your question can only be graded manually, then
     * it should probably return a manualgraded behaviour, irrespective of
     * what is asked for.
     *
     * If your question wants to do somthing especially complicated is some situations,
     * then you may wish to return a particular behaviour related to the
     * one asked for. For example, you migth want to return a
     * qbehaviour_interactive_adapted_for_myqtype.
     *
     * @param question_attempt $qa the attempt we are creating an behaviour for.
     * @param string $preferredbehaviour the requested type of behaviour.
     * @return question_behaviour the new behaviour object.
     */
    public function make_behaviour(question_attempt $qa, $preferredbehaviour) {
        return question_engine::make_archetypal_behaviour($preferredbehaviour, $qa);
    }

    /**
     * Start a new attempt at this question, storing any information that will
     * be needed later in the step.
     *
     * This is where the question can do any initialisation required on a
     * per-attempt basis. For example, this is where the multiple choice
     * question type randomly shuffles the choices (if that option is set).
     *
     * Any information about how the question has been set up for this attempt
     * should be stored in the $step, by calling $step->set_qt_var(...).
     *
     * @param question_attempt_step The first step of the {@link question_attempt}
     *      being started. Can be used to store state.
     * @param int $varant which variant of this question to start. Will be between
     *      1 and {@link get_num_variants()} inclusive.
     */
    public function start_attempt(question_attempt_step $step, $variant) {
    }

    /**
     * When an in-progress {@link question_attempt} is re-loaded from the
     * database, this method is called so that the question can re-initialise
     * its internal state as needed by this attempt.
     *
     * For example, the multiple choice question type needs to set the order
     * of the choices to the order that was set up when start_attempt was called
     * originally. All the information required to do this should be in the
     * $step object, which is the first step of the question_attempt being loaded.
     *
     * @param question_attempt_step The first step of the {@link question_attempt}
     *      being loaded.
     */
    public function apply_attempt_state(question_attempt_step $step) {
    }

    /**
     * Generate a brief, plain-text, summary of this question. This is used by
     * various reports. This should show the particular variant of the question
     * as presented to students. For example, the calculated quetsion type would
     * fill in the particular numbers that were presented to the student.
     * This method will return null if such a summary is not possible, or
     * inappropriate.
     * @return string|null a plain text summary of this question.
     */
    public function get_question_summary() {
        return $this->html_to_text($this->questiontext, $this->questiontextformat);
    }

    /**
     * @return int the number of vaiants that this question has.
     */
    public function get_num_variants() {
        return 1;
    }

    /**
     * @return string that can be used to seed the pseudo-random selection of a
     *      variant.
     */
    public function get_variants_selection_seed() {
        return $this->stamp;
    }

    /**
     * Some questions can return a negative mark if the student gets it wrong.
     *
     * This method returns the lowest mark the question can return, on the
     * fraction scale. that is, where the maximum possible mark is 1.0.
     *
     * @return number minimum fraction this question will ever return.
     */
    public function get_min_fraction() {
        return 0;
    }

    /**
     * Given a response, rest the parts that are wrong.
     * @param array $response a response
     * @return array a cleaned up response with the wrong bits reset.
     */
    public function clear_wrong_from_response(array $response) {
        return array();
    }

    /**
     * Return the number of subparts of this response that are right.
     * @param array $response a response
     * @return array with two elements, the number of correct subparts, and
     * the total number of subparts.
     */
    public function get_num_parts_right(array $response) {
        return array(null, null);
    }

    /**
     * @param moodle_page the page we are outputting to.
     * @return qtype_renderer the renderer to use for outputting this question.
     */
    public function get_renderer(moodle_page $page) {
        return $page->get_renderer($this->qtype->plugin_name());
    }

    /**
     * What data may be included in the form submission when a student submits
     * this question in its current state?
     *
     * This information is used in calls to optional_param. The parameter name
     * has {@link question_attempt::get_field_prefix()} automatically prepended.
     *
     * @return array|string variable name => PARAM_... constant, or, as a special case
     *      that should only be used in unavoidable, the constant question_attempt::USE_RAW_DATA
     *      meaning take all the raw submitted data belonging to this question.
     */
    public abstract function get_expected_data();

    /**
     * What data would need to be submitted to get this question correct.
     * If there is more than one correct answer, this method should just
     * return one possibility. If it is not possible to compute a correct
     * response, this method should return null.
     *
     * @return array|null parameter name => value.
     */
    public abstract function get_correct_response();

    /**
     * Apply {@link format_text()} to some content with appropriate settings for
     * this question.
     *
     * @param string $text some content that needs to be output.
     * @param int $format the FORMAT_... constant.
     * @param question_attempt $qa the question attempt.
     * @param string $component used for rewriting file area URLs.
     * @param string $filearea used for rewriting file area URLs.
     * @param bool $clean Whether the HTML needs to be cleaned. Generally,
     *      parts of the question do not need to be cleaned, and student input does.
     * @return string the text formatted for output by format_text.
     */
    public function format_text($text, $format, $qa, $component, $filearea, $itemid,
            $clean = false) {
        $formatoptions = new stdClass();
        $formatoptions->noclean = !$clean;
        $formatoptions->para = false;
        $text = $qa->rewrite_pluginfile_urls($text, $component, $filearea, $itemid);
        return format_text($text, $format, $formatoptions);
    }

    /**
     * Convert some part of the question text to plain text. This might be used,
     * for example, by get_response_summary().
     * @param string $text The HTML to reduce to plain text.
     * @param int $format the FORMAT_... constant.
     * @return string the equivalent plain text.
     */
    public function html_to_text($text, $format) {
        return html_to_text(format_text($text, $format, array('noclean' => true)), 0, false);
    }

    /** @return the result of applying {@link format_text()} to the question text. */
    public function format_questiontext($qa) {
        return $this->format_text($this->questiontext, $this->questiontextformat,
                $qa, 'question', 'questiontext', $this->id);
    }

    /** @return the result of applying {@link format_text()} to the general feedback. */
    public function format_generalfeedback($qa) {
        return $this->format_text($this->generalfeedback, $this->generalfeedbackformat,
                $qa, 'question', 'generalfeedback', $this->id);
    }

    /**
     * Checks whether the users is allow to be served a particular file.
     * @param question_attempt $qa the question attempt being displayed.
     * @param question_display_options $options the options that control display of the question.
     * @param string $component the name of the component we are serving files for.
     * @param string $filearea the name of the file area.
     * @param array $args the remaining bits of the file path.
     * @param bool $forcedownload whether the user must be forced to download the file.
     * @return bool true if the user can access this file.
     */
    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        if ($component == 'question' && $filearea == 'questiontext') {
            // Question text always visible.
            return true;

        } else if ($component == 'question' && $filearea == 'generalfeedback') {
            return $options->generalfeedback;

        } else {
            // Unrecognised component or filearea.
            return false;
        }
    }
}


/**
 * This class represents a 'question' that actually does not allow the student
 * to respond, like the description 'question' type.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_information_item extends question_definition {
    public function __construct() {
        parent::__construct();
        $this->defaultmark = 0;
        $this->penalty = 0;
        $this->length = 0;
    }

    public function make_behaviour(question_attempt $qa, $preferredbehaviour) {
        question_engine::load_behaviour_class('informationitem');
        return new qbehaviour_informationitem($qa, $preferredbehaviour);
    }

    public function get_expected_data() {
        return array();
    }

    public function get_correct_response() {
        return array();
    }

    public function get_question_summary() {
        return null;
    }
}


/**
 * Interface that a {@link question_definition} must implement to be usable by
 * the manual graded behaviour.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface question_manually_gradable {
    /**
     * Used by many of the behaviours, to work out whether the student's
     * response to the question is complete. That is, whether the question attempt
     * should move to the COMPLETE or INCOMPLETE state.
     *
     * @param array $response responses, as returned by
     *      {@link question_attempt_step::get_qt_data()}.
     * @return bool whether this response is a complete answer to this question.
     */
    public function is_complete_response(array $response);

    /**
     * Use by many of the behaviours to determine whether the student's
     * response has changed. This is normally used to determine that a new set
     * of responses can safely be discarded.
     *
     * @param array $prevresponse the responses previously recorded for this question,
     *      as returned by {@link question_attempt_step::get_qt_data()}
     * @param array $newresponse the new responses, in the same format.
     * @return bool whether the two sets of responses are the same - that is
     *      whether the new set of responses can safely be discarded.
     */
    public function is_same_response(array $prevresponse, array $newresponse);

    /**
     * Produce a plain text summary of a response.
     * @param $response a response, as might be passed to {@link grade_response()}.
     * @return string a plain text summary of that response, that could be used in reports.
     */
    public function summarise_response(array $response);

    /**
     * Categorise the student's response according to the categories defined by
     * get_possible_responses.
     * @param $response a response, as might be passed to {@link grade_response()}.
     * @return array subpartid => {@link question_classified_response} objects.
     *      returns an empty array if no analysis is possible.
     */
    public function classify_response(array $response);
}


/**
 * This class is used in the return value from
 * {@link question_manually_gradable::classify_response()}.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_classified_response {
    /**
     * @var string the classification of this response the student gave to this
     * part of the question. Must match one of the responseclasses returned by
     * {@link question_type::get_possible_responses()}.
     */
    public $responseclassid;
    /** @var string the actual response the student gave to this part. */
    public $response;
    /** @var number the fraction this part of the response earned. */
    public $fraction;
    /**
     * Constructor, just an easy way to set the fields.
     * @param string $responseclassid see the field descriptions above.
     * @param string $response see the field descriptions above.
     * @param number $fraction see the field descriptions above.
     */
    public function __construct($responseclassid, $response, $fraction) {
        $this->responseclassid = $responseclassid;
        $this->response = $response;
        $this->fraction = $fraction;
    }

    public static function no_response() {
        return new question_classified_response(null, get_string('noresponse', 'question'), null);
    }
}


/**
 * Interface that a {@link question_definition} must implement to be usable by
 * the various automatic grading behaviours.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface question_automatically_gradable extends question_manually_gradable {
    /**
     * Use by many of the behaviours to determine whether the student
     * has provided enough of an answer for the question to be graded automatically,
     * or whether it must be considered aborted.
     *
     * @param array $response responses, as returned by
     *      {@link question_attempt_step::get_qt_data()}.
     * @return bool whether this response can be graded.
     */
    public function is_gradable_response(array $response);

    /**
     * In situations where is_gradable_response() returns false, this method
     * should generate a description of what the problem is.
     * @return string the message.
     */
    public function get_validation_error(array $response);

    /**
     * Grade a response to the question, returning a fraction between
     * get_min_fraction() and 1.0, and the corresponding {@link question_state}
     * right, partial or wrong.
     * @param array $response responses, as returned by
     *      {@link question_attempt_step::get_qt_data()}.
     * @return array (number, integer) the fraction, and the state.
     */
    public function grade_response(array $response);

    /**
     * Get one of the question hints. The question_attempt is passed in case
     * the question type wants to do something complex. For example, the
     * multiple choice with multiple responses question type will turn off most
     * of the hint options if the student has selected too many opitions.
     * @param int $hintnumber Which hint to display. Indexed starting from 0
     * @param question_attempt $qa The question_attempt.
     */
    public function get_hint($hintnumber, question_attempt $qa);

    /**
     * Generate a brief, plain-text, summary of the correct answer to this question.
     * This is used by various reports, and can also be useful when testing.
     * This method will return null if such a summary is not possible, or
     * inappropriate.
     * @return string|null a plain text summary of the right answer to this question.
     */
    public function get_right_answer_summary();
}


/**
 * Interface that a {@link question_definition} must implement to be usable by
 * the interactivecountback behaviour.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface question_automatically_gradable_with_countback extends question_automatically_gradable {
    /**
     * Work out a final grade for this attempt, taking into account all the
     * tries the student made.
     * @param array $responses the response for each try. Each element of this
     * array is a response array, as would be passed to {@link grade_response()}.
     * There may be between 1 and $totaltries responses.
     * @param int $totaltries The maximum number of tries allowed.
     * @return numeric the fraction that should be awarded for this
     * sequence of response.
     */
    public function compute_final_grade($responses, $totaltries);
}


/**
 * This class represents a real question. That is, one that is not a
 * {@link question_information_item}.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class question_with_responses extends question_definition
        implements question_manually_gradable {
    public function classify_response(array $response) {
        return array();
    }
}


/**
 * This class represents a question that can be graded automatically.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class question_graded_automatically extends question_with_responses
        implements question_automatically_gradable {
    /** @var Some question types have the option to show the number of sub-parts correct. */
    public $shownumcorrect = false;

    public function is_gradable_response(array $response) {
        return $this->is_complete_response($response);
    }

    public function get_right_answer_summary() {
        $correctresponse = $this->get_correct_response();
        if (empty($correctresponse)) {
            return null;
        }
        return $this->summarise_response($correctresponse);
    }

    /**
     * Check a request for access to a file belonging to a combined feedback field.
     * @param question_attempt $qa the question attempt being displayed.
     * @param question_display_options $options the options that control display of the question.
     * @param string $filearea the name of the file area.
     * @return bool whether access to the file should be allowed.
     */
    protected function check_combined_feedback_file_access($qa, $options, $filearea) {
        $state = $qa->get_state();

        if (!$state->is_finished()) {
            $response = $qa->get_last_qt_data();
            if (!$this->is_gradable_response($response)) {
                return false;
            }
            list($notused, $state) = $this->grade_response($response);
        }

        return $options->feedback && $state->get_feedback_class() . 'feedback' == $filearea;
    }

    /**
     * Check a request for access to a file belonging to a hint.
     * @param question_attempt $qa the question attempt being displayed.
     * @param question_display_options $options the options that control display of the question.
     * @param array $args the remaining bits of the file path.
     * @return bool whether access to the file should be allowed.
     */
    protected function check_hint_file_access($qa, $options, $args) {
        if (!$options->feedback) {
            return false;
        }
        $hint = $qa->get_applicable_hint();
        $hintid = reset($args); // itemid is hint id.
        return $hintid == $hint->id;
    }

    public function get_hint($hintnumber, question_attempt $qa) {
        if (!isset($this->hints[$hintnumber])) {
            return null;
        }
        return $this->hints[$hintnumber];
    }

    public function format_hint(question_hint $hint, question_attempt $qa) {
        return $this->format_text($hint->hint, $hint->hintformat, $qa,
                'question', 'hint', $hint->id);
    }
}


/**
 * This class represents a question that can be graded automatically with
 * countback grading in interactive mode.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class question_graded_automatically_with_countback
        extends question_graded_automatically
        implements question_automatically_gradable_with_countback {

    public function make_behaviour(question_attempt $qa, $preferredbehaviour) {
        if ($preferredbehaviour == 'interactive') {
            return question_engine::make_behaviour('interactivecountback',
                    $qa, $preferredbehaviour);
        }
        return question_engine::make_archetypal_behaviour($preferredbehaviour, $qa);
    }
}


/**
 * This class represents a question that can be graded automatically by using
 * a {@link question_grading_strategy}.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class question_graded_by_strategy extends question_graded_automatically {
    /** @var question_grading_strategy the strategy to use for grading. */
    protected $gradingstrategy;

    /** @param question_grading_strategy  $strategy the strategy to use for grading. */
    public function __construct(question_grading_strategy $strategy) {
        parent::__construct();
        $this->gradingstrategy = $strategy;
    }

    public function get_correct_response() {
        $answer = $this->get_correct_answer();
        if (!$answer) {
            return array();
        }

        return array('answer' => $answer->answer);
    }

    /**
     * Get an answer that contains the feedback and fraction that should be
     * awarded for this resonse.
     * @param array $response a response.
     * @return question_answer the matching answer.
     */
    public function get_matching_answer(array $response) {
        return $this->gradingstrategy->grade($response);
    }

    /**
     * @return question_answer an answer that contains the a response that would
     *      get full marks.
     */
    public function get_correct_answer() {
        return $this->gradingstrategy->get_correct_answer();
    }

    public function grade_response(array $response) {
        $answer = $this->get_matching_answer($response);
        if ($answer) {
            return array($answer->fraction,
                    question_state::graded_state_for_fraction($answer->fraction));
        } else {
            return array(0, question_state::$gradedwrong);
        }
    }

    public function classify_response(array $response) {
        if (empty($response['answer'])) {
            return array($this->id => question_classified_response::no_response());
        }

        $ans = $this->get_matching_answer($response);
        if (!$ans) {
            return array($this->id => new question_classified_response(
                    0, $response['answer'], 0));
        }

        return array($this->id => new question_classified_response(
                $ans->id, $response['answer'], $ans->fraction));
    }
}


/**
 * Class to represent a question answer, loaded from the question_answers table
 * in the database.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_answer {
    /** @var integer the answer id. */
    public $id;

    /** @var string the answer. */
    public $answer;

    /** @var integer one of the FORMAT_... constans. */
    public $answerformat = FORMAT_PLAIN;

    /** @var number the fraction this answer is worth. */
    public $fraction;

    /** @var string the feedback for this answer. */
    public $feedback;

    /** @var integer one of the FORMAT_... constans. */
    public $feedbackformat;

    /**
     * Constructor.
     * @param int $id the answer.
     * @param string $answer the answer.
     * @param int $answerformat the format of the answer.
     * @param number $fraction the fraction this answer is worth.
     * @param string $feedback the feedback for this answer.
     * @param int $feedbackformat the format of the feedback.
     */
    public function __construct($id, $answer, $fraction, $feedback, $feedbackformat) {
        $this->id = $id;
        $this->answer = $answer;
        $this->fraction = $fraction;
        $this->feedback = $feedback;
        $this->feedbackformat = $feedbackformat;
    }
}


/**
 * Class to represent a hint associated with a question.
 * Used by iteractive mode, etc. A question has an array of these.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_hint {
    /** @var integer The hint id. */
    public $id;
    /** @var string The feedback hint to be shown. */
    public $hint;
    /** @var integer The corresponding text FORMAT_... type. */
    public $hintformat;

    /**
     * Constructor.
     * @param int the hint id from the database.
     * @param string $hint The hint text
     * @param int the corresponding text FORMAT_... type.
     */
    public function __construct($id, $hint, $hintformat) {
        $this->id = $id;
        $this->hint = $hint;
        $this->hintformat = $hintformat;
    }

    /**
     * Create a basic hint from a row loaded from the question_hints table in the database.
     * @param object $row with $row->hint set.
     * @return question_hint
     */
    public static function load_from_record($row) {
        return new question_hint($row->id, $row->hint, $row->hintformat);
    }

    /**
     * Adjust this display options according to the hint settings.
     * @param question_display_options $options
     */
    public function adjust_display_options(question_display_options $options) {
        // Do nothing.
    }
}


/**
 * An extension of {@link question_hint} for questions like match and multiple
 * choice with multile answers, where there are options for whether to show the
 * number of parts right at each stage, and to reset the wrong parts.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_hint_with_parts extends question_hint {
    /** @var boolean option to show the number of sub-parts of the question that were right. */
    public $shownumcorrect;

    /** @var boolean option to clear the parts of the question that were wrong on retry. */
    public $clearwrong;

    /**
     * Constructor.
     * @param int the hint id from the database.
     * @param string $hint The hint text
     * @param int the corresponding text FORMAT_... type.
     * @param bool $shownumcorrect whether the number of right parts should be shown
     * @param bool $clearwrong whether the wrong parts should be reset.
     */
    public function __construct($id, $hint, $hintformat, $shownumcorrect, $clearwrong) {
        parent::__construct($id, $hint, $hintformat);
        $this->shownumcorrect = $shownumcorrect;
        $this->clearwrong = $clearwrong;
    }

    /**
     * Create a basic hint from a row loaded from the question_hints table in the database.
     * @param object $row with $row->hint, ->shownumcorrect and ->clearwrong set.
     * @return question_hint_with_parts
     */
    public static function load_from_record($row) {
        return new question_hint_with_parts($row->id, $row->hint, $row->hintformat,
                $row->shownumcorrect, $row->clearwrong);
    }

    public function adjust_display_options(question_display_options $options) {
        parent::adjust_display_options($options);
        if ($this->clearwrong) {
            $options->clearwrong = true;
        }
        $options->numpartscorrect = $this->shownumcorrect;
    }
}


/**
 * This question_grading_strategy interface. Used to share grading code between
 * questions that that subclass {@link question_graded_by_strategy}.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface question_grading_strategy {
    /**
     * Return a question answer that describes the outcome (fraction and feeback)
     * for a particular respons.
     * @param array $response the response.
     * @return question_answer the answer describing the outcome.
     */
    public function grade(array $response);

    /**
     * @return question_answer an answer that contains the a response that would
     *      get full marks.
     */
    public function get_correct_answer();
}


/**
 * This interface defines the methods that a {@link question_definition} must
 * implement if it is to be graded by the
 * {@link question_first_matching_answer_grading_strategy}.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface question_response_answer_comparer {
    /** @return array of {@link question_answers}. */
    public function get_answers();

    /**
     * @param array $response the response.
     * @param question_answer $answer an answer.
     * @return bool whether the response matches the answer.
     */
    public function compare_response_with_answer(array $response, question_answer $answer);
}


/**
 * This grading strategy is used by question types like shortanswer an numerical.
 * It gets a list of possible answers from the question, and returns the first one
 * that matches the given response. It returns the first answer with fraction 1.0
 * when asked for the correct answer.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_first_matching_answer_grading_strategy implements question_grading_strategy {
    /**
     * @var question_response_answer_comparer (presumably also a
     * {@link question_definition}) the question we are doing the grading for.
     */
    protected $question;

    /**
     * @param question_response_answer_comparer $question (presumably also a
     * {@link question_definition}) the question we are doing the grading for.
     */
    public function __construct(question_response_answer_comparer $question) {
        $this->question = $question;
    }

    public function grade(array $response) {
        foreach ($this->question->get_answers() as $aid => $answer) {
            if ($this->question->compare_response_with_answer($response, $answer)) {
                $answer->id = $aid;
                return $answer;
            }
        }
        return null;
    }

    public function get_correct_answer() {
        foreach ($this->question->get_answers() as $answer) {
            $state = question_state::graded_state_for_fraction($answer->fraction);
            if ($state == question_state::$gradedright) {
                return $answer;
            }
        }
        return null;
    }
}