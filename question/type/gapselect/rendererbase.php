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
 * Base class for rendering question types like this one.
 *
 * @package    qtype_gapselect
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Renders question types where the question includes embedded interactive elements.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_elements_embedded_in_question_text_renderer
        extends qtype_with_combined_feedback_renderer {
    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {

        $question = $qa->get_question();

        $questiontext = '';
        foreach ($question->textfragments as $i => $fragment) {
            if ($i > 0) {
                $questiontext .= $this->embedded_element($qa, $i, $options);
            }
            $questiontext .= $fragment;
        }

        $result = '';
        $result .= html_writer::tag('div', $question->format_text($questiontext,
                $question->questiontextformat, $qa, 'question', 'questiontext', $question->id),
                array('class' => $this->qtext_classname(), 'id' => $this->qtext_id($qa)));

        $result .= $this->post_qtext_elements($qa, $options);

        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div',
                    $question->get_validation_error($qa->get_last_qt_data()),
                    array('class' => 'validationerror'));
        }

        return $result;
    }

    protected function qtext_classname() {
        return 'qtext';
    }

    protected function qtext_id($qa) {
        return str_replace(':', '_', $qa->get_qt_field_name(''));
    }

    protected abstract function embedded_element(question_attempt $qa, $place,
            question_display_options $options);

    protected function post_qtext_elements(question_attempt $qa,
            question_display_options $options) {
        return '';
    }

    protected function box_id(question_attempt $qa, $place) {
        return str_replace(':', '_', $qa->get_qt_field_name($place));
    }

    public function specific_feedback(question_attempt $qa) {
        return $this->combined_feedback($qa);
    }

    public function correct_response(question_attempt $qa) {
        $question = $qa->get_question();

        $correctanswer = '';
        foreach ($question->textfragments as $i => $fragment) {
            if ($i > 0) {
                $group = $question->places[$i];
                $choice = $question->choices[$group][$question->rightchoices[$i]];
                $correctanswer .= '[' . str_replace('-', '&#x2011;',
                        $choice->text) . ']';
            }
            $correctanswer .= $fragment;
        }

        if (!empty($correctanswer)) {
            return get_string('correctansweris', 'qtype_gapselect',
                    $question->format_text($correctanswer, $question->questiontextformat,
                            $qa, 'question', 'questiontext', $question->id));
        }
    }
}
