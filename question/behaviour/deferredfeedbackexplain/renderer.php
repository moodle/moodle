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
 * Renderer for the deferred feedback with explanation behaviour.
 *
 * @package   qbehaviour_deferredfeedbackexplain
 * @copyright 2014 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_deferredfeedbackexplain_renderer extends qbehaviour_renderer {

    public function controls(question_attempt $qa, question_display_options $options): string {
        return html_writer::div(html_writer::div($this->explanation($qa, $options), 'answer'), 'ablock');
    }

    /**
     * Render the explanation as either a HTML editor, or read-only, as applicable.
     * @param question_attempt $qa a question attempt.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string HTML fragment.
     */
    protected function explanation(question_attempt $qa, question_display_options $options): string {
        $step = $qa->get_last_step_with_behaviour_var('explanation');

        if (empty($options->readonly)) {
            $answer = $this->explanation_input($qa, $step, $options->context);
        } else {
            $answer = $this->explanation_read_only($step);
        }

        return $answer;
    }

    /**
     * Render the explanation in read-only form.
     * @param question_attempt_step $step from which to get the current explanation.
     * @return string HTML fragment.
     */
    public function explanation_read_only(question_attempt_step $step): string {
        $output = html_writer::tag('p', get_string('pleaseexplain', 'qbehaviour_deferredfeedbackexplain'));

        if ($step->has_behaviour_var('explanation')) {
            $formatoptions = new stdClass();
            $formatoptions->para = false;
            $output .= html_writer::div(format_text($step->get_behaviour_var('explanation'),
                    $step->get_behaviour_var('explanationformat'), $formatoptions), 'explanation_readonly');
        }

        return $output;
    }

    /**
     * Render the explanation in a HTML editor.
     * @param question_attempt $qa a question attempt.
     * @param question_attempt_step $step from which to get the current explanation.
     * @param context $context context we are rendering in.
     * @return string HTML fragment.
     */
    public function explanation_input(question_attempt $qa,
            question_attempt_step $step, context $context): string {
        global $CFG;
        require_once($CFG->dirroot . '/repository/lib.php');

        $inputname = $qa->get_behaviour_field_name('explanation');
        $explanation = $step->get_behaviour_var('explanation');
        $explanationformat = $step->get_behaviour_var('explanationformat');
        $id = $inputname . '_id';

        $editor = editors_get_preferred_editor($explanationformat);
        $strformats = format_text_menu();
        $formats = $editor->get_supported_formats();
        foreach ($formats as $fid) {
            $formats[$fid] = $strformats[$fid];
        }

        $editor->use_editor($id, ['context' => $context, 'autosave' => false],
                ['return_types' => FILE_EXTERNAL]);

        $output = html_writer::tag('p', get_string('pleaseexplain', 'qbehaviour_deferredfeedbackexplain'));

        $output .= html_writer::div(html_writer::tag('textarea', s($explanation),
                ['id' => $id, 'name' => $inputname, 'rows' => 5, 'cols' => 60]));

        $output .= html_writer::start_div();
        if (count($formats) == 1) {
            reset($formats);
            $output .= html_writer::empty_tag('input', ['type' => 'hidden',
                    'name' => $inputname . 'format', 'value' => key($formats)]);

        } else {
            $output .= html_writer::label(get_string('format'), 'menu' . $inputname . 'format', false);
            $output .= ' ';
            $output .= html_writer::select($formats, $inputname . 'format', $explanationformat, '');
        }
        $output .= html_writer::end_div();
        return $output;
    }
}
