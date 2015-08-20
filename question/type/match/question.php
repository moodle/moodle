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
 * Matching question definition class.
 *
 * @package   qtype_match
 * @copyright 2009 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Represents a matching question.
 *
 * @copyright 2009 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_match_question extends question_graded_automatically_with_countback {
    /** @var boolean Whether the question stems should be shuffled. */
    public $shufflestems;

    public $correctfeedback;
    public $correctfeedbackformat;
    public $partiallycorrectfeedback;
    public $partiallycorrectfeedbackformat;
    public $incorrectfeedback;
    public $incorrectfeedbackformat;

    /** @var array of question stems. */
    public $stems;
    /** @var array of choices that can be matched to each stem. */
    public $choices;
    /** @var array index of the right choice for each stem. */
    public $right;

    /** @var array shuffled stem indexes. */
    protected $stemorder;
    /** @var array shuffled choice indexes. */
    protected $choiceorder;

    public function start_attempt(question_attempt_step $step, $variant) {
        $this->stemorder = array_keys($this->stems);
        if ($this->shufflestems) {
            shuffle($this->stemorder);
        }
        $step->set_qt_var('_stemorder', implode(',', $this->stemorder));

        $choiceorder = array_keys($this->choices);
        shuffle($choiceorder);
        $step->set_qt_var('_choiceorder', implode(',', $choiceorder));
        $this->set_choiceorder($choiceorder);
    }

    public function apply_attempt_state(question_attempt_step $step) {
        $this->stemorder = explode(',', $step->get_qt_var('_stemorder'));
        $this->set_choiceorder(explode(',', $step->get_qt_var('_choiceorder')));

        // Add any missing subquestions. Sometimes people edit questions after they
        // have been attempted which breaks things.
        foreach ($this->stemorder as $stemid) {
            if (!isset($this->stems[$stemid])) {
                $this->stems[$stemid] = html_writer::span(
                        get_string('deletedsubquestion', 'qtype_match'), 'notifyproblem');
                $this->stemformat[$stemid] = FORMAT_HTML;
                $this->right[$stemid] = 0;
            }
        }

        // Add any missing choices. Sometimes people edit questions after they
        // have been attempted which breaks things.
        foreach ($this->choiceorder as $choiceid) {
            if (!isset($this->choices[$choiceid])) {
                $this->choices[$choiceid] = get_string('deletedchoice', 'qtype_match');
            }
        }
    }

    /**
     * Helper method used by both {@link start_attempt()} and
     * {@link apply_attempt_state()}.
     * @param array $choiceorder the choices, in order.
     */
    protected function set_choiceorder($choiceorder) {
        $this->choiceorder = array();
        foreach ($choiceorder as $key => $choiceid) {
            $this->choiceorder[$key + 1] = $choiceid;
        }
    }

    public function get_question_summary() {
        $question = $this->html_to_text($this->questiontext, $this->questiontextformat);
        $stems = array();
        foreach ($this->stemorder as $stemid) {
            $stems[] = $this->html_to_text($this->stems[$stemid], $this->stemformat[$stemid]);
        }
        $choices = array();
        foreach ($this->choiceorder as $choiceid) {
            $choices[] = $this->choices[$choiceid];
        }
        return $question . ' {' . implode('; ', $stems) . '} -> {' .
                implode('; ', $choices) . '}';
    }

    public function summarise_response(array $response) {
        $matches = array();
        foreach ($this->stemorder as $key => $stemid) {
            if (array_key_exists($this->field($key), $response) && $response[$this->field($key)]) {
                $matches[] = $this->html_to_text($this->stems[$stemid],
                        $this->stemformat[$stemid]) . ' -> ' .
                        $this->choices[$this->choiceorder[$response[$this->field($key)]]];
            }
        }
        if (empty($matches)) {
            return null;
        }
        return implode('; ', $matches);
    }

    public function classify_response(array $response) {
        $selectedchoicekeys = array();
        foreach ($this->stemorder as $key => $stemid) {
            if (array_key_exists($this->field($key), $response) && $response[$this->field($key)]) {
                $selectedchoicekeys[$stemid] = $this->choiceorder[$response[$this->field($key)]];
            } else {
                $selectedchoicekeys[$stemid] = 0;
            }
        }

        $parts = array();
        foreach ($this->stems as $stemid => $stem) {
            if ($this->right[$stemid] == 0 || !isset($selectedchoicekeys[$stemid])) {
                // Choice for a deleted subquestion, ignore. (See apply_attempt_state.)
                continue;
            }
            $selectedchoicekey = $selectedchoicekeys[$stemid];
            if (empty($selectedchoicekey)) {
                $parts[$stemid] = question_classified_response::no_response();
                continue;
            }
            $choice = $this->choices[$selectedchoicekey];
            if ($choice == get_string('deletedchoice', 'qtype_match')) {
                // Deleted choice, ignore. (See apply_attempt_state.)
                continue;
            }
            $parts[$stemid] = new question_classified_response(
                    $selectedchoicekey, $choice,
                    ($selectedchoicekey == $this->right[$stemid]) / count($this->stems));
        }
        return $parts;
    }

    public function clear_wrong_from_response(array $response) {
        foreach ($this->stemorder as $key => $stemid) {
            if (!array_key_exists($this->field($key), $response) ||
                    $response[$this->field($key)] != $this->get_right_choice_for($stemid)) {
                $response[$this->field($key)] = 0;
            }
        }
        return $response;
    }

    public function get_num_parts_right(array $response) {
        $numright = 0;
        foreach ($this->stemorder as $key => $stemid) {
            $fieldname = $this->field($key);
            if (!array_key_exists($fieldname, $response)) {
                continue;
            }

            $choice = $response[$fieldname];
            if ($choice && $this->choiceorder[$choice] == $this->right[$stemid]) {
                $numright += 1;
            }
        }
        return array($numright, count($this->stemorder));
    }

    /**
     * @param int $key stem number
     * @return string the question-type variable name.
     */
    protected function field($key) {
        return 'sub' . $key;
    }

    public function get_expected_data() {
        $vars = array();
        foreach ($this->stemorder as $key => $notused) {
            $vars[$this->field($key)] = PARAM_INT;
        }
        return $vars;
    }

    public function get_correct_response() {
        $response = array();
        foreach ($this->stemorder as $key => $stemid) {
            $response[$this->field($key)] = $this->get_right_choice_for($stemid);
        }
        return $response;
    }

    public function prepare_simulated_post_data($simulatedresponse) {
        $postdata = array();
        $stemtostemids = array_flip(clean_param_array($this->stems, PARAM_NOTAGS));
        $choicetochoiceno = array_flip($this->choices);
        $choicenotochoiceselectvalue = array_flip($this->choiceorder);
        foreach ($simulatedresponse as $stem => $choice) {
            $choice = clean_param($choice, PARAM_NOTAGS);
            $stemid = $stemtostemids[$stem];
            $shuffledstemno = array_search($stemid, $this->stemorder);
            if (empty($choice)) {
                $choiceselectvalue = 0;
            } else if ($choicetochoiceno[$choice]) {
                $choiceselectvalue = $choicenotochoiceselectvalue[$choicetochoiceno[$choice]];
            } else {
                throw new coding_exception("Unknown choice {$choice} in matching question - {$this->name}.");
            }
            $postdata[$this->field($shuffledstemno)] = $choiceselectvalue;
        }
        return $postdata;
    }

    public function get_student_response_values_for_simulation($postdata) {
        $simulatedresponse = array();
        foreach ($this->stemorder as $shuffledstemno => $stemid) {
            if (!empty($postdata[$this->field($shuffledstemno)])) {
                $choiceselectvalue = $postdata[$this->field($shuffledstemno)];
                $choiceno = $this->choiceorder[$choiceselectvalue];
                $choice = clean_param($this->choices[$choiceno], PARAM_NOTAGS);
                $stem = clean_param($this->stems[$stemid], PARAM_NOTAGS);
                $simulatedresponse[$stem] = $choice;
            }
        }
        ksort($simulatedresponse);
        return $simulatedresponse;
    }

    public function get_right_choice_for($stemid) {
        foreach ($this->choiceorder as $choicekey => $choiceid) {
            if ($this->right[$stemid] == $choiceid) {
                return $choicekey;
            }
        }
    }

    public function is_complete_response(array $response) {
        $complete = true;
        foreach ($this->stemorder as $key => $stemid) {
            $complete = $complete && !empty($response[$this->field($key)]);
        }
        return $complete;
    }

    public function is_gradable_response(array $response) {
        foreach ($this->stemorder as $key => $stemid) {
            if (!empty($response[$this->field($key)])) {
                return true;
            }
        }
        return false;
    }

    public function get_validation_error(array $response) {
        if ($this->is_complete_response($response)) {
            return '';
        }
        return get_string('pleaseananswerallparts', 'qtype_match');
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        foreach ($this->stemorder as $key => $notused) {
            $fieldname = $this->field($key);
            if (!question_utils::arrays_same_at_key_integer(
                    $prevresponse, $newresponse, $fieldname)) {
                return false;
            }
        }
        return true;
    }

    public function grade_response(array $response) {
        list($right, $total) = $this->get_num_parts_right($response);
        $fraction = $right / $total;
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    public function compute_final_grade($responses, $totaltries) {
        $totalstemscore = 0;
        foreach ($this->stemorder as $key => $stemid) {
            $fieldname = $this->field($key);

            $lastwrongindex = -1;
            $finallyright = false;
            foreach ($responses as $i => $response) {
                if (!array_key_exists($fieldname, $response) || !$response[$fieldname] ||
                        $this->choiceorder[$response[$fieldname]] != $this->right[$stemid]) {
                    $lastwrongindex = $i;
                    $finallyright = false;
                } else {
                    $finallyright = true;
                }
            }

            if ($finallyright) {
                $totalstemscore += max(0, 1 - ($lastwrongindex + 1) * $this->penalty);
            }
        }

        return $totalstemscore / count($this->stemorder);
    }

    public function get_stem_order() {
        return $this->stemorder;
    }

    public function get_choice_order() {
        return $this->choiceorder;
    }

    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        if ($component == 'qtype_match' && $filearea == 'subquestion') {
            $subqid = reset($args); // Itemid is sub question id.
            return array_key_exists($subqid, $this->stems);

        } else if ($component == 'question' && in_array($filearea,
                array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback'))) {
            return $this->check_combined_feedback_file_access($qa, $options, $filearea);

        } else if ($component == 'question' && $filearea == 'hint') {
            return $this->check_hint_file_access($qa, $options, $args);

        } else {
            return parent::check_file_access($qa, $options, $component, $filearea,
                    $args, $forcedownload);
        }
    }
}
