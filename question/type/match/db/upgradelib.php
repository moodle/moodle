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
 * Upgrade library code for the match question type.
 *
 * @package    qtype
 * @subpackage match
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Class for converting attempt data for match questions when upgrading
 * attempts to the new question engine.
 *
 * This class is used by the code in question/engine/upgrade/upgradelib.php.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_match_qe2_attempt_updater extends question_qtype_attempt_updater {
    protected $stems;
    protected $choices;
    protected $right;
    protected $stemorder;
    protected $choiceorder;
    protected $flippedchoiceorder;

    public function question_summary() {
        $this->stems = array();
        $this->choices = array();
        $this->right = array();

        foreach ($this->question->options->subquestions as $matchsub) {
            $ans = $matchsub->answertext;
            $key = array_search($matchsub->answertext, $this->choices);
            if ($key === false) {
                $key = $matchsub->id;
                $this->choices[$key] = $matchsub->answertext;
            }

            if ($matchsub->questiontext !== '') {
                $this->stems[$matchsub->id] = $this->to_text($matchsub->questiontext);
                $this->right[$matchsub->id] = $key;
            }
        }

        return $this->to_text($this->question->questiontext) . ' {' .
                implode('; ', $this->stems) . '} -> {' . implode('; ', $this->choices) . '}';
    }

    public function right_answer() {
        $answer = array();
        foreach ($this->stems as $key => $stem) {
            $answer[$stem] = $this->choices[$this->right[$key]];
        }
        return $this->make_summary($answer);
    }

    protected function explode_answer($answer) {
        if (!$answer) {
            return array();
        }
        $bits = explode(',', $answer);
        $selections = array();
        foreach ($bits as $bit) {
            list($stem, $choice) = explode('-', $bit);
            $selections[$stem] = $choice;
        }
        return $selections;
    }

    protected function make_summary($pairs) {
        $bits = array();
        foreach ($pairs as $stem => $answer) {
            $bits[] = $stem . ' -> ' . $answer;
        }
        return implode('; ', $bits);
    }

    protected function lookup_choice($choice) {
        foreach ($this->question->options->subquestions as $matchsub) {
            if ($matchsub->code == $choice) {
                if (array_key_exists($matchsub->id, $this->choices)) {
                    return $matchsub->id;
                } else {
                    return array_search($matchsub->answertext, $this->choices);
                }
            }
        }
        return null;
    }

    public function response_summary($state) {
        $choices = $this->explode_answer($state->answer);
        if (empty($choices)) {
            return null;
        }

        $pairs = array();
        foreach ($choices as $stemid => $choicekey) {
            if (array_key_exists($stemid, $this->stems) && $choices[$stemid]) {
                $choiceid = $this->lookup_choice($choicekey);
                if ($choiceid) {
                    $pairs[$this->stems[$stemid]] = $this->choices[$choiceid];
                } else {
                    $this->logger->log_assumption("Dealing with a place where the
                            student selected a choice that was later deleted for
                            match question {$this->question->id}");
                    $pairs[$this->stems[$stemid]] = '[CHOICE THAT WAS LATER DELETED]';
                }
            }
        }

        if ($pairs) {
            return $this->make_summary($pairs);
        } else {
            return '';
        }
    }

    public function was_answered($state) {
        $choices = $this->explode_answer($state->answer);
        foreach ($choices as $choice) {
            if ($choice) {
                return true;
            }
        }
        return false;
    }

    public function set_first_step_data_elements($state, &$data) {
        $choices = $this->explode_answer($state->answer);
        foreach ($choices as $key => $notused) {
            if (array_key_exists($key, $this->stems)) {
                $this->stemorder[] = $key;
            }
        }

        $this->choiceorder = array_keys($this->choices);
        shuffle($this->choiceorder);
        $this->flippedchoiceorder = array_combine(
                array_values($this->choiceorder), array_keys($this->choiceorder));

        $data['_stemorder'] = implode(',', $this->stemorder);
        $data['_choiceorder'] = implode(',', $this->choiceorder);
    }

    public function supply_missing_first_step_data(&$data) {
        throw new coding_exception('qtype_match_updater::supply_missing_first_step_data ' .
                'not tested');
        $data['_stemorder'] = array_keys($this->stems);
        $data['_choiceorder'] = shuffle(array_keys($this->choices));
    }

    public function set_data_elements_for_step($state, &$data) {
        $choices = $this->explode_answer($state->answer);

        foreach ($this->stemorder as $i => $key) {
            if (empty($choices[$key])) {
                $data['sub' . $i] = 0;
                continue;
            }
            $choice = $this->lookup_choice($choices[$key]);

            if (array_key_exists($choice, $this->flippedchoiceorder)) {
                $data['sub' . $i] = $this->flippedchoiceorder[$choice] + 1;
            } else {
                $data['sub' . $i] = 0;
            }
        }
    }
}
