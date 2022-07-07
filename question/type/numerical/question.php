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
 * Numerical question definition class.
 *
 * @package    qtype
 * @subpackage numerical
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/questionbase.php');

/**
 * Represents a numerical question.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_numerical_question extends question_graded_automatically {
    /** @var array of question_answer. */
    public $answers = array();

    /** @var int one of the constants UNITNONE, UNITRADIO, UNITSELECT or UNITINPUT. */
    public $unitdisplay;
    /** @var int one of the constants UNITGRADEDOUTOFMARK or UNITGRADEDOUTOFMAX. */
    public $unitgradingtype;
    /** @var number the penalty for a missing or unrecognised unit. */
    public $unitpenalty;
    /** @var boolean whether the units come before or after the number */
    public $unitsleft;
    /** @var qtype_numerical_answer_processor */
    public $ap;

    public function get_expected_data() {
        $expected = array('answer' => PARAM_RAW_TRIMMED);
        if ($this->has_separate_unit_field()) {
            $expected['unit'] = PARAM_RAW_TRIMMED;
        }
        return $expected;
    }

    public function has_separate_unit_field() {
        return $this->unitdisplay == qtype_numerical::UNITRADIO ||
                $this->unitdisplay == qtype_numerical::UNITSELECT;
    }

    public function start_attempt(question_attempt_step $step, $variant) {
        $step->set_qt_var('_separators',
                $this->ap->get_point() . '$' . $this->ap->get_separator());
    }

    public function apply_attempt_state(question_attempt_step $step) {
        list($point, $separator) = explode('$', $step->get_qt_var('_separators'));
                $this->ap->set_characters($point, $separator);
    }

    public function summarise_response(array $response) {
        if (isset($response['answer'])) {
            $resp = $response['answer'];
        } else {
            $resp = null;
        }

        if ($this->has_separate_unit_field() && !empty($response['unit'])) {
            $resp = $this->ap->add_unit($resp, $response['unit']);
        }

        return $resp;
    }

    public function un_summarise_response(string $summary) {
        if ($this->has_separate_unit_field()) {
            throw new coding_exception('Sorry, but at the moment un_summarise_response cannot handle the
                has_separate_unit_field case for numerical questions.
                    If you need this, you will have to implement it yourself.');
        }

        if (!empty($summary)) {
            return ['answer' => $summary];
        } else {
            return [];
        }
    }

    public function is_gradable_response(array $response) {
        return array_key_exists('answer', $response) &&
                ($response['answer'] || $response['answer'] === '0' || $response['answer'] === 0);
    }

    public function is_complete_response(array $response) {
        if (!$this->is_gradable_response($response)) {
            return false;
        }

        list($value, $unit) = $this->ap->apply_units($response['answer']);
        if (is_null($value)) {
            return false;
        }

        if ($this->unitdisplay != qtype_numerical::UNITINPUT && $unit) {
            return false;
        }

        if ($this->has_separate_unit_field() && empty($response['unit'])) {
            return false;
        }

        if ($this->ap->contains_thousands_seaparator($response['answer'])) {
            return false;
        }

        return true;
    }

    public function get_validation_error(array $response) {
        if (!$this->is_gradable_response($response)) {
            return get_string('pleaseenterananswer', 'qtype_numerical');
        }

        list($value, $unit) = $this->ap->apply_units($response['answer']);
        if (is_null($value)) {
            return get_string('invalidnumber', 'qtype_numerical');
        }

        if ($this->unitdisplay != qtype_numerical::UNITINPUT && $unit) {
            return get_string('invalidnumbernounit', 'qtype_numerical');
        }

        if ($this->has_separate_unit_field() && empty($response['unit'])) {
            return get_string('unitnotselected', 'qtype_numerical');
        }

        if ($this->ap->contains_thousands_seaparator($response['answer'])) {
            return get_string('pleaseenteranswerwithoutthousandssep', 'qtype_numerical',
                    $this->ap->get_separator());
        }

        return '';
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        if (!question_utils::arrays_same_at_key_missing_is_blank(
                $prevresponse, $newresponse, 'answer')) {
            return false;
        }

        if ($this->has_separate_unit_field()) {
            return question_utils::arrays_same_at_key_missing_is_blank(
                $prevresponse, $newresponse, 'unit');
        }

        return true;
    }

    public function get_correct_response() {
        $answer = $this->get_correct_answer();
        if (!$answer) {
            return array();
        }

        $response = array('answer' => str_replace('.', $this->ap->get_point(), $answer->answer));

        if ($this->has_separate_unit_field()) {
            $response['unit'] = $this->ap->get_default_unit();
        } else if ($this->unitdisplay == qtype_numerical::UNITINPUT) {
            $response['answer'] = $this->ap->add_unit($answer->answer);
        }

        return $response;
    }

    /**
     * Get an answer that contains the feedback and fraction that should be
     * awarded for this response.
     * @param number $value the numerical value of a response.
     * @param number $multiplier for the unit the student gave, if any. When no
     *      unit was given, or an unrecognised unit was given, $multiplier will be null.
     * @return question_answer the matching answer.
     */
    public function get_matching_answer($value, $multiplier) {
        if (is_null($value) || $value === '') {
            return null;
        }

        if (!is_null($multiplier)) {
            $scaledvalue = $value * $multiplier;
        } else {
            $scaledvalue = $value;
        }
        foreach ($this->answers as $answer) {
            if ($answer->within_tolerance($scaledvalue)) {
                $answer->unitisright = !is_null($multiplier);
                return $answer;
            } else if ($answer->within_tolerance($value)) {
                $answer->unitisright = false;
                return $answer;
            }
        }

        return null;
    }

    public function get_correct_answer() {
        foreach ($this->answers as $answer) {
            $state = question_state::graded_state_for_fraction($answer->fraction);
            if ($state == question_state::$gradedright) {
                return $answer;
            }
        }
        return null;
    }

    /**
     * Adjust the fraction based on whether the unit was correct.
     * @param number $fraction
     * @param bool $unitisright
     * @return number
     */
    public function apply_unit_penalty($fraction, $unitisright) {
        if ($unitisright) {
            return $fraction;
        }

        if ($this->unitgradingtype == qtype_numerical::UNITGRADEDOUTOFMARK) {
            $fraction -= $this->unitpenalty * $fraction;
        } else if ($this->unitgradingtype == qtype_numerical::UNITGRADEDOUTOFMAX) {
            $fraction -= $this->unitpenalty;
        }
        return max($fraction, 0);
    }

    public function grade_response(array $response) {
        if ($this->has_separate_unit_field()) {
            $selectedunit = $response['unit'];
        } else {
            $selectedunit = null;
        }
        list($value, $unit, $multiplier) = $this->ap->apply_units(
                $response['answer'], $selectedunit);

        $answer = $this->get_matching_answer($value, $multiplier);
        if (!$answer) {
            return array(0, question_state::$gradedwrong);
        }

        $fraction = $this->apply_unit_penalty($answer->fraction, $answer->unitisright);
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    public function classify_response(array $response) {
        if (!$this->is_gradable_response($response)) {
            return array($this->id => question_classified_response::no_response());
        }

        if ($this->has_separate_unit_field()) {
            $selectedunit = $response['unit'];
        } else {
            $selectedunit = null;
        }
        list($value, $unit, $multiplier) = $this->ap->apply_units($response['answer'], $selectedunit);
        $ans = $this->get_matching_answer($value, $multiplier);

        $resp = $response['answer'];
        if ($this->has_separate_unit_field()) {
            $resp = $this->ap->add_unit($resp, $unit);
        }

        if ($value === null) {
            // Invalid response shown as no response (but show actual response).
            return array($this->id => new question_classified_response(null, $resp, 0));
        } else if (!$ans) {
            // Does not match any answer.
            return array($this->id => new question_classified_response(0, $resp, 0));
        }

        return array($this->id => new question_classified_response($ans->id,
                $resp,
                $this->apply_unit_penalty($ans->fraction, $ans->unitisright)));
    }

    public function check_file_access($qa, $options, $component, $filearea, $args,
            $forcedownload) {
        if ($component == 'question' && $filearea == 'answerfeedback') {
            $currentanswer = $qa->get_last_qt_var('answer');
            if ($this->has_separate_unit_field()) {
                $selectedunit = $qa->get_last_qt_var('unit');
            } else {
                $selectedunit = null;
            }
            list($value, $unit, $multiplier) = $this->ap->apply_units(
                    $currentanswer, $selectedunit);
            $answer = $this->get_matching_answer($value, $multiplier);
            $answerid = reset($args); // Itemid is answer id.
            return $options->feedback && $answer && $answerid == $answer->id;

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
            'unitgradingtype' => $this->unitgradingtype,
            'unitpenalty' => $this->unitpenalty,
            'unitdisplay' => $this->unitdisplay,
            'unitsleft' => $this->unitsleft,
        ];
    }
}


/**
 * Subclass of {@link question_answer} with the extra information required by
 * the numerical question type.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_numerical_answer extends question_answer {
    /** @var float allowable margin of error. */
    public $tolerance;
    /** @var integer|string see {@link get_tolerance_interval()} for the meaning of this value. */
    public $tolerancetype = 2;

    public function __construct($id, $answer, $fraction, $feedback, $feedbackformat, $tolerance) {
        parent::__construct($id, $answer, $fraction, $feedback, $feedbackformat);
        $this->tolerance = abs($tolerance);
    }

    public function get_tolerance_interval() {
        if ($this->answer === '*') {
            throw new coding_exception('Cannot work out tolerance interval for answer *.');
        }

        // Smallest number that, when added to 1, is different from 1.
        $epsilon = pow(10, -1 * ini_get('precision'));

        // We need to add a tiny fraction depending on the set precision to make
        // the comparison work correctly, otherwise seemingly equal values can
        // yield false. See MDL-3225.
        $tolerance = abs($this->tolerance) + $epsilon;

        switch ($this->tolerancetype) {
            case 1: case 'relative':
                $range = abs($this->answer) * $tolerance;
                return array($this->answer - $range, $this->answer + $range);

            case 2: case 'nominal':
                $tolerance = $this->tolerance + $epsilon * max(abs($this->tolerance), abs($this->answer), $epsilon);
                return array($this->answer - $tolerance, $this->answer + $tolerance);

            case 3: case 'geometric':
                $quotient = 1 + abs($tolerance);
                return array($this->answer / $quotient, $this->answer * $quotient);

            default:
                throw new coding_exception('Unknown tolerance type ' . $this->tolerancetype);
        }
    }

    public function within_tolerance($value) {
        if ($this->answer === '*') {
            return true;
        }
        list($min, $max) = $this->get_tolerance_interval();
        return $min <= $value && $value <= $max;
    }
}
