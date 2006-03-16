<?php
    function question_multichoice_restore($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the multichoices array
        $multichoices = $info['#']['MULTICHOICE'];

        //Iterate over multichoices
        for($i = 0; $i < sizeof($multichoices); $i++) {
            $mul_info = $multichoices[$i];
            //traverse_xmlize($mul_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the question_multichoice record structure
            $multichoice->question = $new_question_id;
            $multichoice->layout = backup_todb($mul_info['#']['LAYOUT']['0']['#']);
            $multichoice->answers = backup_todb($mul_info['#']['ANSWERS']['0']['#']);
            $multichoice->single = backup_todb($mul_info['#']['SINGLE']['0']['#']);
            $multichoice->shuffleanswers = backup_todb($mul_info['#']['SHUFFLEANSWERS']['0']['#']);

            //We have to recode the answers field (a list of answers id)
            //Extracts answer id from sequence
            $answers_field = "";
            $in_first = true;
            $tok = strtok($multichoice->answers,",");
            while ($tok) {
                //Get the answer from backup_ids
                $answer = backup_getid($restore->backup_unique_code,"question_answers",$tok);
                if ($answer) {
                    if ($in_first) {
                        $answers_field .= $answer->new_id;
                        $in_first = false;
                    } else {
                        $answers_field .= ",".$answer->new_id;
                    }
                }
                //check for next
                $tok = strtok(",");
            }
            //We have the answers field recoded to its new ids
            $multichoice->answers = $answers_field;

            //The structure is equal to the db, so insert the question_shortanswer
            $newid = insert_record ("question_multichoice",$multichoice);

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

    function question_multichoice_recode_answer($state, $restore) {
        $answer_field = "";
        $in_first = true;
        $tok = strtok($state->answer,",");
        while ($tok) {
            //Get the answer from backup_ids
            $answer = backup_getid($restore->backup_unique_code,"question_answers",$tok);
            if ($answer) {
                if ($in_first) {
                    $answer_field .= $answer->new_id;
                    $in_first = false;
                } else {
                    $answer_field .= ",".$answer->new_id;
                }
            }
            //check for next
            $tok = strtok(",");
        }
        return $answer_field;
    }

?>
