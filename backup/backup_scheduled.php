<?PHP //$Id$
    //This file contains all the code needed to execute scheduled backups

//This function is executed via moodle cron
//It prepares all the info and execute backups as necessary
function schedule_backup_cron() {

    global $CFG;
    
    $status = true;

    $emailpending = false;

    //Get now
    $now = time();

    //First of all, we have to see if the scheduled is active and detect
    //that there isn't another cron running
    echo "    Checking backup status";
    $backup_config = backup_get_config();
    if(!isset($backup_config->backup_sche_active) || !$backup_config->backup_sche_active) {
        echo "...INACTIVE\n";
        return true;
    } else if (isset($backup_config->backup_sche_running) && $backup_config->backup_sche_running) {
        echo "...RUNNING\n";
        return true;
    } else {
        echo "...OK\n";
        //Mark backup_sche_running
        backup_set_config("backup_sche_running","1");
    }

    //Now we get the main admin user (we'll use his timezone, mail...)
    echo "    Getting admin info\n";
    $admin = get_admin();
    if (!$admin) {
        $status = false;
    }

    //Now we get a list of courses in the server
    if ($status) {
        echo "    Checking courses\n";
        $courses = get_records("course");
        //For each course, we check (insert, update) the backup_course table
        //with needed data
        foreach ($courses as $course) {
            if ($status) {
                echo "        $course->fullname\n";
                //We check if the course exists in backup_course
                $backup_course = get_record("backup_courses","courseid",$course->id);
                //If it doesn't exist, create
                if (!$backup_course) {
                    $temp_backup_course->courseid = $course->id;
                    $newid = insert_record("backup_courses",$temp_backup_course);
                    //And get it from db
                    $backup_course = get_record("backup_courses","id",$newid);
                }
                //If it doesn't exist now, error
                if (!$backup_course) {
                    echo "            ERROR (in backup_courses detection)\n";
                    $status = false;
                }
                //Now we backup every course with nextstarttime < now
                if ($backup_course->nextstarttime > 0 && $backup_course->nextstarttime < $now) {
                    //Set laststarttime
                    $starttime = time();
                    set_field("backup_courses","laststarttime",$starttime,"courseid",$backup_course->courseid);
                    //Launch backup
                    $course_status = schedule_backup_launch_backup($course,$starttime);
                    //We have to send a email because we have included at least one backup
                    $emailpending = true;
                    //Set lastendtime
                    set_field("backup_courses","lastendtime",time(),"courseid",$backup_course->courseid);
                    //Set laststatus
                    if ($course_status) {
                        set_field("backup_courses","laststatus","1","courseid",$backup_course->courseid);
                    } else {
                        set_field("backup_courses","laststatus","0","courseid",$backup_course->courseid);
                    }
                }

                //Now, calculate next execution of the course
                $nextstarttime = schedule_backup_next_execution ($backup_course,$backup_config,$now,$admin->timezone);
                //Save it to db
                set_field("backup_courses","nextstarttime",$nextstarttime,"courseid",$backup_course->courseid);
                //Print it to screen as necessary
                $showtime = "undefined";
                if ($nextstarttime > 0) {
                    $showtime = userdate($nextstarttime,"",$admin->timezone);
                }
                echo "            Next execution: $showtime\n";
            }
        }
    }

    //Delete old logs
    if (!empty($CFG->loglifetime)) {
        echo "    Deleting old logs\n";
        $loglifetime = $now - ($CFG->loglifetime * 86400);
        delete_records_select("backup_log", "laststarttime < '$loglifetime'");
    }

    //Send email to admin
    if ($emailpending) {
        echo "    Sending email to admin\n";
        $message = "";
        //Build the message text (future versions should handle html messages too!!)
        $logs = get_records_select ("backup_log","laststarttime >= '$now'","id");
        if ($logs) {
            $currentcourse = 1;
            foreach ($logs as $log) {
                if ($currentcourse != $log->courseid) {
                    $message .= "\n==================================================\n\n";
                    $currentcourse = $log->courseid;
                }
                $message .= userdate($log->time,"%T",$admin->timezone)." ".$log->info."\n";
            }
        }
        //Send the message
        $site = get_site();
        email_to_user($admin,$admin,"$site->shortname: ".get_string("scheduledbackupstatus"),$message);
    }
    

    //Everything is finished stop backup_sche_running
    backup_set_config("backup_sche_running","0");

    return $status;
}

//This function executes the ENTIRE backup of a course (passed as parameter)
//using all the scheduled backup preferences
function schedule_backup_launch_backup($course,$starttime = 0) {

    $preferences = false;
    $status = false;

    echo "            Executing backup\n";
    schedule_backup_log($starttime,$course->id,"Start backup course $course->fullname");
    schedule_backup_log($starttime,$course->id,"  Phase 1: Checking and counting:");
    $preferences = schedule_backup_course_configure($course,$starttime);
    if ($preferences) {
        schedule_backup_log($starttime,$course->id,"  Phase 2: Executing and copying:");
        $status = schedule_backup_course_execute($preferences,$starttime);
    }
    if ($status && $preferences) {
        echo "            End backup OK\n";
        schedule_backup_log($starttime,$course->id,"End backup course $course->fullname - OK");
    } else {
        echo "            End backup with ERROR\n";
        schedule_backup_log($starttime,$course->id,"End backup course $course->fullname - ERROR!!");
    }

    return $status && $preferences;
}

//This function saves to backup_log all the needed process info
//to use it later.  NOTE: If $starttime = 0 no info in saved
function schedule_backup_log($starttime,$courseid,$message) {

    if ($starttime) {
        $log->courseid = $courseid;
        $log->time = time();
        $log->laststarttime = $starttime;
        $log->info = $message;
    
        insert_record ("backup_log",$log);
    }

}

//This function returns the next future GMT time to execute the course based in the
//configuration of the scheduled backups
function schedule_backup_next_execution ($backup_course,$backup_config,$now,$timezone) {

    $result = -1;

    //Get today's midnight GMT
    $midnight = usergetmidnight($now,$timezone);

    //Get today's day of week (0=Sunday...6=Saturday)
    $date = usergetdate($now,$timezone);
    $dayofweek = $date['wday'];

    //Get number of days (from today) to execute backups
    $scheduled_days = substr($backup_config->backup_sche_weekdays,$dayofweek).
                      $backup_config->backup_sche_weekdays;
    $daysfromtoday = strpos($scheduled_days, "1");

    //If some day has been found
    if ($daysfromtoday !== false) {
        //Calculate distance
        $dist = ($daysfromtoday * 86400) +                     //Days distance
                ($backup_config->backup_sche_hour*3600) +      //Hours distance
                ($backup_config->backup_sche_minute*60);       //Minutes distance
        $result = $midnight + $dist;
    } 

    //If that time is past, call the function recursively to obtain the next valid day
    if ($result > 0 && $result < time()) {
        $result = schedule_backup_next_execution ($backup_course,$backup_config,$now + 86400,$timezone);
    }

    return $result;
}



//This function implements all the needed code to prepare a course
//to be in backup (insert temp info into backup temp tables).
function schedule_backup_course_configure($course,$starttime = 0) {  

    global $CFG;
    
    $status = true;

    schedule_backup_log($starttime,$course->id,"    checking parameters");

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
        schedule_backup_log($starttime,$course->id,"    calculating backup name");
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
        schedule_backup_log($starttime,$course->id,"    calculating modules data");
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
                        schedule_backup_log($starttime,$course->id,"      $modname");
                        $modcheckbackup($course->id,$$var,$backup_unique_code);
                    }
                }
            }
        }
    }

    //Now calculate the users
    if ($status) {
        schedule_backup_log($starttime,$course->id,"    calculating users");
        user_check_backup($course->id,$backup_unique_code,$preferences->backup_users);  
    }

    //Now calculate the logs
    if ($status) {
        if ($preferences->backup_logs) {
            schedule_backup_log($starttime,$course->id,"    calculating logs");
            log_check_backup($course->id);
        }
    }

    //Now calculate the userfiles
    if ($status) {
        if ($preferences->backup_user_files) {
            schedule_backup_log($starttime,$course->id,"    calculating user files");
            user_files_check_backup($course->id,$preferences->backup_unique_code);
        }
    }
 
    //Now calculate the coursefiles
    if ($status) {
       if ($preferences->backup_course_files) {
            schedule_backup_log($starttime,$course->id,"    calculating course files");
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
function schedule_backup_course_execute($preferences,$starttime = 0) {

    global $CFG;

    $status = true;

    //Another Info to add
    $preferences->moodle_version = $CFG->version;
    $preferences->moodle_release = $CFG->release;
    $preferences->backup_version = $CFG->backup_version;
    $preferences->backup_release = $CFG->backup_release;

    //Check for temp and backup and backup_unique_code directory
    //Create them as needed
    schedule_backup_log($starttime,$preferences->backup_course,"    checking temp structures");
    $status = check_and_create_backup_dir($preferences->backup_unique_code);
    //Empty dir
    if ($status) {
        schedule_backup_log($starttime,$preferences->backup_course,"    cleaning old data");
        $status = clear_backup_dir($preferences->backup_unique_code);
    }

    //Delete old_entries from backup tables
    if ($status) {
        $status = backup_delete_old_data();
    }

    //Create the moodle.xml file
    if ($status) {
        schedule_backup_log($starttime,$preferences->backup_course,"    creating backup file");
        //Obtain the xml file (create and open) and print prolog information
        $backup_file = backup_open_xml($preferences->backup_unique_code);
        //Prints general info about backup to file
        if ($backup_file) {
            schedule_backup_log($starttime,$preferences->backup_course,"      general info");
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
            schedule_backup_log($starttime,$preferences->backup_course,"      sections info");
            $status = backup_course_sections($backup_file,$preferences);
        }

        //User info
        if ($status) {
            schedule_backup_log($starttime,$preferences->backup_course,"      user info");
            $status = backup_user_info($backup_file,$preferences);
        }

        //If we have selected to backup quizzes, backup categories and
        //questions structure (step 1). See notes on mod/quiz/backuplib.php
        if ($status and $preferences->mods['quiz']->backup) {
            schedule_backup_log($starttime,$preferences->backup_course,"      categories & questions");
            $status = quiz_backup_question_categories($backup_file,$preferences);
        }
        
        //Print logs if selected
        if ($status) {
            if ($preferences->backup_logs) {  
                schedule_backup_log($starttime,$preferences->backup_course,"      logs");
                $status = backup_log_info($backup_file,$preferences);
            }
        }

        //Print scales info
        if ($status) {
            schedule_backup_log($starttime,$preferences->backup_course,"      scales");
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
                schedule_backup_log($starttime,$preferences->backup_course,"      modules");
                //Start modules tag
                $status = backup_modules_start ($backup_file,$preferences);
                //Iterate over modules and call backup
                foreach ($preferences->mods as $module) {
                    if ($module->backup and $status) {
                        schedule_backup_log($starttime,$preferences->backup_course,"        $module->name");
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
            schedule_backup_log($starttime,$preferences->backup_course,"    copying user files");
            $status = backup_copy_user_files ($preferences);
        }
    }

    //Now, if selected, copy course files
    if ($status) {
        if ($preferences->backup_course_files) {
            schedule_backup_log($starttime,$preferences->backup_course,"    copying course files");
            $status = backup_copy_course_files ($preferences);
        }
    }

    //Now, zip all the backup directory contents
    if ($status) {
        schedule_backup_log($starttime,$preferences->backup_course,"    zipping files");
        $status = backup_zip ($preferences);
    }

    //Now, copy the zip file to course directory
    if ($status) {
        schedule_backup_log($starttime,$preferences->backup_course,"    copying backup");
        $status = copy_zip_to_course_dir ($preferences);
    }

    //Now, clean temporary data (db and filesystem)
    if ($status) {
        schedule_backup_log($starttime,$preferences->backup_course,"    cleaning temp data");
        $status = clean_temp_data ($preferences);
    }

    return $status;
}

?>
