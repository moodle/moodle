<?PHP //$Id$
    //This file contains all the general function needed (file manipulation...)
    //not directly part of the backup/restore utility

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
                umask(0000);
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

    //This function return the names of all directories under a give directory
    //Not recursive
    function list_directories ($rootdir) {
  
        $dir = opendir($rootdir);
        while ($file=readdir($dir)) {
            if ($file=="." || $file=="..") {
                continue;
            }
            if (is_dir($rootdir."/".$file)) {
                $results[$file] = $file;
            }
        }
        closedir($dir);  
        return $results;       
    }

    //This function return the names of all directories and files under a give directory
    //Not recursive
    function list_directories_and_files ($rootdir) {
 
        $dir = opendir($rootdir);
        while ($file=readdir($dir)) {
            if ($file=="." || $file=="..") {
                continue;
            }
            $results[$file] = $file;
        }
        closedir($dir); 
        return $results;
    }

    //This function clean data from backup tables and
    //delete all temp files used
    function clean_temp_data ($preferences) {

        global $CFG;

        $status = true;

        //true->do it, false->don't do it. To debug if necessary.
        if (true) {
            //Now delete from tables
            $status = execute_sql("DELETE FROM {$CFG->prefix}backup_ids
                                   WHERE backup_code = '$preferences->backup_unique_code'",false);
            if ($status) {
                $status = execute_sql("DELETE FROM {$CFG->prefix}backup_files
                                       WHERE backup_code = '$preferences->backup_unique_code'",false);
            }
            //Now, delete temp directory (if exists)
            $file_path = $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code;
            if (is_dir($file_path)) {
                $status = delete_dir_contents($file_path);
                //There is nothing, delete the directory itself
                if ($status) {
                    $status = rmdir($file_path);
                }
            }
        }
        return $status;
    }

    // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //This functions are used to copy any file or directory ($from_file)
    //to a new file or directory ($to_file). It works recursively and
    //mantains file perms.
    //I've copied it from: http://www.php.net/manual/en/function.copy.php
    //Little modifications done
  
    function backup_copy_file ($from_file,$to_file) {
        if (is_file($from_file)) {
            $perms=fileperms($from_file);
            return copy($from_file,$to_file) && chmod($to_file,$perms);
        }
        else if (is_dir($from_file)) {
            return backup_copy_dir($from_file,$to_file);
        }
        else{
            return false;
        }
    }

    function backup_copy_dir($from_file,$to_file) {
        if (!is_dir($to_file)) {
            mkdir($to_file);
            chmod("$to_file",0777);
        }
        $dir = opendir($from_file);
        while ($file=readdir($dir)) {
            if ($file=="." || $file=="..") {
                continue;
            }
            $status = backup_copy_file ("$from_file/$file","$to_file/$file");
        }
        closedir($dir);
        return $status;
    }
    ///Ends copy file/dirs functions
    // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    //This function upgrades, if necesary, the backup-restore tables
    //It's called from backup.php and restore.php
    function upgrade_backup_db($updgradeto,$backup_release,$continueto) {
    
        global $CFG,$db;

        //Check backup_version
        if ($CFG->backup_version) {
            if ($updgradeto > $CFG->backup_version) {  // upgrade
                $a->oldversion = $CFG->backup_version;
                $a->newversion = $updgradeto;
                $strdatabasechecking = get_string("databasechecking", "", $a);
                $strdatabasesuccess  = get_string("databasesuccess");
                print_header($strdatabasechecking, $strdatabasechecking, $strdatabasechecking);
                print_heading($strdatabasechecking);
                $db->debug=true;
                if (backup_upgrade($a->oldversion)) {
                    $db->debug=false;
                    if (set_config("backup_version", $a->newversion)) {
                        notify($strdatabasesuccess, "green");
                        notify("You are running Backup/Recovery version ".$backup_release,"black");
                        print_continue($continueto);
                        die;
                    } else {
                        notify("Upgrade failed!  (Could not update version in config table)");
                        die;
                    }
                } else {
                    $db->debug=false;
                    notify("Upgrade failed!  See backup_version.php");
                    die;
                }
            } else if ($updgradeto < $CFG->backup_version) {
                notify("WARNING!!!  The code you are using is OLDER than the version that made these databases!");
            }
        //Not exists. Starting installation
        } else {
            $strdatabaseupgrades = get_string("databaseupgrades");
            print_header($strdatabaseupgrades, $strdatabaseupgrades, $strdatabaseupgrades);
    
            if (set_config("backup_version", "2003010100")) {
                print_heading("You are currently going to install the needed structures to Backup/Recover");
                print_continue($continue_to);
                die;
            }
        }
    }
 
    //This function is used to insert records in the backup_ids table
    function backup_putid ($backup_unique_code, $table, $old_id, $new_id, $info="") {

        global $CFG;
 
        $status = true;
        
        //First delete to avoid PK duplicates
        $status = backup_delid($backup_unique_code, $table, $old_id);

        $status = execute_sql("INSERT INTO {$CFG->prefix}backup_ids
                                   (backup_code, table_name, old_id, new_id, info)
                               VALUES 
                                   ($backup_unique_code, '$table', '$old_id', '$new_id', '$info')",false);
        return $status;
    }

    //This function is used to delete recods from the backup_ids table
    function backup_delid ($backup_unique_code, $table, $old_id) {

        global $CFG;

        $status = true;

        $status = execute_sql("DELETE FROM {$CFG->prefix}backup_ids
                               WHERE backup_code = $backup_unique_code AND
                                     table_name = '$table' AND
                                     old_id = '$old_id'",false);
        return $status;
    }

    //This function is used to get a record from the backup_ids table
    function backup_getid ($backup_unique_code, $table, $old_id) {

        global $CFG;

        $status = true;

        $status = get_record ("backup_ids","backup_code",$backup_unique_code,
                                           "table_name",$table, 
                                           "old_id", $old_id);

        return $status;
    }

    //This function is used to add slashes and decode from UTF-8
    //It's used intensivelly when restoring modules and saving them in db
    function backup_todb ($data) {
        return addslashes(utf8_decode($data));
    }

?>
