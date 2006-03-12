<?php
    function question_truefalse_restore($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the truefalse array
        $truefalses = $info['#']['TRUEFALSE'];

        //Iterate over truefalse
        for($i = 0; $i < sizeof($truefalses); $i++) {
            $tru_info = $truefalses[$i];
            //traverse_xmlize($tru_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the question_truefalse record structure
            $truefalse->question = $new_question_id;
            $truefalse->trueanswer = backup_todb($tru_info['#']['TRUEANSWER']['0']['#']);
            $truefalse->falseanswer = backup_todb($tru_info['#']['FALSEANSWER']['0']['#']);

            ////We have to recode the trueanswer field
            $answer = backup_getid($restore->backup_unique_code,"question_answers",$truefalse->trueanswer);
            if ($answer) {
                $truefalse->trueanswer = $answer->new_id;
            }

            ////We have to recode the falseanswer field
            $answer = backup_getid($restore->backup_unique_code,"question_answers",$truefalse->falseanswer);
            if ($answer) {
                $truefalse->falseanswer = $answer->new_id;
            }

            //The structure is equal to the db, so insert the question_truefalse
            $newid = insert_record ("question_truefalse",$truefalse);

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
