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
 * Defines the renderer base classes for question types.
 *
 * @package    moodlecore
 * @subpackage questiontypes
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Renderer base classes for question types.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_renderer extends plugin_renderer_base {
    /**
     * Generate the display of the formulation part of the question. This is the
     * area that contains the quetsion text, and the controls for students to
     * input their answers. Some question types also embed bits of feedback, for
     * example ticks and crosses, in this area.
     *
     * @param question_attempt $qa the question attempt to display.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string HTML fragment.
     */
    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {
        return $qa->get_question()->format_questiontext($qa);
    }

    /**
     * In the question output there are some class="accesshide" headers to help
     * screen-readers. This method returns the text to use for the heading above
     * the formulation_and_controls section.
     * @return string to use as the heading.
     */
    public function formulation_heading() {
        return get_string('questiontext', 'question');
    }

    /**
     * Output hidden form fields to clear any wrong parts of the student's response.
     *
     * This method will only be called if the question is in read-only mode.
     * @param question_attempt $qa the question attempt to display.
     * @return string HTML fragment.
     */
    public function clear_wrong(question_attempt $qa) {
        $response = $qa->get_last_qt_data();
        if (!$response) {
            return '';
        }
        $cleanresponse = $qa->get_question()->clear_wrong_from_response($response);
        $output = '';
        foreach ($cleanresponse as $name => $value) {
            $attr = array(
                'type' => 'hidden',
                'name' => $qa->get_qt_field_name($name),
                'value' => s($value),
            );
            $output .= html_writer::empty_tag('input', $attr);
        }
        return $output;
    }

    /**
     * Generate the display of the outcome part of the question. This is the
     * area that contains the various forms of feedback. This function generates
     * the content of this area belonging to the question type.
     *
     * Subclasses will normally want to override the more specific methods
     * {specific_feedback()}, {general_feedback()} and {correct_response()}
     * that this method calls.
     *
     * @param question_attempt $qa the question attempt to display.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string HTML fragment.
     */
    public function feedback(question_attempt $qa, question_display_options $options) {
        $output = '';
        $hint = null;

        if ($options->feedback) {
            $output .= html_writer::nonempty_tag('div', $this->specific_feedback($qa),
                    array('class' => 'specificfeedback'));
            $hint = $qa->get_applicable_hint();
        }

        if ($options->numpartscorrect) {
            $output .= html_writer::nonempty_tag('div', $this->num_parts_correct($qa),
                    array('class' => 'numpartscorrect'));
        }

        if ($hint) {
            $output .= $this->hint($qa, $hint);
        }

        if ($options->generalfeedback) {
            $output .= html_writer::nonempty_tag('div', $this->general_feedback($qa),
                    array('class' => 'generalfeedback'));
        }

        if ($options->rightanswer) {
            $output .= html_writer::nonempty_tag('div', $this->correct_response($qa),
                    array('class' => 'rightanswer'));
        }

        return $output;
    }

    /**
     * Gereate the specific feedback. This is feedback that varies accordin to
     * the reponse the student gave.
     * @param question_attempt $qa the question attempt to display.
     * @return string HTML fragment.
     */
    protected function specific_feedback(question_attempt $qa) {
        return '';
    }

    /**
     * Gereate a brief statement of how many sub-parts of this question the
     * student got right.
     * @param question_attempt $qa the question attempt to display.
     * @return string HTML fragment.
     */
    protected function num_parts_correct(question_attempt $qa) {
        $a = new stdClass();
        list($a->num, $a->outof) = $qa->get_question()->get_num_parts_right(
                $qa->get_last_qt_data());
        if (is_null($a->outof)) {
            return '';
        } else {
            return get_string('yougotnright', 'question', $a);
        }
    }

    /**
     * Gereate the specific feedback. This is feedback that varies accordin to
     * the reponse the student gave.
     * @param question_attempt $qa the question attempt to display.
     * @return string HTML fragment.
     */
    protected function hint(question_attempt $qa, question_hint $hint) {
        return html_writer::nonempty_tag('div',
                $qa->get_question()->format_hint($hint, $qa), array('class' => 'hint'));
    }

    /**
     * Gereate the general feedback. This is feedback is shown ot all students.
     *
     * @param question_attempt $qa the question attempt to display.
     * @return string HTML fragment.
     */
    protected function general_feedback(question_attempt $qa) {
        return $qa->get_question()->format_generalfeedback($qa);
    }

    /**
     * Gereate an automatic description of the correct response to this question.
     * Not all question types can do this. If it is not possible, this method
     * should just return an empty string.
     *
     * @param question_attempt $qa the question attempt to display.
     * @return string HTML fragment.
     */
    protected function correct_response(question_attempt $qa) {
        return '';
    }

    /**
     * Display any extra question-type specific content that should be visible
     * when grading, if appropriate.
     *
     * @param question_attempt $qa a question attempt.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string HTML fragment.
     */
    public function manual_comment(question_attempt $qa, question_display_options $options) {
        return '';
    }

    /**
     * Return any HTML that needs to be included in the page's <head> when this
     * question is used.
     * @param $qa the question attempt that will be displayed on the page.
     * @return string HTML fragment.
     */
    public function head_code(question_attempt $qa) {
        // This method is used by the Opaque question type. The remote question
        // engine can send back arbitrary CSS that we have to link to in the
        // page header. If it was not for that, we might be able to eliminate
        // this method and load the required CSS and JS some other way.
        $qa->get_question()->qtype->find_standard_scripts();
    }

    protected function feedback_class($fraction) {
        return question_state::graded_state_for_fraction($fraction)->get_feedback_class();
    }

    /**
     * Return an appropriate icon (green tick, red cross, etc.) for a grade.
     * @param float $fraction grade on a scale 0..1.
     * @param bool $selected whether to show a big or small icon. (Deprecated)
     * @return string html fragment.
     */
    protected function feedback_image($fraction, $selected = true) {
        $state = question_state::graded_state_for_fraction($fraction);

        if ($state == question_state::$gradedright) {
            $icon = 'tick_green';
        } else if ($state == question_state::$gradedpartial) {
            $icon = 'tick_amber';
        } else {
            $icon = 'cross_red';
        }
        if ($selected) {
            $icon .= '_big';
        } else {
            $icon .= '_small';
        }

        $attributes = array(
            'src' => $this->output->pix_url('i/' . $icon),
            'alt' => get_string($state->get_feedback_class(), 'question'),
            'class' => 'questioncorrectnessicon',
        );

        return html_writer::empty_tag('img', $attributes);
    }
}

/**
 * Renderer base classes for question types.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_with_combined_feedback_renderer extends qtype_renderer {
    protected function combined_feedback(question_attempt $qa) {
        $question = $qa->get_question();

        $state = $qa->get_state();

        if (!$state->is_finished()) {
            $response = $qa->get_last_qt_data();
            if (!$qa->get_question()->is_gradable_response($response)) {
                return '';
            }
            list($notused, $state) = $qa->get_question()->grade_response($response);
        }

        $feedback = '';
        $field = $state->get_feedback_class() . 'feedback';
        $format = $state->get_feedback_class() . 'feedbackformat';
        if ($question->$field) {
            $feedback .= $question->format_text($question->$field, $question->$format,
                    $qa, 'question', $field, $question->id);
        }

        return $feedback;
    }
}
