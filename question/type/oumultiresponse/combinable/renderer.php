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
 * @package   qtype_oumultiresponse
 * @copyright  2013 The Open University
 * @author     Jamie Pratt <me@jamiep.org>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


class qtype_oumultiresponse_embedded_renderer extends qtype_renderer
    implements qtype_combined_subquestion_renderer_interface {

    public function subquestion(question_attempt $qa,
                                question_display_options $options,
                                qtype_combined_combinable_base $subq,
                                $placeno) {
        $question = $subq->question;
        $fullresponse = new qtype_combined_response_array_param($qa->get_last_qt_data());
        $response = $fullresponse->for_subq($subq);

        $commonattributes = array(
            'type' => 'checkbox'
        );

        if ($options->readonly) {
            $commonattributes['disabled'] = 'disabled';
        }

        $checkboxes = array();
        $feedbackimg = array();
        $classes = array();
        foreach ($question->get_order($qa) as $value => $ansid) {
            $inputname = $qa->get_qt_field_name($subq->step_data_name('choice'.$value));
            $ans = $question->answers[$ansid];
            $inputattributes = array();
            $inputattributes['name'] = $inputname;
            $inputattributes['value'] = 1;
            $inputattributes['id'] = $inputname;
            $isselected = $question->is_choice_selected($response, $value);
            if ($isselected) {
                $inputattributes['checked'] = 'checked';
            }
            $hidden = '';
            if (!$options->readonly) {
                $hidden = html_writer::empty_tag('input', array(
                    'type' => 'hidden',
                    'name' => $inputattributes['name'],
                    'value' => 0,
                ));
            }

            $checkboxes[] = html_writer::empty_tag('input', $inputattributes + $commonattributes) .
                    html_writer::tag('label',
                            html_writer::span(\qtype_combined\utils::number_in_style($value, $question->answernumbering),
                                    'answernumber') .
                            $question->format_text($ans->answer, $ans->answerformat, $qa, 'question', 'answer', $ansid),
                            ['for' => $inputattributes['id']]);

            $class = 'r' . ($value % 2);
            if ($options->correctness && $isselected) {
                $iscbcorrect = ($ans->fraction > 0) ? 1 : 0;
                $feedbackimg[] = $this->feedback_image($iscbcorrect);
                $class .= ' ' . $this->feedback_class($iscbcorrect);
            } else {
                $feedbackimg[] = '';
            }
            $classes[] = $class;
        }

        $cbhtml = '';

        if ('h' === $subq->get_layout()) {
            $inputwraptag = 'span';
            $classname = 'horizontal';
        } else {
            $inputwraptag = 'div';
            $classname = 'vertical';
        }

        foreach ($checkboxes as $key => $checkbox) {
            $cbhtml .= html_writer::tag($inputwraptag, $checkbox . ' ' . $feedbackimg[$key],
                                        array('class' => $classes[$key])) . "\n";
        }

        $result = html_writer::tag($inputwraptag, $cbhtml, array('class' => 'answer'));
        $result = html_writer::div($result, $classname);

        return $result;
    }
}
