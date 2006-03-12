<?php
    function question_rqp_restore($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the truefalse array
        $rqps = $info['#']['RQP'];

        //Iterate over rqp
        for($i = 0; $i < sizeof($rqps); $i++) {
            $tru_info = $rqps[$i];
            //traverse_xmlize($tru_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the question_rqp record structure
            $rqp->question = $new_question_id;
            $rqp->type = backup_todb($tru_info['#']['TYPE']['0']['#']);
            $rqp->source = backup_todb($tru_info['#']['SOURCE']['0']['#']);
            $rqp->format = backup_todb($tru_info['#']['FORMAT']['0']['#']);
            $rqp->flags = backup_todb($tru_info['#']['FLAGS']['0']['#']);
            $rqp->maxscore = backup_todb($tru_info['#']['MAXSCORE']['0']['#']);

            //The structure is equal to the db, so insert the question_rqp
            $newid = insert_record ("question_rqp",$rqp);

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
