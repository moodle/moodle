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
 * OU multiple response question definition class.
 *
 * @package qtype_oumultiresponse
 * @copyright 2010 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once($CFG->dirroot . '/question/type/multichoice/question.php');


/**
 * Represents an OU multiple response question.
 *
 * @copyright 2010 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_oumultiresponse_question extends qtype_multichoice_multi_question
        implements question_automatically_gradable_with_countback {

    public function make_behaviour(question_attempt $qa, $preferredbehaviour) {
        if ($preferredbehaviour == 'interactive') {
            return question_engine::make_behaviour('interactivecountback', $qa, $preferredbehaviour);
        }
        return question_engine::make_archetypal_behaviour($preferredbehaviour, $qa);
    }

    public function classify_response(array $response) {
        $choices = parent::classify_response($response);
        $numright = $this->get_num_correct_choices();
        foreach ($choices as $choice) {
            $choice->fraction /= $numright;
        }
        return $choices;
    }

    public function grade_response(array $response) {
        list($numright, $total) = $this->get_num_parts_right($response);
        $numwrong = $this->get_num_selected_choices($response) - $numright;
        $numcorrect = $this->get_num_correct_choices();

        $fraction = max(min($numright, $numcorrect - $numwrong), 0) / $numcorrect;

        $state = question_state::graded_state_for_fraction($fraction);
        if ($state == question_state::$gradedwrong && $numright > 0) {
            $state = question_state::$gradedpartial;
        }

        return array($fraction, $state);
    }

    protected function disable_hint_settings_when_too_many_selected(question_hint_with_parts $hint) {
        parent::disable_hint_settings_when_too_many_selected($hint);
        $hint->showchoicefeedback = false;
    }

    public function compute_final_grade($responses, $totaltries) {
        $responsehistories = array();
        foreach ($this->order as $key => $ansid) {
            $fieldname = $this->field($key);
            $responsehistories[$ansid] = '';
            foreach ($responses as $response) {
                if (!array_key_exists($fieldname, $response) || !$response[$fieldname]) {
                    $responsehistories[$ansid] .= '0';
                } else {
                    $responsehistories[$ansid] .= '1';
                }
            }
        }

        return self::grade_computation($responsehistories, $this->answers,
                $this->penalty, $totaltries);
    }

    /**
     * Implement the scoring rules.
     *
     * @param array $responsehistory an array $answerid -> string of 1s and 0s. The 1s and 0s are
     * the history of which tries this answer was selected on, so 011 means not selected on the
     * first try, then selected on the second and third tries. All the strings must be the same length.
     * @param array $answers $question->options->answers, that is an array $answerid => $answer,
     * where $answer->fraction is 0 or 1. The key fields are
     * @return float the score.
     */
    public static function grade_computation($responsehistory, $answers, $penalty, $questionnumtries) {
        // First we reverse the strings to get the most recent responses to the start, then
        // distinguish right and wrong by replacing 1 with 2 for right answers.
        $workspace = array();
        $numright = 0;
        foreach ($responsehistory as $id => $string) {
            $workspace[$id] = strrev($string);
            if (!question_state::graded_state_for_fraction($answers[$id]->fraction)->is_incorrect()) {
                $workspace[$id] = str_replace('1', '2', $workspace[$id]);
                $numright++;
            }
        }

        // Now we sort which should put answers more likely to help the candidate near the bottom of
        // workspace.
        sort($workspace);

        // Now, for each try we check to see if too many options were selected. If so, we
        // unselect correct answers in that, starting from the top of workspace - the ones that are
        // likely to turn out least favourable in the end.
        $actualnumtries = strlen(reset($workspace));
        for ($try = 0; $try < $actualnumtries; $try++) {
            $numselected = 0;
            foreach ($workspace as $string) {
                if (substr($string, $try, 1) != '0') {
                    $numselected++;
                }
            }
            if ($numselected > $numright) {
                $numtoclear = $numselected - $numright;
                $newworkspace = array();
                foreach ($workspace as $string) {
                    if (substr($string, $try, 1) == '2' && $numtoclear > 0) {
                        $string = self::replace_char_at($string, $try, '0');
                        $numtoclear--;
                    }
                    $newworkspace[] = $string;
                }
                $workspace = $newworkspace;
            }
        }

        // Now convert each string into a score. The score depends on the number of 2s at the start
        // of the string. Add extra 2s if the student got it right in fewer than the maximum
        // permitted number of tries.
        $triesnotused = $questionnumtries - $actualnumtries;
        foreach ($workspace as $string) {
            $string = str_replace('1', '0', $string); // Turn any remaining 1s to 0s for convinience.
            $num2s = strpos($string . '0', '0');
            if ($num2s > 0) {
                $num2s += $triesnotused;
                $scores[] = max(0, 1 / $numright * (1 - $penalty * ($questionnumtries - $num2s)));
            } else {
                $scores[] = 0;
            }
        }

        // Finally, sum the scores
        return array_sum($scores);
    }

    protected static function replace_char_at($string, $pos, $newchar) {
        return substr($string, 0, $pos) . $newchar . substr($string, $pos + 1);
    }
}
