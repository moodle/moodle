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

use qtype_ordering\output\correct_response;
use qtype_ordering\output\feedback;
use qtype_ordering\output\formulation_and_controls;
use qtype_ordering\output\num_parts_correct;
use qtype_ordering\output\specific_grade_detail_feedback;

/**
 * Ordering question renderer class.
 *
 * @package    qtype_ordering
 * @copyright  2013 Gordon Bateson (gordonbateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ordering_renderer extends qtype_with_combined_feedback_renderer {

    /**
     * Generate the display of the formulation part of the question. This is the
     * area that contains the question text, and the controls for students to
     * input their answers. Some question types also embed bits of feedback, for
     * example ticks and crosses, in this area.
     *
     * @param question_attempt $qa the question attempt to display.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string HTML fragment.
     * @throws moodle_exception
     * @codeCoverageIgnore
     */
    public function formulation_and_controls(question_attempt $qa, question_display_options $options): string {
        $formulationandcontrols = new formulation_and_controls($qa, $options);
        return $this->output->render_from_template('qtype_ordering/formulation_and_controls',
            $formulationandcontrols->export_for_template($this->output));
    }

    /**
     * Generate the display of the outcome part of the question. This is the
     * area that contains the various forms of feedback. This function generates
     * the content of this area belonging to the question type.
     *
     * @param question_attempt $qa The question attempt to display.
     * @param question_display_options $options Controls what should and should not be displayed.
     * @return string HTML fragment.
     * @throws moodle_exception
     * @codeCoverageIgnore
     */
    public function feedback(question_attempt $qa, question_display_options $options): string {
        $feedback = new feedback($qa, $options);
        return $this->output->render_from_template('qtype_ordering/feedback',
            $feedback->export_for_template($this->output));
    }

    /**
     * Display the grade detail of the response.
     *
     * @param question_attempt $qa The question attempt to display.
     * @return string Output grade detail of the response.
     * @throws moodle_exception
     * @codeCoverageIgnore
     */
    public function specific_grade_detail_feedback(question_attempt $qa): string {
        $specificgradedetailfeedback = new specific_grade_detail_feedback($qa);
        return $this->output->render_from_template('qtype_ordering/specific_grade_detail_feedback',
            $specificgradedetailfeedback->export_for_template($this->output));
    }

    /**
     * Generate the specific feedback. This is feedback that varies according to
     * the response the student gave.
     *
     * @param question_attempt $qa The question attempt to display.
     * @return string HTML fragment.
     * @codeCoverageIgnore
     */
    public function specific_feedback(question_attempt $qa): string {
        return $this->combined_feedback($qa);
    }

    /**
     * Generate an automatic description of the correct response to this question.
     * Not all question types can do this. If it is not possible, this method
     * should just return an empty string.
     *
     * @param question_attempt $qa the question attempt to display.
     * @return string HTML fragment.
     * @throws moodle_exception
     * @codeCoverageIgnore
     */
    public function correct_response(question_attempt $qa): string {
        $correctresponse = new correct_response($qa);

        return $this->output->render_from_template('qtype_ordering/correct_response',
            $correctresponse->export_for_template($this->output));
    }

    /**
     * Generate a brief statement of how many sub-parts of this question the
     * student got correct|partial|incorrect.
     *
     * @param question_attempt $qa The question attempt to display.
     * @return string HTML fragment.
     * @throws moodle_exception
     * @codeCoverageIgnore
     */
    protected function num_parts_correct(question_attempt $qa): string {
        $numpartscorrect = new num_parts_correct($qa);
        return $this->output->render_from_template('qtype_ordering/num_parts_correct',
            $numpartscorrect->export_for_template($this->output));
    }

    /**
     * Return an appropriate icon (green tick, red cross, etc.) for a grade.
     * Note: Strict typing the params here breaks code eval as the parent function is not strictly typed.
     *
     * @param float $fraction The fraction of the maximum grade that was awarded.
     * @param bool $selected Deprecated: size option.
     * @return string html fragment.
     */
    public function feedback_image($fraction, $selected = true): string {
        return parent::feedback_image($fraction);
    }
}
