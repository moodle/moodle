<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //assignment mods

    //This is the "graphical" structure of the assignment mod:
    //
    //                     assignment
    //                    (CL,pk->id)             
    //                        |
    //                        |
    //                        |
    //                 assignment_submisions 
    //           (UL,pk->id, fk->assignment,files)
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
    function assignment_restore_mods($mod,$restore) {

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

            //Now, build the ASSIGNMENT record structure
            $assignment->course = $restore->course_id;
            $assignment->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $assignment->description = backup_todb($info['MOD']['#']['DESCRIPTION']['0']['#']);
            $assignment->format = backup_todb($info['MOD']['#']['FORMAT']['0']['#']);
            $assignment->resubmit = backup_todb($info['MOD']['#']['RESUBMIT']['0']['#']);
            $assignment->type = backup_todb($info['MOD']['#']['TYPE']['0']['#']);
            $assignment->maxbytes = backup_todb($info['MOD']['#']['MAXBYTES']['0']['#']);
            $assignment->timedue = backup_todb($info['MOD']['#']['TIMEDUE']['0']['#']);
            $assignment->grade = backup_todb($info['MOD']['#']['GRADE']['0']['#']);
            $assignment->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);

            //We have to recode the grade field if it is <0 (scale)
            if ($assignment->grade < 0) {
                $scale = backup_getid($restore->backup_unique_code,"scale",abs($assignment->grade));        
                if ($scale) {
                    $assignment->grade = -($scale->new_id);       
                }
            }

            //The structure is equal to the db, so insert the assignment
            $newid = insert_record ("assignment",$assignment);

            //Do some output     
            echo "<ul><li>".get_string("modulename","assignment")." \"".$assignment->name."\"<br>";
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //Now check if want to restore user data and do it.
                if ($restore->mods['assignment']->userinfo) {
                    //Restore assignmet_submissions
                    $status = assignment_submissions_restore_mods ($mod->id, $newid,$info,$restore);
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

    //This function restores the assignment_submissions
    function assignment_submissions_restore_mods($old_assignment_id, $new_assignment_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the submissions array 
        $submissions = $info['MOD']['#']['SUBMISSIONS']['0']['#']['SUBMISSION'];

        //Iterate over submissions
        for($i = 0; $i < sizeof($submissions); $i++) {
            $sub_info = $submissions[$i];
            //traverse_xmlize($sub_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($sub_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($sub_info['#']['USERID']['0']['#']);

            //Now, build the ASSIGNMENT_SUBMISSIONS record structure
            $submission->assignment = $new_assignment_id;
            $submission->userid = backup_todb($sub_info['#']['USERID']['0']['#']);
            $submission->timecreated = backup_todb($sub_info['#']['TIMECREATED']['0']['#']);
            $submission->timemodified = backup_todb($sub_info['#']['TIMEMODIFIED']['0']['#']);
            $submission->numfiles = backup_todb($sub_info['#']['NUMFILES']['0']['#']);
            $submission->grade = backup_todb($sub_info['#']['GRADE']['0']['#']);
            $submission->comment = backup_todb($sub_info['#']['COMMENT']['0']['#']);
            $submission->teacher = backup_todb($sub_info['#']['TEACHER']['0']['#']);
            $submission->timemarked = backup_todb($sub_info['#']['TIMEMARKED']['0']['#']);
            $submission->mailed = backup_todb($sub_info['#']['MAILED']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$submission->userid);
            if ($user) {
                $submission->userid = $user->new_id;
            }

            //We have to recode the teacher field
            $user = backup_getid($restore->backup_unique_code,"user",$submission->teacher);
            if ($user) {
                $submission->teacher = $user->new_id;
            } 

            //The structure is equal to the db, so insert the assignment_submission
            $newid = insert_record ("assignment_submissions",$submission);

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
                backup_putid($restore->backup_unique_code,"assignment_submission",$oldid,
                             $newid);

                //Now copy moddata associated files
                $status = assignment_restore_files ($old_assignment_id, $new_assignment_id, 
                                                    $olduserid, $submission->userid, $restore);

            } else {
                $status = false;
            }
        }

        return $status;
    }

    //This function copies the assignment related info from backup temp dir to course moddata folder,
    //creating it if needed and recoding everything (assignment id and user id) 
    function assignment_restore_files ($oldassid, $newassid, $olduserid, $newuserid, $restore) {

        global $CFG;

        $status = true;
        $todo = false;
        $moddata_path = "";
        $assignment_path = "";
        $temp_path = "";

        //First, we check to "course_id" exists and create is as necessary
        //in CFG->dataroot
        $dest_dir = $CFG->dataroot."/".$restore->course_id;
        $status = check_dir_exists($dest_dir,true);

        //Now, locate course's moddata directory
        $moddata_path = $CFG->dataroot."/".$restore->course_id."/".$CFG->moddata;
   
        //Check it exists and create it
        $status = check_dir_exists($moddata_path,true);

        //Now, locate assignment directory
        if ($status) {
            $assignment_path = $moddata_path."/assignment";
            //Check it exists and create it
            $status = check_dir_exists($assignment_path,true);
        }

        //Now locate the temp dir we are gong to restore
        if ($status) {
            $temp_path = $CFG->dataroot."/temp/backup/".$restore->backup_unique_code.
                         "/moddata/assignment/".$oldassid."/".$olduserid;
            //Check it exists
            if (is_dir($temp_path)) {
                $todo = true;
            }
        }

        //If todo, we create the neccesary dirs in course moddata/assignment
        if ($status and $todo) {
            //First this assignment id
            $this_assignment_path = $assignment_path."/".$newassid;
            $status = check_dir_exists($this_assignment_path,true);
            //Now this user id
            $user_assignment_path = $this_assignment_path."/".$newuserid;
            //And now, copy temp_path to user_assignment_path
            $status = backup_copy_file($temp_path, $user_assignment_path); 
        }
       
        return $status;
    }

//This function returns a log record with all the necessay transformations
    //done. It's used by restore_log_module() to restore modules log.
    function assignment_restore_logs($restore,$log) {
                    
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
        case "upload":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?a=".$mod->new_id;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "view submission":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "submissions.php?id=".$mod->new_id;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "update grades":
            if ($log->cmid) {
                //Extract the assignment id from the url field                             
                $assid = substr(strrchr($log->url,"="),1);
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$assid);
                if ($mod) {
                    $log->url = "submissions.php?id=".$mod->new_id;
                    $status = true;
                }
            }
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
