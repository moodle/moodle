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
 * Multiple choice question definition classes.
 *
 * @package    qtype
 * @subpackage multichoice
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Base class for multiple choice questions. The parts that are common to
 * single select and multiple select.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_multichoice_base extends question_graded_automatically {
    const LAYOUT_DROPDOWN = 0;
    const LAYOUT_VERTICAL = 1;
    const LAYOUT_HORIZONTAL = 2;

    public $answers;

    public $shuffleanswers;
    public $answernumbering;
    public $layout = self::LAYOUT_VERTICAL;

    public $correctfeedback;
    public $correctfeedbackformat;
    public $partiallycorrectfeedback;
    public $partiallycorrectfeedbackformat;
    public $incorrectfeedback;
    public $incorrectfeedbackformat;

    protected $order = null;

    public function start_attempt(question_attempt_step $step, $variant) {
        $this->order = array_keys($this->answers);
        if ($this->shuffleanswers) {
            shuffle($this->order);
        }
        $step->set_qt_var('_order', implode(',', $this->order));
    }

    public function apply_attempt_state(question_attempt_step $step) {
        $this->order = explode(',', $step->get_qt_var('_order'));
    }

    public function get_question_summary() {
        $question = $this->html_to_text($this->questiontext, $this->questiontextformat);
        $choices = array();
        foreach ($this->order as $ansid) {
            $choices[] = $this->html_to_text($this->answers[$ansid]->answer,
                    $this->answers[$ansid]->answerformat);
        }
        return $question . ': ' . implode('; ', $choices);
    }

    public function get_order(question_attempt $qa) {
        $this->init_order($qa);
        return $this->order;
    }

    protected function init_order(question_attempt $qa) {
        if (is_null($this->order)) {
            $this->order = explode(',', $qa->get_step(0)->get_qt_var('_order'));
        }
    }

    public abstract function get_response(question_attempt $qa);

    public abstract function is_choice_selected($response, $value);

    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        if ($component == 'question' && in_array($filearea,
                array('correctfeedback', 'partiallycorrectfeedback', 'incorrectfeedback'))) {
            return $this->check_combined_feedback_file_access($qa, $options, $filearea);

        } else if ($component == 'question' && $filearea == 'answer') {
            $answerid = reset($args); // itemid is answer id.
            return  in_array($answerid, $this->order);

        } else if ($component == 'question' && $filearea == 'answerfeedback') {
            $answerid = reset($args); // itemid is answer id.
            $response = $this->get_response($qa);
            $isselected = false;
            foreach ($this->order as $value => $ansid) {
                if ($ansid == $answerid) {
                    $isselected = $this->is_choice_selected($response, $value);
                    break;
                }
            }
            // $options->suppresschoicefeedback is a hack specific to the
            // oumultiresponse question type. It would be good to refactor to
            // avoid refering to it here.
            return $options->feedback && empty($options->suppresschoicefeedback) &&
                    $isselected;

        } else if ($component == 'question' && $filearea == 'hint') {
            return $this->check_hint_file_access($qa, $options, $args);

        } else {
            return parent::check_file_access($qa, $options, $component, $filearea,
                    $args, $forcedownload);
        }
    }

    public function make_html_inline($html) {
        $html = preg_replace('~\s*<p>\s*~u', '', $html);
        $html = preg_replace('~\s*</p>\s*~u', '<br />', $html);
        $html = preg_replace('~(<br\s*/?>)+$~u', '', $html);
        return trim($html);
    }
}


/**
 * Represents a multiple choice question where only one choice should be selected.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multichoice_single_question extends qtype_multichoice_base {
    public function get_renderer(moodle_page $page) {
        return $page->get_renderer('qtype_multichoice', 'single');
    }

    public function get_min_fraction() {
        $minfraction = 0;
        foreach ($this->answers as $ans) {
            $minfraction = min($minfraction, $ans->fraction);
        }
        return $minfraction;
    }

    /**
     * Return an array of the question type variables that could be submitted
     * as part of a question of this type, with their types, so they can be
     * properly cleaned.
     * @return array variable name => PARAM_... constant.
     */
    public function get_expected_data() {
        return array('answer' => PARAM_INT);
    }

    public function summarise_response(array $response) {
        if (!array_key_exists('answer', $response) ||
                !array_key_exists($response['answer'], $this->order)) {
            return null;
        }
        $ansid = $this->order[$response['answer']];
        return $this->html_to_text($this->answers[$ansid]->answer,
                $this->answers[$ansid]->answerformat);
    }

    public function classify_response(array $response) {
        if (!array_key_exists('answer', $response) ||
                !array_key_exists($response['answer'], $this->order)) {
            return array($this->id => question_classified_response::no_response());
        }
        $choiceid = $this->order[$response['answer']];
        $ans = $this->answers[$choiceid];
        return array($this->id => new question_classified_response($choiceid,
                $this->html_to_text($ans->answer, $ans->answerformat), $ans->fraction));
    }

    public function get_correct_response() {
        foreach ($this->order as $key => $answerid) {
            if (question_state::graded_state_for_fraction(
                    $this->answers[$answerid]->fraction)->is_correct()) {
                return array('answer' => $key);
            }
        }
        return array();
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        return question_utils::arrays_same_at_key($prevresponse, $newresponse, 'answer');
    }

    public function is_complete_response(array $response) {
        return array_key_exists('answer', $response) && $response['answer'] !== '';
    }

    public function is_gradable_response(array $response) {
        return $this->is_complete_response($response);
    }

    public function grade_response(array $response) {
        if (array_key_exists('answer', $response) &&
                array_key_exists($response['answer'], $this->order)) {
            $fraction = $this->answers[$this->order[$response['answer']]]->fraction;
        } else {
            $fraction = 0;
        }
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    public function get_validation_error(array $response) {
        if ($this->is_gradable_response($response)) {
            return '';
        }
        return get_string('pleaseselectananswer', 'qtype_multichoice');
    }

    public function get_response(question_attempt $qa) {
        return $qa->get_last_qt_var('answer', -1);
    }

    public function is_choice_selected($response, $value) {
        return (string) $response === (string) $value;
    }
}


/**
 * Represents a multiple choice question where multiple choices can be selected.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multichoice_multi_question extends qtype_multichoice_base {
    public function get_renderer(moodle_page $page) {
        return $page->get_renderer('qtype_multichoice', 'multi');
    }

    public function get_min_fraction() {
        return 0;
    }

    public function clear_wrong_from_response(array $response) {
        foreach ($this->order as $key => $ans) {
            if (array_key_exists($this->field($key), $response) &&
                    question_state::graded_state_for_fraction(
                    $this->answers[$ans]->fraction)->is_incorrect()) {
                $response[$this->field($key)] = 0;
            }
        }
        return $response;
    }

    public function get_num_parts_right(array $response) {
        $numright = 0;
        foreach ($this->order as $key => $ans) {
            $fieldname = $this->field($key);
            if (!array_key_exists($fieldname, $response) || !$response[$fieldname]) {
                continue;
            }

            if (!question_state::graded_state_for_fraction(
                    $this->answers[$ans]->fraction)->is_incorrect()) {
                $numright += 1;
            }
        }
        return array($numright, count($this->order));
    }

    /**
     * @param int $key choice number
     * @return string the question-type variable name.
     */
    protected function field($key) {
        return 'choice' . $key;
    }

    public function get_expected_data() {
        $expected = array();
        foreach ($this->order as $key => $notused) {
            $expected[$this->field($key)] = PARAM_BOOL;
        }
        return $expected;
    }

    public function summarise_response(array $response) {
        $selectedchoices = array();
        foreach ($this->order as $key => $ans) {
            $fieldname = $this->field($key);
            if (array_key_exists($fieldname, $response) && $response[$fieldname]) {
                $selectedchoices[] = $this->html_to_text($this->answers[$ans]->answer,
                        $this->answers[$ans]->answerformat);
            }
        }
        if (empty($selectedchoices)) {
            return null;
        }
        return implode('; ', $selectedchoices);
    }

    public function classify_response(array $response) {
        $selectedchoices = array();
        foreach ($this->order as $key => $ansid) {
            $fieldname = $this->field($key);
            if (array_key_exists($fieldname, $response) && $response[$fieldname]) {
                $selectedchoices[$ansid] = 1;
            }
        }
        $choices = array();
        foreach ($this->answers as $ansid => $ans) {
            if (isset($selectedchoices[$ansid])) {
                $choices[$ansid] = new question_classified_response($ansid,
                        $this->html_to_text($ans->answer, $ans->answerformat), $ans->fraction);
            }
        }
        return $choices;
    }

    public function get_correct_response() {
        $response = array();
        foreach ($this->order as $key => $ans) {
            if (!question_state::graded_state_for_fraction(
                    $this->answers[$ans]->fraction)->is_incorrect()) {
                $response[$this->field($key)] = 1;
            }
        }
        return $response;
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        foreach ($this->order as $key => $notused) {
            $fieldname = $this->field($key);
            if (!question_utils::arrays_same_at_key_integer($prevresponse, $newresponse, $fieldname)) {
                return false;
            }
        }
        return true;
    }

    public function is_complete_response(array $response) {
        foreach ($this->order as $key => $notused) {
            if (!empty($response[$this->field($key)])) {
                return true;
            }
        }
        return false;
    }

    public function is_gradable_response(array $response) {
        return $this->is_complete_response($response);
    }

    /**
     * @param array $response responses, as returned by
     *      {@link question_attempt_step::get_qt_data()}.
     * @return int the number of choices that were selected. in this response.
     */
    public function get_num_selected_choices(array $response) {
        $numselected = 0;
        foreach ($response as $key => $value) {
            if (!empty($value)) {
                $numselected += 1;
            }
        }
        return $numselected;
    }

    /**
     * @return int the number of choices that are correct.
     */
    public function get_num_correct_choices() {
        $numcorrect = 0;
        foreach ($this->answers as $ans) {
            if (!question_state::graded_state_for_fraction($ans->fraction)->is_incorrect()) {
                $numcorrect += 1;
            }
        }
        return $numcorrect;
    }

    public function grade_response(array $response) {
        $fraction = 0;
        foreach ($this->order as $key => $ansid) {
            if (!empty($response[$this->field($key)])) {
                $fraction += $this->answers[$ansid]->fraction;
            }
        }
        $fraction = min(max(0, $fraction), 1.0);
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    public function get_validation_error(array $response) {
        if ($this->is_gradable_response($response)) {
            return '';
        }
        return get_string('pleaseselectatleastoneanswer', 'qtype_multichoice');
    }

    /**
     * Disable those hint settings that we don't want when the student has selected
     * more choices than the number of right choices. This avoids giving the game away.
     * @param question_hint_with_parts $hint a hint.
     */
    protected function disable_hint_settings_when_too_many_selected(
            question_hint_with_parts $hint) {
        $hint->clearwrong = false;
    }

    public function get_hint($hintnumber, question_attempt $qa) {
        $hint = parent::get_hint($hintnumber, $qa);
        if (is_null($hint)) {
            return $hint;
        }

        if ($this->get_num_selected_choices($qa->get_last_qt_data()) >
                $this->get_num_correct_choices()) {
            $hint = clone($hint);
            $this->disable_hint_settings_when_too_many_selected($hint);
        }
        return $hint;
    }

    public function get_response(question_attempt $qa) {
        return $qa->get_last_qt_data();
    }

    public function is_choice_selected($response, $value) {
        return !empty($response['choice' . $value]);
    }
}
