<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //scorm mods

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

    function scorm_backup_mods($bf,$preferences) {
        
        global $CFG;

        $status = true;

        //Iterate over scorm table
        $scorms = get_records ("scorm","course",$preferences->backup_course,"id");
        if ($scorms) {
            foreach ($scorms as $scorm) {
                //Start mod
                fwrite ($bf,start_tag("MOD",3,true));
                //Print scorm data
                fwrite ($bf,full_tag("ID",4,false,$scorm->id));
                fwrite ($bf,full_tag("MODTYPE",4,false,"scorm"));
                fwrite ($bf,full_tag("NAME",4,false,$scorm->name));
                fwrite ($bf,full_tag("REFERENCE",4,false,$scorm->reference));
                fwrite ($bf,full_tag("DATADIR",4,false,$scorm->datadir));
                fwrite ($bf,full_tag("LAUNCH",4,false,$scorm->launch));
                fwrite ($bf,full_tag("SUMMARY",4,false,$scorm->summary));
                fwrite ($bf,full_tag("AUTO",4,false,$scorm->auto));
                fwrite ($bf,full_tag("POPUP",4,false,$scorm->popup));
                fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$scorm->timemodified));
                $status = backup_scorm_scoes($bf,$preferences,$scorm->id);
 
                //if we've selected to backup users info, then execute backup_scorm_sco_users
                if ($status) {
                    if ($preferences->mods["scorm"]->userinfo) {
                        $status = backup_scorm_sco_users($bf,$preferences,$scorm->id);
                    }
                }
                //End mod
                $status =fwrite ($bf,end_tag("MOD",3,true));
            }
            //backup scorm files
            if ($status) {
                $status = backup_scorm_files($bf,$preferences);    
            }
            
        }
        return $status;
    }

    //Backup scorm_scoes contents (executed from scorm_backup_mods)
    function backup_scorm_scoes ($bf,$preferences,$scorm) {

        global $CFG;

        $status = true;

        $scorm_scoes = get_records("scorm_scoes","scorm",$scorm,"id");
        //If there is submissions
        if ($scorm_scoes) {
            //Write start tag
            $status =fwrite ($bf,start_tag("SCOES",4,true));
            //Iterate over each sco
            foreach ($scorm_scoes as $sco) {
                //Start sco
                $status =fwrite ($bf,start_tag("SCO",5,true));
                //Print submission contents
                fwrite ($bf,full_tag("ID",6,false,$sco->id));
                fwrite ($bf,full_tag("PARENT",6,false,$sco->parent));
                fwrite ($bf,full_tag("IDENTIFIER",6,false,$sco->identifier));
                fwrite ($bf,full_tag("LAUNCH",6,false,$sco->launch));
                fwrite ($bf,full_tag("TYPE",6,false,$sco->type));
                fwrite ($bf,full_tag("TITLE",6,false,$sco->title));
                fwrite ($bf,full_tag("NEXT",6,false,$sco->next));
                fwrite ($bf,full_tag("PREVIOUS",6,false,$sco->previous));
                //End sco
                $status =fwrite ($bf,end_tag("SCO",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("SCOES",4,true));
        }
        return $status;
    }
  
   //Backup scorm_sco_users contents (executed from scorm_backup_mods)
    function backup_scorm_sco_users ($bf,$preferences,$scorm) {

        global $CFG;

        $status = true;

        $scorm_sco_users = get_records("scorm_sco_users","scormid",$scorm,"id");
        //If there is submissions
        if ($scorm_sco_users) {
            //Write start tag
            $status =fwrite ($bf,start_tag("SCO_USERS",4,true));
            //Iterate over each sco
            foreach ($scorm_sco_users as $sco_user) {
                //Start sco
                $status =fwrite ($bf,start_tag("SCO_USER",5,true));
                //Print submission contents
                fwrite ($bf,full_tag("ID",6,false,$sco_user->id));
                fwrite ($bf,full_tag("USERID",6,false,$sco_user->userid));
                fwrite ($bf,full_tag("SCOID",6,false,$sco_user->scoid));
                fwrite ($bf,full_tag("CMI_CORE_LESSON_LOCATION",6,false,$sco_user->cmi_core_lesson_location));
                fwrite ($bf,full_tag("CMI_CORE_LESSON_STATUS",6,false,$sco_user->cmi_core_lesson_status));
                fwrite ($bf,full_tag("CMI_CORE_EXIT",6,false,$sco_user->cmi_core_exit));
                fwrite ($bf,full_tag("CMI_CORE_TOTAL_TIME",6,false,$sco_user->cmi_core_total_time));
                fwrite ($bf,full_tag("CMI_CORE_SESSION_TIME",6,false,$sco_user->cmi_core_session_time));
                fwrite ($bf,full_tag("CMI_CORE_SCORE_RAW",6,false,$sco_user->cmi_core_score_raw));
                fwrite ($bf,full_tag("CMI_SUSPEND_DATA",6,false,$sco_user->cmi_suspend_data));
                fwrite ($bf,full_tag("CMI_LAUNCH_DATA",6,false,$sco_user->cmi_launch_data));
                //End sco
                $status =fwrite ($bf,end_tag("SCO_USER",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("SCO_USERS",4,true));
        }
        return $status;
    }
   
   ////Return an array of info (name,value)
   function scorm_check_backup_mods($course,$user_data=false,$backup_unique_code) {
        //First the course data
        $info[0][0] = get_string("modulenameplural","scorm");
        if ($ids = scorm_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }

        //Now, if requested, the user_data
        if ($user_data) {
            $info[1][0] = get_string("scoes","scorm");
            if ($ids = scorm_sco_users_ids_by_course ($course)) {
                $info[1][1] = count($ids);
            } else {
                $info[1][1] = 0;
            }
        }
        return $info;
    }

    //Backup scorm package files
    function backup_scorm_files($bf,$preferences) {

        global $CFG;

        $status = true;

        //First we check to moddata exists and create it as necessary
        //in temp/backup/$backup_code  dir
        $status = check_and_create_moddata_dir($preferences->backup_unique_code);
        //Now copy the scorm dir
        if ($status) {
            if (is_dir($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/scorm")) {
                $status = backup_copy_file($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/scorm",
                                           $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/moddata/scorm");
            }
        }

        return $status;

    }


    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of scorms id
    function scorm_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT a.id, a.course
                                 FROM {$CFG->prefix}scorm a
                                 WHERE a.course = '$course'");
    }
   
    //Returns an array of scorm_scoes id
    function scorm_sco_users_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.scormid
                                 FROM {$CFG->prefix}scorm_sco_users s,
                                      {$CFG->prefix}scorm a
                                 WHERE a.course = '$course' AND
                                       s.scormid = a.id");
    }
?>
