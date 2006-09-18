<?php //$Id$
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
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("modulename","survey")." \"".format_string(stripslashes($survey->name),true)."\"</li>";
            }
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //Now check if want to restore user data and do it.
                if (restore_userdata_selected($restore,'survey',$mod->id)) {
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
                backup_putid($restore->backup_unique_code,"survey_analysis",$oldid,
                             $newid);
            } else {
                $status = false;
            }
        }

        return $status;
    }

    //Return a content decoded to support interactivities linking. Every module
    //should have its own. They are called automatically from
    //servey_decode_content_links_caller() function in each module
    //in the restore process
    function servey_decode_content_links ($content,$restore) {
            
        global $CFG;
            
        $result = $content;
                
        //Link to the list of serveys
                
        $searchstring='/\$@(SURVEYINDEX)\*([0-9]+)@\$/';
        //We look for it
        preg_match_all($searchstring,$content,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //print_object($foundset);                                     //Debug
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (course id)
                $rec = backup_getid($restore->backup_unique_code,"course",$old_id);
                //Personalize the searchstring
                $searchstring='/\$@(SURVEYINDEX)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/servey/index.php?id='.$rec->new_id,$result);
                } else { 
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/servey/index.php?id='.$old_id,$result);
                }
            }
        }

        //Link to servey view by moduleid

        $searchstring='/\$@(SURVEYVIEWBYID)\*([0-9]+)@\$/';
        //We look for it
        preg_match_all($searchstring,$result,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //print_object($foundset);                                     //Debug
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (course_modules id)
                $rec = backup_getid($restore->backup_unique_code,"course_modules",$old_id);
                //Personalize the searchstring
                $searchstring='/\$@(SURVEYVIEWBYID)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/servey/view.php?id='.$rec->new_id,$result);
                } else {
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/servey/view.php?id='.$old_id,$result);
                }
            }
        }

        return $result;
    }

    //This function makes all the necessary calls to xxxx_decode_content_links()
    //function in each module, passing them the desired contents to be decoded
    //from backup format to destination site/course in order to mantain inter-activities
    //working in the backup/restore process. It's called from restore_decode_content_links()
    //function in restore process
    function survey_decode_content_links_caller($restore) {
        global $CFG;
        $status = true;
        
        if ($surveys = get_records_sql ("SELECT s.id, s.intro
                                   FROM {$CFG->prefix}survey s
                                   WHERE s.course = $restore->course_id")) {
                                               //Iterate over each survey->intro
            $i = 0;   //Counter to send some output to the browser to avoid timeouts
            foreach ($surveys as $survey) {
                //Increment counter
                $i++;
                $content = $survey->intro;
                $result = restore_decode_content_links_worker($content,$restore);
                if ($result != $content) {
                    //Update record
                    $survey->intro = addslashes($result);
                    $status = update_record("survey",$survey);
                    if (debugging()) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<br /><hr />'.s($content).'<br />changed to<br />'.s($result).'<hr /><br />';
                        }
                    }
                }
                //Do some output
                if (($i+1) % 5 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 100 == 0) {
                            echo "<br />";
                        }
                    }
                    backup_flush(300);
                }
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
        case "download":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    //Rebuild the url, extracting the type (txt, xls)
                    $filetype = substr($log->url,-3);
                    $log->url = "download.php?id=".$log->cmid."&type=".$filetype;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        default:
            if (!defined('RESTORE_SILENTLY')) {
                echo "action (".$log->module."-".$log->action.") unknown. Not restored<br />";                 //Debug
            }
            break;
        }

        if ($status) {
            $status = $log;
        }
        return $status;
    }
?>
