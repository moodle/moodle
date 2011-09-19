<?php
    //This file contains all the general function needed (file manipulation...)
    //not directly part of the backup/restore utility plus some constants

    // Define "restoreto" options
    define('RESTORETO_CURRENT_DELETING',  0);
    define('RESTORETO_CURRENT_ADDING',    1);
    define('RESTORETO_NEW_COURSE',        2);
    define('RESTORETO_EXISTING_DELETING', 3);
    define('RESTORETO_EXISTING_ADDING',   4);

    require_once($CFG->libdir . '/completionlib.php');

    //Sets a name/value pair in config_plugin table
    function backup_set_config($name, $value) {
        return set_config($name, $value, 'backup');
    }

    //Gets all the information from config_plugin table
    function backup_get_config() {
        $backup_config = get_config('backup');
        return (object)$backup_config;
    }

    //Delete old data in backup tables (if exists)
    //Four hours seem to be appropiate now that backup is stable
    function backup_delete_old_data() {
        global $CFG, $DB;

        //Change this if you want !!
        $hours = 4;
        //End change this
        $seconds = $hours * 60 * 60;
        $delete_from = time()-$seconds;
        //Now delete from tables
        $status = $DB->execute("DELETE FROM {backup_ids}
                                 WHERE backup_code < ?", array($delete_from));
        if ($status) {
            $status = $DB->execute("DELETE FROM {backup_files}
                                     WHERE backup_code < ?", array($delete_from));
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
        //Get files and directories in the temp backup dir witout descend
        $list = get_directory_list($CFG->tempdir."/backup", "", false, true, true);
        foreach ($list as $file) {
            $file_path = $CFG->tempdir."/backup/".$file;
            $moddate = filemtime($file_path);
            if ($status && $moddate < $delete_from) {
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

    //Function to check and create the needed dir to
    //save all the backup
    function check_and_create_backup_dir($backup_unique_code) {
        global $CFG;

        $status = check_dir_exists($CFG->tempdir."",true);
        if ($status) {
            $status = check_dir_exists($CFG->tempdir."/backup",true);
        }
        if ($status) {
            $status = check_dir_exists($CFG->tempdir."/backup/".$backup_unique_code,true);
        }

        return $status;
    }

    //Function to delete all the directory contents recursively
    //it supports a excluded dit too
    //Copied from the web !!
    function delete_dir_contents ($dir,$excludeddir="") {
        global $CFG;

        if (!is_dir($dir)) {
            // if we've been given a directory that doesn't exist yet, return true.
            // this happens when we're trying to clear out a course that has only just
            // been created.
            return true;
        }
        $slash = "/";

        // Create arrays to store files and directories
        $dir_files      = array();
        $dir_subdirs    = array();

        // Make sure we can delete it
        chmod($dir, $CFG->directorypermissions);

        if ((($handle = opendir($dir))) == FALSE) {
            // The directory could not be opened
            return false;
        }

        // Loop through all directory entries, and construct two temporary arrays containing files and sub directories
        while (false !== ($entry = readdir($handle))) {
            if (is_dir($dir. $slash .$entry) && $entry != ".." && $entry != "." && $entry != $excludeddir) {
                $dir_subdirs[] = $dir. $slash .$entry;
            }
            else if ($entry != ".." && $entry != "." && $entry != $excludeddir) {
                $dir_files[] = $dir. $slash .$entry;
            }
        }

        // Delete all files in the curent directory return false and halt if a file cannot be removed
        $countdirfiles = count($dir_files);
        for ($i=0; $i<$countdirfiles; $i++) {
            chmod($dir_files[$i], $CFG->directorypermissions);
            if (((unlink($dir_files[$i]))) == FALSE) {
                return false;
            }
        }

        // Empty sub directories and then remove the directory
        $countdirsubdirs = count($dir_subdirs);
        for($i=0; $i<$countdirsubdirs; $i++) {
            chmod($dir_subdirs[$i], $CFG->directorypermissions);
            if (delete_dir_contents($dir_subdirs[$i]) == FALSE) {
                return false;
            }
            else {
                if (remove_dir($dir_subdirs[$i]) == FALSE) {
                return false;
                }
            }
        }

        // Close directory
        closedir($handle);

        // Success, every thing is gone return true
        return true;
    }

    //Function to clear (empty) the contents of the backup_dir
    function clear_backup_dir($backup_unique_code) {
        global $CFG;

        $rootdir = $CFG->tempdir."/backup/".$backup_unique_code;

        //Delete recursively
        $status = delete_dir_contents($rootdir);

        return $status;
    }

    //Returns the module type of a course_module's id in a course
    function get_module_type ($courseid,$moduleid) {
        global $DB;

        $results = $DB->get_records_sql("SELECT cm.id, m.name
                                           FROM {course_modules} cm, {modules} m
                                          WHERE cm.course = ? AND cm.id = ? AND
                                                m.id = cm.module", array($courseid, $moduleid));

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

        $results = null;

        $dir = opendir($rootdir);
        while (false !== ($file=readdir($dir))) {
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

        $results = "";

        $dir = opendir($rootdir);
        while (false !== ($file=readdir($dir))) {
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
        global $CFG, $DB;

        $status = true;

        //true->do it, false->don't do it. To debug if necessary.
        if (true) {
            //Now delete from tables
            $status = $DB->delete_records('backup_ids', array('backup_code'=>$preferences->backup_unique_code))
                   && $DB->delete_records('backup_files', array('backup_code'=>$preferences->backup_unique_code));

            //Now, delete temp directory (if exists)
            $file_path = $CFG->tempdir."/backup/".$preferences->backup_unique_code;
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

    function backup_copy_file ($from_file,$to_file,$log_clam=false) {
        global $CFG;

        if (is_file($from_file)) {
            //echo "<br />Copying ".$from_file." to ".$to_file;              //Debug
            //$perms=fileperms($from_file);
            //return copy($from_file,$to_file) && chmod($to_file,$perms);
            umask(0000);
            if (copy($from_file,$to_file)) {
                chmod($to_file,$CFG->directorypermissions);
                if (!empty($log_clam)) {
                    //clam_log_upload($to_file,null,true);
                }
                return true;
            }
            return false;
        }
        else if (is_dir($from_file)) {
            return backup_copy_dir($from_file,$to_file);
        }
        else{
            //echo "<br />Error: not file or dir ".$from_file;               //Debug
            return false;
        }
    }

    function backup_copy_dir($from_file,$to_file) {
        global $CFG;

        $status = true; // Initialize this, next code will change its value if needed

        if (!is_dir($to_file)) {
            //echo "<br />Creating ".$to_file;                                //Debug
            umask(0000);
            $status = mkdir($to_file,$CFG->directorypermissions);
        }
        $dir = opendir($from_file);
        while (false !== ($file=readdir($dir))) {
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

    /**
     * Are we restoring a backup that was made on the same site that we are restoring to?
     * This relies on some information that was only added to backup files in January 2009.
     * For older backup files, fall back to guessing based on wwwroot. MDL-16614 explains
     * when this guess could give the wrong answer.
     * @return boolean true if the backup was made on the same site we are restoring to.
     */
    function backup_is_same_site(&$restore) {
        global $CFG;
        static $hashedsiteid = null;
        if (is_null($hashedsiteid)) {
            $hashedsiteid = md5(get_site_identifier());
        }
        if (!empty($restore->original_siteidentifier)) {
            return $restore->original_siteidentifier == $hashedsiteid;
        } else {
            return $restore->original_wwwroot == $CFG->wwwroot;
        }
    }

    //This function is used to insert records in the backup_ids table
    //If the info field is greater than max_db_storage, then its info
    //is saved to filesystem
    function backup_putid($backup_unique_code, $table, $old_id, $new_id, $info="") {
        global $CFG, $DB;

        $max_db_storage = 128;  //Max bytes to save to db, else save to file

        $status = true;

        //First delete to avoid PK duplicates
        $status = backup_delid($backup_unique_code, $table, $old_id);

        //Now, serialize info
        $info_ser = serialize($info);

        //Now, if the size of $info_ser > $max_db_storage, save it to filesystem and
        //insert a "infile" in the info field

        if (strlen($info_ser) > $max_db_storage) {
            //Calculate filename (in current_backup_dir, $backup_unique_code_$table_$old_id.info)
            $filename = $CFG->tempdir."/backup/".$backup_unique_code."/".$backup_unique_code."_".$table."_".$old_id.".info";
            //Save data to file
            $status = backup_data2file($filename,$info_ser);
            //Set info_to save
            $info_to_save = "infile";
        } else {
            //Saving to db
            $info_to_save = $info_ser;
        }

        //Now, insert the record
        if ($status) {
            //Build the record
            $rec = new stdClass();
            $rec->backup_code = $backup_unique_code;
            $rec->table_name = $table;
            $rec->old_id = $old_id;
            $rec->new_id = ($new_id === null? 0 : $new_id);
            $rec->info = $info_to_save;

            $DB->insert_record('backup_ids', $rec, false);
        }
        return $status;
    }

    //This function is used to delete recods from the backup_ids table
    //If the info field is "infile" then the file is deleted too
    function backup_delid ($backup_unique_code, $table, $old_id) {
        global $DB;
        return $DB->delete_records('backup_ids', array('backup_code'=>$backup_unique_code, 'table_name'=>$table, 'old_id'=>$old_id));
    }

    //This function is used to get a record from the backup_ids table
    //If the info field is "infile" then its info
    //is read from filesystem
    function backup_getid ($backup_unique_code, $table, $old_id) {
        global $CFG, $DB;

        $status = true;
        $status2 = true;

        $status = $DB->get_record("backup_ids", array("backup_code"=>$backup_unique_code,
                                  "table_name"=>$table, "old_id"=>$old_id));

        //If info field = "infile", get file contents
        if (!empty($status->info) && $status->info == "infile") {
            $filename = $CFG->tempdir."/backup/".$backup_unique_code."/".$backup_unique_code."_".$table."_".$old_id.".info";
            //Read data from file
            $status2 = backup_file2data($filename,$info);
            if ($status2) {
                //unserialize data
                $status->info = unserialize($info);
            } else {
                $status = false;
            }
        } else {
            //Only if status (record exists)
            if (!empty($status->info)) {
                if ($status->info === 'needed') {
                    // TODO: ugly hack - fix before 1.9.1
                    debugging('Incorrect string "needed" in $status->info, please fix the code (table:'.$table.'; old_id:'.$old_id.').', DEBUG_DEVELOPER);
                } else {
                    ////First strip slashes
                    $temp = $status->info;
                    //Now unserialize
                    $status->info = unserialize($temp);
                }
            }
        }

        return $status;
    }

    //This function is used to add slashes (and decode from UTF-8 if needed)
    //It's used intensivelly when restoring modules and saving them in db
    function backup_todb ($data) {
        // MDL-10770
        if ($data === '$@NULL@$') {
            return null;
        } else {
            return restore_decode_absolute_links($data);
        }
    }

    //This function is used to check that every necessary function to
    //backup/restore exists in the current php installation. Thanks to
    //gregb@crowncollege.edu by the idea.
    function backup_required_functions($justcheck=false) {

        if(!function_exists('utf8_encode')) {
            if (empty($justcheck)) {
                print_error('needphpext', '', '', 'XML');
            } else {
                return false;
            }
        }

        return true;
    }

    //This function send n white characters to the browser and flush the
    //output buffer. Used to avoid browser timeouts and to show the progress.
    function backup_flush($n=0,$time=false) {
        if (defined('RESTORE_SILENTLY_NOFLUSH')) {
            return;
        }
        if ($time) {
            $ti = strftime("%X",time());
        } else {
            $ti = "";
        }
        echo str_repeat(" ", $n) . $ti . "\n";
        flush();
    }

    //This function creates the filename and write data to it
    //returning status as result
    function backup_data2file ($file,&$data) {

        $status = true;
        $status2 = true;

        $f = fopen($file,"w");
        $status = fwrite($f,$data);
        $status2 = fclose($f);

        return ($status && $status2);
    }

    //This function read the filename and read data from it
    function backup_file2data ($file,&$data) {

        $status = true;
        $status2 = true;

        $f = fopen($file,"r");
        $data = fread ($f,filesize($file));
        $status2 = fclose($f);

        return ($status && $status2);
    }
