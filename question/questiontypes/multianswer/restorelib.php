<?php
    function question_multianswer_restore($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the multianswers array
        $multianswers = $info['#']['MULTIANSWERS']['0']['#']['MULTIANSWER'];
        //Iterate over multianswers
        for($i = 0; $i < sizeof($multianswers); $i++) {
            $mul_info = $multianswers[$i];
            //traverse_xmlize($mul_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We need this later
            $oldid = backup_todb($mul_info['#']['ID']['0']['#']);

            //Now, build the question_multianswer record structure
            $multianswer->question = $new_question_id;
            $multianswer->sequence = backup_todb($mul_info['#']['SEQUENCE']['0']['#']);

            //We have to recode the sequence field (a list of question ids)
            //Extracts question id from sequence
            $sequence_field = "";
            $in_first = true;
            $tok = strtok($multianswer->sequence,",");
            while ($tok) {
                //Get the answer from backup_ids
                $question = backup_getid($restore->backup_unique_code,"question",$tok);
                if ($question) {
                    if ($in_first) {
                        $sequence_field .= $question->new_id;
                        $in_first = false;
                    } else {
                        $sequence_field .= ",".$question->new_id;
                    }
                }
                //check for next
                $tok = strtok(",");
            }
            //We have the answers field recoded to its new ids
            $multianswer->sequence = $sequence_field;
            //The structure is equal to the db, so insert the question_multianswer
            $newid = insert_record ("question_multianswer",$multianswer);

            //Save ids in backup_ids
            if ($newid) {
                backup_putid($restore->backup_unique_code,"question_multianswer",
                             $oldid, $newid);
            }

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
/*
            //If we have created the question_multianswer record, now, depending of the
            //answertype, delegate the restore to every qtype function
            if ($newid) {
                if ($multianswer->answertype == "1") {
                    $status = quiz_restore_shortanswer ($old_question_id,$new_question_id,$mul_info,$restore);
                } else if ($multianswer->answertype == "3") {
                    $status = quiz_restore_multichoice ($old_question_id,$new_question_id,$mul_info,$restore);
                } else if ($multianswer->answertype == "8") {
                    $status = quiz_restore_numerical ($old_question_id,$new_question_id,$mul_info,$restore);
                }
            } else {
                $status = false;
            }
*/
        }

        return $status;
    }

    function question_multianswer_restore_map($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the multianswers array
        $multianswers = $info['#']['MULTIANSWERS']['0']['#']['MULTIANSWER'];
        //Iterate over multianswers
        for($i = 0; $i < sizeof($multianswers); $i++) {
            $mul_info = $multianswers[$i];
            //traverse_xmlize($mul_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We need this later
            $oldid = backup_todb($mul_info['#']['ID']['0']['#']);

            //Now, build the question_multianswer record structure
            $multianswer->question = $new_question_id;
            $multianswer->answers = backup_todb($mul_info['#']['ANSWERS']['0']['#']);
            $multianswer->positionkey = backup_todb($mul_info['#']['POSITIONKEY']['0']['#']);
            $multianswer->answertype = backup_todb($mul_info['#']['ANSWERTYPE']['0']['#']);
            $multianswer->norm = backup_todb($mul_info['#']['NORM']['0']['#']);

            //If we are in this method is because the question exists in DB, so its
            //multianswer must exist too.
            //Now, we are going to look for that multianswer in DB and to create the
            //mappings in backup_ids to use them later where restoring states (user level).

            //Get the multianswer from DB (by question and positionkey)
            $db_multianswer = get_record ("question_multianswer","question",$new_question_id,
                                                      "positionkey",$multianswer->positionkey);
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

            //We have the database multianswer, so update backup_ids
            if ($db_multianswer) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"question_multianswer",$oldid,
                             $db_multianswer->id);
            } else {
                $status = false;
            }
        }

        return $status;
    }
?>
