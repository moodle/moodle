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
 * Upgrade library code for the multianswer question type.
 *
 * @package    qtype
 * @subpackage multianswer
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Class for converting attempt data for multianswer questions when upgrading
 * attempts to the new question engine.
 *
 * This class is used by the code in question/engine/upgrade/upgradelib.php.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multianswer_qe2_attempt_updater extends question_qtype_attempt_updater {

    public function question_summary() {
        $summary = $this->to_text($this->question->questiontext);
        foreach ($this->question->options->questions as $i => $subq) {
            switch ($subq->qtype) {
                case 'multichoice':
                    $choices = array();
                    foreach ($subq->options->answers as $ans) {
                        $choices[] = $this->to_text($ans->answer);
                    }
                    $answerbit = '{' . implode('; ', $choices) . '}';
                    break;
                case 'numerical':
                case 'shortanswer':
                    $answerbit = '_____';
                    break;
                default:
                    $answerbit = '{ERR unknown sub-question type}';
            }
            $summary = str_replace('{#' . $i . '}', $answerbit, $summary);
        }
        return $summary;
    }

    public function right_answer() {
        $right = array();

        foreach ($this->question->options->questions as $i => $subq) {
            foreach ($subq->options->answers as $ans) {
                if ($ans->fraction > 0.999) {
                    $right[$i] = $ans->answer;
                    break;
                }
            }
        }

        return $this->display_response($right);
    }

    public function explode_answer($answer) {
        $response = array();

        foreach (explode(',', $answer) as $part) {
            list($index, $partanswer) = explode('-', $part, 2);
            $response[$index] = str_replace(
                    array('&#0044;', '&#0045;'), array(",", "-"), $partanswer);
        }

        return $response;
    }

    public function display_response($response) {
        $summary = array();
        foreach ($this->question->options->questions as $i => $subq) {
            $a = new stdClass();
            $a->i = $i;
            $a->response = $this->to_text($response[$i]);
            $summary[] = get_string('subqresponse', 'qtype_multianswer', $a);
        }

        return implode('; ', $summary);
    }

    public function response_summary($state) {
        $response = $this->explode_answer($state->answer);
        foreach ($this->question->options->questions as $i => $subq) {
            if ($response[$i] && $subq->qtype == 'multichoice') {
                $response[$i] = $subq->options->answers[$response[$i]]->answer;
            }
        }
        return $this->display_response($response);
    }

    public function was_answered($state) {
        return !empty($state->answer);
    }

    public function set_first_step_data_elements($state, &$data) {
        foreach ($this->question->options->questions as $i => $subq) {
            switch ($subq->qtype) {
                case 'multichoice':
                    $data[$this->add_prefix('_order', $i)] =
                            implode(',', array_keys($subq->options->answers));
                    break;
                case 'numerical':
                    $data[$this->add_prefix('_separators', $i)] = '.$,';
                    break;
            }
        }
    }

    public function supply_missing_first_step_data(&$data) {
    }

    public function set_data_elements_for_step($state, &$data) {
        $response = $this->explode_answer($state->answer);
        foreach ($this->question->options->questions as $i => $subq) {
            if (empty($response[$i])) {
                continue;
            }

            switch ($subq->qtype) {
                case 'multichoice':
                    $choices = array();
                    $order = 0;
                    foreach ($subq->options->answers as $ans) {
                        if ($ans->id == $response[$i]) {
                            $data[$this->add_prefix('answer', $i)] = $order;
                        }
                        $order++;
                    }
                    $answerbit = '{' . implode('; ', $choices) . '}';
                    break;
                case 'numerical':
                case 'shortanswer':
                    $data[$this->add_prefix('answer', $i)] = $response[$i];
                    break;
            }
        }
    }

    public function add_prefix($field, $i) {
        $prefix = 'sub' . $i . '_';
        if (substr($field, 0, 2) === '!_') {
            return '-_' . $prefix . substr($field, 2);
        } else if (substr($field, 0, 1) === '-') {
            return '-' . $prefix . substr($field, 1);
        } else if (substr($field, 0, 1) === '_') {
            return '_' . $prefix . substr($field, 1);
        } else {
            return $prefix . $field;
        }
    }
}
