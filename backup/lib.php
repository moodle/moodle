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
?>
