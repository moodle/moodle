<?php //$Id$
    //This php script contains all the stuff to backup/restore
    //reservation mods

    //This is the "graphical" structure of the scorm mod:
    //
    //                      scorm                                      
    //                   (CL,pk->id)---------------------
    //                        |				|
    //                        |				|
    //                        |				|
    //                   scorm_scoes 			|	
    //             (UL,pk->id, fk->scorm)		|
    //                        |				|
    //                        |				|
    //                        |				|
    //                scorm_scoes_track 		|
    //  (UL,pk->id, fk->scormid, fk->scoid, fk->userid)--	
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
            $scorm->reference = backup_todb($info['MOD']['#']['MAXGRADE']['0']['#']);
            $scorm->reference = backup_todb($info['MOD']['#']['GRADEMETHOD']['0']['#']);
            $scorm->launch = backup_todb($info['MOD']['#']['LAUNCH']['0']['#']);
            $scorm->summary = backup_todb($info['MOD']['#']['SUMMARY']['0']['#']);
            $scorm->auto = backup_todb($info['MOD']['#']['AUTO']['0']['#']);
            $scorm->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);

            //The structure is equal to the db, so insert the scorm
            $newid = insert_record ("scorm",$scorm);
            //Do some output     
            echo "<li>".get_string("modulename","scorm")." \"".$scorm->name."\"</li>";
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                             
                //Now copy moddata associated files
                $status = scorm_restore_files ($scorm, $restore);
                
                if ($status)
                    $status = scorm_scoes_restore_mods ($newid,$info,$restore);
                    
            } else {
                $status = false;
            }
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
            $sco->manifest = backup_todb($sub_info['#']['MANIFEST']['0']['#']);
            $sco->organization = backup_todb($sub_info['#']['ORGANIZATION']['0']['#']);
            $sco->parent = backup_todb($sub_info['#']['PARENT']['0']['#']);
            $sco->identifier = backup_todb($sub_info['#']['IDENTIFIER']['0']['#']);
            $sco->launch = backup_todb($sub_info['#']['LAUNCH']['0']['#']);
            $sco->scormtype = backup_todb($sub_info['#']['SCORMTYPE']['0']['#']);
            $sco->title = backup_todb($sub_info['#']['TITLE']['0']['#']);
            $sco->title = backup_todb($sub_info['#']['PREREQUISITES']['0']['#']);
            $sco->title = backup_todb($sub_info['#']['MAXTIMEALLOWED']['0']['#']);
            $sco->title = backup_todb($sub_info['#']['TIMELIMITACTION']['0']['#']);
            $sco->datafromlms = backup_todb($sub_info['#']['DATAFROMLMS']['0']['#']);
            $sco->title = backup_todb($sub_info['#']['MASTERYSCORE']['0']['#']);
            $sco->next = backup_todb($sub_info['#']['NEXT']['0']['#']);
            $sco->previous = backup_todb($sub_info['#']['PREVIOUS']['0']['#']);

            //The structure is equal to the db, so insert the scorm_scoes
            $newid = insert_record ("scorm_scoes",$sco);
            
            //Now check if want to restore user data and do it.
            if ($restore->mods['scorm']->userinfo) {
                //Restore scorm_scoes
                if ($status) {
                    $status = scorm_scoes_tracks_restore_mods ($scorm_id,$info,$restore);
                }
            }

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
                backup_putid($restore->backup_unique_code,"scorm_scoes", $oldid, $newid);
            } else {
                $status = false;
            }
        }

        return $status;
    }
    
    //This function restores the scorm_scoes_track
    function scorm_scoes_tracks_restore_mods($scorm_id,$info,$restore) {

        global $CFG;

        $status = true;
        $sco_tracks = NULL;

        //Get the sco array
        if (!empty($info['MOD']['#']['SCO_TRACKS']['0']['#']['SCO_TRACK']))
            $sco_tracks = $info['MOD']['#']['SCO_TRACKS']['0']['#']['SCO_TRACK'];

        //Iterate over sco_users
        for($i = 0; $i < sizeof($sco_tracks); $i++) {
            $sco_track = $sco_tracks[$i];

            //We'll need this later!!
            $oldid = backup_todb($sub_info['#']['ID']['0']['#']);
            $oldscoid = backup_todb($sub_info['#']['SCOID']['0']['#']);
            $olduserid = backup_todb($sub_info['#']['USERID']['0']['#']);

            //Now, build the scorm_scoes_track record structure
            $sco_user->scormid = $scorm_id;
            $sco_user->userid = backup_todb($sub_info['#']['USERID']['0']['#']);
            $sco_user->scoid = backup_todb($sub_info['#']['SCOID']['0']['#']);
            $sco_user->element = backup_todb($sub_info['#']['ELEMENT']['0']['#']);
            $sco_user->value = backup_todb($sub_info['#']['VALUE']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$sco_track->userid);
            if ($user) {
                $sco_track->userid = $user->new_id;
            }
            
            //We have to recode the scoid field
            $sco = backup_getid($restore->backup_unique_code,"scorm_scoes",$sco_track->scoid);
            if ($sco) {
                $sco_track->scoid = $sco->new_id;
            }

            //The structure is equal to the db, so insert the scorm_scoes_track
            $newid = insert_record ("scorm_scoes_track",$sco_track);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br />";
                }
                backup_flush(300);
            }

        }

        return $status;
    }
    
    //This function copies the scorm related info from backup temp dir to course moddata folder,
    //creating it if needed
    function scorm_restore_files ($package, $restore) {

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
                         "/moddata/scorm/".$package->id;
            //Check it exists
            if (is_dir($temp_path)) {
                $todo = true;
            }
        }

        //If todo, we create the neccesary dirs in course moddata/scorm
        if ($status and $todo) {
            //Make scorm package directory path
            $this_scorm_path = $scorm_path."/".$package->id;
       
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
