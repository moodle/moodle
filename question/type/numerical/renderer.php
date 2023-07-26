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
        if ($question->has_separate_unit_field()) {
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
            'size' => 30,
            'class' => 'form-control d-inline',
        );

        if ($options->readonly) {
            $inputattributes['readonly'] = 'readonly';
        }

        $feedbackimg = '';
        if ($options->correctness) {
            list($value, $unit, $multiplier) = $question->ap->apply_units(
                    $currentanswer, $selectedunit);
            $answer = $question->get_matching_answer($value, $multiplier);
            if ($answer) {
                $unitisright = $question->is_unit_right($answer, $value, $multiplier);
                $fraction = $question->apply_unit_penalty($answer->fraction, $unitisright);
            } else {
                $fraction = 0;
            }
            $inputattributes['class'] .= ' ' . $this->feedback_class($fraction);
            $feedbackimg = $this->feedback_image($fraction);
        }

        $questiontext = $question->format_questiontext($qa);
        $placeholder = false;
        if (preg_match('/_____+/', $questiontext, $matches)) {
            $placeholder = $matches[0];
            $inputattributes['size'] = round(strlen($placeholder) * 1.1);
        }

        $input = html_writer::empty_tag('input', $inputattributes) . $feedbackimg;

        if ($question->has_separate_unit_field()) {
            if ($question->unitdisplay == qtype_numerical::UNITRADIO) {
                $choices = array();
                $i = 1;
                foreach ($question->ap->get_unit_options() as $unit) {
                    $id = $qa->get_qt_field_name('unit') . '_' . $i++;
                    $radioattrs = array('type' => 'radio', 'id' => $id, 'value' => $unit,
                            'name' => $qa->get_qt_field_name('unit'));
                    if ($unit == $selectedunit) {
                        $radioattrs['checked'] = 'checked';
                    }
                    $choices[] = html_writer::tag('label',
                            html_writer::empty_tag('input', $radioattrs) . $unit,
                            array('for' => $id, 'class' => 'unitchoice'));
                }

                $unitchoice = html_writer::tag('span', implode(' ', $choices),
                        array('class' => 'unitchoices'));

            } else if ($question->unitdisplay == qtype_numerical::UNITSELECT) {
                $unitchoice = html_writer::label(get_string('selectunit', 'qtype_numerical'),
                        'menu' . $qa->get_qt_field_name('unit'), false, array('class' => 'accesshide'));
                $unitchoice .= html_writer::select($question->ap->get_unit_options(),
                        $qa->get_qt_field_name('unit'), $selectedunit, array(''=>'choosedots'),
                        array('disabled' => $options->readonly));
            }

            if ($question->ap->are_units_before()) {
                $input = $unitchoice . ' ' . $input;
            } else {
                $input = $input . ' ' . $unitchoice;
            }
        }

        if ($placeholder) {
            $inputinplace = html_writer::tag('label', $options->add_question_identifier_to_label(get_string('answer')),
                    array('for' => $inputattributes['id'], 'class' => 'sr-only'));
            $inputinplace .= $input;
            $questiontext = substr_replace($questiontext, $inputinplace,
                    strpos($questiontext, $placeholder), strlen($placeholder));
        }

        $result = html_writer::tag('div', $questiontext, array('class' => 'qtext'));

        if (!$placeholder) {
            $result .= html_writer::start_tag('div', array('class' => 'ablock form-inline'));
            $label = $options->add_question_identifier_to_label(get_string('answercolon', 'qtype_numerical'), true);
            $result .= html_writer::tag('label', $label,
                array('for' => $inputattributes['id']));
            $result .= html_writer::tag('span', $input, array('class' => 'answer'));
            $result .= html_writer::end_tag('div');
        }

        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div',
                    $question->get_validation_error(array('answer' => $currentanswer, 'unit' => $selectedunit)),
                    array('class' => 'validationerror'));
        }

        return $result;
    }

    public function specific_feedback(question_attempt $qa) {
        $question = $qa->get_question();

        if ($question->has_separate_unit_field()) {
            $selectedunit = $qa->get_last_qt_var('unit');
        } else {
            $selectedunit = null;
        }
        list($value, $unit, $multiplier) = $question->ap->apply_units(
                $qa->get_last_qt_var('answer'), $selectedunit);
        $answer = $question->get_matching_answer($value, $multiplier);

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

        $response = str_replace('.', $question->ap->get_point(), $answer->answer);
        if ($question->unitdisplay != qtype_numerical::UNITNONE) {
            $response = $question->ap->add_unit($response);
        }

        return get_string('correctansweris', 'qtype_shortanswer', $response);
    }
}
