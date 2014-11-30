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
 * True-false question renderer class.
 *
 * @package    qtype
 * @subpackage ordering
 * @copyright  2013 Gordon Bateson (gordonbateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Generates the output for true-false questions.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ordering_renderer extends qtype_renderer {

    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        global $CFG, $DB;

        static $addStyle = true;
        static $addScript = true;

        $question = $qa->get_question();
        $ordering = $question->get_ordering_options();
        $answers  = $question->get_ordering_answers();

        if (empty($ordering) || empty($answers)) {
            return ''; // shouldn't happen !!
        }

        if ($ordering->studentsee==0) { // all items
            $ordering->studentsee = count($answers);
        } else {
            // a nasty hack so that "studentsee" is the same
            // as what is displayed by edit_ordering_form.php
            $ordering->studentsee += 2;
        }

        if ($options->readonly || $options->correctness) {
            // don't allow items to be dragged and dropped
            $readonly = true;
        } else {
            $readonly = false;
        }

        if ($options->correctness) {
            list($answerids, $correctorder) = $this->get_response($qa, $question, $answers);
        } else {
            $correctorder = array();
            switch ($ordering->logical) {
                case 0: // all
                    $answerids = array_keys($answers);
                    break;

                case 1: // random subset
                    $answerids = array_rand($answers, $ordering->studentsee);
                    break;

                case 2: // contiguous subset
                    if (count($answers) > $ordering->studentsee) {
                        $offset = mt_rand(0, count($answers) - $ordering->studentsee);
                        $answers = array_slice($answers, $offset, $ordering->studentsee, true);
                    }
                    $answerids = array_keys($answers);
                    break;
            }
            shuffle($answerids);
        }

        $response_name = $qa->get_qt_field_name($question->get_response_fieldname());
        $response_id = 'id_'.preg_replace('/[^a-zA-Z0-9]+/', '_', $response_name);
        $sortable_id = 'id_sortable_'.$question->id;

        $result = '';
        if ($readonly==false) {
            $result .= html_writer::tag('script', '', array('type'=>'text/javascript', 'src'=>$CFG->wwwroot.'/question/type/ordering/js/jquery.js'));
            $result .= html_writer::tag('script', '', array('type'=>'text/javascript', 'src'=>$CFG->wwwroot.'/question/type/ordering/js/jquery-ui.js'));
            $result .= html_writer::tag('script', '', array('type'=>'text/javascript', 'src'=>$CFG->wwwroot.'/question/type/ordering/js/jquery.ui.touch-punch.js'));
        }

        $style = "\n";
        $style .= "ul#$sortable_id li {\n";
        $style .= "    position: relative;\n";
        $style .= "}\n";
        if ($addStyle) {
            $addStyle = false; // only add style once
            $style .= "ul.boxy {\n";
            $style .= "    border: 1px solid #ccc;\n";
            $style .= "    float: left;\n";
            $style .= "    font-family: Arial, sans-serif;\n";
            $style .= "    font-size: 13px;\n";
            $style .= "    list-style-type: none;\n";
            $style .= "    margin: 0px;\n";
            $style .= "    margin-left: 5px;\n";
            $style .= "    padding: 4px 4px 0 4px;\n";
            $style .= "    width: 360px;\n";
            $style .= "}\n";
            $style .= "ul.boxy li {\n";
            $style .= "    background-color: #eeeeee;\n";
            $style .= "    border: 1px solid #cccccc;\n";
            $style .= "    border-image: initial;\n";
            if ($readonly==false) {
                $style .= "    cursor: move;\n";
            }
            $style .= "    list-style-type: none;\n";
            $style .= "    margin-bottom: 1px;\n";
            $style .= "    min-height: 20px;\n";
            $style .= "    padding: 8px 2px;\n";
            $style .= "}\n";
        }
        $result .= html_writer::tag('style', $style, array('type' => 'text/css'));

        if ($readonly==false) {
            $script = "\n";
            $script .= "//<![CDATA[\n";
            $script .= "$(function() {\n";
            $script .= "    $('#$sortable_id').sortable({\n";
            $script .= "        update: function(event, ui) {\n";
            $script .= "            var ItemsOrder = $(this).sortable('toArray').toString();\n";
            $script .= "            $('#$response_id').attr('value', ItemsOrder);\n";
            $script .= "        }\n";
            $script .= "    });\n";
            $script .= "    $('#$sortable_id').disableSelection();\n";
            $script .= "});\n";
            $script .= "$(document).ready(function() {\n";
            $script .= "    var ItemsOrder = $('#$sortable_id').sortable('toArray').toString();\n";
            $script .= "    $('#$response_id').attr('value', ItemsOrder);\n";
            $script .= "});\n";
            $script .= "//]]>\n";
            $result .= html_writer::tag('script', $script, array('type' => 'text/javascript'));
        }

        $result .= html_writer::tag('div', $question->format_questiontext($qa), array('class' => 'qtext'));

        if (count($answerids)) {
            $result .= html_writer::start_tag('div', array('class' => 'ablock'));
            $result .= html_writer::start_tag('div', array('class' => 'answer'));
            $result .= html_writer::start_tag('ul',  array('class' => 'boxy', 'id' => $sortable_id));

            // generate ordering items
            foreach ($answerids as $position => $answerid) {
                if (array_key_exists($answerid, $answers)) {
                    if ($options->correctness) {
                        if ($correctorder[$position]==$answerid) {
                            $class = 'correctposition';
                            $img = $this->feedback_image(1);
                        } else {
                            $class = 'wrongposition';
                            $img = $this->feedback_image(0);
                        }
                        $img = "$img ";
                    } else {
                        $class = 'ui-state-default';
                        $img = '';
                    }
                    // the original "id" revealed the correct order of the answers
                    // because $answer->fraction holds the correct order number
                    // $id = 'ordering_item_'.$answerid.'_'.intval($answers[$answerid]->fraction);
                    $params = array('class' => $class, 'id' => $answers[$answerid]->md5key);

                    $result .= html_writer::tag('li', $img.$answers[$answerid]->answer, $params);
                }
            }

            $result .= html_writer::end_tag('ul');
            $result .= html_writer::end_tag('div'); // answer
            $result .= html_writer::end_tag('div'); // ablock

            $params = array('type' => 'hidden', 'name' => $response_name, 'id' => $response_id, 'value' => '');
            $result .= html_writer::empty_tag('input', $params);
            $result .= html_writer::tag('div', '', array('style' => 'clear:both;'));
        }

        return $result;
    }

    public function correct_response(question_attempt $qa) {
        global $DB;

        $output = '';

        $showcorrect = true;
        if ($step = $qa->get_last_step()) {
            switch ($step->get_state()) {
                case 'gradedright':
                    $showcorrect = false;
                    $msg = get_string('correctfeedback', 'qtype_ordering');
                    break;
                case 'gradedpartial':
                    $fraction = round($step->get_fraction(), 2);
                    $msg = get_string('partiallycorrectfeedback', 'qtype_ordering', $fraction);
                    break;
                case 'gradedwrong':
                    $msg = get_string('incorrectfeedback', 'qtype_ordering');
                    break;
                default:
                    $showcorrect = false;
                    $msg = '';
            }
            if ($msg) {
                $output .= html_writer::tag('p', $msg);
            }
        }

        if ($showcorrect) {
            $question = $qa->get_question();
            $answers  = $question->get_ordering_answers();
            list($answerids, $correctorder) = $this->get_response($qa, $question, $answers);
            if (count($correctorder)) {
                $output .= html_writer::tag('p', get_string('correctorder', 'qtype_ordering'));
                $output .= html_writer::start_tag('ol');
                foreach ($correctorder as $position => $answerid) {
                    $output .= html_writer::tag('li', $answers[$answerid]->answer);
                }
                $output .= html_writer::end_tag('ol');
            } else {
                $output .= html_writer::tag('p', get_string('noresponsedetails', 'qtype_ordering'));
            }
        }

        return $output;
    }

    public function get_response($qa, $question, $answers) {
        $answerids = array();
        $correctorder = array();

        $name = $question->get_response_fieldname();
        if ($step = $qa->get_last_step_with_qt_var($name)) {

            $response = $step->get_qt_var($name);  // "$md5key, ..."
            $response = explode(',', $response);   // array($position => $md5key, ...)
            $response = array_flip($response);     // array($md5key => $position, ...)

            foreach ($answers as $answer) {
                if (array_key_exists($answer->md5key, $response)) {
                    $position = $response[$answer->md5key];
                    $sortorder = intval($answer->fraction);
                    $answerids[$position] = $answer->id;
                    $correctorder[$sortorder] = $answer->id;
                }
            }

            ksort($answerids);
            ksort($correctorder);
            $correctorder = array_values($correctorder);
        }

        return array($answerids, $correctorder);
    }
}
