<?PHP //$Id$
    //This file contains all the function needed in the backup/restore utility
    //except the mod-related funtions that are into every backuplib.php inside
    //every mod directory

    //Insert necessary category ids to backup_ids table
    function insert_category_ids ($course,$backup_unique_code) {
        global $CFG;
        $status = true;
        $status = execute_sql("INSERT INTO {$CFG->prefix}backup_ids
                                   (backup_code, table_name, old_id)
                               SELECT DISTINCT '$backup_unique_code','quiz_categories',t.category
                               FROM {$CFG->prefix}quiz_questions t,
                                    {$CFG->prefix}quiz_question_grades g,
                                    {$CFG->prefix}quiz q
                               WHERE q.course = '$course' AND
                                     g.quiz = q.id AND
                                     g.question = t.id",false);
        return $status;
    }
    
    //Delete category ids from backup_ids table
    function delete_category_ids ($backup_unique_code) {
        global $CFG;
        $status = true;
        $status = execute_sql("DELETE FROM {$CFG->prefix}backup_ids
                               WHERE backup_code = '$backup_unique_code'",false);
        return $status;
    }
 
    //Calculate the number of users to backup and put their ids in backup_ids
    //Return an array of info (name,value)
    function user_check_backup($course,$backup_unique_code,$backup_users) {
        //$backup_users=0-->all
        //              1-->course
        //              2-->needed-->NOT IMPLEMEMTED

        global $CFG;
        global $db;

        $count_users = 0;
        
        //Select all users from user
        $users = get_records ("user");
        //Iterate over users putting their roles
        foreach ($users as $user) {
               $user->info = "";
            //Is Admin in tables (not is_admin()) !!
            if (record_exists("user_admins","userid",$user->id)) {
                $user->info .= "admin";
                $user->role_admin = true;
            }
            //Is Course Creator in tables (not is_coursecreator()) !!
            if (record_exists("user_coursecreators","userid",$user->id)) {
                $user->info .= "coursecreator";
                $user->role_coursecreator = true;
            }
            //Is Teacher in tables (not is_teacher()) !!
            if (record_exists("user_teachers","course",$course,"userid",$user->id)) {
                $user->info .= "teacher";
                $user->role_teacher = true;
            }
            //Is Student in tables (not is_student()) !!
            if (record_exists("user_students","course",$course,"userid",$user->id)) {
                $user->info .= "student";
                $user->role_student = true;
            }
            //Now create the backup_id record
            $backupids_rec->backup_code = $backup_unique_code;
            $backupids_rec->table_name = "user";
            $backupids_rec->old_id = $user->id;
            $backupids_rec->info = $user->info;

            //Insert the record id. backup_users decide it.
            //When all users
            if ($backup_users == 0) { 
                $status = insert_record("backup_ids",$backupids_rec,false);
                $count_users++;
            //When course users
            } else if ($backup_users == 1) {
                 //Only if user has any role
                if ($backupids_rec->info) {
                    $status = insert_record("backup_ids",$backupids_rec,false);
                    $count_users++;
                }
            }
        }
        
        //Prepare Info
        //Gets the user data
        $info[0][0] = get_string("users");
        $info[0][1] = $count_users;

        return $info;
    }

    //Calculate the number of log entries to backup
    //Return an array of info (name,value)
    function log_check_backup($course) {

        global $CFG;

        //Execute the insert
        $status = execute_sql($sql_insert,false);

        //Now execute the select
        $ids = get_records_sql("SELECT DISTINCT l.id,l.course
                                FROM {$CFG->prefix}log l
                                WHERE l.course = '$course'");
        //Gets the user data
        $info[0][0] = get_string("logs");
        if ($ids) {
            $info[0][1] = count($ids);
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
            $coursedirs = get_directory_list($rootdir);
            foreach ($coursedirs as $dir) {
                //Extracts user id from file path
                $tok = strtok($dir,"/");
                if ($tok) {
                   $userid = $tok;
                } else {
                   $tok = "";
                }
                //Insert them into backup_files
                $status = execute_sql("INSERT INTO {$CFG->prefix}backup_files
                                           (backup_code, file_type, path, old_id)
                                       VALUES
                                           ('$backup_unique_code','user','$dir','$userid')",false);
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
    //under $CFG->dataroot/$course, except $CFG->moddata
    //and put them (their path) in backup_ids
    //Return an array of info (name,value)
    function course_files_check_backup($course,$backup_unique_code) {

        global $CFG;

        $rootdir = $CFG->dataroot."/$course";
        //Check if directory exists
        if (is_dir($rootdir)) {
            $coursedirs = get_directory_list($rootdir,$CFG->moddata);
            foreach ($coursedirs as $dir) {
                //Insert them into backup_files
               $status = execute_sql("INSERT INTO {$CFG->prefix}backup_files
                                              (backup_code, file_type, path)
                                       VALUES
                                          ('$backup_unique_code','course','$dir')",false);
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
   
    //Delete old data in backup tables (if exists)
    //Two days seems to be apropiate
    function backup_delete_old_data() {

        global $CFG; 

        //Change this if you want !!
        $days = 2;
        //End change this
        $seconds = $days * 24 * 60 * 60;
        $delete_from = time()-$seconds;
        //Now delete from tables
        $status = execute_sql("DELETE FROM {$CFG->prefix}backup_ids
                               WHERE backup_code < '$delete_from'",false);
        if ($status) {
            $status = execute_sql("DELETE FROM {$CFG->prefix}backup_files
                                   WHERE backup_code < '$delete_from'",false);
        }
        //Now, delete old directory (if exists)
        if ($status) {
            $status = backup_delete_old_dirs($delete_from);
        }
        return($status);
    }

    //Function to delete dirs/files into temp/backup directory
    //older than $delete_from
    function backup_delete_old_dirs($delete_from) {

        global $CFG;

        $status = true;
        $list = get_directory_list($CFG->dataroot."/temp/backup", "", false);
        foreach ($list as $file) {
            $file_path = $CFG->dataroot."/temp/backup/".$file;
            $moddate = filemtime($file_path);
            if ($status and $moddate < $delete_from) {
                //If directory, recurse
                if (is_dir($file_path)) {
                    $status = delete_dir_contents($file_path);
                    //There is nothing, delete the directory itself
                    if ($status) {
                        $status = rmdir($file_path);
                    }
                //If file
                } else {
                    unlink("$file_path");
                }
            }
        }

        return $status;
    }

    //Function to check if a directory exists
    //and, optionally, create it
    function check_dir_exists($dir,$create=false) {

        global $CFG; 

        $status = true;
        if(!is_dir($dir)) {
            if (!$create) {
                $status = false;
            } else {
                $status = mkdir ($dir,$CFG->directorypermissions);
            }
        }
        return $status;
    }

    //Function to check and create the needed dir to 
    //save all the backup
    function check_and_create_backup_dir($backup_unique_code) {
   
        global $CFG; 

        $status = check_dir_exists($CFG->dataroot."/temp",true);
        if ($status) {
            $status = check_dir_exists($CFG->dataroot."/temp/backup",true);
        }
        if ($status) {
            $status = check_dir_exists($CFG->dataroot."/temp/backup/".$backup_unique_code,true);
        }
        
        return $status;
    }

    //Function to delete all the directory contents recursively
    //Copied from admin/delete.php
    function delete_dir_contents ($rootdir) {

        $dir = opendir($rootdir);

        $status = true;

        while ($file = readdir($dir)) {
            if ($file != "." and $file != "..") {
                $fullfile = "$rootdir/$file";
                if (filetype($fullfile) == "dir") {
                    delete_dir_contents($fullfile);
                    if (!rmdir($fullfile)) {
                        $status = false;
                    }
                } else {
                    if (!unlink("$fullfile")) {
                        $status = false;
                    }
                }
            }
        }
        closedir($dir);
 
        return $status;

    }

    //Function to clear (empty) the contents of the backup_dir
    //Copied from admin/delete.php
    function clear_backup_dir($backup_unique_code) {
  
        global $CFG; 

        $rootdir = $CFG->dataroot."/temp/backup/".$backup_unique_code;
        
        //Delete recursively
        $status = delete_dir_contents($rootdir);

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
    function start_tag($tag,$level=0,$endline=false) {
        if ($endline) {
           $endchar = "\n";
        } else {
           $endchar = "";
        }
        return str_repeat(" ",$level*2)."<".strtoupper($tag).">".$endchar;
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
    function full_tag($tag,$level=0,$endline=true,$content,$to_utf=true) {
        $st = start_tag($tag,$level,$endline);
        $co="";
        if ($to_utf) {
            $co = utf8_encode(htmlspecialchars($content));
        } else {
            $co = htmlspecialchars($content);
        }
        $et = end_tag($tag,0,true);
        return $st.$co.$et;
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
                 
            //Print the end
            fwrite ($bf,end_tag("MOD",3,true));
        }
        //The user in backup
        if ($preferences->backup_users == 1) {
            fwrite ($bf,full_tag("USERS",3,false,"course"));
        } else {
            fwrite ($bf,full_tag("USERS",3,false,"all"));
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

        fwrite ($bf,end_tag("DETAILS",2,true));


        $status = fwrite ($bf,end_tag("INFO",1,true)); 

        return $status;
    }
    
    //Prints course's general info (table course)
    function backup_course_start ($bf,$preferences) {

        global $CFG;

        $status = true;
        
        //Course open tag
        fwrite ($bf,start_tag("COURSE",1,true));

        //Get info from course
        $course=false;
        if ($courses = get_records("course","id",$preferences->backup_course)) {
            $course = $courses[$preferences->backup_course];
        }
        if ($course) {
            //Prints course info
            fwrite ($bf,full_tag("ID",2,false,$course->id));
            //Obtain the category
            $category = false;
            if ($categories = get_records("course_categories","id","$course->category")) {
                $category = $categories[$course->category];
            }
            if ($category) {
                //Prints category info
                fwrite ($bf,start_tag("CATEGORY",2,true));
                fwrite ($bf,full_tag("ID",3,false,$course->category));
                fwrite ($bf,full_tag("NAME",3,false,$category->name));
                fwrite ($bf,end_tag("CATEGORY",2,true));
            }
            //Continues with the course
            fwrite ($bf,full_tag("PASSWORD",2,false,$course->password));
            fwrite ($bf,full_tag("FULLNAME",2,false,$course->fullname));
            fwrite ($bf,full_tag("SHORTNAME",2,false,$course->shortname));
            fwrite ($bf,full_tag("SUMMARY",2,false,$course->summary));
            fwrite ($bf,full_tag("FORMAT",2,false,$course->format));
            fwrite ($bf,full_tag("NEWSITEMS",2,false,$course->newsitems));
            fwrite ($bf,full_tag("TEACHER",2,false,$course->teacher));
            fwrite ($bf,full_tag("TEACHERS",2,false,$course->teachers));
            fwrite ($bf,full_tag("STUDENT",2,false,$course->student));
            fwrite ($bf,full_tag("STUDENTS",2,false,$course->students));
            fwrite ($bf,full_tag("GUEST",2,false,$course->guest));
            fwrite ($bf,full_tag("STARDATE",2,false,$course->stardate));
            fwrite ($bf,full_tag("NUMSECTIONS",2,false,$course->numsections));
            fwrite ($bf,full_tag("SHOWRECENT",2,false,$course->showrecent));
            fwrite ($bf,full_tag("MARKER",2,false,$course->marker));
            fwrite ($bf,full_tag("TIMECREATED",2,false,$course->timecreated));
            $status = fwrite ($bf,full_tag("TIMEMODIFIED",2,false,$course->timemodified));
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
               //Gets course_module data from db
               $course_module = get_records ("course_modules","id",$tok);
               //If it's the first, pring MODS tag
               if ($first_record) {
                   fwrite ($bf,start_tag("MODS",4,true));
                   $first_record = false;
               }
               //Print mod info from course_modules
               fwrite ($bf,start_tag("MOD",5,true));
               //Save neccesary info to backup_ids
               fwrite ($bf,full_tag("ID",6,false,$tok));
               fwrite ($bf,full_tag("TYPE",6,false,$moduletype));
               fwrite ($bf,full_tag("INSTANCE",6,false,$course_module[$tok]->instance));
               fwrite ($bf,full_tag("DELETED",6,false,$course_module[$tok]->deleted));
               fwrite ($bf,full_tag("SCORE",6,false,$course_module[$tok]->score));
               fwrite ($bf,full_tag("VISIBLE",6,false,$course_module[$tok]->visible));
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

    //Returns the module type of a course_module's id in a course 
    function get_module_type ($courseid,$moduleid) {

        global $CFG;

        $results = get_records_sql ("SELECT cm.id, m.name
                                    FROM {$CFG->prefix}course_modules cm,
                                         {$CFG->prefix}modules m
                                    WHERE cm.course = '$courseid' AND
                                          cm.id = '$moduleid' AND
                                          m.id = cm.module");

        if ($results) {
            $name = $results[$moduleid]->name;
        } else {
            $name = false;
        }


      return $name;
    }

    //Print users to xml
    //Only users previously calculated in backup_ids will output
    //
    function backup_user_info ($bf,$preferences) {
    
        global $CFG;

        $status = true;

        $users = get_records_sql("SELECT u.old_id, u.table_name,u.info
                              FROM {$CFG->prefix}backup_ids u
                              WHERE u.backup_code = '$preferences->backup_unique_code' AND
                                    u.table_name = 'user'");

        //If we have users to backup
        if ($users) {
            //Begin Users tag
            fwrite ($bf,start_tag("USERS",2,true));
            //With every user
            foreach ($users as $user) {
                //Get user data from table
                $user_data = get_record("user","id",$user->old_id);
                //Begin User tag
                fwrite ($bf,start_tag("USER",3,true));
                //Output all user data
                fwrite ($bf,full_tag("ID",4,false,$user_data->id));
                fwrite ($bf,full_tag("CONFIRMED",4,false,$user_data->confirmed));
                fwrite ($bf,full_tag("DELETED",4,false,$user_data->deleted));
                fwrite ($bf,full_tag("USERNAME",4,false,$user_data->username));
                fwrite ($bf,full_tag("PASSWORD",4,false,$user_data->password));
                fwrite ($bf,full_tag("IDNUMBER",4,false,$user_data->idnumber));
                fwrite ($bf,full_tag("FIRSTNAME",4,false,$user_data->firsname));
                fwrite ($bf,full_tag("LASTNAME",4,false,$user_data->lastname));
                fwrite ($bf,full_tag("EMAIL",4,false,$user_data->email));
                fwrite ($bf,full_tag("ICQ",4,false,$user_data->icq));
                fwrite ($bf,full_tag("PHONE1",4,false,$user_data->phone1));
                fwrite ($bf,full_tag("PHONE2",4,false,$user_data->phone2));
                fwrite ($bf,full_tag("INSTITUTION",4,false,$user_data->institution));
                fwrite ($bf,full_tag("DEPARTMENT",4,false,$user_data->department));
                fwrite ($bf,full_tag("ADDRESS",4,false,$user_data->address));
                fwrite ($bf,full_tag("CITY",4,false,$user_data->city));
                fwrite ($bf,full_tag("COUNTRY",4,false,$user_data->country));
                fwrite ($bf,full_tag("LANG",4,false,$user_data->lang));
                fwrite ($bf,full_tag("TIMEZONE",4,false,$user_data->timezone));
                fwrite ($bf,full_tag("FIRSTACCESS",4,false,$user_data->firstaccess));
                fwrite ($bf,full_tag("LASTACCESS",4,false,$user_data->lastaccess));
                fwrite ($bf,full_tag("LASTLOGIN",4,false,$user_data->lastlogin));
                fwrite ($bf,full_tag("CURRENTLOGIN",4,false,$user_data->currentlogin));
                fwrite ($bf,full_tag("LASTIP",4,false,$user_data->lastIP));
                fwrite ($bf,full_tag("SECRET",4,false,$user_data->secret));
                fwrite ($bf,full_tag("PICTURE",4,false,$user_data->picture));
                fwrite ($bf,full_tag("URL",4,false,$user_data->url));
                fwrite ($bf,full_tag("DESCRIPTION",4,false,$user_data->description));
                fwrite ($bf,full_tag("MAILFORMAT",4,false,$user_data->mailformat));
                fwrite ($bf,full_tag("MAILDISPLAY",4,false,$user_data->maildisplay));
                fwrite ($bf,full_tag("HTMLEDITOR",4,false,$user_data->htmleditor));
                fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$user_data->timemodified));

                //Output every user role (with its associated info) 
                $user->isadmin = strpos($user->info,"admin");
                $user->iscoursecreator = strpos($user->info,"coursecreator");
                $user->isteacher = strpos($user->info,"teacher");
                $user->isstudent = strpos($user->info,"student");
                if ($user->isadmin!==false or 
                    $user->iscoursecreator!==false or 
                    $user->isteacher!==false or 
                    $user->isstudent!==false) {
                    //Begin ROLES tag
                    fwrite ($bf,start_tag("ROLES",4,true));
                    //PRINT ROLE INFO
                    //Admins
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
                        //Print ROLE end
                        fwrite ($bf,end_tag("ROLE",5,true));   
                    }


                    //End ROLES tag
                    fwrite ($bf,end_tag("ROLES",4,true));
                }
                //End User tag
                fwrite ($bf,end_tag("USER",3,true));
            }
            //End Users tag
            fwrite ($bf,end_tag("USERS",2,true));
        } else {
            //There isn't users. Impossible !!
            $status = false;
        }

        return $status;
    }

    //Backup log info (time ordered)
    function backup_log_info($bf,$preferences) {

        global $CFG;
  
        $status = true;

        $logs = get_records ("log","course",$preferences->backup_course);

        //We have logs
        if ($logs) {
            //Pring logs header
            fwrite ($bf,start_tag("LOGS",2,true));
            //Iterate 
            foreach ($logs as $log) {
                //See if it is a valid module to backup
                if ($log->module == "course" or 
                    $log->module == "user") {
                    //Begin log tag
                     fwrite ($bf,start_tag("LOG",3,true));

                    //Output log data
                    fwrite ($bf,full_tag("ID",4,false,$log->id));
                    fwrite ($bf,full_tag("TIME",4,false,$log->time));
                    fwrite ($bf,full_tag("USERID",4,false,$log->userid));
                    fwrite ($bf,full_tag("IP",4,false,$log->ip));
                    fwrite ($bf,full_tag("MODULE",4,false,$log->module));
                    fwrite ($bf,full_tag("ACTION",4,false,$log->action));
                    fwrite ($bf,full_tag("URL",4,false,$log->url));
                    fwrite ($bf,full_tag("INFO",4,false,$log->info));

                    //End log data
                     fwrite ($bf,end_tag("LOG",3,true));
                }
            }
            $status = fwrite ($bf,end_tag("LOGS",2,true));
        }

        

        return $status;
    }
?>
