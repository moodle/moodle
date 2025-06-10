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
 * Select from drop down list question renderer class.
 *
 * @package    qtype_gapselect
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/gapselect/rendererbase.php');


/**
 * Generates the output for select missing words questions.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_gapselect_renderer extends qtype_elements_embedded_in_question_text_renderer {
    protected function embedded_element(question_attempt $qa, $place,
            question_display_options $options) {
        $question = $qa->get_question();
        $group = $question->places[$place];

        $fieldname = $question->field($place);

        $value = $qa->get_last_qt_var($question->field($place));

        $attributes = [
            'id' => $this->box_id($qa, 'p' . $place),
            'class' => 'custom-select place' . $place,
        ];
        $groupclass = 'group' . $group;

        if ($options->readonly) {
            $attributes['disabled'] = 'disabled';
        }

        $orderedchoices = $question->get_ordered_choices($group);
        $selectoptions = [];
        foreach ($orderedchoices as $orderedchoicevalue => $orderedchoice) {
            $selectoptions[$orderedchoicevalue] = format_string($orderedchoice->text);
        }

        $feedbackimage = '';
        if ($options->correctness) {
            $response = $qa->get_last_qt_data();
            if (array_key_exists($fieldname, $response)) {
                $fraction = (int) ($response[$fieldname] ==
                        $question->get_right_choice_for($place));
                $attributes['class'] .= ' ' . $this->feedback_class($fraction);
                $feedbackimage = $this->feedback_image($fraction);
            }
        }

        $label = $options->add_question_identifier_to_label(get_string('blanknumber', 'qtype_gapselect', $place));
        // Use non-breaking space instead of 'Choose...'.
        $selecthtml = html_writer::label($label, $attributes['id'], false, ['class' => 'sr-only']);
        $selecthtml .= html_writer::select($selectoptions, $qa->get_qt_field_name($fieldname),
                        $value, '&nbsp;', $attributes) . ' ' . $feedbackimage;
        return html_writer::tag('span', $selecthtml, ['class' => 'control '.$groupclass]);
    }

}
