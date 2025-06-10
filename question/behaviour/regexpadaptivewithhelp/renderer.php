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
 * Renderer for outputting parts of a question belonging to the regexp (with help) behaviour.
 *
 * @package    qbehaviour_regexpadaptivewithhelp
 * @subpackage regexp
 * @copyright  2011 Tim Hunt & Joseph R�zeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../adaptive/renderer.php');

/**
 * Renderer for outputting parts of a question belonging to the legacy adaptive behaviour.
 */
class qbehaviour_regexpadaptivewithhelp_renderer extends qbehaviour_adaptive_renderer {
    /**
     * Get graded step.
     * @param question_attempt $qa a question attempt.
     */
    protected function get_graded_step(question_attempt $qa) {
        foreach ($qa->get_reverse_step_iterator() as $step) {
            if ($step->has_behaviour_var('_try')) {
                return $step;
            }
        }
    }

    /**
     * Moved help strings to this function to get appropriate strings for the nopenalty behaviour.
     */
    public function help_msg() {
        $helptexts = [];
        $helptexts[1] = get_string('buyletter', 'qbehaviour_regexpadaptivewithhelp');
        $helptexts[2] = get_string('buyword', 'qbehaviour_regexpadaptivewithhelp');
        $helptexts[3] = get_string('buywordorpunctuation', 'qbehaviour_regexpadaptivewithhelp');
        return $helptexts;
    }
    /**
     * Display the "Help" button.
     * @param question_attempt $qa a question attempt.
     * @param question_display_options $options controls what should and should not be displayed.
     * @param text $helptext the help text.
     */
    public function controls(question_attempt $qa, question_display_options $options, $helptext='') {
        // If student's answer is no longer improvable, then there's no point enabling the hint button.
        $isimprovable = $qa->get_behaviour()->is_state_improvable($qa->get_state());
        $output = $this->submit_button($qa, $options).'&nbsp;';
        $helpmode = $qa->get_question()->usehint;

        // JR DEC 2020 Do not display the Help button if it has just been clicked for help!
        $helprequested = false;
        $gradedstep = $this->get_graded_step($qa);
        $response = $qa->get_last_qt_data();
        $question = $qa->get_question(false);
        $closest = $question->closest;

        if ($gradedstep && $gradedstep->has_behaviour_var('_helps') ) {
            $helprequested = true;
        }

        // Check if error consists of misplaced words ONLY, no wrong words.
        $overflow = false;
        if ($closest && $response) {
            $t = true;
            $guesserrors = $closest[5];
            if ($guesserrors == 11) {
                $t = false;
            }
            if (($response['answer'] > $closest[0]) && $t) {
                $overflow = true;
            }
        }

        // Do NOT display the Help (word or letter) button in those cases.
        if ($helpmode == 0 || $options->readonly || !$isimprovable || $helprequested === true || $overflow) {
            return $output;
        }
        $helptext = $this->help_msg()[$helpmode];
        // JR dec 2020 added btn-secondary class for button display similar to default question check button.
        $attributes = [
            'type' => 'submit',
            'id' => $qa->get_behaviour_field_name('helpme'),
            'name' => $qa->get_behaviour_field_name('helpme'),
            'value' => $helptext,
            'class' => 'submit btn btn-secondary',
        ];

        $attributes['round'] = true;
        $output .= html_writer::empty_tag('input', $attributes);
        return $output;
    }

    /**
     * Display the extra help for the student, if it was requested.
     * @param question_attempt $qa a question attempt.
     * @param question_display_options $options controls what should and should not be displayed.
     */
    public function extra_help(question_attempt $qa, question_display_options $options) {
        return html_writer::nonempty_tag('div', $qa->get_behaviour()->get_extra_help_if_requested($options->markdp));
    }
    /**
     * Display feedback.
     * @param question_attempt $qa a question attempt.
     * @param question_display_options $options controls what should and should not be displayed.
     */
    public function feedback(question_attempt $qa, question_display_options $options) {
        // Try to find the last graded step.
        $gradedstep = $this->get_graded_step($qa);
        if ($gradedstep) {
            if ($gradedstep->has_behaviour_var('_helps') ) {
                return $this->extra_help($qa, $options);
            }
        }
        if (is_null($gradedstep) || $qa->get_max_mark() == 0 ||
                $options->marks < question_display_options::MARK_AND_MAX) {
            return '';
        }

        // Let student know wether the answer was correct.
        if ($qa->get_state()->is_commented()) {
            $class = $qa->get_state()->get_feedback_class();
        } else {
            $class = question_state::graded_state_for_fraction(
                    $gradedstep->get_behaviour_var('_rawfraction'))->get_feedback_class();
        }
        $penalty = $qa->get_question()->penalty;
        $gradingdetails = '';
        if ($penalty != 0) {
            $gradingdetails = $this->render_adaptive_marks(
                $qa->get_behaviour()->get_adaptive_marks(), $options);
        }
        $output = '';
        $output .= html_writer::tag('div', $gradingdetails,
                ['class' => 'gradingdetails']);

        return $output;
    }

}
