<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //survey mods

    //This is the "graphical" structure of the survey mod:
    //                                                    --------------------
    //                           survey                   | survey_questions |
    //                        (CL,pk->id)                 |(CL,pk->id,?????) |
    //                            |                       --------------------
    //                            |
    //             -----------------------------------        
    //             |                                 |
    //        survey_analysis                   survey_answers
    //    (UL,pk->id, fk->survey)           (UL,pk->id, fk->survey)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------


    function survey_restore_mods($mod,$restore) {

        global $CFG,$db;

        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

        if ($data) {
            //Now get completed xmlized object   
            $info = $data->info;
            //traverse_xmlize($info);                                                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the SURVEY record structure
            $survey->course = $restore->course_id;
            $survey->template = backup_todb($info['MOD']['#']['TEMPLATE']['0']['#']);
            $survey->days = backup_todb($info['MOD']['#']['DAYS']['0']['#']);
            $survey->timecreated = backup_todb($info['MOD']['#']['TIMECREATED']['0']['#']);
            $survey->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);
            $survey->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $survey->intro = backup_todb($info['MOD']['#']['INTRO']['0']['#']);
            $survey->questions = backup_todb($info['MOD']['#']['QUESTIONS']['0']['#']);

            //The structure is equal to the db, so insert the survey
            $newid = insert_record ("survey",$survey);

            //Do some output
            echo "<ul><li>".get_string("modulename","survey")." \"".$survey->name."\"<br>";
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //Now check if want to restore user data and do it.
                if ($restore->mods['survey']->userinfo) {
                    //Restore survey_answers
                    $status = survey_answers_restore_mods ($newid,$info,$restore);
                    //Restore survey_analysis
                    if ($status) {
                        $status = survey_analysis_restore_mods ($newid,$info,$restore);
                    }
                }
            } else {
                $status = false;
            }

            //Finalize ul
            echo "</ul>";

        } else {
            $status = false;
        }

        return $status;
    }

    //This function restores the survey_answers
    function survey_answers_restore_mods($survey_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the answers array
        $answers = $info['MOD']['#']['ANSWERS']['0']['#']['ANSWER'];

        //Iterate over answers
        for($i = 0; $i < sizeof($answers); $i++) {
            $sub_info = $answers[$i];
            //traverse_xmlize($sub_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($sub_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($sub_info['#']['USERID']['0']['#']);

            //Now, build the SURVEY_ANSWERS record structure
            $answer->survey = $survey_id;
            $answer->userid = backup_todb($sub_info['#']['USERID']['0']['#']);
            $answer->question = backup_todb($sub_info['#']['QUESTION']['0']['#']);
            $answer->time = backup_todb($sub_info['#']['TIME']['0']['#']);
            $answer->answer1 = backup_todb($sub_info['#']['ANSWER1']['0']['#']);
            $answer->answer2 = backup_todb($sub_info['#']['ANSWER2']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$answer->userid);
            if ($user) {
                $answer->userid = $user->new_id;
            }

            //The structure is equal to the db, so insert the survey_answers
            $newid = insert_record ("survey_answers",$answer);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br>";
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"survey_answers",$oldid,
                             $newid);
            } else {
                $status = false;
            }
        }

        return $status;
    }

    //This function restores the survey_analysis
    function survey_analysis_restore_mods($survey_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the analysis array
        $analysis = $info['MOD']['#']['ANALYSIS']['0']['#']['ANALYS'];

        //Iterate over analysis
        for($i = 0; $i < sizeof($analysis); $i++) {
            $sub_info = $analysis[$i];
            //traverse_xmlize($sub_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($sub_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($sub_info['#']['USERID']['0']['#']);

            //Now, build the SURVEY_ANALYSIS record structure
            $analys->survey = $survey_id;
            $analys->userid = backup_todb($sub_info['#']['USERID']['0']['#']);
            $analys->notes = backup_todb($sub_info['#']['NOTES']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$analys->userid);
            if ($user) {
                $analys->userid = $user->new_id;
            }

            //The structure is equal to the db, so insert the survey_analysis
            $newid = insert_record ("survey_analysis",$analys);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";       
                if (($i+1) % 1000 == 0) {
                    echo "<br>";
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"survey_analysis",$oldid,
                             $newid);
            } else {
                $status = false;
            }
        }

        return $status;
    }

    //This function returns a log record with all the necessay transformations
    //done. It's used by restore_log_module() to restore modules log.
    function survey_restore_logs($restore,$log) {

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
        case "submit":
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
        case "view form":
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
        case "view graph":
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
        case "view report":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "report.php?id=".$log->cmid;
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
            echo "action (".$log->module."-".$log->action.") unknow. Not restored<br>";                 //Debug
            break;
        }

        if ($status) {
            $status = $log;
        }
        return $status;
    }
?>
