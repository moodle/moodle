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
 * Question behaviour for regexp question type (with help).
 *
 * @package    qbehaviour_regexpadaptivewithhelp
 * @copyright  2011 Tim Hunt & Joseph Rézeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once(dirname(__FILE__) . '/../adaptive/behaviour.php');

/**
 * Question behaviour for regexp question type (with help).
 * @copyright  2011 Tim Hunt & Joseph Rézeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_regexpadaptivewithhelp extends qbehaviour_adaptive {
    /**
     * Question behaviour for regexp question type (with help).
     */
    const IS_ARCHETYPAL = false;

    /**
     * Question behaviour for regexp question type (with help).
     */
    public function required_question_definition_type() {
        return 'question_automatically_gradable';
    }

    /**
     * Get the most recently submitted step.
     * @return question_attempt_step
     */
    public function get_graded_step() {
        foreach ($this->qa->get_reverse_step_iterator() as $step) {
            if ($step->has_behaviour_var('_try')) {
                return $step;
            }
        }
    }

    /**
     * Question behaviour for regexp question type (with help).
     */
    public function get_expected_data() {
        $expected = parent::get_expected_data();
        if ($this->qa->get_state()->is_active()) {
            $expected['helpme'] = PARAM_BOOL;
        }
        return $expected;
    }

    /**
     * Question behaviour for regexp question type (with help)
     *
     * @param question_attempt_pending_step $pendingstep
     * @return array
     */
    public function process_action(question_attempt_pending_step $pendingstep) {
        if ($pendingstep->has_behaviour_var('helpme')) {
            return $this->process_helpme($pendingstep);
        } else {
            return parent::process_action($pendingstep);
        }
    }

    /**
     * Question behaviour for regexp question type (with help).
     * @param question_attempt_pending_step $pendingstep
     * @return mixed
     */
    public function process_submit(question_attempt_pending_step $pendingstep) {
        $status = $this->process_save($pendingstep);

        $response = $pendingstep->get_qt_data();

        // Added 'helpme' condition so student can ask for help with an empty response.
        if (!$this->question->is_gradable_response($response) && !$pendingstep->has_behaviour_var('helpme')) {
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

        // Added 'helpme' condition so question attempt would not be DISCARDED when student asks for help.
        if ($this->question->is_same_response($response, $prevresponse) && !$pendingstep->has_behaviour_var('helpme') ) {
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

    /**
     * Question behaviour for regexp question type (with help).
     * @param question_attempt_step $step
     */
    public function summarise_action(question_attempt_step $step) {
        if ($step->has_behaviour_var('helpme')) {
            return $this->summarise_helpme($step);
        } else {
            return parent::summarise_action($step);
        }
    }

    /**
     * Question behaviour for regexp question type (with help).
     * @param question_attempt_step $step
     */
    public function summarise_helpme(question_attempt_step $step) {
        return get_string('submittedwithhelp', 'qbehaviour_regexpadaptivewithhelp',
                $this->question->summarise_response_withhelp($step->get_qt_data()));
    }

    /**
     * Question behaviour for regexp question type (with help)
     * @param int $fraction
     * @param int $prevtries
     * @param bool $helpnow
     * @return int
     */
    protected function adjusted_fraction($fraction, $prevtries, $helpnow = 0) {
        $numhelps = $this->qa->get_last_behaviour_var('_helps') + $helpnow;
        return $fraction - $this->question->penalty * ($prevtries - $numhelps) -
                $this->question->penalty * $numhelps;
    }

    /**
     * Question behaviour for regexp question type (with help).
     * @param question_attempt_pending_step $pendingstep
     * @return mixed
     */
    public function process_helpme(question_attempt_pending_step $pendingstep) {
        $keep = $this->process_submit($pendingstep);
        if ($keep == question_attempt::KEEP && $pendingstep->get_state() != question_state::$invalid) {
            $prevtries = $this->qa->get_last_behaviour_var('_try', 0);
            $prevhelps = $this->qa->get_last_behaviour_var('_help', 0);
            $prevbest = $this->qa->get_fraction();
            if (is_null($prevbest)) {
                $prevbest = 0;
            }
            $fraction = $pendingstep->get_behaviour_var('_rawfraction');

            $pendingstep->set_fraction(max($prevbest, $this->adjusted_fraction($fraction, $prevtries, 1)));
            $pendingstep->set_behaviour_var('_helps', $prevhelps + 1);
        }

        return $keep;
    }

    /**
     * Question behaviour for regexp question type (with help).
     * @param string $dp
     * @return string
     */
    public function get_extra_help_if_requested($dp) {
        // Try to find the last graded step.
        $gradedstep = $this->get_graded_step($this->qa);
        $isstateimprovable = $this->qa->get_behaviour()->is_state_improvable($this->qa->get_state());
        if (is_null($gradedstep) || !$gradedstep->has_behaviour_var('helpme')) {
            return '';
        }
        $output = '';
        $helptext = '';
        $addedletter = $this->get_added_letter($gradedstep);
        if ($addedletter) {
            $helpmode = $this->question->usehint;
            switch ($helpmode) {
                case 1 : $helptext = get_string('addedletter', 'qbehaviour_regexpadaptivewithhelp', $addedletter);
                    break;
                case 2 :
                    $wholeword = preg_match('/^\s.*$/', $addedletter);
                    if ($wholeword) {
                        $addedletter = trim($addedletter);
                        $helptext = get_string('addedword', 'qbehaviour_regexpadaptivewithhelp', $addedletter);
                        break;
                    } else {
                        $closest = rtrim($this->question->closest[0]);
                        $pattern = '/[^ ]*$/';
                        preg_match($pattern, $closest, $results);
                        $addedletter = $results[0];
                        $helptext = get_string('completedword', 'qbehaviour_regexpadaptivewithhelp', $addedletter);
                        break;
                    }
                case 3 :
                    $pattern = '/((?<!\w)[\p{P}]|[\p{P}](?!\w))/';
                    $ispunctuation = preg_match($pattern, $addedletter);
                    if ($ispunctuation) {
                        $helptext = get_string('addedpunctuation', 'qbehaviour_regexpadaptivewithhelp', $addedletter);
                        break;
                    }

                    $wholeword = preg_match('/^\s.*$/', $addedletter);
                    $isafterpunctuation = preg_match('/[\p{P}]/', $this->question->closest[1], $m, 0, -1);
                    if ($wholeword || $isafterpunctuation) {
                        $helptext = get_string('addedword', 'qbehaviour_regexpadaptivewithhelp', $addedletter);
                        break;
                    }
                    $closest = rtrim($this->question->closest[0]);

                    $pattern = '/[^ ]*$/';
                    preg_match($pattern, $closest, $results);
                    $addedletter = $results[0];
                    $helptext = get_string('completedword', 'qbehaviour_regexpadaptivewithhelp', $addedletter);
                    break;
            }
            $output .= $helptext;
        }
        $penalty = $this->question->penalty;
        if ($isstateimprovable && $penalty > 0) {
            $nbtries = $gradedstep->get_behaviour_var('_try');
            $helppenalty = '';
            $totalpenalties = '';
            $helppenalty = $this->get_help_penalty($penalty, $dp, 'helppenalty');
            $totalpenalties = $this->get_help_penalty($nbtries * $penalty, $dp, 'totalpenalties');
            $output .= $helppenalty. $totalpenalties;
        }
        return $output;
    }

    /**
     * Question behaviour for regexp question type (with help).
     * @param string $penalty
     * @param string $dp
     * @param string $penaltystring
     * @return string
     */
    public function get_help_penalty($penalty, $dp, $penaltystring) {
        $helppenalty = format_float($penalty, $dp);
        // If total of help penalties >= 1 then display total in red.
        if ($helppenalty >= 1) {
            $helppenalty = '<span class="flagged-tag">' .$helppenalty . '<span>';
        }
        $output = '';
        $output .= get_string($penaltystring, 'qbehaviour_regexpadaptivewithhelp', $helppenalty).' ';
        return $output;
    }

    /**
     * Question behaviour for regexp question type (with help).
     * @param array $gradedstep
     * @return string
     */
    public function get_added_letter($gradedstep) {
        $data = $gradedstep->get_qt_data();
        $answer = $data['answer'];
        $closest = $this->question->closest;
        $addedletter = '';
        if ($answer != $closest[0]) {
            $addedletter = $closest[4];
        }
        return $addedletter;
    }

}
