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
 * Defines the renderer base class for question behaviours.
 *
 * @package    moodlecore
 * @subpackage questionbehaviours
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Renderer base class for question behaviours.
 *
 * The methods in this class are mostly called from {@link core_question_renderer}
 * which coordinates the overall output of questions.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qbehaviour_renderer extends plugin_renderer_base {
    /**
     * Generate some HTML (which may be blank) that appears in the question
     * formulation area, afer the question type generated output.
     *
     * For example.
     * immediatefeedback and interactive mode use this to show the Submit button,
     * and CBM use this to display the certainty choices.
     *
     * @param question_attempt $qa a question attempt.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string HTML fragment.
     */
    public function controls(question_attempt $qa, question_display_options $options) {
        return '';
    }

    /**
     * Generate some HTML (which may be blank) that appears in the outcome area,
     * after the question-type generated output.
     *
     * For example, the CBM models use this to display an explanation of the score
     * adjustment that was made based on the certainty selected.
     *
     * @param question_attempt $qa a question attempt.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string HTML fragment.
     */
    public function feedback(question_attempt $qa, question_display_options $options) {
        return '';
    }

    public function manual_comment_fields(question_attempt $qa, question_display_options $options) {
        global $CFG;

        require_once($CFG->dirroot.'/lib/filelib.php');
        require_once($CFG->dirroot.'/repository/lib.php');

        $inputname = $qa->get_behaviour_field_name('comment');
        $id = $inputname . '_id';
        list($commenttext, $commentformat, $commentstep) = $qa->get_current_manual_comment();

        $editor = editors_get_preferred_editor($commentformat);
        $strformats = format_text_menu();
        $formats = $editor->get_supported_formats();
        foreach ($formats as $fid) {
            $formats[$fid] = $strformats[$fid];
        }

        $draftitemareainputname = $qa->get_behaviour_field_name('comment:itemid');
        $draftitemid = optional_param($draftitemareainputname, false, PARAM_INT);

        if (!$draftitemid && $commentstep === null) {
            $commenttext = '';
            $draftitemid = file_get_unused_draft_itemid();
        } else if (!$draftitemid) {
            list($draftitemid, $commenttext) = $commentstep->prepare_response_files_draft_itemid_with_text(
                    'bf_comment', $options->context->id, $commenttext);
        }

        $editor->set_text($commenttext);
        $editor->use_editor($id, question_utils::get_editor_options($options->context),
                question_utils::get_filepicker_options($options->context, $draftitemid));

        $commenteditor = html_writer::tag('div', html_writer::tag('textarea', s($commenttext),
                array('id' => $id, 'name' => $inputname, 'rows' => 3, 'cols' => 60)));

        $attributes = ['type'  => 'hidden', 'name'  => $draftitemareainputname, 'value' => $draftitemid];
        $commenteditor .= html_writer::empty_tag('input', $attributes);

        $editorformat = '';
        if (count($formats) == 1) {
            reset($formats);
            $editorformat .= html_writer::empty_tag('input', array('type' => 'hidden',
                    'name' => $inputname . 'format', 'value' => key($formats)));
        } else {
            $editorformat = html_writer::start_tag('div', array('class' => 'fitem'));
            $editorformat .= html_writer::start_tag('div', array('class' => 'fitemtitle'));
            $editorformat .= html_writer::tag('label', get_string('format'), array('for'=>'menu'.$inputname.'format'));
            $editorformat .= html_writer::end_tag('div');
            $editorformat .= html_writer::start_tag('div', array('class' => 'felement fhtmleditor'));
            $editorformat .= html_writer::select($formats, $inputname.'format', $commentformat, '');
            $editorformat .= html_writer::end_tag('div');
            $editorformat .= html_writer::end_tag('div');
        }

        $comment = html_writer::tag('div', html_writer::tag('div',
                html_writer::tag('label', get_string('comment', 'question'),
                array('for' => $id)), array('class' => 'fitemtitle')) .
                html_writer::tag('div', $commenteditor, array('class' => 'felement fhtmleditor', 'data-fieldtype' => "editor")),
                array('class' => 'fitem'));
        $comment .= $editorformat;

        $mark = '';
        if ($qa->get_max_mark()) {
            $currentmark = $qa->get_current_manual_mark();
            $maxmark = $qa->get_max_mark();

            $fieldsize = strlen($qa->format_max_mark($options->markdp)) - 1;
            $markfield = $qa->get_behaviour_field_name('mark');

            $attributes = array(
                'type' => 'text',
                'size' => $fieldsize,
                'name' => $markfield,
                'id'=> $markfield
            );
            if (!is_null($currentmark)) {
                $attributes['value'] = $currentmark;
            }

            $markrange = html_writer::empty_tag('input', array(
                'type' => 'hidden',
                'name' => $qa->get_behaviour_field_name('maxmark'),
                'value' => $maxmark,
            )) . html_writer::empty_tag('input', array(
                'type' => 'hidden',
                'name' => $qa->get_control_field_name('minfraction'),
                'value' => $qa->get_min_fraction(),
            )) . html_writer::empty_tag('input', array(
                'type' => 'hidden',
                'name' => $qa->get_control_field_name('maxfraction'),
                'value' => $qa->get_max_fraction(),
            ));

            $error = $qa->validate_manual_mark($currentmark);
            $errorclass = '';
            if ($error !== '') {
                $erroclass = ' error';
                $error = html_writer::tag('span', $error,
                        array('class' => 'error')) . html_writer::empty_tag('br');
            }

            $a = new stdClass();
            $a->max = $qa->format_max_mark($options->markdp);
            $a->mark = html_writer::empty_tag('input', $attributes);
            $mark = html_writer::tag('div', html_writer::tag('div',
                        html_writer::tag('label', get_string('mark', 'question'),
                        array('for' => $markfield)),
                    array('class' => 'fitemtitle')) .
                    html_writer::tag('div', $error . get_string('xoutofmax', 'question', $a) .
                        $markrange, array('class' => 'felement ftext' . $errorclass)
                    ), array('class' => 'fitem'));
        }

        return html_writer::tag('fieldset', html_writer::tag('div', $comment . $mark,
                array('class' => 'fcontainer clearfix')), array('class' => 'hidden'));
    }

    public function manual_comment_view(question_attempt $qa, question_display_options $options) {
        $output = '';
        if ($qa->has_manual_comment()) {
            $output .= get_string('commentx', 'question',
                    $qa->get_behaviour(false)->format_comment(null, null, $options->context));
        }
        if ($options->manualcommentlink) {
            $url = new moodle_url($options->manualcommentlink, array('slot' => $qa->get_slot()));
            $link = $this->output->action_link($url, get_string('commentormark', 'question'),
                    new popup_action('click', $url, 'commentquestion',
                    array('width' => 600, 'height' => 800)));
            $output .= html_writer::tag('div', $link, array('class' => 'commentlink'));
        }
        return $output;
    }

    /**
     * Display the manual comment, and a link to edit it, if appropriate.
     *
     * @param question_attempt $qa a question attempt.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string HTML fragment.
     */
    public function manual_comment(question_attempt $qa, question_display_options $options) {
        if ($options->manualcomment == question_display_options::EDITABLE) {
            return $this->manual_comment_fields($qa, $options);

        } else if ($options->manualcomment == question_display_options::VISIBLE) {
            return $this->manual_comment_view($qa, $options);

        } else {
            return '';
        }
    }

    /**
     * Several behaviours need a submit button, so put the common code here.
     * The button is disabled if the question is displayed read-only.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string HTML fragment.
     */
    protected function submit_button(question_attempt $qa, question_display_options $options) {
        if (!$qa->get_state()->is_active()) {
            return '';
        }
        $attributes = array(
            'type' => 'submit',
            'id' => $qa->get_behaviour_field_name('submit'),
            'name' => $qa->get_behaviour_field_name('submit'),
            'value' => get_string('check', 'question'),
            'class' => 'submit btn btn-secondary',
        );
        if ($options->readonly) {
            $attributes['disabled'] = 'disabled';
        }
        $output = html_writer::empty_tag('input', $attributes);
        if (!$options->readonly) {
            $this->page->requires->js_init_call('M.core_question_engine.init_submit_button',
                    array($attributes['id']));
        }
        return $output;
    }

    /**
     * Return any HTML that needs to be included in the page's <head> when
     * questions using this model are used.
     * @param $qa the question attempt that will be displayed on the page.
     * @return string HTML fragment.
     */
    public function head_code(question_attempt $qa) {
        return '';
    }

    /**
     * Generate the display of the marks for this question.
     * @param question_attempt $qa the question attempt to display.
     * @param core_question_renderer $qoutput the renderer for standard parts of questions.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return HTML fragment.
     */
    public function mark_summary(question_attempt $qa, core_question_renderer $qoutput,
            question_display_options $options) {
        return $qoutput->standard_mark_summary($qa, $this, $options);
    }

    /**
     * Generate the display of the available marks for this question.
     * @param question_attempt $qa the question attempt to display.
     * @param core_question_renderer $qoutput the renderer for standard parts of questions.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return HTML fragment.
     */
    public function marked_out_of_max(question_attempt $qa, core_question_renderer $qoutput,
            question_display_options $options) {
        return $qoutput->standard_marked_out_of_max($qa, $options);
    }

    /**
     * Generate the display of the marks for this question out of the available marks.
     * @param question_attempt $qa the question attempt to display.
     * @param core_question_renderer $qoutput the renderer for standard parts of questions.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return HTML fragment.
     */
    public function mark_out_of_max(question_attempt $qa, core_question_renderer $qoutput,
            question_display_options $options) {
        return $qoutput->standard_mark_out_of_max($qa, $options);
    }
}
