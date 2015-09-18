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
 * Drag-and-drop onto image question renderer class.
 *
 * @package    qtype_ddimageortext
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

/**
 * Generates the output for drag-and-drop onto image questions.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddtoimage_renderer_base extends qtype_with_combined_feedback_renderer {

    public function clear_wrong(question_attempt $qa) {
        $question = $qa->get_question();
        $response = $qa->get_last_qt_data();

        if (!empty($response)) {
            $cleanresponse = $question->clear_wrong_from_response($response);
        } else {
            $cleanresponse = $response;
        }
        $cleanresponsehtml = '';
        foreach ($cleanresponse as $fieldname => $value) {
            list (, $html) = $this->hidden_field_for_qt_var($qa, $fieldname, $value);
            $cleanresponsehtml .= $html;
        }
        return $cleanresponsehtml;
    }

    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {
        global $PAGE;

        $question = $qa->get_question();
        $response = $qa->get_last_qt_data();

        $questiontext = $question->format_questiontext($qa);

        $output = html_writer::tag('div', $questiontext, array('class' => 'qtext'));

        $bgimage = self::get_url_for_image($qa, 'bgimage');

        $img = html_writer::empty_tag('img', array(
                'src' => $bgimage, 'class' => 'dropbackground',
                'alt' => get_string('dropbackground', 'qtype_ddimageortext')));

        $droparea = html_writer::tag('div', $img, array('class' => 'droparea'));

        $dragimagehomes = '';
        foreach ($question->choices as $groupno => $group) {
            $dragimagehomesgroup = '';
            $orderedgroup = $question->get_ordered_choices($groupno);
            foreach ($orderedgroup as $choiceno => $dragimage) {
                $dragimageurl = self::get_url_for_image($qa, 'dragimage', $dragimage->id);
                $classes = array("group{$groupno}",
                                 'draghome',
                                 "dragitemhomes{$dragimage->no}",
                                 "choice{$choiceno}");
                if ($dragimage->infinite) {
                    $classes[] = 'infinite';
                }
                if ($dragimageurl === null) {
                    $classes[] = 'yui3-cssfonts';
                    $dragimagehomesgroup .= html_writer::tag('div', $dragimage->text,
                            array('src' => $dragimageurl, 'class' => join(' ', $classes)));
                } else {
                    $dragimagehomesgroup .= html_writer::empty_tag('img',
                            array('src' => $dragimageurl, 'alt' => $dragimage->text,
                                    'class' => join(' ', $classes)));
                }
            }
            $dragimagehomes .= html_writer::tag('div', $dragimagehomesgroup,
                    array('class' => 'dragitemgroup' . $groupno));
        }

        $dragitemsclass = 'dragitems';
        if ($options->readonly) {
            $dragitemsclass .= ' readonly';
        }
        $dragitems = html_writer::tag('div', $dragimagehomes, array('class' => $dragitemsclass));
        $dropzones = html_writer::tag('div', '', array('class' => 'dropzones'));

        $hiddens = '';
        foreach ($question->places as $placeno => $place) {
            $varname = $question->field($placeno);
            list($fieldname, $html) = $this->hidden_field_for_qt_var($qa, $varname);
            $hiddens .= $html;
            $question->places[$placeno]->fieldname = $fieldname;
        }
        $output .= html_writer::tag('div',
                $droparea . $dragitems . $dropzones . $hiddens, array('class' => 'ddarea'));
        $topnode = 'div#q'.$qa->get_slot().' div.ddarea';
        $params = array('drops' => $question->places,
                        'topnode' => $topnode,
                        'readonly' => $options->readonly);

        $PAGE->requires->yui_module('moodle-qtype_ddimageortext-dd',
                                        'M.qtype_ddimageortext.init_question',
                                        array($params));

        if ($qa->get_state() == question_state::$invalid) {
            $output .= html_writer::nonempty_tag('div',
                                        $question->get_validation_error($qa->get_last_qt_data()),
                                        array('class' => 'validationerror'));
        }
        return $output;
    }

    protected static function get_url_for_image(question_attempt $qa, $filearea, $itemid = 0) {
        $question = $qa->get_question();
        $qubaid = $qa->get_usage_id();
        $slot = $qa->get_slot();
        $fs = get_file_storage();
        if ($filearea == 'bgimage') {
            $itemid = $question->id;
        }
        $componentname = $question->qtype->plugin_name();
        $draftfiles = $fs->get_area_files($question->contextid, $componentname,
                                                                        $filearea, $itemid, 'id');
        if ($draftfiles) {
            foreach ($draftfiles as $file) {
                if ($file->is_directory()) {
                    continue;
                }
                $url = moodle_url::make_pluginfile_url($question->contextid, $componentname,
                                            $filearea, "$qubaid/$slot/{$itemid}", '/',
                                            $file->get_filename());
                return $url->out();
            }
        }
        return null;
    }

    protected function hidden_field_for_qt_var(question_attempt $qa, $varname, $value = null,
                                                $classes = null) {
        if ($value === null) {
            $value = $qa->get_last_qt_var($varname);
        }
        $fieldname = $qa->get_qt_field_name($varname);
        $attributes = array('type' => 'hidden',
                                'id' => str_replace(':', '_', $fieldname),
                                'name' => $fieldname,
                                'value' => $value);
        if ($classes !== null) {
            $attributes['class'] = join(' ', $classes);
        }
        return array($fieldname, html_writer::empty_tag('input', $attributes)."\n");
    }

    public function specific_feedback(question_attempt $qa) {
        return $this->combined_feedback($qa);
    }

    public function correct_response(question_attempt $qa) {
        return '';
    }
}
