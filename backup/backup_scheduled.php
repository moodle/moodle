<?PHP //$Id$
    //This file contains all the code needed to execute scheduled backups

//This function implements all the needed code to prepare a course
//to be in backup (insert temp info into backup temp tables).
function schedule_backup_course_configure($course) {  

    global $CFG;
    
    $status = true;

    //Check the required variable
    if (empty($course->id)) {
        $status = false;
    }
    //Get scheduled backup preferences
    $backup_config =  backup_get_config();

    //Checks backup_config pairs exist
    if ($status) {
        if (!isset($backup_config->backup_sche_modules)) {
            $backup_config->backup_sche_modules = 1;
        }
        if (!isset($backup_config->backup_sche_withuserdata)) {
            $backup_config->backup_sche_withuserdata = 1;
        }
        if (!isset($backup_config->backup_sche_users)) {
            $backup_config->backup_sche_users = 1;
        }
        if (!isset($backup_config->backup_sche_logs)) {
            $backup_config->backup_sche_logs = 0;
        }
        if (!isset($backup_config->backup_sche_userfiles)) {
            $backup_config->backup_sche_userfiles = 1;
        }
        if (!isset($backup_config->backup_sche_coursefiles)) {
            $backup_config->backup_sche_coursefiles = 1;
        }
        if (!isset($backup_config->backup_sche_active)) {
            $backup_config->backup_sche_active = 0;
        }
        if (!isset($backup_config->backup_sche_weekdays)) {
            $backup_config->backup_sche_weekdays = "0000000";
        }
        if (!isset($backup_config->backup_sche_hour)) {
            $backup_config->backup_sche_hour = 00;
        }
        if (!isset($backup_config->backup_sche_minute)) {
            $backup_config->backup_sche_minute = 00;
        }
        if (!isset($backup_config->backup_sche_destination)) {
            $backup_config->backup_sche_destination = "";
        }
    }

    if ($status) {
       //Checks for the required files/functions to backup every mod
        //And check if there is data about it
        $count = 0;
        if ($allmods = get_records("modules") ) {
            foreach ($allmods as $mod) {
                $modname = $mod->name;
                $modfile = "$CFG->dirroot/mod/$modname/backuplib.php";
                $modbackup = $modname."_backup_mods";
                $modcheckbackup = $modname."_check_backup_mods";
                if (file_exists($modfile)) {
                   include_once($modfile);
                   if (function_exists($modbackup) and function_exists($modcheckbackup)) {
                       $var = "exists_".$modname;
                       $$var = true;
                       $count++;
                   }
                }
                //Check data
                //Check module info
                $var = "backup_".$modname;
                if (!isset($$var)) {
                    $$var = $backup_config->backup_sche_modules;
                }
                //Now stores all the mods preferences into an array into preferences
                $preferences->mods[$modname]->backup = $$var;

                //Check include user info
                $var = "backup_user_info_".$modname;
                if (!isset($$var)) {
                    $$var = $backup_config->backup_sche_withuserdata;
                }
                //Now stores all the mods preferences into an array into preferences
                $preferences->mods[$modname]->userinfo = $$var;
                //And the name of the mod
                $preferences->mods[$modname]->name = $modname;
            }
        }
    }
    
    //Convert other parameters
    if ($status) {
        $preferences->backup_users = $backup_config->backup_sche_users;
        $preferences->backup_logs = $backup_config->backup_sche_logs;
        $preferences->backup_user_files = $backup_config->backup_sche_userfiles;
        $preferences->backup_course_files = $backup_config->backup_sche_coursefiles;
        $preferences->backup_course = $course->id;
        $preferences->backup_destination = $backup_config->backup_sche_destination;
    }
    
    //Calculate the backup string
    if ($status) {
        //Take off some characters in the filename !!
        $takeoff = array(" ", ":", "/", "\\", "|");
        $backup_name = str_replace($takeoff,"_",strtolower(get_string("backupfilename")));
        //If non-translated, use "backup"
        if (substr($backup_name,0,1) == "[") {
            $backup_name = "backup";
        }
        //Calculate the format string
        $backup_name_format = str_replace(" ","_",get_string("backupnameformat"));
        //If non-translated, use "%Y%m%d-%H%M"
        if (substr($backup_name_format,0,1) == "[") {
            $backup_name_format = "%%Y%%m%%d-%%H%%M";
        }
        $backup_name .= str_replace($takeoff,"_","-".strtolower($course->shortname)."-".userdate(time(),$backup_name_format,99,false).".zip");
        $preferences->backup_name = $backup_name;
    }

    //Calculate the backup unique code to allow simultaneus backups (to define
    //the temp-directory name and records in backup temp tables
    if ($status) {
        $backup_unique_code = time();
        $preferences->backup_unique_code = $backup_unique_code;
    }

    //Calculate necesary info to backup modules
    if ($status) {
        if ($allmods = get_records("modules") ) {
            foreach ($allmods as $mod) {
                $modname = $mod->name;
                $modbackup = $modname."_backup_mods";
                //If exists the lib & function
                $var = "exists_".$modname;
                if (isset($$var) && $$var) {
                    //Add hidden fields
                    $var = "backup_".$modname;
                    //Only if selected
                    if ($$var == 1) {
                        $var = "backup_user_info_".$modname;
                        //Call the check function to show more info
                        $modcheckbackup = $modname."_check_backup_mods";
                        $modcheckbackup($course->id,$$var,$backup_unique_code);
                    }
                }
            }
        }
    }

    //Now calculate the users
    if ($status) {
        user_check_backup($course->id,$backup_unique_code,$preferences->backup_users);  
    }

    //Now calculate the logs
    if ($status) {
        if ($preferences->backup_logs) {
            log_check_backup($course->id);
        }
    }

    //Now calculate the userfiles
    if ($status) {
        if ($preferences->backup_user_files) {
            user_files_check_backup($course->id,$preferences->backup_unique_code);
        }
    }
 
    //Now calculate the coursefiles
    if ($status) {
       if ($preferences->backup_course_files) {
            course_files_check_backup($course->id,$preferences->backup_unique_code);
        }
    }

    //If everything is ok, return calculated preferences
    if ($status) {
        $status = $preferences;
    }

    return $status;
}

//This function implements all the needed code to backup a course
//copying it to the desired destination (default if not specified)
function schedule_backup_course_execute($preferences) {

    global $CFG;

    $status = true;

    //Another Info to add
    $preferences->moodle_version = $CFG->version;
    $preferences->moodle_release = $CFG->release;
    $preferences->backup_version = $CFG->backup_version;
    $preferences->backup_release = $CFG->backup_release;

    //Check for temp and backup and backup_unique_code directory
    //Create them as needed
    $status = check_and_create_backup_dir($preferences->backup_unique_code);
    //Empty dir
    if ($status) {
        $status = clear_backup_dir($preferences->backup_unique_code);
    }

    //Delete old_entries from backup tables
    if ($status) {
        $status = backup_delete_old_data();
    }

    //Create the moodle.xml file
    if ($status) {
        //Obtain the xml file (create and open) and print prolog information
        $backup_file = backup_open_xml($preferences->backup_unique_code);
        //Prints general info about backup to file
        if ($backup_file) {
            $status = backup_general_info($backup_file,$preferences);
        } else {
            $status = false;
        }

        //Prints course start (tag and general info)
        if ($status) {
            $status = backup_course_start($backup_file,$preferences);
        }

        //Section info
        if ($status) {
            $status = backup_course_sections($backup_file,$preferences);
        }

        //User info
        if ($status) {
            $status = backup_user_info($backup_file,$preferences);
        }

        //If we have selected to backup quizzes, backup categories and
        //questions structure (step 1). See notes on mod/quiz/backuplib.php
        if ($status and $preferences->mods['quiz']->backup) {
            $status = quiz_backup_question_categories($backup_file,$preferences);
        }
        
        //Print logs if selected
        if ($status) {
            if ($preferences->backup_logs) {  
                $status = backup_log_info($backup_file,$preferences);
            }
        }

        //Print scales info
        if ($status) {
            $status = backup_scales_info($backup_file,$preferences);
        }

        //Module info, this unique function makes all the work!!
        //db export and module fileis copy
        if ($status) {
            $mods_to_backup = false;
            //Check if we have any mod to backup
            foreach ($preferences->mods as $module) {
                if ($module->backup) { 
                    $mods_to_backup = true;
                }    
            }
            //If we have to backup some module
            if ($mods_to_backup) {
                //Start modules tag
                $status = backup_modules_start ($backup_file,$preferences);
                //Iterate over modules and call backup
                foreach ($preferences->mods as $module) {
                    if ($module->backup and $status) {
                        $status = backup_module($backup_file,$preferences,$module->name);
                    }
                }
                //Close modules tag
                $status = backup_modules_end ($backup_file,$preferences);
            }
        }

        //Prints course end 
        if ($status) {
            $status = backup_course_end($backup_file,$preferences);
        }

        //Close the xml file and xml data
        if ($backup_file) {
            backup_close_xml($backup_file);
        }
    }
    
    //Now, if selected, copy user files
    if ($status) {
        if ($preferences->backup_user_files) {
            $status = backup_copy_user_files ($preferences);
        }
    }

    //Now, if selected, copy course files
    if ($status) {
        if ($preferences->backup_course_files) {
            $status = backup_copy_course_files ($preferences);
        }
    }

    //Now, zip all the backup directory contents
    if ($status) {
        $status = backup_zip ($preferences);
    }

    //Now, copy the zip file to course directory
    if ($status) {
        $status = copy_zip_to_course_dir ($preferences);
    }

    //Now, clean temporary data (db and filesystem)
    if ($status) {
        $status = clean_temp_data ($preferences);
    }

    return $status;
}

?>
