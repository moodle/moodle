<?php
    function question_essay_restore($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the truefalse array
        $essays = $info['#']['ESSAY'];

        //Iterate over truefalse
        for($i = 0; $i < sizeof($essays); $i++) {
            $essay_info = $essays[$i];
            //traverse_xmlize($tru_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the question_essay record structure
            $essay->question = $new_question_id;
            $essay->answer = backup_todb($essay_info['#']['ANSWER']['0']['#']);

            ////We have to recode the answer field
            $answer = backup_getid($restore->backup_unique_code,"question_answers",$essay->answer);
            if ($answer) {
                $essay->answer = $answer->new_id;
            }

            //The structure is equal to the db, so insert the question_essay
            $newid = insert_record ("question_essay",$essay);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br />";
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
