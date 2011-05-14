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
 * Numerical question renderer class.
 *
 * @package qtype_numerical
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Generates the output for short answer questions.
 *
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_numerical_renderer extends qtype_renderer {
    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {

        $question = $qa->get_question();
        $currentanswer = $qa->get_last_qt_var('answer');
        if ($question->unitdisplay == qtype_numerical::UNITSELECT) {
            $selectedunit = $qa->get_last_qt_var('unit');
        } else {
            $selectedunit = null;
        }

        $inputname = $qa->get_qt_field_name('answer');
        $inputattributes = array(
            'type' => 'text',
            'name' => $inputname,
            'value' => $currentanswer,
            'id' => $inputname,
            'size' => 80,
        );

        if ($options->readonly) {
            $inputattributes['readonly'] = 'readonly';
        }

        $feedbackimg = '';
        if ($options->correctness) {
            list($value, $unit) = $question->ap->apply_units($currentanswer, $selectedunit);
            $answer = $question->get_matching_answer($value);
            if ($answer) {
                $fraction = $question->apply_unit_penalty($answer->fraction, $unit);
            } else {
                $fraction = 0;
            }
            $inputattributes['class'] = $this->feedback_class($fraction);
            $feedbackimg = $this->feedback_image($fraction);
        }

        $questiontext = $question->format_questiontext($qa);
        $placeholder = false;
        if (preg_match('/_____+/', $questiontext, $matches)) {
            $placeholder = $matches[0];
            $inputattributes['size'] = round(strlen($placeholder) * 1.1);
        }

        $input = html_writer::empty_tag('input', $inputattributes) . $feedbackimg;

        if ($question->unitdisplay == qtype_numerical::UNITSELECT) {
            $unitselect = html_writer::select($question->ap->get_unit_options(),
                    $qa->get_qt_field_name('unit'), $selectedunit, array(''=>'choosedots'),
                    array('disabled' => $options->readonly));
            if ($question->ap->are_units_before()) {
                $input = $unitselect . ' ' . $input;
            } else {
                $input = $input . ' ' . $unitselect;
            }
        }

        if ($placeholder) {
            $questiontext = substr_replace($questiontext, $input,
                    strpos($questiontext, $placeholder), strlen($placeholder));
        }

        $result = html_writer::tag('div', $questiontext, array('class' => 'qtext'));

        if (!$placeholder) {
            $result .= html_writer::start_tag('div', array('class' => 'ablock'));
            $result .= get_string('answer', 'qtype_shortanswer',
                    html_writer::tag('div', $input, array('class' => 'answer')));
            $result .= html_writer::end_tag('div');
        }

        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div',
                    $question->get_validation_error(array('answer' => $currentanswer)),
                    array('class' => 'validationerror'));
        }

        return $result;
    }

    public function specific_feedback(question_attempt $qa) {
        $question = $qa->get_question();

        if ($question->unitdisplay == qtype_numerical::UNITSELECT) {
            $selectedunit = $qa->get_last_qt_var('unit');
        } else {
            $selectedunit = null;
        }
        list($value, $unit) = $question->ap->apply_units(
                $qa->get_last_qt_var('answer'), $selectedunit);
        $answer = $question->get_matching_answer($value);

        if ($answer && $answer->feedback) {
            $feedback = $question->format_text($answer->feedback, $answer->feedbackformat,
                    $qa, 'question', 'answerfeedback', $answer->id);
        } else {
            $feedback = '';
        }

        if ($question->unitgradingtype && !$question->ap->is_known_unit($unit)) {
            $feedback .= html_writer::tag('p', get_string('unitincorrect', 'qtype_numerical'));
        }

        return $feedback;
    }

    public function correct_response(question_attempt $qa) {
        $question = $qa->get_question();
        $answer = $question->get_correct_answer();
        if (!$answer) {
            return '';
        }

        $response = $answer->answer;
        if ($question->unitdisplay != qtype_numerical::UNITNONE) {
            $response = $question->ap->add_unit($response);
        }

        return get_string('correctansweris', 'qtype_shortanswer', s($response));
    }
}
