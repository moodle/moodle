<?php //$Id$
    //This file contains all the code needed to execute scheduled backups

//This function is executed via moodle cron
//It prepares all the info and execute backups as necessary
function schedule_backup_cron() {

    global $CFG;

    $status = true;

    $emailpending = false;

    //Check for required functions...
    if(!function_exists('utf8_encode')) {
        mtrace("        ERROR: You need to add XML support to your PHP installation!");
        return true;
    }

    //Get now
    $now = time();

    //First of all, we have to see if the scheduled is active and detect
    //that there isn't another cron running
    mtrace("    Checking backup status",'...');
    $backup_config = backup_get_config();
    if(!isset($backup_config->backup_sche_active) || !$backup_config->backup_sche_active) {
        mtrace("INACTIVE");
        return true;
    } else if (isset($backup_config->backup_sche_running) && $backup_config->backup_sche_running) {
        mtrace("RUNNING");
        //Now check if it's a really running task or something very old looking
        //for info in backup_logs to unlock status as necessary
        $timetosee = 1800;   //Half an hour looking for activity
        $timeafter = time() - $timetosee;
        $numofrec = count_records_select ("backup_log","time > $timeafter");
        if (!$numofrec) {
            $timetoseemin = $timetosee/60;
            mtrace("    No activity in last ".$timetoseemin." minutes. Unlocking status");
        } else {
            mtrace("    Scheduled backup seems to be running. Execution delayed");
            return true;
        }
    } else {
        mtrace("OK");
        //Mark backup_sche_running
        backup_set_config("backup_sche_running","1");
    }

    //Now we get the main admin user (we'll use his timezone, mail...)
    mtrace("    Getting admin info");
    $admin = get_admin();
    if (!$admin) {
        $status = false;
    }

    //Delete old_entries from backup tables
    if ($status) {
        mtrace("    Deleting old data");
        $status = backup_delete_old_data();
    }

    //Now we get a list of courses in the server
    if ($status) {
        mtrace("    Checking courses");
        //First of all, we delete everything from backup tables related to deleted courses
        mtrace("        Skipping deleted courses");
        $skipped = 0;
        if ($bckcourses = get_records('backup_courses')) {
            foreach($bckcourses as $bckcourse) {
                //Search if it exists
                if (!$exists = get_record('course', 'id', "$bckcourse->courseid")) {
                    //Doesn't exist, so delete from backup tables
                    delete_records('backup_courses', 'courseid', "$bckcourse->courseid");
                    delete_records('backup_log', 'courseid', "$bckcourse->courseid");
                    $skipped++;
                }
            }
        }
        mtrace("            $skipped courses");
        //Now process existing courses
        $courses = get_records("course");
        //For each course, we check (insert, update) the backup_course table
        //with needed data
        foreach ($courses as $course) {
            if ($status) {
                mtrace("        $course->fullname");
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
                    mtrace("            ERROR (in backup_courses detection)");
                    $status = false;
                    continue;
                }
                // Skip backup of unavailable courses that have remained unmodified in a month
                $skipped = false;
                if (!$course->visible && ($now - $course->timemodified) > 31*24*60*60) {  //Hidden + unmodified last month
                    mtrace("            SKIPPING - hidden+unmodified");
                    set_field("backup_courses","laststatus","3","courseid",$backup_course->courseid);
                    $skipped = true;
                }
                //Now we backup every non skipped course with nextstarttime < now
                if (!$skipped  && $backup_course->nextstarttime > 0 && $backup_course->nextstarttime < $now) {
                    //We have to send a email because we have included at least one backup
                    $emailpending = true;
                    //Only make the backup if laststatus isn't 2-UNFINISHED (uncontrolled error)
                    if ($backup_course->laststatus != 2) {
                        //Set laststarttime
                        $starttime = time();
                        set_field("backup_courses","laststarttime",$starttime,"courseid",$backup_course->courseid);
                        //Set course status to unfinished, the process will reset it
                        set_field("backup_courses","laststatus","2","courseid",$backup_course->courseid);
                        //Launch backup
                        $course_status = schedule_backup_launch_backup($course,$starttime);
                        //Set lastendtime
                        set_field("backup_courses","lastendtime",time(),"courseid",$backup_course->courseid);
                        //Set laststatus
                        if ($course_status) {
                            set_field("backup_courses","laststatus","1","courseid",$backup_course->courseid);
                        } else {
                            set_field("backup_courses","laststatus","0","courseid",$backup_course->courseid);
                        }
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
                mtrace("            Next execution: $showtime");
            }
        }
    }

    //Delete old logs
    if (!empty($CFG->loglifetime)) {
        mtrace("    Deleting old logs");
        $loglifetime = $now - ($CFG->loglifetime * 86400);
        delete_records_select("backup_log", "laststarttime < '$loglifetime'");
    }

    //Send email to admin if necessary
    if ($emailpending) {
        mtrace("    Sending email to admin");
        $message = "";

        //Get info about the status of courses
        $count_all = count_records('backup_courses');
        $count_ok = count_records('backup_courses','laststatus','1');
        $count_error = count_records('backup_courses','laststatus','0');
        $count_unfinished = count_records('backup_courses','laststatus','2');
        $count_skipped = count_records('backup_courses','laststatus','3');

        //Build the message text
        //Summary
        $message .= get_string('summary')."\n";
        $message .= "==================================================\n";
        $message .= "  ".get_string('courses').": ".$count_all."\n";
        $message .= "  ".get_string('ok').": ".$count_ok."\n";
        $message .= "  ".get_string('skipped').": ".$count_skipped."\n";
        $message .= "  ".get_string('error').": ".$count_error."\n";
        $message .= "  ".get_string('unfinished').": ".$count_unfinished."\n\n";

        //Reference
        if ($count_error != 0 || $count_unfinished != 0) {
            $message .= "  ".get_string('backupfailed')."\n\n";
            $dest_url = "$CFG->wwwroot/$CFG->admin/report/backups/index.php";
            $message .= "  ".get_string('backuptakealook','',$dest_url)."\n\n";
            //Set message priority
            $admin->priority = 1;
            //Reset unfinished to error
            set_field('backup_courses','laststatus','0','laststatus','2');
        } else {
            $message .= "  ".get_string('backupfinished')."\n";
        }

        //Build the message subject
        $site = get_site();
        $prefix = $site->shortname.": ";
        if ($count_error != 0 || $count_unfinished != 0) {
            $prefix .= "[".strtoupper(get_string('error'))."] ";
        }
        $subject = $prefix.get_string("scheduledbackupstatus");

        //Send the message
        email_to_user($admin,$admin,$subject,$message);
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

    mtrace("            Executing backup");
    schedule_backup_log($starttime,$course->id,"Start backup course $course->fullname");
    schedule_backup_log($starttime,$course->id,"  Phase 1: Checking and counting:");
    $preferences = schedule_backup_course_configure($course,$starttime);

    if ($preferences) {
        schedule_backup_log($starttime,$course->id,"  Phase 2: Executing and copying:");
        $status = schedule_backup_course_execute($preferences,$starttime);
    }

    if ($status && $preferences) {
        //Only if the backup_sche_keep is set
        if ($preferences->backup_keep) {
            schedule_backup_log($starttime,$course->id,"  Phase 3: Deleting old backup files:");
            $status = schedule_backup_course_delete_old_files($preferences,$starttime);
        }
    }

    if ($status && $preferences) {
        mtrace("            End backup OK");
        schedule_backup_log($starttime,$course->id,"End backup course $course->fullname - OK");
    } else {
        mtrace("            End backup with ERROR");
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
        $log->info = addslashes($message);

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
        if (!isset($backup_config->backup_sche_metacourse)) {
            $backup_config->backup_sche_metacourse = 1;
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
        if (!isset($backup_config->backup_sche_sitefiles)) {
            $backup_config->backup_sche_sitefiles = 1;
        }
        if (!isset($backup_config->backup_sche_gradebook_history)) {
            $backup_config->backup_sche_gradebook_history = 0;
        }
        if (!isset($backup_config->backup_sche_messages)) {
            $backup_config->backup_sche_messages = 0;
        }
        if (!isset($backup_config->backup_sche_blogs)) {
            $backup_config->backup_sche_blogs = 0;
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
        if (!isset($backup_config->backup_sche_keep)) {
            $backup_config->backup_sche_keep = 1;
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

                       // PENNY NOTES: I have moved from here to the closing brace inside
                       // by two sets of ifs()
                       // to avoid the backup failing on a non existant backup.
                       // If the file/function/whatever doesn't exist, we don't want to set this
                       // this module in backup preferences at all.
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
        }

        // now set instances
        if ($coursemods = get_course_mods($course->id)) {
            foreach ($coursemods as $mod) {
                if (array_key_exists($mod->modname,$preferences->mods)) { // we are to backup this module
                    if (empty($preferences->mods[$mod->modname]->instances)) {
                        $preferences->mods[$mod->modname]->instances = array(); // avoid warnings
                    }
                    $preferences->mods[$mod->modname]->instances[$mod->instance]->backup = $preferences->mods[$mod->modname]->backup;
                    $preferences->mods[$mod->modname]->instances[$mod->instance]->userinfo = $preferences->mods[$mod->modname]->userinfo;
                    // there isn't really a nice way to do this...
                    $preferences->mods[$mod->modname]->instances[$mod->instance]->name = get_field($mod->modname,'name','id',$mod->instance);
                }
            }
        }

        // finally, clean all the $preferences->mods[] not having instances. Nothing to backup about them
        foreach ($preferences->mods as $modname => $mod) {
            if (!isset($mod->instances)) {
                unset($preferences->mods[$modname]);
            }
        }
    }

    //Convert other parameters
    if ($status) {
        $preferences->backup_metacourse = $backup_config->backup_sche_metacourse;
        $preferences->backup_users = $backup_config->backup_sche_users;
        $preferences->backup_logs = $backup_config->backup_sche_logs;
        $preferences->backup_user_files = $backup_config->backup_sche_userfiles;
        $preferences->backup_course_files = $backup_config->backup_sche_coursefiles;
        $preferences->backup_site_files = $backup_config->backup_sche_sitefiles;
        $preferences->backup_gradebook_history = $backup_config->backup_sche_gradebook_history;
        $preferences->backup_messages = $backup_config->backup_sche_messages;
        $preferences->backup_blogs = $backup_config->backup_sche_blogs;
        $preferences->backup_course = $course->id;
        $preferences->backup_destination = $backup_config->backup_sche_destination;
        $preferences->backup_keep = $backup_config->backup_sche_keep;
    }

    //Calculate various backup preferences
    if ($status) {
        schedule_backup_log($starttime,$course->id,"    calculating backup name");

        //Calculate the backup file name
        $backup_name = backup_get_zipfile_name($course);

        //Calculate the string to match the keep preference
        $keep_name = backup_get_keep_name($course);

        //Set them
        $preferences->backup_name = $backup_name;
        $preferences->keep_name = $keep_name;

        //Roleasignments
        $roles = get_records('role', '', '', 'sortorder');
        foreach ($roles as $role) {
            $preferences->backuproleassignments[$role->id] = $role;
        }

        //Another Info
        backup_add_static_preferences($preferences);
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
        //Decide about include users with messages, based on SITEID
        if ($preferences->backup_messages && $preferences->backup_course == SITEID) {
            $include_message_users = true;
        } else {
            $include_message_users = false;
        }
        //Decide about include users with blogs, based on SITEID
        if ($preferences->backup_blogs && $preferences->backup_course == SITEID) {
            $include_blog_users = true;
        } else {
            $include_blog_users = false;
        }
        user_check_backup($course->id,$backup_unique_code,$preferences->backup_users,$include_message_users, $include_blog_users);
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

    //Now calculate the sitefiles
    if ($status) {
       if ($preferences->backup_site_files) {
            schedule_backup_log($starttime,$course->id,"    calculating site files");
            site_files_check_backup($course->id,$preferences->backup_unique_code);
        }
    }

    //If everything is ok, return calculated preferences
    if ($status) {
        $status = $preferences;
    }

    return $status;
}

//TODO: Unify this function with backup_execute() to have both backups 100% equivalent. Moodle 2.0

//This function implements all the needed code to backup a course
//copying it to the desired destination (default if not specified)
function schedule_backup_course_execute($preferences,$starttime = 0) {

    global $CFG;

    $status = true;

    //Some parts of the backup doesn't know about $preferences, so we
    //put a copy of it inside that CFG (always global) to be able to
    //use it. Then, when needed I search for preferences inside CFG
    //Used to avoid some problems in full_tag() when preferences isn't
    //set globally (i.e. in scheduled backups)
    $CFG->backup_preferences = $preferences;

    //Check for temp and backup and backup_unique_code directory
    //Create them as needed
    schedule_backup_log($starttime,$preferences->backup_course,"    checking temp structures");
    $status = check_and_create_backup_dir($preferences->backup_unique_code);
    //Empty backup dir
    if ($status) {
        schedule_backup_log($starttime,$preferences->backup_course,"    cleaning current dir");
        $status = clear_backup_dir($preferences->backup_unique_code);
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

        //Metacourse information
        if ($status && $preferences->backup_metacourse) {
            schedule_backup_log($starttime,$preferences->backup_course,"      metacourse info");
            $status = backup_course_metacourse($backup_file,$preferences);
        }

        //Block info
        if ($status) {
            schedule_backup_log($starttime,$preferences->backup_course,"      blocks info");
            $status = backup_course_blocks($backup_file,$preferences);
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

        //If we have selected to backup messages and we are
        //doing a SITE backup, let's do it
        if ($status && $preferences->backup_messages && $preferences->backup_course == SITEID) {
            schedule_backup_log($starttime,$preferences->backup_course,"      messages");
            $status = backup_messages($backup_file,$preferences);
        }

        //If we have selected to backup blogs and we are
        //doing a SITE backup, let's do it
        if ($status && $preferences->backup_blogs && $preferences->backup_course == SITEID) {
            schedule_backup_log($starttime,$preferences->backup_course,"      blogs");
            $status = backup_blogs($backup_file,$preferences);
        }

        //If we have selected to backup quizzes, backup categories and
        //questions structure (step 1). See notes on mod/quiz/backuplib.php
        if ($status and $preferences->mods['quiz']->backup) {
            schedule_backup_log($starttime,$preferences->backup_course,"      categories & questions");
            $status = backup_question_categories($backup_file,$preferences);
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

        //Print groups info
        if ($status) {
            schedule_backup_log($starttime,$preferences->backup_course,"      groups");
            $status = backup_groups_info($backup_file,$preferences);
        }

        //Print groupings info
        if ($status) {
            schedule_backup_log($starttime,$preferences->backup_course,"      groupings");
            $status = backup_groupings_info($backup_file,$preferences);
        }

        //Print groupings_groups info
        if ($status) {
            schedule_backup_log($starttime,$preferences->backup_course,"      groupings_groups");
            $status = backup_groupings_groups_info($backup_file,$preferences);
        }

        //Print events info
        if ($status) {
            schedule_backup_log($starttime,$preferences->backup_course,"      events");
            $status = backup_events_info($backup_file,$preferences);
        }

        //Print gradebook info
        if ($status) {
            schedule_backup_log($starttime,$preferences->backup_course,"      gradebook");
            $status = backup_gradebook_info($backup_file,$preferences);
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

        //Backup course format data, if any.
        if ($status) {
            schedule_backup_log($starttime,$preferences->backup_course,"      course format data");
            $status = backup_format_data($backup_file,$preferences);
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

    //Now, if selected, copy site files
    if ($status) {
        if ($preferences->backup_site_files) {
            schedule_backup_log($starttime,$preferences->backup_course,"    copying site files");
            $status = backup_copy_site_files ($preferences);
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

    //Unset CFG->backup_preferences only needed in scheduled backups
    unset ($CFG->backup_preferences);

    return $status;
}

//This function deletes old backup files when the "keep" limit has been reached
//in the destination directory.
function schedule_backup_course_delete_old_files($preferences,$starttime=0) {

    global $CFG;

    $status = true;

    //Calculate the directory to check
    $dirtocheck = "";
    //if $preferences->backup_destination isn't empty, then check that directory
    if (!empty($preferences->backup_destination)) {
        $dirtocheck = $preferences->backup_destination;
    //else calculate standard backup directory location
    } else {
        $dirtocheck = $CFG->dataroot."/".$preferences->backup_course."/backupdata";
    }
    schedule_backup_log($starttime,$preferences->backup_course,"    checking $dirtocheck");
    if ($CFG->debug > 7) {
        mtrace("            Keeping backup files in $dirtocheck");
    }

    //Get all the files in $dirtocheck
    $files = get_directory_list($dirtocheck,"",false);
    //Get all matching files ($preferences->keep_name) from $files
    $matchingfiles = array();
    foreach ($files as $file) {
        if (substr($file, 0, strlen($preferences->keep_name)) == $preferences->keep_name) {
            $modifieddate = filemtime($dirtocheck."/".$file);
            $matchingfiles[$modifieddate] = $file;
        }
    }
    //Sort by key (modified date) to get the oldest first (instead of doing that by name
    //because it could give us problems in some languages with different format names).
    ksort($matchingfiles);

    //Count matching files
    $countmatching = count($matchingfiles);
    schedule_backup_log($starttime,$preferences->backup_course,"        found $countmatching backup files");
    mtrace("                found $countmatching backup files");
    if ($preferences->backup_keep < $countmatching) {
        schedule_backup_log($starttime,$preferences->backup_course,"        keep limit ($preferences->backup_keep) reached. Deleting old files");
        mtrace("                keep limit ($preferences->backup_keep) reached. Deleting old files");
        $filestodelete = $countmatching - $preferences->backup_keep;
        $filesdeleted = 0;
        foreach ($matchingfiles as $matchfile) {
            if ($filesdeleted < $filestodelete) {
                schedule_backup_log($starttime,$preferences->backup_course,"        $matchfile deleted");
                mtrace("                $matchfile deleted");
                $filetodelete = $dirtocheck."/".$matchfile;
                unlink($filetodelete);
                $filesdeleted++;
            }
        }
    }
    return $status;
}

?>
