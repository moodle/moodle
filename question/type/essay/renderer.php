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
 * Essay question renderer class.
 *
 * @package    qtype
 * @subpackage essay
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Generates the output for essay questions.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essay_renderer extends qtype_renderer {
    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {

        $question = $qa->get_question();
        $responseoutput = $question->get_format_renderer($this->page);

        // Answer field.
        $inputname = $qa->get_qt_field_name('answer');
        $response = $qa->get_last_qt_var('answer', '');
        if (empty($options->readonly)) {
            $answer = $responseoutput->response_area_input($inputname,
                    $response, $question->responsefieldlines);

        } else {
            $answer =$responseoutput->response_area_read_only($inputname,
                    $response, $question->responsefieldlines);
        }

        $files = '';
        if (empty($options->readonly)) {
            if ($question->attachments == 1) {
                $files = $this->filepicker_input($qa, $options);
            } else if ($question->attachments != 0) {
                $files = $this->filemanager_input($qa, $options);
            }

        } else if ($question->attachments != 0) {
            $files = $this->files_read_only($qa, $options);
        }

        $result = '';
        $result .= html_writer::tag('div', $question->format_questiontext($qa),
                array('class' => 'qtext'));

        $result .= html_writer::start_tag('div', array('class' => 'ablock'));
        $result .= html_writer::tag('div', $answer, array('class' => 'answer'));
        $result .= html_writer::tag('div', $files, array('class' => 'attachments'));
        $result .= html_writer::end_tag('div');

        return $result;
    }

    /**
     * Displays any attached files when the question is in read-only mode.
     */
    public function files_read_only(question_attempt $qa, question_display_options $options) {
        $files = $qa->get_last_qt_files('attachments', $options->context->id);
        $output = array();

        foreach ($files as $file) {
            $mimetype = $file->get_mimetype();
            $output[] = html_writer::tag('p', html_writer::link($qa->get_response_file_url($file),
                    $this->output->pix_icon(file_mimetype_icon($mimetype), $mimetype,
                    'moodle', array('class' => 'icon')) . ' ' . s($file->get_filename())));
        }
        return implode($output);
    }

    /**
     * Displays the input control for when the student should upload a single file.
     */
    public function filepicker_input(question_attempt $qa, question_display_options $options) {
        global $CFG;
        require_once($CFG->dirroot . '/lib/form/filemanager.php');

        $pickeroptions = new stdClass();
        $pickeroptions->mainfile = null;
        $pickeroptions->maxfiles = 1;
        $pickeroptions->itemid = $qa->prepare_response_files_draft_itemid(
                'attachments', $options->context->id);
        $pickeroptions->context = $options->context;

        return form_filemanager_render($pickeroptions);
    }

    /**
     * Displays the input control for when the student should upload a number of files.
     */
    public function filemanager_input(question_attempt $qa, question_display_options $options) {
        return '';
    }
}


/**
 * A base class to abstract out the differences between different type of
 * response format.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_essay_format_renderer_base extends plugin_renderer_base {
    /**
     * Render the students respone when the question is in read-only mode.
     * @param string $inputname the field name to use for this input.
     * @param string $response the student's current response.
     * @param int $lines approximate size of input box to display.
     */
    public abstract function response_area_read_only($inputname, $response, $lines);

    /**
     * Render the students respone when the question is in read-only mode.
     * @param string $inputname the field name to use for this input.
     * @param string $response the student's current response.
     * @param int $lines approximate size of input box to display.
     */
    public abstract function response_area_input($inputname, $response, $lines);
}


/**
 * An essay format renderer for essays where the student should use the HTML
 * editor without the file picker.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essay_format_editor_renderer extends plugin_renderer_base {
    public function response_area_read_only($inputname, $response, $lines) {
        $formatoptions = new stdClass();
        $formatoptions->para = false;
        return html_writer::tag('div', format_text($response, FORMAT_HTML, $formatoptions),
                array('class' => 'qtype_essay_editor qtype_essay_response'));
    }

    public function response_area_input($inputname, $response, $lines) {
        return print_textarea(can_use_html_editor(), 18, 80, 630, 400, $inputname, $response, 0, true);
    }
}


/**
 * An essay format renderer for essays where the student should use the HTML
 * editor with the file picker.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essay_format_editorfilepicker_renderer extends plugin_renderer_base {
    public function response_area_read_only($inputname, $response, $lines) {
        return 'Not yet implemented.';
    }

    public function response_area_input($inputname, $response, $lines) {
        return 'Not yet implemented.';
    }
}


/**
 * An essay format renderer for essays where the student should use a plain
 * input box, but with a normal, proportional font.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essay_format_plain_renderer extends plugin_renderer_base {
    /**
     * @return string the HTML for the textarea.
     */
    protected function textarea($response, $lines, $attributes) {
        $attributes['class'] = $this->class_name() . ' qtype_essay_response';
        $attributes['rows'] = $lines;
        return html_writer::tag('textarea', s($response), $attributes);
    }

    protected function class_name() {
        return 'qtype_essay_plain';
    }

    public function response_area_read_only($inputname, $response, $lines) {
        return $this->textarea($response, $lines, array('readonly' => 'readonly'));
    }

    public function response_area_input($inputname, $response, $lines) {
        return $this->textarea($response, $lines, array('name' => $inputname));
    }
}


/**
 * An essay format renderer for essays where the student should use a plain
 * input box with a monospaced font. You might use this, for example, for a
 * question where the students should type computer code.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_essay_format_monospaced_renderer extends qtype_essay_format_plain_renderer {
    protected function class_name() {
        return 'qtype_essay_monospaced';
    }
}
