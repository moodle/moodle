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
 * time for immediate feedback, with certainty based marking.
 *
 * @package    qbehaviour
 * @subpackage immediatecbm
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../immediatefeedback/behaviour.php');


/**
 * Question behaviour for immediate feedback with CBM.
 *
 * Each question has a submit button next to it along with some radio buttons
 * to input a certainly, that is, how sure they are that they are right.
 * The student can submit their answer at any time for immediate feedback.
 * Once the qustion is submitted, it is not possible for the student to change
 * their answer any more. The student's degree of certainly affects their score.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_immediatecbm extends qbehaviour_immediatefeedback {
    const IS_ARCHETYPAL = true;

    public static function get_required_behaviours() {
        return array('immediatefeedback', 'deferredcbm');
    }

    public function get_min_fraction() {
        return question_cbm::adjust_fraction(parent::get_min_fraction(), question_cbm::HIGH);
    }

    public function get_expected_data() {
        if ($this->qa->get_state()->is_active()) {
            return array(
                'submit' => PARAM_BOOL,
                'certainty' => PARAM_INT,
            );
        }
        return parent::get_expected_data();
    }

    public function get_right_answer_summary() {
        $summary = parent::get_right_answer_summary();
        return question_cbm::summary_with_certainty($summary, question_cbm::HIGH);
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
                $this->qa->get_last_behaviour_var('certainty') ==
                        $pendingstep->get_behaviour_var('certainty');
    }

    protected function is_complete_response($pendingstep) {
        return parent::is_complete_response($pendingstep) &&
                $pendingstep->has_behaviour_var('certainty');
    }

    public function process_submit(question_attempt_pending_step $pendingstep) {
        if ($this->qa->get_state()->is_finished()) {
            return question_attempt::DISCARD;
        }

        if (!$this->qa->get_question()->is_gradable_response($pendingstep->get_qt_data()) ||
                !$pendingstep->has_behaviour_var('certainty')) {
            $pendingstep->set_state(question_state::$invalid);
            return question_attempt::KEEP;
        }

        return $this->do_grading($pendingstep, $pendingstep);
    }

    public function process_finish(question_attempt_pending_step $pendingstep) {
        if ($this->qa->get_state()->is_finished()) {
            return question_attempt::DISCARD;
        }

        $laststep = $this->qa->get_last_step();
        return $this->do_grading($laststep, $pendingstep);
    }

    protected function do_grading(question_attempt_step $responsesstep,
            question_attempt_pending_step $pendingstep) {
        if (!$this->question->is_gradable_response($responsesstep->get_qt_data())) {
            $pendingstep->set_state(question_state::$gaveup);

        } else {
            $response = $responsesstep->get_qt_data();
            list($fraction, $state) = $this->question->grade_response($response);

            if ($responsesstep->has_behaviour_var('certainty')) {
                $certainty = $responsesstep->get_behaviour_var('certainty');
            } else {
                $certainty = question_cbm::default_certainty();
                $pendingstep->set_behaviour_var('_assumedcertainty', $certainty);
            }

            $pendingstep->set_behaviour_var('_rawfraction', $fraction);
            $pendingstep->set_fraction(question_cbm::adjust_fraction($fraction, $certainty));
            $pendingstep->set_state($state);
            $pendingstep->set_new_response_summary(question_cbm::summary_with_certainty(
                    $this->question->summarise_response($response),
                    $responsesstep->get_behaviour_var('certainty')));
        }
        return question_attempt::KEEP;
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
