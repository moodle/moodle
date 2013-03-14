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
 * @subpackage ddwtos
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
class qtype_ddwtos_renderer extends qtype_elements_embedded_in_question_text_renderer {

    protected function qtext_classname() {
        return 'qtext ddwtos_questionid_for_javascript';
    }

    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {
        global $PAGE;

        $result = parent::formulation_and_controls($qa, $options);

        $inputids = array();
        $question = $qa->get_question();
        foreach ($question->places as $placeno => $place) {
            $inputids[$placeno] = $this->box_id($qa, $question->field($placeno));
        }

        $params = array(
            'inputids' => $inputids,
            'topnode' => 'div.que.ddwtos#q' . $qa->get_slot(),
            'readonly' => $options->readonly
        );

        $PAGE->requires->yui_module('moodle-qtype_ddwtos-dd',
                'M.qtype_ddwtos.init_question', array($params));

        return $result;
    }

    protected function post_qtext_elements(question_attempt $qa,
            question_display_options $options) {
        $result = '';
        $question = $qa->get_question();

        $dragboxs = '';
        foreach ($question->choices as $group => $choices) {
            $dragboxs .= $this->drag_boxes($qa, $group,
                    $question->get_ordered_choices($group), $options);
        }

        $classes = array('answercontainer');
        if (!$options->readonly) {
            $classes[] = 'notreadonly';
        } else {
            $classes[] = 'readonly';
        }
        $result .= html_writer::tag('div', $dragboxs, array('class' => implode(' ', $classes)));

        $classes = array('drags');
        if (!$options->readonly) {
            $classes[] = 'notreadonly';
        } else {
            $classes[] = 'readonly';
        }
        $result .= html_writer::tag('div', '', array('class' => implode(' ', $classes)));

        // We abuse the clear_wrong method to output the hidden form fields we
        // want irrespective of whether we are actually clearing the wrong
        // bits of the response.
        if (!$options->clearwrong) {
            $result .= $this->clear_wrong($qa, false);
        }
        return $result;
    }

    protected function embedded_element(question_attempt $qa, $place,
            question_display_options $options) {
        $question = $qa->get_question();
        $group = $question->places[$place];
        $boxcontents = '&#160;';

        $value = $qa->get_last_qt_var($question->field($place));

        $attributes = array(
            'class' => 'place' . $place . ' drop group' . $group
        );

        if ($options->readonly) {
            $attributes['class'] .= ' readonly';
        } else {
            $attributes['tabindex'] = '0';
        }

        $feedbackimage = '';
        if ($options->correctness) {
            $response = $qa->get_last_qt_data();
            $fieldname = $question->field($place);
            if (array_key_exists($fieldname, $response)) {
                $fraction = (int) ($response[$fieldname] ==
                        $question->get_right_choice_for($place));
                $feedbackimage = $this->feedback_image($fraction);
            }
        }

        return html_writer::tag('span', $boxcontents, $attributes) . ' ' . $feedbackimage;
    }

    protected function drag_boxes($qa, $group, $choices, question_display_options $options) {
        $boxes = '';
        foreach ($choices as $key => $choice) {
            // Bug 8632: long text entry causes bug in drag and drop field in IE.
            $content = str_replace('-', '&#x2011;', $choice->text);
            $content = str_replace(' ', '&#160;', $content);

            $infinite = '';
            if ($choice->infinite) {
                $infinite = ' infinite';
            }

            $boxes .= html_writer::tag('span', $content, array(
                    'class' => 'draghome choice' . $key . ' group' .
                            $choice->draggroup . $infinite)) . ' ';
        }

        return html_writer::nonempty_tag('div', $boxes,
                array('class' => 'draggrouphomes' . $choice->draggroup));
    }

    /**
     * Actually, this question type abuses this method to always ouptut the
     * hidden fields it needs.
     */
    public function clear_wrong(question_attempt $qa, $reallyclear = true) {
        $question = $qa->get_question();
        $response = $qa->get_last_qt_data();

        if (!empty($response) && $reallyclear) {
            $cleanresponse = $question->clear_wrong_from_response($response);
        } else {
            $cleanresponse = $response;
        }

        $output = '';
        foreach ($question->places as $place => $group) {
            $fieldname = $question->field($place);
            if (array_key_exists($fieldname, $response)) {
                $value = $response[$fieldname];
            } else {
                $value = '0';
            }
            if (array_key_exists($fieldname, $cleanresponse)) {
                $cleanvalue = $cleanresponse[$fieldname];
            } else {
                $cleanvalue = '0';
            }
            if ($cleanvalue != $value) {
                $output .= html_writer::empty_tag('input', array(
                        'type' => 'hidden',
                        'id' => $this->box_id($qa, 'p' . $place),
                        'value' => s($value))) .
                        html_writer::empty_tag('input', array(
                        'type' => 'hidden',
                        'name' => $qa->get_qt_field_name($fieldname),
                        'value' => s($cleanvalue)));
            } else {
                $output .= html_writer::empty_tag('input', array(
                        'type' => 'hidden',
                        'id' => $this->box_id($qa, 'p' . $place),
                        'name' => $qa->get_qt_field_name($fieldname),
                        'value' => s($value)));
            }
        }
        return $output;
    }

}
