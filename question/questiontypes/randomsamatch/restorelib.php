<?php
    function question_randomsamatch_restore($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the randomsamatchs array
        $randomsamatchs = $info['#']['RANDOMSAMATCH'];

        //Iterate over randomsamatchs
        for($i = 0; $i < sizeof($randomsamatchs); $i++) {
            $ran_info = $randomsamatchs[$i];
            //traverse_xmlize($ran_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the question_randomsamatch record structure
            $randomsamatch->question = $new_question_id;
            $randomsamatch->choose = backup_todb($ran_info['#']['CHOOSE']['0']['#']);
            $randomsamatch->shuffleanswers = backup_todb($ran_info['#']['SHUFFLEANSWERS']['0']['#']);

            //The structure is equal to the db, so insert the question_randomsamatch
            $newid = insert_record ("question_randomsamatch",$randomsamatch);

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
