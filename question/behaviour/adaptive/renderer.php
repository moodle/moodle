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
 * @package    qbehaviour
 * @subpackage adaptive
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Renderer for outputting parts of a question belonging to the legacy
 * adaptive behaviour.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_adaptive_renderer extends qbehaviour_renderer {

    public function controls(question_attempt $qa, question_display_options $options) {
        return $this->submit_button($qa, $options);
    }

    public function feedback(question_attempt $qa, question_display_options $options) {

        // If the latest answer was invalid, display an informative message.
        if ($qa->get_state() == question_state::$invalid) {
            return html_writer::nonempty_tag('div', $this->disregarded_info(),
                    array('class' => 'gradingdetails'));
        }

        // Otherwise get the details.
        return $this->render_adaptive_marks(
                $qa->get_behaviour()->get_adaptive_marks(), $options);
    }

    /**
     * Display the scoring information about an adaptive attempt.
     * @param qbehaviour_adaptive_mark_details contains all the score details we need.
     * @param question_display_options $options display options.
     */
    public function render_adaptive_marks(qbehaviour_adaptive_mark_details $details, question_display_options $options) {
        if ($details->state == question_state::$todo || $options->marks < question_display_options::MARK_AND_MAX) {
            // No grades yet.
            return '';
        }

        // Display the grading details from the last graded state.
        $class = $details->state->get_feedback_class();
        return html_writer::tag('div', get_string($class, 'question'),
                        array('class' => 'correctness badge ' . $class))
                . html_writer::tag('div', $this->grading_details($details, $options),
                        array('class' => 'gradingdetails'));
    }

    /**
     * Display the information about the penalty calculations.
     * @param qbehaviour_adaptive_mark_details contains all the score details we need.
     * @param question_display_options $options display options.
     * @return string html fragment
     */
    protected function grading_details(qbehaviour_adaptive_mark_details $details, question_display_options $options) {

        $mark = $details->get_formatted_marks($options->markdp);

        if ($details->currentpenalty == 0 && $details->totalpenalty == 0) {
            return get_string('gradingdetails', 'qbehaviour_adaptive', $mark);
        }

        $output = '';

        // Print details of grade adjustment due to penalties
        if ($details->rawmark != $details->actualmark) {
            if (!$details->improvable) {
                return get_string('gradingdetailswithadjustment', 'qbehaviour_adaptive', $mark);
            } else if ($details->totalpenalty > $details->currentpenalty) {
                return get_string('gradingdetailswithadjustmenttotalpenalty', 'qbehaviour_adaptive', $mark);
            } else {
                return get_string('gradingdetailswithadjustmentpenalty', 'qbehaviour_adaptive', $mark);
            }

        } else {
            if (!$details->improvable) {
                return get_string('gradingdetails', 'qbehaviour_adaptive', $mark);
            } else if ($details->totalpenalty > $details->currentpenalty) {
                return get_string('gradingdetailswithtotalpenalty', 'qbehaviour_adaptive', $mark);
            } else {
                return get_string('gradingdetailswithpenalty', 'qbehaviour_adaptive', $mark);
            }
        }

        return $output;
    }

    /**
     * Display information about a disregarded (incomplete) response.
     */
    protected function disregarded_info() {
        return get_string('disregardedwithoutpenalty', 'qbehaviour_adaptive');
    }
}
