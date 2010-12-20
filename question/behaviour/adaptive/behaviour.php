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
 * @package qbehaviour_adaptive
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Question behaviour for adaptive mode.
 *
 * This is the old version of interactive mode.
 *
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_adaptive extends question_behaviour_with_save {
    const IS_ARCHETYPAL = true;

    public function required_question_definition_type() {
        return 'question_automatically_gradable';
    }

    public function get_expected_data() {
        if ($this->qa->get_state()->is_active()) {
            return array('submit' => PARAM_BOOL);
        }
        return parent::get_expected_data();
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

    public function get_state_string($showcorrectness) {
        $state = $this->qa->get_state();

        $laststep = $this->qa->get_last_step();
        if ($laststep->has_behaviour_var('_try')) {
            $state = question_state::graded_state_for_fraction(
                    $laststep->get_behaviour_var('_rawfraction'));
        }

        return $state->default_string($showcorrectness);
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
        if (!$this->question->is_gradable_response($response)) {
            $pendingstep->set_state(question_state::$invalid);
            if ($this->qa->get_state() != question_state::$invalid) {
                $status = question_attempt::KEEP;
            }
            return $status;
        }

        $prevtries = $this->qa->get_last_behaviour_var('_try', 0);
        $prevbest = $pendingstep->get_fraction();
        if (is_null($prevbest)) {
            $prevbest = 0;
        }

        list($fraction, $state) = $this->question->grade_response($response);

        $pendingstep->set_fraction(max($prevbest, $this->adjusted_fraction($fraction, $prevtries)));
        if ($state == question_state::$gradedright) {
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

        $laststep = $this->qa->get_last_step();
        $response = $laststep->get_qt_data();
        if (!$this->question->is_gradable_response($response)) {
            $pendingstep->set_state(question_state::$gaveup);
            return question_attempt::KEEP;
        }

        $prevtries = $this->qa->get_last_behaviour_var('_try', 0);
        $prevbest = $pendingstep->get_fraction();
        if (is_null($prevbest)) {
            $prevbest = 0;
        }

        if ($laststep->has_behaviour_var('_try')) {
            // Last answer was graded, we want to regrade it. Otherwise the answer
            // has changed, and we are grading a new try.
            $prevtries -= 1;
        }

        list($fraction, $state) = $this->question->grade_response($response);

        $pendingstep->set_fraction(max($prevbest, $this->adjusted_fraction($fraction, $prevtries)));
        $pendingstep->set_state($state);
        $pendingstep->set_behaviour_var('_try', $prevtries + 1);
        $pendingstep->set_behaviour_var('_rawfraction', $fraction);
        $pendingstep->set_new_response_summary($this->question->summarise_response($response));
        return question_attempt::KEEP;
    }
}
