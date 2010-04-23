<?php //$Id$
    //This file contains all the function needed in the backup utility
    //except the mod-related funtions that are into every backuplib.php inside
    //every mod directory

    /**
     * This function calculates the users to be added to backup based in the
     * settings defined at backup. All the resulting user ids are sent to
     * backup_ids for later usage.
     * @param int $courseid id of the course to backup
     * @param int $backup_unique_code unique code of the backup being executed
     * @param int $backup_unique_code setting specifying what users to export (0=all, 1=needed, 2=none)
     * @param int $backup_messages flag (true/false) defining if messages must be
     *                             considered to extract needed users
     * @param int $backup_blogs flag (true/false) defining if blogs must be
     *                          considered to extract needed users
     * @return array one array (key, value) sumarizing the result of the function (number of users)
     */
    function user_check_backup($courseid,$backup_unique_code,$backup_users,$backup_messages,$backup_blogs) {

        $context = get_context_instance(CONTEXT_COURSE, $courseid);
        $count_users = 0;
        $backupable_users = array();

        if ($backup_users == 0) { /// All users
            $backupable_users = backup_get_all_users();

        } else if ($backup_users == 1) { /// Needed users

        /// Calculate needed users (calling every xxxx_get_participants function + scales users
        /// + messages users + blogs users)
            $needed_users = backup_get_needed_users($courseid, $backup_messages, $backup_blogs);

        /// Calculate enrolled users (having course:view cap)
            $enrolled_users = backup_get_enrolled_users($courseid);

        /// Calculate backupable users (needed + enrolled)
        /// First, needed
            $backupable_users = $needed_users;

        /// Now, enrolled
            if ($enrolled_users) {
                foreach ($enrolled_users as $enrolled_user) {
                    $backupable_users[$enrolled_user->id]->id = $enrolled_user->id;
                }
            }
        }

    /// If we have backupable users
        if ($backupable_users) {
        /// Iterate over users putting their roles
            foreach ($backupable_users as $backupable_user) {
                $backupable_user->info = "";

            /// Is needed user or enrolled user, mark it as needed
                if (isset($needed_users[$backupable_user->id]) || isset($enrolled_users[$backupable_user->id])) {
                    $backupable_user->info .= "needed";
                }   ///  Yu: also needed because they can view course
                    /// might need another variable

            /// Now create the backup_id record
                $backupids_rec->backup_code = $backup_unique_code;
                $backupids_rec->table_name = "user";
                $backupids_rec->old_id = $backupable_user->id;
                $backupids_rec->info = $backupable_user->info;

            /// TODO: Change this call inserting to a standard backup_putid() call
            /// And read data acordingly with backup_getid() when needed.
            /// TODO: Also analyse it the "needed" info is really needed for anything. Drop if not.
            /// Insert the user to the backup_ids table. backup_user_info() will use that info
                $status = insert_record('backup_ids', $backupids_rec, false);
                $count_users++;
            }
        /// Do some output
            backup_flush(30);
        }

      /// Prepare Info
      /// Gets the user data
        $info[0][0] = get_string("users");
        $info[0][1] = $count_users;

        return $info;
    }

    //Returns every needed user (participant) in a course
    //It uses the xxxx_get_participants() function
    //Also it search for users having messages and
    //users having blogs
    //WARNING: It returns only NEEDED users, not every
    //   every student and teacher in the course, so it
    //must be merged with backup_get_enrrolled_users !!

    function backup_get_needed_users ($courseid, $includemessages=false, $includeblogs=false) {

        global $CFG;

        $result = false;

        $course_modules = get_records_sql ("SELECT cm.id, m.name, cm.instance
                                            FROM {$CFG->prefix}modules m,
                                                 {$CFG->prefix}course_modules cm
                                            WHERE m.id = cm.module and
                                                  cm.course = '$courseid'");

        if ($course_modules) {
            //Iterate over each module
            foreach ($course_modules as $course_module) {
                $modlib = "$CFG->dirroot/mod/$course_module->name/lib.php";
                $modgetparticipants = $course_module->name."_get_participants";
                if (file_exists($modlib)) {
                    include_once($modlib);
                    if (function_exists($modgetparticipants)) {
                        $module_participants = $modgetparticipants($course_module->instance);
                        //Add them to result
                        if ($module_participants) {
                            foreach ($module_participants as $module_participant) {
                                $result[$module_participant->id]->id = $module_participant->id;
                            }
                        }
                    }
                 }
            }
        }

        //Now, add message users if necessary
        if ($includemessages) {
            include_once("$CFG->dirroot/message/lib.php");
            //Get users
            $messageusers = message_get_participants();
            //Add message users to results
            if ($messageusers) {
                foreach ($messageusers as $messageuser) {
                    //If id != 0
                    if ($messageuser->id !=0) {
                        $result[$messageuser->id]->id = $messageuser->id;
                    }
                }
            }
        }

        //Now, add blog users if necessary
        if ($includeblogs) {
            include_once("$CFG->dirroot/blog/lib.php");
            //Get users
            $blogusers = blog_get_participants();
            //Add blog users to results
            if ($blogusers) {
                foreach ($blogusers as $bloguser) {
                    //If id != 0
                    if ($bloguser->id !=0) {
                        $result[$bloguser->id]->id = $bloguser->id;
                    }
                }
            }
        }

        return $result;

    }

    //Returns every enrolled user (student and teacher) in a course

    function backup_get_enrolled_users ($courseid) {

        global $CFG;

        // get all users with moodle/course:view capability, this will include people
        // assigned at cat level, or site level
        // but it should be ok if they have no direct assignment at course, mod, block level
        return get_users_by_capability(get_context_instance(CONTEXT_COURSE, $courseid), 'moodle/course:view', '', '', '', '', '', '', false);
    }

    //Returns all users ids (every record in users table)
    function backup_get_all_users() {

        return get_records('user', '', '', '', 'id, id');
    }

    //Calculate the number of log entries to backup
    //Return an array of info (name,value)
    function log_check_backup($course) {

        global $CFG;

        //Now execute the count
        $ids = count_records("log","course",$course);

        //Gets the user data
        $info[0][0] = get_string("logs");
        if ($ids) {
            $info[0][1] = $ids;
        } else {
            $info[0][1] = 0;
        }

        return $info;
    }

    //Calculate the number of user files to backup
    //Under $CFG->dataroot/users
    //Return an array of info (name,value)
    function user_files_check_backup($course,$backup_unique_code) {

        global $CFG;
        $count = 0;
        
        $backup_users = get_recordset_select("backup_ids",
            "backup_code='$backup_unique_code' AND table_name='user'", "", "id, old_id");
 
        while ($user = rs_fetch_next_record($backup_users)) {               
            //Is this user needed in the backup?
            $userdir = make_user_directory($user->old_id, true);
            if (check_dir_exists($userdir)) {
                $count++;
            }
            //Do some output
            backup_flush(30);
        }
        rs_close($backup_users);
        //Gets the user data
        $info[0][0] = get_string('userswithfiles', 'moodle');
        $info[0][1] = $count;

        return $info;
    }

    /**
     * Calculate the number of course files to backup
     * under $CFG->dataroot/$course, except $CFG->moddata, and backupdata
     * and put them (their path) in backup_ids
     * Return an array of info (name,value)
     */
    function course_files_check_backup($course, $backup_unique_code) {

        global $CFG;

        $rootdir = $CFG->dataroot."/$course";
        //Check if directory exists
        if (is_dir($rootdir)) {
            //Get files and directories without descend
            $coursedirs = get_directory_list($rootdir,$CFG->moddata,false,true,true);
            $backupdata_dir = "backupdata";
            foreach ($coursedirs as $dir) {
                //Check it isn't backupdata_dir
                if (strpos($dir,$backupdata_dir)!==0) {
                    //Insert them into backup_files
                    $status = execute_sql("INSERT INTO {$CFG->prefix}backup_files
                                                  (backup_code, file_type, path)
                                           VALUES
                                              ('$backup_unique_code','course','".addslashes($dir)."')",false);
                }
            //Do some output
            backup_flush(30);
            }
        }

        //Now execute the select
        $ids = get_records_sql("SELECT DISTINCT b.path, b.old_id
                                FROM {$CFG->prefix}backup_files b
                                WHERE backup_code = '$backup_unique_code' AND
                                      file_type = 'course'");
        //Gets the user data
        $info = array();
        $info[0] = array();
        $info[0][0] = get_string("files");
        if ($ids) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }

        return $info;
    }

    /**
     * Calculate the number of site files to backup
     * under $CFG->dataroot/SITEID
     * Their path is already in backup_ids, put there by modules check_backup functions.
     * Modules only put in paths of files that are used.
     *
     * Return an array of info (name,value)
     */
    function site_files_check_backup($course, $backup_unique_code) {
        global $CFG;

        //execute the select, records have been inserted by modules during their ****_check_backup_mods function.
        $ids = get_records_sql("SELECT DISTINCT b.path
                                FROM {$CFG->prefix}backup_files b
                                WHERE backup_code = '$backup_unique_code' AND
                                      file_type = 'site'");
        //Gets the user data
        $info = array();
        $info[0] = array();
        $info[0][0] = get_string('files');
        if ($ids) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }

        return $info;
    }

    //Function to check and create the needed moddata dir to
    //save all the mod backup files. We always name it moddata
    //to be able to restore it, but in restore we check for
    //$CFG->moddata !!
    function check_and_create_moddata_dir($backup_unique_code) {

        global $CFG;

        $status = check_dir_exists($CFG->dataroot."/temp/backup/".$backup_unique_code."/moddata",true);

        return $status;
    }

    //Function to check and create the "user_files" dir to
    //save all the user files we need from "users" dir
    function check_and_create_user_files_dir($backup_unique_code) {

        global $CFG;

        $status = check_dir_exists($CFG->dataroot."/temp/backup/".$backup_unique_code."/user_files",true);

        return $status;
    }

    //Function to check and create the "group_files" dir to
    //save all the user files we need from "groups" dir
    function check_and_create_group_files_dir($backup_unique_code) {

        global $CFG;

        $status = check_dir_exists($CFG->dataroot."/temp/backup/".$backup_unique_code."/group_files",true);

        return $status;
    }

    //Function to check and create the "course_files" dir to
    //save all the course files we need from "CFG->datadir/course" dir
    function check_and_create_course_files_dir($backup_unique_code) {

        global $CFG;

        $status = check_dir_exists($CFG->dataroot."/temp/backup/".$backup_unique_code."/course_files",true);

        return $status;
    }

    //Function to check and create the "site_files" dir to
    //save all the course files we need from "CFG->datadir/SITEID" dir
    function check_and_create_site_files_dir($backup_unique_code) {

        global $CFG;

        $status = check_dir_exists($CFG->dataroot."/temp/backup/".$backup_unique_code."/site_files",true);

        return $status;
    }

    //Function to create, open and write header of the xml file
    function backup_open_xml($backup_unique_code) {

        global $CFG;

        $status = true;

        //Open for writing

        $file = $CFG->dataroot."/temp/backup/".$backup_unique_code."/moodle.xml";
        $backup_file = fopen($file,"w");
        //Writes the header
        $status = fwrite ($backup_file,"<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
        if ($status) {
            $status = fwrite ($backup_file,start_tag("MOODLE_BACKUP",0,true));
        }
        if ($status) {
            return $backup_file;
        } else {
            return false;
        }
    }

    //Close the file
    function backup_close_xml($backup_file) {
        $status = fwrite ($backup_file,end_tag("MOODLE_BACKUP",0,true));
        return fclose($backup_file);
    }

    //Return the xml start tag
    function start_tag($tag,$level=0,$endline=false,$attributes=null) {
        if ($endline) {
           $endchar = "\n";
        } else {
           $endchar = "";
        }
        $attrstring = '';
        if (!empty($attributes) && is_array($attributes)) {
            foreach ($attributes as $key => $value) {
                $attrstring .= " ".xml_tag_safe_content($key)."=\"".
                    xml_tag_safe_content($value)."\"";
            }
        }
        return str_repeat(" ",$level*2)."<".strtoupper($tag).$attrstring.">".$endchar;
    }

    //Return the xml end tag
    function end_tag($tag,$level=0,$endline=true) {
        if ($endline) {
           $endchar = "\n";
        } else {
           $endchar = "";
        }
        return str_repeat(" ",$level*2)."</".strtoupper($tag).">".$endchar;
    }

    //Return the start tag, the contents and the end tag
    function full_tag($tag,$level=0,$endline=true,$content,$attributes=null) {

        global $CFG;
        //Here we encode absolute links
        // MDL-10770
        if (is_null($content)) {
            $content = '$@NULL@$'; 
        } else {
            $content = backup_encode_absolute_links($content);
        }
        $st = start_tag($tag,$level,$endline,$attributes);

        $co = xml_tag_safe_content($content);

        $et = end_tag($tag,0,true);

        return $st.$co.$et;
    }


    function xml_tag_safe_content($content) {
        global $CFG;
        //If enabled, we strip all the control chars (\x0-\x1f) from the text but tabs (\x9),
        //newlines (\xa) and returns (\xd). The delete control char (\x7f) is also included.
        //because they are forbiden in XML 1.0 specs. The expression below seems to be
        //UTF-8 safe too because it simply ignores the rest of characters.
        $content = preg_replace("/[\x-\x8\xb-\xc\xe-\x1f\x7f]/is","",$content);
        $content = preg_replace("/\r\n|\r/", "\n", htmlspecialchars($content));
        return $content;
    }

    //Prints General info about the course
    //name, moodle_version (internal and release), backup_version, date, info in file...
    function backup_general_info ($bf,$preferences) {

        global $CFG;

        fwrite ($bf,start_tag("INFO",1,true));

        //The name of the backup
        fwrite ($bf,full_tag("NAME",2,false,$preferences->backup_name));
        //The moodle_version
        fwrite ($bf,full_tag("MOODLE_VERSION",2,false,$preferences->moodle_version));
        fwrite ($bf,full_tag("MOODLE_RELEASE",2,false,$preferences->moodle_release));
        //The backup_version
        fwrite ($bf,full_tag("BACKUP_VERSION",2,false,$preferences->backup_version));
        fwrite ($bf,full_tag("BACKUP_RELEASE",2,false,$preferences->backup_release));
        //The date
        fwrite ($bf,full_tag("DATE",2,false,$preferences->backup_unique_code));
        //The original site wwwroot
        fwrite ($bf,full_tag("ORIGINAL_WWWROOT",2,false,$CFG->wwwroot));
        //The original site identifier. MD5 hashed for security.
        fwrite ($bf,full_tag("ORIGINAL_SITE_IDENTIFIER_HASH",2,false,md5(get_site_identifier())));
        //The zip method used
        if (!empty($CFG->zip)) {
            $zipmethod = 'external';
        } else {
            $zipmethod = 'internal';
        }
        //Indicate if it includes external MNET users
        $sql = "SELECT b.old_id
                   FROM   {$CFG->prefix}backup_ids b
                     JOIN {$CFG->prefix}user       u ON b.old_id=u.id
                   WHERE b.backup_code = '$preferences->backup_unique_code'
                         AND b.table_name = 'user' AND u.mnethostid != '{$CFG->mnet_localhost_id}'";
        if (record_exists_sql($sql)) {
            fwrite ($bf,full_tag("MNET_REMOTEUSERS",2,false,'true'));
        }
        fwrite ($bf,full_tag("ZIP_METHOD",2,false,$zipmethod));
        //Te includes tag
        fwrite ($bf,start_tag("DETAILS",2,true));
        //Now, go to mod element of preferences to print its status
        foreach ($preferences->mods as $element) {
            //Calculate info
            $included = "false";
            $userinfo = "false";
            if ($element->backup) {
                $included = "true";
                if ($element->userinfo) {
                    $userinfo = "true";
                }
            }
            //Prints the mod start
            fwrite ($bf,start_tag("MOD",3,true));
            fwrite ($bf,full_tag("NAME",4,false,$element->name));
            fwrite ($bf,full_tag("INCLUDED",4,false,$included));
            fwrite ($bf,full_tag("USERINFO",4,false,$userinfo));

            if (isset($preferences->mods[$element->name]->instances)
                && is_array($preferences->mods[$element->name]->instances)
                && count($preferences->mods[$element->name]->instances)) {
                fwrite ($bf, start_tag("INSTANCES",4,true));
                foreach ($preferences->mods[$element->name]->instances as $id => $object) {
                    if (!empty($object->backup)) {
                        //Calculate info
                        $included = "false";
                        $userinfo = "false";
                        if ($object->backup) {
                            $included = "true";
                            if ($object->userinfo) {
                                $userinfo = "true";
                            }
                        }
                        fwrite ($bf, start_tag("INSTANCE",5,true));
                        fwrite ($bf, full_tag("ID",5,false,$id));
                        fwrite ($bf, full_tag("NAME",5,false,$object->name));
                        fwrite ($bf, full_tag("INCLUDED",5,false,$included)) ;
                        fwrite ($bf, full_tag("USERINFO",5,false,$userinfo));
                        fwrite ($bf, end_tag("INSTANCE",5,true));
                    }
                }
                fwrite ($bf, end_tag("INSTANCES",4,true));
            }


            //Print the end
            fwrite ($bf,end_tag("MOD",3,true));
        }
        //The metacourse in backup
        if ($preferences->backup_metacourse == 1) {
            fwrite ($bf,full_tag("METACOURSE",3,false,"true"));
        } else {
            fwrite ($bf,full_tag("METACOURSE",3,false,"false"));
        }
        //The user in backup
        if ($preferences->backup_users == 1) {
            fwrite ($bf,full_tag("USERS",3,false,"course"));
        } else if ($preferences->backup_users == 0) {
            fwrite ($bf,full_tag("USERS",3,false,"all"));
        } else {
            fwrite ($bf,full_tag("USERS",3,false,"none"));
        }
        //The logs in backup
        if ($preferences->backup_logs == 1) {
            fwrite ($bf,full_tag("LOGS",3,false,"true"));
        } else {
            fwrite ($bf,full_tag("LOGS",3,false,"false"));
        }
        //The user files
        if ($preferences->backup_user_files == 1) {
            fwrite ($bf,full_tag("USERFILES",3,false,"true"));
        } else {
            fwrite ($bf,full_tag("USERFILES",3,false,"false"));
        }
        //The course files
        if ($preferences->backup_course_files == 1) {
            fwrite ($bf,full_tag("COURSEFILES",3,false,"true"));
        } else {
            fwrite ($bf,full_tag("COURSEFILES",3,false,"false"));
        }
        //The site files
        if ($preferences->backup_site_files == 1) {
            fwrite ($bf,full_tag("SITEFILES",3,false,"true"));
        } else {
            fwrite ($bf,full_tag("SITEFILES",3,false,"false"));
        }
        //The gradebook histories
        if (empty($CFG->disablegradehistory) && $preferences->backup_gradebook_history == 1) {
            fwrite ($bf,full_tag("GRADEBOOKHISTORIES",3,false,"true"));
        } else {
            fwrite ($bf,full_tag("GRADEBOOKHISTORIES",3,false,"false"));
        }
        //The messages in backup
        if ($preferences->backup_messages == 1 && $preferences->backup_course == SITEID) {
            fwrite ($bf,full_tag("MESSAGES",3,false,"true"));
        } else {
            fwrite ($bf,full_tag("MESSAGES",3,false,"false"));
        }
        //The blogs in backup
        if ($preferences->backup_blogs == 1 && $preferences->backup_course == SITEID) {
            fwrite ($bf,full_tag("BLOGS",3,false,"true"));
        } else {
            fwrite ($bf,full_tag("BLOGS",3,false,"false"));
        }
        //The mode of writing the block data
        fwrite ($bf,full_tag('BLOCKFORMAT',3,false,'instances'));
        fwrite ($bf,end_tag("DETAILS",2,true));

        $status = fwrite ($bf,end_tag("INFO",1,true));

        ///Roles stuff goes in here

        fwrite ($bf, start_tag('ROLES', 1, true));
        $roles = backup_fetch_roles($preferences);

        $sitecontext = get_context_instance(CONTEXT_SYSTEM);
        $coursecontext = get_context_instance(CONTEXT_COURSE, $preferences->backup_course);

        foreach ($roles as $role) {
            fwrite ($bf,start_tag('ROLE',2,true));
            fwrite ($bf,full_tag('ID', 3, false, $role->id));
            fwrite ($bf,full_tag('NAME',3,false,$role->name));
            fwrite ($bf,full_tag('SHORTNAME',3,false,$role->shortname));
        /// Calculate $role name in course
            $nameincourse = role_get_name($role, $coursecontext);
            if ($nameincourse != $role->name) {
                fwrite ($bf,full_tag('NAMEINCOURSE', 3, false, $nameincourse));
            }
            // find and write all default capabilities
            fwrite ($bf,start_tag('CAPABILITIES',3,true));
            // pull out all default (site context) capabilities
            if ($capabilities = role_context_capabilities($role->id, $sitecontext)) {
                foreach ($capabilities as $capability=>$value) {
                    fwrite ($bf,start_tag('CAPABILITY',4,true));
                    fwrite ($bf,full_tag('NAME', 5, false, $capability));
                    fwrite ($bf,full_tag('PERMISSION', 5, false, $value));
                    // use this to pull out the other info (timemodified and modifierid)

                    $cap = get_record_sql("SELECT *
                                           FROM {$CFG->prefix}role_capabilities
                                           WHERE capability = '$capability'
                                                 AND contextid = $sitecontext->id
                                                 AND roleid = $role->id");
                    fwrite ($bf, full_tag("TIMEMODIFIED", 5, false, $cap->timemodified));
                    fwrite ($bf, full_tag("MODIFIERID", 5, false, $cap->modifierid));
                    fwrite ($bf,end_tag('CAPABILITY',4,true));
                }
            }
            fwrite ($bf,end_tag('CAPABILITIES',3,true));
            fwrite ($bf,end_tag('ROLE',2,true));
        }
        fwrite ($bf,end_tag('ROLES', 1, true));
        return $status;
    }

    //Prints course's general info (table course)
    function backup_course_start ($bf,$preferences) {

        global $CFG;

        $status = true;

        //Course open tag
        fwrite ($bf,start_tag("COURSE",1,true));
        //Header open tag
        fwrite ($bf,start_tag("HEADER",2,true));

        //Get info from course
        $course = get_record("course","id",$preferences->backup_course);
        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        if ($course) {
            //Prints course info
            fwrite ($bf,full_tag("ID",3,false,$course->id));
            //Obtain the category
            $category = get_record("course_categories","id","$course->category");
            if ($category) {
                //Prints category info
                fwrite ($bf,start_tag("CATEGORY",3,true));
                fwrite ($bf,full_tag("ID",4,false,$course->category));
                fwrite ($bf,full_tag("NAME",4,false,$category->name));
                fwrite ($bf,end_tag("CATEGORY",3,true));
            }
            //Continues with the course
            fwrite ($bf,full_tag("PASSWORD",3,false,$course->password));
            fwrite ($bf,full_tag("FULLNAME",3,false,$course->fullname));
            fwrite ($bf,full_tag("SHORTNAME",3,false,$course->shortname));
            fwrite ($bf,full_tag("IDNUMBER",3,false,$course->idnumber));
            fwrite ($bf,full_tag("SUMMARY",3,false,$course->summary));
            fwrite ($bf,full_tag("FORMAT",3,false,$course->format));
            fwrite ($bf,full_tag("SHOWGRADES",3,false,$course->showgrades));
            fwrite ($bf,full_tag("NEWSITEMS",3,false,$course->newsitems));
            fwrite ($bf,full_tag("TEACHER",3,false,$course->teacher));
            fwrite ($bf,full_tag("TEACHERS",3,false,$course->teachers));
            fwrite ($bf,full_tag("STUDENT",3,false,$course->student));
            fwrite ($bf,full_tag("STUDENTS",3,false,$course->students));
            fwrite ($bf,full_tag("GUEST",3,false,$course->guest));
            fwrite ($bf,full_tag("STARTDATE",3,false,$course->startdate));
            fwrite ($bf,full_tag("NUMSECTIONS",3,false,$course->numsections));
            //fwrite ($bf,full_tag("SHOWRECENT",3,false,$course->showrecent));    INFO: This is out in 1.3
            fwrite ($bf,full_tag("MAXBYTES",3,false,$course->maxbytes));
            fwrite ($bf,full_tag("SHOWREPORTS",3,false,$course->showreports));
            fwrite ($bf,full_tag("GROUPMODE",3,false,$course->groupmode));
            fwrite ($bf,full_tag("GROUPMODEFORCE",3,false,$course->groupmodeforce));
            fwrite ($bf,full_tag("DEFAULTGROUPINGID",3,false,$course->defaultgroupingid));
            fwrite ($bf,full_tag("LANG",3,false,$course->lang));
            fwrite ($bf,full_tag("THEME",3,false,$course->theme));
            fwrite ($bf,full_tag("COST",3,false,$course->cost));
            fwrite ($bf,full_tag("CURRENCY",3,false,$course->currency));
            fwrite ($bf,full_tag("MARKER",3,false,$course->marker));
            fwrite ($bf,full_tag("VISIBLE",3,false,$course->visible));
            fwrite ($bf,full_tag("HIDDENSECTIONS",3,false,$course->hiddensections));
            fwrite ($bf,full_tag("TIMECREATED",3,false,$course->timecreated));
            fwrite ($bf,full_tag("TIMEMODIFIED",3,false,$course->timemodified));
            //If not selected, force metacourse to 0
            if (!$preferences->backup_metacourse) {
                $status = fwrite ($bf,full_tag("METACOURSE",3,false,'0'));
            //else, export the field as is in DB
            } else {
                $status = fwrite ($bf,full_tag("METACOURSE",3,false,$course->metacourse));
            }
            fwrite ($bf,full_tag("EXPIRENOTIFY",3,false,$course->expirynotify));
            fwrite ($bf,full_tag("NOTIFYSTUDENTS",3,false,$course->notifystudents));
            fwrite ($bf,full_tag("EXPIRYTHRESHOLD",3,false,$course->expirythreshold));
            fwrite ($bf,full_tag("ENROLLABLE",3,false,$course->enrollable));
            fwrite ($bf,full_tag("ENROLSTARTDATE",3,false,$course->enrolstartdate));
            fwrite ($bf,full_tag("ENROLENDDATE",3,false,$course->enrolenddate));
            fwrite ($bf,full_tag("ENROLPERIOD",3,false,$course->enrolperiod));

            /// write local course overrides here?
            write_role_overrides_xml($bf, $context, 3);
            /// write role_assign code here
            write_role_assignments_xml($bf, $preferences, $context, 3);
            //Print header end
            fwrite ($bf,end_tag("HEADER",2,true));
        } else {
           $status = false;
        }

       return $status;
    }

    //Prints course's end tag
    function backup_course_end ($bf,$preferences) {

        //Course end tag
        $status = fwrite ($bf,end_tag("COURSE",1,true));

        return $status;

    }

    //Prints course's metacourse info (table course_meta)
    function backup_course_metacourse ($bf,$preferences) {

        global $CFG;

        $status = true;

        //Get info from meta
        $parents = get_records_sql ("SELECT m.*, c.idnumber, c.shortname
                                     FROM {$CFG->prefix}course_meta m,
                                          {$CFG->prefix}course c
                                          WHERE m.child_course = '$preferences->backup_course' AND
                                                m.parent_course = c.id");
        $childs =  get_records_sql ("SELECT m.*, c.idnumber, c.shortname
                                     FROM {$CFG->prefix}course_meta m,
                                          {$CFG->prefix}course c
                                          WHERE m.parent_course = '$preferences->backup_course' AND
                                                m.child_course = c.id");

        if ($parents || $childs) {
            //metacourse open tag
            fwrite ($bf,start_tag("METACOURSE",2,true));
            if ($parents) {
                fwrite($bf, start_tag("PARENTS",3,true));
                //Iterate over every parent
                foreach ($parents as $parent) {
                    //Begin parent
                    fwrite ($bf,start_tag("PARENT",4,true));
                    fwrite ($bf,full_tag("ID",5,false,$parent->parent_course));
                    fwrite ($bf,full_tag("IDNUMBER",5,false,$parent->idnumber));
                    fwrite ($bf,full_tag("SHORTNAME",5,false,$parent->shortname));
                    //End parent
                    fwrite ($bf,end_tag("PARENT",4,true));
                }
                fwrite ($bf,end_tag("PARENTS",3,true));
            }
            if ($childs) {
                fwrite($bf, start_tag("CHILDS",3,true));
                //Iterate over every child
                foreach ($childs as $child) {
                    //Begin parent
                    fwrite ($bf,start_tag("CHILD",4,true));
                    fwrite ($bf,full_tag("ID",5,false,$child->child_course));
                    fwrite ($bf,full_tag("IDNUMBER",5,false,$child->idnumber));
                    fwrite ($bf,full_tag("SHORTNAME",5,false,$child->shortname));
                    //End parent
                    fwrite ($bf,end_tag("CHILD",4,true));
                }
                fwrite ($bf,end_tag("CHILDS",3,true));
            }
            //metacourse close tag
            $status = fwrite ($bf,end_tag("METACOURSE",3,true));
        }

        return $status;

    }

    //Prints course's messages info (tables message, message_read and message_contacts)
    function backup_messages ($bf,$preferences) {

        global $CFG;

        $status = true;

    /// Check we have something to backup
        $unreads = count_records ('message');
        $reads   = count_records ('message_read');
        $contacts= count_records ('message_contacts');

        if ($unreads || $reads || $contacts) {
            $counter = 0;
        /// message open tag
            fwrite ($bf,start_tag("MESSAGES",2,true));

            if ($unreads) {
                $rs_unreads = get_recordset('message');
            /// Iterate over every unread
                while ($unread = rs_fetch_next_record($rs_unreads)) {
                /// start message
                    fwrite($bf, start_tag("MESSAGE",3,true));
                    fwrite ($bf,full_tag("ID",4,false,$unread->id));
                    fwrite ($bf,full_tag("STATUS",4,false,"UNREAD"));
                    fwrite ($bf,full_tag("USERIDFROM",4,false,$unread->useridfrom));
                    fwrite ($bf,full_tag("USERIDTO",4,false,$unread->useridto));
                    fwrite ($bf,full_tag("MESSAGE",4,false,$unread->message));
                    fwrite ($bf,full_tag("FORMAT",4,false,$unread->format));
                    fwrite ($bf,full_tag("TIMECREATED",4,false,$unread->timecreated));
                    fwrite ($bf,full_tag("MESSAGETYPE",4,false,$unread->messagetype));
                /// end message
                    fwrite ($bf,end_tag("MESSAGE",3,true));

                /// Do some output
                    $counter++;
                    if ($counter % 20 == 0) {
                        echo ".";
                        if ($counter % 400 == 0) {
                            echo "<br />";
                        }
                        backup_flush(300);
                    }
                }
                rs_close($rs_unreads);
            }

            if ($reads) {
                $rs_reads = get_recordset('message_read');
            /// Iterate over every unread
                while ($read = rs_fetch_next_record($rs_reads)) {
                /// start message
                    fwrite($bf, start_tag("MESSAGE",3,true));
                    fwrite ($bf,full_tag("ID",4,false,$read->id));
                    fwrite ($bf,full_tag("STATUS",4,false,"READ"));
                    fwrite ($bf,full_tag("USERIDFROM",4,false,$read->useridfrom));
                    fwrite ($bf,full_tag("USERIDTO",4,false,$read->useridto));
                    fwrite ($bf,full_tag("MESSAGE",4,false,$read->message));
                    fwrite ($bf,full_tag("FORMAT",4,false,$read->format));
                    fwrite ($bf,full_tag("TIMECREATED",4,false,$read->timecreated));
                    fwrite ($bf,full_tag("MESSAGETYPE",4,false,$read->messagetype));
                    fwrite ($bf,full_tag("TIMEREAD",4,false,$read->timeread));
                    fwrite ($bf,full_tag("MAILED",4,false,$read->mailed));
                /// end message
                    fwrite ($bf,end_tag("MESSAGE",3,true));

                /// Do some output
                    $counter++;
                    if ($counter % 20 == 0) {
                        echo ".";
                        if ($counter % 400 == 0) {
                            echo "<br />";
                        }
                        backup_flush(300);
                    }
                }
                rs_close($rs_reads);
            }

            if ($contacts) {
                fwrite($bf, start_tag("CONTACTS",3,true));
                $rs_contacts = get_recordset('message_contacts');
            /// Iterate over every contact
                while ($contact = rs_fetch_next_record($rs_contacts)) {
                /// start contact
                    fwrite($bf, start_tag("CONTACT",4,true));
                    fwrite ($bf,full_tag("ID",5,false,$contact->id));
                    fwrite ($bf,full_tag("USERID",5,false,$contact->userid));
                    fwrite ($bf,full_tag("CONTACTID",5,false,$contact->contactid));
                    fwrite ($bf,full_tag("BLOCKED",5,false,$contact->blocked));
                /// end contact
                    fwrite ($bf,end_tag("CONTACT",4,true));

                /// Do some output
                    $counter++;
                    if ($counter % 20 == 0) {
                        echo ".";
                        if ($counter % 400 == 0) {
                            echo "<br />";
                        }
                        backup_flush(300);
                    }
                }
                rs_close($rs_contacts);
                fwrite($bf, end_tag("CONTACTS",3,true));
            }

        /// messages close tag
            $status = fwrite ($bf,end_tag("MESSAGES",2,true));
        }

        return $status;

    }

    //Print blogs info (post table, module=blog, course=0)
    function backup_blogs($bf, $preferences) {

        global $CFG;

        $status = true;

    /// Check we have something to backup
        $siteblogs = count_records('post', 'module', 'blog', 'courseid', 0);

        if ($siteblogs) {
            $counter = 0;
        /// blogs open tag
            fwrite ($bf, start_tag("BLOGS",2,true));

            if ($siteblogs) {
                $rs_blogs = get_recordset_sql("SELECT * from {$CFG->prefix}post
                                                WHERE module = 'blog'
                                                  AND courseid = 0");
            /// Iterate over every blog
                while ($blog = rs_fetch_next_record($rs_blogs)) {
                /// start blog
                    fwrite($bf, start_tag("BLOG",3,true));
                /// blog body
                    fwrite ($bf,full_tag("ID",4,false,$blog->id));
                    fwrite ($bf,full_tag("MODULE",4,false,$blog->module));
                    fwrite ($bf,full_tag("USERID",4,false,$blog->userid));
                    fwrite ($bf,full_tag("COURSEID",4,false,$blog->courseid));
                    fwrite ($bf,full_tag("GROUPID",4,false,$blog->groupid));
                    fwrite ($bf,full_tag("MODULEID",4,false,$blog->moduleid));
                    fwrite ($bf,full_tag("COURSEMODULEID",4,false,$blog->coursemoduleid));
                    fwrite ($bf,full_tag("SUBJECT",4,false,$blog->subject));
                    fwrite ($bf,full_tag("SUMMARY",4,false,$blog->summary));
                    fwrite ($bf,full_tag("CONTENT",4,false,$blog->content));
                    fwrite ($bf,full_tag("UNIQUEHASH",4,false,$blog->uniquehash));
                    fwrite ($bf,full_tag("RATING",4,false,$blog->rating));
                    fwrite ($bf,full_tag("FORMAT",4,false,$blog->format));
                    fwrite ($bf,full_tag("ATTACHMENT",4,false,$blog->attachment));
                    fwrite ($bf,full_tag("PUBLISHSTATE",4,false,$blog->publishstate));
                    fwrite ($bf,full_tag("LASTMODIFIED",4,false,$blog->lastmodified));
                    fwrite ($bf,full_tag("CREATED",4,false,$blog->created));
                    fwrite ($bf,full_tag("USERMODIFIED",4,false,$blog->usermodified));

                /// Blog tags
                /// Check if we have blog tags to backup
                    if (!empty($CFG->usetags)) {
                        if ($tags = tag_get_tags('post', $blog->id)) { //This return them ordered by default
                        /// Start BLOG_TAGS tag
                            fwrite ($bf,start_tag("BLOG_TAGS",4,true));
                        /// Write blog tags fields
                            foreach ($tags as $tag) {
                                fwrite ($bf,start_tag("BLOG_TAG",5,true));
                                fwrite ($bf,full_tag("NAME",6,false,$tag->name));
                                fwrite ($bf,full_tag("RAWNAME",6,false,$tag->rawname));
                                fwrite ($bf,end_tag("BLOG_TAG",5,true));
                            }
                        /// End BLOG_TAGS tag
                            fwrite ($bf,end_tag("BLOG_TAGS",4,true));
                        }
                    }

                /// Blog comments
                    /// TODO: Blog comments go here (2.0)

                /// end blog
                    fwrite($bf, end_tag("BLOG",3,true));

                /// Do some output
                    $counter++;
                    if ($counter % 20 == 0) {
                        echo ".";
                        if ($counter % 400 == 0) {
                            echo "<br />";
                        }
                        backup_flush(300);
                    }
                }
                rs_close($rs_blogs);
            }
        /// blogs close tag
            $status = fwrite($bf, end_tag("BLOGS",2,true));
        }

        return $status;
    }

    //Prints course's blocks info (table block_instance)
    function backup_course_blocks ($bf,$preferences) {

        global $CFG;

        $status = true;

        // Read all of the block table
        $blocks = blocks_get_record();

        $pages = array();
        $pages[] = page_create_object(PAGE_COURSE_VIEW, $preferences->backup_course);

        if (!empty($CFG->showblocksonmodpages)) {
            // get course structure
            $course  = get_record('course', 'id', $preferences->backup_course);
            $modinfo =& get_fast_modinfo($course);

            foreach($preferences->mods as $module) {
                if (!$module->backup) {
                    continue;
                }

                if (empty($modinfo->instances[$module->name])) {
                    continue;
                }

                $pagetypes = page_import_types('mod/'.$module->name.'/');
                if (empty($pagetypes)) {
                    continue;
                }

                foreach($pagetypes as $pagetype) {
                    foreach($modinfo->instances[$module->name] as $cm) {
                        if (!empty($module->instances[$cm->instance]->backup)) {
                            $pages[] = page_create_object($pagetype, $cm->instance);
                        }
                    }
                }
            }
        }

        //Blocks open tag
        fwrite ($bf,start_tag('BLOCKS',2,true));

        foreach($pages as $page) {
            if ($instances = blocks_get_by_page($page)) {
                //Iterate over every block
                foreach ($instances as $position) {
                    foreach ($position as $instance) {

                        //If we somehow have a block with an invalid id, skip it
                        if(empty($blocks[$instance->blockid]->name)) {
                            continue;
                        }
                        $blockname = $blocks[$instance->blockid]->name;

                        if (!$blockobj = block_instance($blockname, $instance)) {
                            // Invalid block
                            continue;
                        }

                        // encode absolute links in block config
                        $instance->configdata = $blockobj->get_backup_encoded_config();

                        //Begin Block
                        fwrite ($bf,start_tag('BLOCK',3,true));
                        fwrite ($bf,full_tag('ID', 4, false,$instance->id));
                        fwrite ($bf,full_tag('NAME',4,false,$blockname));
                        fwrite ($bf,full_tag('PAGEID',4,false,$instance->pageid));
                        fwrite ($bf,full_tag('PAGETYPE',4,false,$instance->pagetype));
                        fwrite ($bf,full_tag('POSITION',4,false,$instance->position));
                        fwrite ($bf,full_tag('WEIGHT',4,false,$instance->weight));
                        fwrite ($bf,full_tag('VISIBLE',4,false,$instance->visible));
                        fwrite ($bf,full_tag('CONFIGDATA',4,false,$instance->configdata));
                        // Write instance data if needed
                        if ($blockobj->backuprestore_instancedata_used()) {
                            fwrite ($bf,start_tag('INSTANCEDATA',4,true));
                            $status = $blockobj->instance_backup($bf, $preferences);
                            fwrite ($bf,end_tag('INSTANCEDATA',4,true));
                        }
                        $context = get_context_instance(CONTEXT_BLOCK, $instance->id);
                        write_role_overrides_xml($bf, $context, 4);
                        /// write role_assign code here
                        write_role_assignments_xml($bf, $preferences, $context, 4);
                        //End Block
                        fwrite ($bf,end_tag('BLOCK',3,true));
                    }
                }
            }
        }

        //Blocks close tag
        $status = fwrite ($bf,end_tag('BLOCKS',2,true));

        return $status;

    }

    //Prints course's sections info (table course_sections)
    function backup_course_sections ($bf,$preferences) {

        global $CFG;

        $status = true;


        //Get info from sections
        $section=false;
        if ($sections = get_records("course_sections","course",$preferences->backup_course,"section")) {
            //Section open tag
            fwrite ($bf,start_tag("SECTIONS",2,true));
            //Iterate over every section (ordered by section)
            foreach ($sections as $section) {
                //Begin Section
                fwrite ($bf,start_tag("SECTION",3,true));
                fwrite ($bf,full_tag("ID",4,false,$section->id));
                fwrite ($bf,full_tag("NUMBER",4,false,$section->section));
                fwrite ($bf,full_tag("SUMMARY",4,false,$section->summary));
                fwrite ($bf,full_tag("VISIBLE",4,false,$section->visible));
                //Now print the mods in section
                backup_course_modules ($bf,$preferences,$section);
                //End section
                fwrite ($bf,end_tag("SECTION",3,true));
            }
            //Section close tag
            $status = fwrite ($bf,end_tag("SECTIONS",2,true));
        }

        return $status;

    }

    //Prints course's format data (any data the format might want to save).
    function backup_format_data ($bf,$preferences) {
        global $CFG;

        // Check course format
        if(!($format=get_field('course','format','id',$preferences->backup_course))) {
                return false;
        }
        // Write appropriate tag. Note that we always put this tag there even if
        // blank, it makes parsing easier
        fwrite ($bf,start_tag("FORMATDATA",2,true));

        $file=$CFG->dirroot."/course/format/$format/backuplib.php";
        if(file_exists($file)) {
            // If the file is there, the function must be or it's an error.
            require_once($file);
            $function=$format.'_backup_format_data';
            if(!function_exists($function)) {
                    return false;
            }
            if(!$function($bf,$preferences)) {
                    return false;
            }
        }

        // This last return just checks the file writing has been ok (ish)
        return fwrite ($bf,end_tag("FORMATDATA",2,true));
    }

    //Prints course's modules info (table course_modules)
    //Only for selected mods in preferences
    function backup_course_modules ($bf,$preferences,$section) {

        global $CFG;

        $status = true;

        $first_record = true;

        //Now print the mods in section
        //Extracts mod id from sequence
        $tok = strtok($section->sequence,",");
        while ($tok) {
           //Get module's type
           $moduletype = get_module_type ($preferences->backup_course,$tok);
           //Check if we've selected to backup that type
           if ($moduletype and $preferences->mods[$moduletype]->backup) {
               $selected = true;
           } else {
               $selected = false;
           }

           if ($selected) {
               $context = get_context_instance(CONTEXT_MODULE, $tok);
               //Gets course_module data from db - verify activity exists and is enabled!
               $sql = "SELECT cm.*
                         FROM {$CFG->prefix}course_modules cm
                              JOIN {$CFG->prefix}modules m     ON m.id = cm.module
                              JOIN {$CFG->prefix}$moduletype a ON a.id = cm.instance
                        WHERE m.visible = 1 AND cm.id = $tok";
               if (!$course_module = get_record_sql($sql)) {
                   // cm exists but activity instance missing - probably caused by double clicking
                   $tok = strtok(",");
                   continue;
               }

               //If it's the first, pring MODS tag
               if ($first_record) {
                   fwrite ($bf,start_tag("MODS",4,true));
                   $first_record = false;
               }
               // if we're doing selected instances, check that too.
               if (is_array($preferences->mods[$moduletype]->instances)
                   && count($preferences->mods[$moduletype]->instances)
                   && (!array_key_exists($course_module->instance,$preferences->mods[$moduletype]->instances)
                       || empty($preferences->mods[$moduletype]->instances[$course_module->instance]->backup))) {
                   $tok = strtok(",");
                   continue;
               }

               // find all role values that has an override in this context
               $roles = get_records('role_capabilities', 'contextid', $context->id);

               //Print mod info from course_modules
               fwrite ($bf,start_tag("MOD",5,true));
               //Save neccesary info to backup_ids
               fwrite ($bf,full_tag("ID",6,false,$tok));
               fwrite ($bf,full_tag("TYPE",6,false,$moduletype));
               fwrite ($bf,full_tag("INSTANCE",6,false,$course_module->instance));
               fwrite ($bf,full_tag("ADDED",6,false,$course_module->added));
               fwrite ($bf,full_tag("SCORE",6,false,$course_module->score));
               fwrite ($bf,full_tag("INDENT",6,false,$course_module->indent));
               fwrite ($bf,full_tag("VISIBLE",6,false,$course_module->visible));
               fwrite ($bf,full_tag("GROUPMODE",6,false,$course_module->groupmode));
               fwrite ($bf,full_tag("GROUPINGID",6,false,$course_module->groupingid));
               fwrite ($bf,full_tag("GROUPMEMBERSONLY",6,false,$course_module->groupmembersonly));
               fwrite ($bf,full_tag("IDNUMBER",6,false,$course_module->idnumber));
               // get all the role_capabilities overrides in this mod
               write_role_overrides_xml($bf, $context, 6);
               /// write role_assign code here
               write_role_assignments_xml($bf, $preferences, $context, 6);
               /// write role_assign code here

               fwrite ($bf,end_tag("MOD",5,true));
           }
           //check for next
           $tok = strtok(",");
        }

        //Si ha habido modulos, final de MODS
        if (!$first_record) {
            $status =fwrite ($bf,end_tag("MODS",4,true));
        }

        return $status;
    }

    //Print users to xml
    //Only users previously calculated in backup_ids will output
    //
    function backup_user_info ($bf,$preferences) {

        global $CFG;
        require_once ($CFG->dirroot.'/tag/lib.php');

        $status = true;

        // Use a recordset to for the memory handling on to
        // the DB and run faster
        // Note the outer join with mnet_host: It shouldn't be neccesary
        // but there are some sites having mnet_host records missing
        // and that causes backup to fail (no users). Being a bit more
        // flexible here (outer joing) we bypass the problem and doesn't
        // cause more troubles. Eloy - MDL-16879
        $users = get_recordset_sql("SELECT b.old_id, b.table_name, b.info,
                                           u.*, m.wwwroot
                                    FROM   {$CFG->prefix}backup_ids b
                                      JOIN {$CFG->prefix}user       u ON b.old_id=u.id
                                      LEFT JOIN {$CFG->prefix}mnet_host  m ON u.mnethostid=m.id
                                    WHERE b.backup_code = '$preferences->backup_unique_code' AND
                                          b.table_name = 'user'");

        //If we have users to backup
        if ($users && !rs_EOF($users)) {
            //Begin Users tag
            fwrite ($bf,start_tag("USERS",2,true));
            $counter = 0;
            //With every user
            while ($user = rs_fetch_next_record($users)) {
                //Begin User tag
                fwrite ($bf,start_tag("USER",3,true));
                //Output all user data
                fwrite ($bf,full_tag("ID",4,false,$user->id));
                fwrite ($bf,full_tag("AUTH",4,false,$user->auth));
                fwrite ($bf,full_tag("CONFIRMED",4,false,$user->confirmed));
                fwrite ($bf,full_tag("POLICYAGREED",4,false,$user->policyagreed));
                fwrite ($bf,full_tag("DELETED",4,false,$user->deleted));
                fwrite ($bf,full_tag("USERNAME",4,false,$user->username));
                // Prevent user passwords in backup files unless
                // $CFG->includeuserpasswordsinbackup is defined. MDL-20838
                if (!empty($CFG->includeuserpasswordsinbackup)) {
                    fwrite ($bf,full_tag("PASSWORD",4,false,$user->password));
                }
                fwrite ($bf,full_tag("IDNUMBER",4,false,$user->idnumber));
                fwrite ($bf,full_tag("FIRSTNAME",4,false,$user->firstname));
                fwrite ($bf,full_tag("LASTNAME",4,false,$user->lastname));
                fwrite ($bf,full_tag("EMAIL",4,false,$user->email));
                fwrite ($bf,full_tag("EMAILSTOP",4,false,$user->emailstop));
                fwrite ($bf,full_tag("ICQ",4,false,$user->icq));
                fwrite ($bf,full_tag("SKYPE",4,false,$user->skype));
                fwrite ($bf,full_tag("YAHOO",4,false,$user->yahoo));
                fwrite ($bf,full_tag("AIM",4,false,$user->aim));
                fwrite ($bf,full_tag("MSN",4,false,$user->msn));
                fwrite ($bf,full_tag("PHONE1",4,false,$user->phone1));
                fwrite ($bf,full_tag("PHONE2",4,false,$user->phone2));
                fwrite ($bf,full_tag("INSTITUTION",4,false,$user->institution));
                fwrite ($bf,full_tag("DEPARTMENT",4,false,$user->department));
                fwrite ($bf,full_tag("ADDRESS",4,false,$user->address));
                fwrite ($bf,full_tag("CITY",4,false,$user->city));
                fwrite ($bf,full_tag("COUNTRY",4,false,$user->country));
                fwrite ($bf,full_tag("LANG",4,false,$user->lang));
                fwrite ($bf,full_tag("THEME",4,false,$user->theme));
                fwrite ($bf,full_tag("TIMEZONE",4,false,$user->timezone));
                fwrite ($bf,full_tag("FIRSTACCESS",4,false,$user->firstaccess));
                fwrite ($bf,full_tag("LASTACCESS",4,false,$user->lastaccess));
                fwrite ($bf,full_tag("LASTLOGIN",4,false,$user->lastlogin));
                fwrite ($bf,full_tag("CURRENTLOGIN",4,false,$user->currentlogin));
                fwrite ($bf,full_tag("LASTIP",4,false,$user->lastip));
                fwrite ($bf,full_tag("PICTURE",4,false,$user->picture));
                fwrite ($bf,full_tag("URL",4,false,$user->url));
                fwrite ($bf,full_tag("DESCRIPTION",4,false,$user->description));
                fwrite ($bf,full_tag("MAILFORMAT",4,false,$user->mailformat));
                fwrite ($bf,full_tag("MAILDIGEST",4,false,$user->maildigest));
                fwrite ($bf,full_tag("MAILDISPLAY",4,false,$user->maildisplay));
                fwrite ($bf,full_tag("HTMLEDITOR",4,false,$user->htmleditor));
                fwrite ($bf,full_tag("AJAX",4,false,$user->ajax));
                fwrite ($bf,full_tag("AUTOSUBSCRIBE",4,false,$user->autosubscribe));
                fwrite ($bf,full_tag("TRACKFORUMS",4,false,$user->trackforums));
                if ($user->mnethostid != $CFG->mnet_localhost_id && !empty($user->wwwroot)) {
                    fwrite ($bf,full_tag("MNETHOSTURL",4,false,$user->wwwroot));
                }
                fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$user->timemodified));

                /// write assign/override code for context_userid

                $user->isneeded = strpos($user->info,"needed");

                fwrite ($bf,start_tag("ROLES",4,true));
                if ($user->info != "needed" && $user->info!="") {
                    //PRINT ROLE INFO
                    $roles = explode(",", $user->info);
                    foreach ($roles as $role) {
                        if ($role!="" && $role!="needed") {
                            fwrite ($bf,start_tag("ROLE",5,true));
                            //Print Role info
                            fwrite ($bf,full_tag("TYPE",6,false,$role));
                            //Print ROLE end
                            fwrite ($bf,end_tag("ROLE",5,true));
                        }
                    }
                }
                    //Needed
                if ($user->isneeded!==false) {
                    //Print ROLE start
                    fwrite ($bf,start_tag("ROLE",5,true));
                    //Print Role info
                    fwrite ($bf,full_tag("TYPE",6,false,"needed"));
                    //Print ROLE end
                    fwrite ($bf,end_tag("ROLE",5,true));
                }

                    //End ROLES tag
                fwrite ($bf,end_tag("ROLES",4,true));

                //Check if we have custom profile fields to backup
                if ($cpfields = get_records_sql("SELECT uif.shortname, uif.datatype, uidata.data
                                                 FROM {$CFG->prefix}user_info_field uif,
                                                      {$CFG->prefix}user_info_data uidata
                                                 WHERE uif.id = uidata.fieldid
                                                   AND uidata.userid = $user->id")) {
                    //Start USER_CUSTOM_PROFILE_FIELDS tag
                    fwrite ($bf,start_tag("USER_CUSTOM_PROFILE_FIELDS",4,true));
                    //Write custom profile fields
                    foreach ($cpfields as $cpfield) {
                        fwrite ($bf,start_tag("USER_CUSTOM_PROFILE_FIELD",5,true));
                        fwrite ($bf,full_tag("FIELD_NAME",6,false,$cpfield->shortname));
                        fwrite ($bf,full_tag("FIELD_TYPE",6,false,$cpfield->datatype));
                        fwrite ($bf,full_tag("FIELD_DATA",6,false,$cpfield->data));
                        fwrite ($bf,end_tag("USER_CUSTOM_PROFILE_FIELD",5,true));
                    }
                    //End USER_CUSTOM_PROFILE_FIELDS tag
                    fwrite ($bf,end_tag("USER_CUSTOM_PROFILE_FIELDS",4,true));
                }

                //Check if we have user tags to backup
                if (!empty($CFG->usetags)) {
                    if ($tags = tag_get_tags('user', $user->id)) { //This return them ordered by default
                        //Start USER_TAGS tag
                        fwrite ($bf,start_tag("USER_TAGS",4,true));
                        //Write user tags fields
                        foreach ($tags as $tag) {
                            fwrite ($bf,start_tag("USER_TAG",5,true));
                            fwrite ($bf,full_tag("NAME",6,false,$tag->name));
                            fwrite ($bf,full_tag("RAWNAME",6,false,$tag->rawname));
                            fwrite ($bf,end_tag("USER_TAG",5,true));
                        }
                        //End USER_TAGS tag
                        fwrite ($bf,end_tag("USER_TAGS",4,true));
                    }
                }

                //Check if we have user_preferences to backup
                if ($preferences_data = get_records("user_preferences","userid",$user->old_id)) {
                    //Start USER_PREFERENCES tag
                    fwrite ($bf,start_tag("USER_PREFERENCES",4,true));
                    //Write each user_preference
                    foreach ($preferences_data as $user_preference) {
                        fwrite ($bf,start_tag("USER_PREFERENCE",5,true));
                        fwrite ($bf,full_tag("NAME",6,false,$user_preference->name));
                        fwrite ($bf,full_tag("VALUE",6,false,$user_preference->value));
                        fwrite ($bf,end_tag("USER_PREFERENCE",5,true));
                    }
                        //End USER_PREFERENCES tag
                    fwrite ($bf,end_tag("USER_PREFERENCES",4,true));
                }

                $context = get_context_instance(CONTEXT_USER, $user->old_id);

                write_role_overrides_xml($bf, $context, 4);
                /// write role_assign code here
                write_role_assignments_xml($bf, $preferences, $context, 4);
                //End User tag
                fwrite ($bf,end_tag("USER",3,true));
                //Do some output
                $counter++;
                if ($counter % 10 == 0) {
                    echo ".";
                    if ($counter % 200 == 0) {
                        echo "<br />";
                    }
                    backup_flush(300);
                }
            }
            //End Users tag
            fwrite ($bf,end_tag("USERS",2,true));
        } else {
            // There aren't any users.
            $status = true;
        }

        if ($users) {
            rs_close($users);
        }

        return $status;
    }

    //Backup log info (time ordered)
    function backup_log_info($bf,$preferences) {

        global $CFG;

        //Number of records to get in every chunk
        $recordset_size = 1000;

        $status = true;

        //Counter, points to current record
        $counter = 0;

        //Count records
        $count_logs = count_records("log","course",$preferences->backup_course);

        //Pring logs header
        if ($count_logs > 0 ) {
            fwrite ($bf,start_tag("LOGS",2,true));
        }
        while ($counter < $count_logs) {
            //Get a chunk of records
            $logs = get_records ("log","course",$preferences->backup_course,"time","*",$counter,$recordset_size);

            //We have logs
            if ($logs) {
                //Iterate
                foreach ($logs as $log) {
                    //See if it is a valid module to backup
                    if ($log->module == "course" or
                        $log->module == "user" or
                        (array_key_exists($log->module, $preferences->mods) and $preferences->mods[$log->module]->backup == 1)) {
                        // logs with 'upload' in module field are ignored, there is no restore code anyway
                        //Begin log tag
                         fwrite ($bf,start_tag("LOG",3,true));

                        //Output log tag
                        fwrite ($bf,full_tag("ID",4,false,$log->id));
                        fwrite ($bf,full_tag("TIME",4,false,$log->time));
                        fwrite ($bf,full_tag("USERID",4,false,$log->userid));
                        fwrite ($bf,full_tag("IP",4,false,$log->ip));
                        fwrite ($bf,full_tag("MODULE",4,false,$log->module));
                        fwrite ($bf,full_tag("CMID",4,false,$log->cmid));
                        fwrite ($bf,full_tag("ACTION",4,false,$log->action));
                        fwrite ($bf,full_tag("URL",4,false,$log->url));
                        fwrite ($bf,full_tag("INFO",4,false,$log->info));

                        //End log tag
                         fwrite ($bf,end_tag("LOG",3,true));
                    }
                    //Do some output
                    $counter++;
                    if ($counter % 20 == 0) {
                        echo ".";
                        if ($counter % 400 == 0) {
                            echo "<br />";
                        }
                        backup_flush(300);
                    }
                }
            }
        }
        //End logs tag
        if ($count_logs > 0 ) {
            $status = fwrite ($bf,end_tag("LOGS",2,true));
        }
        return $status;
    }

    //Backup gradebook info
    function backup_gradebook_info($bf, $preferences) {
        global $CFG;
        require_once($CFG->libdir.'/gradelib.php');

        //first make sure items are properly sorted and everything is ok
        grade_category::fetch_course_tree($preferences->backup_course, true);
        grade_regrade_final_grades($preferences->backup_course);

        $status = true;

        // see if ALL grade items of type mod of this course are being backed up
        // if not, we do not need to backup grade category and associated grade items/grades
        $backupall = true;

        if ($grade_items = get_records_sql("SELECT *
                                              FROM {$CFG->prefix}grade_items
                                             WHERE courseid = $preferences->backup_course
                                                   AND itemtype = 'mod'")) {
            foreach ($grade_items as $grade_item) {
                // get module information
                // if some activities not selected, we do not backup categories at all
                if (!backup_mod_selected($preferences,$grade_item->itemmodule,$grade_item->iteminstance)) {
                    $backupall = false;
                    break;
                }
            }
            unset($grade_items); //free memory
        }

        //Gradebook header
        fwrite ($bf,start_tag("GRADEBOOK",2,true));

        $status = backup_gradebook_outcomes_info($bf, $preferences);
        $status = backup_gradebook_grade_letters_info($bf,$preferences);

        // Now backup grade_item (inside grade_category)
        if ($backupall) {
            $status = backup_gradebook_category_info($bf,$preferences);
        }

        $status = backup_gradebook_item_info($bf,$preferences, $backupall);

        // backup gradebook histories (only if grade history is enabled and selected)
        if (empty($CFG->disablegradehistory) && $preferences->backup_gradebook_history) {
            $status = backup_gradebook_outcomes_history($bf, $preferences);
            $status = backup_gradebook_categories_history_info($bf, $preferences);
            $status = backup_gradebook_items_history_info($bf, $preferences);
            $status = backup_gradebook_grades_history_info($bf, $preferences);
        }

        //Gradebook footer
        $status = fwrite ($bf,end_tag("GRADEBOOK",2,true));
        return $status;
    }

    function backup_gradebook_category_info($bf, $preferences) {
        global $CFG;
        $status = true;

        // get grade categories in proper order - specified in category grade items
        $course_item = grade_item::fetch_course_item($preferences->backup_course);
        $grade_categories = get_records_sql("SELECT gc.*, gi.sortorder
                                               FROM {$CFG->prefix}grade_categories gc
                                                    JOIN {$CFG->prefix}grade_items gi
                                                      ON (gi.iteminstance = gc.id)
                                              WHERE gc.courseid = $preferences->backup_course
                                                    AND (gi.itemtype='course' OR gi.itemtype='category')
                                           ORDER BY gi.sortorder ASC");

        if ($grade_categories) {
            //Begin grade_categories tag
            fwrite ($bf,start_tag("GRADE_CATEGORIES",3,true));
            //Iterate for each category
            foreach ($grade_categories as $grade_category) {
                //Begin grade_category
                fwrite ($bf,start_tag("GRADE_CATEGORY",4,true));
                //Output individual fields
                fwrite ($bf,full_tag("ID",5,false,$grade_category->id));

                // not keeping path and depth because they can be derived
                fwrite ($bf,full_tag("PARENT",5,false,$grade_category->parent));
                fwrite ($bf,full_tag("FULLNAME",5,false,$grade_category->fullname));
                fwrite ($bf,full_tag("AGGREGATION",5,false,$grade_category->aggregation));
                fwrite ($bf,full_tag("KEEPHIGH",5,false,$grade_category->keephigh));
                fwrite ($bf,full_tag("DROPLOW",5,false,$grade_category->droplow));
                fwrite ($bf,full_tag("AGGREGATEONLYGRADED",5,false,$grade_category->aggregateonlygraded));
                fwrite ($bf,full_tag("AGGREGATEOUTCOMES",5,false,$grade_category->aggregateoutcomes));
                fwrite ($bf,full_tag("AGGREGATESUBCATS",5,false,$grade_category->aggregatesubcats));
                fwrite ($bf,full_tag("TIMECREATED",5,false,$grade_category->timecreated));
                fwrite ($bf,full_tag("TIMEMODIFIED",5,false,$grade_category->timemodified));

                //End grade_category
                fwrite ($bf,end_tag("GRADE_CATEGORY",4,true));
            }
            //End grade_categories tag
            $status = fwrite ($bf,end_tag("GRADE_CATEGORIES",3,true));
        }

        return $status;
    }

    //Backup gradebook_item (called from backup_gradebook_info
    function backup_gradebook_item_info($bf, $preferences, $backupall) {
        global $CFG;

        $status = true;
        // get all the grade_items, ordered by sort order since upon restoring, it is not always
        // possible to use the same sort order. We could at least preserve the sortorder by restoring
        // grade_items in the original sortorder
        if ($grade_items = get_records_sql("SELECT *
                                              FROM {$CFG->prefix}grade_items
                                             WHERE courseid = $preferences->backup_course
                                          ORDER BY sortorder ASC")) {

            //Begin grade_items tag
            fwrite ($bf,start_tag("GRADE_ITEMS",3,true));
            //Iterate over each item
            foreach ($grade_items as $item) {
                // Instantiate a grade_item object, to access its methods
                $grade_item = new grade_item($item, false);

                // do not restore if this grade_item is a mod, and
                if ($grade_item->itemtype == 'mod') {
                    //MDL-12463 - exclude grade_items of instances not chosen for backup
                    if (empty($preferences->mods[$grade_item->itemmodule]->instances[$grade_item->iteminstance]->backup)) {
                        continue;
                    }

                } else if ($grade_item->itemtype == 'category') {
                    // if not all grade items are being backed up
                    // we ignore this type of grade_item and grades associated
                    if (!$backupall) {
                        continue;
                    }
                }

                //Begin grade_item
                fwrite ($bf,start_tag("GRADE_ITEM",4,true));
                //Output individual fields

                fwrite ($bf,full_tag("ID",5,false,$grade_item->id));
                fwrite ($bf,full_tag("CATEGORYID",5,false,$grade_item->categoryid));
                fwrite ($bf,full_tag("ITEMNAME",5,false,$grade_item->itemname));
                fwrite ($bf,full_tag("ITEMTYPE",5,false,$grade_item->itemtype));
                fwrite ($bf,full_tag("ITEMMODULE",5,false,$grade_item->itemmodule));
                fwrite ($bf,full_tag("ITEMINSTANCE",5,false,$grade_item->iteminstance));
                fwrite ($bf,full_tag("ITEMNUMBER",5,false,$grade_item->itemnumber));
                fwrite ($bf,full_tag("ITEMINFO",5,false,$grade_item->iteminfo));
                fwrite ($bf,full_tag("IDNUMBER",5,false,$grade_item->idnumber));
                // use [idnumber] in calculation instead of [#giXXX#]
                fwrite ($bf,full_tag("CALCULATION",5,false,$grade_item->get_calculation()));
                fwrite ($bf,full_tag("GRADETYPE",5,false,$grade_item->gradetype));
                fwrite ($bf,full_tag("GRADEMAX",5,false,$grade_item->grademax));
                fwrite ($bf,full_tag("GRADEMIN",5,false,$grade_item->grademin));
                fwrite ($bf,full_tag("SCALEID",5,false,$grade_item->scaleid));
                fwrite ($bf,full_tag("OUTCOMEID",5,false,$grade_item->outcomeid));
                fwrite ($bf,full_tag("GRADEPASS",5,false,$grade_item->gradepass));
                fwrite ($bf,full_tag("MULTFACTOR",5,false,$grade_item->multfactor));
                fwrite ($bf,full_tag("PLUSFACTOR",5,false,$grade_item->plusfactor));
                fwrite ($bf,full_tag("AGGREGATIONCOEF",5,false,$grade_item->aggregationcoef));
                fwrite ($bf,full_tag("DISPLAY",5,false,$grade_item->display));
                fwrite ($bf,full_tag("DECIMALS",5,false,$grade_item->decimals));
                fwrite ($bf,full_tag("HIDDEN",5,false,$grade_item->hidden));
                fwrite ($bf,full_tag("LOCKED",5,false,$grade_item->locked));
                fwrite ($bf,full_tag("LOCKTIME",5,false,$grade_item->locktime));
                fwrite ($bf,full_tag("NEEDSUPDATE",5,false,$grade_item->needsupdate));
                fwrite ($bf,full_tag("TIMECREATED",5,false,$grade_item->timecreated));
                fwrite ($bf,full_tag("TIMEMODIFIED",5,false,$grade_item->timemodified));

                // back up the other stuff here
                // mod grades should only be backed up if selected
                if ($grade_item->itemtype == 'mod' && !backup_userdata_selected($preferences,$grade_item->itemmodule,$grade_item->iteminstance)) {
                    // do not write grades if a mod grade_item is being restored
                    // but userdata is not selected
                } else {
                    $status = backup_gradebook_grades_info($bf,$preferences,$grade_item->id);
                }
                //End grade_item
                fwrite ($bf,end_tag("GRADE_ITEM",4,true));
            }
            //End grade_items tag
            $status = fwrite ($bf,end_tag("GRADE_ITEMS",3,true));
        }

        return $status;
    }
    //Backup gradebook_item (called from backup_gradebook_info

    function backup_gradebook_grade_letters_info($bf, $preferences) {
        global $CFG;
        $status = true;

        $context = get_context_instance(CONTEXT_COURSE, $preferences->backup_course);
        $grade_letters = get_records_sql("SELECT *
                                            FROM {$CFG->prefix}grade_letters
                                           WHERE contextid = $context->id");
        if ($grade_letters) {
            //Begin grade_l tag
            fwrite ($bf,start_tag("GRADE_LETTERS",3,true));
            //Iterate for each letter
            foreach ($grade_letters as $grade_letter) {
                //Begin grade_letter
                fwrite ($bf,start_tag("GRADE_LETTER",4,true));
                //Output individual fields
                fwrite ($bf,full_tag("ID",5,false,$grade_letter->id));
                fwrite ($bf,full_tag("LOWERBOUNDARY",5,false,$grade_letter->lowerboundary));
                fwrite ($bf,full_tag("LETTER",5,false,$grade_letter->letter));

                //End grade_letter
                fwrite ($bf,end_tag("GRADE_LETTER",4,true));
            }
            //End grade_categories tag
            $status = fwrite ($bf,end_tag("GRADE_LETTERS",3,true));
        }

        return $status;
    }

    function backup_gradebook_outcomes_info($bf, $preferences) {

        global $CFG;
        $status = true;
        // only back up courses already in the grade_outcomes_courses table
        $grade_outcomes = get_records_sql("SELECT go.*
                                             FROM {$CFG->prefix}grade_outcomes go
                                                  JOIN {$CFG->prefix}grade_outcomes_courses goc
                                                    ON (goc.outcomeid = go.id)
                                            WHERE goc.courseid = $preferences->backup_course");

        if (!empty($grade_outcomes)) {
            //Begin grade_outcomes tag
            fwrite ($bf,start_tag("GRADE_OUTCOMES",3,true));
            //Iterate for each outcome
            foreach ($grade_outcomes as $grade_outcome) {
                //Begin grade_outcome
                fwrite ($bf,start_tag("GRADE_OUTCOME",4,true));
                //Output individual fields

                fwrite ($bf,full_tag("ID",5,false,$grade_outcome->id));
                fwrite ($bf,full_tag("COURSEID",5,false,$grade_outcome->courseid));
                fwrite ($bf,full_tag("SHORTNAME",5,false,$grade_outcome->shortname));
                fwrite ($bf,full_tag("FULLNAME",5,false,$grade_outcome->fullname));
                fwrite ($bf,full_tag("SCALEID",5,false,$grade_outcome->scaleid));
                fwrite ($bf,full_tag("DESCRIPTION",5,false,$grade_outcome->description));
                fwrite ($bf,full_tag("TIMECREATED",5,false,$grade_outcome->timecreated));
                fwrite ($bf,full_tag("TIMEMODIFIED",5,false,$grade_outcome->timemodified));
                fwrite ($bf,full_tag("USERMODIFIED",5,false,$grade_outcome->usermodified));

                //End grade_outcome
                fwrite ($bf,end_tag("GRADE_OUTCOME",4,true));
            }
            //End grade_outcomes tag
            $status = fwrite ($bf,end_tag("GRADE_OUTCOMES",3,true));
        }
        return $status;
    }

    function backup_gradebook_grades_info($bf,$preferences, $itemid) {

        global $CFG;

        $status = true;

        // find all grades belonging to this item
        if ($grades = get_records('grade_grades', 'itemid', $itemid)) {
            fwrite ($bf,start_tag("GRADE_GRADES",5,true));
            foreach ($grades as $grade) {
            /// Grades are only sent to backup if the user is one target user
                if (backup_getid($preferences->backup_unique_code, 'user', $grade->userid)) {
                    fwrite ($bf,start_tag("GRADE",6,true));
                    fwrite ($bf,full_tag("ID",7,false,$grade->id));
                    fwrite ($bf,full_tag("USERID",7,false,$grade->userid));
                    fwrite ($bf,full_tag("RAWGRADE",7,false,$grade->rawgrade));
                    fwrite ($bf,full_tag("RAWGRADEMAX",7,false,$grade->rawgrademax));
                    fwrite ($bf,full_tag("RAWGRADEMIN",7,false,$grade->rawgrademin));
                    fwrite ($bf,full_tag("RAWSCALEID",7,false,$grade->rawscaleid));
                    fwrite ($bf,full_tag("USERMODIFIED",7,false,$grade->usermodified));
                    fwrite ($bf,full_tag("FINALGRADE",7,false,$grade->finalgrade));
                    fwrite ($bf,full_tag("HIDDEN",7,false,$grade->hidden));
                    fwrite ($bf,full_tag("LOCKED",7,false,$grade->locked));
                    fwrite ($bf,full_tag("LOCKTIME",7,false,$grade->locktime));
                    fwrite ($bf,full_tag("EXPORTED",7,false,$grade->exported));
                    fwrite ($bf,full_tag("OVERRIDDEN",7,false,$grade->overridden));
                    fwrite ($bf,full_tag("EXCLUDED",7,false,$grade->excluded));
                    fwrite ($bf,full_tag("FEEDBACK",7,false,$grade->feedback));
                    fwrite ($bf,full_tag("FEEDBACKFORMAT",7,false,$grade->feedbackformat));
                    fwrite ($bf,full_tag("INFORMATION",7,false,$grade->information));
                    fwrite ($bf,full_tag("INFORMATIONFORMAT",7,false,$grade->informationformat));
                    fwrite ($bf,full_tag("TIMECREATED",7,false,$grade->timecreated));
                    fwrite ($bf,full_tag("TIMEMODIFIED",7,false,$grade->timemodified));
                    fwrite ($bf,end_tag("GRADE",6,true));
                }
            }
            $status = fwrite ($bf,end_tag("GRADE_GRADES",5,true));
        }
        return $status;
    }

    function backup_gradebook_categories_history_info($bf, $preferences) {

        global $CFG;
        $status = true;

        // find all grade categories history
        if ($chs = get_records('grade_categories_history', 'courseid', $preferences->backup_course)) {
            fwrite ($bf,start_tag("GRADE_CATEGORIES_HISTORIES",3,true));
            foreach ($chs as $ch) {
                fwrite ($bf,start_tag("GRADE_CATEGORIES_HISTORY",4,true));
                fwrite ($bf,full_tag("ID",5,false,$ch->id));
                fwrite ($bf,full_tag("ACTION",5,false,$ch->action));
                fwrite ($bf,full_tag("OLDID",5,false,$ch->oldid));
                fwrite ($bf,full_tag("SOURCE",5,false,$ch->source));
                fwrite ($bf,full_tag("TIMEMODIFIED",5,false,$ch->timemodified));
                fwrite ($bf,full_tag("LOGGEDUSER",5,false,$ch->loggeduser));
                fwrite ($bf,full_tag("PARENT",5,false,$ch->parent));
                fwrite ($bf,full_tag("DEPTH",5,false,$ch->depth));
                fwrite ($bf,full_tag("PATH",5,false,$ch->path));
                fwrite ($bf,full_tag("FULLNAME",5,false,$ch->fullname));
                fwrite ($bf,full_tag("AGGRETGATION",5,false,$ch->aggregation));
                fwrite ($bf,full_tag("KEEPHIGH",5,false,$ch->keephigh));
                fwrite ($bf,full_tag("DROPLOW",5,false,$ch->droplow));
                fwrite ($bf,full_tag("AGGREGATEONLYGRADED",5,false,$ch->aggregateonlygraded));
                fwrite ($bf,full_tag("AGGREGATEOUTCOMES",5,false,$ch->aggregateoutcomes));
                fwrite ($bf,full_tag("AGGREGATESUBCATS",5,false,$ch->aggregatesubcats));
                fwrite ($bf,end_tag("GRADE_CATEGORIES_HISTORY",4,true));
            }
            $status = fwrite ($bf,end_tag("GRADE_CATEGORIES_HISTORIES",3,true));
        }
        return $status;
    }

    function backup_gradebook_grades_history_info($bf, $preferences) {

        global $CFG;
        $status = true;

        // find all grade categories history
        if ($chs = get_records_sql("SELECT ggh.*
                                      FROM {$CFG->prefix}grade_grades_history ggh
                                           JOIN {$CFG->prefix}grade_items gi
                                             ON gi.id = ggh.itemid
                                     WHERE gi.courseid = $preferences->backup_course")) {
            fwrite ($bf,start_tag("GRADE_GRADES_HISTORIES",3,true));
            foreach ($chs as $ch) {
            /// Grades are only sent to backup if the user is one target user
                if (backup_getid($preferences->backup_unique_code, 'user', $ch->userid)) {
                    fwrite ($bf,start_tag("GRADE_GRADES_HISTORY",4,true));
                    fwrite ($bf,full_tag("ID",5,false,$ch->id));
                    fwrite ($bf,full_tag("ACTION",5,false,$ch->action));
                    fwrite ($bf,full_tag("OLDID",5,false,$ch->oldid));
                    fwrite ($bf,full_tag("SOURCE",5,false,$ch->source));
                    fwrite ($bf,full_tag("TIMEMODIFIED",5,false,$ch->timemodified));
                    fwrite ($bf,full_tag("LOGGEDUSER",5,false,$ch->loggeduser));
                    fwrite ($bf,full_tag("ITEMID",5,false,$ch->itemid));
                    fwrite ($bf,full_tag("USERID",5,false,$ch->userid));
                    fwrite ($bf,full_tag("RAWGRADE",5,false,$ch->rawgrade));
                    fwrite ($bf,full_tag("RAWGRADEMAX",5,false,$ch->rawgrademax));
                    fwrite ($bf,full_tag("RAWGRADEMIN",5,false,$ch->rawgrademin));
                    fwrite ($bf,full_tag("RAWSCALEID",5,false,$ch->rawscaleid));
                    fwrite ($bf,full_tag("USERMODIFIED",5,false,$ch->usermodified));
                    fwrite ($bf,full_tag("FINALGRADE",5,false,$ch->finalgrade));
                    fwrite ($bf,full_tag("HIDDEN",5,false,$ch->hidden));
                    fwrite ($bf,full_tag("LOCKED",5,false,$ch->locked));
                    fwrite ($bf,full_tag("LOCKTIME",5,false,$ch->locktime));
                    fwrite ($bf,full_tag("EXPORTED",5,false,$ch->exported));
                    fwrite ($bf,full_tag("OVERRIDDEN",5,false,$ch->overridden));
                    fwrite ($bf,full_tag("EXCLUDED",5,false,$ch->excluded));
                    fwrite ($bf,full_tag("FEEDBACK",5,false,$ch->feedback));
                    fwrite ($bf,full_tag("FEEDBACKFORMAT",5,false,$ch->feedbackformat));
                    fwrite ($bf,full_tag("INFORMATION",5,false,$ch->information));
                    fwrite ($bf,full_tag("INFORMATIONFORMAT",5,false,$ch->informationformat));
                    fwrite ($bf,end_tag("GRADE_GRADES_HISTORY",4,true));
                }
            }
            $status = fwrite ($bf,end_tag("GRADE_GRADES_HISTORIES",3,true));
        }
        return $status;
    }

    function backup_gradebook_items_history_info($bf, $preferences) {

        global $CFG;
        $status = true;

        // find all grade categories history
        if ($chs = get_records('grade_items_history','courseid', $preferences->backup_course)) {
            fwrite ($bf,start_tag("GRADE_ITEM_HISTORIES",3,true));
            foreach ($chs as $ch) {
                fwrite ($bf,start_tag("GRADE_ITEM_HISTORY",4,true));
                fwrite ($bf,full_tag("ID",5,false,$ch->id));
                fwrite ($bf,full_tag("ACTION",5,false,$ch->action));
                fwrite ($bf,full_tag("OLDID",5,false,$ch->oldid));
                fwrite ($bf,full_tag("SOURCE",5,false,$ch->source));
                fwrite ($bf,full_tag("TIMEMODIFIED",5,false,$ch->timemodified));
                fwrite ($bf,full_tag("LOGGEDUSER",5,false,$ch->loggeduser));
                fwrite ($bf,full_tag("CATEGORYID",5,false,$ch->categoryid));
                fwrite ($bf,full_tag("ITEMNAME",5,false,$ch->itemname));
                fwrite ($bf,full_tag("ITEMTYPE",5,false,$ch->itemtype));
                fwrite ($bf,full_tag("ITEMMODULE",5,false,$ch->itemmodule));
                fwrite ($bf,full_tag("ITEMINSTANCE",5,false,$ch->iteminstance));
                fwrite ($bf,full_tag("ITEMNUMBER",5,false,$ch->itemnumber));
                fwrite ($bf,full_tag("ITEMINFO",5,false,$ch->iteminfo));
                fwrite ($bf,full_tag("IDNUMBER",5,false,$ch->idnumber));
                fwrite ($bf,full_tag("CALCULATION",5,false,$ch->calculation));
                fwrite ($bf,full_tag("GRADETYPE",5,false,$ch->gradetype));
                fwrite ($bf,full_tag("GRADEMAX",5,false,$ch->grademax));
                fwrite ($bf,full_tag("GRADEMIN",5,false,$ch->grademin));
                fwrite ($bf,full_tag("SCALEID",5,false,$ch->scaleid));
                fwrite ($bf,full_tag("OUTCOMEID",5,false,$ch->outcomeid));
                fwrite ($bf,full_tag("GRADEPASS",5,false,$ch->gradepass));
                fwrite ($bf,full_tag("MULTFACTOR",5,false,$ch->multfactor));
                fwrite ($bf,full_tag("PLUSFACTOR",5,false,$ch->plusfactor));
                fwrite ($bf,full_tag("AGGREGATIONCOEF",5,false,$ch->aggregationcoef));
                fwrite ($bf,full_tag("SORTORDER",5,false,$ch->sortorder));
                //fwrite ($bf,full_tag("DISPLAY",7,false,$ch->display));
                //fwrite ($bf,full_tag("DECIMALS",7,false,$ch->decimals));
                fwrite ($bf,full_tag("HIDDEN",5,false,$ch->hidden));
                fwrite ($bf,full_tag("LOCKED",5,false,$ch->locked));
                fwrite ($bf,full_tag("LOCKTIME",5,false,$ch->locktime));
                fwrite ($bf,full_tag("NEEDSUPDATE",5,false,$ch->needsupdate));
                fwrite ($bf,end_tag("GRADE_ITEM_HISTORY",4,true));
            }
            $status = fwrite ($bf,end_tag("GRADE_ITEM_HISTORIES",3,true));

        }
        return $status;
    }

    function backup_gradebook_outcomes_history($bf, $preferences) {
        global $CFG;
        $status = true;

        // find all grade categories history
        if ($chs = get_records('grade_outcomes_history','courseid', $preferences->backup_course)) {
            fwrite ($bf,start_tag("GRADE_OUTCOME_HISTORIES",3,true));
            foreach ($chs as $ch) {
                fwrite ($bf,start_tag("GRADE_OUTCOME_HISTORY",4,true));
                fwrite ($bf,full_tag("ID",5,false,$ch->id));
                fwrite ($bf,full_tag("OLDID",5,false,$ch->oldid));
                fwrite ($bf,full_tag("ACTION",5,false,$ch->action));
                fwrite ($bf,full_tag("SOURCE",5,false,$ch->source));
                fwrite ($bf,full_tag("TIMEMODIFIED",5,false,$ch->timemodified));
                fwrite ($bf,full_tag("LOGGEDUSER",5,false,$ch->loggeduser));
                fwrite ($bf,full_tag("SHORTNAME",5,false,$ch->shortname));
                fwrite ($bf,full_tag("FULLNAME",5,false,$ch->fullname));
                fwrite ($bf,full_tag("SCALEID",5,false,$ch->scaleid));
                fwrite ($bf,full_tag("DESCRIPTION",5,false,$ch->description));
                fwrite ($bf,end_tag("GRADE_OUTCOME_HISTORY",4,true));
            }
            $status = fwrite ($bf,end_tag("GRADE_OUTCOME_HISTORIES",3,true));
        }
        return $status;
    }

    //Backup scales info (common and course scales)
    function backup_scales_info($bf,$preferences) {

        global $CFG;

        $status = true;

        //Counter, points to current record
        $counter = 0;

        //Get scales (common and course scales)
        $scales = get_records_sql("SELECT id, courseid, userid, name, scale, description, timemodified
                                     FROM {$CFG->prefix}scale
                                    WHERE courseid = 0 OR courseid = $preferences->backup_course");

        //Copy only used scales to $backupscales. They will be in backup (unused no). See Bug 1223.
        $backupscales = array();
        if ($scales) {
            foreach ($scales as $scale) {
                if (course_scale_used($preferences->backup_course, $scale->id)) {
                    $backupscales[] = $scale;
                }
            }
        }

        //Pring scales header
        if ($backupscales) {
            //Pring scales header
            fwrite ($bf,start_tag("SCALES",2,true));
            //Iterate
            foreach ($backupscales as $scale) {
                //Begin scale tag
                fwrite ($bf,start_tag("SCALE",3,true));
                //Output scale tag
                fwrite ($bf,full_tag("ID",4,false,$scale->id));
                fwrite ($bf,full_tag("COURSEID",4,false,$scale->courseid));
                fwrite ($bf,full_tag("USERID",4,false,$scale->userid));
                fwrite ($bf,full_tag("NAME",4,false,$scale->name));
                fwrite ($bf,full_tag("SCALETEXT",4,false,$scale->scale));
                fwrite ($bf,full_tag("DESCRIPTION",4,false,$scale->description));
                fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$scale->timemodified));
                //End scale tag
                fwrite ($bf,end_tag("SCALE",3,true));
            }
            //End scales tag
            $status = fwrite ($bf,end_tag("SCALES",2,true));
        }
        return $status;
    }

    //Backup events info (course events)
    function backup_events_info($bf,$preferences) {

        global $CFG;

        $status = true;

        //Counter, points to current record
        $counter = 0;

        //Get events (course events)
        $events = get_records_select("event","courseid='$preferences->backup_course' AND instance='0'","id");

        //Pring events header
        if ($events) {
            //Pring events header
            fwrite ($bf,start_tag("EVENTS",2,true));
            //Iterate
            foreach ($events as $event) {
                //Begin event tag
                fwrite ($bf,start_tag("EVENT",3,true));
                //Output event tag
                fwrite ($bf,full_tag("ID",4,false,$event->id));
                fwrite ($bf,full_tag("NAME",4,false,$event->name));
                fwrite ($bf,full_tag("DESCRIPTION",4,false,$event->description));
                fwrite ($bf,full_tag("FORMAT",4,false,$event->format));
                fwrite ($bf,full_tag("GROUPID",4,false,$event->groupid));
                fwrite ($bf,full_tag("USERID",4,false,$event->userid));
                fwrite ($bf,full_tag("REPEATID",4,false,$event->repeatid));
                fwrite ($bf,full_tag("EVENTTYPE",4,false,$event->eventtype));
                fwrite ($bf,full_tag("MODULENAME",4,false,$event->modulename));
                fwrite ($bf,full_tag("TIMESTART",4,false,$event->timestart));
                fwrite ($bf,full_tag("TIMEDURATION",4,false,$event->timeduration));
                fwrite ($bf,full_tag("VISIBLE",4,false,$event->visible));
                fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$event->timemodified));
                //End event tag
                fwrite ($bf,end_tag("EVENT",3,true));
            }
            //End events tag
            $status = fwrite ($bf,end_tag("EVENTS",2,true));
        }
        return $status;
    }

    //Backup groups info
    function backup_groups_info($bf,$preferences) {

        global $CFG;

        $status = true;
        $status2 = true;

        //Get groups
        $groups = get_records("groups","courseid",$preferences->backup_course);

        //Pring groups header
        if ($groups) {
            //Pring groups header
            fwrite ($bf,start_tag("GROUPS",2,true));
            //Iterate
            foreach ($groups as $group) {
                //Begin group tag
                fwrite ($bf,start_tag("GROUP",3,true));
                //Output group contents
                fwrite ($bf,full_tag("ID",4,false,$group->id));
                //fwrite ($bf,full_tag("COURSEID",4,false,$group->courseid));
                fwrite ($bf,full_tag("NAME",4,false,$group->name));
                fwrite ($bf,full_tag("DESCRIPTION",4,false,$group->description));
                fwrite ($bf,full_tag("ENROLMENTKEY",4,false,$group->enrolmentkey));
                fwrite ($bf,full_tag("PICTURE",4,false,$group->picture));
                fwrite ($bf,full_tag("HIDEPICTURE",4,false,$group->hidepicture));
                fwrite ($bf,full_tag("TIMECREATED",4,false,$group->timecreated));
                fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$group->timemodified));

                //Now, backup groups_members, only if users are included
                if ($preferences->backup_users != 2) {
                    $status2 = backup_groups_members_info($bf,$preferences,$group->id);
                }

                //End group tag
                fwrite ($bf,end_tag("GROUP",3,true));
            }
            //End groups tag
            $status = fwrite ($bf,end_tag("GROUPS",2,true));

            //Now save group_files
            if ($status && $status2) {
                $status2 = backup_copy_group_files($preferences);
            }
        }
        return ($status && $status2);
    }

    //Backup groups_members info
    function backup_groups_members_info($bf,$preferences,$groupid) {

        global $CFG;

        $status = true;

        //Get groups_members that are being included in backup
        $groups_members = get_records_sql("SELECT gm.*
                                           FROM {$CFG->prefix}groups_members gm,
                                                {$CFG->prefix}backup_ids bi
                                           WHERE gm.groupid = $groupid
                                             AND bi.backup_code = $preferences->backup_unique_code
                                             AND bi.table_name = 'user'
                                             AND bi.old_id = gm.userid");

        //Pring groups_members header
        if ($groups_members) {
            //Pring groups_members header
            fwrite ($bf,start_tag("MEMBERS",4,true));
            //Iterate
            foreach ($groups_members as $group_member) {
                //Begin group_member tag
                fwrite ($bf,start_tag("MEMBER",5,true));
                //Output group_member contents
                fwrite ($bf,full_tag("GROUPID",6,false,$group_member->groupid));
                fwrite ($bf,full_tag("USERID",6,false,$group_member->userid));
                fwrite ($bf,full_tag("TIMEADDED",6,false,$group_member->timeadded));
                //End group_member tag
                fwrite ($bf,end_tag("MEMBER",5,true));
            }
            //End groups_members tag
            $status = fwrite ($bf,end_tag("MEMBERS",4,true));
        }
        return $status;
    }

    //Backup groupings info
    function backup_groupings_info($bf,$preferences) {

        global $CFG;

        $status = true;

        //Get groups
        $groupings = get_records("groupings","courseid",$preferences->backup_course);

        //Pring groups header
        if ($groupings) {
            //Pring groups header
            fwrite ($bf,start_tag("GROUPINGS",2,true));
            //Iterate
            foreach ($groupings as $grouping) {
                //Begin group tag
                fwrite ($bf,start_tag("GROUPING",3,true));
                //Output group contents
                fwrite ($bf,full_tag("ID",4,false,$grouping->id));
                //fwrite ($bf,full_tag("COURSEID",4,false,$grouping->courseid));
                fwrite ($bf,full_tag("NAME",4,false,$grouping->name));
                fwrite ($bf,full_tag("DESCRIPTION",4,false,$grouping->description));
                fwrite ($bf,full_tag("CONFIGDATA",4,false,$grouping->configdata));
                fwrite ($bf,full_tag("TIMECREATED",4,false,$grouping->timecreated));
                fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$grouping->timemodified));

                //End group tag
                fwrite ($bf,end_tag("GROUPING",3,true));
            }
            //End groups tag
            $status = fwrite ($bf,end_tag("GROUPINGS",2,true));

            //(Now save grouping_files)
        }
        return $status;
    }

    //Backup groupings-groups info
    function backup_groupings_groups_info($bf,$preferences) {

        global $CFG;

        $status = true;

        //Get grouping_groups
        $courseid = $preferences->backup_course;
        $sql = "SELECT gg.* FROM {$CFG->prefix}groupings g, {$CFG->prefix}groupings_groups gg
                 WHERE g.courseid=$courseid AND g.id=gg.groupingid";
        $grouping_groups = get_records_sql($sql);

        //Pring grouping_groups header
        if ($grouping_groups) {
            //Pring grouping_groups header
            fwrite ($bf,start_tag("GROUPINGSGROUPS",2,true));
            //Iterate
            foreach ($grouping_groups as $members) {
                //Begin grouping_group tag
                fwrite ($bf,start_tag("GROUPINGGROUP",3,true));
                //Output group_member contents
                fwrite ($bf,full_tag("ID",4,false,$members->id));
                fwrite ($bf,full_tag("GROUPINGID",4,false,$members->groupingid));
                fwrite ($bf,full_tag("GROUPID",4,false,$members->groupid));
                fwrite ($bf,full_tag("TIMEADDED",4,false,$members->timeadded));
                //End grouping_group tag
                fwrite ($bf,end_tag("GROUPINGGROUP",3,true));
            }
            //End grouping_groups tag
            $status = fwrite ($bf,end_tag("GROUPINGSGROUPS",2,true));
        }
        return $status;
    }

    //Start the modules tag
    function backup_modules_start ($bf,$preferences) {

        return fwrite ($bf,start_tag("MODULES",2,true));
    }

    //End the modules tag
    function backup_modules_end ($bf,$preferences) {

        return fwrite ($bf,end_tag("MODULES",2,true));
    }

    //This function makes all the necesary calls to every mod
    //to export itself and its files !!!
    function backup_module($bf,$preferences,$module) {

        global $CFG;

        $status = true;
        $statusm = true;

        require_once($CFG->dirroot.'/mod/'.$module.'/backuplib.php');

        if (isset($preferences->mods[$module]->instances)
            && is_array($preferences->mods[$module]->instances)) {
            $onemodbackup = $module.'_backup_one_mod';
            if (function_exists($onemodbackup)) {
                foreach ($preferences->mods[$module]->instances as $instance => $object) {
                    if (!empty($object->backup)) {
                        $statusm = $onemodbackup($bf,$preferences,$instance);
                        if (!$statusm) {
                            if (!defined('BACKUP_SILENTLY')) {
                                notify('backup of '.$module.'-'.$object->name.' failed.');
                            }
                            $status = false;
                        }
                    }
                }
            }  else {
                $status = false;
            }
        } else { // whole module.
            //First, re-check if necessary functions exists
            $modbackup = $module."_backup_mods";
            if (function_exists($modbackup)) {
                //Call the function
                $status = $modbackup($bf,$preferences);
            } else {
                //Something was wrong. Function should exist.
                $status = false;
            }
        }

        return $status;

    }

    //This function encode things to make backup multi-site fully functional
    //It does this conversions:
    // - $CFG->wwwroot/file.php/courseid ------------------> $@FILEPHP@$ (slasharguments links)
    // - $CFG->wwwroot/file.php?file=/courseid ------------> $@FILEPHP@$ (non-slasharguments links)
    // - Every module/block/course_format xxxx_encode_content_links() is executed too
    //
    function backup_encode_absolute_links($content) {

        global $CFG,$preferences;

    /// MDL-14072: Prevent NULLs, empties and numbers to be processed by the
    /// heavy interlinking. Just a few cpu cycles saved.
        if ($content === NULL) {
            return NULL;
        } else if ($content === '') {
            return '';
        } else if (is_numeric($content)) {
            return $content;
        }

        //Use one static variable to cache all the require_once calls that,
        //under PHP5 seems to increase load too much, and we are requiring
        //them here thousands of times (one per content). MDL-8700.
        //Once fixed by PHP, we'll delete this hack

        static $includedfiles;
        if (!isset($includedfiles)) {
            $includedfiles = array();
        }

        //Check if we support unicode modifiers in regular expressions. Cache it.
        static $unicoderegexp;
        if (!isset($unicoderegexp)) {
            $unicoderegexp = @preg_match('/\pL/u', 'a'); // This will fail silenty, returning false,
        }                                                // if regexp libraries don't support unicode

        //Check if preferences is ok. If it isn't set, we are
        //in a scheduled_backup to we are able to get a copy
        //from CFG->backup_preferences
        if (!isset($preferences)) {
            $mypreferences = $CFG->backup_preferences;
        } else {
            //We are in manual backups so global preferences must exist!!
            $mypreferences = $preferences;
        }
        //First, we check for every call to file.php inside the course
        $search = array($CFG->wwwroot.'/file.php/'.$mypreferences->backup_course,
                        $CFG->wwwroot.'/file.php?file=/'.$mypreferences->backup_course,
                        $CFG->wwwroot.'/file.php?file=%2f'.$mypreferences->backup_course,
                        $CFG->wwwroot.'/file.php?file=%2F'.$mypreferences->backup_course);

        $replace = array('$@FILEPHP@$', '$@FILEPHP@$', '$@FILEPHP@$', '$@FILEPHP@$');

        $result = str_replace($search,$replace,$content);

        // Now we look for any '$@FILEPHP@$' URLs, replacing:
        //     - slashes and %2F by $@SLASH@$
        //     - &forcedownload=1 &amp;forcedownload=1 and ?forcedownload=1 by $@FORCEDOWNLOAD@$
        // This way, backup contents will be neutral and independent of slasharguments configuration. MDL-18799
        // Based in $unicoderegexp, decide the regular expression to use
        if ($unicoderegexp) { //We can use unicode modifiers
            $search = '/(\$@FILEPHP@\$)((?:(?:\/|%2f|%2F))(?:(?:\([-;:@#&=\pL0-9\$~_.+!*\',]*?\))|[-;:@#&=\pL0-9\$~_.+!*\',]|%[a-fA-F0-9]{2}|\/)*)?(\?(?:(?:(?:\([-;:@#&=\pL0-9\$~_.+!*\',]*?\))|[-;:@#&=?\pL0-9\$~_.+!*\',]|%[a-fA-F0-9]{2}|\/)*))?(?<![,.;])/u';
        } else { //We cannot ue unicode modifiers
            $search = '/(\$@FILEPHP@\$)((?:(?:\/|%2f|%2F))(?:(?:\([-;:@#&=a-zA-Z0-9\$~_.+!*\',]*?\))|[-;:@#&=a-zA-Z0-9\$~_.+!*\',]|%[a-fA-F0-9]{2}|\/)*)?(\?(?:(?:(?:\([-;:@#&=a-zA-Z0-9\$~_.+!*\',]*?\))|[-;:@#&=?a-zA-Z0-9\$~_.+!*\',]|%[a-fA-F0-9]{2}|\/)*))?(?<![,.;])/';
        }
        $result = preg_replace_callback($search, 'backup_process_filephp_uses', $result);

        foreach ($mypreferences->mods as $name => $info) {
        /// We only include the corresponding backuplib.php if it hasn't been included before
        /// This will save some load under PHP5. MDL-8700.
        /// Once fixed by PHP, we'll delete this hack
            if (!in_array($name, $includedfiles)) {
                include_once("$CFG->dirroot/mod/$name/backuplib.php");
                $includedfiles[] = $name;
            }
            //Check if the xxxx_encode_content_links exists
            $function_name = $name."_encode_content_links";
            if (function_exists($function_name)) {
                $result = $function_name($result,$mypreferences);
            }
        }

        // For the current course format call its encode_content_links method (if it exists)
        static $format_function_name;
        if (!isset($format_function_name)) {
            $format_function_name = false;
            if ($format = get_field('course', 'format', 'id', $mypreferences->backup_course)) {
                if (file_exists("$CFG->dirroot/course/format/$format/backuplib.php")) {
                    include_once("$CFG->dirroot/course/format/$format/backuplib.php");
                    $function_name = $format.'_encode_format_content_links';
                    if (function_exists($function_name)) {
                        $format_function_name = $function_name;
                    }
                }
            }
        }
        // If the above worked - then we have a function to call
        if ($format_function_name) {
            $result = $format_function_name($result, $mypreferences);
        }

        // For each block, call its encode_content_links method.
        // This encodes forexample links to blocks/something/viewphp?id=666
        // that are stored in other activities.
        static $blockobjects = null;
        if (!isset($blockobjects)) {
            $blockobjects = array();
            if ($blocks = get_records('block', 'visible', 1)) {
                foreach ($blocks as $block) {
                    if ($blockobject = block_instance($block->name)) {
                        $blockobjects[] = $blockobject;
                    }
                }
            }
        }

        foreach ($blockobjects as $blockobject) {
            $result = $blockobject->encode_content_links($result,$mypreferences);
        }

        // Finally encode some well-know links to course
        $result = backup_course_encode_links($result, $mypreferences);

        if ($result != $content) {
            debugging('<br /><hr />'.s($content).'<br />changed to<br />'.s($result).'<hr /><br />');
        }

        return $result;
    }

    /**
     * Encode some well-know links to course. Restore will recode them to new course id
     */
    function backup_course_encode_links($content, $preferences) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        // Link to the course main page (it also covers "&topic=xx" and "&week=xx"
        // because they don't become transformed (section number) in backup/restore
        $search = '/(' . $base . '\/course\/view.php\?id\=)([0-9]+)/';
        $result = preg_replace($search,'$@COURSEVIEWBYID*$2@$', $content);

        return $result;
    }

    /**
     * Callback preg_replace function used by backup_encode_absolute_links()
     * to process $@FILEPHP@$ URLs to get slasharguments independent URLs
     */
    function backup_process_filephp_uses($matches) {

        // Replace slashes (plain and encoded) and forcedownload=1 parameter
        $search = array('/', '%2f', '%2F', '?forcedownload=1', '&forcedownload=1', '&amp;forcedownload=1');
        $replace = array('$@SLASH@$', '$@SLASH@$', '$@SLASH@$', '$@FORCEDOWNLOAD@$', '$@FORCEDOWNLOAD@$', '$@FORCEDOWNLOAD@$');

        $result = $matches[1] . (isset($matches[2]) ? str_replace($search, $replace, $matches[2]) : '') . (isset($matches[3]) ? str_replace($search, $replace, $matches[3]) : '');

        return $result;
    }

    //This function copies all the needed files under the "user" directory to the "user_files"
    //directory under temp/backup
    function backup_copy_user_files ($preferences) {

        global $CFG;

        $status = true;

        //First we check that "user_files" exists and create it if necessary
        //in temp/backup/$backup_code  dir
        $status = check_and_create_user_files_dir($preferences->backup_unique_code);
        //now get a list of users that we need for this backup.
        $backup_users = get_recordset_select("backup_ids",
            "backup_code='$preferences->backup_unique_code' AND table_name='user'", "", "id, old_id");
        while ($user = rs_fetch_next_record($backup_users)) {
            //If this user's directory exists, copy it
            $userdir = make_user_directory($user->old_id, true);
            if (check_dir_exists($userdir)) {
                //first remove dirroot so we can split out the folders.
                $parts = explode('/', str_replace($CFG->dataroot.'/user/', '', $userdir));
                $status = true;
                if (is_array($parts)) {
                    $group = $parts[0];
                    $userid = $parts[1];

                    // Create group dir first
                    $status = check_dir_exists("$CFG->dataroot/temp/backup/$preferences->backup_unique_code/user_files/". $group, true);
                }

                $status = $status && backup_copy_file($userdir, 
                    "$CFG->dataroot/temp/backup/$preferences->backup_unique_code/user_files/$group/$user->old_id");
            }
            //Do some output
            backup_flush(30);
        }
        rs_close($backup_users);

        return $status;
    }

    //This function copies all the needed files under the "groups" directory to the "group_files"
    //directory under temp/backup
    function backup_copy_group_files ($preferences) {

        global $CFG;

        $status = true;

        //First we check if "group_files" exists and create it as necessary
        //in temp/backup/$backup_code  dir
        $status = check_and_create_group_files_dir($preferences->backup_unique_code);

        //Now iterate over directories under "groups" to check if that user must be
        //copied to backup

        $rootdir = $CFG->dataroot.'/groups';
        //Check if directory exists
        if (is_dir($rootdir)) {
            $list = list_directories ($rootdir);
            if ($list) {
                //Iterate
                foreach ($list as $dir) {
                    //Look for dir like group in groups table
                    $data = get_record ('groups', 'courseid', $preferences->backup_course,
                                                  'id',$dir);
                    //If exists, copy it
                    if ($data) {
                        $status = backup_copy_file($rootdir."/".$dir,
                                       $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/group_files/".$dir);
                    }
                }
            }
        }
        return $status;
    }

    //This function copies all the course files under the course directory (except the moddata
    //directory to the "course_files" directory under temp/backup
    function backup_copy_course_files ($preferences) {

        global $CFG;

        $status = true;

        //First we check to "course_files" exists and create it as necessary
        //in temp/backup/$backup_code  dir
        $status = check_and_create_course_files_dir($preferences->backup_unique_code);

        //Now iterate over files and directories except $CFG->moddata and backupdata to be
        //copied to backup

        $rootdir = $CFG->dataroot."/".$preferences->backup_course;

        $name_moddata = $CFG->moddata;
        $name_backupdata = "backupdata";
        //Check if directory exists
        if (is_dir($rootdir)) {
            $list = list_directories_and_files ($rootdir);
            if ($list) {
                //Iterate
                foreach ($list as $dir) {
                    if ($dir !== $name_moddata and $dir !== $name_backupdata) {
                        $status = backup_copy_file($rootdir."/".$dir,
                                       $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/course_files/".$dir);
                    }
                }
            }
        }
        return $status;
    }
    /*
     * This function copies all the site files under the site directory (except the moddata and backupdata
     * directories to the "site_files" directory under temp/backup
     */
    function backup_copy_site_files ($preferences) {

        global $CFG;

        $status = true;

        if ($preferences->backup_course == SITEID){
            return $status;
        }

        //First we check to "site_files" exists and create it as necessary
        //in temp/backup/$backup_code  dir
        $status = $status && check_and_create_site_files_dir($preferences->backup_unique_code);

        $rootdir = $CFG->dataroot."/".SITEID;



        $files = get_records_select('backup_files',
                        "backup_code = '$preferences->backup_unique_code' AND file_type = 'site'");
        if ($files) {
            //Iterate
            foreach ($files as $fileobj) {
                //check for dir structure and create recursively
                $file = $fileobj->path;
                $status = $status && check_dir_exists(dirname($CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/site_files/".$file), true, true);
                $status = $status && backup_copy_file($rootdir."/".$file,
                                   $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/site_files/".$file);
            }
        }
        return $status;
    }
    //This function creates the zip file containing all the backup info
    //moodle.xml, moddata, user_files, course_files.
    //The zipped file is created in the backup directory and named with
    //the "oficial" name of the backup
    //It uses "pclzip" if available or system "zip" (unix only)
    function backup_zip ($preferences) {

        global $CFG;

        $status = true;

        //Base dir where everything happens
        $basedir = cleardoubleslashes($CFG->dataroot."/temp/backup/".$preferences->backup_unique_code);
        //Backup zip file name
        $name = $preferences->backup_name;
        //List of files and directories
        $filelist = list_directories_and_files ($basedir);

        //Convert them to full paths
        $files = array();
        foreach ($filelist as $file) {
           $files[] = "$basedir/$file";
        }

        $status = zip_files($files, "$basedir/$name");

        //echo "<br/>Status: ".$status;                                     //Debug
        return $status;

    }

    //This function copies the final zip to the course dir
    function copy_zip_to_course_dir ($preferences) {

        global $CFG;

        $status = true;

        //Define zip location (from)
        $from_zip_file = $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/".$preferences->backup_name;

        //Initialise $to_zip_file
        $to_zip_file="";

        //If $preferences->backup_destination isn't empty, then copy to custom directory
        if (!empty($preferences->backup_destination)) {
            $to_zip_file = $preferences->backup_destination."/".$preferences->backup_name;
        } else {
            //Define zip destination (course dir)
            $to_zip_file = $CFG->dataroot."/".$preferences->backup_course;

            //echo "<p>From: ".$from_zip_file."<br />";                                              //Debug

            //echo "<p>Checking: ".$to_zip_file."<br />";                                          //Debug

            //Checks course dir exists
            $status = check_dir_exists($to_zip_file,true);

            //Define zip destination (backup dir)
            $to_zip_file = $to_zip_file."/backupdata";

            //echo "<p>Checking: ".$to_zip_file."<br />";                                          //Debug

            //Checks backup dir exists
            $status = check_dir_exists($to_zip_file,true);

            //Define zip destination (zip file)
            $to_zip_file = $to_zip_file."/".$preferences->backup_name;
        }

        //echo "<p>To: ".$to_zip_file."<br />";                                              //Debug

        //Copy zip file
        if ($status) {
            $status = backup_copy_file ($from_zip_file,$to_zip_file);
        }

        return $status;
    }

    /**
     * compatibility function
     * with new granular backup
     * we need to know
     */
    function backup_userdata_selected($preferences,$modname,$modid) {
        return ((empty($preferences->mods[$modname]->instances)
                 && !empty($preferences->mods[$modname]->userinfo))
                || (is_array($preferences->mods[$modname]->instances)
                    && array_key_exists($modid,$preferences->mods[$modname]->instances)
                    && !empty($preferences->mods[$modname]->instances[$modid]->userinfo)));
    }


    function backup_mod_selected($preferences,$modname,$modid) {
        return ((empty($preferences->mods[$modname]->instances)
                 && !empty($preferences->mods[$modname]->backup))
                || (is_array($preferences->mods[$modname]->instances)
                    && array_key_exists($modid,$preferences->mods[$modname]->instances)
                    && !empty($preferences->mods[$modname]->instances[$modid]->backup)));
    }

    /*
     * Checks for the required files/functions to backup every mod
     * And check if there is data about it
     */
    function backup_fetch_prefs_from_request(&$preferences,&$count,$course) {
        global $CFG,$SESSION;

        // check to see if it's in the session already
        if (!empty($SESSION->backupprefs)  && array_key_exists($course->id,$SESSION->backupprefs) && !empty($SESSION->backupprefs[$course->id])) {
            $sprefs = $SESSION->backupprefs[$course->id];
            $preferences = $sprefs;
            // refetch backup_name just in case.
            $bn = optional_param('backup_name','',PARAM_FILE);
            if (!empty($bn)) {
                $preferences->backup_name = $bn;
            }
            $count = 1;
            return true;
        }

        if ($allmods = get_records("modules") ) {
            foreach ($allmods as $mod) {
                $modname = $mod->name;
                $modfile = "$CFG->dirroot/mod/$modname/backuplib.php";
                $modbackup = $modname."_backup_mods";
                $modbackupone = $modname."_backup_one_mod";
                $modcheckbackup = $modname."_check_backup_mods";
                if (!file_exists($modfile)) {
                    continue;
                }
                include_once($modfile);
                if (!function_exists($modbackup) || !function_exists($modcheckbackup)) {
                    continue;
                }
                $var = "exists_".$modname;
                $preferences->$var = true;
                $count++;
                // check that there are instances and we can back them up individually
                if (!count_records('course_modules','course',$course->id,'module',$mod->id) || !function_exists($modbackupone)) {
                    continue;
                }
                $var = 'exists_one_'.$modname;
                $preferences->$var = true;
                $varname = $modname.'_instances';
                $preferences->$varname = get_all_instances_in_course($modname, $course, NULL, true);
                foreach ($preferences->$varname as $instance) {
                    $preferences->mods[$modname]->instances[$instance->id]->name = $instance->name;
                    $var = 'backup_'.$modname.'_instance_'.$instance->id;
                    $$var = optional_param($var,0);
                    $preferences->$var = $$var;
                    $preferences->mods[$modname]->instances[$instance->id]->backup = $$var;
                    $var = 'backup_user_info_'.$modname.'_instance_'.$instance->id;
                    $$var = optional_param($var,0);
                    $preferences->$var = $$var;
                    $preferences->mods[$modname]->instances[$instance->id]->userinfo = $$var;
                    $var = 'backup_'.$modname.'_instances';
                    $preferences->$var = 1; // we need this later to determine what to display in modcheckbackup.
                }

                //Check data
                //Check module info
                $preferences->mods[$modname]->name = $modname;

                $var = "backup_".$modname;
                $$var = optional_param( $var,0);
                $preferences->$var = $$var;
                $preferences->mods[$modname]->backup = $$var;

                //Check include user info
                $var = "backup_user_info_".$modname;
                $$var = optional_param( $var,0);
                $preferences->$var = $$var;
                $preferences->mods[$modname]->userinfo = $$var;

            }
        }

        //Check other parameters
        $preferences->backup_metacourse = optional_param('backup_metacourse',1,PARAM_INT);
        $preferences->backup_users = optional_param('backup_users',1,PARAM_INT);
        $preferences->backup_logs = optional_param('backup_logs',0,PARAM_INT);
        $preferences->backup_user_files = optional_param('backup_user_files',1,PARAM_INT);
        $preferences->backup_course_files = optional_param('backup_course_files',1,PARAM_INT);
        $preferences->backup_gradebook_history = optional_param('backup_gradebook_history', 1, PARAM_INT);
        $preferences->backup_site_files = optional_param('backup_site_files',1,PARAM_INT);
        $preferences->backup_messages = optional_param('backup_messages',1,PARAM_INT);
        $preferences->backup_blogs = optional_param('backup_blogs',1,PARAM_INT);
        $preferences->backup_course = $course->id;
        $preferences->backup_name = required_param('backup_name',PARAM_FILE);
        $preferences->backup_unique_code =  required_param('backup_unique_code');

        $roles = get_records('role', '', '', 'sortorder');
        $preferences->backuproleassignments = array();
        foreach ($roles as $role) {
            if (optional_param('backupassignments_' . $role->shortname, 0, PARAM_INT)) {
                $preferences->backuproleassignments[$role->id] = $role;
            }
        }

        // put it (back) in the session
        $SESSION->backupprefs[$course->id] = $preferences;
    }

    /* Finds all related roles used in course, mod and blocks context
     * @param object $preferences
     * @param object $course
     * @return array of role objects
     */
    function backup_fetch_roles($preferences) {

        global $CFG;
        $contexts = array();
        $roles = array();

        /// adding course context
        $coursecontext = get_context_instance(CONTEXT_COURSE, $preferences->backup_course);
        $contexts[$coursecontext->id] = $coursecontext;

        /// adding mod contexts
        $mods = $preferences->mods;
        foreach ($mods as $modname => $mod) {
            $instances = $mod->instances;
            foreach ($instances as $id => $instance) {
                // if this type of mod is to be backed up
                if ($instance->backup) {
                    $cm = get_coursemodule_from_instance($modname, $id);
                    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
                    // put context in array keys
                    $contexts[$context->id] = $context;
                }
            }
        }

        // add all roles assigned at user context
        if ($preferences->backup_users) {
            if ($users = get_records_sql("SELECT u.old_id, u.table_name,u.info
                                            FROM {$CFG->prefix}backup_ids u
                                            WHERE u.backup_code = '$preferences->backup_unique_code' AND
                                            u.table_name = 'user'")) {
                foreach ($users as $user) {
                    $context = get_context_instance(CONTEXT_USER, $user->old_id);
                    $contexts[$context->id] = $context;
                }
            }

        }

        // add all roles assigned at block context
        if ($courseblocks = get_records_sql("SELECT *
                                             FROM {$CFG->prefix}block_instance
                                             WHERE pagetype = '".PAGE_COURSE_VIEW."'
                                                   AND pageid = {$preferences->backup_course}")) {

            foreach ($courseblocks as $courseblock) {

                $context = get_context_instance(CONTEXT_BLOCK, $courseblock->id);
                $contexts[$context->id] = $context;
            }
        }

        // foreach context, call get_roles_on_exact_context + get_roles_with_override_on_context() and insert into array
        foreach ($contexts as $context) {
            if ($proles = get_roles_on_exact_context($context)) { // Look for roles assignments
                foreach ($proles as $prole) {
                    $roles[$prole->id] = $prole;
                }
            }
            if ($proles = get_roles_with_override_on_context($context)) { // Look for roles overrides
                foreach ($proles as $prole) {
                    $roles[$prole->id] = $prole;
                }
            }
        }

        return $roles;
    }

    /* function to print xml for overrides */
    function write_role_overrides_xml($bf, $context, $startlevel) {
        fwrite ($bf, start_tag("ROLES_OVERRIDES", $startlevel, true));
        if ($roles = get_roles_with_override_on_context($context)) {
            foreach ($roles as $role) {
                fwrite ($bf, start_tag("ROLE", $startlevel+1, true));
                fwrite ($bf, full_tag("ID", $startlevel+2, false, $role->id));
                fwrite ($bf, full_tag("NAME", $startlevel+2, false, $role->name));
                fwrite ($bf, full_tag("SHORTNAME", $startlevel+2, false, $role->shortname));
                fwrite ($bf, start_tag("CAPABILITIES", $startlevel+2, true));
                if ($capabilities = get_capabilities_from_role_on_context($role, $context)) {
                    foreach ($capabilities as $capability) {
                        fwrite ($bf, start_tag("CAPABILITY", $startlevel+3, true));
                        fwrite ($bf, full_tag("NAME", $startlevel+4, false, $capability->capability));
                        fwrite ($bf, full_tag("PERMISSION", $startlevel+4, false, $capability->permission));
                        fwrite ($bf, full_tag("TIMEMODIFIED", $startlevel+4, false, $capability->timemodified));
                        if (!isset($capability->modifierid)) {
                            $capability->modifierid = 0;
                        }
                        fwrite ($bf, full_tag("MODIFIERID", $startlevel+4, false, $capability->modifierid));
                        fwrite ($bf, end_tag("CAPABILITY", $startlevel+3, true));
                    }
                }
                fwrite ($bf, end_tag("CAPABILITIES", $startlevel+2, true));
                fwrite ($bf, end_tag("ROLE", $startlevel+1, true));
            }
        }
        fwrite ($bf, end_tag("ROLES_OVERRIDES", $startlevel, true));
    }

    /* function to print xml for assignment */
    function write_role_assignments_xml($bf, $preferences, $context, $startlevel) {
    /// write role_assign code here
        fwrite ($bf, start_tag("ROLES_ASSIGNMENTS", $startlevel, true));

        if ($roles = get_roles_with_assignment_on_context($context)) {
            foreach ($roles as $role) {
            /// Skip non-selected roles
                if (!isset($preferences->backuproleassignments[$role->id])) {
                    continue;
                }
                fwrite ($bf, start_tag("ROLE", $startlevel+1, true));
                fwrite ($bf, full_tag("ID", $startlevel+2, false, $role->id));
                fwrite ($bf, full_tag("NAME", $startlevel+2, false, $role->name));
                fwrite ($bf, full_tag("SHORTNAME", $startlevel+2, false, $role->shortname));
                fwrite ($bf, start_tag("ASSIGNMENTS", $startlevel+2, true));
                if ($assignments = get_users_from_role_on_context($role, $context)) {
                    foreach ($assignments as $assignment) {
                    /// Role assignments are only sent to backup if the user is one target user
                        if (backup_getid($preferences->backup_unique_code, 'user', $assignment->userid)) {
                            fwrite ($bf, start_tag("ASSIGNMENT", $startlevel+3, true));
                            fwrite ($bf, full_tag("USERID", $startlevel+4, false, $assignment->userid));
                            fwrite ($bf, full_tag("HIDDEN", $startlevel+4, false, $assignment->hidden));
                            fwrite ($bf, full_tag("TIMESTART", $startlevel+4, false, $assignment->timestart));
                            fwrite ($bf, full_tag("TIMEEND", $startlevel+4, false, $assignment->timeend));
                            fwrite ($bf, full_tag("TIMEMODIFIED", $startlevel+4, false, $assignment->timemodified));
                            if (!isset($assignment->modifierid)) {
                                $assignment->modifierid = 0;
                            }
                            fwrite ($bf, full_tag("MODIFIERID", $startlevel+4, false, $assignment->modifierid));
                            fwrite ($bf, full_tag("ENROL", $startlevel+4, false, $assignment->enrol));
                            fwrite ($bf, full_tag("SORTORDER", $startlevel+4, false, $assignment->sortorder));
                            fwrite ($bf, end_tag("ASSIGNMENT", $startlevel+3, true));
                        }
                    }
                }
                fwrite ($bf, end_tag("ASSIGNMENTS", $startlevel+2, true));
                fwrite ($bf, end_tag("ROLE", $startlevel+1, true));
            }
        }
        fwrite ($bf, end_tag("ROLES_ASSIGNMENTS", $startlevel, true));
    }


    function backup_execute(&$preferences, &$errorstr) {
        global $CFG;
        $status = true;

        //Check for temp and backup and backup_unique_code directory
        //Create them as needed
        if (!defined('BACKUP_SILENTLY')) {
            echo "<li>".get_string("creatingtemporarystructures").'</li>';
        }

        $status = check_and_create_backup_dir($preferences->backup_unique_code);
        //Empty dir
        if ($status) {
            $status = clear_backup_dir($preferences->backup_unique_code);
        }

        //Delete old_entries from backup tables
        if (!defined('BACKUP_SILENTLY')) {
            echo "<li>".get_string("deletingolddata").'</li>';
        }
        $status = backup_delete_old_data();
        if (!$status) {
            if (!defined('BACKUP_SILENTLY')) {
                notify ("An error occurred deleting old backup data");
            }
            else {
                $errorstr = "An error occurred deleting old backup data";
                return false;
            }
        }

        //Create the moodle.xml file
        if ($status) {
            if (!defined('BACKUP_SILENTLY')) {
                echo "<li>".get_string("creatingxmlfile");
                //Begin a new list to xml contents
                echo "<ul>";
                echo "<li>".get_string("writingheader").'</li>';
            }
            //Obtain the xml file (create and open) and print prolog information
            $backup_file = backup_open_xml($preferences->backup_unique_code);
            if (!defined('BACKUP_SILENTLY')) {
                echo "<li>".get_string("writinggeneralinfo").'</li>';
            }
            //Prints general info about backup to file
            if ($backup_file) {
                if (!$status = backup_general_info($backup_file,$preferences)) {
                    if (!defined('BACKUP_SILENTLY')) {
                        notify("An error occurred while backing up general info");
                    }
                    else {
                        $errorstr = "An error occurred while backing up general info";
                        return false;
                    }
                }
            }
            if (!defined('BACKUP_SILENTLY')) {
                echo "<li>".get_string("writingcoursedata");
                //Start new ul (for course)
                echo "<ul>";
                echo "<li>".get_string("courseinfo").'</li>';
            }
            //Prints course start (tag and general info)
            if ($status) {
                if (!$status = backup_course_start($backup_file,$preferences)) {
                    if (!defined('BACKUP_SILENTLY')) {
                        notify("An error occurred while backing up course start");
                    }
                    else {
                        $errorstr = "An error occurred while backing up course start";
                        return false;
                    }
                }
            }
            //Metacourse information
            if ($status && $preferences->backup_metacourse) {
                if (!defined('BACKUP_SILENTLY')) {
                    echo "<li>".get_string("metacourse").'</li>';
                }
                if (!$status = backup_course_metacourse($backup_file,$preferences)) {
                    if (!defined('BACKUP_SILENTLY')) {
                        notify("An error occurred while backing up metacourse info");
                    }
                    else {
                        $errorstr = "An error occurred while backing up metacourse info";
                        return false;
                    }
                }
            }
            if (!defined('BACKUP_SILENTLY')) {
                echo "<li>".get_string("blocks").'</li>';
            }
            //Blocks information
            if ($status) {
                if (!$status = backup_course_blocks($backup_file,$preferences)) {
                    if (!defined('BACKUP_SILENTLY')) {
                        notify("An error occurred while backing up course blocks");
                    }
                    else {
                        $errorstr = "An error occurred while backing up course blocks";
                        return false;
                    }
                }
            }
            if (!defined('BACKUP_SILENTLY')) {
                echo "<li>".get_string("sections").'</li>';
            }
            //Section info
            if ($status) {
                if (!$status = backup_course_sections($backup_file,$preferences)) {
                    if (!defined('BACKUP_SILENTLY')) {
                        notify("An error occurred while backing up course sections");
                    }
                    else {
                        $errorstr = "An error occurred while backing up course sections";
                        return false;
                    }
                }
            }

            //End course contents (close ul)
            if (!defined('BACKUP_SILENTLY')) {
                echo "</ul></li>";
            }

            //User info
            if ($status) {
                if (!defined('BACKUP_SILENTLY')) {
                    echo "<li>".get_string("writinguserinfo").'</li>';
                }
                if (!$status = backup_user_info($backup_file,$preferences)) {
                    if (!defined('BACKUP_SILENTLY')) {
                        notify("An error occurred while backing up user info");
                    }
                    else {
                        $errorstr = "An error occurred while backing up user info";
                        return false;
                    }
                }
            }

            //If we have selected to backup messages and we are
            //doing a SITE backup, let's do it
            if ($status && $preferences->backup_messages && $preferences->backup_course == SITEID) {
                if (!defined('BACKUP_SILENTLY')) {
                    echo "<li>".get_string("writingmessagesinfo").'</li>';
                }
                if (!$status = backup_messages($backup_file,$preferences)) {
                    if (!defined('BACKUP_SILENTLY')) {
                        notify("An error occurred while backing up messages");
                    }
                    else {
                        $errorstr = "An error occurred while backing up messages";
                        return false;
                    }
                }
            }

            //If we have selected to backup blogs and we are
            //doing a SITE backup, let's do it
            if ($status && $preferences->backup_blogs && $preferences->backup_course == SITEID) {
                if (!defined('BACKUP_SILENTLY')) {
                    echo "<li>".get_string("writingblogsinfo").'</li>';
                }
                if (!$status = backup_blogs($backup_file,$preferences)) {
                    if (!defined('BACKUP_SILENTLY')) {
                        notify("An error occurred while backing up blogs");
                    }
                    else {
                        $errorstr = "An error occurred while backing up blogs";
                        return false;
                    }
                }
            }

            //If we have selected to backup quizzes or other modules that use questions
            //we've already added ids of categories and questions to backup to backup_ids table
            if ($status) {
                if (!defined('BACKUP_SILENTLY')) {
                    echo "<li>".get_string("writingcategoriesandquestions").'</li>';
                }
                require_once($CFG->dirroot.'/question/backuplib.php');
                if (!$status = backup_question_categories($backup_file, $preferences)) {
                    if (!defined('BACKUP_SILENTLY')) {
                        notify("An error occurred while backing up quiz categories");
                    }
                    else {
                        $errorstr = "An error occurred while backing up quiz categories";
                        return false;
                    }
                }
            }

            //Print logs if selected
            if ($status) {
                if ($preferences->backup_logs) {
                    if (!defined('BACKUP_SILENTLY')) {
                        echo "<li>".get_string("writingloginfo").'</li>';
                    }
                    if (!$status = backup_log_info($backup_file,$preferences)) {
                        if (!defined('BACKUP_SILENTLY')) {
                            notify("An error occurred while backing up log info");
                        }
                        else {
                            $errorstr = "An error occurred while backing up log info";
                            return false;
                        }
                    }
                }
            }

            //Print scales info
            if ($status) {
                if (!defined('BACKUP_SILENTLY')) {
                    echo "<li>".get_string("writingscalesinfo").'</li>';
                }
                if (!$status = backup_scales_info($backup_file,$preferences)) {
                    if (!defined('BACKUP_SILENTLY')) {
                        notify("An error occurred while backing up scales");
                    }
                    else {
                        $errorstr = "An error occurred while backing up scales";
                        return false;
                    }
                }
            }

            //Print groups info
            if ($status) {
                if (!defined('BACKUP_SILENTLY')) {
                    echo "<li>".get_string("writinggroupsinfo").'</li>';
                }
                if (!$status = backup_groups_info($backup_file,$preferences)) {
                    if (!defined('BACKUP_SILENTLY')) {
                        notify("An error occurred while backing up groups");
                    }
                    else {
                        $errostr = "An error occurred while backing up groups";
                        return false;
                    }
                }
            }

            //Print groupings info
            if ($status) {
                if (!defined('BACKUP_SILENTLY')) {
                    echo "<li>".get_string("writinggroupingsinfo").'</li>';
                }
                if (!$status = backup_groupings_info($backup_file,$preferences)) {
                    if (!defined('BACKUP_SILENTLY')) {
                        notify("An error occurred while backing up groupings");
                    }
                    else {
                        $errorstr = "An error occurred while backing up groupings";
                        return false;
                    }
                }
            }

            //Print groupings_groups info
            if ($status) {
                if (!defined('BACKUP_SILENTLY')) {
                    echo "<li>".get_string("writinggroupingsgroupsinfo").'</li>';
                }
                if (!$status = backup_groupings_groups_info($backup_file,$preferences)) {
                    if (!defined('BACKUP_SILENTLY')) {
                        notify("An error occurred while backing up groupings groups");
                    }
                    else {
                        $errorstr = "An error occurred while backing up groupings groups";
                        return false;
                    }
                }
            }

            //Print events info
            if ($status) {
                if (!defined('BACKUP_SILENTLY')) {
                    echo "<li>".get_string("writingeventsinfo").'</li>';
                }
                if (!$status = backup_events_info($backup_file,$preferences)) {
                    if (!defined('BACKUP_SILENTLY')) {
                        notify("An error occurred while backing up events");
                    }
                    else {
                        $errorstr = "An error occurred while backing up events";
                        return false;
                    }
                }
            }

            //Print gradebook info
            if ($status) {
                if (!defined('BACKUP_SILENTLY')) {
                    echo "<li>".get_string("writinggradebookinfo").'</li>';
                }
                if (!$status = backup_gradebook_info($backup_file,$preferences)) {
                    if (!defined('BACKUP_SILENTLY')) {
                        notify("An error occurred while backing up gradebook");
                    }
                    else {
                        $errorstr = "An error occurred while backing up gradebook";
                        return false;
                    }
                }
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
                    if (!defined('BACKUP_SILENTLY')) {
                        echo "<li>".get_string("writingmoduleinfo");
                    }
                    //Start modules tag
                    if (!$status = backup_modules_start ($backup_file,$preferences)) {
                        if (!defined('BACKUP_SILENTLY')) {
                            notify("An error occurred while backing up module info");
                        }
                        else {
                            $errorstr = "An error occurred while backing up module info";
                            return false;
                        }
                    }
                    //Open ul for module list
                    if (!defined('BACKUP_SILENTLY')) {
                        echo "<ul>";
                    }
                    //Iterate over modules and call backup
                    foreach ($preferences->mods as $module) {
                        if ($module->backup and $status) {
                            if (!defined('BACKUP_SILENTLY')) {
                                echo "<li>".get_string("modulenameplural",$module->name).'</li>';
                            }
                            if (!$status = backup_module($backup_file,$preferences,$module->name)) {
                                if (!defined('BACKUP_SILENTLY')) {
                                    notify("An error occurred while backing up '$module->name'");
                                }
                                else {
                                    $errorstr = "An error occurred while backing up '$module->name'";
                                    return false;
                                }
                            }
                        }
                    }
                    //Close ul for module list
                    if (!defined('BACKUP_SILENTLY')) {
                        echo "</ul></li>";
                    }
                    //Close modules tag
                    if (!$status = backup_modules_end ($backup_file,$preferences)) {
                        if (!defined('BACKUP_SILENTLY')) {
                            notify("An error occurred while finishing the module backups");
                        }
                        else {
                            $errorstr = "An error occurred while finishing the module backups";
                            return false;
                        }
                    }
                }
            }

            //Backup course format data, if any.
            if (!defined('BACKUP_SILENTLY')) {
                echo '<li>'.get_string("courseformatdata").'</li>';
            }
            if($status) {
                if (!$status = backup_format_data($backup_file,$preferences)) {
                    if (!defined('BACKUP_SILENTLY')) {
                        notify("An error occurred while backing up the course format data");
                    }
                    else {
                        $errorstr = "An error occurred while backing up the course format data";
                        return false;
                    }
                }
            }

            //Prints course end
            if ($status) {
                if (!$status = backup_course_end($backup_file,$preferences)) {
                    if (!defined('BACKUP_SILENTLY')) {
                        notify("An error occurred while closing the course backup");
                    }
                    else {
                        $errorstr = "An error occurred while closing the course backup";
                        return false;
                    }
                }
            }
            //Close the xml file and xml data
            if ($backup_file) {
                backup_close_xml($backup_file);
            }

            //End xml contents (close ul)
            if (!defined('BACKUP_SILENTLY')) {
                echo "</ul></li>";
            }
        }

        //Now, if selected, copy user files
        if ($status) {
            if ($preferences->backup_user_files) {
                if (!defined('BACKUP_SILENTLY')) {
                    echo "<li>".get_string("copyinguserfiles").'</li>';
                }
                if (!$status = backup_copy_user_files ($preferences)) {
                    if (!defined('BACKUP_SILENTLY')) {
                        notify("An error occurred while copying user files");
                    }
                    else {
                        $errorstr = "An error occurred while copying user files";
                        return false;
                    }
                }
            }
        }

        //Now, if selected, copy course files
        if ($status) {
            if ($preferences->backup_course_files) {
                if (!defined('BACKUP_SILENTLY')) {
                    echo "<li>".get_string("copyingcoursefiles").'</li>';
                }
                if (!$status = backup_copy_course_files ($preferences)) {
                    if (!defined('BACKUP_SILENTLY')) {
                        notify("An error occurred while copying course files");
                    }
                    else {
                        $errorstr = "An error occurred while copying course files";
                        return false;
                    }
                }
            }
        }
        //Now, if selected, copy site files
        if ($status) {
            if ($preferences->backup_site_files) {
                if (!defined('BACKUP_SILENTLY')) {
                    echo "<li>".get_string("copyingsitefiles").'</li>';
                }
                if (!$status = backup_copy_site_files ($preferences)) {
                    if (!defined('BACKUP_SILENTLY')) {
                        notify("An error occurred while copying site files");
                    }
                    else {
                        $errorstr = "An error occurred while copying site files";
                        return false;
                    }
                }
            }
        }
        //Now, zip all the backup directory contents
        if ($status) {
            if (!defined('BACKUP_SILENTLY')) {
                echo "<li>".get_string("zippingbackup").'</li>';
            }
            if (!$status = backup_zip ($preferences)) {
                if (!defined('BACKUP_SILENTLY')) {
                    notify("An error occurred while zipping the backup");
                }
                else {
                    $errorstr = "An error occurred while zipping the backup";
                    return false;
                }
            }
        }

        //Now, copy the zip file to course directory
        if ($status) {
            if (!defined('BACKUP_SILENTLY')) {
                echo "<li>".get_string("copyingzipfile").'</li>';
            }
            if (!$status = copy_zip_to_course_dir ($preferences)) {
                if (!defined('BACKUP_SILENTLY')) {
                    notify("An error occurred while copying the zip file to the course directory");
                }
                else {
                    $errorstr = "An error occurred while copying the zip file to the course directory";
                    return false;
                }
            }
        }

        //Now, clean temporary data (db and filesystem)
        if ($status) {
            if (!defined('BACKUP_SILENTLY')) {
                echo "<li>".get_string("cleaningtempdata").'</li>';
            }
            if (!$status = clean_temp_data ($preferences)) {
                if (!defined('BACKUP_SILENTLY')) {
                    notify("An error occurred while cleaning up temporary data");
                }
                else {
                    $errorstr = "An error occurred while cleaning up temporary data";
                    return false;
                }
            }
        }

        return $status;
    }

    /**
     * This function calculates the "backup" part of the file name
     * from lang files. Used both in manual and scheduled backups
     *
     * @param object $course course object
     * @return string "backup" part of the filename
     */
    function backup_get_backup_string($course) {

    /// Calculate the backup word
    /// Take off some characters in the filename !!
        $takeoff = array(" ", ":", "/", "\\", "|");
        $backup_word = str_replace($takeoff,"_",moodle_strtolower(get_string("backupfilename")));
    /// If non-translated, use "backup"
        if (substr($backup_word,0,1) == "[") {
            $backup_word= "backup";
        }

        return $backup_word;
    }

    /**
    * This function generates the default zipfile name for a backup
    * based on the course shortname
    *
    * @param object $course course object
    * @return string filename (excluding path information)
    */
    function backup_get_zipfile_name($course) {

        //Calculate the backup word
        $backup_word = backup_get_backup_string($course);

        //Calculate the date format string
        $backup_date_format = str_replace(" ","_",get_string("backupnameformat"));
        //If non-translated, use "%Y%m%d-%H%M"
        if (substr($backup_date_format,0,1) == "[") {
            $backup_date_format = "%%Y%%m%%d-%%H%%M";
        }

        //Calculate the shortname
        $backup_shortname = clean_filename($course->shortname);
        if (empty($backup_shortname) or $backup_shortname == '_' ) {
            $backup_shortname = $course->id;
        }

        //Calculate the final backup filename
        //The backup word
        $backup_name = $backup_word."-";
        //The shortname
        $backup_name .= moodle_strtolower($backup_shortname)."-";
        //The date format
        $backup_name .= userdate(time(),$backup_date_format,99,false);
        //The extension
        $backup_name .= ".zip";
        //And finally, clean everything
        $backup_name = clean_filename($backup_name);

        return $backup_name;
    }

    /**
    * This function generates the common file substring for a course
    * used to keep n copies by the scheduled backup
    *
    * @param object $course course object
    * @return string common part of filename in backups of this course
    */
    function backup_get_keep_name($course) {

        //Calculate the backup word
        $backup_word = backup_get_backup_string($course);

        //Calculate the shortname
        $backup_shortname = clean_filename($course->shortname);
        if (empty($backup_shortname) or $backup_shortname == '_' ) {
            $backup_shortname = $course->id;
        }

        $keep_name = $backup_word . "-" . moodle_strtolower($backup_shortname)."-";
        $keep_name = clean_filename($keep_name);

        return $keep_name;
    }
    /**
    * This function adds on the standard items to the preferences
    * Like moodle version and backup version
    *
    * @param object $preferences existing preferences object.
    * (passed by reference)
    */
    function backup_add_static_preferences(&$preferences) {
        global $CFG;
        $preferences->moodle_version = $CFG->version;
        $preferences->moodle_release = $CFG->release;
        $preferences->backup_version = $CFG->backup_version;
        $preferences->backup_release = $CFG->backup_release;
    }

?>
