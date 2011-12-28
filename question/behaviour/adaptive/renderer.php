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
        if ($qa->get_state() == question_state::$invalid) {
            // If the latest answer was invalid, display an informative message
            $output = '';
            $info = $this->disregarded_info();
            if ($info) {
                $output = html_writer::tag('div', $info, array('class' => 'gradingdetails'));
            }
            return $output;
        }

        // Try to find the last graded step.

        $gradedstep = $qa->get_behaviour()->get_graded_step($qa);
        if (is_null($gradedstep) || $qa->get_max_mark() == 0 ||
                $options->marks < question_display_options::MARK_AND_MAX) {
            return '';
        }

        // Display the grading details from the last graded state
        $mark = new stdClass();
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

        $gradingdetails .= $this->penalty_info($qa, $mark, $options);

        $output = '';
        $output .= html_writer::tag('div', get_string($class, 'question'),
                array('class' => 'correctness ' . $class));
        $output .= html_writer::tag('div', $gradingdetails,
                array('class' => 'gradingdetails'));
        return $output;
    }

    /**
     * Display the information about the penalty calculations.
     * @param question_attempt $qa the question attempt.
     * @param object $mark contains information about the current mark.
     * @param question_display_options $options display options.
     */
    protected function penalty_info(question_attempt $qa, $mark,
            question_display_options $options) {

        $currentpenalty = $qa->get_question()->penalty * $qa->get_max_mark();
        if ($currentpenalty == 0) {
            return '';
        }
        $output = '';

        // Print details of grade adjustment due to penalties
        if ($mark->raw != $mark->cur) {
            $output .= ' ' . get_string('gradingdetailsadjustment', 'qbehaviour_adaptive', $mark);
        }

        // Print information about any new penalty, only relevant if the answer can be improved.
        if ($qa->get_behaviour()->is_state_improvable($qa->get_state())) {
            $output .= ' ' . get_string('gradingdetailspenalty', 'qbehaviour_adaptive',
                    format_float($currentpenalty, $options->markdp));
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
