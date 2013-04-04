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
 * This file defines the question usage class, and a few related classes.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * This class keeps track of a group of questions that are being attempted,
 * and which state, and so on, each one is currently in.
 *
 * A quiz attempt or a lesson attempt could use an instance of this class to
 * keep track of all the questions in the attempt and process student submissions.
 * It is basically a collection of {@question_attempt} objects.
 *
 * The questions being attempted as part of this usage are identified by an integer
 * that is passed into many of the methods as $slot. ($question->id is not
 * used so that the same question can be used more than once in an attempt.)
 *
 * Normally, calling code should be able to do everything it needs to be calling
 * methods of this class. You should not normally need to get individual
 * {@question_attempt} objects and play around with their inner workind, in code
 * that it outside the quetsion engine.
 *
 * Instances of this class correspond to rows in the question_usages table.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_usage_by_activity {
    /**
     * @var integer|string the id for this usage. If this usage was loaded from
     * the database, then this is the database id. Otherwise a unique random
     * string is used.
     */
    protected $id = null;

    /**
     * @var string name of an archetypal behaviour, that should be used
     * by questions in this usage if possible.
     */
    protected $preferredbehaviour = null;

    /** @var context the context this usage belongs to. */
    protected $context;

    /** @var string plugin name of the plugin this usage belongs to. */
    protected $owningcomponent;

    /** @var array {@link question_attempt}s that make up this usage. */
    protected $questionattempts = array();

    /** @var question_usage_observer that tracks changes to this usage. */
    protected $observer;

    /**
     * Create a new instance. Normally, calling code should use
     * {@link question_engine::make_questions_usage_by_activity()} or
     * {@link question_engine::load_questions_usage_by_activity()} rather than
     * calling this constructor directly.
     *
     * @param string $component the plugin creating this attempt. For example mod_quiz.
     * @param object $context the context this usage belongs to.
     */
    public function __construct($component, $context) {
        $this->owningcomponent = $component;
        $this->context = $context;
        $this->observer = new question_usage_null_observer();
    }

    /**
     * @param string $behaviour the name of an archetypal behaviour, that should
     * be used by questions in this usage if possible.
     */
    public function set_preferred_behaviour($behaviour) {
        $this->preferredbehaviour = $behaviour;
        $this->observer->notify_modified();
    }

    /** @return string the name of the preferred behaviour. */
    public function get_preferred_behaviour() {
        return $this->preferredbehaviour;
    }

    /** @return context the context this usage belongs to. */
    public function get_owning_context() {
        return $this->context;
    }

    /** @return string the name of the plugin that owns this attempt. */
    public function get_owning_component() {
        return $this->owningcomponent;
    }

    /** @return int|string If this usage came from the database, then the id
     * from the question_usages table is returned. Otherwise a random string is
     * returned. */
    public function get_id() {
        if (is_null($this->id)) {
            $this->id = random_string(10);
        }
        return $this->id;
    }

    /**
     * For internal use only. Used by {@link question_engine_data_mapper} to set
     * the id when a usage is saved to the database.
     * @param int $id the newly determined id for this usage.
     */
    public function set_id_from_database($id) {
        $this->id = $id;
        foreach ($this->questionattempts as $qa) {
            $qa->set_usage_id($id);
        }
    }

    /** @return question_usage_observer that is tracking changes made to this usage. */
    public function get_observer() {
        return $this->observer;
    }

    /**
     * You should almost certainly not call this method from your code. It is for
     * internal use only.
     * @param question_usage_observer that should be used to tracking changes made to this usage.
     */
    public function set_observer($observer) {
        $this->observer = $observer;
        foreach ($this->questionattempts as $qa) {
            $qa->set_observer($observer);
        }
    }

    /**
     * Add another question to this usage.
     *
     * The added question is not started until you call {@link start_question()}
     * on it.
     *
     * @param question_definition $question the question to add.
     * @param number $maxmark the maximum this question will be marked out of in
     *      this attempt (optional). If not given, $question->defaultmark is used.
     * @return int the number used to identify this question within this usage.
     */
    public function add_question(question_definition $question, $maxmark = null) {
        $qa = new question_attempt($question, $this->get_id(), $this->observer, $maxmark);
        if (count($this->questionattempts) == 0) {
            $this->questionattempts[1] = $qa;
        } else {
            $this->questionattempts[] = $qa;
        }
        end($this->questionattempts); // Ready to get the last key on the next line.
        $qa->set_slot(key($this->questionattempts));
        $this->observer->notify_attempt_added($qa);
        return $qa->get_slot();
    }

    /**
     * Get the question_definition for a question in this attempt.
     * @param int $slot the number used to identify this question within this usage.
     * @return question_definition the requested question object.
     */
    public function get_question($slot) {
        return $this->get_question_attempt($slot)->get_question();
    }

    /** @return array all the identifying numbers of all the questions in this usage. */
    public function get_slots() {
        return array_keys($this->questionattempts);
    }

    /** @return int the identifying number of the first question that was added to this usage. */
    public function get_first_question_number() {
        reset($this->questionattempts);
        return key($this->questionattempts);
    }

    /** @return int the number of questions that are currently in this usage. */
    public function question_count() {
        return count($this->questionattempts);
    }

    /**
     * Note the part of the {@link question_usage_by_activity} comment that explains
     * that {@link question_attempt} objects should be considered part of the inner
     * workings of the question engine, and should not, if possible, be accessed directly.
     *
     * @return question_attempt_iterator for iterating over all the questions being
     * attempted. as part of this usage.
     */
    public function get_attempt_iterator() {
        return new question_attempt_iterator($this);
    }

    /**
     * Check whether $number actually corresponds to a question attempt that is
     * part of this usage. Throws an exception if not.
     *
     * @param int $slot a number allegedly identifying a question within this usage.
     */
    protected function check_slot($slot) {
        if (!array_key_exists($slot, $this->questionattempts)) {
            throw new coding_exception('There is no question_attempt number ' . $slot .
                    ' in this attempt.');
        }
    }

    /**
     * Note the part of the {@link question_usage_by_activity} comment that explains
     * that {@link question_attempt} objects should be considered part of the inner
     * workings of the question engine, and should not, if possible, be accessed directly.
     *
     * @param int $slot the number used to identify this question within this usage.
     * @return question_attempt the corresponding {@link question_attempt} object.
     */
    public function get_question_attempt($slot) {
        $this->check_slot($slot);
        return $this->questionattempts[$slot];
    }

    /**
     * Get the current state of the attempt at a question.
     * @param int $slot the number used to identify this question within this usage.
     * @return question_state.
     */
    public function get_question_state($slot) {
        return $this->get_question_attempt($slot)->get_state();
    }

    /**
     * @param int $slot the number used to identify this question within this usage.
     * @param bool $showcorrectness Whether right/partial/wrong states should
     * be distinguised.
     * @return string A brief textual description of the current state.
     */
    public function get_question_state_string($slot, $showcorrectness) {
        return $this->get_question_attempt($slot)->get_state_string($showcorrectness);
    }

    /**
     * @param int $slot the number used to identify this question within this usage.
     * @param bool $showcorrectness Whether right/partial/wrong states should
     * be distinguised.
     * @return string a CSS class name for the current state.
     */
    public function get_question_state_class($slot, $showcorrectness) {
        return $this->get_question_attempt($slot)->get_state_class($showcorrectness);
    }

    /**
     * Get the time of the most recent action performed on a question.
     * @param int $slot the number used to identify this question within this usage.
     * @return int timestamp.
     */
    public function get_question_action_time($slot) {
        return $this->get_question_attempt($slot)->get_last_action_time();
    }

    /**
     * Get the current fraction awarded for the attempt at a question.
     * @param int $slot the number used to identify this question within this usage.
     * @return number|null The current fraction for this question, or null if one has
     * not been assigned yet.
     */
    public function get_question_fraction($slot) {
        return $this->get_question_attempt($slot)->get_fraction();
    }

    /**
     * Get the current mark awarded for the attempt at a question.
     * @param int $slot the number used to identify this question within this usage.
     * @return number|null The current mark for this question, or null if one has
     * not been assigned yet.
     */
    public function get_question_mark($slot) {
        return $this->get_question_attempt($slot)->get_mark();
    }

    /**
     * Get the maximum mark possible for the attempt at a question.
     * @param int $slot the number used to identify this question within this usage.
     * @return number the available marks for this question.
     */
    public function get_question_max_mark($slot) {
        return $this->get_question_attempt($slot)->get_max_mark();
    }

    /**
     * Get the current mark awarded for the attempt at a question.
     * @param int $slot the number used to identify this question within this usage.
     * @return number|null The current mark for this question, or null if one has
     * not been assigned yet.
     */
    public function get_total_mark() {
        $mark = 0;
        foreach ($this->questionattempts as $qa) {
            if ($qa->get_max_mark() > 0 && $qa->get_state() == question_state::$needsgrading) {
                return null;
            }
            $mark += $qa->get_mark();
        }
        return $mark;
    }

    /**
     * @return string a simple textual summary of the question that was asked.
     */
    public function get_question_summary($slot) {
        return $this->get_question_attempt($slot)->get_question_summary();
    }

    /**
     * @return string a simple textual summary of response given.
     */
    public function get_response_summary($slot) {
        return $this->get_question_attempt($slot)->get_response_summary();
    }

    /**
     * @return string a simple textual summary of the correct resonse.
     */
    public function get_right_answer_summary($slot) {
        return $this->get_question_attempt($slot)->get_right_answer_summary();
    }

    /**
     * Get the {@link core_question_renderer}, in collaboration with appropriate
     * {@link qbehaviour_renderer} and {@link qtype_renderer} subclasses, to generate the
     * HTML to display this question.
     * @param int $slot the number used to identify this question within this usage.
     * @param question_display_options $options controls how the question is rendered.
     * @param string|null $number The question number to display. 'i' is a special
     *      value that gets displayed as Information. Null means no number is displayed.
     * @return string HTML fragment representing the question.
     */
    public function render_question($slot, $options, $number = null) {
        $options->context = $this->context;
        return $this->get_question_attempt($slot)->render($options, $number);
    }

    /**
     * Generate any bits of HTML that needs to go in the <head> tag when this question
     * is displayed in the body.
     * @param int $slot the number used to identify this question within this usage.
     * @return string HTML fragment.
     */
    public function render_question_head_html($slot) {
        //$options->context = $this->context;
        return $this->get_question_attempt($slot)->render_head_html();
    }

    /**
     * Like {@link render_question()} but displays the question at the past step
     * indicated by $seq, rather than showing the latest step.
     *
     * @param int $slot the number used to identify this question within this usage.
     * @param int $seq the seq number of the past state to display.
     * @param question_display_options $options controls how the question is rendered.
     * @param string|null $number The question number to display. 'i' is a special
     *      value that gets displayed as Information. Null means no number is displayed.
     * @return string HTML fragment representing the question.
     */
    public function render_question_at_step($slot, $seq, $options, $number = null) {
        $options->context = $this->context;
        return $this->get_question_attempt($slot)->render_at_step(
                $seq, $options, $number, $this->preferredbehaviour);
    }

    /**
     * Checks whether the users is allow to be served a particular file.
     * @param int $slot the number used to identify this question within this usage.
     * @param question_display_options $options the options that control display of the question.
     * @param string $component the name of the component we are serving files for.
     * @param string $filearea the name of the file area.
     * @param array $args the remaining bits of the file path.
     * @param bool $forcedownload whether the user must be forced to download the file.
     * @return bool true if the user can access this file.
     */
    public function check_file_access($slot, $options, $component, $filearea,
            $args, $forcedownload) {
        return $this->get_question_attempt($slot)->check_file_access(
                $options, $component, $filearea, $args, $forcedownload);
    }

    /**
     * Replace a particular question_attempt with a different one.
     *
     * For internal use only. Used when reloading the state of a question from the
     * database.
     *
     * @param array $records Raw records loaded from the database.
     * @param int $questionattemptid The id of the question_attempt to extract.
     * @return question_attempt The newly constructed question_attempt_step.
     */
    public function replace_loaded_question_attempt_info($slot, $qa) {
        $this->check_slot($slot);
        $this->questionattempts[$slot] = $qa;
    }

    /**
     * You should probably not use this method in code outside the question engine.
     * The main reason for exposing it was for the benefit of unit tests.
     * @param int $slot the number used to identify this question within this usage.
     * @return string return the prefix that is pre-pended to field names in the HTML
     * that is output.
     */
    public function get_field_prefix($slot) {
        return $this->get_question_attempt($slot)->get_field_prefix();
    }

    /**
     * Get the number of variants available for the question in this slot.
     * @param int $slot the number used to identify this question within this usage.
     * @return int the number of variants available.
     */
    public function get_num_variants($slot) {
        return $this->get_question_attempt($slot)->get_question()->get_num_variants();
    }

    /**
     * Get the variant of the question being used in a given slot.
     * @param int $slot the number used to identify this question within this usage.
     * @return int the variant of this question that is being used.
     */
    public function get_variant($slot) {
        return $this->get_question_attempt($slot)->get_variant();
    }

    /**
     * Start the attempt at a question that has been added to this usage.
     * @param int $slot the number used to identify this question within this usage.
     * @param int $variant which variant of the question to use. Must be between
     *      1 and ->get_num_variants($slot) inclusive. If not give, a variant is
     *      chosen at random.
     */
    public function start_question($slot, $variant = null) {
        if (is_null($variant)) {
            $variant = rand(1, $this->get_num_variants($slot));
        }

        $qa = $this->get_question_attempt($slot);
        $qa->start($this->preferredbehaviour, $variant);
        $this->observer->notify_attempt_modified($qa);
    }

    /**
     * Start the attempt at all questions that has been added to this usage.
     * @param question_variant_selection_strategy how to pick which variant of each question to use.
     * @param int $timestamp optional, the timstamp to record for this action. Defaults to now.
     * @param int $userid optional, the user to attribute this action to. Defaults to the current user.
     */
    public function start_all_questions(question_variant_selection_strategy $variantstrategy = null,
            $timestamp = null, $userid = null) {
        if (is_null($variantstrategy)) {
            $variantstrategy = new question_variant_random_strategy();
        }

        foreach ($this->questionattempts as $qa) {
            $qa->start($this->preferredbehaviour, $qa->select_variant($variantstrategy));
            $this->observer->notify_attempt_modified($qa);
        }
    }

    /**
     * Start the attempt at a question, starting from the point where the previous
     * question_attempt $oldqa had reached. This is used by the quiz 'Each attempt
     * builds on last' mode.
     * @param int $slot the number used to identify this question within this usage.
     * @param question_attempt $oldqa a previous attempt at this quetsion that
     *      defines the starting point.
     */
    public function start_question_based_on($slot, question_attempt $oldqa) {
        $qa = $this->get_question_attempt($slot);
        $qa->start_based_on($oldqa);
        $this->observer->notify_attempt_modified($qa);
    }

    /**
     * Process all the question actions in the current request.
     *
     * If there is a parameter slots included in the post data, then only
     * those question numbers will be processed, otherwise all questions in this
     * useage will be.
     *
     * This function also does {@link update_question_flags()}.
     *
     * @param int $timestamp optional, use this timestamp as 'now'.
     * @param array $postdata optional, only intended for testing. Use this data
     * instead of the data from $_POST.
     */
    public function process_all_actions($timestamp = null, $postdata = null) {
        foreach ($this->get_slots_in_request($postdata) as $slot) {
            if (!$this->validate_sequence_number($slot, $postdata)) {
                continue;
            }
            $submitteddata = $this->extract_responses($slot, $postdata);
            $this->process_action($slot, $submitteddata, $timestamp);
        }
        $this->update_question_flags($postdata);
    }

    /**
     * Process all the question autosave data in the current request.
     *
     * If there is a parameter slots included in the post data, then only
     * those question numbers will be processed, otherwise all questions in this
     * useage will be.
     *
     * This function also does {@link update_question_flags()}.
     *
     * @param int $timestamp optional, use this timestamp as 'now'.
     * @param array $postdata optional, only intended for testing. Use this data
     * instead of the data from $_POST.
     */
    public function process_all_autosaves($timestamp = null, $postdata = null) {
        foreach ($this->get_slots_in_request($postdata) as $slot) {
            if (!$this->is_autosave_required($slot, $postdata)) {
                continue;
            }
            $submitteddata = $this->extract_responses($slot, $postdata);
            $this->process_autosave($slot, $submitteddata, $timestamp);
        }
        $this->update_question_flags($postdata);
    }

    /**
     * Get the list of slot numbers that should be processed as part of processing
     * the current request.
     * @param array $postdata optional, only intended for testing. Use this data
     * instead of the data from $_POST.
     * @return array of slot numbers.
     */
    protected function get_slots_in_request($postdata = null) {
        // Note: we must not use "question_attempt::get_submitted_var()" because there is no attempt instance!!!
        if (is_null($postdata)) {
            $slots = optional_param('slots', null, PARAM_SEQUENCE);
        } else if (array_key_exists('slots', $postdata)) {
            $slots = clean_param($postdata['slots'], PARAM_SEQUENCE);
        } else {
            $slots = null;
        }
        if (is_null($slots)) {
            $slots = $this->get_slots();
        } else if (!$slots) {
            $slots = array();
        } else {
            $slots = explode(',', $slots);
        }
        return $slots;
    }

    /**
     * Get the submitted data from the current request that belongs to this
     * particular question.
     *
     * @param int $slot the number used to identify this question within this usage.
     * @param $postdata optional, only intended for testing. Use this data
     * instead of the data from $_POST.
     * @return array submitted data specific to this question.
     */
    public function extract_responses($slot, $postdata = null) {
        return $this->get_question_attempt($slot)->get_submitted_data($postdata);
    }

    /**
     * Process a specific action on a specific question.
     * @param int $slot the number used to identify this question within this usage.
     * @param $submitteddata the submitted data that constitutes the action.
     */
    public function process_action($slot, $submitteddata, $timestamp = null) {
        $qa = $this->get_question_attempt($slot);
        $qa->process_action($submitteddata, $timestamp);
        $this->observer->notify_attempt_modified($qa);
    }

    /**
     * Process an autosave action on a specific question.
     * @param int $slot the number used to identify this question within this usage.
     * @param $submitteddata the submitted data that constitutes the action.
     */
    public function process_autosave($slot, $submitteddata, $timestamp = null) {
        $qa = $this->get_question_attempt($slot);
        if ($qa->process_autosave($submitteddata, $timestamp)) {
            $this->observer->notify_attempt_modified($qa);
        }
    }

    /**
     * Check that the sequence number, that detects weird things like the student
     * clicking back, is OK. If the sequence check variable is not present, returns
     * false. If the check variable is present and correct, returns true. If the
     * variable is present and wrong, throws an exception.
     * @param int $slot the number used to identify this question within this usage.
     * @param array $submitteddata the submitted data that constitutes the action.
     * @return bool true if the check variable is present and correct. False if it
     * is missing. (Throws an exception if the check fails.)
     */
    public function validate_sequence_number($slot, $postdata = null) {
        $qa = $this->get_question_attempt($slot);
        $sequencecheck = $qa->get_submitted_var(
                $qa->get_control_field_name('sequencecheck'), PARAM_INT, $postdata);
        if (is_null($sequencecheck)) {
            return false;
        } else if ($sequencecheck != $qa->get_sequence_check_count()) {
            throw new question_out_of_sequence_exception($this->id, $slot, $postdata);
        } else {
            return true;
        }
    }

    /**
     * Check, based on the sequence number, whether this auto-save is still required.
     * @param int $slot the number used to identify this question within this usage.
     * @param array $submitteddata the submitted data that constitutes the action.
     * @return bool true if the check variable is present and correct, otherwise false.
     */
    public function is_autosave_required($slot, $postdata = null) {
        $qa = $this->get_question_attempt($slot);
        $sequencecheck = $qa->get_submitted_var(
                $qa->get_control_field_name('sequencecheck'), PARAM_INT, $postdata);
        if (is_null($sequencecheck)) {
            return false;
        } else if ($sequencecheck != $qa->get_sequence_check_count()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Update the flagged state for all question_attempts in this usage, if their
     * flagged state was changed in the request.
     *
     * @param $postdata optional, only intended for testing. Use this data
     * instead of the data from $_POST.
     */
    public function update_question_flags($postdata = null) {
        foreach ($this->questionattempts as $qa) {
            $flagged = $qa->get_submitted_var(
                    $qa->get_flag_field_name(), PARAM_BOOL, $postdata);
            if (!is_null($flagged) && $flagged != $qa->is_flagged()) {
                $qa->set_flagged($flagged);
            }
        }
    }

    /**
     * Get the correct response to a particular question. Passing the results of
     * this method to {@link process_action()} will probably result in full marks.
     * If it is not possible to compute a correct response, this method should return null.
     * @param int $slot the number used to identify this question within this usage.
     * @return array that constitutes a correct response to this question.
     */
    public function get_correct_response($slot) {
        return $this->get_question_attempt($slot)->get_correct_response();
    }

    /**
     * Finish the active phase of an attempt at a question.
     *
     * This is an external act of finishing the attempt. Think, for example, of
     * the 'Submit all and finish' button in the quiz. Some behaviours,
     * (for example, immediatefeedback) give a way of finishing the active phase
     * of a question attempt as part of a {@link process_action()} call.
     *
     * After the active phase is over, the only changes possible are things like
     * manual grading, or changing the flag state.
     *
     * @param int $slot the number used to identify this question within this usage.
     */
    public function finish_question($slot, $timestamp = null) {
        $qa = $this->get_question_attempt($slot);
        $qa->finish($timestamp);
        $this->observer->notify_attempt_modified($qa);
    }

    /**
     * Finish the active phase of an attempt at a question. See {@link finish_question()}
     * for a fuller description of what 'finish' means.
     */
    public function finish_all_questions($timestamp = null) {
        foreach ($this->questionattempts as $qa) {
            $qa->finish($timestamp);
            $this->observer->notify_attempt_modified($qa);
        }
    }

    /**
     * Perform a manual grading action on a question attempt.
     * @param int $slot the number used to identify this question within this usage.
     * @param string $comment the comment being added to the question attempt.
     * @param number $mark the mark that is being assigned. Can be null to just
     * add a comment.
     * @param int $commentformat one of the FORMAT_... constants. The format of $comment.
     */
    public function manual_grade($slot, $comment, $mark, $commentformat = null) {
        $qa = $this->get_question_attempt($slot);
        $qa->manual_grade($comment, $mark, $commentformat);
        $this->observer->notify_attempt_modified($qa);
    }

    /**
     * Regrade a question in this usage. This replays the sequence of submitted
     * actions to recompute the outcomes.
     * @param int $slot the number used to identify this question within this usage.
     * @param bool $finished whether the question attempt should be forced to be finished
     *      after the regrade, or whether it may still be in progress (default false).
     * @param number $newmaxmark (optional) if given, will change the max mark while regrading.
     */
    public function regrade_question($slot, $finished = false, $newmaxmark = null) {
        $oldqa = $this->get_question_attempt($slot);
        if (is_null($newmaxmark)) {
            $newmaxmark = $oldqa->get_max_mark();
        }

        $newqa = new question_attempt($oldqa->get_question(), $oldqa->get_usage_id(),
                $this->observer, $newmaxmark);
        $newqa->set_database_id($oldqa->get_database_id());
        $newqa->set_slot($oldqa->get_slot());
        $newqa->regrade($oldqa, $finished);

        $this->questionattempts[$slot] = $newqa;
        $this->observer->notify_attempt_modified($newqa);
    }

    /**
     * Regrade all the questions in this usage (without changing their max mark).
     * @param bool $finished whether each question should be forced to be finished
     *      after the regrade, or whether it may still be in progress (default false).
     */
    public function regrade_all_questions($finished = false) {
        foreach ($this->questionattempts as $slot => $notused) {
            $this->regrade_question($slot, $finished);
        }
    }

    /**
     * Create a question_usage_by_activity from records loaded from the database.
     *
     * For internal use only.
     *
     * @param Iterator $records Raw records loaded from the database.
     * @param int $questionattemptid The id of the question_attempt to extract.
     * @return question_usage_by_activity The newly constructed usage.
     */
    public static function load_from_records($records, $qubaid) {
        $record = $records->current();
        while ($record->qubaid != $qubaid) {
            $records->next();
            if (!$records->valid()) {
                throw new coding_exception("Question usage $qubaid not found in the database.");
            }
            $record = $records->current();
        }

        $quba = new question_usage_by_activity($record->component,
            context::instance_by_id($record->contextid, IGNORE_MISSING));
        $quba->set_id_from_database($record->qubaid);
        $quba->set_preferred_behaviour($record->preferredbehaviour);

        $quba->observer = new question_engine_unit_of_work($quba);

        // If slot is null then the current pointer in $records will not be
        // advanced in the while loop below, and we get stuck in an infinite loop,
        // since this method is supposed to always consume at least one record.
        // Therefore, in this case, advance the record here.
        if (is_null($record->slot)) {
            $records->next();
        }

        while ($record && $record->qubaid == $qubaid && !is_null($record->slot)) {
            $quba->questionattempts[$record->slot] =
                    question_attempt::load_from_records($records,
                    $record->questionattemptid, $quba->observer,
                    $quba->get_preferred_behaviour());
            if ($records->valid()) {
                $record = $records->current();
            } else {
                $record = false;
            }
        }

        return $quba;
    }
}


/**
 * A class abstracting access to the
 * {@link question_usage_by_activity::$questionattempts} array.
 *
 * This class snapshots the list of {@link question_attempts} to iterate over
 * when it is created. If a question is added to the usage mid-iteration, it
 * will now show up.
 *
 * To create an instance of this class, use
 * {@link question_usage_by_activity::get_attempt_iterator()}
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_attempt_iterator implements Iterator, ArrayAccess {
    /** @var question_usage_by_activity that we are iterating over. */
    protected $quba;
    /** @var array of question numbers. */
    protected $slots;

    /**
     * To create an instance of this class, use
     * {@link question_usage_by_activity::get_attempt_iterator()}.
     * @param $quba the usage to iterate over.
     */
    public function __construct(question_usage_by_activity $quba) {
        $this->quba = $quba;
        $this->slots = $quba->get_slots();
        $this->rewind();
    }

    /** @return question_attempt_step */
    public function current() {
        return $this->offsetGet(current($this->slots));
    }
    /** @return int */
    public function key() {
        return current($this->slots);
    }
    public function next() {
        next($this->slots);
    }
    public function rewind() {
        reset($this->slots);
    }
    /** @return bool */
    public function valid() {
        return current($this->slots) !== false;
    }

    /** @return bool */
    public function offsetExists($slot) {
        return in_array($slot, $this->slots);
    }
    /** @return question_attempt_step */
    public function offsetGet($slot) {
        return $this->quba->get_question_attempt($slot);
    }
    public function offsetSet($slot, $value) {
        throw new coding_exception('You are only allowed read-only access to ' .
                'question_attempt::states through a question_attempt_step_iterator. Cannot set.');
    }
    public function offsetUnset($slot) {
        throw new coding_exception('You are only allowed read-only access to ' .
                'question_attempt::states through a question_attempt_step_iterator. Cannot unset.');
    }
}


/**
 * Interface for things that want to be notified of signficant changes to a
 * {@link question_usage_by_activity}.
 *
 * A question behaviour controls the flow of actions a student can
 * take as they work through a question, and later, as a teacher manually grades it.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface question_usage_observer {
    /** Called when a field of the question_usage_by_activity is changed. */
    public function notify_modified();

    /**
     * Called when the fields of a question attempt in this usage are modified.
     * @param question_attempt $qa the newly added question attempt.
     */
    public function notify_attempt_modified(question_attempt $qa);

    /**
     * Called when a new question attempt is added to this usage.
     * @param question_attempt $qa the newly added question attempt.
     */
    public function notify_attempt_added(question_attempt $qa);

    /**
     * Called when a new step is added to a question attempt in this usage.
     * @param question_attempt_step $step the new step.
     * @param question_attempt $qa the usage it is being added to.
     * @param int $seq the sequence number of the new step.
     */
    public function notify_step_added(question_attempt_step $step, question_attempt $qa, $seq);

    /**
     * Called when a new step is updated in a question attempt in this usage.
     * @param question_attempt_step $step the step that was updated.
     * @param question_attempt $qa the usage it is being added to.
     * @param int $seq the sequence number of the new step.
     */
    public function notify_step_modified(question_attempt_step $step, question_attempt $qa, $seq);

    /**
     * Called when a new step is updated in a question attempt in this usage.
     * @param question_attempt_step $step the step to delete.
     * @param question_attempt $qa the usage it is being added to.
     */
    public function notify_step_deleted(question_attempt_step $step, question_attempt $qa);

}


/**
 * Null implmentation of the {@link question_usage_watcher} interface.
 * Does nothing.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_usage_null_observer implements question_usage_observer {
    public function notify_modified() {
    }
    public function notify_attempt_modified(question_attempt $qa) {
    }
    public function notify_attempt_added(question_attempt $qa) {
    }
    public function notify_step_added(question_attempt_step $step, question_attempt $qa, $seq) {
    }
    public function notify_step_modified(question_attempt_step $step, question_attempt $qa, $seq) {
    }
    public function notify_step_deleted(question_attempt_step $step, question_attempt $qa) {
    }
}
