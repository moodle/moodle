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
 * Question behaviour for the old adaptive mode.
 *
 * @package    qbehaviour
 * @subpackage adaptive
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Question behaviour for adaptive mode.
 *
 * This is the old version of interactive mode.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_adaptive extends question_behaviour_with_save {
    const IS_ARCHETYPAL = true;

    public function is_compatible_question(question_definition $question) {
        return $question instanceof question_automatically_gradable;
    }

    public function get_expected_data() {
        if ($this->qa->get_state()->is_active()) {
            return array('submit' => PARAM_BOOL);
        }
        return parent::get_expected_data();
    }

    public function get_state_string($showcorrectness) {
        $laststep = $this->qa->get_last_step();
        if ($laststep->has_behaviour_var('_try')) {
            $state = question_state::graded_state_for_fraction(
                    $laststep->get_behaviour_var('_rawfraction'));
            return $state->default_string(true);
        }

        $state = $this->qa->get_state();
        if ($state == question_state::$todo) {
            return get_string('notcomplete', 'qbehaviour_adaptive');
        } else {
            return parent::get_state_string($showcorrectness);
        }
    }

    public function get_right_answer_summary() {
        return $this->question->get_right_answer_summary();
    }

    public function adjust_display_options(question_display_options $options) {
        parent::adjust_display_options($options);
        if (!$this->qa->get_state()->is_finished() &&
                $this->qa->get_last_behaviour_var('_try')) {
            $options->feedback = true;
        }
    }

    public function process_action(question_attempt_pending_step $pendingstep) {
        if ($pendingstep->has_behaviour_var('comment')) {
            return $this->process_comment($pendingstep);
        } else if ($pendingstep->has_behaviour_var('finish')) {
            return $this->process_finish($pendingstep);
        } else if ($pendingstep->has_behaviour_var('submit')) {
            return $this->process_submit($pendingstep);
        } else {
            return $this->process_save($pendingstep);
        }
    }

    public function summarise_action(question_attempt_step $step) {
        if ($step->has_behaviour_var('comment')) {
            return $this->summarise_manual_comment($step);
        } else if ($step->has_behaviour_var('finish')) {
            return $this->summarise_finish($step);
        } else if ($step->has_behaviour_var('submit')) {
            return $this->summarise_submit($step);
        } else {
            return $this->summarise_save($step);
        }
    }

    public function process_save(question_attempt_pending_step $pendingstep) {
        $status = parent::process_save($pendingstep);
        $prevgrade = $this->qa->get_fraction();
        if (!is_null($prevgrade)) {
            $pendingstep->set_fraction($prevgrade);
        }
        $pendingstep->set_state(question_state::$todo);
        return $status;
    }

    protected function adjusted_fraction($fraction, $prevtries) {
        return $fraction - $this->question->penalty * $prevtries;
    }

    public function process_submit(question_attempt_pending_step $pendingstep) {
        $status = $this->process_save($pendingstep);

        $response = $pendingstep->get_qt_data();
        if (!$this->question->is_complete_response($response)) {
            $pendingstep->set_state(question_state::$invalid);
            if ($this->qa->get_state() != question_state::$invalid) {
                $status = question_attempt::KEEP;
            }
            return $status;
        }

        $prevstep = $this->qa->get_last_step_with_behaviour_var('_try');
        $prevresponse = $prevstep->get_qt_data();
        $prevtries = $this->qa->get_last_behaviour_var('_try', 0);
        $prevbest = $pendingstep->get_fraction();
        if (is_null($prevbest)) {
            $prevbest = 0;
        }

        if ($this->question->is_same_response($response, $prevresponse)) {
            return question_attempt::DISCARD;
        }

        list($fraction, $state) = $this->question->grade_response($response);

        $pendingstep->set_fraction(max($prevbest, $this->adjusted_fraction($fraction, $prevtries)));
        if ($prevstep->get_state() == question_state::$complete) {
            $pendingstep->set_state(question_state::$complete);
        } else if ($state == question_state::$gradedright) {
            $pendingstep->set_state(question_state::$complete);
        } else {
            $pendingstep->set_state(question_state::$todo);
        }
        $pendingstep->set_behaviour_var('_try', $prevtries + 1);
        $pendingstep->set_behaviour_var('_rawfraction', $fraction);
        $pendingstep->set_new_response_summary($this->question->summarise_response($response));

        return question_attempt::KEEP;
    }

    public function process_finish(question_attempt_pending_step $pendingstep) {
        if ($this->qa->get_state()->is_finished()) {
            return question_attempt::DISCARD;
        }

        $prevtries = $this->qa->get_last_behaviour_var('_try', 0);
        $prevbest = $this->qa->get_fraction();
        if (is_null($prevbest)) {
            $prevbest = 0;
        }

        $laststep = $this->qa->get_last_step();
        $response = $laststep->get_qt_data();
        if (!$this->question->is_gradable_response($response)) {
            $state = question_state::$gaveup;
            $fraction = 0;
        } else {

            if ($laststep->has_behaviour_var('_try')) {
                // Last answer was graded, we want to regrade it. Otherwise the answer
                // has changed, and we are grading a new try.
                $prevtries -= 1;
            }

            list($fraction, $state) = $this->question->grade_response($response);

            $pendingstep->set_behaviour_var('_try', $prevtries + 1);
            $pendingstep->set_behaviour_var('_rawfraction', $fraction);
            $pendingstep->set_new_response_summary($this->question->summarise_response($response));
        }

        $pendingstep->set_state($state);
        $pendingstep->set_fraction(max($prevbest, $this->adjusted_fraction($fraction, $prevtries)));
        return question_attempt::KEEP;
    }

    /**
     * Got the most recently graded step. This is mainly intended for use by the
     * renderer.
     * @return question_attempt_step the most recently graded step.
     */
    public function get_graded_step() {
        $step = $this->qa->get_last_step_with_behaviour_var('_try');
        if ($step->has_behaviour_var('_try')) {
            return $step;
        } else {
            return null;
        }
    }

    /**
     * Determine whether a question state represents an "improvable" result,
     * that is, whether the user can still improve their score.
     *
     * @param question_state $state the question state.
     * @return bool whether the state is improvable
     */
    public function is_state_improvable(question_state $state) {
        return $state == question_state::$todo;
    }

    /**
     * @return qbehaviour_adaptive_mark_details the information about the current state-of-play, scoring-wise,
     * for this adaptive attempt.
     */
    public function get_adaptive_marks() {

        // Try to find the last graded step.
        $gradedstep = $this->get_graded_step();
        if (is_null($gradedstep) || $this->qa->get_max_mark() == 0) {
            // No score yet.
            return new qbehaviour_adaptive_mark_details(question_state::$todo);
        }

        // Work out the applicable state.
        if ($this->qa->get_state()->is_commented()) {
            $state = $this->qa->get_state();
        } else {
            $state = question_state::graded_state_for_fraction(
                                $gradedstep->get_behaviour_var('_rawfraction'));
        }

        // Prepare the grading details.
        $details = $this->adaptive_mark_details_from_step($gradedstep, $state, $this->qa->get_max_mark(), $this->question->penalty);
        $details->improvable = $this->is_state_improvable($this->qa->get_state());
        return $details;
    }

    /**
     * Actually populate the qbehaviour_adaptive_mark_details object.
     * @param question_attempt_step $gradedstep the step that holds the relevant mark details.
     * @param question_state $state the state corresponding to $gradedstep.
     * @param unknown_type $maxmark the maximum mark for this question_attempt.
     * @param unknown_type $penalty the penalty for this question, as a fraction.
     */
    protected function adaptive_mark_details_from_step(question_attempt_step $gradedstep,
            question_state $state, $maxmark, $penalty) {

        $details = new qbehaviour_adaptive_mark_details($state);
        $details->maxmark    = $maxmark;
        $details->actualmark = $gradedstep->get_fraction() * $details->maxmark;
        $details->rawmark    = $gradedstep->get_behaviour_var('_rawfraction') * $details->maxmark;

        $details->currentpenalty = $penalty * $details->maxmark;
        $details->totalpenalty   = $details->currentpenalty * $this->qa->get_last_behaviour_var('_try', 0);

        $details->improvable = $this->is_state_improvable($gradedstep->get_state());

        return $details;
    }
}


/**
 * This class encapsulates all the information about the current state-of-play
 * scoring-wise. It is used to communicate between the beahviour and the renderer.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_adaptive_mark_details {
    /** @var question_state the current state of the question. */
    public $state;

    /** @var float the maximum mark for this question. */
    public $maxmark;

    /** @var float the current mark for this question. */
    public $actualmark;

    /** @var float the raw mark for this question before penalties were applied. */
    public $rawmark;

    /** @var float the the amount of additional penalty this attempt attracted. */
    public $currentpenalty;

    /** @var float the total that will apply to future attempts. */
    public $totalpenalty;

    /** @var bool whether it is possible for this mark to be improved in future. */
    public $improvable;

    /**
     * Constructor.
     * @param question_state $state
     */
    public function __construct($state, $maxmark = null, $actualmark = null, $rawmark = null,
            $currentpenalty = null, $totalpenalty = null, $improvable = null) {
        $this->state          = $state;
        $this->maxmark        = $maxmark;
        $this->actualmark     = $actualmark;
        $this->rawmark        = $rawmark;
        $this->currentpenalty = $currentpenalty;
        $this->totalpenalty   = $totalpenalty;
        $this->improvable     = $improvable;
    }

    /**
     * Get the marks, formatted to a certain number of decimal places, in the
     * form required by calls like get_string('gradingdetails', 'qbehaviour_adaptive', $a).
     * @param int $markdp the number of decimal places required.
     * @return array ready to substitute into language strings.
     */
    public function get_formatted_marks($markdp) {
        return array(
            'max'          => format_float($this->maxmark,        $markdp),
            'cur'          => format_float($this->actualmark,     $markdp),
            'raw'          => format_float($this->rawmark,        $markdp),
            'penalty'      => format_float($this->currentpenalty, $markdp),
            'totalpenalty' => format_float($this->totalpenalty,   $markdp),
        );
    }
}
