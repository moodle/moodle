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


?>
