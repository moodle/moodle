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
 * Definition class for embedded element in question text question.
 *
 * Used by gap-select, drag and drop and possibly others.
 *
 * @package    qtype_gapselect
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/questionbase.php');

/**
 * Represents embedded element in question text question.
 *
 * Parent of drag and drop and select from drop down list and others.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_gapselect_question_base extends question_graded_automatically_with_countback {
    /** @var boolean Whether the question stems should be shuffled. */
    public $shufflechoices;

    /** @var string Feedback for any correct response. */
    public $correctfeedback;
    /** @var int format of $correctfeedback. */
    public $correctfeedbackformat;
    /** @var string Feedback for any partially correct response. */
    public $partiallycorrectfeedback;
    /** @var int format of $partiallycorrectfeedback. */
    public $partiallycorrectfeedbackformat;
    /** @var string Feedback for any incorrect response. */
    public $incorrectfeedback;
    /** @var int format of $incorrectfeedback. */
    public $incorrectfeedbackformat;

    /**
     * @var array of arrays. The outer keys are the choice group numbers.
     * The inner keys for most question types number sequentialy from 1. However
     * for ddimageortext questions it is strange (and difficult to change now).
     * the first item in each group gets numbered 1, and the other items get numbered
     * $choice->no. Be careful!
     * The values are arrays of qtype_gapselect_choice objects (or a subclass).
     */
    public $choices;

    /**
     * @var array place number => group number of the places in the question
     * text where choices can be put. Places are numbered from 1.
     */
    public $places;

    /**
     * @var array of strings, one longer than $places, which is achieved by
     * indexing from 0. The bits of question text that go between the placeholders.
     */
    public $textfragments;

    /** @var array index of the right choice for each stem. */
    public $rightchoices;

    /** @var array shuffled choice indexes. */
    protected $choiceorder;

    public function start_attempt(question_attempt_step $step, $variant) {
        foreach ($this->choices as $group => $choices) {
            $choiceorder = array_keys($choices);
            if ($this->shufflechoices) {
                shuffle($choiceorder);
            }
            $step->set_qt_var('_choiceorder' . $group, implode(',', $choiceorder));
            $this->set_choiceorder($group, $choiceorder);
        }
    }

    public function apply_attempt_state(question_attempt_step $step) {
        foreach ($this->choices as $group => $choices) {
            $this->set_choiceorder($group, explode(',',
                    $step->get_qt_var('_choiceorder' . $group)));
        }
    }

    /**
     * Helper method used by both {@link start_attempt()} and
     * {@link apply_attempt_state()}.
     * @param int $group the group number.
     * @param array $choiceorder the choices, in order.
     */
    protected function set_choiceorder($group, $choiceorder) {
        foreach ($choiceorder as $key => $value) {
            $this->choiceorder[$group][$key + 1] = $value;
        }
    }

    public function get_question_summary() {
        $question = $this->html_to_text($this->questiontext, $this->questiontextformat);
        $groups = array();
        foreach ($this->choices as $group => $choices) {
            $cs = array();
            foreach ($choices as $choice) {
                $cs[] = html_to_text($choice->text, 0, false);
            }
            $groups[] = '[[' . $group . ']] -> {' . implode(' / ', $cs) . '}';
        }
        return $question . '; ' . implode('; ', $groups);
    }

    protected function get_selected_choice($group, $shuffledchoicenumber) {
        $choiceno = $this->choiceorder[$group][$shuffledchoicenumber];
        return isset($this->choices[$group][$choiceno]) ? $this->choices[$group][$choiceno] : null;
    }

    public function summarise_response(array $response) {
        $matches = array();
        $allblank = true;
        foreach ($this->places as $place => $group) {
            if (array_key_exists($this->field($place), $response) &&
                    $response[$this->field($place)]) {
                $choices[] = '{' . $this->summarise_choice(
                        $this->get_selected_choice($group, $response[$this->field($place)])) . '}';
                $allblank = false;
            } else {
                $choices[] = '{}';
            }
        }
        if ($allblank) {
            return null;
        }
        return implode(' ', $choices);
    }

    /**
     * Convert a choice to plain text.
     * @param qtype_gapselect_choice $choice one of the choices for a place.
     * @return a plain text summary of the choice.
     */
    public function summarise_choice($choice) {
        return $this->html_to_text($choice->text, FORMAT_PLAIN);
    }

    public function get_random_guess_score() {
        $accum = 0;

        foreach ($this->places as $placegroup) {
            $accum += 1 / count($this->choices[$placegroup]);
        }

        return $accum / count($this->places);
    }

    public function clear_wrong_from_response(array $response) {
        foreach ($this->places as $place => $notused) {
            if (array_key_exists($this->field($place), $response) &&
                    $response[$this->field($place)] != $this->get_right_choice_for($place)) {
                $response[$this->field($place)] = '0';
            }
        }
        return $response;
    }

    public function get_num_parts_right(array $response) {
        $numright = 0;
        foreach ($this->places as $place => $notused) {
            if (!array_key_exists($this->field($place), $response)) {
                continue;
            }
            if ($response[$this->field($place)] == $this->get_right_choice_for($place)) {
                $numright += 1;
            }
        }
        return array($numright, count($this->places));
    }

    /**
     * Get the field name corresponding to a given place.
     * @param int $place stem number
     * @return string the question-type variable name.
     */
    public function field($place) {
        return 'p' . $place;
    }

    public function get_expected_data() {
        $vars = array();
        foreach ($this->places as $place => $notused) {
            $vars[$this->field($place)] = PARAM_INTEGER;
        }
        return $vars;
    }

    public function get_correct_response() {
        $response = array();
        foreach ($this->places as $place => $notused) {
            $response[$this->field($place)] = $this->get_right_choice_for($place);
        }
        return $response;
    }

    public function get_right_choice_for($place) {
        $group = $this->places[$place];
        foreach ($this->choiceorder[$group] as $choicekey => $choiceid) {
            if ($this->rightchoices[$place] == $choiceid) {
                return $choicekey;
            }
        }
    }

    public function get_ordered_choices($group) {
        $choices = array();
        foreach ($this->choiceorder[$group] as $choicekey => $choiceid) {
            $choices[$choicekey] = $this->choices[$group][$choiceid];
        }
        return $choices;
    }

    public function is_complete_response(array $response) {
        $complete = true;
        foreach ($this->places as $place => $notused) {
            $complete = $complete && !empty($response[$this->field($place)]);
        }
        return $complete;
    }

    public function is_gradable_response(array $response) {
        foreach ($this->places as $place => $notused) {
            if (!empty($response[$this->field($place)])) {
                return true;
            }
        }
        return false;
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        foreach ($this->places as $place => $notused) {
            $fieldname = $this->field($place);
            if (!question_utils::arrays_same_at_key_integer(
                    $prevresponse, $newresponse, $fieldname)) {
                return false;
            }
        }
        return true;
    }

    public function get_validation_error(array $response) {
        if ($this->is_complete_response($response)) {
            return '';
        }
        return get_string('pleaseputananswerineachbox', 'qtype_gapselect');
    }

    public function grade_response(array $response) {
        list($right, $total) = $this->get_num_parts_right($response);
        $fraction = $right / $total;
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    public function compute_final_grade($responses, $totaltries) {
        $totalscore = 0;
        foreach ($this->places as $place => $notused) {
            $fieldname = $this->field($place);

            $lastwrongindex = -1;
            $finallyright = false;
            foreach ($responses as $i => $response) {
                if (!array_key_exists($fieldname, $response) ||
                        $response[$fieldname] != $this->get_right_choice_for($place)) {
                    $lastwrongindex = $i;
                    $finallyright = false;
                } else {
                    $finallyright = true;
                }
            }

            if ($finallyright) {
                $totalscore += max(0, 1 - ($lastwrongindex + 1) * $this->penalty);
            }
        }

        return $totalscore / count($this->places);
    }

    public function classify_response(array $response) {
        $parts = array();
        foreach ($this->places as $place => $group) {
            if (!array_key_exists($this->field($place), $response) ||
                    !$response[$this->field($place)]) {
                $parts[$place] = question_classified_response::no_response();
                continue;
            }

            $fieldname = $this->field($place);
            $choiceno = $this->choiceorder[$group][$response[$fieldname]];
            $choice = $this->choices[$group][$choiceno];
            $parts[$place] = new question_classified_response(
                    $choiceno, html_to_text($choice->text, 0, false),
                    ($this->get_right_choice_for($place) == $response[$fieldname]) / count($this->places));
        }
        return $parts;
    }

    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        if ($component == 'question' && in_array($filearea,
                array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback'))) {
            return $this->check_combined_feedback_file_access($qa, $options, $filearea, $args);

        } else if ($component == 'question' && $filearea == 'hint') {
            return $this->check_hint_file_access($qa, $options, $args);

        } else {
            return parent::check_file_access($qa, $options, $component, $filearea,
                    $args, $forcedownload);
        }
    }
}
