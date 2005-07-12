<?php //$Id$
    //This php script contains all the stuff to backup/restore
    //reservation mods

    //This is the "graphical" structure of the scorm mod:
    //
    //                      scorm
    //                   (CL,pk->id)---------------------
    //                        |                         |
    //                        |                         |
    //                        |                         |
    //                   scorm_scoes                    |
    //             (UL,pk->id, fk->scorm)               |
    //                        |                         |
    //                        |                         |
    //                        |                         |
    //                scorm_scoes_track                 |
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
            $scorm->version = backup_todb($info['MOD']['#']['VERSION']['0']['#']);
            $scorm->maxgrade = backup_todb($info['MOD']['#']['MAXGRADE']['0']['#']);
            if (!is_int($scorm->maxgrade)) {
                $scorm->maxgrade = 0;
            }
            $scorm->grademethod = backup_todb($info['MOD']['#']['GRADEMETHOD']['0']['#']);
            if (!is_int($scorm->grademethod)) {
                $scorm->grademethod = 0;
            }
            if ($restore->backup_version < 2005041500) {
                $scorm->datadir = substr(backup_todb($info['MOD']['#']['DATADIR']['0']['#']),1);
            } else {
                $scorm->datadir = backup_todb($info['MOD']['#']['ID']['0']['#']);
            }
            $oldlaunch = backup_todb($info['MOD']['#']['LAUNCH']['0']['#']);
            $scorm->summary = backup_todb($info['MOD']['#']['SUMMARY']['0']['#']);
            $scorm->hidebrowse = backup_todb($info['MOD']['#']['HIDEBROWSE']['0']['#']);
            $scorm->hidetoc = backup_todb($info['MOD']['#']['HIDETOC']['0']['#']);
            $scorm->auto = backup_todb($info['MOD']['#']['AUTO']['0']['#']);
            if ($restore->backup_version < 2005040200) {
                $oldpopup = backup_todb($info['MOD']['#']['POPUP']['0']['#']);
                if (!empty($oldpopup)) {
                    $scorm->popup = 1;
                } else {
                    $scorm->popup = 0;
                }
            } else {
                $scorm->popup = backup_todb($info['MOD']['#']['POPUP']['0']['#']);
            }
            $scorm->width = backup_todb($info['MOD']['#']['WIDTH']['0']['#']);
            if ($scorm->width == 0) {
                $scorm->width = 800;
            }
            $scorm->height = backup_todb($info['MOD']['#']['HEIGHT']['0']['#']);
            if ($scorm->height == 0) {
                $scorm->height = 600;
            }
            $scorm->timemodified = time();

            //The structure is equal to the db, so insert the scorm
            $newid = insert_record ("scorm",$scorm);
            //Do some output
            echo "<li>".get_string("modulename","scorm")." \"".format_string(stripslashes($scorm->name),true)."\"</li>";
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                $scorm->id = $newid;
                //Now copy moddata associated files
                $status = scorm_restore_files ($scorm, $restore);

                if ($status) {
                    $status = scorm_scoes_restore_mods ($newid,$info,$restore);
                    if ($status) {
                        $launchsco = backup_getid($restore->backup_unique_code,"scorm_scoes",$oldlaunch);
                        $scorm->launch = $launchsco->new_id;
                        update_record('scorm',$scorm);
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
            if ($restore->backup_version < 2005031300) {
                $sco->scormtype = backup_todb($sub_info['#']['TYPE']['0']['#']);
            } else {
                $sco->scormtype = backup_todb($sub_info['#']['SCORMTYPE']['0']['#']);
            }
            $sco->title = backup_todb($sub_info['#']['TITLE']['0']['#']);
            $sco->prerequisites = backup_todb($sub_info['#']['PREREQUISITES']['0']['#']);
            $sco->maxtimeallowed = backup_todb($sub_info['#']['MAXTIMEALLOWED']['0']['#']);
            $sco->timelimitaction = backup_todb($sub_info['#']['TIMELIMITACTION']['0']['#']);
            $sco->datafromlms = backup_todb($sub_info['#']['DATAFROMLMS']['0']['#']);
            $sco->masteryscore = backup_todb($sub_info['#']['MASTERYSCORE']['0']['#']);
            $sco->next = backup_todb($sub_info['#']['NEXT']['0']['#']);
            $sco->previous = backup_todb($sub_info['#']['PREVIOUS']['0']['#']);

            //The structure is equal to the db, so insert the scorm_scoes
            $newid = insert_record ("scorm_scoes",$sco);
            
            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"scorm_scoes", $oldid, $newid);
            } else {
                $status = false;
            }
        }

        //Now check if want to restore user data and do it.
        if ($restore->mods['scorm']->userinfo) {
            //Restore scorm_scoes
            if ($status) {
                if ($restore->backup_version < 2005031300) {
                    $status = scorm_scoes_tracks_restore_mods_pre15 ($scorm_id,$info,$restore);
                } else {
                    $status = scorm_scoes_tracks_restore_mods ($scorm_id,$info,$restore);
                }
            }
        }    
        
        return $status;
    }

    //This function restores the scorm_scoes_track
    function scorm_scoes_tracks_restore_mods($scorm_id,$info,$restore) {

        global $CFG;

        $status = true;
        $scotracks = NULL;

        //Get the sco array
        if (!empty($info['MOD']['#']['SCO_TRACKS']['0']['#']['SCO_TRACK']))
            $scotracks = $info['MOD']['#']['SCO_TRACKS']['0']['#']['SCO_TRACK'];

        //Iterate over sco_users
        for($i = 0; $i < sizeof($scotracks); $i++) {
            $sub_info = $scotracks[$i];
            unset($scotrack);

            //Now, build the scorm_scoes_track record structure
            $scotrack->scormid = $scorm_id;
            $scotrack->userid = backup_todb($sub_info['#']['USERID']['0']['#']);
            $scotrack->scoid = backup_todb($sub_info['#']['SCOID']['0']['#']);
            $scotrack->element = backup_todb($sub_info['#']['ELEMENT']['0']['#']);
            $scotrack->value = backup_todb($sub_info['#']['VALUE']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$scotrack->userid);
            if (!empty($user)) {
                $scotrack->userid = $user->new_id;
            }

            //We have to recode the scoid field
            $sco = backup_getid($restore->backup_unique_code,"scorm_scoes",$scotrack->scoid);
            if ($sco != NULL) {
                $scotrack->scoid = $sco->new_id;
            }

            //The structure is equal to the db, so insert the scorm_scoes_track
            $newid = insert_record ("scorm_scoes_track",$scotrack);

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
    
    //This function restores the scorm_scoes_track from Moodle 1.4
    function scorm_scoes_tracks_restore_mods_pre15 ($scorm_id,$info,$restore) {

        global $CFG;

        $status = true;
        $scousers = NULL;

        //Get the sco array
        if (!empty($info['MOD']['#']['SCO_USERS']['0']['#']['SCO_USER'])) {
            $scousers = $info['MOD']['#']['SCO_USERS']['0']['#']['SCO_USER'];
        }
        
        $oldelements = array ('CMI_CORE_LESSON_LOCATION',
                              'CMI_CORE_LESSON_STATUS',
                              'CMI_CORE_EXIT',
                              'CMI_CORE_TOTAL_TIME',
                              'CMI_CORE_SCORE_RAW',
                              'CMI_SUSPEND_DATA');
        $newelements = array ('cmi.core.lesson_location',
                              'cmi.core.lesson_status',
                              'cmi.core.exit',
                              'cmi.core.total_time',
                              'cmi.core.score.raw',
                              'cmi.suspend_data');

        //Iterate over sco_users
        for ($i = 0; $i < sizeof($scousers); $i++) {
            $sub_info = $scousers[$i];
            unset($scotrack);

            //We'll need this later!!
            $oldid = backup_todb($sub_info['#']['ID']['0']['#']);
            $oldscoid = backup_todb($sub_info['#']['SCOID']['0']['#']);
            $olduserid = backup_todb($sub_info['#']['USERID']['0']['#']);

            //Now, build the scorm_scoes_track record structure
            $scotrack->scormid = $scorm_id;
            $scotrack->userid = backup_todb($sub_info['#']['USERID']['0']['#']);
            $scotrack->scoid = backup_todb($sub_info['#']['SCOID']['0']['#']);
            $pos = 0;
            foreach ($oldelements as $oldelement) {
                $elementvalue = backup_todb($sub_info['#'][$oldelement]['0']['#']);
                if (!empty($elementvalue)) {
                    $scotrack->element = $newelements[$pos];
                    $scotrack->value = backup_todb($sub_info['#'][strtoupper($oldelement)]['0']['#']);

                    //We have to recode the userid field
                    $user = backup_getid($restore->backup_unique_code,"user",$scotrack->userid);
                    if (!empty($user)) {
                        $scotrack->userid = $user->new_id;
                    }

                    //We have to recode the scoid field
                    $sco = backup_getid($restore->backup_unique_code,"scorm_scoes",$scotrack->scoid);
                    if (!empty($sco)) {
                        $scotrack->scoid = $sco->new_id;
                    }

                    //The structure is equal to the db, so insert the scorm_scoes_track
                    $newid = insert_record ("scorm_scoes_track",$scotrack);
                }
                $pos++;
            }

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
                         "/moddata/scorm/".$package->datadir;
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

    //Return a content decoded to support interactivities linking. Every module
    //should have its own. They are called automatically from
    //scorm_decode_content_links_caller() function in each module
    //in the restore process
    function scorm_decode_content_links ($content,$restore) {
            
        global $CFG;
            
        $result = $content;
                
        //Link to the list of scorms
                
        $searchstring='/\$@(SCORMINDEX)\*([0-9]+)@\$/';
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
                $searchstring='/\$@(SCORMINDEX)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/scorm/index.php?id='.$rec->new_id,$result);
                } else { 
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/scorm/index.php?id='.$old_id,$result);
                }
            }
        }

        //Link to scorm view by moduleid

        $searchstring='/\$@(SCORMVIEWBYID)\*([0-9]+)@\$/';
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
                $searchstring='/\$@(SCORMVIEWBYID)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/scorm/view.php?id='.$rec->new_id,$result);
                } else {
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/scorm/view.php?id='.$old_id,$result);
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
    function scorm_decode_content_links_caller($restore) {
        global $CFG;
        $status = true;

        if ($scorms = get_records_sql ("SELECT s.id, s.summary
                                   FROM {$CFG->prefix}scorm s
                                   WHERE s.course = $restore->course_id")) {

            $i = 0;   //Counter to send some output to the browser to avoid timeouts
            foreach ($scorms as $scorm) {
                //Increment counter
                $i++;
                $content = $scorm->summary;
                $result = restore_decode_content_links_worker($content,$restore);

                if ($result != $content) {
                    //Update record
                    $scorm->summary = addslashes($result);
                    $status = update_record("scorm",$scorm);
                    if ($CFG->debug>7) {
                        echo '<br /><hr />'.htmlentities($content).'<br />changed to<br />'.htmlentities($result).'<hr /><br />';
                    }
                }
                //Do some output
                if (($i+1) % 5 == 0) {
                    echo ".";
                    if (($i+1) % 100 == 0) {
                        echo "<br />";
                    }
                    backup_flush(300);
                }
            }
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
