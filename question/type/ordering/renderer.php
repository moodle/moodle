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

/** Prevent direct access to this script */
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

    protected $correctinfo = null;
    protected $currentinfo = null;
    protected $itemscores = array();

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
            case qtype_ordering_question::LAYOUT_VERTICAL   : $axis = 'y'; break;
            case qtype_ordering_question::LAYOUT_HORIZONTAL : $axis = '';  break;
            default: $axis = '';
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

                // set the CSS class and correctness img for this response
                if ($options->correctness) {
                    $score = $this->get_ordering_item_score($question, $position, $answerid);
                    list($score, $maxscore, $fraction, $percent, $class, $img) = $score;
                } else {
                    $class = 'sortableitem';
                    $img = '';
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

        if ($feedback = $this->combined_feedback($qa)) {
            $feedback = html_writer::tag('p', $feedback);
        }

        $gradingtype = '';
        $gradedetails = '';
        $scoredetails = '';

        // if required, add explanation of grade calculation
        if ($step = $qa->get_last_step()) {
            $state = $step->get_state();
            if ($state=='gradedpartial' || $state=='gradedwrong') {

                $plugin = 'qtype_ordering';
                $question = $qa->get_question();

                // fetch grading type
                $gradingtype = $question->options->gradingtype;
                $gradingtype = qtype_ordering_question::get_grading_types($gradingtype);

                // format grading type, e.g. Grading type: Relative to next item, excluding last item
                if ($gradingtype) {
                    $gradingtype = get_string('gradingtype', $plugin).': '.$gradingtype;
                    $gradingtype = html_writer::tag('p', $gradingtype, array('class' => 'gradingtype'));
                }

                // fetch grade details and score details
                if ($currentresponse = $question->currentresponse) {

                    $totalscore = 0;
                    $totalmaxscore = 0;

                    $layoutclass = $question->get_ordering_layoutclass();
                    $params = array('class' => $layoutclass);

                    $scoredetails .= html_writer::tag('p', get_string('scoredetails', $plugin));
                    $scoredetails .= html_writer::start_tag('ol', array('class' => 'scoredetails'));

                    // format scoredetails, e.g. 1 /2 = 50%, for each item
                    foreach ($currentresponse as $position => $answerid) {
                        $answer = $question->answers[$answerid];
                        $score = $this->get_ordering_item_score($question, $position, $answerid);
                        list($score, $maxscore, $fraction, $percent, $class, $img) = $score;
                        if ($maxscore===null) {
                            $score = get_string('noscore', $plugin);
                        } else {
                            $totalscore += $score;
                            $totalmaxscore += $maxscore;
                            $score = "$score / $maxscore = $percent%";
                        }
                        $scoredetails .= html_writer::tag('li', $score, $params);
                    }

                    $scoredetails .= html_writer::end_tag('ol');

                    // format gradedetails, e.g. 4 /6 = 67%
                    if ($totalscore==0 || $totalmaxscore==0) {
                        $gradedetails = 0;
                    } else {
                        $gradedetails = round(100 * $totalscore / $totalmaxscore, 0);
                    }
                    $gradedetails = "$totalscore / $totalmaxscore = $gradedetails%";
                    $gradedetails = get_string('gradedetails', $plugin).': '.$gradedetails;
                    $gradedetails = html_writer::tag('p', $gradedetails, array('class' => 'gradedetails'));
                }
            }
        }

        return $feedback.$gradingtype.$gradedetails.$scoredetails;
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

    /////////////////////////////////////
    // custom methods
    /////////////////////////////////////

    protected function get_response_info($question) {

        $gradingtype = $question->options->gradingtype;
        switch ($gradingtype) {

            case qtype_ordering_question::GRADING_ABSOLUTE_POSITION:
                $this->correctinfo = $question->correctresponse;
                $this->currentinfo = $question->currentresponse;
                break;

            case qtype_ordering_question::GRADING_RELATIVE_NEXT_EXCLUDE_LAST:
            case qtype_ordering_question::GRADING_RELATIVE_NEXT_INCLUDE_LAST:
                $this->correctinfo = $question->get_next_answerids($question->correctresponse, $gradingtype==2);
                $this->currentinfo = $question->get_next_answerids($question->currentresponse, $gradingtype==2);
                break;

            case qtype_ordering_question::GRADING_RELATIVE_ONE_PREVIOUS_AND_NEXT:
            case qtype_ordering_question::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT:
                $this->correctinfo = $question->get_previous_and_next_answerids($question->correctresponse, $gradingtype==4);
                $this->currentinfo = $question->get_previous_and_next_answerids($question->currentresponse, $gradingtype==4);
                break;

            case qtype_ordering_question::GRADING_LONGEST_ORDERED_SUBSET:
            case qtype_ordering_question::GRADING_LONGEST_CONTIGUOUS_SUBSET:
                $this->correctinfo = $question->correctresponse;
                $this->currentinfo = $question->currentresponse;
                $subset = $question->get_ordered_subset($gradingtype==6);
                foreach ($this->currentinfo as $position => $answerid) {
                    if (array_search($position, $subset)===false) {
                        $this->currentinfo[$position] = 0;
                    } else {
                        $this->currentinfo[$position] = 1;
                    }
                }
                break;
        }
    }

    protected function get_ordering_item_score($question, $position, $answerid) {

        if (! isset($this->itemscores[$position])) {

            if ($this->correctinfo===null || $this->currentinfo===null) {
                $this->get_response_info($question);
            }

            $correctinfo = $this->correctinfo;
            $currentinfo = $this->currentinfo;

            $score    = 0;    // actual score for this item
            $maxscore = null; // max score for this item
            $fraction = 0.0;  // $score / $maxscore
            $percent  = 0;    // 100 * $fraction
            $class    = '';   // CSS class
            $img      = '';   // icon to show correctness

            switch ($question->options->gradingtype) {

                case qtype_ordering_question::GRADING_ABSOLUTE_POSITION:
                    if (isset($correctinfo[$position])) {
                        if ($correctinfo[$position]==$answerid) {
                            $score = 1;
                        }
                        $maxscore = 1;
                    }
                    break;

                case qtype_ordering_question::GRADING_RELATIVE_NEXT_EXCLUDE_LAST:
                case qtype_ordering_question::GRADING_RELATIVE_NEXT_INCLUDE_LAST:
                    if (isset($correctinfo[$answerid])) {
                        if (isset($currentinfo[$answerid]) && $currentinfo[$answerid]==$correctinfo[$answerid]) {
                            $score = 1;
                        }
                        $maxscore = 1;
                    }
                    break;

                case qtype_ordering_question::GRADING_RELATIVE_ONE_PREVIOUS_AND_NEXT:
                case qtype_ordering_question::GRADING_RELATIVE_ALL_PREVIOUS_AND_NEXT:
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

                case qtype_ordering_question::GRADING_LONGEST_ORDERED_SUBSET:
                case qtype_ordering_question::GRADING_LONGEST_CONTIGUOUS_SUBSET:
                    if (isset($correctinfo[$position])) {
                        if (isset($currentinfo[$position])) {
                            $score = $currentinfo[$position];
                        }
                        $maxscore = 1;
                    }
                    break;
            }

            if ($maxscore===null) {
                // an unscored item is either an illegal item
                // or last item of RELATIVE_NEXT_EXCLUDE_LAST
                // or an item from an unrecognized grading type
                $class = 'unscored';
            } else {
                if ($maxscore==0) {
                    $fraction = 0.0;
                    $percent = 0;
                } else {
                    $fraction = ($score / $maxscore);
                    $percent = round(100 * $fraction, 0);
                }
                switch (true) {
                    case ($fraction > 0.999999): $class = 'correct';   break;
                    case ($fraction < 0.000001): $class = 'incorrect'; break;
                    case ($fraction >= 0.66):    $class = 'partial66'; break;
                    case ($fraction >= 0.33):    $class = 'partial33'; break;
                    default:                     $class = 'partial00'; break;
                }
                $img = $this->feedback_image($fraction);
            }

            $score = array($score, $maxscore, $fraction, $percent, $class, $img);
            $this->itemscores[$position] = $score;
        }

        return $this->itemscores[$position];
    }
}
