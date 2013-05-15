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
 * Combined question embedded sub question renderer class.
 *
 * @package    qtype_gapselect
 * @copyright  2013 The Open University
 * @author     Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


class qtype_gapselect_embedded_renderer extends qtype_renderer
    implements qtype_combined_subquestion_renderer_interface {

    protected function box_id(question_attempt $qa, $place) {
        return str_replace(':', '_', $qa->get_qt_field_name($place));
    }

    public function subquestion(question_attempt $qa,
                                question_display_options $options,
                                qtype_combined_combinable_base $subq,
                                $placeno) {
        $question = $subq->question;
        $place = $placeno + 1;
        $group = $question->places[$place];

        $fieldname = $subq->step_data_name($question->field($place));

        $value = $qa->get_last_qt_var($fieldname);

        $attributes = array(
            'id' => str_replace(':', '_', $qa->get_qt_field_name($fieldname)),
        );

        if ($options->readonly) {
            $attributes['disabled'] = 'disabled';
        }

        $orderedchoices = $question->get_ordered_choices($group);
        $selectoptions = array();
        foreach ($orderedchoices as $orderedchoicevalue => $orderedchoice) {
            $selectoptions[$orderedchoicevalue] = $orderedchoice->text;
        }

        $feedbackimage = '';
        if ($options->correctness) {
            $response = $qa->get_last_qt_data();
            if (array_key_exists($fieldname, $response)) {
                $fraction = (int) ($response[$fieldname] == $question->get_right_choice_for($place));
                $attributes['class'] = $this->feedback_class($fraction);
                $feedbackimage = $this->feedback_image($fraction);
            }
        }

        $selecthtml = html_writer::select($selectoptions, $qa->get_qt_field_name($fieldname),
                                          $value, get_string('choosedots'), $attributes) . ' ' . $feedbackimage;
        return html_writer::tag('span', $selecthtml, array('class' => 'control'));
    }
}
