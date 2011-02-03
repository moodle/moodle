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
 * @package qtype_numerical
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/question/type/numerical/questiontype.php');


/**
 * Represents a numerical question.
 *
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_numerical_question extends question_graded_by_strategy
        implements question_response_answer_comparer {
    /** @var array of question_answer. */
    public $answers = array();
    /** @var qtype_numerical_answer_processor */
    public $ap;

    public function __construct() {
        parent::__construct(new question_first_matching_answer_grading_strategy($this));
    }

    public function get_expected_data() {
        return array('answer' => PARAM_RAW_TRIMMED);
    }

    public function init_first_step(question_attempt_step $step) {
        if ($step->has_qt_var('_separators')) {
            list($point, $separator) = explode('$', $step->get_qt_var('_separators'));
            $this->ap->set_characters($point, $separator);
        } else {
            $step->set_qt_var('_separators',
                    $this->ap->get_point() . '$' . $this->ap->get_separator());
        }
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

    public function get_answers() {
        return $this->answers;
    }

    public function compare_response_with_answer(array $response, question_answer $answer) {
        list($value, $unit) = $this->ap->apply_units($response['answer']);
        return $answer->within_tolerance($value);
    }
}


/**
 * Subclass of {@link question_answer} with the extra information required by
 * the numerical question type.
 *
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
            throw new Exception('Cannot work out tolerance interval for answer *.');
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
                throw new Exception('Unknown tolerance type ' . $this->tolerancetype);
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
