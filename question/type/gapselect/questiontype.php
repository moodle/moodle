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
 * Question type class for the select missing words question type.
 *
 * @package qtype
 * @subpackage gapselect
 * @copyright 2011 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/format/xml/format.php');

require_once($CFG->dirroot . '/question/type/gapselect/questiontypebase.php');

/**
 * The select missing words question type class.
 *
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_gapselect extends qtype_gapselect_base {
    protected function choice_options_to_feedback($choice) {
        return $choice['choicegroup'];
    }

    protected function make_choice($choicedata) {
        return new qtype_gapselect_choice($choicedata->answer, $choicedata->feedback);
    }

    protected function feedback_to_choice_options($feedback) {
        return array('selectgroup' => $feedback);
    }


    protected function choice_group_key() {
        return 'selectgroup';
    }

    function import_from_xml($data, $question, $format, $extra=null) {
        if (!isset($data['@']['type']) || $data['@']['type'] != 'gapselect') {
            return false;
        }

        $question = $format->import_headers($data);
        $question->qtype = 'gapselect';

        $question->shuffleanswers = $format->trans_single(
                $format->getpath($data, array('#', 'shuffleanswers', 0, '#'), 1));

        if (!empty($data['#']['selectoption'])) {
            // Modern XML format.
            $selectoptions = $data['#']['selectoption'];
            $question->answer = array();
            $question->selectgroup = array();

            foreach ($data['#']['selectoption'] as $selectoptionxml) {
                $question->choices[] = array(
                    'answer' => $format->getpath($selectoptionxml, array('#', 'text', 0, '#'), '', true),
                    'selectgroup' => $format->getpath($selectoptionxml, array('#', 'group', 0, '#'), 1),
                );
            }

        } else {
            // Legacy format containing PHP serialisation.
            foreach ($data['#']['answer'] as $answerxml) {
                $ans = $format->import_answer($answerxml);
                $question->choices[] = array(
                    'answer' => $ans->answer,
                    'selectgroup' => $ans->feedback,
                );
            }
        }

        $format->import_combined_feedback($question, $data, true);
        $format->import_hints($question, $data, true);

        return $question;
    }

    function export_to_xml($question, $format, $extra = null) {
        $output = '';

        $output .= '    <shuffleanswers>' . $question->options->shuffleanswers . "</shuffleanswers>\n";

        $output .= $format->write_combined_feedback($question->options);

        foreach ($question->options->answers as $answer) {
            $output .= "    <selectoption>\n";
            $output .= $format->writetext($answer->answer, 3);
            $output .= "      <group>{$answer->feedback}</group>\n";
            $output .= "    </selectoption>\n";
        }

        return $output;
    }

    /*
     * Backup the data in the question
     *
     * This is used in question/backuplib.php
     */
    public function backup($bf, $preferences, $question, $level = 6) {
        $status = true;
        $gapselects = get_records("question_gapselect", "questionid", $question, "id");

        //If there are gapselect
        if ($gapselects) {
            //Iterate over each gapselect
            foreach ($gapselects as $gapselect) {
                $status = fwrite ($bf,start_tag("SDDLS",$level,true));
                //Print oumultiresponse contents
                fwrite ($bf,full_tag("SHUFFLEANSWERS",$level+1,false,$gapselect->shuffleanswers));
                fwrite ($bf,full_tag("CORRECTFEEDBACK",$level+1,false,$gapselect->correctfeedback));
                fwrite ($bf,full_tag("PARTIALLYCORRECTFEEDBACK",$level+1,false,$gapselect->partiallycorrectfeedback));
                fwrite ($bf,full_tag("INCORRECTFEEDBACK",$level+1,false,$gapselect->incorrectfeedback));
                fwrite ($bf,full_tag("SHOWNUMCORRECT",$level+1,false,$gapselect->shownumcorrect));
                $status = fwrite ($bf,end_tag("SDDLS",$level,true));
            }

            //Now print question_answers
            $status = question_backup_answers($bf,$preferences,$question);
        }
        return $status;
    }

    /**
     * Restores the data in the question (This is used in question/restorelib.php)
     *
     */
    public function restore($old_question_id,$new_question_id,$info,$restore) {
        $status = true;

        //Get the gapselect array
        $gapselects = $info['#']['SDDLS'];

        //Iterate over oumultiresponses
        for($i = 0; $i < sizeof($gapselects); $i++) {
            $mul_info = $gapselects[$i];

            //Now, build the question_gapselect record structure
            $gapselect = new stdClass;
            $gapselect->questionid = $new_question_id;
            $gapselect->shuffleanswers = isset($mul_info['#']['SHUFFLEANSWERS']['0']['#'])?backup_todb($mul_info['#']['SHUFFLEANSWERS']['0']['#']):'';
            if (array_key_exists("CORRECTFEEDBACK", $mul_info['#'])) {
                $gapselect->correctfeedback = backup_todb($mul_info['#']['CORRECTFEEDBACK']['0']['#']);
            } else {
                $gapselect->correctfeedback = '';
            }
            if (array_key_exists("PARTIALLYCORRECTFEEDBACK", $mul_info['#'])) {
                $gapselect->partiallycorrectfeedback = backup_todb($mul_info['#']['PARTIALLYCORRECTFEEDBACK']['0']['#']);
            } else {
                $gapselect->partiallycorrectfeedback = '';
            }
            if (array_key_exists("INCORRECTFEEDBACK", $mul_info['#'])) {
                $gapselect->incorrectfeedback = backup_todb($mul_info['#']['INCORRECTFEEDBACK']['0']['#']);
            } else {
                $gapselect->incorrectfeedback = '';
            }
            if (array_key_exists('SHOWNUMCORRECT', $mul_info['#'])) {
                $gapselect->shownumcorrect = backup_todb($mul_info['#']['SHOWNUMCORRECT']['0']['#']);
            } else if (array_key_exists('CORRECTRESPONSESFEEDBACK', $mul_info['#'])) {
                $gapselect->shownumcorrect = backup_todb($mul_info['#']['CORRECTRESPONSESFEEDBACK']['0']['#']);
            } else {
                $gapselect->shownumcorrect = 0;
            }

            $newid = insert_record ("question_gapselect",$gapselect);

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }

            if (!$newid) {
                $status = false;
            }
        }
        return $status;
    }

}
