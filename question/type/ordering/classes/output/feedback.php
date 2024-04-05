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

namespace qtype_ordering\output;

use renderer_base;
use question_attempt;
use question_display_options;

/**
 * Collate various sections of displayable feedback for render.
 *
 * @package    qtype_ordering
 * @copyright  2023 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class feedback extends renderable_base {

    /**
     * Define the feedback with options for display.
     *
     * @param question_attempt $qa The question attempt object.
     * @param question_display_options $options Controls what should and should not be displayed
     * via question_display_options but unit tests are fickle.
     */
    public function __construct(
        question_attempt $qa,
        /** @var question_display_options The question display options. */
        protected question_display_options $options
    ) {
        parent::__construct($qa);
    }

    public function export_for_template(renderer_base $output): array {
        global $PAGE;

        $data = [];
        $question = $this->qa->get_question();
        $qtyperenderer = $PAGE->get_renderer('qtype_ordering');

        if ($this->options->feedback) {
            // Literal render out but we trust the teacher.
            $data['specificfeedback'] = $qtyperenderer->specific_feedback($this->qa);

            $specificgradedetailfeedback = new specific_grade_detail_feedback($this->qa);
            $data['specificgradedetailfeedback'] = $specificgradedetailfeedback->export_for_template($output);

            if ($hint = $this->qa->get_applicable_hint()) {
                $data['hint'] = $question->format_hint($hint, $this->qa);
            }
        }

        if ($this->options->numpartscorrect) {
            $numpartscorrect = new num_parts_correct($this->qa);
            $data['numpartscorrect'] = $numpartscorrect->export_for_template($output);
        }

        if ($this->options->generalfeedback) {
            // Literal render out but we trust the teacher.
            $data['generalfeedback'] = $question->format_generalfeedback($this->qa);
        }

        if ($this->options->rightanswer) {
            $correctresponse = new correct_response($this->qa);
            $data['rightanswer'] = $correctresponse->export_for_template($output);
        }

        return $data;
    }
}
