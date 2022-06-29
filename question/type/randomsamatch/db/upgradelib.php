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
 * Upgrade library code for the randomsamatch question type.
 *
 * @package   qtype_randomsamatch
 * @copyright 2013 Jean-Michel Vedrine
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Class for converting attempt data for randomsamatch questions when upgrading
 * attempts to the new question engine.
 *
 * This class is used by the code in question/engine/upgrade/upgradelib.php.
 *
 * @copyright 2013 Jean-Michel Vedrine
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_randomsamatch_qe2_attempt_updater extends question_qtype_attempt_updater {
    /** @var array of question stems. */
    protected $stems;
    /** @var array of question stems format. */
    protected $stemformat;
    /** @var array of choices that can be matched to each stem. */
    protected $choices;
    /** @var array index of the right choice for each stem. */
    protected $right;
    /** @var array id of the right answer for each stem (used by {@link lookup_choice}). */
    protected $rightanswerid;
    /** @var array shuffled stem indexes. */
    protected $stemorder;
    /** @var array shuffled choice indexes. */
    protected $choiceorder;
    /** @var array flipped version of the choiceorder array. */
    protected $flippedchoiceorder;

    public function question_summary() {
        return ''; // Done later, after we know which shortanswer questions are used.
    }

    public function right_answer() {
        return ''; // Done later, after we know which shortanswer questions are used.
    }

    /**
     * Explode the answer saved as a string in state
     *
     * @param string $answer comma separated list of dash separated pairs
     * @return array
     */
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

    /**
     * Find the index corresponding to a choice
     *
     * @param integer $choice
     * @return integer
     */
    protected function lookup_choice($choice) {
        if (array_key_exists($choice, $this->choices)) {
            // Easy case: choice is a key in the choices array.
            return $choice;
        } else {
            // But choice can also be the id of a shortanser correct answer
            // without been a key of the choices array, in that case we need
            // to first find the shortanswer id, then find the choices index
            // associated to it.
            $questionid = array_search($choice, $this->rightanswerid);
            if ($questionid) {
                return $this->right[$questionid];
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
                            randomsamatch question {$this->question->id}");
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
        $this->stems = array();
        $this->stemformat = array();
        $this->choices = array();
        $this->right = array();
        $this->rightanswer = array();
        $choices = $this->explode_answer($state->answer);
        $this->stemorder = array();
        foreach ($choices as $key => $notused) {
            $this->stemorder[] = $key;
        }
        $wrappedquestions = array();
        // TODO test what happen when some questions are missing.
        foreach ($this->stemorder as $questionid) {
            $wrappedquestions[] = $this->load_question($questionid);
        }
        foreach ($wrappedquestions as $wrappedquestion) {

            // We only take into account the first correct answer.
            $foundcorrect = false;
            foreach ($wrappedquestion->options->answers as $answer) {
                if ($foundcorrect || $answer->fraction != 1.0) {
                    unset($wrappedquestion->options->answers[$answer->id]);
                } else if (!$foundcorrect) {
                    $foundcorrect = true;
                    // Store right answer id, so we can use it later in lookup_choice.
                    $this->rightanswerid[$wrappedquestion->id] = $answer->id;
                    $key = array_search($answer->answer, $this->choices);
                    if ($key === false) {
                        $key = $answer->id;
                        $this->choices[$key] = $answer->answer;
                        $data['_choice_' . $key] = $answer->answer;
                    }
                    $this->stems[$wrappedquestion->id] = $wrappedquestion->questiontext;
                    $this->stemformat[$wrappedquestion->id] = $wrappedquestion->questiontextformat;
                    $this->right[$wrappedquestion->id] = $key;
                    $this->rightanswer[$wrappedquestion->id] = $answer->answer;

                    $data['_stem_' . $wrappedquestion->id] = $wrappedquestion->questiontext;
                    $data['_stemformat_' . $wrappedquestion->id] = $wrappedquestion->questiontextformat;
                    $data['_right_' . $wrappedquestion->id] = $key;

                }
            }
        }
        $this->choiceorder = array_keys($this->choices);
        // We don't shuffle the choices as that seems unnecessary for old upgraded attempts.
        $this->flippedchoiceorder = array_combine(
                array_values($this->choiceorder), array_keys($this->choiceorder));

        $data['_stemorder'] = implode(',', $this->stemorder);
        $data['_choiceorder'] = implode(',', $this->choiceorder);

        $this->updater->qa->questionsummary = $this->to_text($this->question->questiontext) . ' {' .
                implode('; ', $this->stems) . '} -> {' . implode('; ', $this->choices) . '}';

        $answer = array();
        foreach ($this->stems as $key => $stem) {
            $answer[$stem] = $this->choices[$this->right[$key]];
        }
        $this->updater->qa->rightanswer = $this->make_summary($answer);
    }

    public function supply_missing_first_step_data(&$data) {
        throw new coding_exception('qtype_randomsamatch_updater::supply_missing_first_step_data ' .
                'not tested');
        $data['_stemorder'] = array();
        $data['_choiceorder'] = array();
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

    public function load_question($questionid) {
        return $this->qeupdater->load_question($questionid);
    }
}
