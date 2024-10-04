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

    public function formulation_and_controls(question_attempt $qa, question_display_options $options): string {
        $formulationandcontrols = new formulation_and_controls($qa, $options);
        return $this->output->render_from_template('qtype_ordering/formulation_and_controls',
            $formulationandcontrols->export_for_template($this->output));
    }

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
     */
    public function specific_grade_detail_feedback(question_attempt $qa): string {
        $specificgradedetailfeedback = new specific_grade_detail_feedback($qa);
        return $this->output->render_from_template('qtype_ordering/specific_grade_detail_feedback',
            $specificgradedetailfeedback->export_for_template($this->output));
    }

    public function specific_feedback(question_attempt $qa): string {
        return $this->combined_feedback($qa);
    }

    public function correct_response(question_attempt $qa): string {
        $correctresponse = new correct_response($qa);

        return $this->output->render_from_template('qtype_ordering/correct_response',
            $correctresponse->export_for_template($this->output));
    }

    protected function num_parts_correct(question_attempt $qa): string {
        $numpartscorrect = new num_parts_correct($qa);
        return $this->output->render_from_template('qtype_ordering/num_parts_correct',
            $numpartscorrect->export_for_template($this->output));
    }

    public function feedback_image($fraction, $selected = true): string {
        return parent::feedback_image($fraction);
    }
}
