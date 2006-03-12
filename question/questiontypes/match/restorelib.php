<?php
    function question_match_restore($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the matchs array
        $matchs = $info['#']['MATCHS']['0']['#']['MATCH'];

        //We have to build the subquestions field (a list of match_sub id)
        $subquestions_field = "";
        $in_first = true;

        //Iterate over matchs
        for($i = 0; $i < sizeof($matchs); $i++) {
            $mat_info = $matchs[$i];
            //traverse_xmlize($mat_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($mat_info['#']['ID']['0']['#']);

            //Now, build the question_match_SUB record structure
            $match_sub->question = $new_question_id;
            $match_sub->questiontext = backup_todb($mat_info['#']['QUESTIONTEXT']['0']['#']);
            $match_sub->answertext = backup_todb($mat_info['#']['ANSWERTEXT']['0']['#']);

            //The structure is equal to the db, so insert the question_match_sub
            $newid = insert_record ("question_match_sub",$match_sub);

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

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"question_match_sub",$oldid,
                             $newid);
                //We have a new match_sub, append it to subquestions_field
                if ($in_first) {
                    $subquestions_field .= $newid;
                    $in_first = false;
                } else {
                    $subquestions_field .= ",".$newid;
                }
            } else {
                $status = false;
            }
        }

        //We have created every match_sub, now create the match
        $match->question = $new_question_id;
        $match->subquestions = $subquestions_field;

        //The structure is equal to the db, so insert the question_match_sub
        $newid = insert_record ("question_match",$match);

        if (!$newid) {
            $status = false;
        }

        return $status;
    }

    function question_match_restore_map($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the matchs array
        $matchs = $info['#']['MATCHS']['0']['#']['MATCH'];

        //We have to build the subquestions field (a list of match_sub id)
        $subquestions_field = "";
        $in_first = true;

        //Iterate over matchs
        for($i = 0; $i < sizeof($matchs); $i++) {
            $mat_info = $matchs[$i];
            //traverse_xmlize($mat_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($mat_info['#']['ID']['0']['#']);

            //Now, build the question_match_SUB record structure
            $match_sub->question = $new_question_id;
            $match_sub->questiontext = backup_todb($mat_info['#']['QUESTIONTEXT']['0']['#']);
            $match_sub->answertext = backup_todb($mat_info['#']['ANSWERTEXT']['0']['#']);

            //If we are in this method is because the question exists in DB, so its
            //match_sub must exist too.
            //Now, we are going to look for that match_sub in DB and to create the
            //mappings in backup_ids to use them later where restoring states (user level).

            //Get the match_sub from DB (by question, questiontext and answertext)
            $db_match_sub = get_record ("question_match_sub","question",$new_question_id,
                                                      "questiontext",$match_sub->questiontext,
                                                      "answertext",$match_sub->answertext);
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

            //We have the database match_sub, so update backup_ids
            if ($db_match_sub) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"question_match_sub",$oldid,
                             $db_match_sub->id);
            } else {
                $status = false;
            }
        }

        return $status;
    }
?>
