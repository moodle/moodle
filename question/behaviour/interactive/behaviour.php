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
 * Question behaviour where the student can submit questions one at a
 * time for immediate feedback.
 *
 * @package    qbehaviour
 * @subpackage interactive
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Question behaviour for the interactive model.
 *
 * Each question has a submit button next to it which the student can use to
 * submit it. Once the qustion is submitted, it is not possible for the
 * student to change their answer any more, but the student gets full feedback
 * straight away.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_interactive extends question_behaviour_with_multiple_tries {
    /**
     * Special value used for {@link question_display_options::$readonly when
     * we are showing the try again button to the student during an attempt.
     * The particular number was chosen randomly. PHP will treat it the same
     * as true, but in the renderer we reconginse it display the try again
     * button enabled even though the rest of the question is disabled.
     * @var integer
     */
    const READONLY_EXCEPT_TRY_AGAIN = 23485299;

    public function is_compatible_question(question_definition $question) {
        return $question instanceof question_automatically_gradable;
    }

    public function can_finish_during_attempt() {
        return true;
    }

    public function get_right_answer_summary() {
        return $this->question->get_right_answer_summary();
    }

    /**
     * @return bool are we are currently in the try_again state.
     */
    public function is_try_again_state() {
        $laststep = $this->qa->get_last_step();
        return $this->qa->get_state()->is_active() && $laststep->has_behaviour_var('submit') &&
                $laststep->has_behaviour_var('_triesleft');
    }

    public function adjust_display_options(question_display_options $options) {
        // We only need different behaviour in try again states.
        if (!$this->is_try_again_state()) {
            parent::adjust_display_options($options);
            if ($this->qa->get_state() == question_state::$invalid &&
                    $options->marks == question_display_options::MARK_AND_MAX) {
                $options->marks = question_display_options::MAX_ONLY;
            }
            return;
        }

        // Let the hint adjust the options.
        $hint = $this->get_applicable_hint();
        if (!is_null($hint)) {
            $hint->adjust_display_options($options);
        }

        // Now call the base class method, but protect some fields from being overwritten.
        $save = clone($options);
        parent::adjust_display_options($options);
        $options->feedback = $save->feedback;
        $options->numpartscorrect = $save->numpartscorrect;

        // In a try-again state, everything except the try again button
        // Should be read-only. This is a mild hack to achieve this.
        if (!$options->readonly) {
            $options->readonly = self::READONLY_EXCEPT_TRY_AGAIN;
        }
    }

    public function get_applicable_hint() {
        if (!$this->is_try_again_state()) {
            return null;
        }
        return $this->question->get_hint(count($this->question->hints) -
                $this->qa->get_last_behaviour_var('_triesleft'), $this->qa);
    }

    public function get_expected_data() {
        if ($this->is_try_again_state()) {
            return array(
                'tryagain' => PARAM_BOOL,
            );
        } else if ($this->qa->get_state()->is_active()) {
            return array(
                'submit' => PARAM_BOOL,
            );
        }
        return parent::get_expected_data();
    }

    public function get_expected_qt_data() {
        $hint = $this->get_applicable_hint();
        if (!empty($hint->clearwrong)) {
            return $this->question->get_expected_data();
        }
        return parent::get_expected_qt_data();
    }

    public function get_state_string($showcorrectness) {
        $state = $this->qa->get_state();
        if (!$state->is_active() || $state == question_state::$invalid) {
            return parent::get_state_string($showcorrectness);
        }

        return get_string('triesremaining', 'qbehaviour_interactive',
                $this->qa->get_last_behaviour_var('_triesleft'));
    }

    public function init_first_step(question_attempt_step $step, $variant) {
        parent::init_first_step($step, $variant);
        $step->set_behaviour_var('_triesleft', count($this->question->hints) + 1);
    }

    public function process_action(question_attempt_pending_step $pendingstep) {
        if ($pendingstep->has_behaviour_var('finish')) {
            return $this->process_finish($pendingstep);
        }
        if ($this->is_try_again_state()) {
            if ($pendingstep->has_behaviour_var('tryagain')) {
                return $this->process_try_again($pendingstep);
            } else {
                return question_attempt::DISCARD;
            }
        } else {
            if ($pendingstep->has_behaviour_var('comment')) {
                return $this->process_comment($pendingstep);
            } else if ($pendingstep->has_behaviour_var('submit')) {
                return $this->process_submit($pendingstep);
            } else {
                return $this->process_save($pendingstep);
            }
        }
    }

    public function summarise_action(question_attempt_step $step) {
        if ($step->has_behaviour_var('comment')) {
            return $this->summarise_manual_comment($step);
        } else if ($step->has_behaviour_var('finish')) {
            return $this->summarise_finish($step);
        } else if ($step->has_behaviour_var('tryagain')) {
            return get_string('tryagain', 'qbehaviour_interactive');
        } else if ($step->has_behaviour_var('submit')) {
            return $this->summarise_submit($step);
        } else {
            return $this->summarise_save($step);
        }
    }

    public function process_try_again(question_attempt_pending_step $pendingstep) {
        $pendingstep->set_state(question_state::$todo);
        return question_attempt::KEEP;
    }

    public function process_submit(question_attempt_pending_step $pendingstep) {
        if ($this->qa->get_state()->is_finished()) {
            return question_attempt::DISCARD;
        }

        if (!$this->is_complete_response($pendingstep)) {
            $pendingstep->set_state(question_state::$invalid);

        } else {
            $triesleft = $this->qa->get_last_behaviour_var('_triesleft');
            $response = $pendingstep->get_qt_data();
            list($fraction, $state) = $this->question->grade_response($response);
            if ($state == question_state::$gradedright || $triesleft == 1) {
                $pendingstep->set_state($state);
                $pendingstep->set_fraction($this->adjust_fraction($fraction, $pendingstep));

            } else {
                $pendingstep->set_behaviour_var('_triesleft', $triesleft - 1);
                $pendingstep->set_state(question_state::$todo);
            }
            $pendingstep->set_new_response_summary($this->question->summarise_response($response));
        }
        return question_attempt::KEEP;
    }

    protected function adjust_fraction($fraction, question_attempt_pending_step $pendingstep) {
        $totaltries = $this->qa->get_step(0)->get_behaviour_var('_triesleft');
        $triesleft = $this->qa->get_last_behaviour_var('_triesleft');

        $fraction -= ($totaltries - $triesleft) * $this->question->penalty;
        $fraction = max($fraction, 0);
        return $fraction;
    }

    public function process_finish(question_attempt_pending_step $pendingstep) {
        if ($this->qa->get_state()->is_finished()) {
            return question_attempt::DISCARD;
        }

        $response = $this->qa->get_last_qt_data();
        if (!$this->question->is_gradable_response($response)) {
            $pendingstep->set_state(question_state::$gaveup);

        } else {
            list($fraction, $state) = $this->question->grade_response($response);
            $pendingstep->set_fraction($this->adjust_fraction($fraction, $pendingstep));
            $pendingstep->set_state($state);
        }
        $pendingstep->set_new_response_summary($this->question->summarise_response($response));
        return question_attempt::KEEP;
    }

    public function process_save(question_attempt_pending_step $pendingstep) {
        $status = parent::process_save($pendingstep);
        if ($status == question_attempt::KEEP &&
                $pendingstep->get_state() == question_state::$complete) {
            $pendingstep->set_state(question_state::$todo);
        }
        return $status;
    }
}
