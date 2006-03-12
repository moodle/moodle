<?php
    function question_shortanswer_restore($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the shortanswers array
        $shortanswers = $info['#']['SHORTANSWER'];

        //Iterate over shortanswers
        for($i = 0; $i < sizeof($shortanswers); $i++) {
            $sho_info = $shortanswers[$i];
            //traverse_xmlize($sho_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the question_shortanswer record structure
            $shortanswer->question = $new_question_id;
            $shortanswer->answers = backup_todb($sho_info['#']['ANSWERS']['0']['#']);
            $shortanswer->usecase = backup_todb($sho_info['#']['USECASE']['0']['#']);

            //We have to recode the answers field (a list of answers id)
            //Extracts answer id from sequence
            $answers_field = "";
            $in_first = true;
            $tok = strtok($shortanswer->answers,",");
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
            $shortanswer->answers = $answers_field;

            //The structure is equal to the db, so insert the question_shortanswer
            $newid = insert_record ("question_shortanswer",$shortanswer);

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
?>
