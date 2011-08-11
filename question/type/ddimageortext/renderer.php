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
 * Drag-and-drop words into sentences question renderer class.
 *
 * @package    qtype
 * @subpackage ddimagetoimage
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/gapselect/rendererbase.php');


/**
 * Generates the output for drag-and-drop words into sentences questions.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ddimagetoimage_renderer extends qtype_with_combined_feedback_renderer {


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

        $output = '';
        $bgimage = self::get_url_for_image($qa, 'bgimage');

        $img = html_writer::empty_tag('img', array('src'=>$bgimage, 'class'=>'dropbackground'));
        $droparea = html_writer::tag('div', $img, array('class'=>'droparea'));

        $dragimagehomes = '';
        foreach ($question->choices as $groupno => $group) {
            $dragimagehomesgroup = '';
            foreach ($group as $choiceno => $dragimage) {
                $filearea = qtype_ddimagetoimage::drag_image_file_area($dragimage->no - 1);
                $dragimageurl = self::get_url_for_image($qa, $filearea);
                $classes = array("group{$groupno}",
                                 'draghome',
                                 "dragimagehomes{$dragimage->no}",
                                 "choice{$choiceno}");
                if ($dragimage->isinfinite) {
                    $classes[] = 'infinite';
                }
                $dragimagehomesgroup .= html_writer::empty_tag('img',
                                            array('src'=>$dragimageurl,
                                                'class'=>join(' ', $classes)));
            }
            $dragimagehomes .= html_writer::tag('div', $dragimagehomesgroup,
                                            array('class'=>'dragitemgroup'.$groupno));
        }

        $dragitems = html_writer::tag('div', $dragimagehomes, array('class'=>'dragitems'));
        $dropzones = html_writer::empty_tag('div', array('class'=>'dropzones'));
        $output .= html_writer::tag('div', $droparea.$dragitems.$dropzones,
                                                                        array('class'=>'ddarea'));
        foreach ($question->places as $placeno => $place){
            $varname = $question->field($placeno);
            list($fieldname, $html) = $this->hidden_field_for_qt_var($qa, $varname);
            $output .= $html;
            $question->places[$placeno]->fieldname = $fieldname;
        }
        $jsmodule = array(
            'name'     => 'qtype_ddimagetoimage',
            'fullpath' => '/question/type/ddimagetoimage/module.js',
            'requires' => array('node', 'dd', 'dd-drop', 'dd-constrain')
        );

        $topnode = 'div#q'.$qa->get_slot().' div.ddarea';
        $sendtojs = array($question->places, $topnode, $options->readonly);

        $PAGE->requires->js_init_call('M.qtype_ddimagetoimage.init_question',
                                        $sendtojs,
                                        true,
                                        $jsmodule);
        return $output;
    }

    protected static function get_url_for_image(question_attempt $qa, $filearea) {
        $question = $qa->get_question();
        $qubaid = $qa->get_usage_id();
        $slot = $qa->get_slot();
        $fs = get_file_storage();
        $draftfiles = $fs->get_area_files($question->contextid, 'qtype_ddimagetoimage',
                                                                    $filearea, $question->id, 'id');
        if ($draftfiles) {
            foreach ($draftfiles as $file) {
                if ($file->is_directory()) {
                    continue;
                }
                $url = moodle_url::make_pluginfile_url($question->contextid, 'qtype_ddimagetoimage',
                                            $filearea, "$qubaid/$slot/{$question->id}", '/',
                                            $file->get_filename());
                return $url->out();
            }
        }
        throw new coding_exception('File not found in filearea '.$filearea);
    }

    protected function hidden_field_for_qt_var(question_attempt $qa, $varname, $value = null) {
        if ($value === null) {
            $value = $qa->get_last_qt_var($varname);
        }
        $fieldname = $qa->get_qt_field_name($varname);
        $attributes = array('type'=>'hidden',
                                'id' => str_replace(':', '_', $fieldname),
                                'name'=> $fieldname,
                                'value'=> $value);
        return array($fieldname, html_writer::empty_tag('input', $attributes)."\n");
    }

    public function specific_feedback(question_attempt $qa) {
        return $this->combined_feedback($qa);
    }

    public function correct_response(question_attempt $qa) {
        $question = $qa->get_question();

        $correctanswer = '';
        foreach ($question->places as $i => $place) {
            $choice = $question->choices[$place->group][$question->rightchoices[$i]];
            if ($choice->text != '') {
                $text = $choice->text;
            } else {
                $text = get_string('nolabel', 'qtype_ddimagetoimage');
            }
            $correctanswer .= '[' . str_replace('-', '&#x2011;', $text) . ']';
        }

        if (!empty($correctanswer)) {
            return get_string('correctansweris', 'qtype_gapselect', $correctanswer);
        }
    }
}
