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

        if ($backup_users == 0) {
            //Insert all users (from user)
            $sql_insert = "INSERT INTO {$CFG->prefix}backup_ids
                               (backup_code, table_name, old_id)
                           SELECT DISTINCT '$backup_unique_code','user',u.id
                           FROM {$CFG->prefix}user u";
        } else {
            //Insert only course users (from user_students and user_teachers)
            $sql_insert = "INSERT INTO {$CFG->prefix}backup_ids
                               (backup_code, table_name, old_id)
                           SELECT DISTINCT '$backup_unique_code','user',u.id
                           FROM {$CFG->prefix}user u,
                                {$CFG->prefix}user_students s,
                                {$CFG->prefix}user_teachers t
                           WHERE s.course = '$course' AND
                                 t.course = s.course AND
                                 (s.userid = u.id OR t.userid = u.id)";
        }
        //Execute the insert
        $status = execute_sql($sql_insert,false);

        //Now execute the select
        $ids = get_records_sql("SELECT DISTINCT u.old_id,u.table_name
                                FROM {$CFG->prefix}backup_ids u
                                WHERE backup_code = '$backup_unique_code' AND
                                      table_name ='user'");
    
        //Gets the user data
        $info[0][0] = get_string("users");
        if ($ids) {
            $info[0][1] = count($ids);      
        } else {
            $info[0][1] = 0;
        }

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
        $coursedirs = get_directory_list($rootdir,$CFG->moddata);
        foreach ($coursedirs as $dir) {
            //Insert them into backup_files
           $status = execute_sql("INSERT INTO {$CFG->prefix}backup_files
                                      (backup_code, file_type, path)
                                  VALUES
                                      ('$backup_unique_code','course','$dir')",false);
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
        $days = 1;
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
                $status = delete_dir_contents($file_path);
                //There is nothing, delete the directory itself
                if ($status) {
                    $status = rmdir($file_path);
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
            $co = $content;
        } else {
            $co = $content;
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
            fwrite ($bf,full_tag("USERS",3,false,"COURSE"));
        } else {
            fwrite ($bf,full_tag("USERS",3,false,"ALL"));
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


        fwrite ($bf,end_tag("INFO",1,true)); 
    }
?>
