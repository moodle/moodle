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
 * Renderer for outputting parts of a question belonging to the legacy
 * adaptive behaviour.
 *
 * @package qbehaviour_adaptive
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class qbehaviour_adaptive_renderer extends qbehaviour_renderer {
    protected function get_graded_step(question_attempt $qa) {
        foreach ($qa->get_reverse_step_iterator() as $step) {
            if ($step->has_behaviour_var('_try')) {
                return $step;
            }
        }
    }

    public function controls(question_attempt $qa, question_display_options $options) {
        return $this->submit_button($qa, $options);
    }

    public function feedback(question_attempt $qa, question_display_options $options) {
        // Try to find the last graded step.

        $gradedstep = $this->get_graded_step($qa);
        if (is_null($gradedstep) || $qa->get_max_mark() == 0 || !$options->marks) {
            return '';
        }

        // Display the grading details from the last graded state
        $mark = new stdClass;
        $mark->max = $qa->format_max_mark($options->markdp);

        $actualmark = $gradedstep->get_fraction() * $qa->get_max_mark();
        $mark->cur = format_float($actualmark, $options->markdp);

        $rawmark = $gradedstep->get_behaviour_var('_rawfraction') * $qa->get_max_mark();
        $mark->raw = format_float($rawmark, $options->markdp);

        // let student know wether the answer was correct
        if ($qa->get_state()->is_commented()) {
            $class = $qa->get_state()->get_feedback_class();
        } else {
            $class = question_state::graded_state_for_fraction(
                    $gradedstep->get_behaviour_var('_rawfraction'))->get_feedback_class();
        }

        $gradingdetails = get_string('gradingdetails', 'qbehaviour_adaptive', $mark);

        $gradingdetails .= $this->penalty_info($qa, $mark);

        $output = '';
        $output .= html_writer::tag('div', get_string($class, 'question'),
                array('class' => 'correctness ' . $class));
        $output .= html_writer::tag('div', $gradingdetails,
                array('class' => 'gradingdetails'));
        return $output;
    }

    protected function penalty_info($qa, $mark) {
        if (!$qa->get_question()->penalty) {
            return '';
        }
        $output = '';

        // print details of grade adjustment due to penalties
        if ($mark->raw != $mark->cur) {
            $output .= ' ' . get_string('gradingdetailsadjustment', 'qbehaviour_adaptive', $mark);
        }

        // print info about new penalty
        // penalty is relevant only if the answer is not correct and further attempts are possible
        if (!$qa->get_state()->is_finished()) {
            $output .= ' ' . get_string('gradingdetailspenalty', 'qbehaviour_adaptive', $qa->get_question()->penalty);
        }

        return $output;
    }
}
