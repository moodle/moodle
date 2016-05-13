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
 * Multianswer question renderer classes.
 * Handle shortanswer, numerical and various multichoice subquestions
 *
 * @package    qtype
 * @subpackage multianswer
 * @copyright  2010 Pierre Pichet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once($CFG->dirroot . '/question/type/shortanswer/renderer.php');


/**
 * Base class for generating the bits of output common to multianswer
 * (Cloze) questions.
 * This render the main question text and transfer to the subquestions
 * the task of display their input elements and status
 * feedback, grade, correct answer(s)
 *
 * @copyright 2010 Pierre Pichet
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multianswer_renderer extends qtype_renderer {

    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {
        $question = $qa->get_question();

        $output = '';
        $subquestions = array();
        foreach ($question->textfragments as $i => $fragment) {
            if ($i > 0) {
                $index = $question->places[$i];
                $token = 'qtypemultianswer' . $i . 'marker';
                $token = '<span class="nolink">' . $token . '</span>';
                $output .= $token;
                $subquestions[$token] = $this->subquestion($qa, $options, $index,
                        $question->subquestions[$index]);
            }
            $output .= $fragment;
        }
        $output = $question->format_text($output, $question->questiontextformat,
                $qa, 'question', 'questiontext', $question->id);
        $output = str_replace(array_keys($subquestions), array_values($subquestions), $output);

        if ($qa->get_state() == question_state::$invalid) {
            $output .= html_writer::nonempty_tag('div',
                    $question->get_validation_error($qa->get_last_qt_data()),
                    array('class' => 'validationerror'));
        }

        $this->page->requires->js_init_call('M.qtype_multianswer.init',
                array('#q' . $qa->get_slot()), false, array(
                    'name'     => 'qtype_multianswer',
                    'fullpath' => '/question/type/multianswer/module.js',
                    'requires' => array('base', 'node', 'event', 'overlay'),
                ));

        return $output;
    }

    public function subquestion(question_attempt $qa,
            question_display_options $options, $index, question_graded_automatically $subq) {

        $subtype = $subq->qtype->name();
        if ($subtype == 'numerical' || $subtype == 'shortanswer') {
            $subrenderer = 'textfield';
        } else if ($subtype == 'multichoice') {
            if ($subq->layout == qtype_multichoice_base::LAYOUT_DROPDOWN) {
                $subrenderer = 'multichoice_inline';
            } else if ($subq->layout == qtype_multichoice_base::LAYOUT_HORIZONTAL) {
                $subrenderer = 'multichoice_horizontal';
            } else {
                $subrenderer = 'multichoice_vertical';
            }
        } else {
            throw new coding_exception('Unexpected subquestion type.', $subq);
        }
        $renderer = $this->page->get_renderer('qtype_multianswer', $subrenderer);
        return $renderer->subquestion($qa, $options, $index, $subq);
    }

    public function correct_response(question_attempt $qa) {
        return '';
    }
}


/**
 * Subclass for generating the bits of output specific to shortanswer
 * subquestions.
 *
 * @copyright 2011 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_multianswer_subq_renderer_base extends qtype_renderer {

    abstract public function subquestion(question_attempt $qa,
            question_display_options $options, $index,
            question_graded_automatically $subq);

    /**
     * Render the feedback pop-up contents.
     *
     * @param question_graded_automatically $subq the subquestion.
     * @param float $fraction the mark the student got. null if this subq was not answered.
     * @param string $feedbacktext the feedback text, already processed with format_text etc.
     * @param string $rightanswer the right answer, already processed with format_text etc.
     * @param question_display_options $options the display options.
     * @return string the HTML for the feedback popup.
     */
    protected function feedback_popup(question_graded_automatically $subq,
            $fraction, $feedbacktext, $rightanswer, question_display_options $options) {

        $feedback = array();
        if ($options->correctness) {
            if (is_null($fraction)) {
                $state = question_state::$gaveup;
            } else {
                $state = question_state::graded_state_for_fraction($fraction);
            }
            $feedback[] = $state->default_string(true);
        }

        if ($options->feedback && $feedbacktext) {
            $feedback[] = $feedbacktext;
        }

        if ($options->rightanswer) {
            $feedback[] = get_string('correctansweris', 'qtype_shortanswer', $rightanswer);
        }

        $subfraction = '';
        if ($options->marks >= question_display_options::MARK_AND_MAX && $subq->maxmark > 0
                && (!is_null($fraction) || $feedback)) {
            $a = new stdClass();
            $a->mark = format_float($fraction * $subq->maxmark, $options->markdp);
            $a->max = format_float($subq->maxmark, $options->markdp);
            $feedback[] = get_string('markoutofmax', 'question', $a);
        }

        if (!$feedback) {
            return '';
        }

        return html_writer::tag('span', implode('<br />', $feedback),
                array('class' => 'feedbackspan accesshide'));
    }
}


/**
 * Subclass for generating the bits of output specific to shortanswer
 * subquestions.
 *
 * @copyright 2011 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multianswer_textfield_renderer extends qtype_multianswer_subq_renderer_base {

    public function subquestion(question_attempt $qa, question_display_options $options,
            $index, question_graded_automatically $subq) {

        $fieldprefix = 'sub' . $index . '_';
        $fieldname = $fieldprefix . 'answer';

        $response = $qa->get_last_qt_var($fieldname);
        if ($subq->qtype->name() == 'shortanswer') {
            $matchinganswer = $subq->get_matching_answer(array('answer' => $response));
        } else if ($subq->qtype->name() == 'numerical') {
            list($value, $unit, $multiplier) = $subq->ap->apply_units($response, '');
            $matchinganswer = $subq->get_matching_answer($value, 1);
        } else {
            $matchinganswer = $subq->get_matching_answer($response);
        }

        if (!$matchinganswer) {
            if (is_null($response) || $response === '') {
                $matchinganswer = new question_answer(0, '', null, '', FORMAT_HTML);
            } else {
                $matchinganswer = new question_answer(0, '', 0.0, '', FORMAT_HTML);
            }
        }

        // Work out a good input field size.
        $size = max(1, core_text::strlen(trim($response)) + 1);
        foreach ($subq->answers as $ans) {
            $size = max($size, core_text::strlen(trim($ans->answer)));
        }
        $size = min(60, round($size + rand(0, $size * 0.15)));
        // The rand bit is to make guessing harder.

        $inputattributes = array(
            'type' => 'text',
            'name' => $qa->get_qt_field_name($fieldname),
            'value' => $response,
            'id' => $qa->get_qt_field_name($fieldname),
            'size' => $size,
        );
        if ($options->readonly) {
            $inputattributes['readonly'] = 'readonly';
        }

        $feedbackimg = '';
        if ($options->correctness) {
            $inputattributes['class'] = $this->feedback_class($matchinganswer->fraction);
            $feedbackimg = $this->feedback_image($matchinganswer->fraction);
        }

        if ($subq->qtype->name() == 'shortanswer') {
            $correctanswer = $subq->get_matching_answer($subq->get_correct_response());
        } else {
            $correctanswer = $subq->get_correct_answer();
        }

        $feedbackpopup = $this->feedback_popup($subq, $matchinganswer->fraction,
                $subq->format_text($matchinganswer->feedback, $matchinganswer->feedbackformat,
                        $qa, 'question', 'answerfeedback', $matchinganswer->id),
                s($correctanswer->answer), $options);

        $output = html_writer::start_tag('span', array('class' => 'subquestion'));
        $output .= html_writer::tag('label', get_string('answer'),
                array('class' => 'subq accesshide', 'for' => $inputattributes['id']));
        $output .= html_writer::empty_tag('input', $inputattributes);
        $output .= $feedbackimg;
        $output .= $feedbackpopup;
        $output .= html_writer::end_tag('span');

        return $output;
    }
}


/**
 * Render an embedded multiple-choice question that is displayed as a select menu.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multianswer_multichoice_inline_renderer
        extends qtype_multianswer_subq_renderer_base {

    public function subquestion(question_attempt $qa, question_display_options $options,
            $index, question_graded_automatically $subq) {

        $fieldprefix = 'sub' . $index . '_';
        $fieldname = $fieldprefix . 'answer';

        $response = $qa->get_last_qt_var($fieldname);
        $choices = array();
        $matchinganswer = new question_answer(0, '', null, '', FORMAT_HTML);
        $rightanswer = null;
        foreach ($subq->get_order($qa) as $value => $ansid) {
            $ans = $subq->answers[$ansid];
            $choices[$value] = $subq->format_text($ans->answer, $ans->answerformat,
                    $qa, 'question', 'answer', $ansid);
            if ($subq->is_choice_selected($response, $value)) {
                $matchinganswer = $ans;
            }
        }

        $inputattributes = array(
            'id' => $qa->get_qt_field_name($fieldname),
        );
        if ($options->readonly) {
            $inputattributes['disabled'] = 'disabled';
        }

        $feedbackimg = '';
        if ($options->correctness) {
            $inputattributes['class'] = $this->feedback_class($matchinganswer->fraction);
            $feedbackimg = $this->feedback_image($matchinganswer->fraction);
        }
        $select = html_writer::select($choices, $qa->get_qt_field_name($fieldname),
                $response, array('' => ''), $inputattributes);

        $order = $subq->get_order($qa);
        $correctresponses = $subq->get_correct_response();
        $rightanswer = $subq->answers[$order[reset($correctresponses)]];
        if (!$matchinganswer) {
            $matchinganswer = new question_answer(0, '', null, '', FORMAT_HTML);
        }
        $feedbackpopup = $this->feedback_popup($subq, $matchinganswer->fraction,
                $subq->format_text($matchinganswer->feedback, $matchinganswer->feedbackformat,
                        $qa, 'question', 'answerfeedback', $matchinganswer->id),
                $subq->format_text($rightanswer->answer, $rightanswer->answerformat,
                        $qa, 'question', 'answer', $rightanswer->id), $options);

        $output = html_writer::start_tag('span', array('class' => 'subquestion'));
        $output .= html_writer::tag('label', get_string('answer'),
                array('class' => 'subq accesshide', 'for' => $inputattributes['id']));
        $output .= $select;
        $output .= $feedbackimg;
        $output .= $feedbackpopup;
        $output .= html_writer::end_tag('span');

        return $output;
    }
}


/**
 * Render an embedded multiple-choice question vertically, like for a normal
 * multiple-choice question.
 *
 * @copyright  2010 Pierre Pichet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multianswer_multichoice_vertical_renderer extends qtype_multianswer_subq_renderer_base {

    public function subquestion(question_attempt $qa, question_display_options $options,
            $index, question_graded_automatically $subq) {

        $fieldprefix = 'sub' . $index . '_';
        $fieldname = $fieldprefix . 'answer';
        $response = $qa->get_last_qt_var($fieldname);

        $inputattributes = array(
            'type' => 'radio',
            'name' => $qa->get_qt_field_name($fieldname),
        );
        if ($options->readonly) {
            $inputattributes['disabled'] = 'disabled';
        }

        $result = $this->all_choices_wrapper_start();
        $fraction = null;
        foreach ($subq->get_order($qa) as $value => $ansid) {
            $ans = $subq->answers[$ansid];

            $inputattributes['value'] = $value;
            $inputattributes['id'] = $inputattributes['name'] . $value;

            $isselected = $subq->is_choice_selected($response, $value);
            if ($isselected) {
                $inputattributes['checked'] = 'checked';
                $fraction = $ans->fraction;
            } else {
                unset($inputattributes['checked']);
            }

            $class = 'r' . ($value % 2);
            if ($options->correctness && $isselected) {
                $feedbackimg = $this->feedback_image($ans->fraction);
                $class .= ' ' . $this->feedback_class($ans->fraction);
            } else {
                $feedbackimg = '';
            }

            $result .= $this->choice_wrapper_start($class);
            $result .= html_writer::empty_tag('input', $inputattributes);
            $result .= html_writer::tag('label', $subq->format_text($ans->answer,
                    $ans->answerformat, $qa, 'question', 'answer', $ansid),
                    array('for' => $inputattributes['id']));
            $result .= $feedbackimg;

            if ($options->feedback && $isselected && trim($ans->feedback)) {
                $result .= html_writer::tag('div',
                        $subq->format_text($ans->feedback, $ans->feedbackformat,
                                $qa, 'question', 'answerfeedback', $ansid),
                        array('class' => 'specificfeedback'));
            }

            $result .= $this->choice_wrapper_end();
        }

        $result .= $this->all_choices_wrapper_end();

        $feedback = array();
        if ($options->feedback && $options->marks >= question_display_options::MARK_AND_MAX &&
                $subq->maxmark > 0) {
            $a = new stdClass();
            $a->mark = format_float($fraction * $subq->maxmark, $options->markdp);
            $a->max = format_float($subq->maxmark, $options->markdp);

            $feedback[] = html_writer::tag('div', get_string('markoutofmax', 'question', $a));
        }

        if ($options->rightanswer) {
            foreach ($subq->answers as $ans) {
                if (question_state::graded_state_for_fraction($ans->fraction) ==
                        question_state::$gradedright) {
                    $feedback[] = get_string('correctansweris', 'qtype_multichoice',
                            $subq->format_text($ans->answer, $ans->answerformat,
                                    $qa, 'question', 'answer', $ansid));
                    break;
                }
            }
        }

        $result .= html_writer::nonempty_tag('div', implode('<br />', $feedback), array('class' => 'outcome'));

        return $result;
    }

    /**
     * @param string $class class attribute value.
     * @return string HTML to go before each choice.
     */
    protected function choice_wrapper_start($class) {
        return html_writer::start_tag('div', array('class' => $class));
    }

    /**
     * @return string HTML to go after each choice.
     */
    protected function choice_wrapper_end() {
        return html_writer::end_tag('div');
    }

    /**
     * @return string HTML to go before all the choices.
     */
    protected function all_choices_wrapper_start() {
        return html_writer::start_tag('div', array('class' => 'answer'));
    }

    /**
     * @return string HTML to go after all the choices.
     */
    protected function all_choices_wrapper_end() {
        return html_writer::end_tag('div');
    }
}


/**
 * Render an embedded multiple-choice question vertically, like for a normal
 * multiple-choice question.
 *
 * @copyright  2010 Pierre Pichet
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multianswer_multichoice_horizontal_renderer
        extends qtype_multianswer_multichoice_vertical_renderer {

    protected function choice_wrapper_start($class) {
        return html_writer::start_tag('td', array('class' => $class));
    }

    protected function choice_wrapper_end() {
        return html_writer::end_tag('td');
    }

    protected function all_choices_wrapper_start() {
        return html_writer::start_tag('table', array('class' => 'answer')) .
                html_writer::start_tag('tbody') . html_writer::start_tag('tr');
    }

    protected function all_choices_wrapper_end() {
        return html_writer::end_tag('tr') . html_writer::end_tag('tbody') .
                html_writer::end_tag('table');
    }
}
