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
 * ORDERING question renderer class.
 * (originally based on the TRUE-FALSE renderer)
 *
 * @package    qtype
 * @subpackage ordering
 * @copyright  2013 Gordon Bateson (gordonbateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (! class_exists('qtype_with_combined_feedback_renderer')) { // Moodle 2.0
    require_once($CFG->dirroot.'/question/type/ordering/legacy/rendererbase.php');
}

/**
 * Generates the output for ORDERING questions
 *
 * @copyright  2013 Gordon Bateson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ordering_renderer extends qtype_with_combined_feedback_renderer {

    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        global $CFG, $DB;

        $question = $qa->get_question();
        $response = $qa->get_last_qt_data();
        $question->update_current_response($response);
        $currentresponse = $question->currentresponse;
        $correctresponse = $question->correctresponse;

        // generate fieldnames and ids
        //   response_fieldname : 1_response_319
        //   response_name      : q27:1_response_319
        //   response_id        : id_q27_1_response_319
        //   sortable_id        : id_sortable_q27_1_response_319
        $response_fieldname = $question->get_response_fieldname();
        $response_name = $qa->get_qt_field_name($response_fieldname);
        $response_id = 'id_'.preg_replace('/[^a-zA-Z0-9]+/', '_', $response_name);
        $sortable_id = 'id_sortable_'.$question->id;

        $result = '';

        if ($options->readonly || $options->correctness) {
            // don't allow items to be dragged and dropped
        } else {
            $script = "\n";
            $script .= "//<![CDATA[\n";
            $script .= "if (window.$) {\n"; // $ is an alias for jQuery
            $script .= "    $(function() {\n";
            $script .= "        $('#$sortable_id').sortable({\n";
            $script .= "            update: function(event, ui) {\n";
            $script .= "                var ItemsOrder = $(this).sortable('toArray').toString();\n";
            $script .= "                $('#$response_id').attr('value', ItemsOrder);\n";
            $script .= "            },\n";
            $script .= "            opacity: 0.6\n";
            $script .= "        });\n";
            $script .= "        $('#$sortable_id').disableSelection();\n";
            $script .= "    });\n";
            $script .= "    $(document).ready(function() {\n";
            $script .= "        var ItemsOrder = $('#$sortable_id').sortable('toArray').toString();\n";
            $script .= "        $('#$response_id').attr('value', ItemsOrder);\n";
            $script .= "    });\n";
            $script .= "}\n";
            $script .= "//]]>\n";
            $result .= html_writer::tag('script', $script, array('type' => 'text/javascript'));
        }

        $result .= html_writer::tag('div', $question->format_questiontext($qa), array('class' => 'qtext'));

        $printeditems = false;
        if (count($currentresponse)) {

            // generate ordering items
            foreach ($currentresponse as $position => $answerid) {
                if (! array_key_exists($answerid, $question->answers)) {
                    continue; // shouldn't happen !!
                }
                if (! array_key_exists($position, $correctresponse)) {
                    continue; // shouldn't happen !!
                }

                if ($printeditems==false) {
                    $printeditems = true;
                    $result .= html_writer::start_tag('div', array('class' => 'ablock'));
                    $result .= html_writer::start_tag('div', array('class' => 'answer'));
                    $result .= html_writer::start_tag('ul',  array('class' => 'sortablelist', 'id' => $sortable_id));
                }

                if ($options->correctness) {
                    if ($correctresponse[$position]==$answerid) {
                        $class = 'correctposition';
                        $img = $this->feedback_image(1);
                    } else {
                        $class = 'wrongposition';
                        $img = $this->feedback_image(0);
                    }
                    $img = "$img ";
                } else {
                    $class = 'sortableitem';
                    $img = '';
                }
                // the original "id" revealed the correct order of the answers
                // because $answer->fraction holds the correct order number
                // $id = 'ordering_item_'.$answerid.'_'.intval($question->answers[$answerid]->fraction);
                $answer = $question->answers[$answerid];
                $params = array('class' => $class, 'id' => $answer->md5key);
                $result .= html_writer::tag('li', $img.$answer->answer, $params);
            }
        }

        if ($printeditems) {
            $result .= html_writer::end_tag('ul');
            $result .= html_writer::end_tag('div'); // answer
            $result .= html_writer::end_tag('div'); // ablock

            $result .= html_writer::empty_tag('input', array('type'  => 'hidden',
                                                             'name'  => $response_name,
                                                             'id'    => $response_id,
                                                             'value' => ''));
            $result .= html_writer::tag('div', '', array('style' => 'clear:both;'));
        }

        return $result;
    }

    public function specific_feedback(question_attempt $qa) {
        return $this->combined_feedback($qa);
    }

    public function correct_response(question_attempt $qa) {
        global $DB;

        $output = '';

        $showcorrect = false;
        $question = $qa->get_question();
        if (empty($question->correctresponse)) {
            $output .= html_writer::tag('p', get_string('noresponsedetails', 'qtype_ordering'));
        } else {
            if ($step = $qa->get_last_step()) {
                switch ($step->get_state()) {
                    case 'gradedright'  : $showcorrect = false; break;
                    case 'gradedpartial': $showcorrect = true;  break;
                    case 'gradedwrong'  : $showcorrect = true;  break;
                }
            }
        }
        if ($showcorrect) {
            $output .= html_writer::tag('p', get_string('correctorder', 'qtype_ordering'));
            $output .= html_writer::start_tag('ol');
            $correctresponse = $question->correctresponse;
            foreach ($correctresponse as $position => $answerid) {
                $answer = $question->answers[$answerid];
                $output .= html_writer::tag('li', $answer->answer);
            }
            $output .= html_writer::end_tag('ol');
        }

        return $output;
    }
}
