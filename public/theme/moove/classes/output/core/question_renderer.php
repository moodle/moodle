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
 * Renderers for outputting parts of the question engine.
 *
 * @subpackage questionengine
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_moove\output\core;

require_once($CFG->dirroot . "/question/engine/renderer.php");

use html_writer;
use question_attempt;
use qbehaviour_renderer;
use qtype_renderer;
use question_display_options;
use question_flags;
use moodle_url;

/**
 * This renderer controls the overall output of questions.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_renderer extends \core_question_renderer {
    /**
     * Generate the information bit of the question display that contains the
     * metadata like the question number, current state, and mark.
     * @param question_attempt $qa the question attempt to display.
     * @param qbehaviour_renderer $behaviouroutput the renderer to output the behaviour
     *      specific parts.
     * @param qtype_renderer $qtoutput the renderer to output the question type
     *      specific parts.
     * @param question_display_options $options controls what should and should not be displayed.
     * @param string|null $number The question number to display. 'i' is a special
     *      value that gets displayed as Information. Null means no number is displayed.
     * @return HTML fragment.
     */
    protected function info(
        question_attempt $qa,
        qbehaviour_renderer $behaviouroutput,
        qtype_renderer $qtoutput,
        question_display_options $options,
        $number
    ) {
        $output = '';
        $output .= '<div class="d-flex align-items-center flex-wrap mb-sm-2 mb-md-0">' .
            $this->number($number) .
            '<div class="d-inline-flex align-items-center flex-wrap">' .
            $this->status($qa, $behaviouroutput, $options) .
            $this->mark_summary($qa, $behaviouroutput, $options) .
            '</div></div>';
        $output .= '<div>' .
            $this->question_flag($qa, $options->flags) .
            $this->edit_question_link($qa, $options) .
            '</div>';
        return $output;
    }

    /**
     * Generate the display of the question number.
     * @param string|null $number The question number to display. 'i' is a special
     *      value that gets displayed as Information. Null means no number is displayed.
     * @return string fragment.
     */
    protected function number($number) {
        if (trim($number) === '') {
            return '';
        }

        $numbertext = get_string(
            'questionx',
            'question',
            html_writer::tag('span', $number, ['class' => 'rui-qno'])
        );

        if (trim($number) === 'i') {
            $numbertext = get_string('information', 'question');
        }

        return html_writer::tag('h4', $numbertext, ['class' => 'h3 w-100 mb-2']);
    }

    /**
     * Generate the display of the status line that gives the current state of
     * the question.
     * @param question_attempt $qa the question attempt to display.
     * @param qbehaviour_renderer $behaviouroutput the renderer to output the behaviour
     *      specific parts.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string fragment.
     */
    protected function status(
        question_attempt $qa,
        qbehaviour_renderer $behaviouroutput,
        question_display_options $options) {
        return html_writer::tag(
            'div',
            $qa->get_state_string($options->correctness),
            ['class' => 'state mr-2 my-2']
        );
    }

    /**
     * Render the question flag, assuming $flagsoption allows it.
     *
     * @param question_attempt $qa the question attempt to display.
     * @param int $flagsoption the option that says whether flags should be displayed.
     */
    protected function question_flag(question_attempt $qa, $flagsoption) {
        $divattributes = ['class' => 'questionflag mx-1 d-none'];

        switch ($flagsoption) {
            case question_display_options::VISIBLE:
                $flagcontent = $this->get_flag_html($qa->is_flagged());
                break;
            case question_display_options::EDITABLE:
                $id = $qa->get_flag_field_name();
                $checkboxattributes = [
                    'type' => 'checkbox',
                    'id' => $id . 'checkbox',
                    'name' => $id,
                    'value' => 1,
                ];
                if ($qa->is_flagged()) {
                    $checkboxattributes['checked'] = 'checked';
                }
                $postdata = question_flags::get_postdata($qa);

                $flagcontent = html_writer::empty_tag(
                    'input',
                    ['type' => 'hidden', 'name' => $id, 'value' => 0]
                );
                $flagcontent .= html_writer::empty_tag('input', $checkboxattributes);
                $flagcontent .= html_writer::empty_tag(
                    'input',
                    ['type' => 'hidden', 'value' => $postdata, 'class' => 'questionflagpostdata']
                );
                $flagcontent .= html_writer::tag(
                    'label',
                    $this->get_flag_html($qa->is_flagged(), $id . 'img'),
                    ['id' => $id . 'label', 'for' => $id . 'checkbox']
                );
                $flagcontent .= "\n";

                $divattributes = [
                    'class' => 'questionflag mb-sm-2 mb-md-0 mx-md-2 editable d-inline-flex',
                    'aria-atomic' => 'true',
                    'aria-relevant' => 'text',
                    'aria-live' => 'assertive',
                ];

                break;
            default:
                $flagcontent = '';
        }

        return html_writer::nonempty_tag('div', $flagcontent, $divattributes);
    }

    /**
     * Generate the display of the edit question link.
     *
     * @param question_attempt $qa The question attempt to display.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string
     */
    protected function edit_question_link(question_attempt $qa, question_display_options $options) {
        if (empty($options->editquestionparams)) {
            return '';
        }

        $params = $options->editquestionparams;
        if ($params['returnurl'] instanceof moodle_url) {
            $params['returnurl'] = $params['returnurl']->out_as_local_url(false);
        }
        $params['id'] = $qa->get_question_id();
        $editurl = new moodle_url('/question/bank/editquestion/question.php', $params);

        return html_writer::tag(
            'div',
            html_writer::link(
                $editurl, $this->pix_icon('t/edit', get_string('edit'), '', ['class' => 'iconsmall']) .
                get_string('editquestion', 'question'),
                ['class' => 'btn btn-sm btn-secondary ml-2']
            ),
            ['class' => 'editquestion']
        );
    }
}
