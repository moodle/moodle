<?php //$Id$
    //This php script contains all the stuff to backup/restore
    //choice mods

    //This is the "graphical" structure of the choice mod:
    //
    //                      choice                                      
    //                    (CL,pk->id)
    //                        |
    //                        |
    //                        |
    //                   choice_responses 
    //               (UL,pk->id, fk->choice)     
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    //This function executes all the restore procedure about this mod
    function choice_restore_mods($mod,$restore) {

        global $CFG;

        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

        if ($data) {
            //Now get completed xmlized object
            $info = $data->info;
            //traverse_xmlize($info);                                                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the CHOICE record structure
            $choice->course = $restore->course_id;
            $choice->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $choice->text = backup_todb($info['MOD']['#']['TEXT']['0']['#']);
            $choice->format = backup_todb($info['MOD']['#']['FORMAT']['0']['#']);
            $choice->showunanswered = backup_todb($info['MOD']['#']['SHOWUNANSWERED']['0']['#']);
            $choice->timeopen = backup_todb($info['MOD']['#']['TIMEOPEN']['0']['#']);
            $choice->timeclose = backup_todb($info['MOD']['#']['TIMECLOSE']['0']['#']);
            $choice->publish = backup_todb($info['MOD']['#']['PUBLISH']['0']['#']);
            $choice->release = backup_todb($info['MOD']['#']['RELEASE']['0']['#']);
            $choice->display = backup_todb($info['MOD']['#']['DISPLAY']['0']['#']);
            $choice->allowupdate = backup_todb($info['MOD']['#']['ALLOWUPDATE']['0']['#']);
            $choice->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);

            //To mantain compatibilty, in 1.4 the publish setting meaning has changed. We
            //have to modify some things it if the release field isn't present in the backup file.
            if (! isset($info['MOD']['#']['RELEASE']['0']['#'])) {  //It's a pre-14 backup filea
                //Set the allowupdate field
                if ($choice->publish == 0) { 
                    $choice->allowupdate = 1;
                }
                //Set the release field as defined by the old publish field
                if ($choice->publish > 0) {
                    $choice->release = 1;
                }
                //Recode the publish field to its 1.4 meaning
                if ($choice->publish > 0) {
                    $choice->publish--;
                }
            }
            
            //The structure is equal to the db, so insert the choice
            $newid = insert_record ("choice",$choice);
            
            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //Now restore the answers for this choice 
                
                //check to see how answers are stored in the table - if answer1 - answer6 exist, this is an old version of choice
                if (isset($info['MOD']['#']['ANSWER1']['0']['#']) || isset($info['MOD']['#']['ANSWER2']['0']['#']) || isset($info['MOD']['#']['ANSWER3']['0']['#']) || isset($info['MOD']['#']['ANSWER4']['0']['#']) || isset($info['MOD']['#']['ANSWER5']['0']['#']) || isset($info['MOD']['#']['ANSWER6']['0']['#']) ) {           
                    $answers[1] = backup_todb($info['MOD']['#']['ANSWER1']['0']['#']);
                    $answers[2] = backup_todb($info['MOD']['#']['ANSWER2']['0']['#']);
                    $answers[3] = backup_todb($info['MOD']['#']['ANSWER3']['0']['#']);
                    $answers[4] = backup_todb($info['MOD']['#']['ANSWER4']['0']['#']);
                    $answers[5] = backup_todb($info['MOD']['#']['ANSWER5']['0']['#']);
                    $answers[6] = backup_todb($info['MOD']['#']['ANSWER6']['0']['#']);
                
                    for($i = 1; $i < 7; $i++) { //insert new answers into db.  
                        if (!empty($answers[$i])) {  //make sure this answer has something in it!
                            $answer->choice = $newid;
                            $answer->answer = $answers[$i];
                            $answer->timemodified = $info['MOD']['#']['TIMEMODIFIED']['0']['#'];
                            $ansid[$i] = insert_record ("choice_answers",$answer);
                            $status = true;
                        }
                    }
                    
                    //now restore the responses for this choice.
                    if ($restore->mods['choice']->userinfo) {
                        //Restore choice_responses
                        $status = choice_responses_restore_mods($newid,$info,$restore,$ansid,"1.4");     
                    }                               
                 } else {
                     //this is a normal backup file                
                     $answers = $info['MOD']['#']['ANSWERS']['0']['#']['ANSWER'];
                     for($i = 0; $i < sizeof($answers); $i++) {
                     $sub_info = $answers[$i];                                                                       
                         $answer->choice = $newid;
                         $answer->answer = backup_todb($sub_info['#']['CHOICE_ANSWER']['0']['#']);
                         $answer->timemodified = backup_todb($sub_info['#']['TIMEMODIFIED']['0']['#']);
                         $ansid[$sub_info['#']['ID']['0']['#']] = insert_record ("choice_answers",$answer);     
                         $status = true;                                        
                     }
                     //now restore the responses for this choice.
                     if ($restore->mods['choice']->userinfo) {
                        //Restore choice_responses
                        $status = choice_responses_restore_mods($newid,$info,$restore,$ansid);     
                     }                               
                 }                                                                                          
            } else {
                $status = false;
            }
            //Do some output     
            echo "<li>".get_string("modulename","choice")." \"".format_string(stripslashes($choice->name),true)."\"</li>";
            backup_flush(300);

            
        } else {
            $status = false;
        }
        return $status;
    }

        
    //This function restores the choice_responses
    function choice_responses_restore_mods($choice_id,$info,$restore,$answerids,$version) {

        global $CFG;

        $status = true;

        //Get the responses array
        if ($version == "1.4") { //this version stores responses differently.
            $responses = $info['MOD']['#']['ANSWERS']['0']['#']['ANSWER'];
        } else {
            $responses = $info['MOD']['#']['RESPONSES']['0']['#']['RESPONSE'];
        }

        //Iterate over responses
        for($i = 0; $i < sizeof($responses); $i++) {
            $sub_info = $responses[$i];
            //traverse_xmlize($sub_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($sub_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($sub_info['#']['USERID']['0']['#']);

            //Now, build the CHOICE_RESPONSES record structure
            $response->choice = $choice_id;
            $response->userid = backup_todb($sub_info['#']['USERID']['0']['#']);
            $response->timemodified = backup_todb($sub_info['#']['TIMEMODIFIED']['0']['#']);

            //we have to recode the answer field
            if ($version == "1.4") { 
                //this is an old style choice.
                $response->answerid = $answerids[backup_todb($sub_info['#']['CHOICE_ANSWER']['0']['#'])];          
              //  
            } else {            
                $response->answerid = $answerids[backup_todb($sub_info['#']['CHOICE_RESPONSE']['0']['#'])];
            }

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$response->userid);
            if ($user) {
                $response->userid = $user->new_id;
            }

            //The structure is equal to the db, so insert the choice_responses
            $newid = insert_record ("choice_responses",$response);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br />";
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"choice_responses",$oldid,
                             $newid);
            } else {
                $status = false;
            }
        }

        return $status;
    }
    //This function returns a log record with all the necessay transformations
    //done. It's used by restore_log_module() to restore modules log.
    function choice_restore_logs($restore,$log) {
                    
        $status = false;
                    
        //Depending of the action, we recode different things
        switch ($log->action) {
        case "add":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "update":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "choose":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "choose again":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "view":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "view all":
            $log->url = "index.php?id=".$log->course;
            $status = true;
            break;
        default:
            echo "action (".$log->module."-".$log->action.") unknow. Not restored<br />";                 //Debug
            break;
        }

        if ($status) {
            $status = $log;
        }
        return $status;
    }
?>
