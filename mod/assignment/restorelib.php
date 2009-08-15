<?php //$Id$
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
            //if necessary, write to restorelog and adjust date/time fields
            if ($restore->course_startdateoffset) {
                restore_log_date_changes('Assignment', $restore, $info['MOD']['#'], array('TIMEDUE', 'TIMEAVAILABLE'));
            }
            //traverse_xmlize($info);                                                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the ASSIGNMENT record structure
            $assignment->course = $restore->course_id;
            $assignment->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $assignment->description = backup_todb($info['MOD']['#']['DESCRIPTION']['0']['#']);
            $assignment->format = backup_todb($info['MOD']['#']['FORMAT']['0']['#']);
            $assignment->resubmit = backup_todb($info['MOD']['#']['RESUBMIT']['0']['#']);
            $assignment->preventlate = backup_todb($info['MOD']['#']['PREVENTLATE']['0']['#']);
            $assignment->emailteachers = backup_todb($info['MOD']['#']['EMAILTEACHERS']['0']['#']);
            $assignment->var1 = backup_todb($info['MOD']['#']['VAR1']['0']['#']);
            $assignment->var2 = backup_todb($info['MOD']['#']['VAR2']['0']['#']);
            $assignment->var3 = backup_todb($info['MOD']['#']['VAR3']['0']['#']);
            $assignment->var4 = backup_todb($info['MOD']['#']['VAR4']['0']['#']);
            $assignment->var5 = backup_todb($info['MOD']['#']['VAR5']['0']['#']);
            $assignment->type = isset($info['MOD']['#']['TYPE']['0']['#'])?backup_todb($info['MOD']['#']['TYPE']['0']['#']):'';
            $assignment->assignmenttype = backup_todb($info['MOD']['#']['ASSIGNMENTTYPE']['0']['#']);
            $assignment->maxbytes = backup_todb($info['MOD']['#']['MAXBYTES']['0']['#']);
            $assignment->timedue = backup_todb($info['MOD']['#']['TIMEDUE']['0']['#']);
            $assignment->timeavailable = backup_todb($info['MOD']['#']['TIMEAVAILABLE']['0']['#']);
            $assignment->grade = backup_todb($info['MOD']['#']['GRADE']['0']['#']);
            $assignment->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);

            //We have to recode the grade field if it is <0 (scale)
            if ($assignment->grade < 0) {
                $scale = backup_getid($restore->backup_unique_code,"scale",abs($assignment->grade));        
                if ($scale) {
                    $assignment->grade = -($scale->new_id);       
                }
            }

            if (empty($assignment->assignmenttype)) {   /// Pre 1.5 assignment
                if ($assignment->type == 1) {
                    $assignment->assignmenttype = 'uploadsingle';
                } else {
                    $assignment->assignmenttype = 'offline';
                }
            }

            // skip restore of plugins that are not installed
            static $plugins;
            if (!isset($plugins)) {
                $plugins = get_list_of_plugins('mod/assignment/type');
            }

            if (!in_array($assignment->assignmenttype, $plugins)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li><strong>".get_string("modulename","assignment")." \"".format_string(stripslashes($assignment->name),true)."\" - plugin '{$assignment->assignmenttype}' not available!</strong></li>";
                }
                return true; // do not fail the restore
            }

            //The structure is equal to the db, so insert the assignment
            $newid = insert_record ("assignment",$assignment);

            //Do some output     
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("modulename","assignment")." \"".format_string(stripslashes($assignment->name),true)."\"</li>";
            }
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                // load up the subtype and see if it wants anything further restored.
                $class = 'assignment_' . $assignment->assignmenttype;
                require_once($CFG->dirroot . '/mod/assignment/lib.php');
                require_once($CFG->dirroot . '/mod/assignment/type/' . $assignment->assignmenttype . '/assignment.class.php');
                call_user_func(array($class, 'restore_one_mod'), $info, $restore, $assignment);

                //Now check if want to restore user data and do it.
                if (restore_userdata_selected($restore,'assignment',$mod->id)) { 
                    //Restore assignmet_submissions
                    $status = assignment_submissions_restore_mods($mod->id, $newid,$info,$restore, $assignment) && $status;
                }
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }

        return $status;
    }

    //This function restores the assignment_submissions
    function assignment_submissions_restore_mods($old_assignment_id, $new_assignment_id,$info,$restore, $assignment) {

        global $CFG;

        $status = true;

        //Get the submissions array - it might not be present
        if (isset($info['MOD']['#']['SUBMISSIONS']['0']['#']['SUBMISSION'])) {
            $submissions = $info['MOD']['#']['SUBMISSIONS']['0']['#']['SUBMISSION'];
        } else {
            $submissions = array();
        }

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
            $submission->data1 = backup_todb($sub_info['#']['DATA1']['0']['#']);
            $submission->data2 = backup_todb($sub_info['#']['DATA2']['0']['#']);
            $submission->grade = backup_todb($sub_info['#']['GRADE']['0']['#']);
            if (isset($sub_info['#']['COMMENT']['0']['#'])) {
                $submission->submissioncomment = backup_todb($sub_info['#']['COMMENT']['0']['#']);
            } else {
                $submission->submissioncomment = backup_todb($sub_info['#']['SUBMISSIONCOMMENT']['0']['#']);
            }  
            $submission->format = backup_todb($sub_info['#']['FORMAT']['0']['#']);
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
                backup_putid($restore->backup_unique_code,"assignment_submission",$oldid,
                             $newid);

                //Now copy moddata associated files
                $status = assignment_restore_files ($old_assignment_id, $new_assignment_id, 
                                                    $olduserid, $submission->userid, $restore);

                $submission->id = $newid;
                $class = 'assignment_' . $assignment->assignmenttype;
                require_once($CFG->dirroot . '/mod/assignment/lib.php');
                require_once($CFG->dirroot . '/mod/assignment/type/' . $assignment->assignmenttype . '/assignment.class.php');
                call_user_func(array($class, 'restore_one_submission'), $sub_info, $restore, $assignment, $submission);
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

    //Return a content decoded to support interactivities linking. Every module
    //should have its own. They are called automatically from
    //assignment_decode_content_links_caller() function in each module
    //in the restore process
    function assignment_decode_content_links ($content,$restore) {
            
        global $CFG;
            
        $result = $content;
                
        //Link to the list of assignments
                
        $searchstring='/\$@(ASSIGNMENTINDEX)\*([0-9]+)@\$/';
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
                $searchstring='/\$@(ASSIGNMENTINDEX)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/assignment/index.php?id='.$rec->new_id,$result);
                } else { 
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/assignment/index.php?id='.$old_id,$result);
                }
            }
        }

        //Link to assignment view by moduleid

        $searchstring='/\$@(ASSIGNMENTVIEWBYID)\*([0-9]+)@\$/';
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
                $searchstring='/\$@(ASSIGNMENTVIEWBYID)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/assignment/view.php?id='.$rec->new_id,$result);
                } else {
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/assignment/view.php?id='.$old_id,$result);
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
    function assignment_decode_content_links_caller($restore) {
        global $CFG;
        $status = true;

        if ($assignments = get_records_sql ("SELECT a.id, a.description
                                   FROM {$CFG->prefix}assignment a
                                   WHERE a.course = $restore->course_id")) {
            //Iterate over each assignment->description
            $i = 0;   //Counter to send some output to the browser to avoid timeouts
            foreach ($assignments as $assignment) {
                //Increment counter
                $i++;
                $content = $assignment->description;
                $result = restore_decode_content_links_worker($content,$restore);
                if ($result != $content) {
                    //Update record
                    $assignment->description = addslashes($result);
                    $status = update_record("assignment",$assignment);
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

    //This function converts texts in FORMAT_WIKI to FORMAT_MARKDOWN for
    //some texts in the module
    function assignment_restore_wiki2markdown ($restore) {
    
        global $CFG;

        $status = true;

        //Convert assignment->description
        if ($records = get_records_sql ("SELECT a.id, a.description, a.format
                                         FROM {$CFG->prefix}assignment a,
                                              {$CFG->prefix}backup_ids b
                                         WHERE a.course = $restore->course_id AND
                                               a.format = ".FORMAT_WIKI. " AND
                                               b.backup_code = $restore->backup_unique_code AND
                                               b.table_name = 'assignment' AND
                                               b.new_id = a.id")) {
            foreach ($records as $record) {
                //Rebuild wiki links
                $record->description = restore_decode_wiki_content($record->description, $restore);
                //Convert to Markdown
                $wtm = new WikiToMarkdown();
                $record->description = $wtm->convert($record->description, $restore->course_id);
                $record->format = FORMAT_MARKDOWN;
                $status = update_record('assignment', addslashes_object($record));
                //Do some output
                $i++;
                if (($i+1) % 1 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 20 == 0) {
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
