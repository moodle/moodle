<?php

require_once($CFG->dirroot.'/lib/dmllib.php');
require_once($CFG->dirroot.'/backup/lib.php');
require_once($CFG->dirroot.'/backup/backuplib.php');

define('BACKUP_SILENTLY', true);

class copier_backup {

    // unique backup variables
    var $backupid;
    var $backup_from;
    var $userdata;
    var $result;
    var $backup_unique_code;

    // standard variables
    var $shared_backup_directory = "/local/moodlecopier";

    function __construct($backup_from, $backupid, $userdata) {
        $this->backup_from = $backup_from;
        $this->backup_unique_code = time();
        $this->backupid = $backupid;
        $this->userdata = $userdata;

        $this->result = array(
            'result'    => false,
            'warnings'  => array(),
            'errors'    => false,
        );
    }

    function execute() {
        global $CFG;

        $CFG->course_copier_bucket = $this->shared_backup_directory;

        try {
            execute_sql("SET SESSION wait_timeout = 7200");

            // Remove PHP Execution Time Limit
            set_time_limit(0);

            // Increase PHP Memory Limit
            raise_memory_limit('192M');

            // pull the course for backup
            $course = get_record('course','id',$this->backup_from);
            if (empty($course)) {
                throw new Exception('No course found for id');
            }

            // Load up backup preferences from moodle (functions in this class)
            $preferences = new StdClass;
            $this->backup_fetch_prefs($preferences, $course, $this->backup_unique_code, $this->backupid, $this->userdata);

            $errorstr = '';
            // only chance for result = true
            $this->result['result'] = $this->backup_execute($preferences, $errorstr);
            $this->result['errors'] = json_encode( array( "error" => $errorstr) );

        } catch (exception $e) {
            // fatal error - hide the sad panda.
            $this->result['errors'] = json_encode( array( "error" => "Exception occurred: ".$e->getMessage() ) );
        }

        return $this->result;
    }

    function backup_fetch_prefs(&$preferences, $course, $backup_unique_code, $backupid, $backup_user_data = false) {
        global $CFG;

        $preferences->backup_folder = 'course_backup_'.$backupid;

        // set by moodle's function backup_add_static_preferences
        $preferences->moodle_version = $CFG->version;
        $preferences->moodle_release = $CFG->release;
        $preferences->backup_version = $CFG->backup_version;
        $preferences->backup_release = $CFG->backup_release;

        ($backup_user_data) ? $backup_user_data = 1 : $backup_user_data = 0;

        // pull a list of all mods within moodle
        if ($allmods = get_records("modules") ) {
            // loop through mods to create possible backup prefrences
            foreach ($allmods as $mod) {

                $modname = $mod->name;
                $modfile = "$CFG->dirroot/mod/$modname/backuplib.php";
                $modbackup = $modname."_backup_mods";
                $modbackupone = $modname."_backup_one_mod";
                $modcheckbackup = $modname."_check_backup_mods";

                // load backup lib for the particular module, if exists
                if (!file_exists($modfile)) {
                    continue;
                }
                include_once($modfile);

                // verify that the specific functions needed exist within the backup lib
                if (!function_exists($modbackup) || !function_exists($modcheckbackup)) {
                    continue;
                }

                // within preferences, show that this module exists and will backup
                $var = "exists_".$modname;
                $preferences->$var = true;

                // check that there are instances and we can back them up individually
                if (!count_records('course_modules','course',$course->id,'module',$mod->id) || !function_exists($modbackupone)) {
                    continue;
                }

                // within preferences, show that this module can backup inidividual instances
                $var = 'exists_one_'.$modname;
                $preferences->$var = true;

                // add all instances to preferences
                $varname = $modname.'_instances';
                $preferences->$varname = get_all_instances_in_course($modname, $course, NULL, true);

                // loop through each instance to set preferences
                foreach ($preferences->$varname as $instance) {

                    $preferences->mods[$modname]->instances[$instance->id]->name = $instance->name;

                    // mark the instance for backup
                    $var = 'backup_'.$modname.'_instance_'.$instance->id;
                    $$var = 1;
                    $preferences->$var = $$var;
                    $preferences->mods[$modname]->instances[$instance->id]->backup = $$var;

                    // mark the user data to NOT backup
                    // TODO: Later add the option to include user data for projectspace->projectspace transfers
                    $var = 'backup_user_info_'.$modname.'_instance_'.$instance->id;
                    $$var = $backup_user_data;
                    $preferences->$var = $$var;
                    $preferences->mods[$modname]->instances[$instance->id]->userinfo = $$var;

                    // mark the mod within preferences that there are mod instances (we know because we just finished one)
                    $var = 'backup_'.$modname.'_instances';
                    $preferences->$var = 1; // we need this later to determine what to display in modcheckbackup.
                }

                //Check data
                //Check module info
                $preferences->mods[$modname]->name = $modname;

                $var = "backup_".$modname;
                $$var = 1;
                $preferences->$var = $$var;
                $preferences->mods[$modname]->backup = $$var;

                //Check include user info
                $var = "backup_user_info_".$modname;
                $$var = $backup_user_data;
                $preferences->$var = $$var;
                $preferences->mods[$modname]->userinfo = $$var;

            }
        }

        //Check other parameters
        $preferences->backup_metacourse = 0;
        $preferences->backup_users = $backup_user_data;
        $preferences->backup_logs = 0;
        $preferences->backup_user_files = $backup_user_data;
        $preferences->backup_course_files = 1;
        $preferences->backup_gradebook_history = $backup_user_data;
        $preferences->backup_site_files = 1;
        $preferences->backup_messages = 0;
        $preferences->backup_blogs = 0;
        $preferences->backup_course = $course->id;
        $preferences->backup_name = $backup_unique_code;
        $preferences->backup_unique_code =  $backup_unique_code;

        $roles = get_records('role', '', '', 'sortorder');
        $preferences->backuproleassignments = array();
        foreach ($roles as $role) {
            if ($backup_user_data) {
                $preferences->backuproleassignments[$role->id] = $role;
            }
        }

        // Let's sure we are as out of order as moodle is and do the backup 'checks'


        if ($allmods = get_records("modules") ) {

            foreach ($allmods as $mod) {
                $modname = $mod->name;
                $modfile = $CFG->dirroot.'/mod/'.$modname.'/backuplib.php';
                if (!file_exists($modfile)) {
                    continue;
                }
                require_once($modfile);
                $modbackup = $modname."_backup_mods";
                //If exists the lib & function
                $var = "exists_".$modname;
                if (isset($preferences->$var) && $preferences->$var) {
                    $var = "backup_".$modname;
                    //Only if selected
                    if (!empty($preferences->$var) and ($preferences->$var == 1)) {
                        $var = "backup_user_info_".$modname;
                        //Call the check function to show more info
                        $modcheckbackup = $modname."_check_backup_mods";
                        $var = $modname.'_instances';
                        $instancestopass = array();
                        if (!empty($preferences->$var) && is_array($preferences->$var) && count($preferences->$var)) {
                            $table->data = array();
                            $countinstances = 0;
                            foreach ($preferences->$var as $instance) {
                                $var1 = 'backup_'.$modname.'_instance_'.$instance->id;
                                $var2 = 'backup_user_info_'.$modname.'_instance_'.$instance->id;
                                if (!empty($preferences->$var1)) {
                                    $obj = new StdClass;
                                    $obj->name = $instance->name;
                                    $obj->userdata = $preferences->$var2;
                                    $obj->id = $instance->id;
                                    $instancestopass[$instance->id]= $obj;
                                    $countinstances++;

                                }
                            }
                        }
                        $modcheckbackup($course->id,$preferences->$var,$preferences->backup_unique_code,$instancestopass);

                    }
                }
            }

        }

        //Now print the Logs tr conditionally
        if ($preferences->backup_logs && empty($to)) {
            log_check_backup($course->id);
        }

        //Now print the User Files tr conditionally
        if ($preferences->backup_user_files) {
            user_files_check_backup($course->id,$preferences->backup_unique_code);
        }

        //Now print the Course Files tr conditionally
        if ($preferences->backup_course_files) {
            course_files_check_backup($course->id,$preferences->backup_unique_code);
        }

        //Now print the site Files tr conditionally
        if ($preferences->backup_site_files) {
            site_files_check_backup($course->id,$preferences->backup_unique_code);
        }

    }

    function alter_name_paths($preferences) {
	global $CFG;
        $file = $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/moodle.xml";
        $backup_contents = file_get_contents($file);

        $backup_contents = str_replace( "<NAME>lib_courseviews</NAME>", "<NAME>library_course_tools</NAME>", $backup_contents);
        $backup_contents = str_replace( "<NAME>lib_reserves</NAME>", "<NAME>library_reserves</NAME>", $backup_contents);

        file_put_contents($file, $backup_contents);

	return true;
    }

    function backup_execute(&$_preferences, &$errorstr) {
        global $CFG, $preferences;
        $preferences = $_preferences;
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

        if ($status) {
            if(!defined('BACKUP_SILENTLY')) {
                echo "<li>Adjusting moodle.xml block names for upgrade.</li>";
            }
            if (!$status = $this->alter_name_paths($preferences)) {
                if (!defined('BACKUP_SILENTLY')) {
                    notify("An error occurred while adjusting moodle.xml for upgrade");
                }
                else {
                    $errorstr = "An error occurred while adjusting moodle.xml for upgrade";
                    return false;
                }
            }
        }

        // copy to course copier bucket
        if ($status) {
            if(!defined('BACKUP_SILENTLY')) {
                echo "<li>Copying backup to course copier bucket</li>";
            }
            $from = $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/";
            $to = $CFG->course_copier_bucket."/backup/".$preferences->backup_folder.'/';
            if (!$status = backup_copy_dir($from, $to)) {
                if (!defined('BACKUP_SILENTLY')) {
                    notify("An error occurred while copying files to bucket");
                }
                else {
                    $errorstr = "An error occurred while copying files to bucket";
                    return false;
                }
            }
        }

        return $status;
    }
}
