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
 * This file defines the question attempt class, and a few related classes.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Tracks an attempt at one particular question in a {@link question_usage_by_activity}.
 *
 * Most calling code should need to access objects of this class. They should be
 * able to do everything through the usage interface. This class is an internal
 * implementation detail of the question engine.
 *
 * Instances of this class correspond to rows in the question_attempts table, and
 * a collection of {@link question_attempt_steps}. Question inteaction models and
 * question types do work with question_attempt objects.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_attempt {
    /**
     * @var string this is a magic value that question types can return from
     * {@link question_definition::get_expected_data()}.
     */
    const USE_RAW_DATA = 'use raw data';

    /**
     * @var string special value used by manual grading because {@link PARAM_NUMBER}
     * converts '' to 0.
     */
    const PARAM_MARK = 'parammark';

    /**
     * @var string special value to indicate a response variable that is uploaded
     * files.
     */
    const PARAM_FILES = 'paramfiles';

    /**
     * @var string special value to indicate a response variable that is uploaded
     * files.
     */
    const PARAM_CLEANHTML_FILES = 'paramcleanhtmlfiles';

    /** @var integer if this attempts is stored in the question_attempts table, the id of that row. */
    protected $id = null;

    /** @var integer|string the id of the question_usage_by_activity we belong to. */
    protected $usageid;

    /** @var integer the number used to identify this question_attempt within the usage. */
    protected $slot = null;

    /**
     * @var question_behaviour the behaviour controlling this attempt.
     * null until {@link start()} is called.
     */
    protected $behaviour = null;

    /** @var question_definition the question this is an attempt at. */
    protected $question;

    /** @var int which variant of the question to use. */
    protected $variant;

    /** @var number the maximum mark that can be scored at this question. */
    protected $maxmark;

    /**
     * @var number the minimum fraction that can be scored at this question, so
     * the minimum mark is $this->minfraction * $this->maxmark.
     */
    protected $minfraction = null;

    /**
     * @var string plain text summary of the variant of the question the
     * student saw. Intended for reporting purposes.
     */
    protected $questionsummary = null;

    /**
     * @var string plain text summary of the response the student gave.
     * Intended for reporting purposes.
     */
    protected $responsesummary = null;

    /**
     * @var string plain text summary of the correct response to this question
     * variant the student saw. The format should be similar to responsesummary.
     * Intended for reporting purposes.
     */
    protected $rightanswer = null;

    /** @var array of {@link question_attempt_step}s. The steps in this attempt. */
    protected $steps = array();

    /** @var boolean whether the user has flagged this attempt within the usage. */
    protected $flagged = false;

    /** @var question_usage_observer tracks changes to the useage this attempt is part of.*/
    protected $observer;

    /**#@+
     * Constants used by the intereaction models to indicate whether the current
     * pending step should be kept or discarded.
     */
    const KEEP = true;
    const DISCARD = false;
    /**#@-*/

    /**
     * Create a new {@link question_attempt}. Normally you should create question_attempts
     * indirectly, by calling {@link question_usage_by_activity::add_question()}.
     *
     * @param question_definition $question the question this is an attempt at.
     * @param int|string $usageid The id of the
     *      {@link question_usage_by_activity} we belong to. Used by {@link get_field_prefix()}.
     * @param question_usage_observer $observer tracks changes to the useage this
     *      attempt is part of. (Optional, a {@link question_usage_null_observer} is
     *      used if one is not passed.
     * @param number $maxmark the maximum grade for this question_attempt. If not
     * passed, $question->defaultmark is used.
     */
    public function __construct(question_definition $question, $usageid,
            question_usage_observer $observer = null, $maxmark = null) {
        $this->question = $question;
        $this->usageid = $usageid;
        if (is_null($observer)) {
            $observer = new question_usage_null_observer();
        }
        $this->observer = $observer;
        if (!is_null($maxmark)) {
            $this->maxmark = $maxmark;
        } else {
            $this->maxmark = $question->defaultmark;
        }
    }

    /**
     * This method exists so that {@link question_attempt_with_restricted_history}
     * can override it. You should not normally need to call it.
     * @return question_attempt return ourself.
     */
    public function get_full_qa() {
        return $this;
    }

    /** @return question_definition the question this is an attempt at. */
    public function get_question() {
        return $this->question;
    }

    /**
     * Get the variant of the question being used in a given slot.
     * @return int the variant number.
     */
    public function get_variant() {
        return $this->variant;
    }

    /**
     * Set the number used to identify this question_attempt within the usage.
     * For internal use only.
     * @param int $slot
     */
    public function set_number_in_usage($slot) {
        $this->slot = $slot;
    }

    /** @return int the number used to identify this question_attempt within the usage. */
    public function get_slot() {
        return $this->slot;
    }

    /**
     * @return int the id of row for this question_attempt, if it is stored in the
     * database. null if not.
     */
    public function get_database_id() {
        return $this->id;
    }

    /**
     * For internal use only. Set the id of the corresponding database row.
     * @param int $id the id of row for this question_attempt, if it is
     * stored in the database.
     */
    public function set_database_id($id) {
        $this->id = $id;
    }

    /** @return int|string the id of the {@link question_usage_by_activity} we belong to. */
    public function get_usage_id() {
        return $this->usageid;
    }

    /**
     * Set the id of the {@link question_usage_by_activity} we belong to.
     * For internal use only.
     * @param int|string the new id.
     */
    public function set_usage_id($usageid) {
        $this->usageid = $usageid;
    }

    /** @return string the name of the behaviour that is controlling this attempt. */
    public function get_behaviour_name() {
        return $this->behaviour->get_name();
    }

    /**
     * For internal use only.
     * @return question_behaviour the behaviour that is controlling this attempt.
     */
    public function get_behaviour() {
        return $this->behaviour;
    }

    /**
     * Set the flagged state of this question.
     * @param bool $flagged the new state.
     */
    public function set_flagged($flagged) {
        $this->flagged = $flagged;
        $this->observer->notify_attempt_modified($this);
    }

    /** @return bool whether this question is currently flagged. */
    public function is_flagged() {
        return $this->flagged;
    }

    /**
     * Get the name (in the sense a HTML name="" attribute, or a $_POST variable
     * name) to use for the field that indicates whether this question is flagged.
     *
     * @return string  The field name to use.
     */
    public function get_flag_field_name() {
        return $this->get_control_field_name('flagged');
    }

    /**
     * Get the name (in the sense a HTML name="" attribute, or a $_POST variable
     * name) to use for a question_type variable belonging to this question_attempt.
     *
     * See the comment on {@link question_attempt_step} for an explanation of
     * question type and behaviour variables.
     *
     * @param $varname The short form of the variable name.
     * @return string  The field name to use.
     */
    public function get_qt_field_name($varname) {
        return $this->get_field_prefix() . $varname;
    }

    /**
     * Get the name (in the sense a HTML name="" attribute, or a $_POST variable
     * name) to use for a question_type variable belonging to this question_attempt.
     *
     * See the comment on {@link question_attempt_step} for an explanation of
     * question type and behaviour variables.
     *
     * @param $varname The short form of the variable name.
     * @return string  The field name to use.
     */
    public function get_behaviour_field_name($varname) {
        return $this->get_field_prefix() . '-' . $varname;
    }

    /**
     * Get the name (in the sense a HTML name="" attribute, or a $_POST variable
     * name) to use for a control variables belonging to this question_attempt.
     *
     * Examples are :sequencecheck and :flagged
     *
     * @param $varname The short form of the variable name.
     * @return string  The field name to use.
     */
    public function get_control_field_name($varname) {
        return $this->get_field_prefix() . ':' . $varname;
    }

    /**
     * Get the prefix added to variable names to give field names for this
     * question attempt.
     *
     * You should not use this method directly. This is an implementation detail
     * anyway, but if you must access it, use {@link question_usage_by_activity::get_field_prefix()}.
     *
     * @param $varname The short form of the variable name.
     * @return string  The field name to use.
     */
    public function get_field_prefix() {
        return 'q' . $this->usageid . ':' . $this->slot . '_';
    }

    /**
     * Get one of the steps in this attempt.
     * For internal/test code use only.
     * @param int $i the step number.
     * @return question_attempt_step
     */
    public function get_step($i) {
        if ($i < 0 || $i >= count($this->steps)) {
            throw new coding_exception('Index out of bounds in question_attempt::get_step.');
        }
        return $this->steps[$i];
    }

    /**
     * Get the number of steps in this attempt.
     * For internal/test code use only.
     * @return int the number of steps we currently have.
     */
    public function get_num_steps() {
        return count($this->steps);
    }

    /**
     * Return the latest step in this question_attempt.
     * For internal/test code use only.
     * @return question_attempt_step
     */
    public function get_last_step() {
        if (count($this->steps) == 0) {
            return new question_null_step();
        }
        return end($this->steps);
    }

    /**
     * @return question_attempt_step_iterator for iterating over the steps in
     * this attempt, in order.
     */
    public function get_step_iterator() {
        return new question_attempt_step_iterator($this);
    }

    /**
     * The same as {@link get_step_iterator()}. However, for a
     * {@link question_attempt_with_restricted_history} this returns the full
     * list of steps, while {@link get_step_iterator()} returns only the
     * limited history.
     * @return question_attempt_step_iterator for iterating over the steps in
     * this attempt, in order.
     */
    public function get_full_step_iterator() {
        return $this->get_step_iterator();
    }

    /**
     * @return question_attempt_reverse_step_iterator for iterating over the steps in
     * this attempt, in reverse order.
     */
    public function get_reverse_step_iterator() {
        return new question_attempt_reverse_step_iterator($this);
    }

    /**
     * Get the qt data from the latest step that has any qt data. Return $default
     * array if it is no step has qt data.
     *
     * @param string $name the name of the variable to get.
     * @param mixed default the value to return no step has qt data.
     *      (Optional, defaults to an empty array.)
     * @return array|mixed the data, or $default if there is not any.
     */
    public function get_last_qt_data($default = array()) {
        foreach ($this->get_reverse_step_iterator() as $step) {
            $response = $step->get_qt_data();
            if (!empty($response)) {
                return $response;
            }
        }
        return $default;
    }

    /**
     * Get the last step with a particular question type varialbe set.
     * @param string $name the name of the variable to get.
     * @return question_attempt_step the last step, or a step with no variables
     * if there was not a real step.
     */
    public function get_last_step_with_qt_var($name) {
        foreach ($this->get_reverse_step_iterator() as $step) {
            if ($step->has_qt_var($name)) {
                return $step;
            }
        }
        return new question_attempt_step_read_only();
    }

    /**
     * Get the last step with a particular behaviour variable set.
     * @param string $name the name of the variable to get.
     * @return question_attempt_step the last step, or a step with no variables
     * if there was not a real step.
     */
    public function get_last_step_with_behaviour_var($name) {
        foreach ($this->get_reverse_step_iterator() as $step) {
            if ($step->has_behaviour_var($name)) {
                return $step;
            }
        }
        return new question_attempt_step_read_only();
    }

    /**
     * Get the latest value of a particular question type variable. That is, get
     * the value from the latest step that has it set. Return null if it is not
     * set in any step.
     *
     * @param string $name the name of the variable to get.
     * @param mixed default the value to return in the variable has never been set.
     *      (Optional, defaults to null.)
     * @return mixed string value, or $default if it has never been set.
     */
    public function get_last_qt_var($name, $default = null) {
        $step = $this->get_last_step_with_qt_var($name);
        if ($step->has_qt_var($name)) {
            return $step->get_qt_var($name);
        } else {
            return $default;
        }
    }

    /**
     * Get the latest set of files for a particular question type variable of
     * type question_attempt::PARAM_FILES.
     *
     * @param string $name the name of the associated variable.
     * @return array of {@link stored_files}.
     */
    public function get_last_qt_files($name, $contextid) {
        foreach ($this->get_reverse_step_iterator() as $step) {
            if ($step->has_qt_var($name)) {
                return $step->get_qt_files($name, $contextid);
            }
        }
        return array();
    }

    /**
     * Get the URL of a file that belongs to a response variable of this
     * question_attempt.
     * @param stored_file $file the file to link to.
     * @return string the URL of that file.
     */
    public function get_response_file_url(stored_file $file) {
        return file_encode_url(new moodle_url('/pluginfile.php'), '/' . implode('/', array(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $this->usageid,
                $this->slot,
                $file->get_itemid())) .
                $file->get_filepath() . $file->get_filename(), true);
    }

    /**
     * Prepare a draft file are for the files belonging the a response variable
     * of this question attempt. The draft area is populated with the files from
     * the most recent step having files.
     *
     * @param string $name the variable name the files belong to.
     * @param int $contextid the id of the context the quba belongs to.
     * @return int the draft itemid.
     */
    public function prepare_response_files_draft_itemid($name, $contextid) {
        foreach ($this->get_reverse_step_iterator() as $step) {
            if ($step->has_qt_var($name)) {
                return $step->prepare_response_files_draft_itemid($name, $contextid);
            }
        }

        // No files yet.
        $draftid = 0; // Will be filled in by file_prepare_draft_area.
        file_prepare_draft_area($draftid, $contextid, 'question', 'response_' . $name, null);
        return $draftid;
    }

    /**
     * Get the latest value of a particular behaviour variable. That is,
     * get the value from the latest step that has it set. Return null if it is
     * not set in any step.
     *
     * @param string $name the name of the variable to get.
     * @param mixed default the value to return in the variable has never been set.
     *      (Optional, defaults to null.)
     * @return mixed string value, or $default if it has never been set.
     */
    public function get_last_behaviour_var($name, $default = null) {
        foreach ($this->get_reverse_step_iterator() as $step) {
            if ($step->has_behaviour_var($name)) {
                return $step->get_behaviour_var($name);
            }
        }
        return $default;
    }

    /**
     * Get the current state of this question attempt. That is, the state of the
     * latest step.
     * @return question_state
     */
    public function get_state() {
        return $this->get_last_step()->get_state();
    }

    /**
     * @param bool $showcorrectness Whether right/partial/wrong states should
     * be distinguised.
     * @return string A brief textual description of the current state.
     */
    public function get_state_string($showcorrectness) {
        return $this->behaviour->get_state_string($showcorrectness);
    }

    /**
     * @param bool $showcorrectness Whether right/partial/wrong states should
     * be distinguised.
     * @return string a CSS class name for the current state.
     */
    public function get_state_class($showcorrectness) {
        return $this->get_state()->get_state_class($showcorrectness);
    }

    /**
     * @return int the timestamp of the most recent step in this question attempt.
     */
    public function get_last_action_time() {
        return $this->get_last_step()->get_timecreated();
    }

    /**
     * Get the current fraction of this question attempt. That is, the fraction
     * of the latest step, or null if this question has not yet been graded.
     * @return number the current fraction.
     */
    public function get_fraction() {
        return $this->get_last_step()->get_fraction();
    }

    /** @return bool whether this question attempt has a non-zero maximum mark. */
    public function has_marks() {
        // Since grades are stored in the database as NUMBER(12,7).
        return $this->maxmark >= 0.00000005;
    }

    /**
     * @return number the current mark for this question.
     * {@link get_fraction()} * {@link get_max_mark()}.
     */
    public function get_mark() {
        return $this->fraction_to_mark($this->get_fraction());
    }

    /**
     * This is used by the manual grading code, particularly in association with
     * validation. If there is a mark submitted in the request, then use that,
     * otherwise use the latest mark for this question.
     * @return number the current mark for this question.
     * {@link get_fraction()} * {@link get_max_mark()}.
     */
    public function get_current_manual_mark() {
        $mark = $this->get_submitted_var($this->get_behaviour_field_name('mark'), question_attempt::PARAM_MARK);
        if (is_null($mark)) {
            return $this->get_mark();
        } else {
            return $mark;
        }
    }

    /**
     * @param number|null $fraction a fraction.
     * @return number|null the corresponding mark.
     */
    public function fraction_to_mark($fraction) {
        if (is_null($fraction)) {
            return null;
        }
        return $fraction * $this->maxmark;
    }

    /** @return number the maximum mark possible for this question attempt. */
    public function get_max_mark() {
        return $this->maxmark;
    }

    /** @return number the maximum mark possible for this question attempt. */
    public function get_min_fraction() {
        if (is_null($this->minfraction)) {
            throw new coding_exception('This question_attempt has not been started yet, the min fraction is not yet konwn.');
        }
        return $this->minfraction;
    }

    /**
     * The current mark, formatted to the stated number of decimal places. Uses
     * {@link format_float()} to format floats according to the current locale.
     * @param int $dp number of decimal places.
     * @return string formatted mark.
     */
    public function format_mark($dp) {
        return $this->format_fraction_as_mark($this->get_fraction(), $dp);
    }

    /**
     * The current mark, formatted to the stated number of decimal places. Uses
     * {@link format_float()} to format floats according to the current locale.
     * @param int $dp number of decimal places.
     * @return string formatted mark.
     */
    public function format_fraction_as_mark($fraction, $dp) {
        return format_float($this->fraction_to_mark($fraction), $dp);
    }

    /**
     * The maximum mark for this question attempt, formatted to the stated number
     * of decimal places. Uses {@link format_float()} to format floats according
     * to the current locale.
     * @param int $dp number of decimal places.
     * @return string formatted maximum mark.
     */
    public function format_max_mark($dp) {
        return format_float($this->maxmark, $dp);
    }

    /**
     * Return the hint that applies to the question in its current state, or null.
     * @return question_hint|null
     */
    public function get_applicable_hint() {
        return $this->behaviour->get_applicable_hint();
    }

    /**
     * Produce a plain-text summary of what the user did during a step.
     * @param question_attempt_step $step the step in quetsion.
     * @return string a summary of what was done during that step.
     */
    public function summarise_action(question_attempt_step $step) {
        return $this->behaviour->summarise_action($step);
    }

    /**
     * Helper function used by {@link rewrite_pluginfile_urls()} and
     * {@link rewrite_response_pluginfile_urls()}.
     * @return array ids that need to go into the file paths.
     */
    protected function extra_file_path_components() {
        return array($this->get_usage_id(), $this->get_slot());
    }

    /**
     * Calls {@link question_rewrite_question_urls()} with appropriate parameters
     * for content belonging to this question.
     * @param string $text the content to output.
     * @param string $component the component name (normally 'question' or 'qtype_...')
     * @param string $filearea the name of the file area.
     * @param int $itemid the item id.
     * @return srting the content with the URLs rewritten.
     */
    public function rewrite_pluginfile_urls($text, $component, $filearea, $itemid) {
        return question_rewrite_question_urls($text, 'pluginfile.php',
                $this->question->contextid, $component, $filearea,
                $this->extra_file_path_components(), $itemid);
    }

    /**
     * Calls {@link question_rewrite_question_urls()} with appropriate parameters
     * for content belonging to responses to this question.
     *
     * @param string $text the text to update the URLs in.
     * @param int $contextid the id of the context the quba belongs to.
     * @param string $name the variable name the files belong to.
     * @param question_attempt_step $step the step the response is coming from.
     * @return srting the content with the URLs rewritten.
     */
    public function rewrite_response_pluginfile_urls($text, $contextid, $name,
            question_attempt_step $step) {
        return $step->rewrite_response_pluginfile_urls($text, $contextid, $name,
                $this->extra_file_path_components());
    }

    /**
     * Get the {@link core_question_renderer}, in collaboration with appropriate
     * {@link qbehaviour_renderer} and {@link qtype_renderer} subclasses, to generate the
     * HTML to display this question attempt in its current state.
     * @param question_display_options $options controls how the question is rendered.
     * @param string|null $number The question number to display.
     * @return string HTML fragment representing the question.
     */
    public function render($options, $number, $page = null) {
        if (is_null($page)) {
            global $PAGE;
            $page = $PAGE;
        }
        $qoutput = $page->get_renderer('core', 'question');
        $qtoutput = $this->question->get_renderer($page);
        return $this->behaviour->render($options, $number, $qoutput, $qtoutput);
    }

    /**
     * Generate any bits of HTML that needs to go in the <head> tag when this question
     * attempt is displayed in the body.
     * @return string HTML fragment.
     */
    public function render_head_html($page = null) {
        if (is_null($page)) {
            global $PAGE;
            $page = $PAGE;
        }
        // TODO go via behaviour.
        return $this->question->get_renderer($page)->head_code($this) .
                $this->behaviour->get_renderer($page)->head_code($this);
    }

    /**
     * Like {@link render_question()} but displays the question at the past step
     * indicated by $seq, rather than showing the latest step.
     *
     * @param int $seq the seq number of the past state to display.
     * @param question_display_options $options controls how the question is rendered.
     * @param string|null $number The question number to display. 'i' is a special
     *      value that gets displayed as Information. Null means no number is displayed.
     * @return string HTML fragment representing the question.
     */
    public function render_at_step($seq, $options, $number, $preferredbehaviour) {
        $restrictedqa = new question_attempt_with_restricted_history($this, $seq, $preferredbehaviour);
        return $restrictedqa->render($options, $number);
    }

    /**
     * Checks whether the users is allow to be served a particular file.
     * @param question_display_options $options the options that control display of the question.
     * @param string $component the name of the component we are serving files for.
     * @param string $filearea the name of the file area.
     * @param array $args the remaining bits of the file path.
     * @param bool $forcedownload whether the user must be forced to download the file.
     * @return bool true if the user can access this file.
     */
    public function check_file_access($options, $component, $filearea, $args, $forcedownload) {
        return $this->behaviour->check_file_access($options, $component, $filearea, $args, $forcedownload);
    }

    /**
     * Add a step to this question attempt.
     * @param question_attempt_step $step the new step.
     */
    protected function add_step(question_attempt_step $step) {
        $this->steps[] = $step;
        end($this->steps);
        $this->observer->notify_step_added($step, $this, key($this->steps));
    }

    /**
     * Use a strategy to pick a variant.
     * @param question_variant_selection_strategy $variantstrategy a strategy.
     * @return int the selected variant.
     */
    public function select_variant(question_variant_selection_strategy $variantstrategy) {
        return $variantstrategy->choose_variant($this->get_question()->get_num_variants(),
                $this->get_question()->get_variants_selection_seed());
    }

    /**
     * Start this question attempt.
     *
     * You should not call this method directly. Call
     * {@link question_usage_by_activity::start_question()} instead.
     *
     * @param string|question_behaviour $preferredbehaviour the name of the
     *      desired archetypal behaviour, or an actual model instance.
     * @param int $variant the variant of the question to start. Between 1 and
     *      $this->get_question()->get_num_variants() inclusive.
     * @param array $submitteddata optional, used when re-starting to keep the same initial state.
     * @param int $timestamp optional, the timstamp to record for this action. Defaults to now.
     * @param int $userid optional, the user to attribute this action to. Defaults to the current user.
     */
    public function start($preferredbehaviour, $variant, $submitteddata = array(),
            $timestamp = null, $userid = null) {

        // Initialise the behaviour.
        $this->variant = $variant;
        if (is_string($preferredbehaviour)) {
            $this->behaviour =
                    $this->question->make_behaviour($this, $preferredbehaviour);
        } else {
            $class = get_class($preferredbehaviour);
            $this->behaviour = new $class($this, $preferredbehaviour);
        }

        // Record the minimum fraction.
        $this->minfraction = $this->behaviour->get_min_fraction();

        // Initialise the first step.
        $firststep = new question_attempt_step($submitteddata, $timestamp, $userid);
        $firststep->set_state(question_state::$todo);
        if ($submitteddata) {
            $this->question->apply_attempt_state($firststep);
        } else {
            $this->behaviour->init_first_step($firststep, $variant);
        }
        $this->add_step($firststep);

        // Record questionline and correct answer.
        $this->questionsummary = $this->behaviour->get_question_summary();
        $this->rightanswer = $this->behaviour->get_right_answer_summary();
    }

    /**
     * Start this question attempt, starting from the point that the previous
     * attempt $oldqa had reached.
     *
     * You should not call this method directly. Call
     * {@link question_usage_by_activity::start_question_based_on()} instead.
     *
     * @param question_attempt $oldqa a previous attempt at this quetsion that
     *      defines the starting point.
     */
    public function start_based_on(question_attempt $oldqa) {
        $this->start($oldqa->behaviour, $oldqa->get_variant(), $oldqa->get_resume_data());
    }

    /**
     * Used by {@link start_based_on()} to get the data needed to start a new
     * attempt from the point this attempt has go to.
     * @return array name => value pairs.
     */
    protected function get_resume_data() {
        return $this->behaviour->get_resume_data();
    }

    /**
     * Get a particular parameter from the current request. A wrapper round
     * {@link optional_param()}, except that the results is returned without
     * slashes.
     * @param string $name the paramter name.
     * @param int $type one of the standard PARAM_... constants, or one of the
     *      special extra constands defined by this class.
     * @param array $postdata (optional, only inteded for testing use) take the
     *      data from this array, instead of from $_POST.
     * @return mixed the requested value.
     */
    public function get_submitted_var($name, $type, $postdata = null) {
        switch ($type) {
            case self::PARAM_MARK:
                // Special case to work around PARAM_NUMBER converting '' to 0.
                $mark = $this->get_submitted_var($name, PARAM_RAW_TRIMMED, $postdata);
                if ($mark === '') {
                    return $mark;
                } else {
                    return $this->get_submitted_var($name, PARAM_NUMBER, $postdata);
                }

            case self::PARAM_FILES:
                return $this->process_response_files($name, $name, $postdata);

            case self::PARAM_CLEANHTML_FILES:
                $var = $this->get_submitted_var($name, PARAM_CLEANHTML, $postdata);
                return $this->process_response_files($name, $name . ':itemid', $postdata, $var);

            default:
                if (is_null($postdata)) {
                    $var = optional_param($name, null, $type);
                } else if (array_key_exists($name, $postdata)) {
                    $var = clean_param($postdata[$name], $type);
                } else {
                    $var = null;
                }

                return $var;
        }
    }

    /**
     * Handle a submitted variable representing uploaded files.
     * @param string $name the field name.
     * @param string $draftidname the field name holding the draft file area id.
     * @param array $postdata (optional, only inteded for testing use) take the
     *      data from this array, instead of from $_POST. At the moment, this
     *      behaves as if there were no files.
     * @param string $text optional reponse text.
     * @return question_file_saver that can be used to save the files later.
     */
    protected function process_response_files($name, $draftidname, $postdata = null, $text = null) {
        if ($postdata) {
            // There can be no files with test data (at the moment).
            return null;
        }

        $draftitemid = file_get_submitted_draft_itemid($draftidname);
        if (!$draftitemid) {
            return null;
        }

        return new question_file_saver($draftitemid, 'question', 'response_' .
                str_replace($this->get_field_prefix(), '', $name), $text);
    }

    /**
     * Get any data from the request that matches the list of expected params.
     * @param array $expected variable name => PARAM_... constant.
     * @param string $extraprefix '-' or ''.
     * @return array name => value.
     */
    protected function get_expected_data($expected, $postdata, $extraprefix) {
        $submitteddata = array();
        foreach ($expected as $name => $type) {
            $value = $this->get_submitted_var(
                    $this->get_field_prefix() . $extraprefix . $name, $type, $postdata);
            if (!is_null($value)) {
                $submitteddata[$extraprefix . $name] = $value;
            }
        }
        return $submitteddata;
    }

    /**
     * Get all the submitted question type data for this question, whithout checking
     * that it is valid or cleaning it in any way.
     * @return array name => value.
     */
    protected function get_all_submitted_qt_vars($postdata) {
        if (is_null($postdata)) {
            $postdata = $_POST;
        }

        $pattern = '/^' . preg_quote($this->get_field_prefix()) . '[^-:]/';
        $prefixlen = strlen($this->get_field_prefix());

        $submitteddata = array();
        foreach ($_POST as $name => $value) {
            if (preg_match($pattern, $name)) {
                $submitteddata[substr($name, $prefixlen)] = $value;
            }
        }

        return $submitteddata;
    }

    /**
     * Get all the sumbitted data belonging to this question attempt from the
     * current request.
     * @param array $postdata (optional, only inteded for testing use) take the
     *      data from this array, instead of from $_POST.
     * @return array name => value pairs that could be passed to {@link process_action()}.
     */
    public function get_submitted_data($postdata = null) {
        $submitteddata = $this->get_expected_data(
                $this->behaviour->get_expected_data(), $postdata, '-');

        $expected = $this->behaviour->get_expected_qt_data();
        if ($expected === self::USE_RAW_DATA) {
            $submitteddata += $this->get_all_submitted_qt_vars($postdata);
        } else {
            $submitteddata += $this->get_expected_data($expected, $postdata, '');
        }
        return $submitteddata;
    }

    /**
     * Get a set of response data for this question attempt that would get the
     * best possible mark. If it is not possible to compute a correct
     * response, this method should return null.
     * @return array|null name => value pairs that could be passed to {@link process_action()}.
     */
    public function get_correct_response() {
        $response = $this->question->get_correct_response();
        if (is_null($response)) {
            return null;
        }
        $imvars = $this->behaviour->get_correct_response();
        foreach ($imvars as $name => $value) {
            $response['-' . $name] = $value;
        }
        return $response;
    }

    /**
     * Change the quetsion summary. Note, that this is almost never necessary.
     * This method was only added to work around a limitation of the Opaque
     * protocol, which only sends questionLine at the end of an attempt.
     * @param $questionsummary the new summary to set.
     */
    public function set_question_summary($questionsummary) {
        $this->questionsummary = $questionsummary;
        $this->observer->notify_attempt_modified($this);
    }

    /**
     * @return string a simple textual summary of the question that was asked.
     */
    public function get_question_summary() {
        return $this->questionsummary;
    }

    /**
     * @return string a simple textual summary of response given.
     */
    public function get_response_summary() {
        return $this->responsesummary;
    }

    /**
     * @return string a simple textual summary of the correct resonse.
     */
    public function get_right_answer_summary() {
        return $this->rightanswer;
    }

    /**
     * Perform the action described by $submitteddata.
     * @param array $submitteddata the submitted data the determines the action.
     * @param int $timestamp the time to record for the action. (If not given, use now.)
     * @param int $userid the user to attribute the aciton to. (If not given, use the current user.)
     */
    public function process_action($submitteddata, $timestamp = null, $userid = null) {
        $pendingstep = new question_attempt_pending_step($submitteddata, $timestamp, $userid);
        if ($this->behaviour->process_action($pendingstep) == self::KEEP) {
            $this->add_step($pendingstep);
            if ($pendingstep->response_summary_changed()) {
                $this->responsesummary = $pendingstep->get_new_response_summary();
            }
        }
    }

    /**
     * Perform a finish action on this question attempt. This corresponds to an
     * external finish action, for example the user pressing Submit all and finish
     * in the quiz, rather than using one of the controls that is part of the
     * question.
     *
     * @param int $timestamp the time to record for the action. (If not given, use now.)
     * @param int $userid the user to attribute the aciton to. (If not given, use the current user.)
     */
    public function finish($timestamp = null, $userid = null) {
        $this->process_action(array('-finish' => 1), $timestamp, $userid);
    }

    /**
     * Perform a regrade. This replays all the actions from $oldqa into this
     * attempt.
     * @param question_attempt $oldqa the attempt to regrade.
     * @param bool $finished whether the question attempt should be forced to be finished
     *      after the regrade, or whether it may still be in progress (default false).
     */
    public function regrade(question_attempt $oldqa, $finished) {
        $first = true;
        foreach ($oldqa->get_step_iterator() as $step) {
            if ($first) {
                $first = false;
                $this->start($oldqa->behaviour, $oldqa->get_variant(), $step->get_all_data(),
                        $step->get_timecreated(), $step->get_user_id());
            } else {
                $this->process_action($step->get_submitted_data(),
                        $step->get_timecreated(), $step->get_user_id());
            }
        }
        if ($finished) {
            $this->finish();
        }
    }

    /**
     * Perform a manual grading action on this attempt.
     * @param $comment the comment being added.
     * @param $mark the new mark. (Optional, if not given, then only a comment is added.)
     * @param int $timestamp the time to record for the action. (If not given, use now.)
     * @param int $userid the user to attribute the aciton to. (If not given, use the current user.)
     * @return unknown_type
     */
    public function manual_grade($comment, $mark, $timestamp = null, $userid = null) {
        $submitteddata = array('-comment' => $comment);
        if (!is_null($mark)) {
            $submitteddata['-mark'] = $mark;
            $submitteddata['-maxmark'] = $this->maxmark;
        }
        $this->process_action($submitteddata, $timestamp, $userid);
    }

    /** @return bool Whether this question attempt has had a manual comment added. */
    public function has_manual_comment() {
        foreach ($this->steps as $step) {
            if ($step->has_behaviour_var('comment')) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array(string, int) the most recent manual comment that was added
     * to this question, and the FORMAT_... it is.
     */
    public function get_manual_comment() {
        foreach ($this->get_reverse_step_iterator() as $step) {
            if ($step->has_behaviour_var('comment')) {
                return array($step->get_behaviour_var('comment'),
                        $step->get_behaviour_var('commentformat'));
            }
        }
        return array(null, null);
    }

    /**
     * @return array subpartid => object with fields
     *      ->responseclassid matches one of the values returned from quetion_type::get_possible_responses.
     *      ->response the actual response the student gave to this part, as a string.
     *      ->fraction the credit awarded for this subpart, may be null.
     *      returns an empty array if no analysis is possible.
     */
    public function classify_response() {
        return $this->behaviour->classify_response();
    }

    /**
     * Create a question_attempt_step from records loaded from the database.
     *
     * For internal use only.
     *
     * @param Iterator $records Raw records loaded from the database.
     * @param int $questionattemptid The id of the question_attempt to extract.
     * @return question_attempt The newly constructed question_attempt_step.
     */
    public static function load_from_records($records, $questionattemptid,
            question_usage_observer $observer, $preferredbehaviour) {
        $record = $records->current();
        while ($record->questionattemptid != $questionattemptid) {
            $record = $records->next();
            if (!$records->valid()) {
                throw new coding_exception("Question attempt $questionattemptid not found in the database.");
            }
            $record = $records->current();
        }

        try {
            $question = question_bank::load_question($record->questionid);
        } catch (Exception $e) {
            // The question must have been deleted somehow. Create a missing
            // question to use in its place.
            $question = question_bank::get_qtype('missingtype')->make_deleted_instance(
                    $record->questionid, $record->maxmark + 0);
        }

        $qa = new question_attempt($question, $record->questionusageid,
                null, $record->maxmark + 0);
        $qa->set_database_id($record->questionattemptid);
        $qa->set_number_in_usage($record->slot);
        $qa->variant = $record->variant + 0;
        $qa->minfraction = $record->minfraction + 0;
        $qa->set_flagged($record->flagged);
        $qa->questionsummary = $record->questionsummary;
        $qa->rightanswer = $record->rightanswer;
        $qa->responsesummary = $record->responsesummary;
        $qa->timemodified = $record->timemodified;

        $qa->behaviour = question_engine::make_behaviour(
                $record->behaviour, $qa, $preferredbehaviour);

        $i = 0;
        while ($record && $record->questionattemptid == $questionattemptid && !is_null($record->attemptstepid)) {
            $qa->steps[$i] = question_attempt_step::load_from_records($records, $record->attemptstepid);
            if ($i == 0) {
                $question->apply_attempt_state($qa->steps[0]);
            }
            $i++;
            if ($records->valid()) {
                $record = $records->current();
            } else {
                $record = false;
            }
        }

        $qa->observer = $observer;

        return $qa;
    }
}


/**
 * This subclass of question_attempt pretends that only part of the step history
 * exists. It is used for rendering the question in past states.
 *
 * All methods that try to modify the question_attempt throw exceptions.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_attempt_with_restricted_history extends question_attempt {
    /**
     * @var question_attempt the underlying question_attempt.
     */
    protected $baseqa;

    /**
     * Create a question_attempt_with_restricted_history
     * @param question_attempt $baseqa The question_attempt to make a restricted version of.
     * @param int $lastseq the index of the last step to include.
     * @param string $preferredbehaviour the preferred behaviour. It is slightly
     *      annoyting that this needs to be passed, but unavoidable for now.
     */
    public function __construct(question_attempt $baseqa, $lastseq, $preferredbehaviour) {
        $this->baseqa = $baseqa->get_full_qa();

        if ($lastseq < 0 || $lastseq >= $this->baseqa->get_num_steps()) {
            throw new coding_exception('$lastseq out of range', $lastseq);
        }

        $this->steps = array_slice($this->baseqa->steps, 0, $lastseq + 1);
        $this->observer = new question_usage_null_observer();

        // This should be a straight copy of all the remaining fields.
        $this->id = $this->baseqa->id;
        $this->usageid = $this->baseqa->usageid;
        $this->slot = $this->baseqa->slot;
        $this->question = $this->baseqa->question;
        $this->maxmark = $this->baseqa->maxmark;
        $this->minfraction = $this->baseqa->minfraction;
        $this->questionsummary = $this->baseqa->questionsummary;
        $this->responsesummary = $this->baseqa->responsesummary;
        $this->rightanswer = $this->baseqa->rightanswer;
        $this->flagged = $this->baseqa->flagged;

        // Except behaviour, where we need to create a new one.
        $this->behaviour = question_engine::make_behaviour(
                $this->baseqa->get_behaviour_name(), $this, $preferredbehaviour);
    }

    public function get_full_qa() {
        return $this->baseqa;
    }

    public function get_full_step_iterator() {
        return $this->baseqa->get_step_iterator();
    }

    protected function add_step(question_attempt_step $step) {
        coding_exception('Cannot modify a question_attempt_with_restricted_history.');
    }
    public function process_action($submitteddata, $timestamp = null, $userid = null) {
        coding_exception('Cannot modify a question_attempt_with_restricted_history.');
    }
    public function start($preferredbehaviour, $variant, $submitteddata = array(), $timestamp = null, $userid = null) {
        coding_exception('Cannot modify a question_attempt_with_restricted_history.');
    }

    public function set_database_id($id) {
        coding_exception('Cannot modify a question_attempt_with_restricted_history.');
    }
    public function set_flagged($flagged) {
        coding_exception('Cannot modify a question_attempt_with_restricted_history.');
    }
    public function set_number_in_usage($slot) {
        coding_exception('Cannot modify a question_attempt_with_restricted_history.');
    }
    public function set_question_summary($questionsummary) {
        coding_exception('Cannot modify a question_attempt_with_restricted_history.');
    }
    public function set_usage_id($usageid) {
        coding_exception('Cannot modify a question_attempt_with_restricted_history.');
    }
}


/**
 * A class abstracting access to the {@link question_attempt::$states} array.
 *
 * This is actively linked to question_attempt. If you add an new step
 * mid-iteration, then it will be included.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_attempt_step_iterator implements Iterator, ArrayAccess {
    /** @var question_attempt the question_attempt being iterated over. */
    protected $qa;
    /** @var integer records the current position in the iteration. */
    protected $i;

    /**
     * Do not call this constructor directly.
     * Use {@link question_attempt::get_step_iterator()}.
     * @param question_attempt $qa the attempt to iterate over.
     */
    public function __construct(question_attempt $qa) {
        $this->qa = $qa;
        $this->rewind();
    }

    /** @return question_attempt_step */
    public function current() {
        return $this->offsetGet($this->i);
    }
    /** @return int */
    public function key() {
        return $this->i;
    }
    public function next() {
        ++$this->i;
    }
    public function rewind() {
        $this->i = 0;
    }
    /** @return bool */
    public function valid() {
        return $this->offsetExists($this->i);
    }

    /** @return bool */
    public function offsetExists($i) {
        return $i >= 0 && $i < $this->qa->get_num_steps();
    }
    /** @return question_attempt_step */
    public function offsetGet($i) {
        return $this->qa->get_step($i);
    }
    public function offsetSet($offset, $value) {
        throw new coding_exception('You are only allowed read-only access to question_attempt::states through a question_attempt_step_iterator. Cannot set.');
    }
    public function offsetUnset($offset) {
        throw new coding_exception('You are only allowed read-only access to question_attempt::states through a question_attempt_step_iterator. Cannot unset.');
    }
}


/**
 * A variant of {@link question_attempt_step_iterator} that iterates through the
 * steps in reverse order.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_attempt_reverse_step_iterator extends question_attempt_step_iterator {
    public function next() {
        --$this->i;
    }

    public function rewind() {
        $this->i = $this->qa->get_num_steps() - 1;
    }
}
