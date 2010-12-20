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
 * This behaviour that is used when the actual qim was not
 * available.
 *
 * @package qbehaviour_opaque
 * @copyright 2010 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * This behaviour is specifically for use with the Opaque question type.
 *
 *
 * @copyright 2010 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_opaque extends question_behaviour {
    /** @var string */
    protected $preferredbehaviour;
    /** @var string */
    protected $questionsummary;

    public function __construct(question_attempt $qa, $preferredbehaviour) {
        parent::__construct($qa, $preferredbehaviour);
        $this->preferredbehaviour = $preferredbehaviour;
    }

    public function required_question_definition_type() {
        return 'qtype_opaque_question';
    }

    public function get_state_string($showcorrectness) {
        $state = $this->qa->get_state();
        $omstate = $this->qa->get_last_behaviour_var('_statestring');
        if ($state->is_finished()) {
            return $state->default_string($showcorrectness);
        } else if ($omstate) {
            return $omstate;
        } else {
            return get_string('notcomplete', 'qbehaviour_opaque');
        }
    }

    public function init_first_step(question_attempt_step $step) {
        global $USER;

        if ($step->has_behaviour_var('_randomseed')) {
            // Reinitialising, nothing to do.
            return;
        }

        // Set up the random seed to be the current time in milliseconds.
        list($micros, $sec) = explode(" ", microtime());
        $step->set_behaviour_var('_randomseed', $sec . floor($micros * 1000));
        $step->set_behaviour_var('_userid', $USER->id);
        $step->set_behaviour_var('_language', current_language());
        $step->set_behaviour_var('_preferredbehaviour', $this->preferredbehaviour);
        $opaquestate = update_opaque_state($this->qa, $step);
        $step->set_behaviour_var('_statestring', $opaquestate->progressinfo);

        // Remember the question summary.
        $this->questionsummary = html_to_text($opaquestate->xhtml, 0, false);
    }

    public function get_question_summary() {
        return $this->questionsummary;
    }

    protected function is_same_response(question_attempt_step $pendingstep) {
        $newdata = $pendingstep->get_submitted_data();

        foreach ($newdata as $key => $ignored) {
            // If an omact_ button has been clicked, never treat this as a duplicate submission.
            if (strpos($key, 'omact_') === 0) {
                return false;
            }
        }

        $olddata = $this->qa->get_last_step()->get_submitted_data();
        return question_utils::arrays_have_same_keys_and_values($newdata, $olddata);
    }

    public function summarise_action(question_attempt_step $step) {
        if ($step->has_behaviour_var('finish')) {
            return $this->summarise_finish($step);
        } else if ($step->has_behaviour_var('comment')) {
            return $this->summarise_manual_comment($step);
        } else {
            $data = $step->get_qt_data();
            $formatteddata = array();
            foreach ($data as $name => $value) {
                if (substr($name, 0, 1) == '_') {
                    continue;
                }
                $formatteddata[] = $name . ' => ' . s($value);
            }
            if ($formatteddata) {
                return get_string('submitted', 'question', implode(', ', $formatteddata));
            } else {
                return $this->summarise_start($step);
            }
        }
    }

    public function process_action(question_attempt_pending_step $pendingstep) {
        if ($pendingstep->has_behaviour_var('finish')) {
            return $this->process_finish($pendingstep);
        }
        if ($pendingstep->has_behaviour_var('comment')) {
            return $this->process_comment($pendingstep);
        } else if ($this->is_same_response($pendingstep) ||
                $this->qa->get_state()->is_finished()) {
            return question_attempt::DISCARD;
        } else {
            return $this->process_remote_action($pendingstep);
        }
    }

    public function process_finish(question_attempt_pending_step $pendingstep) {
        if ($this->qa->get_state()->is_finished()) {
            return question_attempt::DISCARD;
        }

        // They tried to finish the usage without having finished this question.
        // That is, they gave up.
        $pendingstep->set_state(question_state::$gaveup);
        return question_attempt::KEEP;
    }

    public function process_remote_action(question_attempt_pending_step $pendingstep) {
        $opaquestate = update_opaque_state($this->qa, $pendingstep);

        if (is_string($opaquestate)) {
            notify($opaquestate);
            return question_attempt::DISCARD; // TODO
        }

        if ($opaquestate->resultssequencenumber != $this->qa->get_num_steps()) {
            $pendingstep->set_state(question_state::$todo);
            $pendingstep->set_behaviour_var('_statestring', $opaquestate->progressinfo);

        } else {
            // Look for a score on the default axis.
            $pendingstep->set_fraction(0);
            foreach ($opaquestate->results->scores as $score) {
                if ($score->axis == '') {
                    $pendingstep->set_fraction($score->marks / $this->question->defaultmark);
                }
            }

            if ($opaquestate->results->attempts > 0) {
                $pendingstep->set_state(question_state::$gradedright);
            } else {
                $pendingstep->set_state(
                        question_state::graded_state_for_fraction($pendingstep->get_fraction()));
            }

            if (!empty($opaquestate->results->questionLine)) {
                $this->qa->set_question_summary(
                        $this->cleanup_results($opaquestate->results->questionLine));
            }
            if (!empty($opaquestate->results->answerLine)) {
                $pendingstep->set_new_response_summary(
                        $this->cleanup_results($opaquestate->results->answerLine));
            }
            if (!empty($opaquestate->results->actionSummary)) {
                $pendingstep->set_behaviour_var('_actionsummary',
                        $this->cleanup_results($opaquestate->results->actionSummary));
            }
        }

        return question_attempt::KEEP;
    }

    protected function cleanup_results($line) {
        return preg_replace('/\\s+/', ' ', $line);
    }
}
