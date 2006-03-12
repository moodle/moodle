<?php
    function question_calculated_restore($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the calculated-s array
        $calculateds = $info['#']['CALCULATED'];

        //Iterate over calculateds
        for($i = 0; $i < sizeof($calculateds); $i++) {
            $cal_info = $calculateds[$i];
            //traverse_xmlize($cal_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the question_calculated record structure
            $calculated->question = $new_question_id;
            $calculated->answer = backup_todb($cal_info['#']['ANSWER']['0']['#']);
            $calculated->tolerance = backup_todb($cal_info['#']['TOLERANCE']['0']['#']);
            $calculated->tolerancetype = backup_todb($cal_info['#']['TOLERANCETYPE']['0']['#']);
            $calculated->correctanswerlength = backup_todb($cal_info['#']['CORRECTANSWERLENGTH']['0']['#']);
            $calculated->correctanswerformat = backup_todb($cal_info['#']['CORRECTANSWERFORMAT']['0']['#']);

            ////We have to recode the answer field
            $answer = backup_getid($restore->backup_unique_code,"question_answers",$calculated->answer);
            if ($answer) {
                $calculated->answer = $answer->new_id;
            }

            //The structure is equal to the db, so insert the question_calculated
            $newid = insert_record ("question_calculated",$calculated);

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

            //Now restore numerical_units
            $status = question_restore_numerical_units ($old_question_id,$new_question_id,$cal_info,$restore);

            //Now restore dataset_definitions
            if ($status && $newid) {
                $status = question_restore_dataset_definitions ($old_question_id,$new_question_id,$cal_info,$restore);
            }

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }
?>
