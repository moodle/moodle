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

require_once($CFG->dirroot . '/question/type/numerical/questiontype.php');


/**
 * Represents a numerical question.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_numerical_question extends question_graded_automatically {
    /** @var array of question_answer. */
    public $answers = array();

    /** @var int one of the constants UNITNONE, UNITDISPLAY, UNITSELECT or UNITINPUT. */
    public $unitdisplay;
    /** @var int one of the constants UNITGRADEDOUTOFMARK or UNITGRADEDOUTOFMAX. */
    public $unitgradingtype;
    /** @var number the penalty for a missing or unrecognised unit. */
    public $unitpenalty;

    /** @var qtype_numerical_answer_processor */
    public $ap;

    public function __construct() {
        parent::__construct();
    }

    public function get_expected_data() {
        return array('answer' => PARAM_RAW_TRIMMED);
    }

    public function start_attempt(question_attempt_step $step) {
        $step->set_qt_var('_separators',
                $this->ap->get_point() . '$' . $this->ap->get_separator());
    }

    public function apply_attempt_state(question_attempt_step $step) {
        list($point, $separator) = explode('$', $step->get_qt_var('_separators'));
                $this->ap->set_characters($point, $separator);
    }

    public function summarise_response(array $response) {
        if (isset($response['answer'])) {
            return $response['answer'];
        } else {
            return null;
        }
    }

    public function is_complete_response(array $response) {
        return array_key_exists('answer', $response) &&
                ($response['answer'] || $response['answer'] === '0' || $response['answer'] === 0);
    }

    public function get_validation_error(array $response) {
        if ($this->is_gradable_response($response)) {
            return '';
        }
        return get_string('pleaseenterananswer', 'qtype_numerical');
    }

    public function is_same_response(array $prevresponse, array $newresponse) {
        return question_utils::arrays_same_at_key_missing_is_blank(
                $prevresponse, $newresponse, 'answer');
    }

    public function get_correct_response() {
        $answer = $this->get_correct_answer();
        if (!$answer) {
            return array();
        }

        return array('answer' => $this->ap->add_unit($answer->answer));
    }

    /**
     * Get an answer that contains the feedback and fraction that should be
     * awarded for this resonse.
     * @param number $value the numerical value of a response.
     * @return question_answer the matching answer.
     */
    public function get_matching_answer($value) {
        foreach ($this->answers as $aid => $answer) {
            if ($answer->within_tolerance($value)) {
                $answer->id = $aid;
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

    protected function apply_unit_penalty($fraction, $unit) {
        if (!empty($unit)) {
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
        list($value, $unit) = $this->ap->apply_units($response['answer']);
        $answer = $this->get_matching_answer($value);
        if (!$answer) {
            return array(0, question_state::$gradedwrong);
        }

        $fraction = $this->apply_unit_penalty($answer->fraction, $unit);
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    public function classify_response(array $response) {
        if (empty($response['answer'])) {
            return array($this->id => question_classified_response::no_response());
        }

        list($value, $unit) = $this->ap->apply_units($response['answer']);
        $ans = $this->get_matching_answer($value);
        if (!$ans) {
            return array($this->id => question_classified_response::no_response());
        }
        return array($this->id => new question_classified_response($ans->id,
                $response['answer'],
                $this->apply_unit_penalty($ans->fraction, $unit)));
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

        // We need to add a tiny fraction depending on the set precision to make
        // the comparison work correctly, otherwise seemingly equal values can
        // yield false. See MDL-3225.
        $tolerance = (float) $this->tolerance + pow(10, -1 * ini_get('precision'));

        switch ($this->tolerancetype) {
            case 1: case 'relative':
                $range = abs($this->answer) * $tolerance;
                return array($this->answer - $range, $this->answer + $range);

            case 2: case 'nominal':
                $tolerance = $this->tolerance + pow(10, -1 * ini_get('precision')) *
                        max(1, abs($this->answer));
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
