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
 * Question behaviour that is like the deferred feedback model, but with
 * certainly based marking. That is, in addition to the other controls, there are
 * where the student can indicate how certain they are that their answer is right.
 *
 * @package qbehaviour_deferredcbm
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../deferredfeedback/behaviour.php');

/**
 * Question behaviour for deferred feedback with certainty based marking.
 *
 * The student enters their response during the attempt, along with a certainty,
 * that is, how sure they are that they are right, and it is saved. Later,
 * when the whole attempt is finished, their answer is graded. Their degree
 * of certainty affects their score.
 *
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_deferredcbm extends qbehaviour_deferredfeedback {
    const IS_ARCHETYPAL = true;

    public static function get_unused_display_options() {
        return array('correctness', 'marks', 'specificfeedback', 'generalfeedback',
                'rightanswer');
    }

    public function get_min_fraction() {
        return question_cbm::adjust_fraction(parent::get_min_fraction(), question_cbm::HIGH);
    }

    public function get_expected_data() {
        if ($this->qa->get_state()->is_active()) {
            return array('certainty' => PARAM_INT);
        }
        return parent::get_expected_data();
    }

    public function get_right_answer_summary() {
        $summary = parent::get_right_answer_summary();
        return $summary . ' [' . question_cbm::get_string(question_cbm::HIGH) . ']';
    }

    public function get_correct_response() {
        if ($this->qa->get_state()->is_active()) {
            return array('certainty' => question_cbm::HIGH);
        }
        return array();
    }

    protected function get_our_resume_data() {
        $lastcertainty = $this->qa->get_last_behaviour_var('certainty');
        if ($lastcertainty) {
            return array('-certainty' => $lastcertainty);
        } else {
            return array();
        }
    }

    protected function is_same_response($pendingstep) {
        return parent::is_same_response($pendingstep) &&
                $this->qa->get_last_behaviour_var('certainty') == $pendingstep->get_behaviour_var('certainty');
    }

    protected function is_complete_response($pendingstep) {
        return parent::is_complete_response($pendingstep) && $pendingstep->has_behaviour_var('certainty');
    }

    public function process_finish(question_attempt_pending_step $pendingstep) {
        $status = parent::process_finish($pendingstep);
        if ($status == question_attempt::KEEP) {
            $fraction = $pendingstep->get_fraction();
            if ($this->qa->get_last_step()->has_behaviour_var('certainty')) {
                $certainty = $this->qa->get_last_step()->get_behaviour_var('certainty');
            } else {
                $certainty = question_cbm::default_certainty();
                $pendingstep->set_behaviour_var('_assumedcertainty', $certainty);
            }
            if (!is_null($fraction)) {
                $pendingstep->set_behaviour_var('_rawfraction', $fraction);
                $pendingstep->set_fraction(question_cbm::adjust_fraction($fraction, $certainty));
            }
            $pendingstep->set_new_response_summary(
                    question_cbm::summary_with_certainty($pendingstep->get_new_response_summary(),
                    $this->qa->get_last_step()->get_behaviour_var('certainty')));
        }
        return $status;
    }

    public function summarise_action(question_attempt_step $step) {
        $summary = parent::summarise_action($step);
        if ($step->has_behaviour_var('certainty')) {
            $summary = question_cbm::summary_with_certainty($summary,
                    $step->get_behaviour_var('certainty'));
        }
        return $summary;
    }

    public static function adjust_random_guess_score($fraction) {
        return question_cbm::adjust_fraction($fraction, question_cbm::default_certainty());
    }
}
