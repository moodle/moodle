<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //reservation mods

    //This is the "graphical" structure of the scorm mod:
    //
    //                    scorm                                      
    //                    (CL,pk->id)--------------------
    //                        |				|
    //                        |				|
    //                        |				|
    //                scorm_scoes 			|	
    //            (UL,pk->id, fk->scorm)		|
    //                        |				|
    //                        |				|
    //                        |				|
    //                scorm_sco_users 			|
    //            (UL,pk->id, fk->scormid, fk->scoid)----	
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
    function scorm_restore_mods($mod,$restore) {

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

            //Now, build the SCORM record structure
            $scorm->course = $restore->course_id;
            $scorm->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $scorm->reference = backup_todb($info['MOD']['#']['REFERENCE']['0']['#']);
            $scorm->datadir = backup_todb($info['MOD']['#']['DATADIR']['0']['#']);
            $scorm->launch = backup_todb($info['MOD']['#']['LAUNCH']['0']['#']);
            $scorm->summary = backup_todb($info['MOD']['#']['SUMMARY']['0']['#']);
            $scorm->auto = backup_todb($info['MOD']['#']['AUTO']['0']['#']);
            $scorm->popup = backup_todb($info['MOD']['#']['POPUP']['0']['#']);
            $scorm->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);

            //The structure is equal to the db, so insert the scorm
            $newid = insert_record ("scorm",$scorm);
            //Do some output     
            echo "<ul><li>".get_string("modulename","scorm")." \"".$scorm->name."\"<br>";
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                             
                //Now copy moddata associated files
                $status = scorm_restore_files ($scorm->datadir, $restore);
                
                if ($status)
                    $status = scorm_scoes_restore_mods ($newid,$info,$restore);
                    
                //Now check if want to restore user data and do it.
                if ($restore->mods['scorm']->userinfo) {
                    //Restore scorm_scoes
                    if ($status)
                        $status = scorm_sco_users_restore_mods ($newid,$info,$restore);
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

    //This function restores the scorm_scoes
    function scorm_scoes_restore_mods($scorm_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the sco array
        $scoes = $info['MOD']['#']['SCOES']['0']['#']['SCO'];

        //Iterate over scoes
        for($i = 0; $i < sizeof($scoes); $i++) {
            $sub_info = $scoes[$i];

	    //We'll need this later!!
            $oldid = backup_todb($sub_info['#']['ID']['0']['#']);
            
            //Now, build the scorm_scoes record structure
            $sco->scorm = $scorm_id;
            $sco->parent = backup_todb($sub_info['#']['PARENT']['0']['#']);
            $sco->identifier = backup_todb($sub_info['#']['IDENTIFIER']['0']['#']);
            $sco->launch = backup_todb($sub_info['#']['LAUNCH']['0']['#']);
            $sco->type = backup_todb($sub_info['#']['TYPE']['0']['#']);
            $sco->title = backup_todb($sub_info['#']['TITLE']['0']['#']);
            $sco->next = backup_todb($sub_info['#']['NEXT']['0']['#']);
            $sco->previous = backup_todb($sub_info['#']['PREVIOUS']['0']['#']);

            //The structure is equal to the db, so insert the scorm_scoes
            $newid = insert_record ("scorm_scoes",$sco);

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
                backup_putid($restore->backup_unique_code,"scorm_scoes", $oldid, $newid);
            } else {
                $status = false;
            }
        }

        return $status;
    }
    
    //This function restores the scorm_sco_users
    function scorm_sco_users_restore_mods($scorm_id,$info,$restore) {

        global $CFG;

        $status = true;
        $sco_users = NULL;

        //Get the sco array
        if (!empty($info['MOD']['#']['SCO_USERS']['0']['#']['SCO_USER']))
            $sco_users = $info['MOD']['#']['SCO_USERS']['0']['#']['SCO_USER'];

        //Iterate over sco_users
        for($i = 0; $i < sizeof($sco_users); $i++) {
            $sub_info = $sco_users[$i];

            //We'll need this later!!
            $oldid = backup_todb($sub_info['#']['ID']['0']['#']);
            $oldscoid = backup_todb($sub_info['#']['SCOID']['0']['#']);
            $olduserid = backup_todb($sub_info['#']['USERID']['0']['#']);

            //Now, build the scorm_sco_users record structure
            $sco_user->scormid = $scorm_id;
            $sco_user->userid = backup_todb($sub_info['#']['USERID']['0']['#']);
            $sco_user->scoid = backup_todb($sub_info['#']['SCOID']['0']['#']);
            $sco_user->cmi_core_lesson_location = backup_todb($sub_info['#']['CMI_CORE_LESSON_LOCATION']['0']['#']);
            $sco_user->cmi_core_lesson_status = backup_todb($sub_info['#']['CMI_CORE_LESSON_STATUS']['0']['#']);
            $sco_user->cmi_core_exit = backup_todb($sub_info['#']['CMI_CORE_EXIT']['0']['#']);
            $sco_user->cmi_core_total_time = backup_todb($sub_info['#']['CMI_CORE_TOTAL_TIME']['0']['#']);
            $sco_user->cmi_core_session_time = backup_todb($sub_info['#']['CMI_CORE_SESSION_TIME']['0']['#']);
            $sco_user->cmi_core_score_raw = backup_todb($sub_info['#']['CMI_CORE_SCORE_RAW']['0']['#']);
            $sco_user->cmi_suspend_data = backup_todb($sub_info['#']['CMI_SUSPEND_DATA']['0']['#']);
            $sco_user->cmi_launch_data = backup_todb($sub_info['#']['CMI_LAUNCH_DATA']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$sco_user->userid);
            if ($user) {
                $sco_user->userid = $user->new_id;
            }
            
            //We have to recode the scoid field
            $sco = backup_getid($restore->backup_unique_code,"scorm_scoes",$sco_user->scoid);
            if ($sco) {
                $sco_user->scoid = $sco->new_id;
            }

            //The structure is equal to the db, so insert the scorm_sco_users
            $newid = insert_record ("scorm_sco_users",$sco_user);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br>";
                }
                backup_flush(300);
            }

        }

        return $status;
    }
    
    //This function copies the scorm related info from backup temp dir to course moddata folder,
    //creating it if needed
    function scorm_restore_files ($packagedir, $restore) {

        global $CFG;

        $status = true;
        $todo = false;
        $moddata_path = "";
        $scorm_path = "";
        $temp_path = "";

        //First, we check to "course_id" exists and create is as necessary
        //in CFG->dataroot
        $dest_dir = $CFG->dataroot."/".$restore->course_id;
        $status = check_dir_exists($dest_dir,true);

        //First, locate course's moddata directory
        $moddata_path = $CFG->dataroot."/".$restore->course_id."/".$CFG->moddata;
  
        //Check it exists and create it
        $status = check_dir_exists($moddata_path,true);

        //Now, locate scorm directory
        if ($status) {
            $scorm_path = $moddata_path."/scorm";
            //Check it exists and create it
            $status = check_dir_exists($scorm_path,true);
        }

        //Now locate the temp dir we are restoring from
        if ($status) {
            $temp_path = $CFG->dataroot."/temp/backup/".$restore->backup_unique_code.
                         "/moddata/scorm/".$packagedir;
            //Check it exists
            if (is_dir($temp_path)) {
                $todo = true;
            }
        }

        //If todo, we create the neccesary dirs in course moddata/scorm
        if ($status and $todo) {
            //Make scorm package directory path
            $this_scorm_path = $scorm_path."/".$packagedir;
       
            $status = backup_copy_file($temp_path, $this_scorm_path);
        }

        return $status;
    }
    
    //This function returns a log record with all the necessay transformations
    //done. It's used by restore_log_module() to restore modules log.
    function scorm_restore_logs($restore,$log) {
                    
        $status = true;
                    
        return $status;
    }
?>
