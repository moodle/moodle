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
 * Multiple choice question renderer classes.
 *
 * @package    qtype
 * @subpackage multichoice
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Base class for generating the bits of output common to multiple choice
 * single and multiple questions.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_multichoice_renderer_base extends qtype_with_combined_feedback_renderer {

    /**
     * Method to generating the bits of output after question choices.
     *
     * @param question_attempt $qa The question attempt object.
     * @param question_display_options $options controls what should and should not be displayed.
     *
     * @return string HTML output.
     */
    protected abstract function after_choices(question_attempt $qa, question_display_options $options);

    protected abstract function get_input_type();

    protected abstract function get_input_name(question_attempt $qa, $value);

    protected abstract function get_input_value($value);

    protected abstract function get_input_id(question_attempt $qa, $value);

    /**
     * Whether a choice should be considered right, wrong or partially right.
     * @param question_answer $ans representing one of the choices.
     * @return fload 1.0, 0.0 or something in between, respectively.
     */
    protected abstract function is_right(question_answer $ans);

    protected abstract function prompt();

    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {

        $question = $qa->get_question();
        $response = $question->get_response($qa);

        $inputname = $qa->get_qt_field_name('answer');
        $inputattributes = array();
        $radiobuttons = array();
        $feedbackimg = array();
        $feedback = array();
        $classes = array();
        $letters = range('A', 'Z');
        $let = -1;
        $roclass = $options->readonly ? ' readonly-button' : '';
        foreach ($question->get_order($qa) as $value => $ansid) {
            $let = $let + 1;
            $ans = $question->answers[$ansid];
            $corclass = '';
            $isselected = $question->is_choice_selected($response, $value);
            if ($options->feedback && empty($options->suppresschoicefeedback) &&
                    $isselected && trim($ans->feedback)) {
                $feedback[] = html_writer::tag('div',
                        $question->make_html_inline($question->format_text(
                                $ans->feedback, $ans->feedbackformat,
                                $qa, 'question', 'answerfeedback', $ansid)),
                        array('class' => 'specificfeedback'));
            } else {
                $feedback[] = '';
            }
            if ($options->correctness && ($this->is_right($ans) > 0 || $isselected)) {
                $corclass = $this->is_right($ans) > 0 ? " answer-button-correct" : " answer-button-incorrect";
            }
            $radiobuttons[] = html_writer::tag('button',
                        html_writer::tag('div',
                        html_writer::tag('div', $letters[$let], array('class' => 'col-3 col-sm-3 col-md-2 col-lg-2 col-xl-2 letter-answer')).
                        html_writer::tag('div', $ans->answer, array('class' => 'col-9 col-sm-9 col-md-10 col-lg-10 possible-answer')), array('class' => 'row')),
                        array('class' => 'answer-button shortcut'.$letters[$let].$corclass.$roclass, 'type' => 'submit', 'name' => $this->get_input_name($qa, $value), 'value' => $this->get_input_value($value), 'id' => $this->get_input_id($qa, $value)));
        }

        $result = '';
        $result .= html_writer::tag('div', $question->format_questiontext($qa),
                array('class' => 'qtext'));

        $result .= html_writer::start_tag('div', array('class' => 'ablock'));
        if ($question->showstandardinstruction == 1) {
            $result .= html_writer::tag('div', $this->prompt(), array('class' => 'prompt'));
        }

        $result .= html_writer::start_tag('div', array('class' => 'answer'));
        foreach ($radiobuttons as $key => $radio) {
            $result .= html_writer::tag('div', $radio . ' ' . $feedbackimg[$key] . $feedback[$key],
                    array('class' => $classes[$key])) . "\n";
        }
        $result .= html_writer::end_tag('div'); // Answer.

        $result .= html_writer::empty_tag('input', array(
                    'type' => 'hidden',
                    'name' => $qa->get_qt_field_name('-submit'),
                    'value' => 1,
                    ));

        $result .= html_writer::end_tag('div'); // Ablock.

        $result .= html_writer::tag('script', 'document.onkeypress=function(e){var n=window.event||e;if(console.log(n),1===n.key.length&&n.key.match(/[a-z]/i)){var t=document.getElementsByClassName("shortcut"+n.key.toUpperCase());t.length>0&&-1===t[0].className.indexOf("readonly-button")&&t[0].click()}},document.onkeydown=function(e){if("37"==(e=e||window.event).keyCode)(n=document.getElementsByName("previous")).length>0&&n[0].click();else if("39"==e.keyCode){var n;(n=document.getElementsByName("next")).length>0&&n[0].click()}};', array());

        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div',
                    $question->get_validation_error($qa->get_last_qt_data()),
                    array('class' => 'validationerror'));
        }

        return $result;
    }

    protected function number_html($qnum) {
        return $qnum . '. ';
    }

    /**
     * @param int $num The number, starting at 0.
     * @param string $style The style to render the number in. One of the
     * options returned by {@link qtype_multichoice:;get_numbering_styles()}.
     * @return string the number $num in the requested style.
     */
    protected function number_in_style($num, $style) {
        switch($style) {
            case 'abc':
                $number = chr(ord('a') + $num);
                break;
            case 'ABCD':
                $number = chr(ord('A') + $num);
                break;
            case '123':
                $number = $num + 1;
                break;
            case 'iii':
                $number = question_utils::int_to_roman($num + 1);
                break;
            case 'IIII':
                $number = strtoupper(question_utils::int_to_roman($num + 1));
                break;
            case 'none':
                return '';
            default:
                return 'ERR';
        }
        return $this->number_html($number);
    }

    public function specific_feedback(question_attempt $qa) {
        return $this->combined_feedback($qa);
    }

    /**
     * Function returns string based on number of correct answers
     * @param array $right An Array of correct responses to the current question
     * @return string based on number of correct responses
     */
    protected function correct_choices(array $right) {
        // Return appropriate string for single/multiple correct answer(s).
        if (count($right) == 1) {
                return get_string('correctansweris', 'qtype_multichoice',
                        implode(', ', $right));
        } else if (count($right) > 1) {
                return get_string('correctanswersare', 'qtype_multichoice',
                        implode(', ', $right));
        } else {
                return "";
        }
    }
}


/**
 * Subclass for generating the bits of output specific to multiple choice
 * single questions.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multichoice_single_renderer extends qtype_multichoice_renderer_base {
    protected function get_input_type() {
        return 'radio';
    }

    protected function get_input_name(question_attempt $qa, $value) {
        return $qa->get_qt_field_name('answer');
    }

    protected function get_input_value($value) {
        return $value;
    }

    protected function get_input_id(question_attempt $qa, $value) {
        return $qa->get_qt_field_name('answer' . $value);
    }

    protected function is_right(question_answer $ans) {
        return $ans->fraction;
    }

    protected function prompt() {
        return get_string('selectone', 'qtype_multichoice');
    }

    public function correct_response(question_attempt $qa) {
        $question = $qa->get_question();

        // Put all correct answers (100% grade) into $right.
        $right = array();
        foreach ($question->answers as $ansid => $ans) {
            if (question_state::graded_state_for_fraction($ans->fraction) ==
                    question_state::$gradedright) {
                $right[] = $question->make_html_inline($question->format_text($ans->answer, $ans->answerformat,
                        $qa, 'question', 'answer', $ansid));
            }
        }
        return $this->correct_choices($right);
    }

    public function after_choices(question_attempt $qa, question_display_options $options) {
        return '';
    }
}
/**
 * Subclass for generating the bits of output specific to multiple choice
 * multi=select questions.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multichoice_multi_renderer extends qtype_multichoice_renderer_base {
    protected function after_choices(question_attempt $qa, question_display_options $options) {
        return '';
    }

    protected function get_input_type() {
        return 'checkbox';
    }

    protected function get_input_name(question_attempt $qa, $value) {
        return $qa->get_qt_field_name('choice' . $value);
    }

    protected function get_input_value($value) {
        return 1;
    }

    protected function get_input_id(question_attempt $qa, $value) {
        return $this->get_input_name($qa, $value);
    }

    protected function is_right(question_answer $ans) {
        if ($ans->fraction > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    protected function prompt() {
        return get_string('selectmulti', 'qtype_multichoice');
    }

    public function correct_response(question_attempt $qa) {
        $question = $qa->get_question();

        $right = array();
        foreach ($question->answers as $ansid => $ans) {
            if ($ans->fraction > 0) {
                $right[] = $question->make_html_inline($question->format_text($ans->answer, $ans->answerformat,
                        $qa, 'question', 'answer', $ansid));
            }
        }
        return $this->correct_choices($right);
    }

    protected function num_parts_correct(question_attempt $qa) {
        if ($qa->get_question()->get_num_selected_choices($qa->get_last_qt_data()) >
                $qa->get_question()->get_num_correct_choices()) {
            return get_string('toomanyselected', 'qtype_multichoice');
        }

        return parent::num_parts_correct($qa);
    }
}
