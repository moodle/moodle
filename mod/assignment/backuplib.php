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

    //This function executes all the backup procedure about this mod
    function assignment_backup_mods($bf,$preferences) {

        global $CFG;

        $status = true;

        //Iterate over assignment table
        $assignments = get_records ("assignment","course",$preferences->backup_course,"id");
        if ($assignments) {
            foreach ($assignments as $assignment) {
                if (backup_mod_selected($preferences,'assignment',$assignment->id)) {
                    $status = assignment_backup_one_mod($bf,$preferences,$assignment);
                    // backup files happens in backup_one_mod now too.
                }
            }
        }
        return $status;  
    }

    function assignment_backup_one_mod($bf,$preferences,$assignment) {
        
        global $CFG;
    
        if (is_numeric($assignment)) {
            $assignment = get_record('assignment','id',$assignment);
        }
    
        $status = true;

        //Start mod
        fwrite ($bf,start_tag("MOD",3,true));
        //Print assignment data
        fwrite ($bf,full_tag("ID",4,false,$assignment->id));
        fwrite ($bf,full_tag("MODTYPE",4,false,"assignment"));
        fwrite ($bf,full_tag("NAME",4,false,$assignment->name));
        fwrite ($bf,full_tag("DESCRIPTION",4,false,$assignment->description));
        fwrite ($bf,full_tag("FORMAT",4,false,$assignment->format));
        fwrite ($bf,full_tag("RESUBMIT",4,false,$assignment->resubmit));
        fwrite ($bf,full_tag("PREVENTLATE",4,false,$assignment->preventlate));
        fwrite ($bf,full_tag("EMAILTEACHERS",4,false,$assignment->emailteachers));
        fwrite ($bf,full_tag("VAR1",4,false,$assignment->var1));
        fwrite ($bf,full_tag("VAR2",4,false,$assignment->var2));
        fwrite ($bf,full_tag("VAR3",4,false,$assignment->var3));
        fwrite ($bf,full_tag("VAR4",4,false,$assignment->var4));
        fwrite ($bf,full_tag("VAR5",4,false,$assignment->var5));
        fwrite ($bf,full_tag("ASSIGNMENTTYPE",4,false,$assignment->assignmenttype));
        fwrite ($bf,full_tag("MAXBYTES",4,false,$assignment->maxbytes));
        fwrite ($bf,full_tag("TIMEDUE",4,false,$assignment->timedue));
        fwrite ($bf,full_tag("TIMEAVAILABLE",4,false,$assignment->timeavailable));
        fwrite ($bf,full_tag("GRADE",4,false,$assignment->grade));
        fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$assignment->timemodified));

        $class = 'assignment_' . $assignment->assignmenttype;
        require_once($CFG->dirroot . '/mod/assignment/lib.php');
        require_once($CFG->dirroot . '/mod/assignment/type/' . $assignment->assignmenttype . '/assignment.class.php');
        call_user_func(array($class, 'backup_one_mod'), $bf, $preferences, $assignment);

        //if we've selected to backup users info, then execute backup_assignment_submisions and
        //backup_assignment_files_instance
        if (backup_userdata_selected($preferences,'assignment',$assignment->id)) {
            $status = backup_assignment_submissions($bf,$preferences,$assignment);
            if ($status) {
                $status = backup_assignment_files_instance($bf,$preferences,$assignment->id);
            }
        }
        //End mod
        $status =fwrite ($bf,end_tag("MOD",3,true));

        return $status;
    }

    //Backup assignment_submissions contents (executed from assignment_backup_mods)
    function backup_assignment_submissions ($bf,$preferences,$assignment) {

        global $CFG;

        $status = true;

        $assignment_submissions = get_records("assignment_submissions","assignment",$assignment->id,"id");
        //If there is submissions
        if ($assignment_submissions) {
            //Write start tag
            $status =fwrite ($bf,start_tag("SUBMISSIONS",4,true));
            //Iterate over each submission
            foreach ($assignment_submissions as $ass_sub) {
                //Start submission
                $status =fwrite ($bf,start_tag("SUBMISSION",5,true));
                //Print submission contents
                fwrite ($bf,full_tag("ID",6,false,$ass_sub->id));       
                fwrite ($bf,full_tag("USERID",6,false,$ass_sub->userid));       
                fwrite ($bf,full_tag("TIMECREATED",6,false,$ass_sub->timecreated));       
                fwrite ($bf,full_tag("TIMEMODIFIED",6,false,$ass_sub->timemodified));       
                fwrite ($bf,full_tag("NUMFILES",6,false,$ass_sub->numfiles));       
                fwrite ($bf,full_tag("DATA1",6,false,$ass_sub->data1));       
                fwrite ($bf,full_tag("DATA2",6,false,$ass_sub->data2));       
                fwrite ($bf,full_tag("GRADE",6,false,$ass_sub->grade));       
                fwrite ($bf,full_tag("SUBMISSIONCOMMENT",6,false,$ass_sub->submissioncomment));       
                fwrite ($bf,full_tag("FORMAT",6,false,$ass_sub->format));       
                fwrite ($bf,full_tag("TEACHER",6,false,$ass_sub->teacher));       
                fwrite ($bf,full_tag("TIMEMARKED",6,false,$ass_sub->timemarked));       
                fwrite ($bf,full_tag("MAILED",6,false,$ass_sub->mailed));       

                $class = 'assignment_' . $assignment->assignmenttype;
                require_once($CFG->dirroot . '/mod/assignment/lib.php');
                require_once($CFG->dirroot . '/mod/assignment/type/' . $assignment->assignmenttype . '/assignment.class.php');
                call_user_func(array($class, 'backup_one_submission'), $bf, $preferences, $assignment, $ass_sub);
                //End submission
                $status =fwrite ($bf,end_tag("SUBMISSION",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("SUBMISSIONS",4,true));
        }
        return $status;
    }

    //Backup assignment files because we've selected to backup user info
    //and files are user info's level
    function backup_assignment_files($bf,$preferences) {

        global $CFG;
       
        $status = true;

        //First we check to moddata exists and create it as necessary
        //in temp/backup/$backup_code  dir
        $status = check_and_create_moddata_dir($preferences->backup_unique_code);
        //Now copy the assignment dir
        if ($status) {
            //Only if it exists !! Thanks to Daniel Miksik.
            if (is_dir($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/assignment")) {
                $status = backup_copy_file($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/assignment",
                                           $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/moddata/assignment");
            }
        }

        return $status;

    } 

    function backup_assignment_files_instance($bf,$preferences,$instanceid) {

        global $CFG;
       
        $status = true;

        //First we check to moddata exists and create it as necessary
        //in temp/backup/$backup_code  dir
        $status = check_and_create_moddata_dir($preferences->backup_unique_code);
        $status = check_dir_exists($CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/moddata/assignment/",true);
        //Now copy the assignment dir
        if ($status) {
            //Only if it exists !! Thanks to Daniel Miksik.
            if (is_dir($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/assignment/".$instanceid)) {
                $status = backup_copy_file($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/assignment/".$instanceid,
                                           $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/moddata/assignment/".$instanceid);
            }
        }

        return $status;

    } 

    //Return an array of info (name,value)
    function assignment_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null) {
        if (!empty($instances) && is_array($instances) && count($instances)) {
            $info = array();
            foreach ($instances as $id => $instance) {
                $info += assignment_check_backup_mods_instances($instance,$backup_unique_code);
            }
            return $info;
        }
        //First the course data
        $info[0][0] = get_string("modulenameplural","assignment");
        if ($ids = assignment_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }

        //Now, if requested, the user_data
        if ($user_data) {
            $info[1][0] = get_string("submissions","assignment");
            if ($ids = assignment_submission_ids_by_course ($course)) { 
                $info[1][1] = count($ids);
            } else {
                $info[1][1] = 0;
            }
        }
        return $info;
    }

    //Return an array of info (name,value)
    function assignment_check_backup_mods_instances($instance,$backup_unique_code) {
        $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
        $info[$instance->id.'0'][1] = '';
        if (!empty($instance->userdata)) {
            $info[$instance->id.'1'][0] = get_string("submissions","assignment");
            if ($ids = assignment_submission_ids_by_instance ($instance->id)) {
                $info[$instance->id.'1'][1] = count($ids);
            } else {
                $info[$instance->id.'1'][1] = 0;
            }
        }
        return $info;
    }

    //Return a content encoded to support interactivities linking. Every module
    //should have its own. They are called automatically from the backup procedure.
    function assignment_encode_content_links ($content,$preferences) {

        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        //Link to the list of assignments
        $buscar="/(".$base."\/mod\/assignment\/index.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@ASSIGNMENTINDEX*$2@$',$content);

        //Link to assignment view by moduleid
        $buscar="/(".$base."\/mod\/assignment\/view.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@ASSIGNMENTVIEWBYID*$2@$',$result);

        return $result;
    }

    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of assignments id 
    function assignment_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT a.id, a.course
                                 FROM {$CFG->prefix}assignment a
                                 WHERE a.course = '$course'");
    }
    
    //Returns an array of assignment_submissions id
    function assignment_submission_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.assignment
                                 FROM {$CFG->prefix}assignment_submissions s,
                                      {$CFG->prefix}assignment a
                                 WHERE a.course = '$course' AND
                                       s.assignment = a.id");
    }

    //Returns an array of assignment_submissions id
    function assignment_submission_ids_by_instance ($instanceid) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.assignment
                                 FROM {$CFG->prefix}assignment_submissions s
                                 WHERE s.assignment = $instanceid");
    }
?>
