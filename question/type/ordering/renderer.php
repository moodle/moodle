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
        $response_name      = $qa->get_qt_field_name($response_fieldname);
        $response_id        = 'id_'.preg_replace('/[^a-zA-Z0-9]+/', '_', $response_name);
        $sortable_id        = 'id_sortable_'.$question->id;
        $ablock_id          = 'id_ablock_'.$question->id;

        switch ($question->options->layouttype) {
            case 0 : $axis = 'y'; break; // vertical
            case 1 : $axis = ''; break;  // horizontal
            default: $axis = '';         // unknown
        }

        $result = '';

        if ($options->readonly || $options->correctness) {
            // don't allow items to be dragged and dropped
        } else {
            $script = "\n";
            $script .= "//<![CDATA[\n";
            $script .= "if (window.$) {\n";
            $script .= "    $(function() {\n";
            $script .= "        $('#$sortable_id').sortable({\n";
            $script .= "            axis: '$axis',\n";
            $script .= "            containment: '#$ablock_id',\n";
            $script .= "            opacity: 0.6,\n";
            $script .= "            update: function(event, ui) {\n";
            $script .= "                var ItemsOrder = $(this).sortable('toArray').toString();\n";
            $script .= "                $('#$response_id').attr('value', ItemsOrder);\n";
            $script .= "            }\n";
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

            // set layout class
            $layoutclass = $question->get_ordering_layoutclass();

            // get info about current/correct responses
            if ($options->correctness) {
                switch ($question->options->gradingtype) {

                    case 0: // ABSOLUTE
                        $correctinfo = $correctresponse;
                        $currentinfo = $currentresponse;
                        break;

                    case 1: // RELATIVE_NEXT_EXCLUDE_LAST
                    case 2: // RELATIVE_NEXT_INCLUDE_LAST
                        $currentinfo = $question->get_next_answerids($currentresponse, ($question->options->gradingtype==2));
                        $correctinfo = $question->get_next_answerids($correctresponse, ($question->options->gradingtype==2));
                        break;

                    case 3: // RELATIVE_ONE_PREVIOUS_AND_NEXT
                    case 4: // RELATIVE_ALL_PREVIOUS_AND_NEXT
                        $currentinfo = $question->get_previous_and_next_answerids($currentresponse, ($question->options->gradingtype==4));
                        $correctinfo = $question->get_previous_and_next_answerids($correctresponse, ($question->options->gradingtype==4));
                        break;
                }
            }

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
                    $result .= html_writer::start_tag('div', array('class' => 'ablock', 'id' => $ablock_id));
                    $result .= html_writer::start_tag('div', array('class' => 'answer ordering'));
                    $result .= html_writer::start_tag('ul',  array('class' => 'sortablelist', 'id' => $sortable_id));
                }

                // CSS $class and $img are only used to show correctness
                $class = '';
                $img = '';

                // display the correctness of this item
                if ($options->correctness) {

                    // correctness depends on grading type
                    $score = 0; // actual score for this item
                    $maxscore = null; // maximum score for this item
                    switch ($question->options->gradingtype) {

                        case 0: // ABSOLUTE
                            if (isset($correctinfo[$position])) {
                                if ($correctinfo[$position]==$answerid) {
                                    $score = 1;
                                }
                                $maxscore = 1;
                            }
                            break;

                        case 1; // RELATIVE_NEXT_EXCLUDE_LAST
                        case 2; // RELATIVE_NEXT_INCLUDE_LAST
                            if (isset($correctinfo[$answerid])) {
                                if (isset($currentinfo[$answerid]) && $currentinfo[$answerid]==$correctinfo[$answerid]) {
                                    $score = 1;
                                }
                                $maxscore = 1;
                            }
                            break;

                        case 3; // RELATIVE_ONE_PREVIOUS_AND_NEXT
                        case 4; // RELATIVE_ALL_PREVIOUS_AND_NEXT
                            if (isset($correctinfo[$answerid])) {
                                $maxscore = 0;
                                $prev = $correctinfo[$answerid]->prev;
                                $maxscore += count($prev);
                                $prev = array_intersect($prev, $currentinfo[$answerid]->prev);
                                $score += count($prev);
                                $next = $correctinfo[$answerid]->next;
                                $maxscore += count($next);
                                $next = array_intersect($next, $currentinfo[$answerid]->next);
                                $score += count($next);
                            }
                            break;
                    }
                    if ($maxscore===null) {
                        $class = 'unscored';
                    } else {
                        if ($maxscore==0) {
                            $score = 0.0;
                        } else {
                            $score = ($score / $maxscore);
                        }
                        switch (true) {
                            case ($score > 0.999999): $class = 'correct'; break;
                            case ($score < 0.000001): $class = 'incorrect'; break;
                            case ($score >= 0.66):    $class = 'partial66'; break;
                            case ($score >= 0.33):    $class = 'partial33'; break;
                            default:                  $class = 'partial01'; break;
                        }
                        $img = $this->feedback_image($score).' ';
                    }
                } else {
                    $class = 'sortableitem';
                }
                $class = "$class $layoutclass";

                // the original "id" revealed the correct order of the answers
                // because $answer->fraction holds the correct order number
                // $id = 'ordering_item_'.$answerid.'_'.intval($question->answers[$answerid]->fraction);
                $answer = $question->answers[$answerid];
                $answer->answer = $question->format_text($answer->answer, $answer->answerformat, $qa, 'question', 'answer', $answerid);
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
            // the following DIV is not necessary if we use "overflow: auto;" on the "answer" DIV
            //$result .= html_writer::tag('div', '', array('style' => 'clear:both;'));
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
            $layoutclass = $question->get_ordering_layoutclass();
            $output .= html_writer::tag('p', get_string('correctorder', 'qtype_ordering'));
            $output .= html_writer::start_tag('ol', array('class' => 'correctorder'));
            $correctresponse = $question->correctresponse;
            foreach ($correctresponse as $position => $answerid) {
                $answer = $question->answers[$answerid];
                $output .= html_writer::tag('li', $answer->answer, array('class' => $layoutclass));
            }
            $output .= html_writer::end_tag('ol');
        }

        return $output;
    }
}
