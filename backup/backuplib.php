<?php //$Id$
    //This file contains all the function needed in the backup utility
    //except the mod-related funtions that are into every backuplib.php inside
    //every mod directory
 
    //Calculate the number of users to backup and put their ids in backup_ids
    //Return an array of info (name,value)
    function user_check_backup($course,$backup_unique_code,$backup_users,$backup_messages) {
        //$backup_users=0-->all
        //              1-->course (needed + enrolled)
        //              2-->none

        global $CFG;
        global $db;

        $context = get_context_instance(CONTEXT_COURSE, $course);
        $count_users = 0;

        //If we've selected none, simply return 0
        if ($backup_users == 0 or $backup_users == 1) {
        
            //Calculate needed users (calling every xxxx_get_participants function + scales users)
            $needed_users = backup_get_needed_users($course, $backup_messages);

            //Calculate enrolled users (students + teachers)
            $enrolled_users = backup_get_enrolled_users($course);

            //Calculate all users (every record in users table)
            $all_users = backup_get_all_users();

            //Calculate course users (needed + enrolled)
            //First, needed
            $course_users = $needed_users;
        
            //Now, enrolled
            if ($enrolled_users) {
                foreach ($enrolled_users as $enrolled_user) {
                    $course_users[$enrolled_user->id]->id = $enrolled_user->id; 
                }
            }
       
            //Now, depending of parameters, create $backupable_users
            if ($backup_users == 0) {
                $backupable_users = $all_users;
            } else {
                $backupable_users = $course_users;
            }

            //If we have backupable users
            if ($backupable_users) {
                //Iterate over users putting their roles
                foreach ($backupable_users as $backupable_user) {
                    $backupable_user->info = "";
                                     
                    //Is needed user (exists in needed_users) 
                    if (isset($needed_users[$backupable_user->id])) {
                        $backupable_user->info .= "needed";
                    } else if (isset($course_users[$backupable_user->id])) {
                        $backupable_user->info .= "needed"; 
                    }   // Yu: also needed because they can view course
                        // might need another variable
                                        
                    //Now create the backup_id record
                    $backupids_rec->backup_code = $backup_unique_code;
                    $backupids_rec->table_name = "user";
                    $backupids_rec->old_id = $backupable_user->id;
                    $backupids_rec->info = $backupable_user->info;
        
                    //Insert the record id. backup_users decide it.
                    //When all users
                    $status = insert_record('backup_ids', $backupids_rec, false);
                    $count_users++;
                }
                //Do some output     
                backup_flush(30);
            }
        }

        //Prepare Info
        //Gets the user data
        $info[0][0] = get_string("users");
        $info[0][1] = $count_users;

        return $info;
    }

    //Returns every needed user (participant) in a course
    //It uses the xxxx_get_participants() function
    //plus users needed to backup scales.
    //WARNING: It returns only NEEDED users, not every 
    //   every student and teacher in the course, so it
    //must be merged with backup_get_enrrolled_users !!

    function backup_get_needed_users ($courseid, $includemessages=false) {
        
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

        //Now, add scale users (from site and course scales)
        //Get users
        $scaleusers = get_records_sql("SELECT DISTINCT userid,userid
                                       FROM {$CFG->prefix}scale
                                       WHERE courseid = '0' or courseid = '$courseid'");
        //Add scale users to results
        if ($scaleusers) {
            foreach ($scaleusers as $scaleuser) {
                //If userid != 0
                if ($scaleuser->userid != 0) {
                    $result[$scaleuser->userid]->id = $scaleuser->userid;
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
    
        return $result;

    }

    //Returns every enrolled user (student and teacher) in a course

    function backup_get_enrolled_users ($courseid) {

        global $CFG;
              
        // get all users with moodle/course:view capability, this will include people
        // assigned at cat level, or site level
        // but it should be ok if they have no direct assignment at course, mod, block level
        return get_users_by_capability(get_context_instance(CONTEXT_COURSE, $courseid), 'moodle/course:view');
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
    //and put them (their path) in backup_ids
    //Return an array of info (name,value)
    function user_files_check_backup($course,$backup_unique_code) {

        global $CFG;

        $rootdir = $CFG->dataroot."/users";
        //Check if directory exists
        if (is_dir($rootdir)) {
            //Get directories without descend
            $userdirs = get_directory_list($rootdir,"",false,true,false);
            foreach ($userdirs as $dir) {
                //Extracts user id from file path
                $tok = strtok($dir,"/");
                if ($tok) {
                    $userid = $tok;
                } else {
                    //We were getting $dir='0', so continue (WAS: $tok = "";)
                    continue;
                }
                //Look it is a backupable user
                $data = get_record ("backup_ids","backup_code","$backup_unique_code",
                                                 "table_name","user",
                                                 "old_id",$userid);
                if ($data) {
                    //Insert them into backup_files
                    $status = execute_sql("INSERT INTO {$CFG->prefix}backup_files
                                               (backup_code, file_type, path, old_id)
                                           VALUES
                                               ('$backup_unique_code','user','".addslashes($dir)."','$userid')",false);
                }
                //Do some output
                backup_flush(30);
            }
        }

        //Now execute the select
        $ids = get_records_sql("SELECT DISTINCT b.path, b.old_id
                                FROM {$CFG->prefix}backup_files b
                                WHERE backup_code = '$backup_unique_code' AND
                                      file_type = 'user'");
        //Gets the user data
        $info[0][0] = get_string("files");
        if ($ids) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }

        return $info;  
    }

    //Calculate the number of course files to backup
    //under $CFG->dataroot/$course, except $CFG->moddata, and backupdata
    //and put them (their path) in backup_ids
    //Return an array of info (name,value)
    function course_files_check_backup($course,$backup_unique_code) {

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
        $info[0][0] = get_string("files");
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
        $content = backup_encode_absolute_links($content);

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
        //The messages in backup
        if ($preferences->backup_messages == 1 && $preferences->backup_course == SITEID) {
            fwrite ($bf,full_tag("MESSAGES",3,false,"true"));
        } else {
            fwrite ($bf,full_tag("MESSAGES",3,false,"false"));
        }
        //The mode of writing the block data
        fwrite ($bf,full_tag('BLOCKFORMAT',3,false,'instances'));
        fwrite ($bf,end_tag("DETAILS",2,true));
        
        $status = fwrite ($bf,end_tag("INFO",1,true)); 
        
        ///Roles stuff goes in here
        
        fwrite ($bf, start_tag('ROLES', 1, true));
        $roles = backup_fetch_roles($preferences);
        
        $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
        
        foreach ($roles as $role) {
            fwrite ($bf,start_tag('ROLE',2,true));                    
            fwrite ($bf,full_tag('ID', 3, false, $role->id));
            fwrite ($bf,full_tag('NAME',3,false,$role->name));
            fwrite ($bf,full_tag('SHORTNAME',3,false,$role->shortname));
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
            fwrite ($bf,full_tag("ENROLPERIOD",3,false,$course->enrolperiod));
            fwrite ($bf,full_tag("NUMSECTIONS",3,false,$course->numsections));
            //fwrite ($bf,full_tag("SHOWRECENT",3,false,$course->showrecent));    INFO: This is out in 1.3
            fwrite ($bf,full_tag("MAXBYTES",3,false,$course->maxbytes));
            fwrite ($bf,full_tag("SHOWREPORTS",3,false,$course->showreports));
            fwrite ($bf,full_tag("GROUPMODE",3,false,$course->groupmode));
            fwrite ($bf,full_tag("GROUPMODEFORCE",3,false,$course->groupmodeforce));
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
       
            /// write local course overrides here?
            write_role_overrides_xml($bf, $context, 3);
            /// write role_assign code here
            write_role_assignments_xml($bf, $context, 3);
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

        //Get info from messages
        $unreads = get_records ('message');
        $reads   = get_records ('message_read');
        $contacts= get_records ('message_contacts');

        if ($unreads || $reads || $contacts) {
            $counter = 0;
            //message open tag
            fwrite ($bf,start_tag("MESSAGES",2,true));
            if ($unreads) {
                //Iterate over every unread    
                foreach ($unreads as $unread) {
                    //start message
                    fwrite($bf, start_tag("MESSAGE",3,true));
                    fwrite ($bf,full_tag("ID",4,false,$unread->id));
                    fwrite ($bf,full_tag("STATUS",4,false,"UNREAD"));
                    fwrite ($bf,full_tag("USERIDFROM",4,false,$unread->useridfrom));
                    fwrite ($bf,full_tag("USERIDTO",4,false,$unread->useridto));
                    fwrite ($bf,full_tag("MESSAGE",4,false,$unread->message));
                    fwrite ($bf,full_tag("FORMAT",4,false,$unread->format));
                    fwrite ($bf,full_tag("TIMECREATED",4,false,$unread->timecreated));
                    fwrite ($bf,full_tag("MESSAGETYPE",4,false,$unread->messagetype));
                    //end message
                    fwrite ($bf,end_tag("MESSAGE",3,true));

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

            if ($reads) {
                //Iterate over every read    
                foreach ($reads as $read) {
                    //start message
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
                    //end message
                    fwrite ($bf,end_tag("MESSAGE",3,true));

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

            if ($contacts) {
                fwrite($bf, start_tag("CONTACTS",3,true));
                //Iterate over every contact    
                foreach ($contacts as $contact) {
                    //start contact
                    fwrite($bf, start_tag("CONTACT",4,true));
                    fwrite ($bf,full_tag("ID",5,false,$contact->id));
                    fwrite ($bf,full_tag("USERID",5,false,$contact->userid));
                    fwrite ($bf,full_tag("CONTACTID",5,false,$contact->contactid));
                    fwrite ($bf,full_tag("BLOCKED",5,false,$contact->blocked));
                    //end contact
                    fwrite ($bf,end_tag("CONTACT",4,true));

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
                fwrite($bf, end_tag("CONTACTS",3,true));
            }
            //messages close tag
            $status = fwrite ($bf,end_tag("MESSAGES",2,true));
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

        // Let's see if we have to backup blocks from modules
        $modulerecords = get_records_sql('SELECT name, id FROM '.$CFG->prefix.'modules');
        
        foreach($preferences->mods as $module) {
            if(!$module->backup) {
                continue;
            }

            $cmods = get_records_select('course_modules', 'course = '.$preferences->backup_course.' AND module = '.$modulerecords[$module->name]->id);
            if(empty($cmods)) {
                continue;
            }

            $pagetypes = page_import_types('mod/'.$module->name.'/');
            if(empty($pagetypes)) {
                continue;
            }

            foreach($pagetypes as $pagetype) {
                foreach($cmods as $cmod) {
                    $pages[] = page_create_object($pagetype, $cmod->instance);
                }
            }
        }

        //Blocks open tag
        fwrite ($bf,start_tag('BLOCKS',2,true));

        while($page = array_pop($pages)) {
            if ($instances = blocks_get_by_page($page)) {
                //Iterate over every block
                foreach ($instances as $position) {
                    foreach ($position as $instance) {
                      
                        //If we somehow have a block with an invalid id, skip it
                        if(empty($blocks[$instance->blockid]->name)) {
                            continue;
                        }
                        //Begin Block
                        
                        fwrite ($bf,start_tag('BLOCK',3,true));
                        fwrite ($bf,full_tag('ID', 4, false,$instance->id));
                        fwrite ($bf,full_tag('NAME',4,false,$blocks[$instance->blockid]->name));
                        fwrite ($bf,full_tag('PAGEID',4,false,$instance->pageid));
                        fwrite ($bf,full_tag('PAGETYPE',4,false,$instance->pagetype));
                        fwrite ($bf,full_tag('POSITION',4,false,$instance->position));
                        fwrite ($bf,full_tag('WEIGHT',4,false,$instance->weight));
                        fwrite ($bf,full_tag('VISIBLE',4,false,$instance->visible));
                        fwrite ($bf,full_tag('CONFIGDATA',4,false,$instance->configdata));
                                             
                        $context = get_context_instance(CONTEXT_BLOCK, $instance->id);
                        write_role_overrides_xml($bf, $context, 4);
                        /// write role_assign code here
                        write_role_assignments_xml($bf, $context, 4);
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
               //Gets course_module data from db
               $course_module = get_records ("course_modules","id",$tok);
               //If it's the first, pring MODS tag
               if ($first_record) {
                   fwrite ($bf,start_tag("MODS",4,true));
                   $first_record = false;
               }
               // if we're doing selected instances, check that too.
               if (is_array($preferences->mods[$moduletype]->instances) 
                   && count($preferences->mods[$moduletype]->instances)
                   && (!array_key_exists($course_module[$tok]->instance,$preferences->mods[$moduletype]->instances)
                       || empty($preferences->mods[$moduletype]->instances[$course_module[$tok]->instance]->backup))) {
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
               fwrite ($bf,full_tag("INSTANCE",6,false,$course_module[$tok]->instance));
               fwrite ($bf,full_tag("ADDED",6,false,$course_module[$tok]->added));
               fwrite ($bf,full_tag("SCORE",6,false,$course_module[$tok]->score));
               fwrite ($bf,full_tag("INDENT",6,false,$course_module[$tok]->indent));
               fwrite ($bf,full_tag("VISIBLE",6,false,$course_module[$tok]->visible));
               fwrite ($bf,full_tag("GROUPMODE",6,false,$course_module[$tok]->groupmode));
               // get all the role_capabilities overrides in this mod
               write_role_overrides_xml($bf, $context, 6);
                /// write role_assign code here
               write_role_assignments_xml($bf, $context, 6);         
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

        $status = true;

        // Use a recordset to for the memory handling on to
        // the DB and run faster
        $users = get_recordset_sql("SELECT b.old_id, b.table_name, b.info,
                                           u.*, m.wwwroot
                                    FROM   {$CFG->prefix}backup_ids b
                                      JOIN {$CFG->prefix}user       u ON b.old_id=u.id
                                      JOIN {$CFG->prefix}mnet_host  m ON u.mnethostid=m.id
                                    WHERE b.backup_code = '$preferences->backup_unique_code' AND
                                          b.table_name = 'user'");

        //If we have users to backup
        if ($users && $users->RecordCount()) {
            //Begin Users tag
            fwrite ($bf,start_tag("USERS",2,true));
            $counter = 0;
            //With every user
            while ($user = $users->FetchNextObj()) {
                //Begin User tag
                fwrite ($bf,start_tag("USER",3,true));
                //Output all user data
                fwrite ($bf,full_tag("ID",4,false,$user->id));
                fwrite ($bf,full_tag("AUTH",4,false,$user->auth));
                fwrite ($bf,full_tag("CONFIRMED",4,false,$user->confirmed));
                fwrite ($bf,full_tag("POLICYAGREED",4,false,$user->policyagreed));
                fwrite ($bf,full_tag("DELETED",4,false,$user->deleted));
                fwrite ($bf,full_tag("USERNAME",4,false,$user->username));
                fwrite ($bf,full_tag("PASSWORD",4,false,$user->password));
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
                fwrite ($bf,full_tag("SECRET",4,false,$user->secret));
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
                if ($user->mnethostid != $CFG->mnet_localhost_id) {
                    fwrite ($bf,full_tag("MNETHOSTURL",4,false,$user->wwwroot));
                }
                fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$user->timemodified));

                /// write assign/override code for context_userid

                $user->isneeded = strpos($user->info,"needed");
                //Output every user role (with its associated info) 
                /*
                $user->isadmin = strpos($user->info,"admin");
                $user->iscoursecreator = strpos($user->info,"coursecreator");
                $user->isteacher = strpos($user->info,"teacher");
                $user->isstudent = strpos($user->info,"student");
                
                
                if ($user->isadmin!==false or 
                    $user->iscoursecreator!==false or 
                    $user->isteacher!==false or 
                    $user->isstudent!==false or
                    $user->isneeded!==false) {
                */
                fwrite ($bf,start_tag("ROLES",4,true));
                if ($user->info != "needed" && $user->info!="") {
                    //Begin ROLES tag
                    
                    //PRINT ROLE INFO
                    //Admins
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
                    /*
                    if ($user->isadmin!==false) {
                        //Print ROLE start
                        fwrite ($bf,start_tag("ROLE",5,true));
                        //Print Role info
                        fwrite ($bf,full_tag("TYPE",6,false,"admin"));
                        //Print ROLE end
                        fwrite ($bf,end_tag("ROLE",5,true));
                    }
                    //CourseCreator
                    if ($user->iscoursecreator!==false) {
                        //Print ROLE start 
                        fwrite ($bf,start_tag("ROLE",5,true)); 
                        //Print Role info 
                        fwrite ($bf,full_tag("TYPE",6,false,"coursecreator"));
                        //Print ROLE end
                        fwrite ($bf,end_tag("ROLE",5,true));   
                    }
                    //Teacher
                    if ($user->isteacher!==false) {
                        //Print ROLE start 
                        fwrite ($bf,start_tag("ROLE",5,true)); 
                        //Print Role info 
                        fwrite ($bf,full_tag("TYPE",6,false,"teacher"));
                        //Get specific info for teachers
                        $tea = get_record("user_teachers","userid",$user->old_id,"course",$preferences->backup_course);
                        fwrite ($bf,full_tag("AUTHORITY",6,false,$tea->authority));
                        fwrite ($bf,full_tag("TEA_ROLE",6,false,$tea->role));
                        fwrite ($bf,full_tag("EDITALL",6,false,$tea->editall));
                        fwrite ($bf,full_tag("TIMESTART",6,false,$tea->timestart));
                        fwrite ($bf,full_tag("TIMEEND",6,false,$tea->timeend));
                        fwrite ($bf,full_tag("TIMEMODIFIED",6,false,$tea->timemodified));
                        fwrite ($bf,full_tag("TIMEACCESS",6,false,$tea->timeaccess));
                        fwrite ($bf,full_tag("ENROL",6,false,$tea->enrol));
                        //Print ROLE end
                        fwrite ($bf,end_tag("ROLE",5,true));   
                    }
                    //Student
                    if ($user->isstudent!==false) {
                        //Print ROLE start 
                        fwrite ($bf,start_tag("ROLE",5,true)); 
                        //Print Role info 
                        fwrite ($bf,full_tag("TYPE",6,false,"student"));
                        //Get specific info for students
                        $stu = get_record("user_students","userid",$user->old_id,"course",$preferences->backup_course);
                        fwrite ($bf,full_tag("TIMESTART",6,false,$stu->timestart));
                        fwrite ($bf,full_tag("TIMEEND",6,false,$stu->timeend));
                        fwrite ($bf,full_tag("TIME",6,false,$stu->time));
                        fwrite ($bf,full_tag("TIMEACCESS",6,false,$stu->timeaccess));
                        fwrite ($bf,full_tag("ENROL",6,false,$stu->enrol));
                        //Print ROLE end
                        fwrite ($bf,end_tag("ROLE",5,true));   
                    }*/
                    
                    
                    
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
                write_role_assignments_xml($bf, $context, 4);
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
    function backup_gradebook_info($bf,$preferences) {

        global $CFG;

        $status = true;

        //Gradebook header
        fwrite ($bf,start_tag("GRADEBOOK",2,true));

        //Output grade_preferences
        $grade_preferences = get_records("grade_preferences", "courseid", $preferences->backup_course);
        if ($grade_preferences) {
            //Begin grade_preferences tag
            fwrite ($bf,start_tag("GRADE_PREFERENCES",3,true)); 
            //Iterate for each preference
            foreach ($grade_preferences as $grade_preference) {
                //Begin grade_preference
                fwrite ($bf,start_tag("GRADE_PREFERENCE",4,true)); 
                //Output individual fields
                fwrite ($bf,full_tag("ID",5,false,$grade_preference->id));
                fwrite ($bf,full_tag("PREFERENCE",5,false,$grade_preference->preference));
                fwrite ($bf,full_tag("VALUE",5,false,$grade_preference->value));
                //End grade_preference
                fwrite ($bf,end_tag("GRADE_PREFERENCE",4,true)); 
            }
            //End grade_preferences tag
            $status = fwrite ($bf,end_tag("GRADE_PREFERENCES",3,true));
        }

        //Output grade_letter
        $grade_letters = get_records("grade_letter", "courseid", $preferences->backup_course);
        if ($grade_letters) {
            //Begin grade_letters tag
            fwrite ($bf,start_tag("GRADE_LETTERS",3,true)); 
            //Iterate for each letter
            foreach ($grade_letters as $grade_letter) {
                //Begin grade_letter
                fwrite ($bf,start_tag("GRADE_LETTER",4,true)); 
                //Output individual fields
                fwrite ($bf,full_tag("ID",5,false,$grade_letter->id));
                fwrite ($bf,full_tag("LETTER",5,false,$grade_letter->letter));
                fwrite ($bf,full_tag("GRADE_HIGH",5,false,$grade_letter->grade_high));
                fwrite ($bf,full_tag("GRADE_LOW",5,false,$grade_letter->grade_low));
                //End grade_letter
                fwrite ($bf,end_tag("GRADE_LETTER",4,true)); 
            }
            //End grade_letters tag
            $status = fwrite ($bf,end_tag("GRADE_LETTERS",3,true));
        }

        //Output grade_category
        $grade_categories = get_records("grade_category", "courseid", $preferences->backup_course);
        if ($grade_categories) {
            //Begin grade_categories tag
            fwrite ($bf,start_tag("GRADE_CATEGORIES",3,true)); 
            //Iterate for each category
            foreach ($grade_categories as $grade_category) {
                //Begin grade_category
                fwrite ($bf,start_tag("GRADE_CATEGORY",4,true)); 
                //Output individual fields
                fwrite ($bf,full_tag("ID",5,false,$grade_category->id));
                fwrite ($bf,full_tag("NAME",5,false,$grade_category->name));
                fwrite ($bf,full_tag("DROP_X_LOWEST",5,false,$grade_category->drop_x_lowest));
                fwrite ($bf,full_tag("BONUS_POINTS",5,false,$grade_category->bonus_points));
                fwrite ($bf,full_tag("HIDDEN",5,false,$grade_category->hidden));
                fwrite ($bf,full_tag("WEIGHT",5,false,$grade_category->weight));

                //Now backup grade_item (inside grade_category)
                $status = backup_gradebook_item_info($bf,$preferences,$grade_category->id);

                //End grade_category
                fwrite ($bf,end_tag("GRADE_CATEGORY",4,true)); 
            }
            //End grade_categories tag
            $status = fwrite ($bf,end_tag("GRADE_CATEGORIES",3,true));
        }
        //Gradebook footer
        $status = fwrite ($bf,end_tag("GRADEBOOK",2,true));
        return $status;
    }

    //Backup gradebook_item (called from backup_gradebook_info
    function backup_gradebook_item_info($bf,$preferences,$gradecategoryid) {

        global $CFG;

        $status = true;

        //Output grade_item (only for modules included in backup)
        $grade_items = get_records_sql("SELECT *
                                    FROM {$CFG->prefix}grade_item
                                    WHERE courseid = $preferences->backup_course AND
                                          category = $gradecategoryid");
        //Filter items about modules not included in backup
        $inc_grade_items = array();
        if ($grade_items) {
            foreach ($grade_items as $grade_item) {
                //Get modulename
                $rec_module = get_record("modules", "id", $grade_item->modid);
                //If it exists and it's included in backup
                if ($rec_module && $preferences->mods[$rec_module->name]->backup == 1) {
                    //Set the name and add it
                    $grade_item->module_name = $rec_module->name;
                    $inc_grade_items[] = $grade_item;
                } else {
                    debugging("skipping $grade_item->modid"."-"."$rec_module->name<br />");
                }
            }
        }
        if ($inc_grade_items) {
            //Begin grade_items tag
            fwrite ($bf,start_tag("GRADE_ITEMS",5,true)); 
            //Iterate for each item
            foreach ($inc_grade_items as $grade_item) {
                //Begin grade_item
                fwrite ($bf,start_tag("GRADE_ITEM",6,true)); 
                //Output individual fields
                fwrite ($bf,full_tag("MODULE_NAME",7,false,$grade_item->module_name));
                fwrite ($bf,full_tag("CMINSTANCE",7,false,$grade_item->cminstance));
                fwrite ($bf,full_tag("SCALE_GRADE",7,false,$grade_item->scale_grade));
                fwrite ($bf,full_tag("EXTRA_CREDIT",7,false,$grade_item->extra_credit));
                fwrite ($bf,full_tag("SORT_ORDER",7,false,$grade_item->sort_order));
 
                //Now backup grade_exceptions (inside grade_item)
                $status = backup_gradebook_exceptions_info($bf,$preferences,$grade_item->id);

                //End grade_item
                fwrite ($bf,end_tag("GRADE_ITEM",6,true)); 
            }
            //End grade_items tag
            $status = fwrite ($bf,end_tag("GRADE_ITEMS",5,true));
        }

        return $status;
    }

    //Backup gradebook_exceptions (called from backup_gradebook_item_info
    function backup_gradebook_exceptions_info($bf,$preferences,$gradeitemid) {

        global $CFG;

        $status = true;

        //Output grade_exceptions (only for users included in backup)
        $grade_exceptions = get_records_sql("SELECT *
                                             FROM {$CFG->prefix}grade_exceptions
                                             WHERE courseid = $preferences->backup_course AND
                                                   grade_itemid = $gradeitemid");
        //Filter exceptions about users not included in backup
        $inc_grade_exceptions = array();
        if ($grade_exceptions) {
            foreach ($grade_exceptions as $grade_exception) {
                //Check if user has been included in backup
                $rec_user = get_record ("backup_ids","backup_code",$preferences->backup_unique_code,
                                        "table_name","user",
                                        "old_id",$grade_exception->userid);
                //If it's included in backup
                if ($rec_user) {
                    //Add it
                    $inc_grade_exceptions[] = $grade_exception;
                } else {
                    debugging("skipping $grade_exception->userid"."-user<br />");
                }
            }
        }
        if ($inc_grade_exceptions) {
            //Begin grade_exceptions tag
            fwrite ($bf,start_tag("GRADE_EXCEPTIONS",7,true));
            //Iterate for each exception
            foreach ($inc_grade_exceptions as $grade_exception) {
                //Begin grade_exception
                fwrite ($bf,start_tag("GRADE_EXCEPTION",8,true));
                //Output individual fields
                fwrite ($bf,full_tag("USERID",9,false,$grade_exception->userid));
                //End grade_exception
                fwrite ($bf,end_tag("GRADE_EXCEPTION",8,true));
            }
            //End grade_exceptions tag
            $status = fwrite ($bf,end_tag("GRADE_EXCEPTIONS",7,true));
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
                                   WHERE courseid = '0' or courseid = $preferences->backup_course");

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
        $groups = get_groups($preferences->backup_course); //TODO:check.

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
                ///fwrite ($bf,full_tag("COURSEID",4,false,$group->courseid));
                fwrite ($bf,full_tag("NAME",4,false,$group->name));
                fwrite ($bf,full_tag("DESCRIPTION",4,false,$group->description));
                fwrite ($bf,full_tag("ENROLMENTKEY",4,false,$group->enrolmentkey)); //TODO:
                fwrite ($bf,full_tag("LANG",4,false,$group->lang));
                fwrite ($bf,full_tag("THEME",4,false,$group->theme));
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

        //Get groups_members
        $groups_members = groups_get_member_records($groupid);
        
        //Pring groups_members header
        if ($groups_members) {
            //Pring groups_members header
            fwrite ($bf,start_tag("MEMBERS",4,true));
            //Iterate
            foreach ($groups_members as $group_member) {
                //Begin group_member tag
                fwrite ($bf,start_tag("MEMBER",5,true));
                //Output group_member contents
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
        $status2 = true;

        //Get groups 
        $groupings = groups_get_grouping_records($preferences->backup_course);

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
                fwrite ($bf,full_tag("NAME",4,false,$grouping->name));
                fwrite ($bf,full_tag("DESCRIPTION",4,false,$grouping->description));
                fwrite ($bf,full_tag("TIMECREATED",4,false,$grouping->timecreated));

                $status2 = backup_groupids_info($bf,$preferences,$grouping->id);

                //End group tag
                fwrite ($bf,end_tag("GROUPING",3,true));
            }
            //End groups tag
            $status = fwrite ($bf,end_tag("GROUPINGS",2,true));

            //(Now save grouping_files)
        }
        return ($status && $status2);
    }

    //Backup groupings-groups info
    function backup_groupids_info($bf,$preferences,$groupingid) {
  
        global $CFG;
        
        $status = true;

        //Get groups_members
        $grouping_groups = groups_get_groups_in_grouping_records($groupingid) ;
        
        //Pring groups_members header
        if ($grouping_groups) {
            //Pring groups_members header
            fwrite ($bf,start_tag("GROUPS",4,true));
            //Iterate
            foreach ($grouping_groups as $group2) {
                //Begin group tag
                fwrite ($bf,start_tag("GROUP",5,true));
                //Output group_member contents
                fwrite ($bf,full_tag("GROUPID",6,false,$group2->groupid));
                fwrite ($bf,full_tag("TIMEADDED",6,false,$group2->timeadded)); //TODO:
                //End group tag
                fwrite ($bf,end_tag("GROUP",5,true));
            }
            //End groups_members tag
            $status = fwrite ($bf,end_tag("GROUPS",4,true));
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

        require_once($CFG->dirroot.'/mod/'.$module.'/backuplib.php');

        if (isset($preferences->mods[$module]->instances)
            && is_array($preferences->mods[$module]->instances)) {
            $onemodbackup = $module.'_backup_one_mod';
            if (function_exists($onemodbackup)) {
                foreach ($preferences->mods[$module]->instances as $instance => $object) {
                    if (!empty($object->backup)) {
                        $status = $onemodbackup($bf,$preferences,$instance);
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
    // - Every module xxxx_encode_content_links() is executed too
    //
    function backup_encode_absolute_links($content) {

        global $CFG,$preferences;

        //Use one static variable to cache all the require_once calls that,
        //under PHP5 seems to increase load too much, and we are requiring
        //them here thousands of times (one per content). MDL-8700. 
        //Once fixed by PHP, we'll delete this hack

        static $includedfiles;
        if (!isset($includedfiles)) {
            $includedfiles = array();
        }

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
                        $CFG->wwwroot.'/file.php?file=/'.$mypreferences->backup_course);

        $replace = array('$@FILEPHP@$','$@FILEPHP@$');

        $result = str_replace($search,$replace,$content);

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

        if ($result != $content) {
            debugging('<br /><hr />'.s($content).'<br />changed to<br />'.s($result).'<hr /><br />');
        }

        return $result;
    }

    //This function copies all the needed files under the "users" directory to the "user_files"
    //directory under temp/backup
    function backup_copy_user_files ($preferences) {

        global $CFG;

        $status = true;

        //First we check to "user_files" exists and create it as necessary
        //in temp/backup/$backup_code  dir
        $status = check_and_create_user_files_dir($preferences->backup_unique_code);
 
        //Now iterate over directories under "users" to check if that user must be 
        //copied to backup
        
        $rootdir = $CFG->dataroot."/users";
        //Check if directory exists
        if (is_dir($rootdir)) {
            $list = list_directories ($rootdir);
            if ($list) {
                //Iterate
                foreach ($list as $dir) {
                    //Look for dir like username in backup_ids
                    $data = get_record ("backup_ids","backup_code",$preferences->backup_unique_code,
                                                     "table_name","user", 
                                                     "old_id",$dir);
                    //If exists, copy it
                    if ($data) {
                        $status = backup_copy_file($rootdir."/".$dir,
                                       $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/user_files/".$dir);
                    }
                }
            }
        }

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
                    $data = groups_group_belongs_to_course($dir, $preferences->backup_course);
                    //TODO:check. get_record ('groups', 'courseid', $preferences->backup_course,'id',$dir);
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
                $preferences->$varname = get_all_instances_in_course($modname,$course);
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
        $preferences->backup_messages = optional_param('backup_messages',1,PARAM_INT);
        $preferences->backup_course = $course->id;
        $preferences->backup_name = required_param('backup_name',PARAM_FILE);
        $preferences->backup_unique_code =  required_param('backup_unique_code');

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
        
        // foreach context, call get_roles_on_exact_context insert into array
        foreach ($contexts as $context) {
            if ($proles = get_roles_on_exact_context($context)) {
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
    function write_role_assignments_xml($bf, $context, $startlevel) {
     /// write role_assign code here
        fwrite ($bf, start_tag("ROLES_ASSIGNMENTS", $startlevel, true));
        
        if ($roles = get_roles_with_assignment_on_context($context)) {
            foreach ($roles as $role) {          
                fwrite ($bf, start_tag("ROLE", $startlevel+1, true));
                fwrite ($bf, full_tag("ID", $startlevel+2, false, $role->id));
                fwrite ($bf, full_tag("NAME", $startlevel+2, false, $role->name));
                fwrite ($bf, full_tag("SHORTNAME", $startlevel+2, false, $role->shortname)); 
                fwrite ($bf, start_tag("ASSIGNMENTS", $startlevel+2, true));
                if ($assignments = get_users_from_role_on_context($role, $context)) {
                    foreach ($assignments as $assignment) {
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
                fwrite ($bf, end_tag("ASSIGNMENTS", $startlevel+2, true));
                fwrite ($bf, end_tag("ROLE", $startlevel+1, true));   
            }  
        }   
        fwrite ($bf, end_tag("ROLES_ASSIGNMENTS", $startlevel, true));     
    }
?>
