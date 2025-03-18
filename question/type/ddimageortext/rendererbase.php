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
        $question = $qa->get_question();
        $response = $qa->get_last_qt_data();

        $questiontext = $question->format_questiontext($qa);

        $dropareaclass = 'droparea';
        $draghomesclass = 'draghomes';
        if ($question->dropzonevisibility) {
            $draghomesclass .= ' transparent';
            $dropareaclass .= ' transparent';
        }
        if ($options->readonly) {
            $dropareaclass .= ' readonly';
            $draghomesclass .= ' readonly';
        }

        $output = html_writer::div($questiontext, 'qtext');

        $output .= html_writer::start_div('ddarea');
        $output .= html_writer::start_div($dropareaclass);
        $output .= html_writer::img(self::get_url_for_image($qa, 'bgimage'), get_string('dropbackground', 'qtype_ddmarker'),
                ['class' => 'dropbackground img-fluid w-100']);

        // Note, the mobile app implementation of ddimageortext relies on extracting the
        // blob of places data out of the rendered HTML, which makes it impossible
        // to clean up this structure of otherwise unnecessary stuff.
        $placeinfoforjsandmobileapp = [];
        foreach ($question->places as $placeno => $place) {
            $varname = $question->field($placeno);
            [$fieldname, $html] = $this->hidden_field_for_qt_var($qa, $varname, null,
                ['placeinput', 'place' . $placeno, 'group' . $place->group]);
            $output .= $html;
            $placeinfo = (object) (array) $place;
            $placeinfo->fieldname = $fieldname;
            $placeinfoforjsandmobileapp[$placeno] = $placeinfo;
        }

        $output .= html_writer::div('', 'dropzones', ['data-place-info' => json_encode($placeinfoforjsandmobileapp)]);
        $output .= html_writer::end_div();
        $output .= html_writer::start_div($draghomesclass);

        $dragimagehomes = '';
        foreach ($question->choices as $groupno => $group) {
            $dragimagehomesgroup = '';
            $orderedgroup = $question->get_ordered_choices($groupno);
            foreach ($orderedgroup as $choiceno => $dragimage) {
                $dragimageurl = self::get_url_for_image($qa, 'dragimage', $dragimage->id);
                $classes = [
                        'group' . $groupno,
                        'draghome',
                        'user-select-none',
                        'choice' . $choiceno
                ];
                if ($dragimage->infinite) {
                    $classes[] = 'infinite';
                }
                if ($dragimageurl === null) {
                    $dragimage->text = question_utils::format_question_fragment($dragimage->text, $this->page->context);
                    $dragimagehomesgroup .= html_writer::div($dragimage->text, join(' ', $classes), ['src' => $dragimageurl]);
                } else {
                    $dragimagehomesgroup .= html_writer::img($dragimageurl, $dragimage->text, ['class' => join(' ', $classes)]);
                }
            }
            $dragimagehomes .= html_writer::div($dragimagehomesgroup, 'dragitemgroup' . $groupno);
        }

        $output .= $dragimagehomes;
        $output .= html_writer::end_div();

        $output .= html_writer::end_div();

        $this->page->requires->string_for_js('blank', 'qtype_ddimageortext');
        $this->page->requires->js_call_amd('qtype_ddimageortext/question', 'init',
                [$qa->get_outer_question_div_unique_id(), $options->readonly]);

        if ($qa->get_state() == question_state::$invalid) {
            $output .= html_writer::div($question->get_validation_error($qa->get_last_qt_data()), 'validationerror');
        }
        return $output;
    }

    /**
     * Returns the URL for an image
     *
     * @param object $qa Question attempt object
     * @param string $filearea File area descriptor
     * @param int $itemid Item id to get
     * @return string Output url, or null if not found
     */
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

    /**
     * Returns a hidden field for a qt variable
     *
     * @param object $qa Question attempt object
     * @param string $varname The hidden var name
     * @param string $value The hidden value
     * @param array $classes Any additional css classes to apply
     * @return array Array with field name and the html of the tag
     */
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
