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

require_once($CFG->dirroot . '/question/type/questionbase.php');

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
    /**
     * @var int standard instruction to be displayed if enabled.
     */
    public $showstandardinstruction = 0;
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

        // Add any missing answers. Sometimes people edit questions after they
        // have been attempted which breaks things.
        foreach ($this->order as $ansid) {
            if (isset($this->answers[$ansid])) {
                continue;
            }
            $a = new stdClass();
            $a->id = 0;
            $a->answer = html_writer::span(get_string('deletedchoice', 'qtype_multichoice'),
                    'notifyproblem');
            $a->answerformat = FORMAT_HTML;
            $a->fraction = 0;
            $a->feedback = '';
            $a->feedbackformat = FORMAT_HTML;
            $this->answers[$ansid] = $this->qtype->make_answer($a);
            $this->answers[$ansid]->answerformat = FORMAT_HTML;
        }
    }

    public function validate_can_regrade_with_other_version(question_definition $otherversion): ?string {
        $basemessage = parent::validate_can_regrade_with_other_version($otherversion);
        if ($basemessage) {
            return $basemessage;
        }

        if (count($this->answers) != count($otherversion->answers)) {
            return get_string('regradeissuenumchoiceschanged', 'qtype_multichoice');
        }

        return null;
    }

    public function update_attempt_state_data_for_new_version(
            question_attempt_step $oldstep, question_definition $otherversion) {
        $startdata = parent::update_attempt_state_data_for_new_version($oldstep, $otherversion);

        $mapping = array_combine(array_keys($otherversion->answers), array_keys($this->answers));

        $oldorder = explode(',', $oldstep->get_qt_var('_order'));
        $neworder = [];
        foreach ($oldorder as $oldid) {
            $neworder[] = $mapping[$oldid] ?? $oldid;
        }
        $startdata['_order'] = implode(',', $neworder);

        return $startdata;
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
            return $this->check_combined_feedback_file_access($qa, $options, $filearea, $args);

        } else if ($component == 'question' && $filearea == 'answer') {
            $answerid = reset($args); // Itemid is answer id.
            return  in_array($answerid, $this->order);

        } else if ($component == 'question' && $filearea == 'answerfeedback') {
            $answerid = reset($args); // Itemid is answer id.
            $response = $this->get_response($qa);
            $isselected = false;
            foreach ($this->order as $value => $ansid) {
                if ($ansid == $answerid) {
                    $isselected = $this->is_choice_selected($response, $value);
                    break;
                }
            }
            // Param $options->suppresschoicefeedback is a hack specific to the
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

    /**
     * Return the question settings that define this question as structured data.
     *
     * @param question_attempt $qa the current attempt for which we are exporting the settings.
     * @param question_display_options $options the question display options which say which aspects of the question
     * should be visible.
     * @return mixed structure representing the question settings. In web services, this will be JSON-encoded.
     */
    public function get_question_definition_for_external_rendering(question_attempt $qa, question_display_options $options) {
        // This is a partial implementation, returning only the most relevant question settings for now,
        // ideally, we should return as much as settings as possible (depending on the state and display options).

        return [
            'shuffleanswers' => $this->shuffleanswers,
            'answernumbering' => $this->answernumbering,
            'showstandardinstruction' => $this->showstandardinstruction,
            'layout' => $this->layout,
        ];
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
        if (!$this->is_complete_response($response)) {
            return null;
        }
        $answerid = $this->order[$response['answer']];
        return $this->html_to_text($this->answers[$answerid]->answer,
                $this->answers[$answerid]->answerformat);
    }

    public function un_summarise_response(string $summary) {
        foreach ($this->order as $key => $answerid) {
            if ($summary === $this->html_to_text($this->answers[$answerid]->answer,
                    $this->answers[$answerid]->answerformat)) {
                return ['answer' => $key];
            }
        }
        return [];
    }

    public function classify_response(array $response) {
        if (!$this->is_complete_response($response)) {
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

    public function prepare_simulated_post_data($simulatedresponse) {
        $ansid = 0;
        foreach ($this->answers as $answer) {
            if (clean_param($answer->answer, PARAM_NOTAGS) == $simulatedresponse['answer']) {
                $ansid = $answer->id;
            }
        }
        if ($ansid) {
            return array('answer' => array_search($ansid, $this->order));
        } else {
            return array();
        }
    }

    public function get_student_response_values_for_simulation($postdata) {
        if (!isset($postdata['answer'])) {
            return array();
        } else {
            $answer = $this->answers[$this->order[$postdata['answer']]];
            return array('answer' => clean_param($answer->answer, PARAM_NOTAGS));
        }
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        if (!$this->is_complete_response($prevresponse)) {
            $prevresponse = [];
        }
        if (!$this->is_complete_response($newresponse)) {
            $newresponse = [];
        }
        return question_utils::arrays_same_at_key($prevresponse, $newresponse, 'answer');
    }

    public function is_complete_response(array $response) {
        return array_key_exists('answer', $response) && $response['answer'] !== ''
                && (string) $response['answer'] !== '-1';
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

    public function un_summarise_response(string $summary) {
        // This implementation is not perfect. It will fail if an answer contains '; ',
        // but this method is only for testing, so it is good enough.
        $selectedchoices = explode('; ', $summary);
        $response = [];
        foreach ($this->order as $key => $answerid) {
            if (in_array($this->html_to_text($this->answers[$answerid]->answer,
                    $this->answers[$answerid]->answerformat), $selectedchoices)) {
                $response[$this->field($key)] = '1';
            }
        }
        return $response;
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

    public function prepare_simulated_post_data($simulatedresponse) {
        $postdata = array();
        foreach ($simulatedresponse as $ans => $checked) {
            foreach ($this->answers as $ansid => $answer) {
                if (clean_param($answer->answer, PARAM_NOTAGS) == $ans) {
                    $fieldno = array_search($ansid, $this->order);
                    $postdata[$this->field($fieldno)] = $checked;
                    break;
                }
            }
        }
        return $postdata;
    }

    public function get_student_response_values_for_simulation($postdata) {
        $simulatedresponse = array();
        foreach ($this->order as $fieldno => $ansid) {
            if (isset($postdata[$this->field($fieldno)])) {
                $checked = $postdata[$this->field($fieldno)];
                $simulatedresponse[clean_param($this->answers[$ansid]->answer, PARAM_NOTAGS)] = $checked;
            }
        }
        ksort($simulatedresponse);
        return $simulatedresponse;
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
            // Response keys starting with _ are internal values like _order, so ignore them.
            if (!empty($value) && $key[0] != '_') {
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
