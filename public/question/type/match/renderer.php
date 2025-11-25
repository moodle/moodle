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
 * Matching question renderer class.
 *
 * @package   qtype_match
 * @copyright 2009 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Generates the output for matching questions.
 *
 * @copyright 2009 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_match_renderer extends qtype_with_combined_feedback_renderer {

    public function formulation_and_controls(question_attempt $qa,
            question_display_options $options) {

        $question = $qa->get_question();
        $stemorder = $question->get_stem_order();
        $response = $qa->get_last_qt_data();

        $choices = $this->format_choices($question);

        $questiontextid = $qa->get_qt_field_name('qtext');
        $result = html_writer::div($question->format_questiontext($qa), 'qtext', ['id' => $questiontextid]);

        $result .= html_writer::start_tag('div', ['class' => 'ablock']);
        $result .= html_writer::start_tag('table', ['class' => 'answer table-reboot', 'role' => 'presentation']);
        $result .= html_writer::start_tag('tbody', ['role' => 'presentation']);

        $parity = 0;
        $i = 1;
        foreach ($stemorder as $key => $stemid) {

            $result .= html_writer::start_tag('tr', ['class' => 'r' . $parity, 'role' => 'presentation']);
            $fieldname = 'sub' . $key;

            $itemtextid = $qa->get_qt_field_name($fieldname . '_itemtext');
            $result .= html_writer::tag('td', $this->format_stem_text($qa, $stemid), [
                'class' => 'text',
                'id' => $itemtextid,
                'role' => 'presentation',
            ]);

            $classes = 'control';
            $feedbackimage = '';

            if (array_key_exists($fieldname, $response)) {
                $selected = $response[$fieldname];
            } else {
                $selected = 0;
            }

            $fraction = (int) ($selected && $selected == $question->get_right_choice_for($stemid));

            if ($options->correctness && $selected) {
                $classes .= ' ' . $this->feedback_class($fraction);
                $feedbackimage = $this->feedback_image($fraction);
            }

            // We only want to add the question text to the first answer field to
            // avoid repetition of the question text for the subsequent answer fields.
            if ($i == 1) {
                $ariadescribedbyids = $questiontextid . ' ' . $itemtextid;
            } else {
                $ariadescribedbyids = $itemtextid;
            }

            $labeltext = $options->add_question_identifier_to_label(get_string('answer', 'qtype_match', $i));
            $selectlabel = html_writer::label($labeltext, 'menu' . $qa->get_qt_field_name($fieldname), false, [
                'class' => 'visually-hidden',
            ]);
            $select = html_writer::select(
                $choices,
                $qa->get_qt_field_name($fieldname),
                $selected,
                ['0' => 'choose'],
                [
                    'disabled' => $options->readonly,
                    'class' => 'form-select d-inline-block ms-1',
                    'aria-describedby' => $ariadescribedbyids,
                ]
            );
            $result .= html_writer::tag('td', $selectlabel . $select . ' ' . $feedbackimage, [
                'class' => $classes,
                'role' => 'presentation',
            ]);

            $result .= html_writer::end_tag('tr');
            $parity = 1 - $parity;
            $i++;
        }
        $result .= html_writer::end_tag('tbody');
        $result .= html_writer::end_tag('table');

        $result .= html_writer::end_tag('div'); // Closes <div class="ablock">.

        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div', $question->get_validation_error($response), ['class' => 'validationerror']);
        }

        return $result;
    }

    public function specific_feedback(question_attempt $qa) {
        return $this->combined_feedback($qa);
    }

    /**
     * Format each question stem. Overwritten by randomsamatch renderer.
     *
     * @param question_attempt $qa
     * @param integer $stemid stem index
     * @return string
     */
    public function format_stem_text($qa, $stemid) {
        $question = $qa->get_question();
        return $question->format_text(
                    $question->stems[$stemid], $question->stemformat[$stemid],
                    $qa, 'qtype_match', 'subquestion', $stemid);
    }

    protected function format_choices($question) {
        $choices = array();
        foreach ($question->get_choice_order() as $key => $choiceid) {
            $choices[$key] = format_string($question->choices[$choiceid]);
        }
        return $choices;
    }

    public function correct_response(question_attempt $qa) {
        $question = $qa->get_question();
        $stemorder = $question->get_stem_order();

        $choices = $this->format_choices($question);
        $right = array();
        foreach ($stemorder as $key => $stemid) {
            if (!isset($choices[$question->get_right_choice_for($stemid)])) {
                continue;
            }
            $right[] = $question->make_html_inline($this->format_stem_text($qa, $stemid)) . ' &#x2192; ' .
                    $choices[$question->get_right_choice_for($stemid)];
        }

        if (!empty($right)) {
            return get_string('correctansweris', 'qtype_match', implode(', ', $right));
        }
    }
}
