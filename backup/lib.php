<?php //$Id$
    //This file contains all the general function needed (file manipulation...)
    //not directly part of the backup/restore utility

    require_once($CFG->dirroot.'/lib/uploadlib.php');

    //Sets a name/value pair in backup_config table
    function backup_set_config($name, $value) {
        if (get_field("backup_config", "name", "name", $name)) {
            return set_field("backup_config", "value", addslashes($value), "name", $name);
        } else {
            $config = new object();
            $config->name = $name;
            $config->value = addslashes($value);
            return insert_record("backup_config", $config);
        }
    }

    //Gets all the information from backup_config table
    function backup_get_config() {
        $backup_config = null;
        if ($configs = get_records("backup_config")) {
            foreach ($configs as $config) {
                $backup_config[$config->name] = $config->value;
            }
        }
        return (object)$backup_config;
    }

    //Delete old data in backup tables (if exists)
    //Four hours seem to be appropiate now that backup is stable
    function backup_delete_old_data() {

        global $CFG;

        //Change this if you want !!
        $hours = 4;
        //End change this
        $seconds = $hours * 60 * 60;
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
        //Get files and directories in the temp backup dir witout descend
        $list = get_directory_list($CFG->dataroot."/temp/backup", "", false, true, true);
        foreach ($list as $file) {
            $file_path = $CFG->dataroot."/temp/backup/".$file;
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
    //it supports a excluded dit too
    //Copied from the web !!
    function delete_dir_contents ($dir,$excludeddir="") {

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
        chmod($dir, 0777);

        if ((($handle = opendir($dir))) == FALSE) {
            // The directory could not be opened
            return false;
        }

        // Loop through all directory entries, and construct two temporary arrays containing files and sub directories
        while($entry = readdir($handle)) {
            if (is_dir($dir. $slash .$entry) && $entry != ".." && $entry != "." && $entry != $excludeddir) {
                $dir_subdirs[] = $dir. $slash .$entry;
            }
            else if ($entry != ".." && $entry != "." && $entry != $excludeddir) {
                $dir_files[] = $dir. $slash .$entry;
            }
        }

        // Delete all files in the curent directory return false and halt if a file cannot be removed
        for($i=0; $i<count($dir_files); $i++) {
            chmod($dir_files[$i], 0777);
            if (((unlink($dir_files[$i]))) == FALSE) {
                return false;
            }
        }

        // Empty sub directories and then remove the directory
        for($i=0; $i<count($dir_subdirs); $i++) {
            chmod($dir_subdirs[$i], 0777);
            if (delete_dir_contents($dir_subdirs[$i]) == FALSE) {
                return false;
            }
            else {
                if (rmdir($dir_subdirs[$i]) == FALSE) {
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

        $results = null;

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

        $results = "";

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
                    clam_log_upload($to_file,null,true);
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


    function upgrade_backup_db($continueto) {
    /// This function upgrades the backup tables, if necessary
    /// It's called from admin/index.php, also backup.php and restore.php

        global $CFG, $db;

        require_once ("$CFG->dirroot/backup/version.php");  // Get code versions

        if (empty($CFG->backup_version)) {                  // Backup has never been installed.
            $strdatabaseupgrades = get_string("databaseupgrades");
            print_header($strdatabaseupgrades, $strdatabaseupgrades, $strdatabaseupgrades, "",
                    upgrade_get_javascript(), false, "&nbsp;", "&nbsp;");

            upgrade_log_start();
            print_heading('backup');
            $db->debug=true;

        /// Both old .sql files and new install.xml are supported
        /// but we priorize install.xml (XMLDB) if present
            $status = false;
            if (file_exists($CFG->dirroot . '/backup/db/install.xml')) {
                $status = install_from_xmldb_file($CFG->dirroot . '/backup/db/install.xml'); //New method
            } else if (file_exists($CFG->dirroot . '/backup/db/' . $CFG->dbtype . '.sql')) {
                $status = modify_database($CFG->dirroot . '/backup/db/' . $CFG->dbtype . '.sql'); //Old method
            }

            $db->debug = false;
            if ($status) {
                if (set_config("backup_version", $backup_version) and set_config("backup_release", $backup_release)) {
                    //initialize default backup settings now
                    $adminroot = admin_get_root();
                    apply_default_settings($adminroot->locate('backups'));
                    notify(get_string("databasesuccess"), "green");
                    notify(get_string("databaseupgradebackups", "", $backup_version), "green");
                    print_continue($continueto);
                    print_footer('none');
                    exit;
                } else {
                    error("Upgrade of backup system failed! (Could not update version in config table)");
                }
            } else {
                error("Backup tables could NOT be set up successfully!");
            }
        }

    /// Upgrading code starts here
        $oldupgrade = false;
        $newupgrade = false;
        if (is_readable($CFG->dirroot . '/backup/db/' . $CFG->dbtype . '.php')) {
            include_once($CFG->dirroot . '/backup/db/' . $CFG->dbtype . '.php');  // defines old upgrading function
            $oldupgrade = true;
        }
        if (is_readable($CFG->dirroot . '/backup/db/upgrade.php')) {
            include_once($CFG->dirroot . '/backup/db/upgrade.php');  // defines new upgrading function
            $newupgrade = true;
        }

        if ($backup_version > $CFG->backup_version) {       // Upgrade tables
            $strdatabaseupgrades = get_string("databaseupgrades");
            print_header($strdatabaseupgrades, $strdatabaseupgrades, $strdatabaseupgrades, '', upgrade_get_javascript());

            upgrade_log_start();
            print_heading('backup');

        /// Run de old and new upgrade functions for the module
            $oldupgrade_function = 'backup_upgrade';
            $newupgrade_function = 'xmldb_backup_upgrade';

        /// First, the old function if exists
            $oldupgrade_status = true;
            if ($oldupgrade && function_exists($oldupgrade_function)) {
                $db->debug = true;
                $oldupgrade_status = $oldupgrade_function($CFG->backup_version);
            } else if ($oldupgrade) {
                notify ('Upgrade function ' . $oldupgrade_function . ' was not available in ' .
                        '/backup/db/' . $CFG->dbtype . '.php');
            }

        /// Then, the new function if exists and the old one was ok
            $newupgrade_status = true;
            if ($newupgrade && function_exists($newupgrade_function) && $oldupgrade_status) {
                $db->debug = true;
                $newupgrade_status = $newupgrade_function($CFG->backup_version);
            } else if ($newupgrade) {
                notify ('Upgrade function ' . $newupgrade_function . ' was not available in ' .
                        '/backup/db/upgrade.php');
            }

            $db->debug=false;
        /// Now analyze upgrade results
            if ($oldupgrade_status && $newupgrade_status) {    // No upgrading failed
                if (set_config("backup_version", $backup_version) and set_config("backup_release", $backup_release)) {
                    notify(get_string("databasesuccess"), "green");
                    notify(get_string("databaseupgradebackups", "", $backup_version), "green");
                    print_continue($continueto);
                    print_footer('none');
                    exit;
                } else {
                    error("Upgrade of backup system failed! (Could not update version in config table)");
                }
            } else {
                error("Upgrade failed!  See backup/version.php");
            }

        } else if ($backup_version < $CFG->backup_version) {
            upgrade_log_start();
            notify("WARNING!!!  The code you are using is OLDER than the version that made these databases!");
        }
        upgrade_log_finish();
    }


    //This function is used to insert records in the backup_ids table
    //If the info field is greater than max_db_storage, then its info
    //is saved to filesystem
    function backup_putid ($backup_unique_code, $table, $old_id, $new_id, $info="") {

        global $CFG;

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
            $filename = $CFG->dataroot."/temp/backup/".$backup_unique_code."/".$backup_unique_code."_".$table."_".$old_id.".info";
            //Save data to file
            $status = backup_data2file($filename,$info_ser);
            //Set info_to save
            $info_to_save = "infile";
        } else {
            //Saving to db, addslashes
            $info_to_save = addslashes($info_ser);
        }

        //Now, insert the record
        if ($status) {
            //Build the record
            $rec = new object();
            $rec->backup_code = $backup_unique_code;
            $rec->table_name = $table;
            $rec->old_id = $old_id;
            $rec->new_id = ($new_id === null? 0 : $new_id);
            $rec->info = $info_to_save;

            if (!insert_record('backup_ids', $rec, false)) {
                $status = false;
            }
        }
        return $status;
    }

    //This function is used to delete recods from the backup_ids table
    //If the info field is "infile" then the file is deleted too
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
    //If the info field is "infile" then its info
    //is read from filesystem
    function backup_getid ($backup_unique_code, $table, $old_id) {

        global $CFG;

        $status = true;
        $status2 = true;

        $status = get_record ("backup_ids","backup_code",$backup_unique_code,
                                           "table_name",$table,
                                           "old_id", $old_id);

        //If info field = "infile", get file contents
        if (!empty($status->info) && $status->info == "infile") {
            $filename = $CFG->dataroot."/temp/backup/".$backup_unique_code."/".$backup_unique_code."_".$table."_".$old_id.".info";
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
            if ($status) {
                ////First strip slashes
                $temp = stripslashes($status->info);
                //Now unserialize
                $status->info = unserialize($temp);
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
            return restore_decode_absolute_links(addslashes($data));
        }
    }

    //This function is used to check that every necessary function to
    //backup/restore exists in the current php installation. Thanks to
    //gregb@crowncollege.edu by the idea.
    function backup_required_functions($justcheck=false) {

        if(!function_exists('utf8_encode')) {
            if (empty($justcheck)) {
                error('You need to add XML support to your PHP installation');
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

    /** this function will restore an entire backup.zip into the specified course
     * using standard moodle backup/restore functions, but silently.
     * @param string $pathtofile the absolute path to the backup file.
     * @param int $destinationcourse the course id to restore to.
     * @param boolean $emptyfirst whether to delete all coursedata first.
     * @param boolean $userdata whether to include any userdata that may be in the backup file.
     * @param array $preferences optional, 0 will be used.  Can contain:
     *   metacourse
     *   logs
     *   course_files
     *   messages
     */
    function import_backup_file_silently($pathtofile,$destinationcourse,$emptyfirst=false,$userdata=false, $preferences=array()) {
        global $CFG,$SESSION,$USER; // is there such a thing on cron? I guess so..
        global $restore; // ick
        if (empty($USER)) {
            $USER = get_admin();
            $USER->admin = 1; // not sure why, but this doesn't get set
        }

        define('RESTORE_SILENTLY',true); // don't output all the stuff to us.

        $debuginfo = 'import_backup_file_silently: ';
        $cleanupafter = false;
        $errorstr = ''; // passed by reference to restore_precheck to get errors from.

        if (!$course = get_record('course','id',$destinationcourse)) {
            mtrace($debuginfo.'Course with id $destinationcourse was not a valid course!');
            return false;
        }

        // first check we have a valid file.
        if (!file_exists($pathtofile) || !is_readable($pathtofile)) {
            mtrace($debuginfo.'File '.$pathtofile.' either didn\'t exist or wasn\'t readable');
            return false;
        }

        // now make sure it's a zip file
        require_once($CFG->dirroot.'/lib/filelib.php');
        $filename = substr($pathtofile,strrpos($pathtofile,'/')+1);
        $mimetype = mimeinfo("type", $filename);
        if ($mimetype != 'application/zip') {
            mtrace($debuginfo.'File '.$pathtofile.' was of wrong mimetype ('.$mimetype.')' );
            return false;
        }

        // restore_precheck wants this within dataroot, so lets put it there if it's not already..
        if (strstr($pathtofile,$CFG->dataroot) === false) {
            // first try and actually move it..
            if (!check_dir_exists($CFG->dataroot.'/temp/backup/',true)) {
                mtrace($debuginfo.'File '.$pathtofile.' outside of dataroot and couldn\'t move it! ');
                return false;
            }
            if (!copy($pathtofile,$CFG->dataroot.'/temp/backup/'.$filename)) {
                mtrace($debuginfo.'File '.$pathtofile.' outside of dataroot and couldn\'t move it! ');
                return false;
            } else {
                $pathtofile = 'temp/backup/'.$filename;
                $cleanupafter = true;
            }
        } else {
            // it is within dataroot, so take it off the path for restore_precheck.
            $pathtofile = substr($pathtofile,strlen($CFG->dataroot.'/'));
        }

        if (!backup_required_functions()) {
            mtrace($debuginfo.'Required function check failed (see backup_required_functions)');
            return false;
        }

        @ini_set("max_execution_time","3000");
        raise_memory_limit("192M");

        if (!$backup_unique_code = restore_precheck($destinationcourse,$pathtofile,$errorstr,true)) {
            mtrace($debuginfo.'Failed restore_precheck (error was '.$errorstr.')');
            return false;
        }

        $SESSION->restore = new StdClass;

        // add on some extra stuff we need...
        $SESSION->restore->metacourse   = $restore->metacourse = (isset($preferences['restore_metacourse']) ? $preferences['restore_metacourse'] : 0);
        $SESSION->restore->restoreto    = $restore->restoreto = 1;
        $SESSION->restore->users        = $restore->users = $userdata;
        $SESSION->restore->logs         = $restore->logs = (isset($preferences['restore_logs']) ? $preferences['restore_logs'] : 0);
        $SESSION->restore->user_files   = $restore->user_files = $userdata;
        $SESSION->restore->messages     = $restore->messages = (isset($preferences['restore_messages']) ? $preferences['restore_messages'] : 0);
        $SESSION->restore->course_id    = $restore->course_id = $destinationcourse;
        $SESSION->restore->restoreto    = 1;
        $SESSION->restore->course_id    = $destinationcourse;
        $SESSION->restore->deleting     = $emptyfirst;
        $SESSION->restore->restore_course_files = $restore->course_files = (isset($preferences['restore_course_files']) ? $preferences['restore_course_files'] : 0);
        $SESSION->restore->backup_version = $SESSION->info->backup_backup_version;
        $SESSION->restore->course_startdateoffset = $course->startdate - $SESSION->course_header->course_startdate;

        restore_setup_for_check($SESSION->restore,$backup_unique_code);

        // maybe we need users (defaults to 2 in restore_setup_for_check)
        if (!empty($userdata)) {
            $SESSION->restore->users = 1;
        }

        // we also need modules...
        if ($allmods = get_records("modules")) {
            foreach ($allmods as $mod) {
                $modname = $mod->name;
                //Now check that we have that module info in the backup file
                if (isset($SESSION->info->mods[$modname]) && $SESSION->info->mods[$modname]->backup == "true") {
                    $SESSION->restore->mods[$modname]->restore = true;
                    $SESSION->restore->mods[$modname]->userinfo = $userdata;
                }
                else {
                    // avoid warnings
                    $SESSION->restore->mods[$modname]->restore = false;
                    $SESSION->restore->mods[$modname]->userinfo = false;
                }
            }
        }
        $restore = clone($SESSION->restore);
        if (!restore_execute($SESSION->restore,$SESSION->info,$SESSION->course_header,$errorstr)) {
            mtrace($debuginfo.'Failed restore_execute (error was '.$errorstr.')');
            return false;
        }
        return true;
    }

    /**
    * Function to backup an entire course silently and create a zipfile.
    *
    * @param int $courseid the id of the course
    * @param array $prefs see {@link backup_generate_preferences_artificially}
    */
    function backup_course_silently($courseid, $prefs, &$errorstring) {
        global $CFG, $preferences; // global preferences here because something else wants it :(
        define('BACKUP_SILENTLY', 1);
        if (!$course = get_record('course', 'id', $courseid)) {
            debugging("Couldn't find course with id $courseid in backup_course_silently");
            return false;
        }
        $preferences = backup_generate_preferences_artificially($course, $prefs);
        if (backup_execute($preferences, $errorstring)) {
            return $CFG->dataroot . '/' . $course->id . '/backupdata/' . $preferences->backup_name;
        }
        else {
            return false;
        }
    }

    /**
    * Function to generate the $preferences variable that
    * backup uses.  This will back up all modules and instances in a course.
    *
    * @param object $course course object
    * @param array $prefs can contain:
            backup_metacourse
            backup_users
            backup_logs
            backup_user_files
            backup_course_files
            backup_site_files
            backup_messages
    * and if not provided, they will not be included.
    */

    function backup_generate_preferences_artificially($course, $prefs) {
        global $CFG;
        $preferences = new StdClass;
        $preferences->backup_unique_code = time();
        $preferences->backup_name = backup_get_zipfile_name($course, $preferences->backup_unique_code);
        $count = 0;

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
                    $preferences->$var = true;
                    $preferences->mods[$modname]->instances[$instance->id]->backup = true;
                    $var = 'backup_user_info_'.$modname.'_instance_'.$instance->id;
                    $preferences->$var = true;
                    $preferences->mods[$modname]->instances[$instance->id]->userinfo = true;
                    $var = 'backup_'.$modname.'_instances';
                    $preferences->$var = 1; // we need this later to determine what to display in modcheckbackup.
                }

                //Check data
                //Check module info
                $preferences->mods[$modname]->name = $modname;

                $var = "backup_".$modname;
                $preferences->$var = true;
                $preferences->mods[$modname]->backup = true;

                //Check include user info
                $var = "backup_user_info_".$modname;
                $preferences->$var = true;
                $preferences->mods[$modname]->userinfo = true;

            }
        }

        //Check other parameters
        $preferences->backup_metacourse = (isset($prefs['backup_metacourse']) ? $prefs['backup_metacourse'] : 0);
        $preferences->backup_users = (isset($prefs['backup_users']) ? $prefs['backup_users'] : 0);
        $preferences->backup_logs = (isset($prefs['backup_logs']) ? $prefs['backup_logs'] : 0);
        $preferences->backup_user_files = (isset($prefs['backup_user_files']) ? $prefs['backup_user_files'] : 0);
        $preferences->backup_course_files = (isset($prefs['backup_course_files']) ? $prefs['backup_course_files'] : 0);
        $preferences->backup_site_files = (isset($prefs['backup_site_files']) ? $prefs['backup_site_files'] : 0);
        $preferences->backup_messages = (isset($prefs['backup_messages']) ? $prefs['backup_messages'] : 0);
        $preferences->backup_course = $course->id;
        backup_add_static_preferences($preferences);
        return $preferences;
    }


?>
