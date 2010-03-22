<?php //$Id$
    //Functions used in restore

    require_once($CFG->libdir.'/gradelib.php');

/**
 * Group backup/restore constants, 0.
 */
define('RESTORE_GROUPS_NONE', 0);

/**
 * Group backup/restore constants, 1.
 */
define('RESTORE_GROUPS_ONLY', 1);

/**
 * Group backup/restore constants, 2.
 */
define('RESTORE_GROUPINGS_ONLY', 2);

/**
 * Group backup/restore constants, course/all.
 */
define('RESTORE_GROUPS_GROUPINGS', 3);

    //This function unzips a zip file in the same directory that it is
    //It automatically uses pclzip or command line unzip
    function restore_unzip ($file) {

        return unzip_file($file, '', false);

    }

    //This function checks if moodle.xml seems to be a valid xml file
    //(exists, has an xml header and a course main tag
    function restore_check_moodle_file ($file) {

        $status = true;

        //Check if it exists
        if ($status = is_file($file)) {
            //Open it and read the first 200 bytes (chars)
            $handle = fopen ($file, "r");
            $first_chars = fread($handle,200);
            $status = fclose ($handle);
            //Chek if it has the requires strings
            if ($status) {
                $status = strpos($first_chars,"<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
                if ($status !== false) {
                    $status = strpos($first_chars,"<MOODLE_BACKUP>");
                }
            }
        }

        return $status;
    }

    //This function iterates over all modules in backup file, searching for a
    //MODNAME_refresh_events() to execute. Perhaps it should ve moved to central Moodle...
    function restore_refresh_events($restore) {

        global $CFG;
        $status = true;

        //Take all modules in backup
        $modules = $restore->mods;
        //Iterate
        foreach($modules as $name => $module) {
            //Only if the module is being restored
            if (isset($module->restore) && $module->restore == 1) {
                //Include module library
                include_once("$CFG->dirroot/mod/$name/lib.php");
                //If module_refresh_events exists
                $function_name = $name."_refresh_events";
                if (function_exists($function_name)) {
                    $status = $function_name($restore->course_id);
                }
            }
        }
        return $status;
    }

    //This function makes all the necessary calls to xxxx_decode_content_links_caller()
    //function in each module/block/course format..., passing them the desired contents to be decoded
    //from backup format to destination site/course in order to mantain inter-activities
    //working in the backup/restore process
    function restore_decode_content_links($restore) {
        global $CFG;

        $status = true;

        if (!defined('RESTORE_SILENTLY')) {
            echo "<ul>";
        }

        // Recode links in the course summary.
        if (!defined('RESTORE_SILENTLY')) {
            echo '<li>' . get_string('from') . ' ' . get_string('course');
        }
        $course = get_record('course', 'id', $restore->course_id, '', '', '', '', 'id,summary');
        $coursesummary = backup_todb($course->summary,false); // Exception: Process FILEPHP (not available when restored) MDL-18222
        $coursesummary = restore_decode_content_links_worker($coursesummary, $restore);
        if ($coursesummary != $course->summary) {
            $course->summary = addslashes($coursesummary);
            if (!update_record('course', $course)) {
                $status = false;
            }
        }
        if (!defined('RESTORE_SILENTLY')) {
            echo '</li>';
        }

        // Recode links in section summaries.
        $sections = get_records('course_sections', 'course', $restore->course_id, 'id', 'id,summary');
        if ($sections) {
            if (!defined('RESTORE_SILENTLY')) {
                echo '<li>' . get_string('from') . ' ' . get_string('sections');
            }
            foreach ($sections as $section) {
                $sectionsummary = restore_decode_content_links_worker($section->summary, $restore);
                if ($sectionsummary != $section->summary) {
                    $section->summary = addslashes($sectionsummary);
                    if (!update_record('course_sections', $section)) {
                        $status = false;
                    }
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        // Restore links in modules.
        foreach ($restore->mods as $name => $info) {
            //If the module is being restored
            if (isset($info->restore) && $info->restore == 1) {
                //Check if the xxxx_decode_content_links_caller exists
                include_once("$CFG->dirroot/mod/$name/restorelib.php");
                $function_name = $name."_decode_content_links_caller";
                if (function_exists($function_name)) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo "<li>".get_string ("from")." ".get_string("modulenameplural",$name);
                    }
                    $status = $function_name($restore) && $status;
                    if (!defined('RESTORE_SILENTLY')) {
                        echo '</li>';
                    }
                }
            }
        }

        // For the course format call its decode_content_links method (if it exists)
        $format = get_field('course', 'format', 'id', $restore->course_id);
        if (file_exists("$CFG->dirroot/course/format/$format/restorelib.php")) {
            include_once("$CFG->dirroot/course/format/$format/restorelib.php");
            $function_name = $format.'_decode_format_content_links_caller';

            if (function_exists($function_name)) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>".get_string ("from")." ".get_string("format").' '.$format;
                }
                $status = $function_name($restore);
                if (!defined('RESTORE_SILENTLY')) {
                    echo '</li>';
                }
            }
        }

        // Process all html text also in blocks too
        if (!defined('RESTORE_SILENTLY')) {
            echo '<li>'.get_string ('from').' '.get_string('blocks');
        }

        if ($blocks = get_records('block', 'visible', 1)) {
            foreach ($blocks as $block) {
                if ($blockobject = block_instance($block->name)) {
                    $blockobject->decode_content_links_caller($restore);
                }
            }
        }

        if (!defined('RESTORE_SILENTLY')) {
            echo '</li>';
        }

        // Restore links in questions.
        require_once("$CFG->dirroot/question/restorelib.php");
        if (!defined('RESTORE_SILENTLY')) {
            echo '<li>' . get_string('from') . ' ' . get_string('questions', 'quiz');
        }
        $status = question_decode_content_links_caller($restore) && $status;
        if (!defined('RESTORE_SILENTLY')) {
            echo '</li>';
        }

        if (!defined('RESTORE_SILENTLY')) {
            echo "</ul>";
        }

        return $status;
    }

    //This function is called from all xxxx_decode_content_links_caller(),
    //its task is to ask all modules (maybe other linkable objects) to restore
    //links to them.
    function restore_decode_content_links_worker($content,$restore) {
        global $CFG;
        foreach($restore->mods as $name => $info) {
            $function_name = $name."_decode_content_links";
            if (function_exists($function_name)) {
                $content = $function_name($content,$restore);
            }
        }

        // For the current format, call decode_format_content_links if it exists
        static $format_function_name;
        if (!isset($format_function_name)) {
            $format_function_name = false;
            if ($format = get_field('course', 'format', 'id', $restore->course_id)) {
                if (file_exists("$CFG->dirroot/course/format/$format/restorelib.php")) {
                    include_once("$CFG->dirroot/course/format/$format/restorelib.php");
                    $function_name = $format.'_decode_format_content_links';
                    if (function_exists($function_name)) {
                        $format_function_name = $function_name;
                    }
                }
            }
        }
        // If the above worked - then we have a function to call
        if ($format_function_name) {
            $content = $format_function_name($content, $restore);
        }

        // For each block, call its encode_content_links method
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
            $content = $blockobject->decode_content_links($content,$restore); 
        }

        return $content;
    }

    //This function converts all the wiki texts in the restored course
    //to the Markdown format. Used only for backup files prior 2005041100.
    //It calls every module xxxx_convert_wiki2markdown function
    function restore_convert_wiki2markdown($restore) {

        $status = true;

        if (!defined('RESTORE_SILENTLY')) {
            echo "<ul>";
        }
        foreach ($restore->mods as $name => $info) {
            //If the module is being restored
            if ($info->restore == 1) {
                //Check if the xxxx_restore_wiki2markdown exists
                $function_name = $name."_restore_wiki2markdown";
                if (function_exists($function_name)) {
                    $status = $function_name($restore);
                    if (!defined('RESTORE_SILENTLY')) {
                        echo "<li>".get_string("modulenameplural",$name);
                        echo '</li>';
                    }
                }
            }
        }
        if (!defined('RESTORE_SILENTLY')) {
            echo "</ul>";
        }
        return $status;
    }

    //This function receives a wiki text in the restore process and
    //return it with every link to modules " modulename:moduleid"
    //converted if possible. See the space before modulename!!
    function restore_decode_wiki_content($content,$restore) {

        global $CFG;

        $result = $content;

        $searchstring='/ ([a-zA-Z]+):([0-9]+)\(([^)]+)\)/';
        //We look for it
        preg_match_all($searchstring,$content,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //print_object($foundset);                                     //Debug
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (course id)
                $rec = backup_getid($restore->backup_unique_code,"course_modules",$old_id);
                //Personalize the searchstring
                $searchstring='/ ([a-zA-Z]+):'.$old_id.'\(([^)]+)\)/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,' $1:'.$rec->new_id.'($2)',$result);
                } else {
                    //It's a foreign link so redirect it to its original URL
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/$1/view.php?id='.$old_id.'($2)',$result);
                }
            }
        }
        return $result;
    }


    //This function read the xml file and store it data from the info zone in an object
    function restore_read_xml_info ($xml_file) {

        //We call the main read_xml function, with todo = INFO
        $info = restore_read_xml ($xml_file,"INFO",false);

        return $info;
    }

    //This function read the xml file and store it data from the course header zone in an object
    function restore_read_xml_course_header ($xml_file) {

        //We call the main read_xml function, with todo = COURSE_HEADER
        $info = restore_read_xml ($xml_file,"COURSE_HEADER",false);

        return $info;
    }

    //This function read the xml file and store its data from the blocks in a object
    function restore_read_xml_blocks ($restore, $xml_file) {

        //We call the main read_xml function, with todo = BLOCKS
        $info = restore_read_xml ($xml_file,'BLOCKS',$restore);

        return $info;
    }

    //This function read the xml file and store its data from the sections in a object
    function restore_read_xml_sections ($xml_file) {

        //We call the main read_xml function, with todo = SECTIONS
        $info = restore_read_xml ($xml_file,"SECTIONS",false);

        return $info;
    }

    //This function read the xml file and store its data from the course format in an object
    function restore_read_xml_formatdata ($xml_file) {

        //We call the main read_xml function, with todo = FORMATDATA
        $info = restore_read_xml ($xml_file,'FORMATDATA',false);

        return $info;
    }

    //This function read the xml file and store its data from the metacourse in a object
    function restore_read_xml_metacourse ($xml_file) {

        //We call the main read_xml function, with todo = METACOURSE
        $info = restore_read_xml ($xml_file,"METACOURSE",false);

        return $info;
    }

    //This function read the xml file and store its data from the gradebook in a object
    function restore_read_xml_gradebook ($restore, $xml_file) {

        //We call the main read_xml function, with todo = GRADEBOOK
        $info = restore_read_xml ($xml_file,"GRADEBOOK",$restore);

        return $info;
    }

    //This function read the xml file and store its data from the users in
    //backup_ids->info db (and user's id in $info)
    function restore_read_xml_users ($restore,$xml_file) {

        //We call the main read_xml function, with todo = USERS
        $info = restore_read_xml ($xml_file,"USERS",$restore);

        return $info;
    }

    //This function read the xml file and store its data from the messages in
    //backup_ids->message backup_ids->message_read and backup_ids->contact and db (and their counters in info)
    function restore_read_xml_messages ($restore,$xml_file) {

        //We call the main read_xml function, with todo = MESSAGES
        $info = restore_read_xml ($xml_file,"MESSAGES",$restore);

        return $info;
    }

    //This function read the xml file and store its data from the blogs in
    //backup_ids->blog and backup_ids->blog_tag and db (and their counters in info)
    function restore_read_xml_blogs ($restore,$xml_file) {

        //We call the main read_xml function, with todo = BLOGS
        $info = restore_read_xml ($xml_file,"BLOGS",$restore);

        return $info;
    }


    //This function read the xml file and store its data from the questions in
    //backup_ids->info db (and category's id in $info)
    function restore_read_xml_questions ($restore,$xml_file) {

        //We call the main read_xml function, with todo = QUESTIONS
        $info = restore_read_xml ($xml_file,"QUESTIONS",$restore);

        return $info;
    }

    //This function read the xml file and store its data from the scales in
    //backup_ids->info db (and scale's id in $info)
    function restore_read_xml_scales ($restore,$xml_file) {

        //We call the main read_xml function, with todo = SCALES
        $info = restore_read_xml ($xml_file,"SCALES",$restore);

        return $info;
    }

    //This function read the xml file and store its data from the groups in
    //backup_ids->info db (and group's id in $info)
    function restore_read_xml_groups ($restore,$xml_file) {

        //We call the main read_xml function, with todo = GROUPS
        $info = restore_read_xml ($xml_file,"GROUPS",$restore);

        return $info;
    }

    //This function read the xml file and store its data from the groupings in
    //backup_ids->info db (and grouping's id in $info)
    function restore_read_xml_groupings ($restore,$xml_file) {

        //We call the main read_xml function, with todo = GROUPINGS
        $info = restore_read_xml ($xml_file,"GROUPINGS",$restore);

        return $info;
    }

    //This function read the xml file and store its data from the groupings in
    //backup_ids->info db (and grouping's id in $info)
    function restore_read_xml_groupings_groups ($restore,$xml_file) {

        //We call the main read_xml function, with todo = GROUPINGS
        $info = restore_read_xml ($xml_file,"GROUPINGSGROUPS",$restore);

        return $info;
    }

    //This function read the xml file and store its data from the events (course) in
    //backup_ids->info db (and event's id in $info)
    function restore_read_xml_events ($restore,$xml_file) {

        //We call the main read_xml function, with todo = EVENTS
        $info = restore_read_xml ($xml_file,"EVENTS",$restore);

        return $info;
    }

    //This function read the xml file and store its data from the modules in
    //backup_ids->info
    function restore_read_xml_modules ($restore,$xml_file) {

        //We call the main read_xml function, with todo = MODULES
        $info = restore_read_xml ($xml_file,"MODULES",$restore);

        return $info;
    }

    //This function read the xml file and store its data from the logs in
    //backup_ids->info
    function restore_read_xml_logs ($restore,$xml_file) {

        //We call the main read_xml function, with todo = LOGS
        $info = restore_read_xml ($xml_file,"LOGS",$restore);

        return $info;
    }

    function restore_read_xml_roles ($xml_file) {
        //We call the main read_xml function, with todo = ROLES
        $info = restore_read_xml ($xml_file,"ROLES",false);

        return $info;
    }

    //This function prints the contents from the info parammeter passed
    function restore_print_info ($info) {

        global $CFG;

        $status = true;
        if ($info) {
            $table = new object();
            //This is tha align to every ingo table
            $table->align = array ("right","left");
            //This is the nowrap clause
            $table->wrap = array ("","nowrap");
            //The width
            $table->width = "70%";
            //Put interesting info in table
            //The backup original name
            $tab[0][0] = "<b>".get_string("backuporiginalname").":</b>";
            $tab[0][1] = $info->backup_name;
            //The moodle version
            $tab[1][0] = "<b>".get_string("moodleversion").":</b>";
            $tab[1][1] = $info->backup_moodle_release." (".$info->backup_moodle_version.")";
            //The backup version
            $tab[2][0] = "<b>".get_string("backupversion").":</b>";
            $tab[2][1] = $info->backup_backup_release." (".$info->backup_backup_version.")";
            //The backup date
            $tab[3][0] = "<b>".get_string("backupdate").":</b>";
            $tab[3][1] = userdate($info->backup_date);
            //Is this the same Moodle install?
            if (!empty($info->original_siteidentifier)) {
                $tab[4][0] = "<b>".get_string("backupfromthissite").":</b>";
                if (backup_is_same_site($info)) {
                    $tab[4][1] = get_string('yes');
                } else {
                    $tab[4][1] = get_string('no');
                }
            }
            //Print title
            print_heading(get_string("backup").":");
            $table->data = $tab;
            //Print backup general info
            print_table($table);

            if ($info->backup_backup_version <= 2005070500) {
                 notify(get_string('backupnonisowarning'));  // Message informing that this backup may not work!
            }

            //Now backup contents in another table
            $tab = array();
            //First mods info
            $mods = $info->mods;
            $elem = 0;
            foreach ($mods as $key => $mod) {
                $tab[$elem][0] = "<b>".get_string("modulenameplural",$key).":</b>";
                if ($mod->backup == "false") {
                    $tab[$elem][1] = get_string("notincluded");
                } else {
                    if ($mod->userinfo == "true") {
                        $tab[$elem][1] = get_string("included")." ".get_string("withuserdata");
                    } else {
                        $tab[$elem][1] = get_string("included")." ".get_string("withoutuserdata");
                    }
                    if (isset($mod->instances) && is_array($mod->instances) && count($mod->instances)) {
                        foreach ($mod->instances as $instance) {
                            if ($instance->backup) {
                                $elem++;
                                $tab[$elem][0] = $instance->name;
                                if ($instance->userinfo == 'true') {
                                    $tab[$elem][1] = get_string("included")." ".get_string("withuserdata");
                                } else {
                                    $tab[$elem][1] = get_string("included")." ".get_string("withoutuserdata");
                                }
                            }
                        }
                    }
                }
                $elem++;
            }
            //Metacourse info
            $tab[$elem][0] = "<b>".get_string("metacourse").":</b>";
            if ($info->backup_metacourse == "true") {
                $tab[$elem][1] = get_string("yes");
            } else {
                $tab[$elem][1] = get_string("no");
            }
            $elem++;
            //Users info
            $tab[$elem][0] = "<b>".get_string("users").":</b>";
            $tab[$elem][1] = get_string($info->backup_users);
            $elem++;
            //Logs info
            $tab[$elem][0] = "<b>".get_string("logs").":</b>";
            if ($info->backup_logs == "true") {
                $tab[$elem][1] = get_string("yes");
            } else {
                $tab[$elem][1] = get_string("no");
            }
            $elem++;
            //User Files info
            $tab[$elem][0] = "<b>".get_string("userfiles").":</b>";
            if ($info->backup_user_files == "true") {
                $tab[$elem][1] = get_string("yes");
            } else {
                $tab[$elem][1] = get_string("no");
            }
            $elem++;
            //Course Files info
            $tab[$elem][0] = "<b>".get_string("coursefiles").":</b>";
            if ($info->backup_course_files == "true") {
                $tab[$elem][1] = get_string("yes");
            } else {
                $tab[$elem][1] = get_string("no");
            }
            $elem++;
            //site Files info
            $tab[$elem][0] = "<b>".get_string("sitefiles").":</b>";
            if (isset($info->backup_site_files) && $info->backup_site_files == "true") {
                $tab[$elem][1] = get_string("yes");
            } else {
                $tab[$elem][1] = get_string("no");
            }
            $elem++;
            //gradebook history info
            $tab[$elem][0] = "<b>".get_string('gradebookhistories', 'grades').":</b>";
            if (isset($info->gradebook_histories) && $info->gradebook_histories == "true") {
                $tab[$elem][1] = get_string("yes");
            } else {
                $tab[$elem][1] = get_string("no");
            }
            $elem++;
            //Messages info (only showed if present)
            if ($info->backup_messages == 'true') {
                $tab[$elem][0] = "<b>".get_string('messages','message').":</b>";
                $tab[$elem][1] = get_string('yes');
                $elem++;
            } else {
                //Do nothing
            }
            $elem++;
            //Blogs info (only showed if present)
            if (isset($info->backup_blogs) && $info->backup_blogs == 'true') {
                $tab[$elem][0] = "<b>".get_string('blogs','blog').":</b>";
                $tab[$elem][1] = get_string('yes');
                $elem++;
            } else {
                //Do nothing
            }
            $table->data = $tab;
            //Print title
            print_heading(get_string("backupdetails").":");
            //Print backup general info
            print_table($table);
        } else {
            $status = false;
        }

        return $status;
    }

    //This function prints the contents from the course_header parammeter passed
    function restore_print_course_header ($course_header) {

        $status = true;
        if ($course_header) {
            $table = new object();
            //This is tha align to every ingo table
            $table->align = array ("right","left");
            //The width
            $table->width = "70%";
            //Put interesting course header in table
            //The course name
            $tab[0][0] = "<b>".get_string("name").":</b>";
            $tab[0][1] = $course_header->course_fullname." (".$course_header->course_shortname.")";
            //The course summary
            $tab[1][0] = "<b>".get_string("summary").":</b>";
            $tab[1][1] = $course_header->course_summary;
            $table->data = $tab;
            //Print title
            print_heading(get_string("course").":");
            //Print backup course header info
            print_table($table);
        } else {
            $status = false;
        }
        return $status;
    }

   /**
    * Given one user object (from backup file), perform all the neccesary
    * checks is order to decide how that user will be handled on restore.
    *
    * Note the function requires $user->mnethostid to be already calculated
    * so it's caller responsibility to set it
    *
    * This function is used both by @restore_precheck_users() and
    * @restore_create_users() to get consistent results in both places
    *
    * It returns:
    *   - one user object (from DB), if match has been found and user will be remapped
    *   - boolean true if the user needs to be created
    *   - boolean false if some conflict happened and the user cannot be handled
    *
    * Each test is responsible for returning its results and interrupt
    * execution. At the end, boolean true (user needs to be created) will be
    * returned if no test has interrupted that.
    *
    * Here it's the logic applied, keep it updated:
    *
    *  If restoring users from same site backup:
    *      1A - Normal check: If match by id and username and mnethost  => ok, return target user
    *      1B - Handle users deleted in DB and "alive" in backup file:
    *           If match by id and mnethost and user is deleted in DB and
    *           (match by username LIKE 'backup_email.%' or by non empty email = md5(username)) => ok, return target user
    *      1C - Handle users deleted in backup file and "alive" in DB:
    *           If match by id and mnethost and user is deleted in backup file
    *           and match by email = email_without_time(backup_email) => ok, return target user
    *      1D - Conflict: If match by username and mnethost and doesn't match by id => conflict, return false
    *      1E - None of the above, return true => User needs to be created
    *
    *  if restoring from another site backup (cannot match by id here, replace it by email/firstaccess combination):
    *      2A - Normal check: If match by username and mnethost and (email or non-zero firstaccess) => ok, return target user
    *      2B - Handle users deleted in DB and "alive" in backup file:
    *           2B1 - If match by mnethost and user is deleted in DB and not empty email = md5(username) and
    *                 (username LIKE 'backup_email.%' or non-zero firstaccess) => ok, return target user
    *           2B2 - If match by mnethost and user is deleted in DB and
    *                 username LIKE 'backup_email.%' and non-zero firstaccess) => ok, return target user
    *                 (to cover situations were md5(username) wasn't implemented on delete we requiere both)
    *      2C - Handle users deleted in backup file and "alive" in DB:
    *           If match mnethost and user is deleted in backup file
    *           and by email = email_without_time(backup_email) and non-zero firstaccess=> ok, return target user
    *      2D - Conflict: If match by username and mnethost and not by (email or non-zero firstaccess) => conflict, return false
    *      2E - None of the above, return true => User needs to be created
    *
    * Note: for DB deleted users email is stored in username field, hence we
    *       are looking there for emails. See delete_user()
    * Note: for DB deleted users md5(username) is stored *sometimes* in the email field,
    *       hence we are looking there for usernames if not empty. See delete_user()
    */
    function restore_check_user($restore, $user) {
        global $CFG;

        // Verify mnethostid is set, return error if not
        // it's parent responsibility to define that before
        // arriving here
        if (empty($user->mnethostid)) {
            debugging("restore_check_user() wrong use, mnethostid not set for user $user->username", DEBUG_DEVELOPER);
            return false;
        }

        // Handle checks from same site backups
        if (backup_is_same_site($restore) && empty($CFG->forcedifferentsitecheckingusersonrestore)) {

            // 1A - If match by id and username and mnethost => ok, return target user
            if ($rec = get_record('user', 'id', $user->id, 'username', addslashes($user->username), 'mnethostid', $user->mnethostid)) {
                return $rec; // Matching user found, return it
            }

            // 1B - Handle users deleted in DB and "alive" in backup file
            // Note: for DB deleted users email is stored in username field, hence we
            //       are looking there for emails. See delete_user()
            // Note: for DB deleted users md5(username) is stored *sometimes* in the email field,
            //       hence we are looking there for usernames if not empty. See delete_user()
            // If match by id and mnethost and user is deleted in DB and
            // match by username LIKE 'backup_email.%' or by non empty email = md5(username) => ok, return target user
            if ($rec = get_record_sql("SELECT *
                                         FROM {$CFG->prefix}user u
                                        WHERE id = $user->id
                                          AND mnethostid = $user->mnethostid
                                          AND deleted = 1
                                          AND (
                                                  username LIKE '".addslashes($user->email).".%'
                                               OR (
                                                      ".sql_isnotempty('user', 'email', false, false)."
                                                  AND email = '".md5($user->username)."'
                                                  )
                                              )")) {
                return $rec; // Matching user, deleted in DB found, return it
            }

            // 1C - Handle users deleted in backup file and "alive" in DB
            // If match by id and mnethost and user is deleted in backup file
            // and match by email = email_without_time(backup_email) => ok, return target user
            if ($user->deleted) {
                // Note: for DB deleted users email is stored in username field, hence we
                //       are looking there for emails. See delete_user()
                // Trim time() from email
                $trimemail = preg_replace('/(.*?)\.[0-9]+.?$/', '\\1', $user->username);
                if ($rec = get_record_sql("SELECT *
                                             FROM {$CFG->prefix}user u
                                            WHERE id = $user->id
                                              AND mnethostid = $user->mnethostid
                                              AND email = '".addslashes($trimemail)."'")) {
                    return $rec; // Matching user, deleted in backup file found, return it
                }
            }

            // 1D - If match by username and mnethost and doesn't match by id => conflict, return false
            if ($rec = get_record('user', 'username', addslashes($user->username), 'mnethostid', $user->mnethostid)) {
                if ($user->id != $rec->id) {
                    return false; // Conflict, username already exists and belongs to another id
                }
            }

        // Handle checks from different site backups
        } else {

            // 2A - If match by username and mnethost and
            //     (email or non-zero firstaccess) => ok, return target user
            if ($rec = get_record_sql("SELECT *
                                         FROM {$CFG->prefix}user u
                                        WHERE username = '".addslashes($user->username)."'
                                          AND mnethostid = $user->mnethostid
                                          AND (
                                                  email = '".addslashes($user->email)."'
                                               OR (
                                                      firstaccess != 0
                                                  AND firstaccess = $user->firstaccess
                                                  )
                                              )")) {
                return $rec; // Matching user found, return it
            }

            // 2B - Handle users deleted in DB and "alive" in backup file
            // Note: for DB deleted users email is stored in username field, hence we
            //       are looking there for emails. See delete_user()
            // Note: for DB deleted users md5(username) is stored *sometimes* in the email field,
            //       hence we are looking there for usernames if not empty. See delete_user()
            // 2B1 - If match by mnethost and user is deleted in DB and not empty email = md5(username) and
            //       (by username LIKE 'backup_email.%' or non-zero firstaccess) => ok, return target user
            if ($rec = get_record_sql("SELECT *
                                         FROM {$CFG->prefix}user u
                                        WHERE mnethostid = $user->mnethostid
                                          AND deleted = 1
                                          AND ".sql_isnotempty('user', 'email', false, false)."
                                          AND email = '".md5($user->username)."'
                                          AND (
                                                  username LIKE '".addslashes($user->email).".%'
                                               OR (
                                                      firstaccess != 0
                                                  AND firstaccess = $user->firstaccess
                                                  )
                                              )")) {
                return $rec; // Matching user found, return it
            }

            // 2B2 - If match by mnethost and user is deleted in DB and
            //       username LIKE 'backup_email.%' and non-zero firstaccess) => ok, return target user
            //       (this covers situations where md5(username) wasn't being stored so we require both
            //        the email & non-zero firstaccess to match)
            if ($rec = get_record_sql("SELECT *
                                         FROM {$CFG->prefix}user u
                                        WHERE mnethostid = $user->mnethostid
                                          AND deleted = 1
                                          AND username LIKE '".addslashes($user->email).".%'
                                          AND firstaccess != 0
                                          AND firstaccess = $user->firstaccess")) {
                return $rec; // Matching user found, return it
            }

            // 2C - Handle users deleted in backup file and "alive" in DB
            // If match mnethost and user is deleted in backup file
            // and match by email = email_without_time(backup_email) and non-zero firstaccess=> ok, return target user
            if ($user->deleted) {
                // Note: for DB deleted users email is stored in username field, hence we
                //       are looking there for emails. See delete_user()
                // Trim time() from email
                $trimemail = preg_replace('/(.*?)\.[0-9]+.?$/', '\\1', $user->username);
                if ($rec = get_record_sql("SELECT *
                                             FROM {$CFG->prefix}user u
                                            WHERE mnethostid = $user->mnethostid
                                              AND email = '".addslashes($trimemail)."'
                                              AND firstaccess != 0
                                              AND firstaccess = $user->firstaccess")) {
                    return $rec; // Matching user, deleted in backup file found, return it
                }
            }

            // 2D - If match by username and mnethost and not by (email or non-zero firstaccess) => conflict, return false
            if ($rec = get_record_sql("SELECT *
                                         FROM {$CFG->prefix}user u
                                        WHERE username = '".addslashes($user->username)."'
                                          AND mnethostid = $user->mnethostid
                                      AND NOT (
                                                  email = '".addslashes($user->email)."'
                                               OR (
                                                      firstaccess != 0
                                                  AND firstaccess = $user->firstaccess
                                                  )
                                              )")) {
                return false; // Conflict, username/mnethostid already exist and belong to another user (by email/firstaccess)
            }
        }

        // Arrived here, return true as the user will need to be created and no
        // conflicts have been found in the logic above. This covers:
        // 1E - else => user needs to be created, return true
        // 2E - else => user needs to be created, return true
        return true;
    }

   /**
    * For all the users being restored, check if they are going to cause problems
    * before executing the restore process itself, detecting situations like:
    *   - conflicts preventing restore to continue - provided by @restore_check_user()
    *   - prevent creation of users if not allowed - check some global settings/caps
    */
    function restore_precheck_users($xml_file, $restore, &$problems) {
        global $CFG;

        $status = true; // Init $status

        // We aren't restoring users, nothing to check, allow continue
        if ($restore->users == 2) {
            return true;
        }

        // Get array of users from xml file and load them in backup_ids table
        if (!$info = restore_read_xml_users($restore,$xml_file)) {
            return true; // No users, nothing to check, allow continue
        }

        // We are going to map mnethostid, so load all the available ones
        $mnethosts = get_records('mnet_host', '', '', 'wwwroot', 'wwwroot, id');

        // Calculate the context we are going to use for capability checking
        if (!empty($restore->course_id)) { // Know the target (existing) course, check capabilities there
            $context = get_context_instance(CONTEXT_COURSE, $restore->course_id);
        } else if (!empty($restore->restore_restorecatto)) { // Know the category, check capabilities there
            $context = get_context_instance(CONTEXT_COURSECAT, $restore->restore_restorecatto);
        } else { // Last resort, check capabilities at system level
            $context = get_context_instance(CONTEXT_SYSTEM);
        }

        // Calculate if we have perms to create users, by checking:
        // to 'moodle/restore:createuser' and 'moodle/restore:userinfo'
        // and also observe $CFG->disableusercreationonrestore
        $cancreateuser = false;
        if (has_capability('moodle/restore:createuser', $context) and
            has_capability('moodle/restore:userinfo', $context) and
            empty($CFG->disableusercreationonrestore)) { // Can create users

            $cancreateuser = true;
        }

        // Iterate over all users, checking if they are likely to cause problems on restore
        $counter = 0;
        foreach ($info->users as $userid) {
            $rec = backup_getid($restore->backup_unique_code, 'user', $userid);
            $user = $rec->info;

            // Find the correct mnethostid for user before performing any further check
            if (empty($user->mnethosturl) || $user->mnethosturl === $CFG->wwwroot) {
                $user->mnethostid = $CFG->mnet_localhost_id;
            } else {
                // fast url-to-id lookups
                if (isset($mnethosts[$user->mnethosturl])) {
                    $user->mnethostid = $mnethosts[$user->mnethosturl]->id;
                } else {
                    $user->mnethostid = $CFG->mnet_localhost_id;
                }
            }

            // Calculate the best way to handle this user from backup file
            $usercheck = restore_check_user($restore, $user);

            if (is_object($usercheck)) { // No problem, we have found one user in DB to be mapped to
                // Annotate it, for later process by restore_create_users(). Set new_id to mapping user->id
                backup_putid($restore->backup_unique_code, 'user', $userid, $usercheck->id, $user);

            } else if ($usercheck === false) { // Found conflict, report it as problem
                $problems[] = get_string('restoreuserconflict', '', $user->username);
                $status = false;

            } else if ($usercheck === true) { // User needs to be created, check if we are able
                if ($cancreateuser) { // Can create user, annotate it, for later process by restore_create_users(). Set new_id to 0
                    backup_putid($restore->backup_unique_code, 'user', $userid, 0, $user);

                } else { // Cannot create user, report it as problem

                    $problems[] = get_string('restorecannotcreateuser', '', $user->username);
                    $status = false;
                }

            } else { // Shouldn't arrive here ever, something is for sure wrong in restore_check_user()
                if (!defined('RESTORE_SILENTLY')) {
                    notify('Unexpected error pre-checking user ' . s($user->username) . ' from backup file');
                    return false;
                }
            }

            // Do some output
            $counter++;
            if ($counter % 10 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if ($counter % 200 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }
        }

        return $status;
    }

    //This function create a new course record.
    //When finished, course_header contains the id of the new course
    function restore_create_new_course($restore,&$course_header) {

        global $CFG, $SESSION;

        $status = true;

        $fullname = $course_header->course_fullname;
        $shortname = $course_header->course_shortname;
        $currentfullname = "";
        $currentshortname = "";
        $counter = 0;
        //Iteratere while the name exists
        do {
            if ($counter) {
                $suffixfull = " ".get_string("copyasnoun")." ".$counter;
                $suffixshort = "_".$counter;
            } else {
                $suffixfull = "";
                $suffixshort = "";
            }
            $currentfullname = $fullname.$suffixfull;
            // Limit the size of shortname - database column accepts <= 100 chars
            $currentshortname = substr($shortname, 0, 100 - strlen($suffixshort)).$suffixshort;
            $coursefull  = get_record("course","fullname",addslashes($currentfullname));
            $courseshort = get_record("course","shortname",addslashes($currentshortname));
            $counter++;
        } while ($coursefull || $courseshort);

        //New name = currentname
        $course_header->course_fullname = $currentfullname;
        $course_header->course_shortname = $currentshortname;

        // first try to get it from restore
        if ($restore->restore_restorecatto) {
            $category = get_record('course_categories', 'id', $restore->restore_restorecatto);
        }

        // else we try to get it from the xml file
        //Now calculate the category
        if (empty($category)) {
            $category = get_record("course_categories","id",$course_header->category->id,
                                   "name",addslashes($course_header->category->name));
        }

        //If no exists, try by name only
        if (!$category) {
            $category = get_record("course_categories","name",addslashes($course_header->category->name));
        }

        //If no exists, get category id 1
        if (!$category) {
            $category = get_record("course_categories","id","1");
        }

        //If category 1 doesn'exists, lets create the course category (get it from backup file)
        if (!$category) {
            $ins_category = new object();
            $ins_category->name = addslashes($course_header->category->name);
            $ins_category->parent = 0;
            $ins_category->sortorder = 0;
            $ins_category->coursecount = 0;
            $ins_category->visible = 0;            //To avoid interferences with the rest of the site
            $ins_category->timemodified = time();
            $newid = insert_record("course_categories",$ins_category);
            $category->id = $newid;
            $category->name = $course_header->category->name;
        }
        //If exists, put new category id
        if ($category) {
            $course_header->category->id = $category->id;
            $course_header->category->name = $category->name;
        //Error, cannot locate category
        } else {
            $course_header->category->id = 0;
            $course_header->category->name = get_string("unknowncategory");
            $status = false;
        }

        //Create the course_object
        if ($status) {
            $course = new object();
            $course->category = addslashes($course_header->category->id);
            $course->password = addslashes($course_header->course_password);
            $course->fullname = addslashes($course_header->course_fullname);
            $course->shortname = addslashes($course_header->course_shortname);
            $course->idnumber = addslashes($course_header->course_idnumber);
            $course->idnumber = ''; //addslashes($course_header->course_idnumber); // we don't want this at all.
            $course->summary = addslashes($course_header->course_summary);
            $course->format = addslashes($course_header->course_format);
            $course->showgrades = addslashes($course_header->course_showgrades);
            $course->newsitems = addslashes($course_header->course_newsitems);
            $course->teacher = addslashes($course_header->course_teacher);
            $course->teachers = addslashes($course_header->course_teachers);
            $course->student = addslashes($course_header->course_student);
            $course->students = addslashes($course_header->course_students);
            $course->guest = addslashes($course_header->course_guest);
            $course->startdate = addslashes($course_header->course_startdate);
            $course->startdate += $restore->course_startdateoffset;
            $course->numsections = addslashes($course_header->course_numsections);
            //$course->showrecent = addslashes($course_header->course_showrecent);   INFO: This is out in 1.3
            $course->maxbytes = addslashes($course_header->course_maxbytes);
            $course->showreports = addslashes($course_header->course_showreports);
            if (isset($course_header->course_groupmode)) {
                $course->groupmode = addslashes($course_header->course_groupmode);
            }
            if (isset($course_header->course_groupmodeforce)) {
                $course->groupmodeforce = addslashes($course_header->course_groupmodeforce);
            }
            if (isset($course_header->course_defaultgroupingid)) {
                //keep the original now - convert after groupings restored
                $course->defaultgroupingid = addslashes($course_header->course_defaultgroupingid);
            }
            $course->lang = addslashes($course_header->course_lang);
            $course->theme = addslashes($course_header->course_theme);
            $course->cost = addslashes($course_header->course_cost);
            $course->currency = isset($course_header->course_currency)?addslashes($course_header->course_currency):'';
            $course->marker = addslashes($course_header->course_marker);
            $course->visible = addslashes($course_header->course_visible);
            $course->hiddensections = addslashes($course_header->course_hiddensections);
            $course->timecreated = addslashes($course_header->course_timecreated);
            $course->timemodified = addslashes($course_header->course_timemodified);
            $course->metacourse = addslashes($course_header->course_metacourse);
            $course->expirynotify = isset($course_header->course_expirynotify) ? addslashes($course_header->course_expirynotify):0;
            $course->notifystudents = isset($course_header->course_notifystudents) ? addslashes($course_header->course_notifystudents) : 0;
            $course->expirythreshold = isset($course_header->course_expirythreshold) ? addslashes($course_header->course_expirythreshold) : 0;
            $course->enrollable = isset($course_header->course_enrollable) ? addslashes($course_header->course_enrollable) : 1;
            $course->enrolstartdate = isset($course_header->course_enrolstartdate) ? addslashes($course_header->course_enrolstartdate) : 0;
            if ($course->enrolstartdate)  { //Roll course dates
                $course->enrolstartdate += $restore->course_startdateoffset;
            }
            $course->enrolenddate = isset($course_header->course_enrolenddate) ? addslashes($course_header->course_enrolenddate) : 0;
            if ($course->enrolenddate) { //Roll course dates
                $course->enrolenddate  += $restore->course_startdateoffset;
            }
            $course->enrolperiod = addslashes($course_header->course_enrolperiod);
            //Calculate sortorder field
            $sortmax = get_record_sql('SELECT MAX(sortorder) AS max
                                       FROM ' . $CFG->prefix . 'course
                                       WHERE category=' . $course->category);
            if (!empty($sortmax->max)) {
                $course->sortorder = $sortmax->max + 1;
                unset($sortmax);
            } else {
                $course->sortorder = 100;
            }

            //Now, recode some languages (Moodle 1.5)
            if ($course->lang == 'ma_nt') {
                $course->lang = 'mi_nt';
            }

            //Disable course->metacourse if avoided in restore config
            if (!$restore->metacourse) {
                $course->metacourse = 0;
            }

            //Check if the theme exists in destination server
            $themes = get_list_of_themes();
            if (!in_array($course->theme, $themes)) {
                $course->theme = '';
            }

            //Now insert the record
            $newid = insert_record("course",$course);
            if ($newid) {
                //save old and new course id
                backup_putid ($restore->backup_unique_code,"course",$course_header->course_id,$newid);
                //Replace old course_id in course_header
                $course_header->course_id = $newid;
                $SESSION->restore->course_id = $newid;
                return $newid;
            } else {
                $status = false;
            }
        }

        return $status;
    }



    //This function creates all the block stuff when restoring courses
    //It calls selectively to  restore_create_block_instances() for 1.5
    //and above backups. Upwards compatible with old blocks.
    function restore_create_blocks($restore, $backup_block_format, $blockinfo, $xml_file) {
        global $CFG;
        $status = true;

        blocks_delete_all_on_page(PAGE_COURSE_VIEW, $restore->course_id);
        if (empty($backup_block_format)) {     // This is a backup from Moodle < 1.5
            if (empty($blockinfo)) {
                // Looks like it's from Moodle < 1.3. Let's give the course default blocks...
                $newpage = page_create_object(PAGE_COURSE_VIEW, $restore->course_id);
                blocks_repopulate_page($newpage);
            } else {
                // We just have a blockinfo field, this is a legacy 1.4 or 1.3 backup
                $blockrecords = get_records_select('block', '', '', 'name, id');
                $temp_blocks_l = array();
                $temp_blocks_r = array();
                @list($temp_blocks_l, $temp_blocks_r) = explode(':', $blockinfo);
                $temp_blocks = array(BLOCK_POS_LEFT => explode(',', $temp_blocks_l), BLOCK_POS_RIGHT => explode(',', $temp_blocks_r));
                foreach($temp_blocks as $blockposition => $blocks) {
                    $blockweight = 0;
                    foreach($blocks as $blockname) {
                        if(!isset($blockrecords[$blockname])) {
                            // We don't know anything about this block!
                            continue;
                        }
                        $blockinstance = new stdClass;
                        // Remove any - prefix before doing the name-to-id mapping
                        if(substr($blockname, 0, 1) == '-') {
                            $blockname = substr($blockname, 1);
                            $blockinstance->visible = 0;
                        } else {
                            $blockinstance->visible = 1;
                        }
                        $blockinstance->blockid  = $blockrecords[$blockname]->id;
                        $blockinstance->pageid   = $restore->course_id;
                        $blockinstance->pagetype = PAGE_COURSE_VIEW;
                        $blockinstance->position = $blockposition;
                        $blockinstance->weight   = $blockweight;
                        if(!$status = insert_record('block_instance', $blockinstance)) {
                            $status = false;
                        }
                        ++$blockweight;
                    }
                }
            }
        } else if($backup_block_format == 'instances') {
            $status = restore_create_block_instances($restore,$xml_file);
        }

        return $status;

    }

    //This function creates all the block_instances from xml when restoring in a
    //new course
    function restore_create_block_instances($restore,$xml_file) {
        global $CFG;
        $status = true;

        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            $info = restore_read_xml_blocks($restore,$xml_file);
        }

        if(empty($info->instances)) {
            return $status;
        }

        // First of all, iterate over the blocks to see which distinct pages we have
        // in our hands and arrange the blocks accordingly.
        $pageinstances = array();
        foreach($info->instances as $instance) {

            //pagetype and pageid black magic, we have to handle the case of blocks for the
            //course, blocks from other pages in that course etc etc etc.

            if($instance->pagetype == PAGE_COURSE_VIEW) {
                // This one's easy...
                $instance->pageid  = $restore->course_id;

            } else if (!empty($CFG->showblocksonmodpages)) {
                $parts = explode('-', $instance->pagetype);
                if($parts[0] == 'mod') {
                    if(!$restore->mods[$parts[1]]->restore) {
                        continue;
                    }
                    $getid = backup_getid($restore->backup_unique_code, $parts[1], $instance->pageid);

                    if (empty($getid->new_id)) {
                        // Failed, perhaps the module was not included in the restore  MDL-13554
                        continue;
                    }
                    $instance->pageid = $getid->new_id;
                }
                else {
                    // Not invented here ;-)
                    continue;
                }

            } else {
                // do not restore activity blocks if disabled
                continue;
            }

            if(!isset($pageinstances[$instance->pagetype])) {
                $pageinstances[$instance->pagetype] = array();
            }
            if(!isset($pageinstances[$instance->pagetype][$instance->pageid])) {
                $pageinstances[$instance->pagetype][$instance->pageid] = array();
            }

            $pageinstances[$instance->pagetype][$instance->pageid][] = $instance;
        }

        $blocks = get_records_select('block', 'visible = 1', '', 'name, id, multiple');

        // For each type of page we have restored
        foreach($pageinstances as $thistypeinstances) {

            // For each page id of that type
            foreach($thistypeinstances as $thisidinstances) {

                $addedblocks = array();
                $maxweights  = array();

                // For each block instance in that page
                foreach($thisidinstances as $instance) {

                    if(!isset($blocks[$instance->name])) {
                        //We are trying to restore a block we don't have...
                        continue;
                    }

                    //If we have already added this block once and multiples aren't allowed, disregard it
                    if(!$blocks[$instance->name]->multiple && isset($addedblocks[$instance->name])) {
                        continue;
                    }

                    //If its the first block we add to a new position, start weight counter equal to 0.
                    if(empty($maxweights[$instance->position])) {
                        $maxweights[$instance->position] = 0;
                    }

                    //If the instance weight is greater than the weight counter (we skipped some earlier
                    //blocks most probably), bring it back in line.
                    if($instance->weight > $maxweights[$instance->position]) {
                        $instance->weight = $maxweights[$instance->position];
                    }

                    //Add this instance
                    $instance->blockid = $blocks[$instance->name]->id;

                    // This will only be set if we come from 1.7 and above backups
                    //  Also, must do this before insert (insert_record unsets id)
                    if (!empty($instance->id)) { 
                        $oldid = $instance->id;
                    } else {
                        $oldid = 0;
                    }

                    if ($instance->id = insert_record('block_instance', $instance)) {
                        // Create block instance
                        if (!$blockobj = block_instance($instance->name, $instance)) {
                            $status = false;
                            break;
                        }
                        // Run the block restore if needed
                        if ($blockobj->backuprestore_instancedata_used()) {
                            // Get restore information
                            $data = backup_getid($restore->backup_unique_code,'block_instance',$oldid);
                            $data->new_id = $instance->id;  // For completeness
                            if (!$blockobj->instance_restore($restore, $data)) {
                                $status = false;
                                break;
                            }
                        }
                        // Save oldid after block restore process because info will be over-written with blank string
                        if ($oldid) {
                            backup_putid ($restore->backup_unique_code,"block_instance",$oldid,$instance->id);
                        }

                    } else {
                        $status = false;
                        break;
                    }

                    //Get an object for the block and tell it it's been restored so it can update dates
                    //etc. if necessary
                    if ($blockobj = block_instance($instance->name,$instance)) {
                        $blockobj->after_restore($restore);
                    }

                    //Now we can increment the weight counter
                    ++$maxweights[$instance->position];

                    //Keep track of block types we have already added
                    $addedblocks[$instance->name] = true;

                }
            }
        }

        return $status;
    }

    //This function creates all the course_sections and course_modules from xml
    //when restoring in a new course or simply checks sections and create records
    //in backup_ids when restoring in a existing course
    function restore_create_sections(&$restore, $xml_file) {

        global $CFG,$db;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            $info = restore_read_xml_sections($xml_file);
        }
        //Put the info in the DB, recoding ids and saving the in backup tables

        $sequence = "";

        if ($info) {
            //For each, section, save it to db
            foreach ($info->sections as $key => $sect) {
                $sequence = "";
                $section = new object();
                $section->course = $restore->course_id;
                $section->section = $sect->number;
                $section->summary = backup_todb($sect->summary);
                $section->visible = $sect->visible;
                $section->sequence = "";
                //Now calculate the section's newid
                $newid = 0;
                if ($restore->restoreto == RESTORETO_NEW_COURSE) {
                    //Save it to db (only if restoring to new course)
                    $newid = insert_record("course_sections",$section);
                } else {
                    //Get section id when restoring in existing course
                    $rec = get_record("course_sections","course",$restore->course_id,
                                                        "section",$section->section);
                    //If section exists, has empty summary and backup has some summary, use it. MDL-8848
                    if ($rec && empty($rec->summary) && !empty($section->summary)) {
                        $rec->summary = $section->summary;
                        update_record("course_sections", $rec);
                    }
                    //If that section doesn't exist, get section 0 (every mod will be
                    //asigned there
                    if(!$rec) {
                        $rec = get_record("course_sections","course",$restore->course_id,
                                                            "section","0");
                    }
                    //New check. If section 0 doesn't exist, insert it here !!
                    //Teorically this never should happen but, in practice, some users
                    //have reported this issue.
                    if(!$rec) {
                        $zero_sec = new object();
                        $zero_sec->course = $restore->course_id;
                        $zero_sec->section = 0;
                        $zero_sec->summary = "";
                        $zero_sec->sequence = "";
                        $newid = insert_record("course_sections",$zero_sec);
                        $rec->id = $newid;
                        $rec->sequence = "";
                    }
                    $newid = $rec->id;
                    $sequence = $rec->sequence;
                }
                if ($newid) {
                    //save old and new section id
                    backup_putid ($restore->backup_unique_code,"course_sections",$key,$newid);
                } else {
                    $status = false;
                }
                //If all is OK, go with associated mods
                if ($status) {
                    //If we have mods in the section
                    if (!empty($sect->mods)) {
                        //For each mod inside section
                        foreach ($sect->mods as $keym => $mod) {
                            // Yu: This part is called repeatedly for every instance,
                            // so it is necessary to set the granular flag and check isset()
                            // when the first instance of this type of mod is processed.

                            //if (!isset($restore->mods[$mod->type]->granular) && isset($restore->mods[$mod->type]->instances) && is_array($restore->mods[$mod->type]->instances)) {

                            if (!isset($restore->mods[$mod->type]->granular)) {
                                if (isset($restore->mods[$mod->type]->instances) && is_array($restore->mods[$mod->type]->instances)) {
                                    // This defines whether we want to restore specific
                                    // instances of the modules (granular restore), or
                                    // whether we don't care and just want to restore
                                    // all module instances (non-granular).
                                    $restore->mods[$mod->type]->granular = true;
                                } else {
                                    $restore->mods[$mod->type]->granular = false;
                                }
                            }

                            //Check if we've to restore this module (and instance)
                            if (!empty($restore->mods[$mod->type]->restore)) {
                                if (empty($restore->mods[$mod->type]->granular)  // we don't care about per instance
                                    || (array_key_exists($mod->instance,$restore->mods[$mod->type]->instances)
                                        && !empty($restore->mods[$mod->type]->instances[$mod->instance]->restore))) {

                                    //Get the module id from modules
                                    $module = get_record("modules","name",$mod->type);
                                    if ($module) {
                                        $course_module = new object();
                                        $course_module->course = $restore->course_id;
                                        $course_module->module = $module->id;
                                        $course_module->section = $newid;
                                        $course_module->added = $mod->added;
                                        $course_module->score = $mod->score;
                                        $course_module->indent = $mod->indent;
                                        $course_module->visible = $mod->visible;
                                        $course_module->groupmode = $mod->groupmode;
                                        if ($mod->groupingid and $grouping = restore_grouping_getid($restore, $mod->groupingid)) {
                                            $course_module->groupingid = $grouping->new_id;
                                        } else {
                                            $course_module->groupingid = 0;
                                        }
                                        $course_module->groupmembersonly = $mod->groupmembersonly;
                                        $course_module->instance = 0;
                                        //NOTE: The instance (new) is calculated and updated in db in the
                                        //      final step of the restore. We don't know it yet.
                                        //print_object($course_module);                    //Debug
                                        //Save it to db
                                        if ($mod->idnumber) {
                                            if (grade_verify_idnumber($mod->idnumber, $restore->course_id)) {
                                                $course_module->idnumber = $mod->idnumber;
                                            }
                                        }

                                        $newidmod = insert_record("course_modules", addslashes_recursive($course_module));
                                        if ($newidmod) {
                                            //save old and new module id
                                            //In the info field, we save the original instance of the module
                                            //to use it later
                                            backup_putid ($restore->backup_unique_code,"course_modules",
                                                          $keym,$newidmod,$mod->instance);

                                            $restore->mods[$mod->type]->instances[$mod->instance]->restored_as_course_module = $newidmod;
                                        } else {
                                            $status = false;
                                        }
                                        //Now, calculate the sequence field
                                        if ($status) {
                                            if ($sequence) {
                                                $sequence .= ",".$newidmod;
                                            } else {
                                                $sequence = $newidmod;
                                            }
                                        }
                                    } else {
                                        $status = false;
                                    }
                                }
                            }
                        }
                    }
                }
                //If all is OK, update sequence field in course_sections
                if ($status) {
                    if (isset($sequence)) {
                        $update_rec = new object();
                        $update_rec->id = $newid;
                        $update_rec->sequence = $sequence;
                        $status = update_record("course_sections",$update_rec);
                    }
                }
            }
        } else {
            $status = false;
        }
        return $status;
    }

    //Called to set up any course-format specific data that may be in the file
    function restore_set_format_data($restore,$xml_file) {
        global $CFG,$db;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            return false;
        }
        //Load data from XML to info
        if(!($info = restore_read_xml_formatdata($xml_file))) {
                return false;
        }

        //Process format data if there is any
        if (isset($info->format_data)) {
                if(!$format=get_field('course','format','id',$restore->course_id)) {
                    return false;
                }
                // If there was any data then it must have a restore method
                $file=$CFG->dirroot."/course/format/$format/restorelib.php";
                if(!file_exists($file)) {
                    return false;
                }
                require_once($file);
                $function=$format.'_restore_format_data';
                if(!function_exists($function)) {
                    return false;
                }
                return $function($restore,$info->format_data);
        }

        // If we got here then there's no data, but that's cool
        return true;
    }

    //This function creates all the metacourse data from xml, notifying
    //about each incidence
    function restore_create_metacourse($restore,$xml_file) {

        global $CFG,$db;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            //Load data from XML to info
            $info = restore_read_xml_metacourse($xml_file);
        }

        //Process info about metacourse
        if ($status and $info) {
            //Process child records
            if (!empty($info->childs)) {
                foreach ($info->childs as $child) {
                    $dbcourse = false;
                    $dbmetacourse = false;
                    //Check if child course exists in destination server
                    //(by id in the same server or by idnumber and shortname in other server)
                    if (backup_is_same_site($restore)) {
                        //Same server, lets see by id
                        $dbcourse = get_record('course','id',$child->id);
                    } else {
                        //Different server, lets see by idnumber and shortname, and only ONE record
                        $dbcount = count_records('course','idnumber',$child->idnumber,'shortname',$child->shortname);
                        if ($dbcount == 1) {
                            $dbcourse = get_record('course','idnumber',$child->idnumber,'shortname',$child->shortname);
                        }
                    }
                    //If child course has been found, insert data
                    if ($dbcourse) {
                        $dbmetacourse->child_course = $dbcourse->id;
                        $dbmetacourse->parent_course = $restore->course_id;
                        $status = insert_record ('course_meta',$dbmetacourse);
                    } else {
                        //Child course not found, notice!
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<ul><li>'.get_string ('childcoursenotfound').' ('.$child->id.'/'.$child->idnumber.'/'.$child->shortname.')</li></ul>';
                        }
                    }
                }
                //Now, recreate student enrolments...
                sync_metacourse($restore->course_id);
            }
            //Process parent records
            if (!empty($info->parents)) {
                foreach ($info->parents as $parent) {
                    $dbcourse = false;
                    $dbmetacourse = false;
                    //Check if parent course exists in destination server
                    //(by id in the same server or by idnumber and shortname in other server)
                    if (backup_is_same_site($restore)) {
                        //Same server, lets see by id
                        $dbcourse = get_record('course','id',$parent->id);
                    } else {
                        //Different server, lets see by idnumber and shortname, and only ONE record
                        $dbcount = count_records('course','idnumber',$parent->idnumber,'shortname',$parent->shortname);
                        if ($dbcount == 1) {
                            $dbcourse = get_record('course','idnumber',$parent->idnumber,'shortname',$parent->shortname);
                        }
                    }
                    //If parent course has been found, insert data if it is a metacourse
                    if ($dbcourse) {
                        if ($dbcourse->metacourse) {
                            $dbmetacourse->parent_course = $dbcourse->id;
                            $dbmetacourse->child_course = $restore->course_id;
                            $status = insert_record ('course_meta',$dbmetacourse);
                            //Now, recreate student enrolments in parent course
                            sync_metacourse($dbcourse->id);
                        } else {
                            //Parent course isn't metacourse, notice!
                            if (!defined('RESTORE_SILENTLY')) {
                                echo '<ul><li>'.get_string ('parentcoursenotmetacourse').' ('.$parent->id.'/'.$parent->idnumber.'/'.$parent->shortname.')</li></ul>';
                            }
                        }
                    } else {
                        //Parent course not found, notice!
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<ul><li>'.get_string ('parentcoursenotfound').' ('.$parent->id.'/'.$parent->idnumber.'/'.$parent->shortname.')</li></ul>';
                        }
                    }
                }
            }

        }
        return $status;
    }

    /**
     * This function migrades all the pre 1.9 gradebook data from xml
     */
    function restore_migrate_old_gradebook($restore,$xml_file) {
        global $CFG;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            return false;
        }

        // Get info from xml
        // info will contain the number of record to process
        $info = restore_read_xml_gradebook($restore, $xml_file);

        // If we have info, then process
        if (empty($info)) {
            return $status;
        }

        // make sure top course category exists
        $course_category = grade_category::fetch_course_category($restore->course_id);
        $course_category->load_grade_item();

        // we need to know if all grade items that were backed up are being restored
        // if that is not the case, we do not restore grade categories nor gradeitems of category type or course type
        // i.e. the aggregated grades of that category

        $restoreall = true;  // set to false if any grade_item is not selected/restored
        $importing  = !empty($SESSION->restore->importing); // there should not be a way to import old backups, but anyway ;-)

        if ($importing) {
            $restoreall = false;

        } else {
            $prev_grade_items = grade_item::fetch_all(array('courseid'=>$restore->course_id));
            $prev_grade_cats  = grade_category::fetch_all(array('courseid'=>$restore->course_id));

             // if any categories already present, skip restore of categories from backup
            if (count($prev_grade_items) > 1 or count($prev_grade_cats) > 1) {
                $restoreall = false;
            }
            unset($prev_grade_items);
            unset($prev_grade_cats);
        }

        // force creation of all grade_items - the course_modules already exist
        grade_force_full_regrading($restore->course_id);
        grade_grab_course_grades($restore->course_id);

        // Start ul
        if (!defined('RESTORE_SILENTLY')) {
            echo '<ul>';
        }

    /// Process letters
        $context = get_context_instance(CONTEXT_COURSE, $restore->course_id);
        // respect current grade letters if defined
        if ($status and $restoreall and !record_exists('grade_letters', 'contextid', $context->id)) {
            if (!defined('RESTORE_SILENTLY')) {
                echo '<li>'.get_string('gradeletters','grades').'</li>';
            }
            // Fetch recordset_size records in each iteration
            $recs = get_records_select("backup_ids","table_name = 'grade_letter' AND backup_code = $restore->backup_unique_code",
                                        "",
                                        "old_id");
            if ($recs) {
                foreach ($recs as $rec) {
                    // Get the full record from backup_ids
                    $data = backup_getid($restore->backup_unique_code,'grade_letter',$rec->old_id);
                    if ($data) {
                        $info = $data->info;
                        $dbrec = new object();
                        $dbrec->contextid     = $context->id;
                        $dbrec->lowerboundary = backup_todb($info['GRADE_LETTER']['#']['GRADE_LOW']['0']['#']);
                        $dbrec->letter        = backup_todb($info['GRADE_LETTER']['#']['LETTER']['0']['#']);
                        insert_record('grade_letters', $dbrec);
                    }
                }
            }
        }

        if (!defined('RESTORE_SILENTLY')) {
            echo '<li>'.get_string('categories','grades').'</li>';
        }
        //Fetch recordset_size records in each iteration
        $recs = get_records_select("backup_ids","table_name = 'grade_category' AND backup_code = $restore->backup_unique_code",
                                   "old_id",
                                   "old_id");
        $cat_count = count($recs);
        if ($recs) {
            foreach ($recs as $rec) {
                //Get the full record from backup_ids
                $data = backup_getid($restore->backup_unique_code,'grade_category',$rec->old_id);
                if ($data) {
                    //Now get completed xmlized object
                    $info = $data->info;

                    if ($restoreall) {
                        if ($cat_count == 1) {
                            $course_category->fullname            = backup_todb($info['GRADE_CATEGORY']['#']['NAME']['0']['#'], false);
                            $course_category->droplow             = backup_todb($info['GRADE_CATEGORY']['#']['DROP_X_LOWEST']['0']['#'], false);
                            $course_category->aggregation         = GRADE_AGGREGATE_WEIGHTED_MEAN2;
                            $course_category->aggregateonlygraded = 0;
                            $course_category->update('restore');
                            $grade_category = $course_category;

                        } else {
                            $grade_category = new grade_category();
                            $grade_category->courseid            = $restore->course_id;
                            $grade_category->fullname            = backup_todb($info['GRADE_CATEGORY']['#']['NAME']['0']['#'], false);
                            $grade_category->droplow             = backup_todb($info['GRADE_CATEGORY']['#']['DROP_X_LOWEST']['0']['#'], false);
                            $grade_category->aggregation         = GRADE_AGGREGATE_WEIGHTED_MEAN2;
                            $grade_category->aggregateonlygraded = 0;
                            $grade_category->insert('restore');
                            $grade_category->load_grade_item(); // force cretion of grade_item
                        }

                    } else {
                        $grade_category = null;
                    }

                    /// now, restore grade_items
                    $items = array();
                    if (!empty($info['GRADE_CATEGORY']['#']['GRADE_ITEMS']['0']['#']['GRADE_ITEM'])) {
                        //Iterate over items
                        foreach ($info['GRADE_CATEGORY']['#']['GRADE_ITEMS']['0']['#']['GRADE_ITEM'] as $ite_info) {
                            $modname         = backup_todb($ite_info['#']['MODULE_NAME']['0']['#'], false);
                            $olditeminstance = backup_todb($ite_info['#']['CMINSTANCE']['0']['#'], false);
                            if (!$mod = backup_getid($restore->backup_unique_code,$modname, $olditeminstance)) {
                                continue; // not restored
                            }
                            $iteminstance = $mod->new_id;
                            if (!$cm = get_coursemodule_from_instance($modname, $iteminstance, $restore->course_id)) {
                                continue; // does not exist
                            }

                            if (!$grade_item = grade_item::fetch(array('itemtype'=>'mod', 'itemmodule'=>$cm->modname, 'iteminstance'=>$cm->instance, 'courseid'=>$cm->course, 'itemnumber'=>0))) {
                                continue; // no item yet??
                            }

                            if ($grade_category) {
                                $grade_item->sortorder = backup_todb($ite_info['#']['SORT_ORDER']['0']['#'], false);
                                $grade_item->set_parent($grade_category->id);
                            }

                            if ($importing
                              or ($grade_item->itemtype == 'mod' and !restore_userdata_selected($restore,  $grade_item->itemmodule, $olditeminstance))) {
                                // module instance not selected when restored using granular
                                // skip this item
                                continue;
                            }

                            //Now process grade excludes
                            if (empty($ite_info['#']['GRADE_EXCEPTIONS'])) {
                                continue;
                            }

                            foreach($ite_info['#']['GRADE_EXCEPTIONS']['0']['#']['GRADE_EXCEPTION'] as $exc_info) {
                                if ($u = backup_getid($restore->backup_unique_code,"user",backup_todb($exc_info['#']['USERID']['0']['#']))) {
                                    $userid = $u->new_id;
                                    $grade_grade = new grade_grade(array('itemid'=>$grade_item->id, 'userid'=>$userid));
                                    $grade_grade->excluded = 1;
                                    if ($grade_grade->id) {
                                        $grade_grade->update('restore');
                                    } else {
                                        $grade_grade->insert('restore');
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!defined('RESTORE_SILENTLY')) {
        //End ul
            echo '</ul>';
        }

        return $status;
    }

    /**
     * This function creates all the gradebook data from xml
     */
    function restore_create_gradebook($restore,$xml_file) {
        global $CFG;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            return false;
        }

        // Get info from xml
        // info will contain the number of record to process
        $info = restore_read_xml_gradebook($restore, $xml_file);

        // If we have info, then process
        if (empty($info)) {
            return $status;
        }

        if (empty($CFG->disablegradehistory) and isset($info->gradebook_histories) and $info->gradebook_histories == "true") {
            $restore_histories = true;
        } else {
            $restore_histories = false;
        }

        // make sure top course category exists
        $course_category = grade_category::fetch_course_category($restore->course_id);
        $course_category->load_grade_item();

        // we need to know if all grade items that were backed up are being restored
        // if that is not the case, we do not restore grade categories nor gradeitems of category type or course type
        // i.e. the aggregated grades of that category

        $restoreall = true;  // set to false if any grade_item is not selected/restored or already exist
        $importing  = !empty($SESSION->restore->importing);

        if ($importing) {
            $restoreall = false;

        } else {
            $prev_grade_items = grade_item::fetch_all(array('courseid'=>$restore->course_id));
            $prev_grade_cats  = grade_category::fetch_all(array('courseid'=>$restore->course_id));

             // if any categories already present, skip restore of categories from backup - course item or category already exist
            if (count($prev_grade_items) > 1 or count($prev_grade_cats) > 1) {
                $restoreall = false;
            }
            unset($prev_grade_items);
            unset($prev_grade_cats);

            if ($restoreall) {
                if ($recs = get_records_select("backup_ids","table_name = 'grade_items' AND backup_code = $restore->backup_unique_code", "", "old_id")) {
                    foreach ($recs as $rec) {
                        if ($data = backup_getid($restore->backup_unique_code,'grade_items',$rec->old_id)) {

                            $info = $data->info;
                            // do not restore if this grade_item is a mod, and
                            $itemtype = backup_todb($info['GRADE_ITEM']['#']['ITEMTYPE']['0']['#']);

                            if ($itemtype == 'mod') {
                                $olditeminstance = backup_todb($info['GRADE_ITEM']['#']['ITEMINSTANCE']['0']['#']);
                                $itemmodule      = backup_todb($info['GRADE_ITEM']['#']['ITEMMODULE']['0']['#']);

                                if (empty($restore->mods[$itemmodule]->granular)) {
                                    continue;
                                } else if (!empty($restore->mods[$itemmodule]->instances[$olditeminstance]->restore)) {
                                    continue;
                                }
                                // at least one activity should not be restored - do not restore categories and manual items at all
                                $restoreall = false;
                                break;
                            }
                        }
                    }
                }
            }
        }

        // Start ul
        if (!defined('RESTORE_SILENTLY')) {
            echo '<ul>';
        }

        // array of restored categories - speedup ;-)
        $cached_categories = array();
        $outcomes          = array();

    /// Process letters
        $context = get_context_instance(CONTEXT_COURSE, $restore->course_id);
        // respect current grade letters if defined
        if ($status and $restoreall and !record_exists('grade_letters', 'contextid', $context->id)) {
            if (!defined('RESTORE_SILENTLY')) {
                echo '<li>'.get_string('gradeletters','grades').'</li>';
            }
            // Fetch recordset_size records in each iteration
            $recs = get_records_select("backup_ids","table_name = 'grade_letters' AND backup_code = $restore->backup_unique_code",
                                        "",
                                        "old_id");
            if ($recs) {
                foreach ($recs as $rec) {
                    // Get the full record from backup_ids
                    $data = backup_getid($restore->backup_unique_code,'grade_letters',$rec->old_id);
                    if ($data) {
                        $info = $data->info;
                        $dbrec = new object();
                        $dbrec->contextid     = $context->id;
                        $dbrec->lowerboundary = backup_todb($info['GRADE_LETTER']['#']['LOWERBOUNDARY']['0']['#']);
                        $dbrec->letter        = backup_todb($info['GRADE_LETTER']['#']['LETTER']['0']['#']);
                        insert_record('grade_letters', $dbrec);
                    }
                }
            }
        }

    /// Preprocess outcomes - do not store them yet!
        if ($status and !$importing and $restoreall) {
            if (!defined('RESTORE_SILENTLY')) {
                echo '<li>'.get_string('gradeoutcomes','grades').'</li>';
            }
            $recs = get_records_select("backup_ids","table_name = 'grade_outcomes' AND backup_code = '$restore->backup_unique_code'",
                                        "",
                                        "old_id");
            if ($recs) {
                foreach ($recs as $rec) {
                    //Get the full record from backup_ids
                    $data = backup_getid($restore->backup_unique_code,'grade_outcomes',$rec->old_id);
                    if ($data) {
                        $info = $data->info;

                        //first find out if outcome already exists
                        $shortname = backup_todb($info['GRADE_OUTCOME']['#']['SHORTNAME']['0']['#']);

                        if ($candidates = get_records_sql("SELECT *
                                                             FROM {$CFG->prefix}grade_outcomes
                                                            WHERE (courseid IS NULL OR courseid = $restore->course_id)
                                                                  AND shortname = '$shortname'
                                                         ORDER BY courseid ASC, id ASC")) {
                            $grade_outcome = reset($candidates);
                            $outcomes[$rec->old_id] = $grade_outcome;
                            continue;
                        }

                        $dbrec = new object();

                        if (has_capability('moodle/grade:manageoutcomes', get_context_instance(CONTEXT_SYSTEM))) {
                            $oldoutcome = backup_todb($info['GRADE_OUTCOME']['#']['COURSEID']['0']['#']);
                            if (empty($oldoutcome)) {
                                //site wide
                                $dbrec->courseid = null;
                            } else {
                                //course only
                                $dbrec->courseid = $restore->course_id;
                            }
                        } else {
                            // no permission to add site outcomes
                            $dbrec->courseid = $restore->course_id;
                        }

                        //Get the fields
                        $dbrec->shortname    = backup_todb($info['GRADE_OUTCOME']['#']['SHORTNAME']['0']['#'], false);
                        $dbrec->fullname     = backup_todb($info['GRADE_OUTCOME']['#']['FULLNAME']['0']['#'], false);
                        $dbrec->scaleid      = backup_todb($info['GRADE_OUTCOME']['#']['SCALEID']['0']['#'], false);
                        $dbrec->description  = backup_todb($info['GRADE_OUTCOME']['#']['DESCRIPTION']['0']['#'], false);
                        $dbrec->timecreated  = backup_todb($info['GRADE_OUTCOME']['#']['TIMECREATED']['0']['#'], false);
                        $dbrec->timemodified = backup_todb($info['GRADE_OUTCOME']['#']['TIMEMODIFIED']['0']['#'], false);
                        $dbrec->usermodified = backup_todb($info['GRADE_OUTCOME']['#']['USERMODIFIED']['0']['#'], false);

                        //Need to recode the scaleid
                        if ($scale = backup_getid($restore->backup_unique_code, 'scale', $dbrec->scaleid)) {
                            $dbrec->scaleid = $scale->new_id;
                        }

                        //Need to recode the usermodified
                        if ($modifier = backup_getid($restore->backup_unique_code, 'user', $dbrec->usermodified)) {
                            $dbrec->usermodified = $modifier->new_id;
                        }

                        $grade_outcome = new grade_outcome($dbrec, false);
                        $outcomes[$rec->old_id] = $grade_outcome;
                    }
                }
            }
        }

    /// Process grade items and grades
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo '<li>'.get_string('gradeitems','grades').'</li>';
            }
            $counter = 0;

            //Fetch recordset_size records in each iteration
            $recs = get_records_select("backup_ids","table_name = 'grade_items' AND backup_code = '$restore->backup_unique_code'",
                                        "id", // restore in the backup order
                                        "old_id");

            if ($recs) {
                foreach ($recs as $rec) {
                    //Get the full record from backup_ids
                    $data = backup_getid($restore->backup_unique_code,'grade_items',$rec->old_id);
                    if ($data) {
                        $info = $data->info;

                        // first find out if category or normal item
                        $itemtype =  backup_todb($info['GRADE_ITEM']['#']['ITEMTYPE']['0']['#'], false);
                        if ($itemtype == 'course' or $itemtype == 'category') {
                            if (!$restoreall or $importing) {
                                continue;
                            }

                            $oldcat = backup_todb($info['GRADE_ITEM']['#']['ITEMINSTANCE']['0']['#'], false);
                            if (!$cdata = backup_getid($restore->backup_unique_code,'grade_categories',$oldcat)) {
                                continue;
                            }
                            $cinfo = $cdata->info;
                            unset($cdata);
                            if ($itemtype == 'course') {

                                $course_category->fullname            = backup_todb($cinfo['GRADE_CATEGORY']['#']['FULLNAME']['0']['#'], false);
                                $course_category->aggregation         = backup_todb($cinfo['GRADE_CATEGORY']['#']['AGGREGATION']['0']['#'], false);
                                $course_category->keephigh            = backup_todb($cinfo['GRADE_CATEGORY']['#']['KEEPHIGH']['0']['#'], false);
                                $course_category->droplow             = backup_todb($cinfo['GRADE_CATEGORY']['#']['DROPLOW']['0']['#'], false);
                                $course_category->aggregateonlygraded = backup_todb($cinfo['GRADE_CATEGORY']['#']['AGGREGATEONLYGRADED']['0']['#'], false);
                                $course_category->aggregateoutcomes   = backup_todb($cinfo['GRADE_CATEGORY']['#']['AGGREGATEOUTCOMES']['0']['#'], false);
                                $course_category->aggregatesubcats    = backup_todb($cinfo['GRADE_CATEGORY']['#']['AGGREGATESUBCATS']['0']['#'], false);
                                $course_category->timecreated         = backup_todb($cinfo['GRADE_CATEGORY']['#']['TIMECREATED']['0']['#'], false);
                                $course_category->update('restore');

                                $status = backup_putid($restore->backup_unique_code,'grade_categories',$oldcat,$course_category->id) && $status;
                                $cached_categories[$oldcat] = $course_category;
                                $grade_item = $course_category->get_grade_item();

                            } else {
                                $oldparent = backup_todb($cinfo['GRADE_CATEGORY']['#']['PARENT']['0']['#'], false);
                                if (empty($cached_categories[$oldparent])) {
                                    debugging('parent not found '.$oldparent);
                                    continue; // parent not found, sorry
                                }
                                $grade_category = new grade_category();
                                $grade_category->courseid            = $restore->course_id;
                                $grade_category->parent              = $cached_categories[$oldparent]->id;
                                $grade_category->fullname            = backup_todb($cinfo['GRADE_CATEGORY']['#']['FULLNAME']['0']['#'], false);
                                $grade_category->aggregation         = backup_todb($cinfo['GRADE_CATEGORY']['#']['AGGREGATION']['0']['#'], false);
                                $grade_category->keephigh            = backup_todb($cinfo['GRADE_CATEGORY']['#']['KEEPHIGH']['0']['#'], false);
                                $grade_category->droplow             = backup_todb($cinfo['GRADE_CATEGORY']['#']['DROPLOW']['0']['#'], false);
                                $grade_category->aggregateonlygraded = backup_todb($cinfo['GRADE_CATEGORY']['#']['AGGREGATEONLYGRADED']['0']['#'], false);
                                $grade_category->aggregateoutcomes   = backup_todb($cinfo['GRADE_CATEGORY']['#']['AGGREGATEOUTCOMES']['0']['#'], false);
                                $grade_category->aggregatesubcats    = backup_todb($cinfo['GRADE_CATEGORY']['#']['AGGREGATESUBCATS']['0']['#'], false);
                                $grade_category->timecreated         = backup_todb($cinfo['GRADE_CATEGORY']['#']['TIMECREATED']['0']['#'], false);
                                $grade_category->insert('restore');

                                $status = backup_putid($restore->backup_unique_code,'grade_categories',$oldcat,$grade_category->id) && $status;
                                $cached_categories[$oldcat] = $grade_category;
                                $grade_item = $grade_category->get_grade_item(); // creates grade_item too
                            }
                            unset($cinfo);

                            $idnumber = backup_todb($info['GRADE_ITEM']['#']['IDNUMBER']['0']['#'], false);
                            if (grade_verify_idnumber($idnumber, $restore->course_id)) {
                                $grade_item->idnumber    = $idnumber;
                            }

                            $grade_item->itemname        = backup_todb($info['GRADE_ITEM']['#']['ITEMNAME']['0']['#'], false);
                            $grade_item->iteminfo        = backup_todb($info['GRADE_ITEM']['#']['ITEMINFO']['0']['#'], false);
                            $grade_item->gradetype       = backup_todb($info['GRADE_ITEM']['#']['GRADETYPE']['0']['#'], false);
                            $grade_item->calculation     = backup_todb($info['GRADE_ITEM']['#']['CALCULATION']['0']['#'], false);
                            $grade_item->grademax        = backup_todb($info['GRADE_ITEM']['#']['GRADEMAX']['0']['#'], false);
                            $grade_item->grademin        = backup_todb($info['GRADE_ITEM']['#']['GRADEMIN']['0']['#'], false);
                            $grade_item->gradepass       = backup_todb($info['GRADE_ITEM']['#']['GRADEPASS']['0']['#'], false);
                            $grade_item->multfactor      = backup_todb($info['GRADE_ITEM']['#']['MULTFACTOR']['0']['#'], false);
                            $grade_item->plusfactor      = backup_todb($info['GRADE_ITEM']['#']['PLUSFACTOR']['0']['#'], false);
                            $grade_item->aggregationcoef = backup_todb($info['GRADE_ITEM']['#']['AGGREGATIONCOEF']['0']['#'], false);
                            $grade_item->display         = backup_todb($info['GRADE_ITEM']['#']['DISPLAY']['0']['#'], false);
                            $grade_item->decimals        = backup_todb($info['GRADE_ITEM']['#']['DECIMALS']['0']['#'], false);
                            $grade_item->hidden          = backup_todb($info['GRADE_ITEM']['#']['HIDDEN']['0']['#'], false);
                            $grade_item->locked          = backup_todb($info['GRADE_ITEM']['#']['LOCKED']['0']['#'], false);
                            $grade_item->locktime        = backup_todb($info['GRADE_ITEM']['#']['LOCKTIME']['0']['#'], false);
                            $grade_item->timecreated     = backup_todb($info['GRADE_ITEM']['#']['TIMECREATED']['0']['#'], false);

                            if (backup_todb($info['GRADE_ITEM']['#']['SCALEID']['0']['#'], false)) {
                                $scale = backup_getid($restore->backup_unique_code,"scale",backup_todb($info['GRADE_ITEM']['#']['SCALEID']['0']['#'], false));
                                $grade_item->scaleid     = $scale->new_id;
                            }

                            if  (backup_todb($info['GRADE_ITEM']['#']['OUTCOMEID']['0']['#'], false)) {
                                $outcome = backup_getid($restore->backup_unique_code,"grade_outcomes",backup_todb($info['GRADE_ITEM']['#']['OUTCOMEID']['0']['#'], false));
                                $grade_item->outcomeid   = $outcome->new_id;
                            }

                            $grade_item->update('restore');
                            $status = backup_putid($restore->backup_unique_code,"grade_items", $rec->old_id, $grade_item->id) && $status;

                        } else {
                            if ($itemtype != 'mod' and (!$restoreall or $importing)) {
                                // not extra gradebook stuff if restoring individual activities or something already there
                                continue;
                            }

                            $dbrec = new object();

                            $dbrec->courseid      = $restore->course_id;
                            $dbrec->itemtype      = backup_todb($info['GRADE_ITEM']['#']['ITEMTYPE']['0']['#'], false);
                            $dbrec->itemmodule    = backup_todb($info['GRADE_ITEM']['#']['ITEMMODULE']['0']['#'], false);

                            if ($itemtype == 'mod') {
                                // iteminstance should point to new mod
                                $olditeminstance = backup_todb($info['GRADE_ITEM']['#']['ITEMINSTANCE']['0']['#'], false);
                                $mod = backup_getid($restore->backup_unique_code,$dbrec->itemmodule, $olditeminstance);
                                $dbrec->iteminstance = $mod->new_id;
                                if (!$cm = get_coursemodule_from_instance($dbrec->itemmodule, $mod->new_id)) {
                                    // item not restored - no item
                                    continue;
                                }
                                // keep in sync with activity idnumber
                                $dbrec->idnumber = $cm->idnumber;

                            } else {
                                $idnumber = backup_todb($info['GRADE_ITEM']['#']['IDNUMBER']['0']['#'], false);

                                if (grade_verify_idnumber($idnumber, $restore->course_id)) {
                                    //make sure the new idnumber is unique
                                    $dbrec->idnumber  = $idnumber;
                                }
                            }

                            $dbrec->itemname        = backup_todb($info['GRADE_ITEM']['#']['ITEMNAME']['0']['#'], false);
                            $dbrec->itemtype        = backup_todb($info['GRADE_ITEM']['#']['ITEMTYPE']['0']['#'], false);
                            $dbrec->itemmodule      = backup_todb($info['GRADE_ITEM']['#']['ITEMMODULE']['0']['#'], false);
                            $dbrec->itemnumber      = backup_todb($info['GRADE_ITEM']['#']['ITEMNUMBER']['0']['#'], false);
                            $dbrec->iteminfo        = backup_todb($info['GRADE_ITEM']['#']['ITEMINFO']['0']['#'], false);
                            $dbrec->gradetype       = backup_todb($info['GRADE_ITEM']['#']['GRADETYPE']['0']['#'], false);
                            $dbrec->calculation     = backup_todb($info['GRADE_ITEM']['#']['CALCULATION']['0']['#'], false);
                            $dbrec->grademax        = backup_todb($info['GRADE_ITEM']['#']['GRADEMAX']['0']['#'], false);
                            $dbrec->grademin        = backup_todb($info['GRADE_ITEM']['#']['GRADEMIN']['0']['#'], false);
                            $dbrec->gradepass       = backup_todb($info['GRADE_ITEM']['#']['GRADEPASS']['0']['#'], false);
                            $dbrec->multfactor      = backup_todb($info['GRADE_ITEM']['#']['MULTFACTOR']['0']['#'], false);
                            $dbrec->plusfactor      = backup_todb($info['GRADE_ITEM']['#']['PLUSFACTOR']['0']['#'], false);
                            $dbrec->aggregationcoef = backup_todb($info['GRADE_ITEM']['#']['AGGREGATIONCOEF']['0']['#'], false);
                            $dbrec->display         = backup_todb($info['GRADE_ITEM']['#']['DISPLAY']['0']['#'], false);
                            $dbrec->decimals        = backup_todb($info['GRADE_ITEM']['#']['DECIMALS']['0']['#'], false);
                            $dbrec->hidden          = backup_todb($info['GRADE_ITEM']['#']['HIDDEN']['0']['#'], false);
                            $dbrec->locked          = backup_todb($info['GRADE_ITEM']['#']['LOCKED']['0']['#'], false);
                            $dbrec->locktime        = backup_todb($info['GRADE_ITEM']['#']['LOCKTIME']['0']['#'], false);
                            $dbrec->timecreated     = backup_todb($info['GRADE_ITEM']['#']['TIMECREATED']['0']['#'], false);

                            if (backup_todb($info['GRADE_ITEM']['#']['SCALEID']['0']['#'], false)) {
                                $scale = backup_getid($restore->backup_unique_code,"scale",backup_todb($info['GRADE_ITEM']['#']['SCALEID']['0']['#'], false));
                                $dbrec->scaleid = $scale->new_id;
                            }

                            if  (backup_todb($info['GRADE_ITEM']['#']['OUTCOMEID']['0']['#'])) {
                                $oldoutcome = backup_todb($info['GRADE_ITEM']['#']['OUTCOMEID']['0']['#']);
                                if (empty($outcomes[$oldoutcome])) {
                                    continue; // error!
                                } 
                                if (empty($outcomes[$oldoutcome]->id)) {
                                    $outcomes[$oldoutcome]->insert('restore');
                                    $outcomes[$oldoutcome]->use_in($restore->course_id);
                                    backup_putid($restore->backup_unique_code, "grade_outcomes", $oldoutcome, $outcomes[$oldoutcome]->id);
                                }
                                $dbrec->outcomeid = $outcomes[$oldoutcome]->id;
                            }

                            $grade_item = new grade_item($dbrec, false);
                            $grade_item->insert('restore');
                            if ($restoreall) {
                                // set original parent if restored
                                $oldcat = $info['GRADE_ITEM']['#']['CATEGORYID']['0']['#'];
                                if (!empty($cached_categories[$oldcat])) {
                                    $grade_item->set_parent($cached_categories[$oldcat]->id);
                                }
                            }
                            $status = backup_putid($restore->backup_unique_code,"grade_items", $rec->old_id, $grade_item->id) && $status;
                        }

                        // no need to restore grades if user data is not selected or importing activities
                        if ($importing
                          or ($grade_item->itemtype == 'mod' and !restore_userdata_selected($restore,  $grade_item->itemmodule, $olditeminstance))) {
                            // module instance not selected when restored using granular
                            // skip this item
                            continue;
                        }

                        /// now, restore grade_grades
                        if (!empty($info['GRADE_ITEM']['#']['GRADE_GRADES']['0']['#']['GRADE'])) {
                            //Iterate over items
                            foreach ($info['GRADE_ITEM']['#']['GRADE_GRADES']['0']['#']['GRADE'] as $g_info) {

                                $grade = new grade_grade();
                                $grade->itemid         = $grade_item->id;

                                $olduser = backup_todb($g_info['#']['USERID']['0']['#'], false);
                                $user = backup_getid($restore->backup_unique_code,"user",$olduser);
                                $grade->userid         = $user->new_id;

                                $grade->rawgrade       = backup_todb($g_info['#']['RAWGRADE']['0']['#'], false);
                                $grade->rawgrademax    = backup_todb($g_info['#']['RAWGRADEMAX']['0']['#'], false);
                                $grade->rawgrademin    = backup_todb($g_info['#']['RAWGRADEMIN']['0']['#'], false);
                                // need to find scaleid
                                if (backup_todb($g_info['#']['RAWSCALEID']['0']['#'])) {
                                    $scale = backup_getid($restore->backup_unique_code,"scale",backup_todb($g_info['#']['RAWSCALEID']['0']['#'], false));
                                    $grade->rawscaleid = $scale->new_id;
                                }

                                if (backup_todb($g_info['#']['USERMODIFIED']['0']['#'])) {
                                    if ($modifier = backup_getid($restore->backup_unique_code,"user", backup_todb($g_info['#']['USERMODIFIED']['0']['#'], false))) {
                                        $grade->usermodified = $modifier->new_id;
                                    }
                                }

                                $grade->finalgrade        = backup_todb($g_info['#']['FINALGRADE']['0']['#'], false);
                                $grade->hidden            = backup_todb($g_info['#']['HIDDEN']['0']['#'], false);
                                $grade->locked            = backup_todb($g_info['#']['LOCKED']['0']['#'], false);
                                $grade->locktime          = backup_todb($g_info['#']['LOCKTIME']['0']['#'], false);
                                $grade->exported          = backup_todb($g_info['#']['EXPORTED']['0']['#'], false);
                                $grade->overridden        = backup_todb($g_info['#']['OVERRIDDEN']['0']['#'], false);
                                $grade->excluded          = backup_todb($g_info['#']['EXCLUDED']['0']['#'], false);
                                $grade->feedback          = backup_todb($g_info['#']['FEEDBACK']['0']['#'], false);
                                $grade->feedbackformat    = backup_todb($g_info['#']['FEEDBACKFORMAT']['0']['#'], false);
                                $grade->information       = backup_todb($g_info['#']['INFORMATION']['0']['#'], false);
                                $grade->informationformat = backup_todb($g_info['#']['INFORMATIONFORMAT']['0']['#'], false);
                                $grade->timecreated       = backup_todb($g_info['#']['TIMECREATED']['0']['#'], false);
                                $grade->timemodified      = backup_todb($g_info['#']['TIMEMODIFIED']['0']['#'], false);

                                $grade->insert('restore');
                                backup_putid($restore->backup_unique_code,"grade_grades", backup_todb($g_info['#']['ID']['0']['#']), $grade->id);

                                $counter++;
                                if ($counter % 20 == 0) {
                                    if (!defined('RESTORE_SILENTLY')) {
                                        echo ".";
                                        if ($counter % 400 == 0) {
                                            echo "<br />";
                                        }
                                    }
                                    backup_flush(300);
                                }
                            }
                        }
                    }
                }
            }
        }

    /// add outcomes that are not used when doing full restore
        if ($status and $restoreall) {
            foreach ($outcomes as $oldoutcome=>$grade_outcome) {
                if (empty($grade_outcome->id)) {
                    $grade_outcome->insert('restore');
                    $grade_outcome->use_in($restore->course_id);
                    backup_putid($restore->backup_unique_code, "grade_outcomes", $oldoutcome, $grade_outcome->id);
                }
            }
        }


        if ($status and !$importing and $restore_histories) {
            /// following code is very inefficient 

            $gchcount = count_records ('backup_ids', 'backup_code', $restore->backup_unique_code, 'table_name', 'grade_categories_history');
            $gghcount = count_records ('backup_ids', 'backup_code', $restore->backup_unique_code, 'table_name', 'grade_grades_history');
            $gihcount = count_records ('backup_ids', 'backup_code', $restore->backup_unique_code, 'table_name', 'grade_items_history');
            $gohcount = count_records ('backup_ids', 'backup_code', $restore->backup_unique_code, 'table_name', 'grade_outcomes_history');

            // Number of records to get in every chunk
            $recordset_size = 2;

            // process histories
            if ($gchcount && $status) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo '<li>'.get_string('gradecategoryhistory','grades').'</li>';
                }
                $counter = 0;
                while ($counter < $gchcount) {
                    //Fetch recordset_size records in each iteration
                    $recs = get_records_select("backup_ids","table_name = 'grade_categories_history' AND backup_code = '$restore->backup_unique_code'",
                                                "old_id",
                                                "old_id",
                                                $counter,
                                                $recordset_size);
                    if ($recs) {
                        foreach ($recs as $rec) {
                            //Get the full record from backup_ids
                            $data = backup_getid($restore->backup_unique_code,'grade_categories_history',$rec->old_id);
                            if ($data) {
                                //Now get completed xmlized object
                                $info = $data->info;
                                //traverse_xmlize($info);                            //Debug
                                //print_object ($GLOBALS['traverse_array']);         //Debug
                                //$GLOBALS['traverse_array']="";                     //Debug
    
                                $oldobj = backup_getid($restore->backup_unique_code,"grade_categories", backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['OLDID']['0']['#']));
                                if (empty($oldobj->new_id)) {
                                    // if the old object is not being restored, can't restoring its history
                                    $counter++;
                                    continue;
                                }
                                $dbrec->oldid = $oldobj->new_id;
                                $dbrec->action = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['ACTION']['0']['#']);
                                $dbrec->source = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['SOURCE']['0']['#']);
                                $dbrec->timemodified = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['TIMEMODIFIED']['0']['#']);
    
                                // loggeduser might not be restored, e.g. admin
                                if ($oldobj = backup_getid($restore->backup_unique_code,"user", backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['LOGGEDUSER']['0']['#']))) {
                                    $dbrec->loggeduser = $oldobj->new_id;
                                }
    
                                // this item might not have a parent at all, do not skip it if no parent is specified
                                if (backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['PARENT']['0']['#'])) {
                                    $oldobj = backup_getid($restore->backup_unique_code,"grade_categories", backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['PARENT']['0']['#']));
                                    if (empty($oldobj->new_id)) {
                                        // if the parent category not restored
                                        $counter++;
                                        continue;
                                    }
                                }
                                $dbrec->parent = $oldobj->new_id;
                                $dbrec->depth = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['DEPTH']['0']['#']);
                                // path needs to be rebuilt
                                if ($path = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['PATH']['0']['#'])) {
                                // to preserve the path and make it work, we need to replace the categories one by one
                                // we first get the list of categories in current path
                                    if ($paths = explode("/", $path)) {
                                        $newpath = '';
                                        foreach ($paths as $catid) {
                                            if ($catid) {
                                                // find the new corresponding path
                                                $oldpath = backup_getid($restore->backup_unique_code,"grade_categories", $catid);
                                                $newpath .= "/$oldpath->new_id";
                                            }
                                        }
                                        $dbrec->path = $newpath;
                                    }
                                }
                                $dbrec->fullname = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['FULLNAME']['0']['#']);
                                $dbrec->aggregation = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['AGGRETGATION']['0']['#']);
                                $dbrec->keephigh = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['KEEPHIGH']['0']['#']);
                                $dbrec->droplow = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['DROPLOW']['0']['#']);
                                
                                $dbrec->aggregateonlygraded = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['AGGREGATEONLYGRADED']['0']['#']);
                                $dbrec->aggregateoutcomes = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['AGGREGATEOUTCOMES']['0']['#']);
                                $dbrec->aggregatesubcats = backup_todb($info['GRADE_CATEGORIES_HISTORY']['#']['AGGREGATESUBCATS']['0']['#']);
    
                                $dbrec->courseid = $restore->course_id;
                                insert_record('grade_categories_history', $dbrec);
                                unset($dbrec);
    
                            }
                            //Increment counters
                            $counter++;
                            //Do some output
                            if ($counter % 1 == 0) {
                                if (!defined('RESTORE_SILENTLY')) {
                                    echo ".";
                                    if ($counter % 20 == 0) {
                                        echo "<br />";
                                    }
                                }
                                backup_flush(300);
                            }
                        }
                    }
                }
            }
    
            // process histories
            if ($gghcount && $status) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo '<li>'.get_string('gradegradeshistory','grades').'</li>';
                }
                $counter = 0;
                while ($counter < $gghcount) {
                    //Fetch recordset_size records in each iteration
                    $recs = get_records_select("backup_ids","table_name = 'grade_grades_history' AND backup_code = '$restore->backup_unique_code'",
                                                "old_id",
                                                "old_id",
                                                $counter,
                                                $recordset_size);
                    if ($recs) {
                        foreach ($recs as $rec) {
                            //Get the full record from backup_ids
                            $data = backup_getid($restore->backup_unique_code,'grade_grades_history',$rec->old_id);
                            if ($data) {
                                //Now get completed xmlized object
                                $info = $data->info;
                                //traverse_xmlize($info);                            //Debug
                                //print_object ($GLOBALS['traverse_array']);         //Debug
                                //$GLOBALS['traverse_array']="";                     //Debug
    
                                $oldobj = backup_getid($restore->backup_unique_code,"grade_grades", backup_todb($info['GRADE_GRADES_HISTORY']['#']['OLDID']['0']['#']));
                                if (empty($oldobj->new_id)) {
                                    // if the old object is not being restored, can't restoring its history
                                    $counter++;
                                    continue;
                                }
                                $dbrec->oldid = $oldobj->new_id;
                                $dbrec->action = backup_todb($info['GRADE_GRADES_HISTORY']['#']['ACTION']['0']['#']);
                                $dbrec->source = backup_todb($info['GRADE_GRADES_HISTORY']['#']['SOURCE']['0']['#']);
                                $dbrec->timemodified = backup_todb($info['GRADE_GRADES_HISTORY']['#']['TIMEMODIFIED']['0']['#']);
                                if ($oldobj = backup_getid($restore->backup_unique_code,"user", backup_todb($info['GRADE_GRADES_HISTORY']['#']['LOGGEDUSER']['0']['#']))) {
                                    $dbrec->loggeduser = $oldobj->new_id;
                                }
                                
                                $oldobj = backup_getid($restore->backup_unique_code,"grade_items", backup_todb($info['GRADE_GRADES_HISTORY']['#']['ITEMID']['0']['#']));
                                $dbrec->itemid = $oldobj->new_id;
                                if (empty($dbrec->itemid)) {
                                    $counter++;
                                    continue; // grade item not being restored
                                }
                                $oldobj = backup_getid($restore->backup_unique_code,"user", backup_todb($info['GRADE_GRADES_HISTORY']['#']['USERID']['0']['#']));
                                $dbrec->userid = $oldobj->new_id;
                                $dbrec->rawgrade = backup_todb($info['GRADE_GRADES_HISTORY']['#']['RAWGRADE']['0']['#']);
                                $dbrec->rawgrademax = backup_todb($info['GRADE_GRADES_HISTORY']['#']['RAWGRADEMAX']['0']['#']);
                                $dbrec->rawgrademin = backup_todb($info['GRADE_GRADES_HISTORY']['#']['RAWGRADEMIN']['0']['#']);
                                if ($oldobj = backup_getid($restore->backup_unique_code,"user", backup_todb($info['GRADE_GRADES_HISTORY']['#']['USERMODIFIED']['0']['#']))) {
                                    $dbrec->usermodified = $oldobj->new_id;
                                }
                                
                                if (backup_todb($info['GRADE_GRADES_HISTORY']['#']['RAWSCALEID']['0']['#'])) {
                                    $scale = backup_getid($restore->backup_unique_code,"scale",backup_todb($info['GRADE_GRADES_HISTORY']['#']['RAWSCALEID']['0']['#']));
                                    $dbrec->rawscaleid = $scale->new_id;
                                }
                                
                                $dbrec->finalgrade = backup_todb($info['GRADE_GRADES_HISTORY']['#']['FINALGRADE']['0']['#']);
                                $dbrec->hidden = backup_todb($info['GRADE_GRADES_HISTORY']['#']['HIDDEN']['0']['#']);
                                $dbrec->locked = backup_todb($info['GRADE_GRADES_HISTORY']['#']['LOCKED']['0']['#']);
                                $dbrec->locktime = backup_todb($info['GRADE_GRADES_HISTORY']['#']['LOCKTIME']['0']['#']);
                                $dbrec->exported = backup_todb($info['GRADE_GRADES_HISTORY']['#']['EXPORTED']['0']['#']);
                                $dbrec->overridden = backup_todb($info['GRADE_GRADES_HISTORY']['#']['OVERRIDDEN']['0']['#']);
                                $dbrec->excluded = backup_todb($info['GRADE_GRADES_HISTORY']['#']['EXCLUDED']['0']['#']);
                                $dbrec->feedback = backup_todb($info['GRADE_TEXT_HISTORY']['#']['FEEDBACK']['0']['#']);
                                $dbrec->feedbackformat = backup_todb($info['GRADE_TEXT_HISTORY']['#']['FEEDBACKFORMAT']['0']['#']);
                                $dbrec->information = backup_todb($info['GRADE_TEXT_HISTORY']['#']['INFORMATION']['0']['#']);
                                $dbrec->informationformat = backup_todb($info['GRADE_TEXT_HISTORY']['#']['INFORMATIONFORMAT']['0']['#']);
    
                                insert_record('grade_grades_history', $dbrec);
                                unset($dbrec);
    
                            }
                            //Increment counters
                            $counter++;
                            //Do some output
                            if ($counter % 1 == 0) {
                                if (!defined('RESTORE_SILENTLY')) {
                                    echo ".";
                                    if ($counter % 20 == 0) {
                                        echo "<br />";
                                    }
                                }
                                backup_flush(300);
                            }
                        }
                    }
                }
            }
    
            // process histories
    
            if ($gihcount && $status) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo '<li>'.get_string('gradeitemshistory','grades').'</li>';
                }
                $counter = 0;
                while ($counter < $gihcount) {
                    //Fetch recordset_size records in each iteration
                    $recs = get_records_select("backup_ids","table_name = 'grade_items_history' AND backup_code = '$restore->backup_unique_code'",
                                                "old_id",
                                                "old_id",
                                                $counter,
                                                $recordset_size);
                    if ($recs) {
                        foreach ($recs as $rec) {
                            //Get the full record from backup_ids
                            $data = backup_getid($restore->backup_unique_code,'grade_items_history',$rec->old_id);
                            if ($data) {
                                //Now get completed xmlized object
                                $info = $data->info;
                                //traverse_xmlize($info);                            //Debug
                                //print_object ($GLOBALS['traverse_array']);         //Debug
                                //$GLOBALS['traverse_array']="";                     //Debug
    
    
                                $oldobj = backup_getid($restore->backup_unique_code,"grade_items", backup_todb($info['GRADE_ITEM_HISTORY']['#']['OLDID']['0']['#']));
                                if (empty($oldobj->new_id)) {
                                    // if the old object is not being restored, can't restoring its history
                                    $counter++;
                                    continue;
                                }
                                $dbrec->oldid = $oldobj->new_id;
                                $dbrec->action = backup_todb($info['GRADE_ITEM_HISTORY']['#']['ACTION']['0']['#']);
                                $dbrec->source = backup_todb($info['GRADE_ITEM_HISTORY']['#']['SOURCE']['0']['#']);
                                $dbrec->timemodified = backup_todb($info['GRADE_ITEM_HISTORY']['#']['TIMEMODIFIED']['0']['#']);
                                if ($oldobj = backup_getid($restore->backup_unique_code,"user", backup_todb($info['GRADE_ITEM_HISTORY']['#']['LOGGEDUSER']['0']['#']))) {
                                    $dbrec->loggeduser = $oldobj->new_id;
                                }
                                $dbrec->courseid = $restore->course_id;
                                $oldobj = backup_getid($restore->backup_unique_code,'grade_categories',backup_todb($info['GRADE_ITEM_HISTORY']['#']['CATEGORYID']['0']['#']));
                                $oldobj->categoryid = $category->new_id;
                                if (empty($oldobj->categoryid)) {
                                    $counter++;
                                    continue; // category not restored
                                }
    
                                $dbrec->itemname= backup_todb($info['GRADE_ITEM_HISTORY']['#']['ITEMNAME']['0']['#']);
                                $dbrec->itemtype = backup_todb($info['GRADE_ITEM_HISTORY']['#']['ITEMTYPE']['0']['#']);
                                $dbrec->itemmodule = backup_todb($info['GRADE_ITEM_HISTORY']['#']['ITEMMODULE']['0']['#']);
    
                                // code from grade_items restore
                                $iteminstance = backup_todb($info['GRADE_ITEM_HISTORY']['#']['ITEMINSTANCE']['0']['#']);
                                // do not restore if this grade_item is a mod, and
                                if ($dbrec->itemtype == 'mod') {
    
                                    if (!restore_userdata_selected($restore,  $dbrec->itemmodule, $iteminstance)) {
                                        // module instance not selected when restored using granular
                                        // skip this item
                                        $counter++;
                                        continue;
                                    }
    
                                    // iteminstance should point to new mod
    
                                    $mod = backup_getid($restore->backup_unique_code,$dbrec->itemmodule, $iteminstance);
                                    $dbrec->iteminstance = $mod->new_id;
    
                                } else if ($dbrec->itemtype == 'category') {
                                    // the item instance should point to the new grade category
    
                                    // only proceed if we are restoring all grade items
                                    if ($restoreall) {
                                        $category = backup_getid($restore->backup_unique_code,'grade_categories', $iteminstance);
                                        $dbrec->iteminstance = $category->new_id;
                                    } else {
                                        // otherwise we can safely ignore this grade item and subsequent
                                        // grade_raws, grade_finals etc
                                        continue;
                                    }
                                } elseif ($dbrec->itemtype == 'course') { // We don't restore course type to avoid duplicate course items
                                    if ($restoreall) {
                                        // TODO any special code needed here to restore course item without duplicating it?
                                        // find the course category with depth 1, and course id = current course id
                                        // this would have been already restored
    
                                        $cat = get_record('grade_categories', 'depth', 1, 'courseid', $restore->course_id);
                                        $dbrec->iteminstance = $cat->id;
    
                                    } else {
                                        $counter++;
                                        continue;
                                    }
                                }
    
                                $dbrec->itemnumber = backup_todb($info['GRADE_ITEM_HISTORY']['#']['ITEMNUMBER']['0']['#']);
                                $dbrec->iteminfo = backup_todb($info['GRADE_ITEM_HISTORY']['#']['ITEMINFO']['0']['#']);
                                $dbrec->idnumber = backup_todb($info['GRADE_ITEM_HISTORY']['#']['IDNUMBER']['0']['#']);
                                $dbrec->calculation = backup_todb($info['GRADE_ITEM_HISTORY']['#']['CALCULATION']['0']['#']);
                                $dbrec->gradetype = backup_todb($info['GRADE_ITEM_HISTORY']['#']['GRADETYPE']['0']['#']);
                                $dbrec->grademax = backup_todb($info['GRADE_ITEM_HISTORY']['#']['GRADEMAX']['0']['#']);
                                $dbrec->grademin = backup_todb($info['GRADE_ITEM_HISTORY']['#']['GRADEMIN']['0']['#']);
                                if ($oldobj = backup_getid($restore->backup_unique_code,"scale", backup_todb($info['GRADE_ITEM_HISTORY']['#']['SCALEID']['0']['#']))) {
                                    // scaleid is optional
                                    $dbrec->scaleid = $oldobj->new_id;
                                }
                                if ($oldobj = backup_getid($restore->backup_unique_code,"grade_outcomes", backup_todb($info['GRADE_ITEM_HISTORY']['#']['OUTCOMEID']['0']['#']))) {
                                    // outcome is optional
                                    $dbrec->outcomeid = $oldobj->new_id;
                                }
                                $dbrec->gradepass = backup_todb($info['GRADE_ITEM_HISTORY']['#']['GRADEPASS']['0']['#']);
                                $dbrec->multfactor = backup_todb($info['GRADE_ITEM_HISTORY']['#']['MULTFACTOR']['0']['#']);
                                $dbrec->plusfactor = backup_todb($info['GRADE_ITEM_HISTORY']['#']['PLUSFACTOR']['0']['#']);
                                $dbrec->aggregationcoef = backup_todb($info['GRADE_ITEM_HISTORY']['#']['AGGREGATIONCOEF']['0']['#']);
                                $dbrec->sortorder = backup_todb($info['GRADE_ITEM_HISTORY']['#']['SORTORDER']['0']['#']);
                                $dbrec->display = backup_todb($info['GRADE_ITEM_HISTORY']['#']['DISPLAY']['0']['#']);
                                $dbrec->decimals = backup_todb($info['GRADE_ITEM_HISTORY']['#']['DECIMALS']['0']['#']);
                                $dbrec->hidden = backup_todb($info['GRADE_ITEM_HISTORY']['#']['HIDDEN']['0']['#']);
                                $dbrec->locked = backup_todb($info['GRADE_ITEM_HISTORY']['#']['LOCKED']['0']['#']);
                                $dbrec->locktime = backup_todb($info['GRADE_ITEM_HISTORY']['#']['LOCKTIME']['0']['#']);
                                $dbrec->needsupdate = backup_todb($info['GRADE_ITEM_HISTORY']['#']['NEEDSUPDATE']['0']['#']);
    
                                insert_record('grade_items_history', $dbrec);
                                unset($dbrec);
    
                            }
                            //Increment counters
                            $counter++;
                            //Do some output
                            if ($counter % 1 == 0) {
                                if (!defined('RESTORE_SILENTLY')) {
                                    echo ".";
                                    if ($counter % 20 == 0) {
                                        echo "<br />";
                                    }
                                }
                                backup_flush(300);
                            }
                        }
                    }
                }
            }
    
            // process histories
            if ($gohcount && $status) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo '<li>'.get_string('gradeoutcomeshistory','grades').'</li>';
                }
                $counter = 0;
                while ($counter < $gohcount) {
                    //Fetch recordset_size records in each iteration
                    $recs = get_records_select("backup_ids","table_name = 'grade_outcomes_history' AND backup_code = '$restore->backup_unique_code'",
                                                "old_id",
                                                "old_id",
                                                $counter,
                                                $recordset_size);
                    if ($recs) {
                        foreach ($recs as $rec) {
                            //Get the full record from backup_ids
                            $data = backup_getid($restore->backup_unique_code,'grade_outcomes_history',$rec->old_id);
                            if ($data) {
                                //Now get completed xmlized object
                                $info = $data->info;
                                //traverse_xmlize($info);                            //Debug
                                //print_object ($GLOBALS['traverse_array']);         //Debug
                                //$GLOBALS['traverse_array']="";                     //Debug
    
                                $oldobj = backup_getid($restore->backup_unique_code,"grade_outcomes", backup_todb($info['GRADE_OUTCOME_HISTORY']['#']['OLDID']['0']['#']));
                                if (empty($oldobj->new_id)) {
                                    // if the old object is not being restored, can't restoring its history
                                    $counter++;
                                    continue;
                                }
                                $dbrec->oldid = $oldobj->new_id;
                                $dbrec->action = backup_todb($info['GRADE_OUTCOME_HISTORY']['#']['ACTION']['0']['#']);
                                $dbrec->source = backup_todb($info['GRADE_OUTCOME_HISTORY']['#']['SOURCE']['0']['#']);
                                $dbrec->timemodified = backup_todb($info['GRADE_OUTCOME_HISTORY']['#']['TIMEMODIFIED']['0']['#']);
                                if ($oldobj = backup_getid($restore->backup_unique_code,"user", backup_todb($info['GRADE_OUTCOME_HISTORY']['#']['LOGGEDUSER']['0']['#']))) {
                                    $dbrec->loggeduser = $oldobj->new_id;
                                }
                                $dbrec->courseid = $restore->course_id;
                                $dbrec->shortname = backup_todb($info['GRADE_OUTCOME_HISTORY']['#']['SHORTNAME']['0']['#']);
                                $dbrec->fullname= backup_todb($info['GRADE_OUTCOME_HISTORY']['#']['FULLNAME']['0']['#']);
                                $oldobj = backup_getid($restore->backup_unique_code,"scale", backup_todb($info['GRADE_OUTCOME_HISTORY']['#']['SCALEID']['0']['#']));
                                $dbrec->scaleid = $oldobj->new_id;
                                $dbrec->description = backup_todb($info['GRADE_OUTCOME_HISTORY']['#']['DESCRIPTION']['0']['#']);
    
                                insert_record('grade_outcomes_history', $dbrec);
                                unset($dbrec);
    
                            }
                            //Increment counters
                            $counter++;
                            //Do some output
                            if ($counter % 1 == 0) {
                                if (!defined('RESTORE_SILENTLY')) {
                                    echo ".";
                                    if ($counter % 20 == 0) {
                                        echo "<br />";
                                    }
                                }
                                backup_flush(300);
                            }
                        }
                    }
                }
            }
        }

        if (!defined('RESTORE_SILENTLY')) {
        //End ul
            echo '</ul>';
        }
        return $status;
    }

    //This function creates all the user, user_students, user_teachers
    //user_course_creators and user_admins from xml
    function restore_create_users($restore,$xml_file) {

        global $CFG, $db;
        require_once ($CFG->dirroot.'/tag/lib.php');

        $authcache = array(); // Cache to get some bits from authentication plugins

        $status = true;

        // Users have already been checked by restore_precheck_users() so they are loaded
        // in backup_ids table. They don't need to be loaded (parsed) from XML again. Also, note
        // the same function has performed the needed modifications in the $user->mnethostid field
        // so we don't need to do it again here at all. Just some checks.

        // Get users ids from backup_ids table
        $userids = get_fieldset_select('backup_ids', 'old_id', "backup_code = $restore->backup_unique_code AND table_name = 'user'");

        // Have users to process, proceed with them
        if (!empty($userids)) {

        /// Get languages for quick search later
            $languages = get_list_of_languages();

        /// Iterate over all users loaded from xml
            $counter = 0;

        /// Init trailing messages
            $messages = array();
            foreach ($userids as $userid) {
                // Defaults
                $user_exists = false; // By default user does not exist
                $newid = null;        // By default, there is not newid

                // Get record from backup_ids
                $useridsdbrec = backup_getid($restore->backup_unique_code, 'user', $userid);

                // Based in restore_precheck_users() calculations, if the user exists
                // new_id must contain the id of the matching user
                if (!empty($useridsdbrec->new_id)) {
                    $user_exists = true;
                    $newid = $useridsdbrec->new_id;
                }

                $user = $useridsdbrec->info;
                foreach (array_keys(get_object_vars($user)) as $field) {
                    if (!is_array($user->$field)) {
                        $user->$field = backup_todb($user->$field, false);
                        if (is_null($user->$field)) {
                            $user->$field = '';
                        }
                    }
                }

                //Now, recode some languages (Moodle 1.5)
                if ($user->lang == 'ma_nt') {
                    $user->lang = 'mi_nt';
                }

                //Country list updates - MDL-13060
                //Any user whose country code has been deleted or modified needs to be assigned a valid one.
                $country_update_map = array(
                    'ZR' => 'CD',
                    'TP' => 'TL',
                    'FX' => 'FR',
                    'KO' => 'RS',
                    'CS' => 'RS',
                    'WA' => 'GB');
                if (array_key_exists($user->country, $country_update_map)) {
                    $user->country = $country_update_map[$user->country];
                }

                //If language does not exist here - use site default
                if (!array_key_exists($user->lang, $languages)) {
                    $user->lang = $CFG->lang;
                }

                //Check if it's admin and coursecreator
                $is_admin =         !empty($user->roles['admin']);
                $is_coursecreator = !empty($user->roles['coursecreator']);

                //Check if it's teacher and student
                $is_teacher = !empty($user->roles['teacher']);
                $is_student = !empty($user->roles['student']);

                //Check if it's needed
                $is_needed = !empty($user->roles['needed']);

                //Calculate if it is a course user
                //Has role teacher or student or needed
                $is_course_user = ($is_teacher or $is_student or $is_needed);

                // Only try to perform mnethost/auth modifications if restoring to another server
                // or if, while restoring to same server, the user doesn't exists yet (rebuilt site)
                //
                // So existing user data in same server *won't be modified by restore anymore*,
                // under any circumpstance. If somehting is wrong with existing data, it's server fault.
                if (!backup_is_same_site($restore) || (backup_is_same_site($restore) && !$user_exists)) {
                    //Arriving here, any user with mnet auth and using $CFG->mnet_localhost_id is wrong
                    //as own server cannot be accesed over mnet. Change auth to manual and inform about the switch
                    if ($user->auth == 'mnet' && $user->mnethostid == $CFG->mnet_localhost_id) {
                        // Respect registerauth
                        if ($CFG->registerauth == 'email') {
                            $user->auth = 'email';
                        } else {
                            $user->auth = 'manual';
                        }
                        // inform about the automatic switch of authentication/host
                        if(empty($user->mnethosturl)) {
                            $user->mnethosturl = '----';
                        }
                        $messages[] = get_string('mnetrestore_extusers_switchuserauth', 'admin', $user);
                    }
                }
                unset($user->mnethosturl);

                //Flags to see what parts are we going to restore
                $create_user = true;
                $create_roles = true;
                $create_custom_profile_fields = true;
                $create_tags = true;
                $create_preferences = true;

                //If we are restoring course users and it isn't a course user
                if ($restore->users == 1 and !$is_course_user) {
                    //If only restoring course_users and user isn't a course_user, inform to $backup_ids
                    $status = backup_putid($restore->backup_unique_code,"user",$userid,null,'notincourse');
                    $create_user = false;
                    $create_roles = false;
                    $create_custom_profile_fields = false;
                    $create_tags = false;
                    $create_preferences = false;
                }

                if ($user_exists and $create_user) {
                    //If user exists mark its newid in backup_ids (the same than old)
                    $status = backup_putid($restore->backup_unique_code,"user",$userid,$newid,'exists');
                    $create_user = false;
                    $create_custom_profile_fields = false;
                    $create_tags = false;
                    $create_preferences = false;
                }

                //Here, if create_user, do it
                if ($create_user) {
                    //Unset the id because it's going to be inserted with a new one
                    unset ($user->id);

                /// Disable pictures based on global setting or existing empty value (old backups can contain wrong empties)
                    if (!empty($CFG->disableuserimages) || empty($user->picture)) {
                        $user->picture = 0;
                    }

                    //We need to analyse the AUTH field to recode it:
                    //   - if the field isn't set, we are in a pre 1.4 backup and $CFG->registerauth will decide
                    //   - if the auth isn't enabled in target site, $CFG->registerauth will decide
                    //   - finally, if the auth resulting isn't enabled, default to 'manual'
                    if (empty($user->auth) || !is_enabled_auth($user->auth)) {
                        if ($CFG->registerauth == 'email') {
                            $user->auth = 'email';
                        } else {
                            $user->auth = 'manual';
                        }
                    }
                    if (!is_enabled_auth($user->auth)) { // Final auth check verify, default to manual if not enabled
                        $user->auth = 'manual';
                    }

                    // Now that we know the auth method, for users to be created without pass
                    // if password handling is internal and reset password is available
                    // we set the password to "restored" (plain text), so the login process
                    // will know how to handle that situation in order to allow the user to
                    // recover the password. MDL-20846
                    if (empty($user->password)) { // Only if restore comes without password
                        if (!array_key_exists($user->auth, $authcache)) { // Not in cache
                            $userauth = new stdClass();
                            $authplugin = get_auth_plugin($user->auth);
                            $userauth->preventpassindb = $authplugin->prevent_local_passwords();
                            $userauth->isinternal      = $authplugin->is_internal();
                            $userauth->canresetpwd     = $authplugin->can_reset_password();
                            $authcache[$user->auth] = $userauth;
                        } else {
                            $userauth = $authcache[$user->auth]; // Get from cache
                        }

                        // Most external plugins do not store passwords locally
                        if (!empty($userauth->preventpassindb)) {
                            $user->password = 'not cached';

                        // If Moodle is responsible for storing/validating pwd and reset functionality is available, mark
                        } else if ($userauth->isinternal and $userauth->canresetpwd) {
                            $user->password = 'restored';
                        }
                    }

                    //We need to process the POLICYAGREED field to recalculate it:
                    //    - if the destination site is different (by wwwroot) reset it.
                    //    - if the destination site is the same (by wwwroot), leave it unmodified

                    if (!backup_is_same_site($restore)) {
                        $user->policyagreed = 0;
                    } else {
                        //Nothing to do, we are in the same server
                    }

                    //Check if the theme exists in destination server
                    $themes = get_list_of_themes();
                    if (!in_array($user->theme, $themes)) {
                        $user->theme = '';
                    }

                    //We are going to create the user
                    //The structure is exactly as we need

                    $newid = insert_record ("user", addslashes_recursive($user));
                    //Put the new id
                    $status = backup_putid($restore->backup_unique_code,"user",$userid,$newid,"new");
                }

                ///TODO: This seccion is to support pre 1.7 course backups, using old roles
                ///      teacher, coursecreator, student.... providing a basic mapping to new ones.
                ///      Someday we'll drop support for them and this section will be safely deleted (2.0?)
                //Here, if create_roles, do it as necessary
                if ($create_roles) {
                    //Get the newid and current info from backup_ids
                    $data = backup_getid($restore->backup_unique_code,"user",$userid);
                    $newid = $data->new_id;
                    $currinfo = $data->info.",";

                    //Now, depending of the role, create records in user_studentes and user_teacher
                    //and/or mark it in backup_ids

                    if ($is_admin) {
                        //If the record (user_admins) doesn't exists
                        //Only put status in backup_ids
                        $currinfo = $currinfo."admin,";
                        $status = backup_putid($restore->backup_unique_code,"user",$userid,$newid,$currinfo);
                    }
                    if ($is_coursecreator) {
                        //If the record (user_coursecreators) doesn't exists
                        //Only put status in backup_ids
                        $currinfo = $currinfo."coursecreator,";
                        $status = backup_putid($restore->backup_unique_code,"user",$userid,$newid,$currinfo);
                    }
                    if ($is_needed) {
                        //Only put status in backup_ids
                        $currinfo = $currinfo."needed,";
                        $status = backup_putid($restore->backup_unique_code,"user",$userid,$newid,$currinfo);
                    }
                    if ($is_teacher) {
                        //If the record (teacher) doesn't exists
                        //Put status in backup_ids
                        $currinfo = $currinfo."teacher,";
                        $status = backup_putid($restore->backup_unique_code,"user",$userid,$newid,$currinfo);
                        //Set course and user
                        $user->roles['teacher']->course = $restore->course_id;
                        $user->roles['teacher']->userid = $newid;

                        //Need to analyse the enrol field
                        //    - if it isn't set, set it to $CFG->enrol
                        //    - if we are in a different server (by wwwroot), set it to $CFG->enrol
                        //    - if we are in the same server (by wwwroot), maintain it unmodified.
                        if (empty($user->roles['teacher']->enrol)) {
                            $user->roles['teacher']->enrol = $CFG->enrol;
                        } else if (!backup_is_same_site($restore)) {
                            $user->roles['teacher']->enrol = $CFG->enrol;
                        } else {
                            //Nothing to do. Leave it unmodified
                        }

                        $rolesmapping = $restore->rolesmapping;
                        $context = get_context_instance(CONTEXT_COURSE, $restore->course_id);
                        if ($user->roles['teacher']->editall) {
                            role_assign($rolesmapping['defaultteacheredit'],
                                        $newid,
                                        0,
                                        $context->id,
                                        $user->roles['teacher']->timestart,
                                        $user->roles['teacher']->timeend,
                                        0,
                                        $user->roles['teacher']->enrol);

                            // editting teacher
                        } else {
                            // non editting teacher
                            role_assign($rolesmapping['defaultteacher'],
                                        $newid,
                                        0,
                                        $context->id,
                                        $user->roles['teacher']->timestart,
                                        $user->roles['teacher']->timeend,
                                        0,
                                        $user->roles['teacher']->enrol);
                        }
                    }
                    if ($is_student) {

                        //Put status in backup_ids
                        $currinfo = $currinfo."student,";
                        $status = backup_putid($restore->backup_unique_code,"user",$userid,$newid,$currinfo);
                        //Set course and user
                        $user->roles['student']->course = $restore->course_id;
                        $user->roles['student']->userid = $newid;

                        //Need to analyse the enrol field
                        //    - if it isn't set, set it to $CFG->enrol
                        //    - if we are in a different server (by wwwroot), set it to $CFG->enrol
                        //    - if we are in the same server (by wwwroot), maintain it unmodified.
                        if (empty($user->roles['student']->enrol)) {
                            $user->roles['student']->enrol = $CFG->enrol;
                        } else if (!backup_is_same_site($restore)) {
                            $user->roles['student']->enrol = $CFG->enrol;
                        } else {
                            //Nothing to do. Leave it unmodified
                        }
                        $rolesmapping = $restore->rolesmapping;
                        $context = get_context_instance(CONTEXT_COURSE, $restore->course_id);

                        role_assign($rolesmapping['defaultstudent'],
                                    $newid,
                                    0,
                                    $context->id,
                                    $user->roles['student']->timestart,
                                    $user->roles['student']->timeend,
                                    0,
                                    $user->roles['student']->enrol);

                    }
                    if (!$is_course_user) {
                        //If the record (user) doesn't exists
                        if (!record_exists("user","id",$newid)) {
                            //Put status in backup_ids
                            $currinfo = $currinfo."user,";
                            $status = backup_putid($restore->backup_unique_code,"user",$userid,$newid,$currinfo);
                        }
                    }
                }

            /// Here, if create_custom_profile_fields, do it as necessary
                if ($create_custom_profile_fields) {
                    if (isset($user->user_custom_profile_fields)) {
                        foreach($user->user_custom_profile_fields as $udata) {
                        /// If the profile field has data and the profile shortname-datatype is defined in server
                            if ($udata->field_data) {
                                if ($field = get_record('user_info_field', 'shortname', $udata->field_name, 'datatype', $udata->field_type)) {
                                /// Insert the user_custom_profile_field
                                    $rec = new object();
                                    $rec->userid = $newid;
                                    $rec->fieldid = $field->id;
                                    $rec->data    = $udata->field_data;
                                    insert_record('user_info_data', $rec);
                                }
                            }
                        }
                    }
                }

            /// Here, if create_tags, do it as necessary
                if ($create_tags) {
                /// if tags are enabled and there are user tags
                    if (!empty($CFG->usetags) && isset($user->user_tags)) {
                        $tags = array();
                        foreach($user->user_tags as $user_tag) {
                            $tags[] = $user_tag->rawname;
                        }
                        tag_set('user', $newid, $tags);
                    }
                }

                //Here, if create_preferences, do it as necessary
                if ($create_preferences) {
                    if (isset($user->user_preferences)) {
                        foreach($user->user_preferences as $user_preference) {
                            //We check if that user_preference exists in DB
                            if (!record_exists("user_preferences","userid",$newid,"name",$user_preference->name)) {
                                //Prepare the record and insert it
                                $user_preference->userid = $newid;
                                $status = insert_record("user_preferences",$user_preference);
                            }
                        }
                    }
                }

                //Do some output
                $counter++;
                if ($counter % 10 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if ($counter % 200 == 0) {
                            echo "<br />";
                        }
                    }
                    backup_flush(300);
                }
            } /// End of loop over all the users loaded from backup_ids table

        /// Inform about all the messages geerated while restoring users
            if (!defined('RESTORE_SILENTLY')) {
                if ($messages) {
                    echo '<ul>';
                    foreach ($messages as $message) {
                        echo '<li>' . $message . '</li>';
                    }
                    echo '</ul>';
                }
            }
        }

        return $status;
    }

    //This function creates all the structures messages and contacts
    function restore_create_messages($restore,$xml_file) {

        global $CFG;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            //info will contain the id and name of every table
            //(message, message_read and message_contacts)
            //in backup_ids->info will be the real info (serialized)
            $info = restore_read_xml_messages($restore,$xml_file);

            //If we have info, then process messages & contacts
            if ($info > 0) {
                //Count how many we have
                $unreadcount = count_records ('backup_ids', 'backup_code', $restore->backup_unique_code, 'table_name', 'message');
                $readcount = count_records ('backup_ids', 'backup_code', $restore->backup_unique_code, 'table_name', 'message_read');
                $contactcount = count_records ('backup_ids', 'backup_code', $restore->backup_unique_code, 'table_name', 'message_contacts');
                if ($unreadcount || $readcount || $contactcount) {
                    //Start ul
                    if (!defined('RESTORE_SILENTLY')) {
                        echo '<ul>';
                    }
                    //Number of records to get in every chunk
                    $recordset_size = 4;

                    //Process unread
                    if ($unreadcount) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<li>'.get_string('unreadmessages','message').'</li>';
                        }
                        $counter = 0;
                        while ($counter < $unreadcount) {
                            //Fetch recordset_size records in each iteration
                            $recs = get_records_select("backup_ids","table_name = 'message' AND backup_code = '$restore->backup_unique_code'","old_id","old_id",$counter,$recordset_size);
                            if ($recs) {
                                foreach ($recs as $rec) {
                                    //Get the full record from backup_ids
                                    $data = backup_getid($restore->backup_unique_code,"message",$rec->old_id);
                                    if ($data) {
                                        //Now get completed xmlized object
                                        $info = $data->info;
                                        //traverse_xmlize($info);                            //Debug
                                        //print_object ($GLOBALS['traverse_array']);         //Debug
                                        //$GLOBALS['traverse_array']="";                     //Debug
                                        //Now build the MESSAGE record structure
                                        $dbrec = new object();
                                        $dbrec->useridfrom = backup_todb($info['MESSAGE']['#']['USERIDFROM']['0']['#']);
                                        $dbrec->useridto = backup_todb($info['MESSAGE']['#']['USERIDTO']['0']['#']);
                                        $dbrec->message = backup_todb($info['MESSAGE']['#']['MESSAGE']['0']['#']);
                                        $dbrec->format = backup_todb($info['MESSAGE']['#']['FORMAT']['0']['#']);
                                        $dbrec->timecreated = backup_todb($info['MESSAGE']['#']['TIMECREATED']['0']['#']);
                                        $dbrec->messagetype = backup_todb($info['MESSAGE']['#']['MESSAGETYPE']['0']['#']);
                                        //We have to recode the useridfrom field
                                        $user = backup_getid($restore->backup_unique_code,"user",$dbrec->useridfrom);
                                        if ($user) {
                                            //echo "User ".$dbrec->useridfrom." to user ".$user->new_id."<br />";   //Debug
                                            $dbrec->useridfrom = $user->new_id;
                                        }
                                        //We have to recode the useridto field
                                        $user = backup_getid($restore->backup_unique_code,"user",$dbrec->useridto);
                                        if ($user) {
                                            //echo "User ".$dbrec->useridto." to user ".$user->new_id."<br />";   //Debug
                                            $dbrec->useridto = $user->new_id;
                                        }
                                        //Check if the record doesn't exist in DB!
                                        $exist = get_record('message','useridfrom',$dbrec->useridfrom,
                                                                      'useridto',  $dbrec->useridto,
                                                                      'timecreated',$dbrec->timecreated);
                                        if (!$exist) {
                                            //Not exist. Insert
                                            $status = insert_record('message',$dbrec);
                                        } else {
                                            //Duplicate. Do nothing
                                        }
                                    }
                                    //Do some output
                                    $counter++;
                                    if ($counter % 10 == 0) {
                                        if (!defined('RESTORE_SILENTLY')) {
                                            echo ".";
                                            if ($counter % 200 == 0) {
                                                echo "<br />";
                                            }
                                        }
                                        backup_flush(300);
                                    }
                                }
                            }
                        }
                    }

                    //Process read
                    if ($readcount) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<li>'.get_string('readmessages','message').'</li>';
                        }
                        $counter = 0;
                        while ($counter < $readcount) {
                            //Fetch recordset_size records in each iteration
                            $recs = get_records_select("backup_ids","table_name = 'message_read' AND backup_code = '$restore->backup_unique_code'","old_id","old_id",$counter,$recordset_size);
                            if ($recs) {
                                foreach ($recs as $rec) {
                                    //Get the full record from backup_ids
                                    $data = backup_getid($restore->backup_unique_code,"message_read",$rec->old_id);
                                    if ($data) {
                                        //Now get completed xmlized object
                                        $info = $data->info;
                                        //traverse_xmlize($info);                            //Debug
                                        //print_object ($GLOBALS['traverse_array']);         //Debug
                                        //$GLOBALS['traverse_array']="";                     //Debug
                                        //Now build the MESSAGE_READ record structure
                                        $dbrec->useridfrom = backup_todb($info['MESSAGE']['#']['USERIDFROM']['0']['#']);
                                        $dbrec->useridto = backup_todb($info['MESSAGE']['#']['USERIDTO']['0']['#']);
                                        $dbrec->message = backup_todb($info['MESSAGE']['#']['MESSAGE']['0']['#']);
                                        $dbrec->format = backup_todb($info['MESSAGE']['#']['FORMAT']['0']['#']);
                                        $dbrec->timecreated = backup_todb($info['MESSAGE']['#']['TIMECREATED']['0']['#']);
                                        $dbrec->messagetype = backup_todb($info['MESSAGE']['#']['MESSAGETYPE']['0']['#']);
                                        $dbrec->timeread = backup_todb($info['MESSAGE']['#']['TIMEREAD']['0']['#']);
                                        $dbrec->mailed = backup_todb($info['MESSAGE']['#']['MAILED']['0']['#']);
                                        //We have to recode the useridfrom field
                                        $user = backup_getid($restore->backup_unique_code,"user",$dbrec->useridfrom);
                                        if ($user) {
                                            //echo "User ".$dbrec->useridfrom." to user ".$user->new_id."<br />";   //Debug
                                            $dbrec->useridfrom = $user->new_id;
                                        }
                                        //We have to recode the useridto field
                                        $user = backup_getid($restore->backup_unique_code,"user",$dbrec->useridto);
                                        if ($user) {
                                            //echo "User ".$dbrec->useridto." to user ".$user->new_id."<br />";   //Debug
                                            $dbrec->useridto = $user->new_id;
                                        }
                                        //Check if the record doesn't exist in DB!
                                        $exist = get_record('message_read','useridfrom',$dbrec->useridfrom,
                                                                           'useridto',  $dbrec->useridto,
                                                                           'timecreated',$dbrec->timecreated);
                                        if (!$exist) {
                                            //Not exist. Insert
                                            $status = insert_record('message_read',$dbrec);
                                        } else {
                                            //Duplicate. Do nothing
                                        }
                                    }
                                    //Do some output
                                    $counter++;
                                    if ($counter % 10 == 0) {
                                        if (!defined('RESTORE_SILENTLY')) {
                                            echo ".";
                                            if ($counter % 200 == 0) {
                                                echo "<br />";
                                            }
                                        }
                                        backup_flush(300);
                                    }
                                }
                            }
                        }
                    }

                    //Process contacts
                    if ($contactcount) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<li>'.moodle_strtolower(get_string('contacts','message')).'</li>';
                        }
                        $counter = 0;
                        while ($counter < $contactcount) {
                            //Fetch recordset_size records in each iteration
                            $recs = get_records_select("backup_ids","table_name = 'message_contacts' AND backup_code = '$restore->backup_unique_code'","old_id","old_id",$counter,$recordset_size);
                            if ($recs) {
                                foreach ($recs as $rec) {
                                    //Get the full record from backup_ids
                                    $data = backup_getid($restore->backup_unique_code,"message_contacts",$rec->old_id);
                                    if ($data) {
                                        //Now get completed xmlized object
                                        $info = $data->info;
                                        //traverse_xmlize($info);                            //Debug
                                        //print_object ($GLOBALS['traverse_array']);         //Debug
                                        //$GLOBALS['traverse_array']="";                     //Debug
                                        //Now build the MESSAGE_CONTACTS record structure
                                        $dbrec->userid = backup_todb($info['CONTACT']['#']['USERID']['0']['#']);
                                        $dbrec->contactid = backup_todb($info['CONTACT']['#']['CONTACTID']['0']['#']);
                                        $dbrec->blocked = backup_todb($info['CONTACT']['#']['BLOCKED']['0']['#']);
                                        //We have to recode the userid field
                                        $user = backup_getid($restore->backup_unique_code,"user",$dbrec->userid);
                                        if ($user) {
                                            //echo "User ".$dbrec->userid." to user ".$user->new_id."<br />";   //Debug
                                            $dbrec->userid = $user->new_id;
                                        }
                                        //We have to recode the contactid field
                                        $user = backup_getid($restore->backup_unique_code,"user",$dbrec->contactid);
                                        if ($user) {
                                            //echo "User ".$dbrec->contactid." to user ".$user->new_id."<br />";   //Debug
                                            $dbrec->contactid = $user->new_id;
                                        }
                                        //Check if the record doesn't exist in DB!
                                        $exist = get_record('message_contacts','userid',$dbrec->userid,
                                                                               'contactid',  $dbrec->contactid);
                                        if (!$exist) {
                                            //Not exist. Insert
                                            $status = insert_record('message_contacts',$dbrec);
                                        } else {
                                            //Duplicate. Do nothing
                                        }
                                    }
                                    //Do some output
                                    $counter++;
                                    if ($counter % 10 == 0) {
                                        if (!defined('RESTORE_SILENTLY')) {
                                            echo ".";
                                            if ($counter % 200 == 0) {
                                                echo "<br />";
                                            }
                                        }
                                        backup_flush(300);
                                    }
                                }
                            }
                        }
                    }
                    if (!defined('RESTORE_SILENTLY')) {
                        //End ul
                        echo '</ul>';
                    }
                }
            }
        }

       return $status;
    }

    //This function creates all the structures for blogs and blog tags
    function restore_create_blogs($restore,$xml_file) {

        global $CFG;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            //info will contain the number of blogs in the backup file
            //in backup_ids->info will be the real info (serialized)
            $info = restore_read_xml_blogs($restore,$xml_file);

            //If we have info, then process blogs & blog_tags
            if ($info > 0) {
                //Count how many we have
                $blogcount = count_records ('backup_ids', 'backup_code', $restore->backup_unique_code, 'table_name', 'blog');
                if ($blogcount) {
                    //Number of records to get in every chunk
                    $recordset_size = 4;

                    //Process blog
                    if ($blogcount) {
                        $counter = 0;
                        while ($counter < $blogcount) {
                            //Fetch recordset_size records in each iteration
                            $recs = get_records_select("backup_ids","table_name = 'blog' AND backup_code = '$restore->backup_unique_code'","old_id","old_id",$counter,$recordset_size);
                            if ($recs) {
                                foreach ($recs as $rec) {
                                    //Get the full record from backup_ids
                                    $data = backup_getid($restore->backup_unique_code,"blog",$rec->old_id);
                                    if ($data) {
                                        //Now get completed xmlized object
                                        $info = $data->info;
                                        //traverse_xmlize($info);                            //Debug
                                        //print_object ($GLOBALS['traverse_array']);         //Debug
                                        //$GLOBALS['traverse_array']="";                     //Debug
                                        //Now build the BLOG record structure
                                        $dbrec = new object();
                                        $dbrec->module = backup_todb($info['BLOG']['#']['MODULE']['0']['#']);
                                        $dbrec->userid = backup_todb($info['BLOG']['#']['USERID']['0']['#']);
                                        $dbrec->courseid = backup_todb($info['BLOG']['#']['COURSEID']['0']['#']);
                                        $dbrec->groupid = backup_todb($info['BLOG']['#']['GROUPID']['0']['#']);
                                        $dbrec->moduleid = backup_todb($info['BLOG']['#']['MODULEID']['0']['#']);
                                        $dbrec->coursemoduleid = backup_todb($info['BLOG']['#']['COURSEMODULEID']['0']['#']);
                                        $dbrec->subject = backup_todb($info['BLOG']['#']['SUBJECT']['0']['#']);
                                        $dbrec->summary = backup_todb($info['BLOG']['#']['SUMMARY']['0']['#']);
                                        $dbrec->content = backup_todb($info['BLOG']['#']['CONTENT']['0']['#']);
                                        $dbrec->uniquehash = backup_todb($info['BLOG']['#']['UNIQUEHASH']['0']['#']);
                                        $dbrec->rating = backup_todb($info['BLOG']['#']['RATING']['0']['#']);
                                        $dbrec->format = backup_todb($info['BLOG']['#']['FORMAT']['0']['#']);
                                        $dbrec->attachment = backup_todb($info['BLOG']['#']['ATTACHMENT']['0']['#']);
                                        $dbrec->publishstate = backup_todb($info['BLOG']['#']['PUBLISHSTATE']['0']['#']);
                                        $dbrec->lastmodified = backup_todb($info['BLOG']['#']['LASTMODIFIED']['0']['#']);
                                        $dbrec->created = backup_todb($info['BLOG']['#']['CREATED']['0']['#']);
                                        $dbrec->usermodified = backup_todb($info['BLOG']['#']['USERMODIFIED']['0']['#']);

                                        //We have to recode the userid field
                                        $user = backup_getid($restore->backup_unique_code,"user",$dbrec->userid);
                                        if ($user) {
                                            //echo "User ".$dbrec->userid." to user ".$user->new_id."<br />";   //Debug
                                            $dbrec->userid = $user->new_id;
                                        }

                                        //Check if the record doesn't exist in DB!
                                        $exist = get_record('post','userid', $dbrec->userid,
                                                                   'subject', $dbrec->subject,
                                                                   'created', $dbrec->created);
                                        $newblogid = 0;
                                        if (!$exist) {
                                            //Not exist. Insert
                                            $newblogid = insert_record('post',$dbrec);
                                        }

                                        //Going to restore related tags. Check they are enabled and we have inserted a blog
                                        if ($CFG->usetags && $newblogid) {
                                            //Look for tags in this blog
                                            if (isset($info['BLOG']['#']['BLOG_TAGS']['0']['#']['BLOG_TAG'])) {
                                                $tagsarr = $info['BLOG']['#']['BLOG_TAGS']['0']['#']['BLOG_TAG'];
                                                //Iterate over tags
                                                $tags = array();
                                                for($i = 0; $i < sizeof($tagsarr); $i++) {
                                                    $tag_info = $tagsarr[$i];
                                                    ///traverse_xmlize($tag_info);                        //Debug
                                                    ///print_object ($GLOBALS['traverse_array']);         //Debug
                                                    ///$GLOBALS['traverse_array']="";                     //Debug

                                                    $name = backup_todb($tag_info['#']['NAME']['0']['#']);
                                                    $rawname = backup_todb($tag_info['#']['RAWNAME']['0']['#']);

                                                    $tags[] = $rawname;  //Rawname is all we need
                                                }
                                                tag_set('post', $newblogid, $tags); //Add all the tags in one API call
                                            }
                                        }
                                    }
                                    //Do some output
                                    $counter++;
                                    if ($counter % 10 == 0) {
                                        if (!defined('RESTORE_SILENTLY')) {
                                            echo ".";
                                            if ($counter % 200 == 0) {
                                                echo "<br />";
                                            }
                                        }
                                        backup_flush(300);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $status;
    }

    //This function creates all the categories and questions
    //from xml
    function restore_create_questions($restore,$xml_file) {

        global $CFG, $db;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            //info will contain the old_id of every category
            //in backup_ids->info will be the real info (serialized)
            $info = restore_read_xml_questions($restore,$xml_file);
        }
        //Now, if we have anything in info, we have to restore that
        //categories/questions
        if ($info) {
            if ($info !== true) {
                $status = $status &&  restore_question_categories($info, $restore);
            }
        } else {
            $status = false;
        }
        return $status;
    }

    //This function creates all the scales
    function restore_create_scales($restore,$xml_file) {

        global $CFG, $USER, $db;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            //scales will contain the old_id of every scale
            //in backup_ids->info will be the real info (serialized)
            $scales = restore_read_xml_scales($restore,$xml_file);
        }
        //Now, if we have anything in scales, we have to restore that
        //scales
        if ($scales) {
            if ($scales !== true) {
                //Iterate over each scale
                foreach ($scales as $scale) {
                    //Get record from backup_ids
                    $data = backup_getid($restore->backup_unique_code,"scale",$scale->id);

                    if ($data) {
                        //Now get completed xmlized object
                        $info = $data->info;
                        //traverse_xmlize($info);                                                                     //Debug
                        //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                        //$GLOBALS['traverse_array']="";                                                              //Debug

                        //Now build the SCALE record structure
                        $sca = new object();
                        $sca->courseid = backup_todb($info['SCALE']['#']['COURSEID']['0']['#']);
                        $sca->userid = backup_todb($info['SCALE']['#']['USERID']['0']['#']);
                        $sca->name = backup_todb($info['SCALE']['#']['NAME']['0']['#']);
                        $sca->scale = backup_todb($info['SCALE']['#']['SCALETEXT']['0']['#']);
                        $sca->description = backup_todb($info['SCALE']['#']['DESCRIPTION']['0']['#']);
                        $sca->timemodified = backup_todb($info['SCALE']['#']['TIMEMODIFIED']['0']['#']);

                        // Look for scale (by 'scale' both in standard (course=0) and current course
                        // with priority to standard scales (ORDER clause)
                        // scale is not course unique, use get_record_sql to suppress warning
                        // Going to compare LOB columns so, use the cross-db sql_compare_text() in both sides.
                        $compare_scale_clause = sql_compare_text('scale')  . "=" .  sql_compare_text("'" . $sca->scale . "'");
                        // Scale doesn't exist, create it
                        if (!$sca_db = get_record_sql("SELECT *
                                                         FROM {$CFG->prefix}scale
                                                        WHERE $compare_scale_clause
                                                          AND courseid IN (0, $restore->course_id)
                                                     ORDER BY courseid", true)) {

                            // Try to recode the user field, defaulting to current user if not found
                            $user = backup_getid($restore->backup_unique_code,"user",$sca->userid);
                            if ($user) {
                                $sca->userid = $user->new_id;
                            } else {
                                $sca->userid = $USER->id;
                            }
                            // If scale is standard, if user lacks perms to manage standar scales
                            // 'downgrade' them to course scales
                            if ($sca->courseid == 0 and !has_capability('moodle/course:managescales', get_context_instance(CONTEXT_SYSTEM), $sca->userid)) {
                                $sca->courseid = $restore->course_id;
                            }
                            //The structure is equal to the db, so insert the scale
                            $newid = insert_record ("scale",$sca);

                        // Scale exists, reuse it
                        } else {
                            $newid = $sca_db->id;
                        }

                        if ($newid) {
                            //We have the newid, update backup_ids
                            backup_putid($restore->backup_unique_code,"scale", $scale->id, $newid);
                        }
                    }
                }
            }
        } else {
            $status = false;
        }
        return $status;
    }

    /**
     * Recode group ID field, and set group ID based on restore options.
     * @return object Group object with new_id field.
     */
    function restore_group_getid($restore, $groupid) {
        //We have to recode the groupid field
        $group = backup_getid($restore->backup_unique_code, 'groups', $groupid);
        
        if ($restore->groups == RESTORE_GROUPS_NONE or $restore->groups == RESTORE_GROUPINGS_ONLY) {
            $group->new_id = 0;
        }
        return $group;
    }

    /**
     * Recode grouping ID field, and set grouping ID based on restore options.
     * @return object Group object with new_id field.
     */
    function restore_grouping_getid($restore, $groupingid) {
        //We have to recode the groupid field
        $grouping = backup_getid($restore->backup_unique_code, 'groupings', $groupingid);
        
        if ($restore->groups != RESTORE_GROUPS_GROUPINGS and $restore->groups != RESTORE_GROUPINGS_ONLY) {
            $grouping->new_id = 0;
        }
        return $grouping;
    }

    //This function creates all the groups
    function restore_create_groups($restore,$xml_file) {

        global $CFG;

        //Check it exists
        if (!file_exists($xml_file)) {
            return false;
        }
        //Get info from xml
        if (!$groups = restore_read_xml_groups($restore,$xml_file)) {
            //groups will contain the old_id of every group
            //in backup_ids->info will be the real info (serialized)
            return false;

        } else if ($groups === true) {
            return true;
        }

        $status = true;

        //Iterate over each group
        foreach ($groups as $group) {
            //Get record from backup_ids
            $data = backup_getid($restore->backup_unique_code,"groups",$group->id);

            if ($data) {
                //Now get completed xmlized object
                $info = $data->info;
                //traverse_xmlize($info);                                                                     //Debug
                //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                //$GLOBALS['traverse_array']="";                                                              //Debug
                //Now build the GROUP record structure
                $gro = new Object();
                $gro->courseid         = $restore->course_id;
                $gro->name             = backup_todb($info['GROUP']['#']['NAME']['0']['#']);
                $gro->description      = backup_todb($info['GROUP']['#']['DESCRIPTION']['0']['#']);
                if (isset($info['GROUP']['#']['ENROLMENTKEY']['0']['#'])) {
                    $gro->enrolmentkey = backup_todb($info['GROUP']['#']['ENROLMENTKEY']['0']['#']);
                } else {
                    $gro->enrolmentkey = backup_todb($info['GROUP']['#']['PASSWORD']['0']['#']);
                }
                $gro->picture          = backup_todb($info['GROUP']['#']['PICTURE']['0']['#']);
                $gro->hidepicture      = backup_todb($info['GROUP']['#']['HIDEPICTURE']['0']['#']);
                $gro->timecreated      = backup_todb($info['GROUP']['#']['TIMECREATED']['0']['#']);
                $gro->timemodified     = backup_todb($info['GROUP']['#']['TIMEMODIFIED']['0']['#']);

                //Now search if that group exists (by name and description field) in
                //restore->course_id course
                //Going to compare LOB columns so, use the cross-db sql_compare_text() in both sides.
                $description_clause = '';
                if (!empty($gro->description)) { /// Only for groups having a description
                    $literal_description = "'" . $gro->description . "'";
                    $description_clause = " AND " .
                                          sql_compare_text('description') . " = " .
                                          sql_compare_text($literal_description);
                }
                if (!$gro_db = get_record_sql("SELECT *
                                          FROM {$CFG->prefix}groups
                                          WHERE courseid = $restore->course_id AND
                                                name = '{$gro->name}'" . $description_clause, true)) {
                    //If it doesn't exist, create
                    $newid = insert_record('groups', $gro);

                } else {
                    //get current group id
                    $newid = $gro_db->id;
                }

                if ($newid) {
                    //We have the newid, update backup_ids
                    backup_putid($restore->backup_unique_code,"groups", $group->id, $newid);
                } else {

                    $status = false;
                    continue;
                }

                //Now restore members in the groups_members, only if
                //users are included
                if ($restore->users != 2) {
                   if (!restore_create_groups_members($newid,$info,$restore)) {
                        $status = false;
                   }
                }
            }
        }

        //Now, restore group_files
        if ($status) {
            $status = restore_group_files($restore);
        }

        return $status;
    }

    //This function restores the groups_members
    function restore_create_groups_members($group_id,$info,$restore) {

        if (! isset($info['GROUP']['#']['MEMBERS']['0']['#']['MEMBER'])) {
            //OK, some groups have no members.
            return true;
        }
        //Get the members array
        $members = $info['GROUP']['#']['MEMBERS']['0']['#']['MEMBER'];

        $status = true;

        //Iterate over members
        for($i = 0; $i < sizeof($members); $i++) {
            $mem_info = $members[$i];
            //traverse_xmlize($mem_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the GROUPS_MEMBERS record structure
            $group_member = new Object();
            $group_member->groupid = $group_id;
            $group_member->userid = backup_todb($mem_info['#']['USERID']['0']['#']);
            $group_member->timeadded = backup_todb($mem_info['#']['TIMEADDED']['0']['#']);

            $newid = false;

            //We have to recode the userid field
            if (!$user = backup_getid($restore->backup_unique_code,"user",$group_member->userid)) {
                debugging("group membership can not be restored, user id $group_member->userid not present in backup");
                // do not not block the restore 
                continue;
            }

            $group_member->userid = $user->new_id;

            //The structure is equal to the db, so insert the groups_members
            if (record_exists("groups_members", 'groupid', $group_member->groupid, 'userid', $group_member->userid)) {
                // user already member
            } else if (!insert_record ("groups_members", $group_member)) {
                $status = false;
                continue;
            }

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }
        }

        return $status;
    }

    //This function creates all the groupings
    function restore_create_groupings($restore,$xml_file) {

        //Check it exists
        if (!file_exists($xml_file)) {
            return false;
        }
        //Get info from xml
        if (!$groupings = restore_read_xml_groupings($restore,$xml_file)) {
            return false;

        } else if ($groupings === true) {
            return true;
        }

        $status = true;

        //Iterate over each group
        foreach ($groupings as $grouping) {
            if ($data = backup_getid($restore->backup_unique_code,"groupings",$grouping->id)) {
                //Now get completed xmlized object
                $info = $data->info;
                //Now build the GROUPING record structure
                $gro = new Object();
                ///$gro->id = backup_todb($info['GROUPING']['#']['ID']['0']['#']);
                $gro->courseid    = $restore->course_id;
                $gro->name        = backup_todb($info['GROUPING']['#']['NAME']['0']['#']);
                $gro->description = backup_todb($info['GROUPING']['#']['DESCRIPTION']['0']['#']);
                $gro->configdata  = backup_todb($info['GROUPING']['#']['CONFIGDATA']['0']['#']);
                $gro->timecreated = backup_todb($info['GROUPING']['#']['TIMECREATED']['0']['#']);

                //Now search if that group exists (by name and description field) in
                if ($gro_db = get_record('groupings', 'courseid', $restore->course_id, 'name', $gro->name, 'description', $gro->description)) {
                    //get current group id
                    $newid = $gro_db->id;

                } else {
                    //The structure is equal to the db, so insert the grouping
                    if (!$newid = insert_record('groupings', $gro)) {
                        $status = false;
                        continue;
                    }
                }

                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"groupings",
                             $grouping->id, $newid);
            }
        }


        // now fix the defaultgroupingid in course
        $course = get_record('course', 'id', $restore->course_id);
        if ($course->defaultgroupingid) {
            if ($grouping = restore_grouping_getid($restore, $course->defaultgroupingid)) { 
                set_field('course', 'defaultgroupingid', $grouping->new_id, 'id', $course->id);
            } else {
                set_field('course', 'defaultgroupingid', 0, 'id', $course->id);
            }
        }

        return $status;
    }

    //This function creates all the groupingsgroups
    function restore_create_groupings_groups($restore,$xml_file) {

        //Check it exists
        if (!file_exists($xml_file)) {
            return false;
        }
        //Get info from xml
        if (!$groupingsgroups = restore_read_xml_groupings_groups($restore,$xml_file)) {
            return false;

        } else if ($groupingsgroups === true) {
            return true;
        }

        $status = true;

        //Iterate over each group
        foreach ($groupingsgroups as $groupinggroup) {
            if ($data = backup_getid($restore->backup_unique_code,"groupingsgroups",$groupinggroup->id)) {
                //Now get completed xmlized object
                $info = $data->info;
                //Now build the GROUPING record structure
                $gro_member = new Object();
                $gro_member->groupingid = backup_todb($info['GROUPINGGROUP']['#']['GROUPINGID']['0']['#']);
                $gro_member->groupid    = backup_todb($info['GROUPINGGROUP']['#']['GROUPID']['0']['#']);
                $gro_member->timeadded  = backup_todb($info['GROUPINGGROUP']['#']['TIMEADDED']['0']['#']);

                if (!$grouping = backup_getid($restore->backup_unique_code,"groupings",$gro_member->groupingid)) {
                    $status = false;
                    continue;
                }

                if (!$group = backup_getid($restore->backup_unique_code,"groups",$gro_member->groupid)) {
                    $status = false;
                    continue;
                }

                $gro_member->groupid    = $group->new_id;
                $gro_member->groupingid = $grouping->new_id;
                if (!get_record('groupings_groups', 'groupid', $gro_member->groupid, 'groupingid', $gro_member->groupingid)) {
                    if (!insert_record('groupings_groups', $gro_member)) {
                        $status = false;
                    }
                }
            }
        }

        return $status;
    }

    //This function creates all the course events
    function restore_create_events($restore,$xml_file) {

        global $CFG, $db;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            //events will contain the old_id of every event
            //in backup_ids->info will be the real info (serialized)
            $events = restore_read_xml_events($restore,$xml_file);
        }

        //Get admin->id for later use
        $admin = get_admin();
        $adminid = $admin->id;

        //Now, if we have anything in events, we have to restore that
        //events
        if ($events) {
            if ($events !== true) {
                //Iterate over each event
                foreach ($events as $event) {
                    //Get record from backup_ids
                    $data = backup_getid($restore->backup_unique_code,"event",$event->id);
                    //Init variables
                    $create_event = false;

                    if ($data) {
                        //Now get completed xmlized object
                        $info = $data->info;
                        //traverse_xmlize($info);                                                                     //Debug
                        //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                        //$GLOBALS['traverse_array']="";                                                              //Debug

                        //if necessary, write to restorelog and adjust date/time fields
                        if ($restore->course_startdateoffset) {
                            restore_log_date_changes('Events', $restore, $info['EVENT']['#'], array('TIMESTART'));
                        }

                        //Now build the EVENT record structure
                        $eve->name = backup_todb($info['EVENT']['#']['NAME']['0']['#']);
                        $eve->description = backup_todb($info['EVENT']['#']['DESCRIPTION']['0']['#']);
                        $eve->format = backup_todb($info['EVENT']['#']['FORMAT']['0']['#']);
                        $eve->courseid = $restore->course_id;
                        $eve->groupid = backup_todb($info['EVENT']['#']['GROUPID']['0']['#']);
                        $eve->userid = backup_todb($info['EVENT']['#']['USERID']['0']['#']);
                        $eve->repeatid = backup_todb($info['EVENT']['#']['REPEATID']['0']['#']);
                        $eve->modulename = "";
                        if (!empty($info['EVENT']['#']['MODULENAME'])) {
                            $eve->modulename = backup_todb($info['EVENT']['#']['MODULENAME']['0']['#']);
                        }
                        $eve->instance = 0;
                        $eve->eventtype = backup_todb($info['EVENT']['#']['EVENTTYPE']['0']['#']);
                        $eve->timestart = backup_todb($info['EVENT']['#']['TIMESTART']['0']['#']);
                        $eve->timeduration = backup_todb($info['EVENT']['#']['TIMEDURATION']['0']['#']);
                        $eve->visible = backup_todb($info['EVENT']['#']['VISIBLE']['0']['#']);
                        $eve->timemodified = backup_todb($info['EVENT']['#']['TIMEMODIFIED']['0']['#']);

                        //Now search if that event exists (by name, description, timestart fields) in
                        //restore->course_id course
                        //Going to compare LOB columns so, use the cross-db sql_compare_text() in both sides.
                        $compare_description_clause = sql_compare_text('description')  . "=" .  sql_compare_text("'" . $eve->description . "'");
                        $eve_db = get_record_select("event",
                            "courseid={$eve->courseid} AND name='{$eve->name}' AND $compare_description_clause AND timestart=$eve->timestart");
                        //If it doesn't exist, create
                        if (!$eve_db) {
                            $create_event = true;
                        }
                        //If we must create the event
                        if ($create_event) {

                            //We must recode the userid
                            $user = backup_getid($restore->backup_unique_code,"user",$eve->userid);
                            if ($user) {
                                $eve->userid = $user->new_id;
                            } else {
                                //Assign it to admin
                                $eve->userid = $adminid;
                            }

                            //We have to recode the groupid field
                            $group = backup_getid($restore->backup_unique_code,"groups",$eve->groupid);
                            if ($group) {
                                $eve->groupid = $group->new_id;
                            } else {
                                //Assign it to group 0
                                $eve->groupid = 0;
                            }

                            //The structure is equal to the db, so insert the event
                            $newid = insert_record ("event",$eve);

                            //We must recode the repeatid if the event has it
                            //The repeatid now refers to the id of the original event. (see Bug#5956)
                            if ($newid && !empty($eve->repeatid)) {
                                $repeat_rec = backup_getid($restore->backup_unique_code,"event_repeatid",$eve->repeatid);
                                if ($repeat_rec) {    //Exists, so use it...
                                    $eve->repeatid = $repeat_rec->new_id;
                                } else {              //Doesn't exists, calculate the next and save it
                                    $oldrepeatid = $eve->repeatid;
                                    $eve->repeatid = $newid;
                                    backup_putid($restore->backup_unique_code,"event_repeatid", $oldrepeatid, $eve->repeatid);
                                }
                                $eve->id = $newid;
                                // update the record to contain the correct repeatid
                                update_record('event',$eve);
                            }
                        } else {
                            //get current event id
                            $newid = $eve_db->id;
                        }
                        if ($newid) {
                            //We have the newid, update backup_ids
                            backup_putid($restore->backup_unique_code,"event",
                                         $event->id, $newid);
                        }
                    }
                }
            }
        } else {
            $status = false;
        }
        return $status;
    }

    //This function decode things to make restore multi-site fully functional
    //It does this conversions:
    //    - $@FILEPHP@$ ---|------------> $CFG->wwwroot/file.php/courseid (slasharguments on)
    //                     |------------> $CFG->wwwroot/file.php?file=/courseid (slasharguments off)
    //
    //    - $@SLASH@$ --|---------------> / (slasharguments on)
    //                  |---------------> %2F (slasharguments off)
    //
    //    - $@FORCEDOWNLOAD@$ --|-------> ?forcedownload=1 (slasharguments on)
    //                          |-------> &amp;forcedownload=1(slasharguments off)
    //Note: Inter-activities linking is being implemented as a final
    //step in the restore execution, because we need to have it
    //finished to know all the oldid, newid equivaleces
    function restore_decode_absolute_links($content) {

        global $CFG,$restore;
        require_once($CFG->libdir.'/filelib.php');

    /// MDL-14072: Prevent NULLs, empties and numbers to be processed by the
    /// heavy interlinking. Just a few cpu cycles saved.
        if ($content === NULL) {
            return NULL;
        } else if ($content === '') {
            return '';
        } else if (is_numeric($content)) {
            return $content;
        }

        //Now decode wwwroot and file.php calls
        $search = array ("$@FILEPHP@$");
        $replace = array(get_file_url($restore->course_id));
        $result = str_replace($search,$replace,$content);

        //Now $@SLASH@$ and $@FORCEDOWNLOAD@$ MDL-18799
        $search = array('$@SLASH@$', '$@FORCEDOWNLOAD@$');
        if ($CFG->slasharguments) {
            $replace = array('/', '?forcedownload=1');
        } else {
            $replace = array('%2F', '&amp;forcedownload=1');
        }
        $result = str_replace($search, $replace, $result);

        if ($result != $content && debugging()) {                                  //Debug
            if (!defined('RESTORE_SILENTLY')) {
                echo '<br /><hr />'.s($content).'<br />changed to<br />'.s($result).'<hr /><br />';        //Debug
            }
        }                                                                            //Debug

        return $result;
    }

    //This function restores the userfiles from the temp (user_files) directory to the
    //dataroot/users directory
    function restore_user_files($restore) {

        global $CFG;

        $status = true;

        $counter = 0;

        // 'users' is the old users folder, 'user' is the new one, with a new hierarchy. Detect which one is here and treat accordingly 
        //in CFG->dataroot
        $dest_dir = $CFG->dataroot."/user";
        $status = check_dir_exists($dest_dir,true);

        //Now, we iterate over "user_files" records to check if that user dir must be
        //copied (and renamed) to the "users" dir.
        $rootdir = $CFG->dataroot."/temp/backup/".$restore->backup_unique_code."/user_files";
        
        //Check if directory exists
        $userlist = array();

        if (is_dir($rootdir) && ($list = list_directories ($rootdir))) {
            $counter = 0;
            foreach ($list as $dir) {
                // If there are directories in this folder, we are in the new user hierarchy
                if ($newlist = list_directories("$rootdir/$dir")) {
                    foreach ($newlist as $olduserid) {
                        $userlist[$olduserid] = "$rootdir/$dir/$olduserid";
                    }
                } else {
                    $userlist[$dir] = "$rootdir/$dir";
                }
            }

            foreach ($userlist as $olduserid => $backup_location) { 
                //Look for dir like username in backup_ids
                //If that user exists in backup_ids
                if ($user = backup_getid($restore->backup_unique_code,"user",$olduserid)) {
                    //Only if user has been created now or if it existed previously, but he hasn't got an image (see bug 1123)
                    $newuserdir = make_user_directory($user->new_id, true); // Doesn't create the folder, just returns the location

                    // restore images if new user or image does not exist yet
                    if (!empty($user->new) or !check_dir_exists($newuserdir)) {
                        if (make_user_directory($user->new_id)) { // Creates the folder
                            $status = backup_copy_file($backup_location, $newuserdir, true);
                            $counter ++;
                        }
                        //Do some output
                        if ($counter % 2 == 0) {
                            if (!defined('RESTORE_SILENTLY')) {
                                echo ".";
                                if ($counter % 40 == 0) {
                                    echo "<br />";
                                }
                            }
                            backup_flush(300);
                        }
                    }
                }
            }
        }
        //If status is ok and whe have dirs created, returns counter to inform
        if ($status and $counter) {
            return $counter;
        } else {
            return $status;
        }
    }

    //This function restores the groupfiles from the temp (group_files) directory to the
    //dataroot/groups directory
    function restore_group_files($restore) {

        global $CFG;

        $status = true;

        $counter = 0;

        //First, we check to "groups" exists and create is as necessary
        //in CFG->dataroot
        $dest_dir = $CFG->dataroot.'/groups';
        $status = check_dir_exists($dest_dir,true);

        //Now, we iterate over "group_files" records to check if that user dir must be
        //copied (and renamed) to the "groups" dir.
        $rootdir = $CFG->dataroot."/temp/backup/".$restore->backup_unique_code."/group_files";
        //Check if directory exists
        if (is_dir($rootdir)) {
            $list = list_directories ($rootdir);
            if ($list) {
                //Iterate
                $counter = 0;
                foreach ($list as $dir) {
                    //Look for dir like groupid in backup_ids
                    $data = get_record ("backup_ids","backup_code",$restore->backup_unique_code,
                                                     "table_name","groups",
                                                     "old_id",$dir);
                    //If that group exists in backup_ids
                    if ($data) {
                        if (!file_exists($dest_dir."/".$data->new_id)) {
                            $status = backup_copy_file($rootdir."/".$dir, $dest_dir."/".$data->new_id,true);
                            $counter ++;
                        }
                        //Do some output
                        if ($counter % 2 == 0) {
                            if (!defined('RESTORE_SILENTLY')) {
                                echo ".";
                                if ($counter % 40 == 0) {
                                    echo "<br />";
                                }
                            }
                            backup_flush(300);
                        }
                    }
                }
            }
        }
        //If status is ok and whe have dirs created, returns counter to inform
        if ($status and $counter) {
            return $counter;
        } else {
            return $status;
        }
    }

    //This function restores the course files from the temp (course_files) directory to the
    //dataroot/course_id directory
    function restore_course_files($restore) {

        global $CFG;

        $status = true;

        $counter = 0;

        //First, we check to "course_id" exists and create is as necessary
        //in CFG->dataroot
        $dest_dir = $CFG->dataroot."/".$restore->course_id;
        $status = check_dir_exists($dest_dir,true);

        //Now, we iterate over "course_files" records to check if that file/dir must be
        //copied to the "dest_dir" dir.
        $rootdir = $CFG->dataroot."/temp/backup/".$restore->backup_unique_code."/course_files";
        //Check if directory exists
        if (is_dir($rootdir)) {
            $list = list_directories_and_files ($rootdir);
            if ($list) {
                //Iterate
                $counter = 0;
                foreach ($list as $dir) {
                    //Copy the dir to its new location
                    //Only if destination file/dir doesn exists
                    if (!file_exists($dest_dir."/".$dir)) {
                        $status = backup_copy_file($rootdir."/".$dir,
                                      $dest_dir."/".$dir,true);
                        $counter ++;
                    }
                    //Do some output
                    if ($counter % 2 == 0) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo ".";
                            if ($counter % 40 == 0) {
                                echo "<br />";
                            }
                        }
                        backup_flush(300);
                    }
                }
            }
        }
        //If status is ok and whe have dirs created, returns counter to inform
        if ($status and $counter) {
            return $counter;
        } else {
            return $status;
        }
    }

    //This function restores the site files from the temp (site_files) directory to the
    //dataroot/SITEID directory
    function restore_site_files($restore) {

        global $CFG;

        $status = true;

        $counter = 0;

        //First, we check to "course_id" exists and create is as necessary
        //in CFG->dataroot
        $dest_dir = $CFG->dataroot."/".SITEID;
        $status = check_dir_exists($dest_dir,true);

        //Now, we iterate over "site_files" files to check if that file/dir must be
        //copied to the "dest_dir" dir.
        $rootdir = $CFG->dataroot."/temp/backup/".$restore->backup_unique_code."/site_files";
        //Check if directory exists
        if (is_dir($rootdir)) {
            $list = list_directories_and_files ($rootdir);
            if ($list) {
                //Iterate
                $counter = 0;
                foreach ($list as $dir) {
                    //Avoid copying maintenance.html. MDL-18594
                    if ($dir == 'maintenance.html') {
                       continue;
                    }
                    //Copy the dir to its new location
                    //Only if destination file/dir doesn exists
                    if (!file_exists($dest_dir."/".$dir)) {
                        $status = backup_copy_file($rootdir."/".$dir,
                                      $dest_dir."/".$dir,true);
                        $counter ++;
                    }
                    //Do some output
                    if ($counter % 2 == 0) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo ".";
                            if ($counter % 40 == 0) {
                                echo "<br />";
                            }
                        }
                        backup_flush(300);
                    }
                }
            }
        }
        //If status is ok and whe have dirs created, returns counter to inform
        if ($status and $counter) {
            return $counter;
        } else {
            return $status;
        }
    }


    //This function creates all the structures for every module in backup file
    //Depending what has been selected.
    function restore_create_modules($restore,$xml_file) {

        global $CFG;
        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            //info will contain the id and modtype of every module
            //in backup_ids->info will be the real info (serialized)
            $info = restore_read_xml_modules($restore,$xml_file);
        }
        //Now, if we have anything in info, we have to restore that mods
        //from backup_ids (calling every mod restore function)
        if ($info) {
            if ($info !== true) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo '<ul>';
                }
                //Iterate over each module
                foreach ($info as $mod) {
                    if (empty($restore->mods[$mod->modtype]->granular)  // We don't care about per instance, i.e. restore all instances.
                        || (array_key_exists($mod->id,$restore->mods[$mod->modtype]->instances)
                            && !empty($restore->mods[$mod->modtype]->instances[$mod->id]->restore))) {
                        $modrestore = $mod->modtype."_restore_mods";
                        if (function_exists($modrestore)) {                                               //Debug
                             // we want to restore all mods even when one fails
                             // incorrect code here ignored any errors during module restore in 1.6-1.8
                            $status = $status && $modrestore($mod,$restore);
                        } else {
                            //Something was wrong. Function should exist.
                            $status = false;
                        }
                    }
                }
                if (!defined('RESTORE_SILENTLY')) {
                    echo '</ul>';
                }
            }
        } else {
            $status = false;
        }
       return $status;
    }

    //This function creates all the structures for every log in backup file
    //Depending what has been selected.
    function restore_create_logs($restore,$xml_file) {

        global $CFG,$db;

        //Number of records to get in every chunk
        $recordset_size = 4;
        //Counter, points to current record
        $counter = 0;
        //To count all the recods to restore
        $count_logs = 0;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            //count_logs will contain the number of logs entries to process
            //in backup_ids->info will be the real info (serialized)
            $count_logs = restore_read_xml_logs($restore,$xml_file);
        }

        //Now, if we have records in count_logs, we have to restore that logs
        //from backup_ids. This piece of code makes calls to:
        // - restore_log_course() if it's a course log
        // - restore_log_user() if it's a user log
        // - restore_log_module() if it's a module log.
        //And all is segmented in chunks to allow large recordsets to be restored !!
        if ($count_logs > 0) {
            while ($counter < $count_logs) {
                //Get a chunk of records
                //Take old_id twice to avoid adodb limitation
                $logs = get_records_select("backup_ids","table_name = 'log' AND backup_code = '$restore->backup_unique_code'","old_id","old_id",$counter,$recordset_size);
                //We have logs
                if ($logs) {
                    //Iterate
                    foreach ($logs as $log) {
                        //Get the full record from backup_ids
                        $data = backup_getid($restore->backup_unique_code,"log",$log->old_id);
                        if ($data) {
                            //Now get completed xmlized object
                            $info = $data->info;
                            //traverse_xmlize($info);                                                                     //Debug
                            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                            //$GLOBALS['traverse_array']="";                                                              //Debug
                            //Now build the LOG record structure
                            $dblog = new object();
                            $dblog->time = backup_todb($info['LOG']['#']['TIME']['0']['#']);
                            $dblog->userid = backup_todb($info['LOG']['#']['USERID']['0']['#']);
                            $dblog->ip = backup_todb($info['LOG']['#']['IP']['0']['#']);
                            $dblog->course = $restore->course_id;
                            $dblog->module = backup_todb($info['LOG']['#']['MODULE']['0']['#']);
                            $dblog->cmid = backup_todb($info['LOG']['#']['CMID']['0']['#']);
                            $dblog->action = backup_todb($info['LOG']['#']['ACTION']['0']['#']);
                            $dblog->url = backup_todb($info['LOG']['#']['URL']['0']['#']);
                            $dblog->info = backup_todb($info['LOG']['#']['INFO']['0']['#']);
                            //We have to recode the userid field
                            $user = backup_getid($restore->backup_unique_code,"user",$dblog->userid);
                            if ($user) {
                                //echo "User ".$dblog->userid." to user ".$user->new_id."<br />";                             //Debug
                                $dblog->userid = $user->new_id;
                            }
                            //We have to recode the cmid field (if module isn't "course" or "user")
                            if ($dblog->module != "course" and $dblog->module != "user") {
                                $cm = backup_getid($restore->backup_unique_code,"course_modules",$dblog->cmid);
                                if ($cm) {
                                    //echo "Module ".$dblog->cmid." to module ".$cm->new_id."<br />";                         //Debug
                                    $dblog->cmid = $cm->new_id;
                                } else {
                                    $dblog->cmid = 0;
                                }
                            }
                            //print_object ($dblog);                                                                        //Debug
                            //Now, we redirect to the needed function to make all the work
                            if ($dblog->module == "course") {
                                //It's a course log,
                                $stat = restore_log_course($restore,$dblog);
                            } elseif ($dblog->module == "user") {
                                //It's a user log,
                                $stat = restore_log_user($restore,$dblog);
                            } else {
                                //It's a module log,
                                $stat = restore_log_module($restore,$dblog);
                            }
                        }

                        //Do some output
                        $counter++;
                        if ($counter % 10 == 0) {
                            if (!defined('RESTORE_SILENTLY')) {
                                echo ".";
                                if ($counter % 200 == 0) {
                                    echo "<br />";
                                }
                            }
                            backup_flush(300);
                        }
                    }
                } else {
                    //We never should arrive here
                    $counter = $count_logs;
                    $status = false;
                }
            }
        }

        return $status;
    }

    //This function inserts a course log record, calculating the URL field as necessary
    function restore_log_course($restore,$log) {

        $status = true;
        $toinsert = false;

        //echo "<hr />Before transformations<br />";                                        //Debug
        //print_object($log);                                                           //Debug
        //Depending of the action, we recode different things
        switch ($log->action) {
        case "view":
            $log->url = "view.php?id=".$log->course;
            $log->info = $log->course;
            $toinsert = true;
            break;
        case "guest":
            $log->url = "view.php?id=".$log->course;
            $toinsert = true;
            break;
        case "user report":
            //recode the info field (it's the user id)
            $user = backup_getid($restore->backup_unique_code,"user",$log->info);
            if ($user) {
                $log->info = $user->new_id;
                //Now, extract the mode from the url field
                $mode = substr(strrchr($log->url,"="),1);
                $log->url = "user.php?id=".$log->course."&user=".$log->info."&mode=".$mode;
                $toinsert = true;
            }
            break;
        case "add mod":
            //Extract the course_module from the url field
            $cmid = substr(strrchr($log->url,"="),1);
            //recode the course_module to see it it has been restored
            $cm = backup_getid($restore->backup_unique_code,"course_modules",$cmid);
            if ($cm) {
                $cmid = $cm->new_id;
                //Extract the module name and the module id from the info field
                $modname = strtok($log->info," ");
                $modid = strtok(" ");
                //recode the module id to see if it has been restored
                $mod = backup_getid($restore->backup_unique_code,$modname,$modid);
                if ($mod) {
                    $modid = $mod->new_id;
                    //Now I have everything so reconstruct url and info
                    $log->info = $modname." ".$modid;
                    $log->url = "../mod/".$modname."/view.php?id=".$cmid;
                    $toinsert = true;
                }
            }
            break;
        case "update mod":
            //Extract the course_module from the url field
            $cmid = substr(strrchr($log->url,"="),1);
            //recode the course_module to see it it has been restored
            $cm = backup_getid($restore->backup_unique_code,"course_modules",$cmid);
            if ($cm) {
                $cmid = $cm->new_id;
                //Extract the module name and the module id from the info field
                $modname = strtok($log->info," ");
                $modid = strtok(" ");
                //recode the module id to see if it has been restored
                $mod = backup_getid($restore->backup_unique_code,$modname,$modid);
                if ($mod) {
                    $modid = $mod->new_id;
                    //Now I have everything so reconstruct url and info
                    $log->info = $modname." ".$modid;
                    $log->url = "../mod/".$modname."/view.php?id=".$cmid;
                    $toinsert = true;
                }
            }
            break;
        case "delete mod":
            $log->url = "view.php?id=".$log->course;
            $toinsert = true;
            break;
        case "update":
            $log->url = "edit.php?id=".$log->course;
            $log->info = "";
            $toinsert = true;
            break;
        case "unenrol":
            //recode the info field (it's the user id)
            $user = backup_getid($restore->backup_unique_code,"user",$log->info);
            if ($user) {
                $log->info = $user->new_id;
                $log->url = "view.php?id=".$log->course;
                $toinsert = true;
            }
            break;
        case "enrol":
            //recode the info field (it's the user id)
            $user = backup_getid($restore->backup_unique_code,"user",$log->info);
            if ($user) {
                $log->info = $user->new_id;
                $log->url = "view.php?id=".$log->course;
                $toinsert = true;
            }
            break;
        case "editsection":
            //Extract the course_section from the url field
            $secid = substr(strrchr($log->url,"="),1);
            //recode the course_section to see if it has been restored
            $sec = backup_getid($restore->backup_unique_code,"course_sections",$secid);
            if ($sec) {
                $secid = $sec->new_id;
                //Now I have everything so reconstruct url and info
                $log->url = "editsection.php?id=".$secid;
                $toinsert = true;
            }
            break;
        case "new":
            $log->url = "view.php?id=".$log->course;
            $log->info = "";
            $toinsert = true;
            break;
        case "recent":
            $log->url = "recent.php?id=".$log->course;
            $log->info = "";
            $toinsert = true;
            break;
        case "report log":
            $log->url = "report/log/index.php?id=".$log->course;
            $log->info = $log->course;
            $toinsert = true;
            break;
        case "report live":
            $log->url = "report/log/live.php?id=".$log->course;
            $log->info = $log->course;
            $toinsert = true;
            break;
        case "report outline":
            $log->url = "report/outline/index.php?id=".$log->course;
            $log->info = $log->course;
            $toinsert = true;
            break;
        case "report participation":
            $log->url = "report/participation/index.php?id=".$log->course;
            $log->info = $log->course;
            $toinsert = true;
            break;
        case "report stats":
            $log->url = "report/stats/index.php?id=".$log->course;
            $log->info = $log->course;
            $toinsert = true;
            break;
        default:
            echo "action (".$log->module."-".$log->action.") unknown. Not restored<br />";                 //Debug
            break;
        }

        //echo "After transformations<br />";                                             //Debug
        //print_object($log);                                                           //Debug

        //Now if $toinsert is set, insert the record
        if ($toinsert) {
            //echo "Inserting record<br />";                                              //Debug
            $status = insert_record("log",$log);
        }
        return $status;
    }

    //This function inserts a user log record, calculating the URL field as necessary
    function restore_log_user($restore,$log) {

        $status = true;
        $toinsert = false;

        //echo "<hr />Before transformations<br />";                                        //Debug
        //print_object($log);                                                           //Debug
        //Depending of the action, we recode different things
        switch ($log->action) {
        case "view":
            //recode the info field (it's the user id)
            $user = backup_getid($restore->backup_unique_code,"user",$log->info);
            if ($user) {
                $log->info = $user->new_id;
                $log->url = "view.php?id=".$log->info."&course=".$log->course;
                $toinsert = true;
            }
            break;
        case "change password":
            //recode the info field (it's the user id)
            $user = backup_getid($restore->backup_unique_code,"user",$log->info);
            if ($user) {
                $log->info = $user->new_id;
                $log->url = "view.php?id=".$log->info."&course=".$log->course;
                $toinsert = true;
            }
            break;
        case "login":
            //recode the info field (it's the user id)
            $user = backup_getid($restore->backup_unique_code,"user",$log->info);
            if ($user) {
                $log->info = $user->new_id;
                $log->url = "view.php?id=".$log->info."&course=".$log->course;
                $toinsert = true;
            }
            break;
        case "logout":
            //recode the info field (it's the user id)
            $user = backup_getid($restore->backup_unique_code,"user",$log->info);
            if ($user) {
                $log->info = $user->new_id;
                $log->url = "view.php?id=".$log->info."&course=".$log->course;
                $toinsert = true;
            }
            break;
        case "view all":
            $log->url = "view.php?id=".$log->course;
            $log->info = "";
            $toinsert = true;
        case "update":
            //We split the url by ampersand char
            $first_part = strtok($log->url,"&");
            //Get data after the = char. It's the user being updated
            $userid = substr(strrchr($first_part,"="),1);
            //Recode the user
            $user = backup_getid($restore->backup_unique_code,"user",$userid);
            if ($user) {
                $log->info = "";
                $log->url = "view.php?id=".$user->new_id."&course=".$log->course;
                $toinsert = true;
            }
            break;
        default:
            echo "action (".$log->module."-".$log->action.") unknown. Not restored<br />";                 //Debug
            break;
        }

        //echo "After transformations<br />";                                             //Debug
        //print_object($log);                                                           //Debug

        //Now if $toinsert is set, insert the record
        if ($toinsert) {
            //echo "Inserting record<br />";                                              //Debug
            $status = insert_record("log",$log);
        }
        return $status;
    }

    //This function inserts a module log record, calculating the URL field as necessary
    function restore_log_module($restore,$log) {

        $status = true;
        $toinsert = false;

        //echo "<hr />Before transformations<br />";                                        //Debug
        //print_object($log);                                                           //Debug

        //Now we see if the required function in the module exists
        $function = $log->module."_restore_logs";
        if (function_exists($function)) {
            //Call the function
            $log = $function($restore,$log);
            //If everything is ok, mark the insert flag
            if ($log) {
                $toinsert = true;
            }
        }

        //echo "After transformations<br />";                                             //Debug
        //print_object($log);                                                           //Debug

        //Now if $toinsert is set, insert the record
        if ($toinsert) {
            //echo "Inserting record<br />";                                              //Debug
            $status = insert_record("log",$log);
        }
        return $status;
    }

    //This function adjusts the instance field into course_modules. It's executed after
    //modules restore. There, we KNOW the new instance id !!
    function restore_check_instances($restore) {

        global $CFG;

        $status = true;

        //We are going to iterate over each course_module saved in backup_ids
        $course_modules = get_records_sql("SELECT old_id,new_id
                                           FROM {$CFG->prefix}backup_ids
                                           WHERE backup_code = '$restore->backup_unique_code' AND
                                                 table_name = 'course_modules'");
        if ($course_modules) {
            foreach($course_modules as $cm) {
                //Get full record, using backup_getids
                $cm_module = backup_getid($restore->backup_unique_code,"course_modules",$cm->old_id);
                //Now we are going to the REAL course_modules to get its type (field module)
                $module = get_record("course_modules","id",$cm_module->new_id);
                if ($module) {
                    //We know the module type id. Get the name from modules
                    $type = get_record("modules","id",$module->module);
                    if ($type) {
                        //We know the type name and the old_id. Get its new_id
                        //from backup_ids. It's the instance !!!
                        $instance =  backup_getid($restore->backup_unique_code,$type->name,$cm_module->info);
                        if ($instance) {
                            //We have the new instance, so update the record in course_modules
                            $module->instance = $instance->new_id;
                            //print_object ($module);                             //Debug
                            $status = update_record("course_modules",$module);
                        } else {
                            $status = false;
                        }
                    } else {
                        $status = false;
                    }
                } else {
                    $status = false;
               }
               // MDL-14326 remove empty course modules instance's (credit goes to John T. Macklin from Remote Learner)
               $course_modules_inst_zero = get_records_sql("SELECT id, course, instance
                                           FROM {$CFG->prefix}course_modules
                                           WHERE id = '$cm_module->new_id' AND
                                                 instance = '0'");
                                                 
                    if($course_modules_inst_zero){ // Clean up the invalid instances
                         foreach($course_modules_inst_zero as $course_modules_inst){
                             delete_records('course_modules', 'id',$course_modules_inst->id);
                         }
                    }

            }
        /// Finally, calculate modinfo cache.
            rebuild_course_cache($restore->course_id);
        }


        return $status;
    }

    //=====================================================================================
    //==                                                                                 ==
    //==                         XML Functions (SAX)                                     ==
    //==                                                                                 ==
    //=====================================================================================

    /// This is the class used to split, in first instance, the monolithic moodle.xml into
    /// smaller xml files allowing the MoodleParser later to process only the required info
    /// based in each TODO, instead of processing the whole xml for each TODO. In theory
    /// processing time can be reduced upto 1/20th of original time (depending of the
    /// number of TODOs in the original moodle.xml file)
    ///
    /// Anyway, note it's a general splitter parser, and only needs to be instantiated
    /// with the proper destination dir and the tosplit configuration. Be careful when
    /// using it because it doesn't support XML attributes nor real cdata out from tags.
    /// (both not used in the target Moodle backup files)

    class moodle_splitter_parser {
        var $level = 0;            /// Level we are
        var $tree = array();       /// Array of levels we are
        var $cdata = '';           /// Raw storage for character data
        var $content = '';         /// Content buffer to be printed to file
        var $trailing= '';         /// Content of the trailing tree for each splited file
        var $savepath = null;      /// Path to store splited files
        var $fhandler = null;      /// Current file we are writing to
        var $tosplit = array();    /// Array defining the files we want to split, in this format:
                                   /// array( level/tag/level/tag => filename)
        var $splitwords = array(); /// Denormalised array containing the potential tags
                                   /// being a split point. To speed up check_split_point()
        var $maxsplitlevel = 0;    /// Precalculated max level where any split happens. To speed up check_split_point()
        var $buffersize = 65536;   /// 64KB is a good write buffer. Don't expect big benefits by increasing this.
        var $repectformat = false; /// With this setting enabled, the splited files will look like the original one
                                   /// with all the indentations 100% copied from original (character data outer tags).
                                   /// But this is a waste of time from our perspective, and splited xml files are completely
                                   /// functional without that, so we disable this for production, generating a more compact
                                   /// XML quicker

    /// PHP4 constructor
        function moodle_splitter_parser($savepath, $tosplit = null) {
            return $this->__construct($savepath, $tosplit);
        }

    /// PHP5 constructor
        function __construct($savepath, $tosplit = null) {
            $this->savepath = $savepath;
            if (!empty($tosplit)) {
                $this->tosplit = $tosplit;
            } else { /// No tosplit list passed, process all the possible parts in one moodle.xml file
                $this->tosplit = array(
                                     '1/MOODLE_BACKUP/2/INFO'        => 'split_info.xml',
                                     '1/MOODLE_BACKUP/2/ROLES'       => 'split_roles.xml',
                                     '2/COURSE/3/HEADER'             => 'split_course_header.xml',
                                     '2/COURSE/3/BLOCKS'             => 'split_blocks.xml',
                                     '2/COURSE/3/SECTIONS'           => 'split_sections.xml',
                                     '2/COURSE/3/FORMATDATA'         => 'split_formatdata.xml',
                                     '2/COURSE/3/METACOURSE'         => 'split_metacourse.xml',
                                     '2/COURSE/3/GRADEBOOK'          => 'split_gradebook.xml',
                                     '2/COURSE/3/USERS'              => 'split_users.xml',
                                     '2/COURSE/3/MESSAGES'           => 'split_messages.xml',
                                     '2/COURSE/3/BLOGS'              => 'split_blogs.xml',
                                     '2/COURSE/3/QUESTION_CATEGORIES'=> 'split_questions.xml',
                                     '2/COURSE/3/SCALES'             => 'split_scales.xml',
                                     '2/COURSE/3/GROUPS'             => 'split_groups.xml',
                                     '2/COURSE/3/GROUPINGS'          => 'split_groupings.xml',
                                     '2/COURSE/3/GROUPINGSGROUPS'    => 'split_groupingsgroups.xml',
                                     '2/COURSE/3/EVENTS'             => 'split_events.xml',
                                     '2/COURSE/3/MODULES'            => 'split_modules.xml',
                                     '2/COURSE/3/LOGS'               => 'split_logs.xml'
                                 );
            }
        /// Precalculate some info used to speedup checks
            foreach ($this->tosplit as $key=>$value) {
                $this->splitwords[basename($key)] = true;
                if (((int) basename(dirname($key))) > $this->maxsplitlevel) {
                    $this->maxsplitlevel = (int) basename(dirname($key));
                }
            }
        }

        /// Given one tag being opened, check if it's one split point.
        /// Return false or split filename
        function check_split_point($tag) {
        /// Quick check. Level < 2 cannot be a split point
            if ($this->level < 2) {
                return false;
            }
        /// Quick check. Current tag against potential splitwords
            if (!isset($this->splitwords[$tag])) {
                return false;
            }
        /// Prev test passed, take a look to 2-level tosplit
            $keytocheck = ($this->level - 1) . '/' . $this->tree[$this->level - 1] . '/' . $this->level . '/' . $this->tree[$this->level];
            if (!isset($this->tosplit[$keytocheck])) {
                return false;
            }
        /// Prev test passed, we are in a split point, return new filename
            return $this->tosplit[$keytocheck];
        }

        /// To append data (xml-escaped) to contents buffer
        function character_data($parser, $data) {

            ///$this->content .= preg_replace($this->entity_find, $this->entity_replace, $data); ///40% slower
            ///$this->content .= str_replace($this->entity_find, $this->entity_replace, $data);  ///25% slower
            ///$this->content .= htmlspecialchars($data);                                        ///the best
            /// Instead of htmlspecialchars() each chunk of character data, we are going to
            /// concat it without transformation and will apply the htmlspecialchars() when
            /// that character data is, efectively, going to be added to contents buffer. This
            /// makes the number of transformations to be reduced (speedup) and avoid potential
            /// problems with transformations being applied "in the middle" of multibyte chars.
            $this->cdata .= $data;
        }

        /// To detect start of tags, keeping level, tree and fhandle updated.
        /// Also handles creation of split files
        function start_tag($parser, $tag, $attrs) {

        /// Update things before processing
            $this->level++;
            $this->tree[$this->level] = $tag;

        /// Check if we need to start a new split file,
        /// Speedup: we only do that if we haven't a fhandler and if level <= $maxsplitlevel
            if ($this->level <= $this->maxsplitlevel && !$this->fhandler && $newfilename = $this->check_split_point($tag)) {
            /// Open new file handler, init everything
                $this->fhandler = fopen($this->savepath . '/' . $newfilename, 'w');
                $this->content = '';
                $this->cdata = '';
                $this->trailing = '';
            /// Build the original leading tree (and calculate the original trailing one)
                for ($l = 1; $l < $this->level; $l++) {
                    $this->content .= "<{$this->tree[$l]}>\n";
                    $this->trailing = "\n</{$this->tree[$l]}>" . $this->trailing;
                }
            }
        /// Perform xml-entities transformation and add to contents buffer together with opening tag.
        /// Speedup. We lose nice formatting of the split XML but avoid 50% of transformations and XML is 100% equivalent
            $this->content .= ($this->repectformat ? htmlspecialchars($this->cdata) : '') . "<$tag>";
            $this->cdata = '';
        }

        /// To detect end of tags, keeping level, tree and fhandle updated, writting contents buffer to split file.
        /// Also handles closing of split files
        function end_tag($parser, $tag) {

        /// Perform xml-entities transformation and add to contents buffer together with closing tag, repecting (or no) format
            $this->content .= ($this->repectformat ? htmlspecialchars($this->cdata) : htmlspecialchars(trim($this->cdata))) . "</$tag>";
            $this->cdata = '';

        /// Check if we need to close current split file
        /// Speedup: we only do that if we have a fhandler and if level <= $maxsplitlevel
            if ($this->level <= $this->maxsplitlevel && $this->fhandler && $newfilename = $this->check_split_point($tag)) {
            /// Write pending contents buffer before closing. It's a must
                fwrite($this->fhandler, $this->content);
                $this->content = "";
            /// Write the original trailing tree for fhandler
                fwrite($this->fhandler, $this->trailing);
                fclose($this->fhandler);
                $this->fhandler = null;
            } else {
            /// Normal write of contents (use one buffer to improve speed)
                if ($this->fhandler && strlen($this->content) > $this->buffersize) {
                    fwrite($this->fhandler, $this->content);
                    $this->content = "";
                }
            }

        /// Update things after processing
            $this->tree[$this->level] = "";
            $this->level--;

        }
    }

    /// This function executes the moodle_splitter_parser, causing the monolithic moodle.xml
    /// file to be splitted in n smaller files for better treatament by the MoodleParser in restore_read_xml()
    function restore_split_xml ($xml_file, $preferences) {

        $status = true;

        $xml_parser = xml_parser_create('UTF-8');
        $split_parser = new moodle_splitter_parser(dirname($xml_file));
        xml_set_object($xml_parser,$split_parser);
        xml_set_element_handler($xml_parser, 'start_tag', 'end_tag');
        xml_set_character_data_handler($xml_parser, 'character_data');

        $doteach = filesize($xml_file) / 20;
        $fromdot = 0;

        $fp = fopen($xml_file,"r")
            or $status = false;
        if ($status) {
            $lasttime = time();
            while ($data = fread($fp, 8192)) {
                if (!defined('RESTORE_SILENTLY')) {
                    $fromdot += 8192;
                    if ($fromdot > $doteach) {
                        echo ".";
                        backup_flush(300);
                        $fromdot = 0;
                    }
                    if ((time() - $lasttime) > 10) {
                        $lasttime = time();
                        backup_flush(300);
                    }
                }
                xml_parse($xml_parser, $data, feof($fp))
                    or die(sprintf("XML error: %s at line %d",
                                   xml_error_string(xml_get_error_code($xml_parser)),
                                   xml_get_current_line_number($xml_parser)));
            }
            fclose($fp);
        }
        xml_parser_free($xml_parser);
        return $status;
    }

    //This is the class used to do all the xml parse
    class MoodleParser {

        var $level = 0;        //Level we are
        var $counter = 0;      //Counter
        var $tree = array();   //Array of levels we are
        var $content = "";     //Content under current level
        var $todo = "";        //What we hav to do when parsing
        var $info = "";        //Information collected. Temp storage. Used to return data after parsing.
        var $temp = "";        //Temp storage.
        var $preferences = ""; //Preferences about what to load !!
        var $finished = false; //Flag to say xml_parse to stop

        //This function is used to get the current contents property value
        //They are trimed (and converted from utf8 if needed)
        function getContents() {
            return trim($this->content);
        }

        //This is the startTag handler we use where we are reading the info zone (todo="INFO")
        function startElementInfo($parser, $tagName, $attrs) {
            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //Output something to avoid browser timeouts...
            //backup_flush();

            //Check if we are into INFO zone
            //if ($this->tree[2] == "INFO")                                                             //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";   //Debug
        }

                //This is the startTag handler we use where we are reading the info zone (todo="INFO")
        function startElementRoles($parser, $tagName, $attrs) {
            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //Output something to avoid browser timeouts...
            //backup_flush();

            //Check if we are into INFO zone
            //if ($this->tree[2] == "INFO")                                                             //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";   //Debug
        }


        //This is the startTag handler we use where we are reading the course header zone (todo="COURSE_HEADER")
        function startElementCourseHeader($parser, $tagName, $attrs) {
            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //Output something to avoid browser timeouts...
            //backup_flush();

            //Check if we are into COURSE_HEADER zone
            //if ($this->tree[3] == "HEADER")                                                           //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";   //Debug
        }

        //This is the startTag handler we use where we are reading the blocks zone (todo="BLOCKS")
        function startElementBlocks($parser, $tagName, $attrs) {
            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //Output something to avoid browser timeouts...
            //backup_flush();

            //Check if we are into BLOCKS zone
            //if ($this->tree[3] == "BLOCKS")                                                         //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";   //Debug

            //If we are under a BLOCK tag under a BLOCKS zone, accumule it
            if (isset($this->tree[4]) and isset($this->tree[3])) {  //
                if ($this->tree[4] == "BLOCK" and $this->tree[3] == "BLOCKS") {
                    if (!isset($this->temp)) {
                        $this->temp = "";
                    }
                    $this->temp .= "<".$tagName.">";
                }
            }
        }

        //This is the startTag handler we use where we are reading the sections zone (todo="SECTIONS")
        function startElementSections($parser, $tagName, $attrs) {
            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //Output something to avoid browser timeouts...
            //backup_flush();

            //Check if we are into SECTIONS zone
            //if ($this->tree[3] == "SECTIONS")                                                         //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";   //Debug
        }

        //This is the startTag handler we use where we are reading the optional format data zone (todo="FORMATDATA")
        function startElementFormatData($parser, $tagName, $attrs) {
            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //Output something to avoid browser timeouts...
            //backup_flush();

            //Accumulate all the data inside this tag
            if (isset($this->tree[3]) && $this->tree[3] == "FORMATDATA") {
                if (!isset($this->temp)) {
                    $this->temp = '';
                }
                $this->temp .= "<".$tagName.">";
            }

            //Check if we are into FORMATDATA zone
            //if ($this->tree[3] == "FORMATDATA")                                                         //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";   //Debug
        }

        //This is the startTag handler we use where we are reading the metacourse zone (todo="METACOURSE")
        function startElementMetacourse($parser, $tagName, $attrs) {

            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //Output something to avoid browser timeouts...
            //backup_flush();

            //Check if we are into METACOURSE zone
            //if ($this->tree[3] == "METACOURSE")                                                         //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";   //Debug
        }

        //This is the startTag handler we use where we are reading the gradebook zone (todo="GRADEBOOK")
        function startElementGradebook($parser, $tagName, $attrs) {

            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //Output something to avoid browser timeouts...
            //backup_flush();

            //Check if we are into GRADEBOOK zone
            //if ($this->tree[3] == "GRADEBOOK")                                                         //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";  //Debug

            //If we are under a GRADE_PREFERENCE, GRADE_LETTER or GRADE_CATEGORY tag under a GRADEBOOK zone, accumule it
            if (isset($this->tree[5]) and isset($this->tree[3])) {
                if (($this->tree[5] == "GRADE_ITEM" || $this->tree[5] == "GRADE_CATEGORY" || $this->tree[5] == "GRADE_LETTER" || $this->tree[5] == "GRADE_OUTCOME" || $this->tree[5] == "GRADE_OUTCOMES_COURSE" || $this->tree[5] == "GRADE_CATEGORIES_HISTORY" || $this->tree[5] == "GRADE_GRADES_HISTORY" || $this->tree[5] == "GRADE_TEXT_HISTORY" || $this->tree[5] == "GRADE_ITEM_HISTORY" || $this->tree[5] == "GRADE_OUTCOME_HISTORY") && ($this->tree[3] == "GRADEBOOK")) {

                    if (!isset($this->temp)) {
                        $this->temp = "";
                    }
                    $this->temp .= "<".$tagName.">";
                }
            }
        }

        //This is the startTag handler we use where we are reading the gradebook zone (todo="GRADEBOOK")
        function startElementOldGradebook($parser, $tagName, $attrs) {

            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //Output something to avoid browser timeouts...
            //backup_flush();

            //Check if we are into GRADEBOOK zone
            //if ($this->tree[3] == "GRADEBOOK")                                                         //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";  //Debug

            //If we are under a GRADE_PREFERENCE, GRADE_LETTER or GRADE_CATEGORY tag under a GRADEBOOK zone, accumule it
            if (isset($this->tree[5]) and isset($this->tree[3])) {
                if (($this->tree[5] == "GRADE_PREFERENCE" || $this->tree[5] == "GRADE_LETTER" || $this->tree[5] == "GRADE_CATEGORY" ) && ($this->tree[3] == "GRADEBOOK")) {
                    if (!isset($this->temp)) {
                        $this->temp = "";
                    }
                    $this->temp .= "<".$tagName.">";
                }
            }
        }


        //This is the startTag handler we use where we are reading the user zone (todo="USERS")
        function startElementUsers($parser, $tagName, $attrs) {
            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //Check if we are into USERS zone
            //if ($this->tree[3] == "USERS")                                                            //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";   //Debug
        }

        //This is the startTag handler we use where we are reading the messages zone (todo="MESSAGES")
        function startElementMessages($parser, $tagName, $attrs) {
            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //Output something to avoid browser timeouts...
            //backup_flush();

            //Check if we are into MESSAGES zone
            //if ($this->tree[3] == "MESSAGES")                                                          //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";  //Debug

            //If we are under a MESSAGE tag under a MESSAGES zone, accumule it
            if (isset($this->tree[4]) and isset($this->tree[3])) {
                if (($this->tree[4] == "MESSAGE" || (isset($this->tree[5]) && $this->tree[5] == "CONTACT" )) and ($this->tree[3] == "MESSAGES")) {
                    if (!isset($this->temp)) {
                        $this->temp = "";
                    }
                    $this->temp .= "<".$tagName.">";
                }
            }
        }

        //This is the startTag handler we use where we are reading the blogs zone (todo="BLOGS")
        function startElementBlogs($parser, $tagName, $attrs) {
            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //Output something to avoid browser timeouts...
            //backup_flush();

            //Check if we are into BLOGS zone
            //if ($this->tree[3] == "BLOGS")                                                          //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";  //Debug

            //If we are under a BLOG tag under a BLOGS zone, accumule it
            if (isset($this->tree[4]) and isset($this->tree[3])) {
                if ($this->tree[4] == "BLOG" and $this->tree[3] == "BLOGS") {
                    if (!isset($this->temp)) {
                        $this->temp = "";
                    }
                    $this->temp .= "<".$tagName.">";
                }
            }
        }

        //This is the startTag handler we use where we are reading the questions zone (todo="QUESTIONS")
        function startElementQuestions($parser, $tagName, $attrs) {
            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //if ($tagName == "QUESTION_CATEGORY" && $this->tree[3] == "QUESTION_CATEGORIES") {        //Debug
            //    echo "<P>QUESTION_CATEGORY: ".strftime ("%X",time()),"-";                            //Debug
            //}                                                                                        //Debug

            //Output something to avoid browser timeouts...
            //backup_flush();

            //Check if we are into QUESTION_CATEGORIES zone
            //if ($this->tree[3] == "QUESTION_CATEGORIES")                                              //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";   //Debug

            //If we are under a QUESTION_CATEGORY tag under a QUESTION_CATEGORIES zone, accumule it
            if (isset($this->tree[4]) and isset($this->tree[3])) {
                if (($this->tree[4] == "QUESTION_CATEGORY") and ($this->tree[3] == "QUESTION_CATEGORIES")) {
                    if (!isset($this->temp)) {
                        $this->temp = "";
                    }
                    $this->temp .= "<".$tagName.">";
                }
            }
        }

        //This is the startTag handler we use where we are reading the scales zone (todo="SCALES")
        function startElementScales($parser, $tagName, $attrs) {
            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //if ($tagName == "SCALE" && $this->tree[3] == "SCALES") {                                 //Debug
            //    echo "<P>SCALE: ".strftime ("%X",time()),"-";                                        //Debug
            //}                                                                                        //Debug

            //Output something to avoid browser timeouts...
            //backup_flush();

            //Check if we are into SCALES zone
            //if ($this->tree[3] == "SCALES")                                                           //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";   //Debug

            //If we are under a SCALE tag under a SCALES zone, accumule it
            if (isset($this->tree[4]) and isset($this->tree[3])) {
                if (($this->tree[4] == "SCALE") and ($this->tree[3] == "SCALES")) {
                    if (!isset($this->temp)) {
                        $this->temp = "";
                    }
                    $this->temp .= "<".$tagName.">";
                }
            }
        }

        function startElementGroups($parser, $tagName, $attrs) {
            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //if ($tagName == "GROUP" && $this->tree[3] == "GROUPS") {                                 //Debug
            //    echo "<P>GROUP: ".strftime ("%X",time()),"-";                                        //Debug
            //}                                                                                        //Debug

            //Output something to avoid browser timeouts...
            //backup_flush();

            //Check if we are into GROUPS zone
            //if ($this->tree[3] == "GROUPS")                                                           //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";   //Debug

            //If we are under a GROUP tag under a GROUPS zone, accumule it
            if (isset($this->tree[4]) and isset($this->tree[3])) {
                if (($this->tree[4] == "GROUP") and ($this->tree[3] == "GROUPS")) {
                    if (!isset($this->temp)) {
                        $this->temp = "";
                    }
                    $this->temp .= "<".$tagName.">";
                }
            }
        }

        function startElementGroupings($parser, $tagName, $attrs) {
            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //if ($tagName == "GROUPING" && $this->tree[3] == "GROUPINGS") {                                 //Debug
            //    echo "<P>GROUPING: ".strftime ("%X",time()),"-";                                        //Debug
            //}                                                                                        //Debug

            //Output something to avoid browser timeouts...
            //backup_flush();

            //Check if we are into GROUPINGS zone
            //if ($this->tree[3] == "GROUPINGS")                                                           //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";   //Debug

            //If we are under a GROUPING tag under a GROUPINGS zone, accumule it
            if (isset($this->tree[4]) and isset($this->tree[3])) {
                if (($this->tree[4] == "GROUPING") and ($this->tree[3] == "GROUPINGS")) {
                    if (!isset($this->temp)) {
                        $this->temp = "";
                    }
                    $this->temp .= "<".$tagName.">";
                }
            }
        }

        function startElementGroupingsGroups($parser, $tagName, $attrs) {
            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //if ($tagName == "GROUPINGGROUP" && $this->tree[3] == "GROUPINGSGROUPS") {                                 //Debug
            //    echo "<P>GROUPINGSGROUP: ".strftime ("%X",time()),"-";                                        //Debug
            //}                                                                                        //Debug

            //Output something to avoid browser timeouts...
            backup_flush();

            //Check if we are into GROUPINGSGROUPS zone
            //if ($this->tree[3] == "GROUPINGSGROUPS")                                                           //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";   //Debug

            //If we are under a GROUPINGGROUP tag under a GROUPINGSGROUPS zone, accumule it
            if (isset($this->tree[4]) and isset($this->tree[3])) {
                if (($this->tree[4] == "GROUPINGGROUP") and ($this->tree[3] == "GROUPINGSGROUPS")) {
                    if (!isset($this->temp)) {
                        $this->temp = "";
                    }
                    $this->temp .= "<".$tagName.">";
                }
            }
        }

        //This is the startTag handler we use where we are reading the events zone (todo="EVENTS")
        function startElementEvents($parser, $tagName, $attrs) {
            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //if ($tagName == "EVENT" && $this->tree[3] == "EVENTS") {                                 //Debug
            //    echo "<P>EVENT: ".strftime ("%X",time()),"-";                                        //Debug
            //}                                                                                        //Debug

            //Output something to avoid browser timeouts...
            //backup_flush();

            //Check if we are into EVENTS zone
            //if ($this->tree[3] == "EVENTS")                                                           //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";   //Debug

            //If we are under a EVENT tag under a EVENTS zone, accumule it
            if (isset($this->tree[4]) and isset($this->tree[3])) {
                if (($this->tree[4] == "EVENT") and ($this->tree[3] == "EVENTS")) {
                    if (!isset($this->temp)) {
                        $this->temp = "";
                    }
                    $this->temp .= "<".$tagName.">";
                }
            }
        }

        //This is the startTag handler we use where we are reading the modules zone (todo="MODULES")
        function startElementModules($parser, $tagName, $attrs) {
            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //if ($tagName == "MOD" && $this->tree[3] == "MODULES") {                                     //Debug
            //    echo "<P>MOD: ".strftime ("%X",time()),"-";                                             //Debug
            //}                                                                                           //Debug

            //Output something to avoid browser timeouts...
            //backup_flush();

            //Check if we are into MODULES zone
            //if ($this->tree[3] == "MODULES")                                                          //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";   //Debug

            //If we are under a MOD tag under a MODULES zone, accumule it
            if (isset($this->tree[4]) and isset($this->tree[3])) {
                if (($this->tree[4] == "MOD") and ($this->tree[3] == "MODULES")) {
                    if (!isset($this->temp)) {
                        $this->temp = "";
                    }
                    $this->temp .= "<".$tagName.">";
                }
            }
        }

        //This is the startTag handler we use where we are reading the logs zone (todo="LOGS")
        function startElementLogs($parser, $tagName, $attrs) {
            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //if ($tagName == "LOG" && $this->tree[3] == "LOGS") {                                        //Debug
            //    echo "<P>LOG: ".strftime ("%X",time()),"-";                                             //Debug
            //}                                                                                           //Debug

            //Output something to avoid browser timeouts...
            //backup_flush();

            //Check if we are into LOGS zone
            //if ($this->tree[3] == "LOGS")                                                             //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";   //Debug

            //If we are under a LOG tag under a LOGS zone, accumule it
            if (isset($this->tree[4]) and isset($this->tree[3])) {
                if (($this->tree[4] == "LOG") and ($this->tree[3] == "LOGS")) {
                    if (!isset($this->temp)) {
                        $this->temp = "";
                    }
                    $this->temp .= "<".$tagName.">";
                }
            }
        }

        //This is the startTag default handler we use when todo is undefined
        function startElement($parser, $tagName, $attrs) {
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //Output something to avoid browser timeouts...
            //backup_flush();

            echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";   //Debug
        }

        //This is the endTag handler we use where we are reading the info zone (todo="INFO")
        function endElementInfo($parser, $tagName) {
            //Check if we are into INFO zone
            if ($this->tree[2] == "INFO") {
                //if (trim($this->content))                                                                     //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br />\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br />\n";          //Debug
                //Dependig of different combinations, do different things
                if ($this->level == 3) {
                    switch ($tagName) {
                        case "NAME":
                            $this->info->backup_name = $this->getContents();
                            break;
                        case "MOODLE_VERSION":
                            $this->info->backup_moodle_version = $this->getContents();
                            break;
                        case "MOODLE_RELEASE":
                            $this->info->backup_moodle_release = $this->getContents();
                            break;
                        case "BACKUP_VERSION":
                            $this->info->backup_backup_version = $this->getContents();
                            break;
                        case "BACKUP_RELEASE":
                            $this->info->backup_backup_release = $this->getContents();
                            break;
                        case "DATE":
                            $this->info->backup_date = $this->getContents();
                            break;
                        case "ORIGINAL_WWWROOT":
                            $this->info->original_wwwroot = $this->getContents();
                            break;
                        case "ORIGINAL_SITE_IDENTIFIER_HASH":
                            $this->info->original_siteidentifier = $this->getContents();
                            break;
                        case "MNET_REMOTEUSERS":
                            $this->info->mnet_remoteusers = $this->getContents();
                            break;
                    }
                }
                if ($this->tree[3] == "DETAILS") {
                    if ($this->level == 4) {
                        switch ($tagName) {
                            case "METACOURSE":
                                $this->info->backup_metacourse = $this->getContents();
                                break;
                            case "USERS":
                                $this->info->backup_users = $this->getContents();
                                break;
                            case "LOGS":
                                $this->info->backup_logs = $this->getContents();
                                break;
                            case "USERFILES":
                                $this->info->backup_user_files = $this->getContents();
                                break;
                            case "COURSEFILES":
                                $this->info->backup_course_files = $this->getContents();
                                break;
                            case "SITEFILES":
                                $this->info->backup_site_files = $this->getContents();
                                break;
                            case "GRADEBOOKHISTORIES":
                                $this->info->gradebook_histories = $this->getContents();
                                break;
                            case "MESSAGES":
                                $this->info->backup_messages = $this->getContents();
                                break;
                            case "BLOGS":
                                $this->info->backup_blogs = $this->getContents();
                                break;
                            case 'BLOCKFORMAT':
                                $this->info->backup_block_format = $this->getContents();
                                break;
                        }
                    }
                    if ($this->level == 5) {
                        switch ($tagName) {
                            case "NAME":
                                $this->info->tempName = $this->getContents();
                                break;
                            case "INCLUDED":
                                $this->info->mods[$this->info->tempName]->backup = $this->getContents();
                                break;
                            case "USERINFO":
                                $this->info->mods[$this->info->tempName]->userinfo = $this->getContents();
                                break;
                        }
                    }
                    if ($this->level == 7) {
                        switch ($tagName) {
                            case "ID":
                                $this->info->tempId = $this->getContents();
                                $this->info->mods[$this->info->tempName]->instances[$this->info->tempId]->id = $this->info->tempId;
                                break;
                            case "NAME":
                                $this->info->mods[$this->info->tempName]->instances[$this->info->tempId]->name = $this->getContents();
                                break;
                            case "INCLUDED":
                                $this->info->mods[$this->info->tempName]->instances[$this->info->tempId]->backup = $this->getContents();
                                break;
                            case "USERINFO":
                                $this->info->mods[$this->info->tempName]->instances[$this->info->tempId]->userinfo = $this->getContents();
                                break;
                        }
                    }
                }
            }

            //Stop parsing if todo = INFO and tagName = INFO (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($tagName == "INFO") {
                $this->finished = true;
            }

            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";

        }

        function endElementRoles($parser, $tagName) {
            //Check if we are into ROLES zone
            if ($this->tree[2] == "ROLES") {

                if ($this->tree[3] == "ROLE") {
                    if ($this->level == 4) {
                        switch ($tagName) {
                            case "ID": // this is the old id
                                $this->info->tempid = $this->getContents();
                                $this->info->roles[$this->info->tempid]->id = $this->info->tempid;
                                break;
                            case "NAME":
                                $this->info->roles[$this->info->tempid]->name = $this->getContents();;
                                break;
                            case "SHORTNAME":
                                $this->info->roles[$this->info->tempid]->shortname = $this->getContents();;
                                break;
                            case "NAMEINCOURSE": // custom name of the role in course
                                $this->info->roles[$this->info->tempid]->nameincourse = $this->getContents();;
                                break;
                        }
                    }
                    if ($this->level == 6) {
                        switch ($tagName) {
                            case "NAME":
                                $this->info->tempcapname = $this->getContents();
                                $this->info->roles[$this->info->tempid]->capabilities[$this->info->tempcapname]->name = $this->getContents();
                                break;
                            case "PERMISSION":
                                $this->info->roles[$this->info->tempid]->capabilities[$this->info->tempcapname]->permission = $this->getContents();
                                break;
                            case "TIMEMODIFIED":
                                $this->info->roles[$this->info->tempid]->capabilities[$this->info->tempcapname]->timemodified = $this->getContents();
                                break;
                            case "MODIFIERID":
                                $this->info->roles[$this->info->tempid]->capabilities[$this->info->tempcapname]->modifierid = $this->getContents();
                                break;
                        }
                    }
                }
            }

            //Stop parsing if todo = ROLES and tagName = ROLES (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($tagName == "ROLES") {
                $this->finished = true;
            }

            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";

        }

        //This is the endTag handler we use where we are reading the course_header zone (todo="COURSE_HEADER")
        function endElementCourseHeader($parser, $tagName) {
            //Check if we are into COURSE_HEADER zone
            if ($this->tree[3] == "HEADER") {
                //if (trim($this->content))                                                                     //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br />\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br />\n";          //Debug
                //Dependig of different combinations, do different things
                if ($this->level == 4) {
                    switch ($tagName) {
                        case "ID":
                            $this->info->course_id = $this->getContents();
                            break;
                        case "PASSWORD":
                            $this->info->course_password = $this->getContents();
                            break;
                        case "FULLNAME":
                            $this->info->course_fullname = $this->getContents();
                            break;
                        case "SHORTNAME":
                            $this->info->course_shortname = $this->getContents();
                            break;
                        case "IDNUMBER":
                            $this->info->course_idnumber = $this->getContents();
                            break;
                        case "SUMMARY":
                            $this->info->course_summary = $this->getContents();
                            break;
                        case "FORMAT":
                            $this->info->course_format = $this->getContents();
                            break;
                        case "SHOWGRADES":
                            $this->info->course_showgrades = $this->getContents();
                            break;
                        case "BLOCKINFO":
                            $this->info->blockinfo = $this->getContents();
                            break;
                        case "NEWSITEMS":
                            $this->info->course_newsitems = $this->getContents();
                            break;
                        case "TEACHER":
                            $this->info->course_teacher = $this->getContents();
                            break;
                        case "TEACHERS":
                            $this->info->course_teachers = $this->getContents();
                            break;
                        case "STUDENT":
                            $this->info->course_student = $this->getContents();
                            break;
                        case "STUDENTS":
                            $this->info->course_students = $this->getContents();
                            break;
                        case "GUEST":
                            $this->info->course_guest = $this->getContents();
                            break;
                        case "STARTDATE":
                            $this->info->course_startdate = $this->getContents();
                            break;
                        case "NUMSECTIONS":
                            $this->info->course_numsections = $this->getContents();
                            break;
                        //case "SHOWRECENT":                                          INFO: This is out in 1.3
                        //    $this->info->course_showrecent = $this->getContents();
                        //    break;
                        case "MAXBYTES":
                            $this->info->course_maxbytes = $this->getContents();
                            break;
                        case "SHOWREPORTS":
                            $this->info->course_showreports = $this->getContents();
                            break;
                        case "GROUPMODE":
                            $this->info->course_groupmode = $this->getContents();
                            break;
                        case "GROUPMODEFORCE":
                            $this->info->course_groupmodeforce = $this->getContents();
                            break;
                        case "DEFAULTGROUPINGID":
                            $this->info->course_defaultgroupingid = $this->getContents();
                            break;
                        case "LANG":
                            $this->info->course_lang = $this->getContents();
                            break;
                        case "THEME":
                            $this->info->course_theme = $this->getContents();
                            break;
                        case "COST":
                            $this->info->course_cost = $this->getContents();
                            break;
                        case "CURRENCY":
                            $this->info->course_currency = $this->getContents();
                            break;
                        case "MARKER":
                            $this->info->course_marker = $this->getContents();
                            break;
                        case "VISIBLE":
                            $this->info->course_visible = $this->getContents();
                            break;
                        case "HIDDENSECTIONS":
                            $this->info->course_hiddensections = $this->getContents();
                            break;
                        case "TIMECREATED":
                            $this->info->course_timecreated = $this->getContents();
                            break;
                        case "TIMEMODIFIED":
                            $this->info->course_timemodified = $this->getContents();
                            break;
                        case "METACOURSE":
                            $this->info->course_metacourse = $this->getContents();
                            break;
                        case "EXPIRENOTIFY":
                            $this->info->course_expirynotify = $this->getContents();
                            break;
                        case "NOTIFYSTUDENTS":
                            $this->info->course_notifystudents = $this->getContents();
                            break;
                        case "EXPIRYTHRESHOLD":
                            $this->info->course_expirythreshold = $this->getContents();
                            break;
                        case "ENROLLABLE":
                            $this->info->course_enrollable = $this->getContents();
                            break;
                        case "ENROLSTARTDATE":
                            $this->info->course_enrolstartdate = $this->getContents();
                            break;
                        case "ENROLENDDATE":
                            $this->info->course_enrolenddate = $this->getContents();
                            break;
                        case "ENROLPERIOD":
                            $this->info->course_enrolperiod = $this->getContents();
                            break;
                    }
                }
                if ($this->tree[4] == "CATEGORY") {
                    if ($this->level == 5) {
                        switch ($tagName) {
                            case "ID":
                                $this->info->category->id = $this->getContents();
                                break;
                            case "NAME":
                                $this->info->category->name = $this->getContents();
                                break;
                        }
                    }
                }

                if ($this->tree[4] == "ROLES_ASSIGNMENTS") {
                    if ($this->level == 6) {
                        switch ($tagName) {
                            case "NAME":
                                $this->info->tempname = $this->getContents();
                            break;
                            case "SHORTNAME":
                                $this->info->tempshortname = $this->getContents();
                            break;
                            case "ID":
                                $this->info->tempid = $this->getContents();
                            break;
                        }
                    }

                    if ($this->level == 8) {
                        switch ($tagName) {
                            case "USERID":
                                $this->info->roleassignments[$this->info->tempid]->name = $this->info->tempname;
                                $this->info->roleassignments[$this->info->tempid]->shortname = $this->info->tempshortname;
                                $this->info->tempuser = $this->getContents();
                                $this->info->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->userid = $this->getContents();
                            break;
                            case "HIDDEN":
                                $this->info->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->hidden = $this->getContents();
                            break;
                            case "TIMESTART":
                                $this->info->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->timestart = $this->getContents();
                            break;
                            case "TIMEEND":
                                $this->info->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->timeend = $this->getContents();
                            break;
                            case "TIMEMODIFIED":
                                $this->info->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->timemodified = $this->getContents();
                            break;
                            case "MODIFIERID":
                                $this->info->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->modifierid = $this->getContents();
                            break;
                            case "ENROL":
                                $this->info->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->enrol = $this->getContents();
                            break;
                            case "SORTORDER":
                                $this->info->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->sortorder = $this->getContents();
                            break;

                        }
                    }
                } /// ends role_assignments

                if ($this->tree[4] == "ROLES_OVERRIDES") {
                    if ($this->level == 6) {
                        switch ($tagName) {
                            case "NAME":
                                $this->info->tempname = $this->getContents();
                            break;
                            case "SHORTNAME":
                                $this->info->tempshortname = $this->getContents();
                            break;
                            case "ID":
                                $this->info->tempid = $this->getContents();
                            break;
                        }
                    }

                    if ($this->level == 8) {
                        switch ($tagName) {
                            case "NAME":
                                $this->info->roleoverrides[$this->info->tempid]->name = $this->info->tempname;
                                $this->info->roleoverrides[$this->info->tempid]->shortname = $this->info->tempshortname;
                                $this->info->tempname = $this->getContents(); // change to name of capability
                                $this->info->roleoverrides[$this->info->tempid]->overrides[$this->info->tempname]->name = $this->getContents();
                            break;
                            case "PERMISSION":
                                $this->info->roleoverrides[$this->info->tempid]->overrides[$this->info->tempname]->permission = $this->getContents();
                            break;
                            case "TIMEMODIFIED":
                                $this->info->roleoverrides[$this->info->tempid]->overrides[$this->info->tempname]->timemodified = $this->getContents();
                            break;
                            case "MODIFIERID":
                                $this->info->roleoverrides[$this->info->tempid]->overrides[$this->info->tempname]->modifierid = $this->getContents();
                            break;
                        }
                    }
                } /// ends role_overrides
            }

            //Stop parsing if todo = COURSE_HEADER and tagName = HEADER (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($tagName == "HEADER") {
                $this->finished = true;
            }

            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";

        }

        //This is the endTag handler we use where we are reading the sections zone (todo="BLOCKS")
        function endElementBlocks($parser, $tagName) {
            //Check if we are into BLOCKS zone
            if ($this->tree[3] == 'BLOCKS') {
                //if (trim($this->content))                                                                     //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br />\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br />\n";          //Debug

                // Collect everything into $this->temp
                if (!isset($this->temp)) {
                    $this->temp = "";
                }
                $this->temp .= htmlspecialchars(trim($this->content))."</".$tagName.">";    

                //Dependig of different combinations, do different things
                if ($this->level == 4) {
                    switch ($tagName) {
                        case 'BLOCK':
                            //We've finalized a block, get it
                            $this->info->instances[] = $this->info->tempinstance;
                            unset($this->info->tempinstance);

                            //Also, xmlize INSTANCEDATA and save to db
                            //Prepend XML standard header to info gathered
                            $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                            //Call to xmlize for this portion of xml data (one BLOCK)
                            //echo "-XMLIZE: ".strftime ("%X",time()),"-";                                                //Debug
                            $data = xmlize($xml_data,0);         
                            //echo strftime ("%X",time())."<p>";                                                          //Debug
                            //traverse_xmlize($data);                                                                     //Debug
                            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                            //$GLOBALS['traverse_array']="";                                                              //Debug
                            //Check for instancedata, is exists, then save to DB
                            if (isset($data['BLOCK']['#']['INSTANCEDATA']['0']['#'])) {
                                //Get old id
                                $oldid = $data['BLOCK']['#']['ID']['0']['#'];
                                //Get instancedata
                                
                                if ($data = $data['BLOCK']['#']['INSTANCEDATA']['0']['#']) {
                                    //Restore code calls this multiple times - so might already have the newid
                                    if ($newid = backup_getid($this->preferences->backup_unique_code,'block_instance',$oldid)) {
                                        $newid = $newid->new_id;
                                    } else {
                                        $newid = null;
                                    }
                                    //Save to DB, we will use it later
                                    $status = backup_putid($this->preferences->backup_unique_code,'block_instance',$oldid,$newid,$data);
                                }
                            }
                            //Reset temp
                            unset($this->temp);

                            break;
                        default:
                            die($tagName);
                    }
                }
                if ($this->level == 5) {
                    switch ($tagName) {
                        case 'ID':
                            $this->info->tempinstance->id = $this->getContents();
                        case 'NAME':
                            $this->info->tempinstance->name = $this->getContents();
                            break;
                        case 'PAGEID':
                            $this->info->tempinstance->pageid = $this->getContents();
                            break;
                        case 'PAGETYPE':
                            $this->info->tempinstance->pagetype = $this->getContents();
                            break;
                        case 'POSITION':
                            $this->info->tempinstance->position = $this->getContents();
                            break;
                        case 'WEIGHT':
                            $this->info->tempinstance->weight = $this->getContents();
                            break;
                        case 'VISIBLE':
                            $this->info->tempinstance->visible = $this->getContents();
                            break;
                        case 'CONFIGDATA':
                            $this->info->tempinstance->configdata = $this->getContents();
                            break;
                        default:
                            break;
                    }
                }

                if ($this->tree[5] == "ROLES_ASSIGNMENTS") {
                    if ($this->level == 7) {
                        switch ($tagName) {
                            case "NAME":
                                $this->info->tempname = $this->getContents();
                            break;
                            case "SHORTNAME":
                                $this->info->tempshortname = $this->getContents();
                            break;
                            case "ID":
                                $this->info->tempid = $this->getContents(); // temp roleid
                            break;
                        }
                    }

                    if ($this->level == 9) {

                        switch ($tagName) {
                            case "USERID":
                                $this->info->tempinstance->roleassignments[$this->info->tempid]->name = $this->info->tempname;

                                $this->info->tempinstance->roleassignments[$this->info->tempid]->shortname = $this->info->tempshortname;

                                $this->info->tempuser = $this->getContents();

                                $this->info->tempinstance->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->userid = $this->getContents();
                            break;
                            case "HIDDEN":
                                $this->info->tempinstance->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->hidden = $this->getContents();
                            break;
                            case "TIMESTART":
                                $this->info->tempinstance->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->timestart = $this->getContents();
                            break;
                            case "TIMEEND":
                                $this->info->tempinstance->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->timeend = $this->getContents();
                            break;
                            case "TIMEMODIFIED":
                                $this->info->tempinstance->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->timemodified = $this->getContents();
                            break;
                            case "MODIFIERID":
                                $this->info->tempinstance->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->modifierid = $this->getContents();
                            break;
                            case "ENROL":
                                $this->info->tempinstance->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->enrol = $this->getContents();
                            break;
                            case "SORTORDER":
                                $this->info->tempinstance->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->sortorder = $this->getContents();
                            break;

                        }
                    }
                } /// ends role_assignments

                if ($this->tree[5] == "ROLES_OVERRIDES") {
                    if ($this->level == 7) {
                        switch ($tagName) {
                            case "NAME":
                                $this->info->tempname = $this->getContents();
                            break;
                            case "SHORTNAME":
                                $this->info->tempshortname = $this->getContents();
                            break;
                            case "ID":
                                $this->info->tempid = $this->getContents(); // temp roleid
                            break;
                        }
                    }

                    if ($this->level == 9) {
                        switch ($tagName) {
                            case "NAME":

                                $this->info->tempinstance->roleoverrides[$this->info->tempid]->name = $this->info->tempname;
                                $this->info->tempinstance->roleoverrides[$this->info->tempid]->shortname = $this->info->tempshortname;
                                $this->info->tempname = $this->getContents(); // change to name of capability
                                $this->info->tempinstance->roleoverrides[$this->info->tempid]->overrides[$this->info->tempname]->name = $this->getContents();
                            break;
                            case "PERMISSION":
                                $this->info->tempinstance->roleoverrides[$this->info->tempid]->overrides[$this->info->tempname]->permission = $this->getContents();
                            break;
                            case "TIMEMODIFIED":
                                $this->info->tempinstance->roleoverrides[$this->info->tempid]->overrides[$this->info->tempname]->timemodified = $this->getContents();
                            break;
                            case "MODIFIERID":
                                $this->info->tempinstance->roleoverrides[$this->info->tempid]->overrides[$this->info->tempname]->modifierid = $this->getContents();
                            break;
                        }
                    }
                } /// ends role_overrides
            }

            //Stop parsing if todo = BLOCKS and tagName = BLOCKS (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            //WARNING: ONLY EXIT IF todo = BLOCKS (thus tree[3] = "BLOCKS") OTHERWISE
            //         THE BLOCKS TAG IN THE HEADER WILL TERMINATE US!
            if ($this->tree[3] == 'BLOCKS' && $tagName == 'BLOCKS') {
                $this->finished = true;
            }

            //Clear things
            $this->tree[$this->level] = '';
            $this->level--;
            $this->content = "";
        }

        //This is the endTag handler we use where we are reading the sections zone (todo="SECTIONS")
        function endElementSections($parser, $tagName) {
            //Check if we are into SECTIONS zone
            if ($this->tree[3] == "SECTIONS") {
                //if (trim($this->content))                                                                     //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br />\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br />\n";          //Debug
                //Dependig of different combinations, do different things
                if ($this->level == 4) {
                    switch ($tagName) {
                        case "SECTION":
                            //We've finalized a section, get it
                            $this->info->sections[$this->info->tempsection->id] = $this->info->tempsection;
                            unset($this->info->tempsection);
                    }
                }
                if ($this->level == 5) {
                    switch ($tagName) {
                        case "ID":
                            $this->info->tempsection->id = $this->getContents();
                            break;
                        case "NUMBER":
                            $this->info->tempsection->number = $this->getContents();
                            break;
                        case "SUMMARY":
                            $this->info->tempsection->summary = $this->getContents();
                            break;
                        case "VISIBLE":
                            $this->info->tempsection->visible = $this->getContents();
                            break;
                    }
                }
                if ($this->level == 6) {
                    switch ($tagName) {
                        case "MOD":
                            if (!isset($this->info->tempmod->groupmode)) {
                                $this->info->tempmod->groupmode = 0;
                            }
                            if (!isset($this->info->tempmod->groupingid)) {
                                $this->info->tempmod->groupingid = 0;
                            }
                            if (!isset($this->info->tempmod->groupmembersonly)) {
                                $this->info->tempmod->groupmembersonly = 0;
                            }
                            if (!isset($this->info->tempmod->idnumber)) {
                                $this->info->tempmod->idnumber = null;
                            }

                            //We've finalized a mod, get it
                            $this->info->tempsection->mods[$this->info->tempmod->id]->type =
                                $this->info->tempmod->type;
                            $this->info->tempsection->mods[$this->info->tempmod->id]->instance =
                                $this->info->tempmod->instance;
                            $this->info->tempsection->mods[$this->info->tempmod->id]->added =
                                $this->info->tempmod->added;
                            $this->info->tempsection->mods[$this->info->tempmod->id]->score =
                                $this->info->tempmod->score;
                            $this->info->tempsection->mods[$this->info->tempmod->id]->indent =
                                $this->info->tempmod->indent;
                            $this->info->tempsection->mods[$this->info->tempmod->id]->visible =
                                $this->info->tempmod->visible;
                            $this->info->tempsection->mods[$this->info->tempmod->id]->groupmode =
                                $this->info->tempmod->groupmode;
                            $this->info->tempsection->mods[$this->info->tempmod->id]->groupingid =
                                $this->info->tempmod->groupingid;
                            $this->info->tempsection->mods[$this->info->tempmod->id]->groupmembersonly =
                                $this->info->tempmod->groupmembersonly;
                            $this->info->tempsection->mods[$this->info->tempmod->id]->idnumber =
                                $this->info->tempmod->idnumber;

                            unset($this->info->tempmod);
                    }
                }
                if ($this->level == 7) {
                    switch ($tagName) {
                        case "ID":
                            $this->info->tempmod->id = $this->getContents();
                            break;
                        case "TYPE":
                            $this->info->tempmod->type = $this->getContents();
                            break;
                        case "INSTANCE":
                            $this->info->tempmod->instance = $this->getContents();
                            break;
                        case "ADDED":
                            $this->info->tempmod->added = $this->getContents();
                            break;
                        case "SCORE":
                            $this->info->tempmod->score = $this->getContents();
                            break;
                        case "INDENT":
                            $this->info->tempmod->indent = $this->getContents();
                            break;
                        case "VISIBLE":
                            $this->info->tempmod->visible = $this->getContents();
                            break;
                        case "GROUPMODE":
                            $this->info->tempmod->groupmode = $this->getContents();
                            break;
                        case "GROUPINGID":
                            $this->info->tempmod->groupingid = $this->getContents();
                            break;
                        case "GROUPMEMBERSONLY":
                            $this->info->tempmod->groupmembersonly = $this->getContents();
                            break;
                        case "IDNUMBER":
                            $this->info->tempmod->idnumber = $this->getContents();
                            break;
                        default:
                            break;
                    }
                }

                if (isset($this->tree[7]) && $this->tree[7] == "ROLES_ASSIGNMENTS") {

                    if ($this->level == 9) {
                        switch ($tagName) {
                            case "NAME":
                                $this->info->tempname = $this->getContents();
                            break;
                            case "SHORTNAME":
                                $this->info->tempshortname = $this->getContents();
                            break;
                            case "ID":
                                $this->info->tempid = $this->getContents(); // temp roleid
                            break;
                        }

                    }
                    if ($this->level == 11) {
                        switch ($tagName) {
                            case "USERID":
                                $this->info->tempsection->mods[$this->info->tempmod->id]->roleassignments[$this->info->tempid]->name = $this->info->tempname;

                                $this->info->tempsection->mods[$this->info->tempmod->id]->roleassignments[$this->info->tempid]->shortname = $this->info->tempshortname;

                                $this->info->tempuser = $this->getContents();

                                $this->info->tempsection->mods[$this->info->tempmod->id]->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->userid = $this->getContents();
                            break;
                            case "HIDDEN":
                                $this->info->tempsection->mods[$this->info->tempmod->id]->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->hidden = $this->getContents();
                            break;
                            case "TIMESTART":
                                $this->info->tempsection->mods[$this->info->tempmod->id]->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->timestart = $this->getContents();
                            break;
                            case "TIMEEND":
                                $this->info->tempsection->mods[$this->info->tempmod->id]->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->timeend = $this->getContents();
                            break;
                            case "TIMEMODIFIED":
                                $this->info->tempsection->mods[$this->info->tempmod->id]->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->timemodified = $this->getContents();
                            break;
                            case "MODIFIERID":
                                $this->info->tempsection->mods[$this->info->tempmod->id]->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->modifierid = $this->getContents();
                            break;
                            case "ENROL":
                                $this->info->tempsection->mods[$this->info->tempmod->id]->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->enrol = $this->getContents();
                            break;
                            case "SORTORDER":
                                $this->info->tempsection->mods[$this->info->tempmod->id]->roleassignments[$this->info->tempid]->assignments[$this->info->tempuser]->sortorder = $this->getContents();
                            break;

                        }
                    }
                } /// ends role_assignments

                if (isset($this->tree[7]) && $this->tree[7] == "ROLES_OVERRIDES") {
                    if ($this->level == 9) {
                        switch ($tagName) {
                            case "NAME":
                                $this->info->tempname = $this->getContents();
                            break;
                            case "SHORTNAME":
                                $this->info->tempshortname = $this->getContents();
                            break;
                            case "ID":
                                $this->info->tempid = $this->getContents(); // temp roleid
                            break;
                        }
                    }

                    if ($this->level == 11) {
                        switch ($tagName) {
                            case "NAME":

                                $this->info->tempsection->mods[$this->info->tempmod->id]->roleoverrides[$this->info->tempid]->name = $this->info->tempname;
                                $this->info->tempsection->mods[$this->info->tempmod->id]->roleoverrides[$this->info->tempid]->shortname = $this->info->tempshortname;
                                $this->info->tempname = $this->getContents(); // change to name of capability
                                $this->info->tempsection->mods[$this->info->tempmod->id]->roleoverrides[$this->info->tempid]->overrides[$this->info->tempname]->name = $this->getContents();
                            break;
                            case "PERMISSION":
                                $this->info->tempsection->mods[$this->info->tempmod->id]->roleoverrides[$this->info->tempid]->overrides[$this->info->tempname]->permission = $this->getContents();
                            break;
                            case "TIMEMODIFIED":
                                $this->info->tempsection->mods[$this->info->tempmod->id]->roleoverrides[$this->info->tempid]->overrides[$this->info->tempname]->timemodified = $this->getContents();
                            break;
                            case "MODIFIERID":
                                $this->info->tempsection->mods[$this->info->tempmod->id]->roleoverrides[$this->info->tempid]->overrides[$this->info->tempname]->modifierid = $this->getContents();
                            break;
                        }
                    }
                } /// ends role_overrides

            }

            //Stop parsing if todo = SECTIONS and tagName = SECTIONS (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($tagName == "SECTIONS") {
                $this->finished = true;
            }

            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";

        }

        //This is the endTag handler we use where we are reading the optional format data zone (todo="FORMATDATA")
        function endElementFormatData($parser, $tagName) {
            //Check if we are into FORMATDATA zone
            if ($this->tree[3] == 'FORMATDATA') {
                if (!isset($this->temp)) {
                    $this->temp = '';
                }
                $this->temp .= htmlspecialchars(trim($this->content))."</".$tagName.">";
            }

            if($tagName=='FORMATDATA') {
                //Did we have any data? If not don't bother
                if($this->temp!='<FORMATDATA></FORMATDATA>') {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    $this->temp='';

                    //Call to xmlize for this portion of xml data (the FORMATDATA block)
                    $this->info->format_data = xmlize($xml_data,0);
                }
                //Stop parsing at end of FORMATDATA
                $this->finished=true;
            }

            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";
        }

        //This is the endTag handler we use where we are reading the metacourse zone (todo="METACOURSE")
        function endElementMetacourse($parser, $tagName) {
            //Check if we are into METACOURSE zone
            if ($this->tree[3] == 'METACOURSE') {
                //if (trim($this->content))                                                                     //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br />\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br />\n";          //Debug
                //Dependig of different combinations, do different things
                if ($this->level == 5) {
                    switch ($tagName) {
                        case 'CHILD':
                            //We've finalized a child, get it
                            $this->info->childs[] = $this->info->tempmeta;
                            unset($this->info->tempmeta);
                            break;
                        case 'PARENT':
                            //We've finalized a parent, get it
                            $this->info->parents[] = $this->info->tempmeta;
                            unset($this->info->tempmeta);
                            break;
                        default:
                            die($tagName);
                    }
                }
                if ($this->level == 6) {
                    switch ($tagName) {
                        case 'ID':
                            $this->info->tempmeta->id = $this->getContents();
                            break;
                        case 'IDNUMBER':
                            $this->info->tempmeta->idnumber = $this->getContents();
                            break;
                        case 'SHORTNAME':
                            $this->info->tempmeta->shortname = $this->getContents();
                            break;
                    }
                }
            }

            //Stop parsing if todo = METACOURSE and tagName = METACOURSE (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($this->tree[3] == 'METACOURSE' && $tagName == 'METACOURSE') {
                $this->finished = true;
            }

            //Clear things
            $this->tree[$this->level] = '';
            $this->level--;
            $this->content = "";
        }

        //This is the endTag handler we use where we are reading the gradebook zone (todo="GRADEBOOK")
        function endElementGradebook($parser, $tagName) {
            //Check if we are into GRADEBOOK zone
            if ($this->tree[3] == "GRADEBOOK") {
                //if (trim($this->content))                                                             //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br />\n"; //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br />\n";//Debug
                //Acumulate data to info (content + close tag)
                //Reconvert: strip htmlchars again and trim to generate xml data
                if (!isset($this->temp)) {
                    $this->temp = "";
                }
                $this->temp .= htmlspecialchars(trim($this->content))."</".$tagName.">";
                // We have finished outcome, grade_category or grade_item, reset accumulated
                // data because they are close tags
                if ($this->level == 4) {
                    $this->temp = "";
                }
                //If we've finished a grade item, xmlize it an save to db
                if (($this->level == 5) and ($tagName == "GRADE_ITEM")) {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    //Call to xmlize for this portion of xml data (one PREFERENCE)
                    //echo "-XMLIZE: ".strftime ("%X",time()),"-";                                    //Debug
                    $data = xmlize($xml_data,0);
                    $item_id = $data["GRADE_ITEM"]["#"]["ID"]["0"]["#"];
                    $this->counter++;
                    //Save to db

                    $status = backup_putid($this->preferences->backup_unique_code, 'grade_items', $item_id,
                                           null,$data);
                    //Create returning info
                    $this->info = $this->counter;
                    //Reset temp

                    unset($this->temp);
                }

                //If we've finished a grade_category, xmlize it an save to db
                if (($this->level == 5) and ($tagName == "GRADE_CATEGORY")) {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    //Call to xmlize for this portion of xml data (one CATECORY)
                    //echo "-XMLIZE: ".strftime ("%X",time()),"-";                                    //Debug
                    $data = xmlize($xml_data,0);
                    $category_id = $data["GRADE_CATEGORY"]["#"]["ID"]["0"]["#"];
                    $this->counter++;
                    //Save to db
                    $status = backup_putid($this->preferences->backup_unique_code, 'grade_categories' ,$category_id,
                                           null,$data);
                    //Create returning info
                    $this->info = $this->counter;
                    //Reset temp
                    unset($this->temp);
                }
                
                if (($this->level == 5) and ($tagName == "GRADE_LETTER")) {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    //Call to xmlize for this portion of xml data (one CATECORY)
                    //echo "-XMLIZE: ".strftime ("%X",time()),"-";                                    //Debug
                    $data = xmlize($xml_data,0);
                    $letter_id = $data["GRADE_LETTER"]["#"]["ID"]["0"]["#"];
                    $this->counter++;
                    //Save to db
                    $status = backup_putid($this->preferences->backup_unique_code, 'grade_letters' ,$letter_id,
                                           null,$data);
                    //Create returning info
                    $this->info = $this->counter;
                    //Reset temp
                    unset($this->temp);
                }

                //If we've finished a grade_outcome, xmlize it an save to db
                if (($this->level == 5) and ($tagName == "GRADE_OUTCOME")) {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    //Call to xmlize for this portion of xml data (one CATECORY)
                    //echo "-XMLIZE: ".strftime ("%X",time()),"-";                                    //Debug
                    $data = xmlize($xml_data,0);
                    $outcome_id = $data["GRADE_OUTCOME"]["#"]["ID"]["0"]["#"];
                    $this->counter++;
                    //Save to db
                    $status = backup_putid($this->preferences->backup_unique_code, 'grade_outcomes' ,$outcome_id,
                                           null,$data);
                    //Create returning info
                    $this->info = $this->counter;
                    //Reset temp
                    unset($this->temp);
                }

                //If we've finished a grade_outcomes_course, xmlize it an save to db
                if (($this->level == 5) and ($tagName == "GRADE_OUTCOMES_COURSE")) {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    //Call to xmlize for this portion of xml data (one CATECORY)
                    //echo "-XMLIZE: ".strftime ("%X",time()),"-";                                    //Debug
                    $data = xmlize($xml_data,0);
                    $outcomes_course_id = $data["GRADE_OUTCOMES_COURSE"]["#"]["ID"]["0"]["#"];
                    $this->counter++;
                    //Save to db
                    $status = backup_putid($this->preferences->backup_unique_code, 'grade_outcomes_courses' ,$outcomes_course_id,
                                           null,$data);
                    //Create returning info
                    $this->info = $this->counter;
                    //Reset temp
                    unset($this->temp);
                }

                if (($this->level == 5) and ($tagName == "GRADE_CATEGORIES_HISTORY")) {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    //Call to xmlize for this portion of xml data (one PREFERENCE)
                    //echo "-XMLIZE: ".strftime ("%X",time()),"-";                                    //Debug
                    $data = xmlize($xml_data,0);
                    $id = $data["GRADE_CATEGORIES_HISTORY"]["#"]["ID"]["0"]["#"];
                    $this->counter++;
                    //Save to db

                    $status = backup_putid($this->preferences->backup_unique_code, 'grade_categories_history', $id,
                                           null,$data);
                    //Create returning info
                    $this->info = $this->counter;
                    //Reset temp

                    unset($this->temp);
                }

                if (($this->level == 5) and ($tagName == "GRADE_GRADES_HISTORY")) {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    //Call to xmlize for this portion of xml data (one PREFERENCE)
                    //echo "-XMLIZE: ".strftime ("%X",time()),"-";                                    //Debug
                    $data = xmlize($xml_data,0);
                    $id = $data["GRADE_GRADES_HISTORY"]["#"]["ID"]["0"]["#"];
                    $this->counter++;
                    //Save to db

                    $status = backup_putid($this->preferences->backup_unique_code, 'grade_grades_history', $id,
                                           null,$data);
                    //Create returning info
                    $this->info = $this->counter;
                    //Reset temp

                    unset($this->temp);
                }

                if (($this->level == 5) and ($tagName == "GRADE_ITEM_HISTORY")) {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    //Call to xmlize for this portion of xml data (one PREFERENCE)
                    //echo "-XMLIZE: ".strftime ("%X",time()),"-";                                    //Debug
                    $data = xmlize($xml_data,0);
                    $id = $data["GRADE_ITEM_HISTORY"]["#"]["ID"]["0"]["#"];
                    $this->counter++;
                    //Save to db

                    $status = backup_putid($this->preferences->backup_unique_code, 'grade_items_history', $id,
                                           null,$data);
                    //Create returning info
                    $this->info = $this->counter;
                    //Reset temp

                    unset($this->temp);
                }

                if (($this->level == 5) and ($tagName == "GRADE_OUTCOME_HISTORY")) {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    //Call to xmlize for this portion of xml data (one PREFERENCE)
                    //echo "-XMLIZE: ".strftime ("%X",time()),"-";                                    //Debug
                    $data = xmlize($xml_data,0);
                    $id = $data["GRADE_OUTCOME_HISTORY"]["#"]["ID"]["0"]["#"];
                    $this->counter++;
                    //Save to db

                    $status = backup_putid($this->preferences->backup_unique_code, 'grade_outcomes_history', $id,
                                           null,$data);
                    //Create returning info
                    $this->info = $this->counter;
                    //Reset temp

                    unset($this->temp);
                }
            }

            //Stop parsing if todo = GRADEBOOK and tagName = GRADEBOOK (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($tagName == "GRADEBOOK" and $this->level == 3) {
                $this->finished = true;
                $this->counter = 0;
            }

            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";

        }

        //This is the endTag handler we use where we are reading the gradebook zone (todo="GRADEBOOK")
        function endElementOldGradebook($parser, $tagName) {
            //Check if we are into GRADEBOOK zone
            if ($this->tree[3] == "GRADEBOOK") {
                //if (trim($this->content))                                                             //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br />\n"; //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br />\n";//Debug
                //Acumulate data to info (content + close tag)
                //Reconvert: strip htmlchars again and trim to generate xml data
                if (!isset($this->temp)) {
                    $this->temp = "";
                }
                $this->temp .= htmlspecialchars(trim($this->content))."</".$tagName.">";
                //We have finished preferences, letters or categories, reset accumulated
                //data because they are close tags
                if ($this->level == 4) {
                    $this->temp = "";
                }
                //If we've finished a message, xmlize it an save to db
                if (($this->level == 5) and ($tagName == "GRADE_PREFERENCE")) {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    //Call to xmlize for this portion of xml data (one PREFERENCE)
                    //echo "-XMLIZE: ".strftime ("%X",time()),"-";                                    //Debug
                    $data = xmlize($xml_data,0);
                    //echo strftime ("%X",time())."<p>";                                              //Debug
                    //traverse_xmlize($data);                                                         //Debug
                    //print_object ($GLOBALS['traverse_array']);                                      //Debug
                    //$GLOBALS['traverse_array']="";                                                  //Debug
                    //Now, save data to db. We'll use it later
                    //Get id and status from data
                    $preference_id = $data["GRADE_PREFERENCE"]["#"]["ID"]["0"]["#"];
                    $this->counter++;
                    //Save to db
                    $status = backup_putid($this->preferences->backup_unique_code, 'grade_preferences', $preference_id, 
                                           null,$data);
                    //Create returning info
                    $this->info = $this->counter;
                    //Reset temp
                    unset($this->temp);
                }
                //If we've finished a grade_letter, xmlize it an save to db
                if (($this->level == 5) and ($tagName == "GRADE_LETTER")) {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    //Call to xmlize for this portion of xml data (one LETTER)
                    //echo "-XMLIZE: ".strftime ("%X",time()),"-";                                    //Debug
                    $data = xmlize($xml_data,0);
                    //echo strftime ("%X",time())."<p>";                                              //Debug
                    //traverse_xmlize($data);                                                         //Debug
                    //print_object ($GLOBALS['traverse_array']);                                      //Debug
                    //$GLOBALS['traverse_array']="";                                                  //Debug
                    //Now, save data to db. We'll use it later
                    //Get id and status from data
                    $letter_id = $data["GRADE_LETTER"]["#"]["ID"]["0"]["#"];
                    $this->counter++;
                    //Save to db
                    $status = backup_putid($this->preferences->backup_unique_code, 'grade_letter' ,$letter_id, 
                                           null,$data);
                    //Create returning info
                    $this->info = $this->counter;
                    //Reset temp
                    unset($this->temp);
                }

                //If we've finished a grade_category, xmlize it an save to db
                if (($this->level == 5) and ($tagName == "GRADE_CATEGORY")) {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    //Call to xmlize for this portion of xml data (one CATECORY)
                    //echo "-XMLIZE: ".strftime ("%X",time()),"-";                                    //Debug
                    $data = xmlize($xml_data,0);
                    //echo strftime ("%X",time())."<p>";                                              //Debug
                    //traverse_xmlize($data);                                                         //Debug
                    //print_object ($GLOBALS['traverse_array']);                                      //Debug
                    //$GLOBALS['traverse_array']="";                                                  //Debug
                    //Now, save data to db. We'll use it later
                    //Get id and status from data
                    $category_id = $data["GRADE_CATEGORY"]["#"]["ID"]["0"]["#"];
                    $this->counter++;
                    //Save to db
                    $status = backup_putid($this->preferences->backup_unique_code, 'grade_category' ,$category_id,
                                           null,$data);
                    //Create returning info
                    $this->info = $this->counter;
                    //Reset temp
                    unset($this->temp);
                }
            }

            //Stop parsing if todo = GRADEBOOK and tagName = GRADEBOOK (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($tagName == "GRADEBOOK" and $this->level == 3) {
                $this->finished = true;
                $this->counter = 0;
            }

            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";

        }

        //This is the endTag handler we use where we are reading the users zone (todo="USERS")
        function endElementUsers($parser, $tagName) {
            global $CFG;
            //Check if we are into USERS zone
            if ($this->tree[3] == "USERS") {
                //if (trim($this->content))                                                                     //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br />\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br />\n";          //Debug
                //Dependig of different combinations, do different things
                if ($this->level == 4) {
                    switch ($tagName) {
                        case "USER":
                            //Increment counter
                            $this->counter++;
                            //Save to db, only save if record not already exist
                            // if there already is an new_id for this entry, just use that new_id?
                            $newuser = backup_getid($this->preferences->backup_unique_code,"user",$this->info->tempuser->id);
                            if (isset($newuser->new_id)) {
                                $newid = $newuser->new_id;
                            } else {
                                $newid = null;
                            }

                            backup_putid($this->preferences->backup_unique_code,"user",$this->info->tempuser->id,
                                            $newid,$this->info->tempuser);

                            //Do some output
                            if ($this->counter % 10 == 0) {
                                if (!defined('RESTORE_SILENTLY')) {
                                    echo ".";
                                    if ($this->counter % 200 == 0) {
                                        echo "<br />";
                                    }
                                }
                                backup_flush(300);
                            }

                            //Delete temp obejct
                            unset($this->info->tempuser);
                            break;
                    }
                }

                if ($this->level == 5) {
                    switch ($tagName) {
                        case "ID":
                            $this->info->users[$this->getContents()] = $this->getContents();
                            $this->info->tempuser->id = $this->getContents();
                            break;
                        case "AUTH":
                            $this->info->tempuser->auth = $this->getContents();
                            break;
                        case "CONFIRMED":
                            $this->info->tempuser->confirmed = $this->getContents();
                            break;
                        case "POLICYAGREED":
                            $this->info->tempuser->policyagreed = $this->getContents();
                            break;
                        case "DELETED":
                            $this->info->tempuser->deleted = $this->getContents();
                            break;
                        case "USERNAME":
                            $this->info->tempuser->username = $this->getContents();
                            break;
                        case "PASSWORD":
                            $this->info->tempuser->password = $this->getContents();
                            break;
                        case "IDNUMBER":
                            $this->info->tempuser->idnumber = $this->getContents();
                            break;
                        case "FIRSTNAME":
                            $this->info->tempuser->firstname = $this->getContents();
                            break;
                        case "LASTNAME":
                            $this->info->tempuser->lastname = $this->getContents();
                            break;
                        case "EMAIL":
                            $this->info->tempuser->email = $this->getContents();
                            break;
                        case "EMAILSTOP":
                            $this->info->tempuser->emailstop = $this->getContents();
                            break;
                        case "ICQ":
                            $this->info->tempuser->icq = $this->getContents();
                            break;
                        case "SKYPE":
                            $this->info->tempuser->skype = $this->getContents();
                            break;
                        case "AIM":
                            $this->info->tempuser->aim = $this->getContents();
                            break;
                        case "YAHOO":
                            $this->info->tempuser->yahoo = $this->getContents();
                            break;
                        case "MSN":
                            $this->info->tempuser->msn = $this->getContents();
                            break;
                        case "PHONE1":
                            $this->info->tempuser->phone1 = $this->getContents();
                            break;
                        case "PHONE2":
                            $this->info->tempuser->phone2 = $this->getContents();
                            break;
                        case "INSTITUTION":
                            $this->info->tempuser->institution = $this->getContents();
                            break;
                        case "DEPARTMENT":
                            $this->info->tempuser->department = $this->getContents();
                            break;
                        case "ADDRESS":
                            $this->info->tempuser->address = $this->getContents();
                            break;
                        case "CITY":
                            $this->info->tempuser->city = $this->getContents();
                            break;
                        case "COUNTRY":
                            $this->info->tempuser->country = $this->getContents();
                            break;
                        case "LANG":
                            $this->info->tempuser->lang = $this->getContents();
                            break;
                        case "THEME":
                            $this->info->tempuser->theme = $this->getContents();
                            break;
                        case "TIMEZONE":
                            $this->info->tempuser->timezone = $this->getContents();
                            break;
                        case "FIRSTACCESS":
                            $this->info->tempuser->firstaccess = $this->getContents();
                            break;
                        case "LASTACCESS":
                            $this->info->tempuser->lastaccess = $this->getContents();
                            break;
                        case "LASTLOGIN":
                            $this->info->tempuser->lastlogin = $this->getContents();
                            break;
                        case "CURRENTLOGIN":
                            $this->info->tempuser->currentlogin = $this->getContents();
                            break;
                        case "LASTIP":
                            $this->info->tempuser->lastip = $this->getContents();
                            break;
                        case "PICTURE":
                            $this->info->tempuser->picture = $this->getContents();
                            break;
                        case "URL":
                            $this->info->tempuser->url = $this->getContents();
                            break;
                        case "DESCRIPTION":
                            $this->info->tempuser->description = $this->getContents();
                            break;
                        case "MAILFORMAT":
                            $this->info->tempuser->mailformat = $this->getContents();
                            break;
                        case "MAILDIGEST":
                            $this->info->tempuser->maildigest = $this->getContents();
                            break;
                        case "MAILDISPLAY":
                            $this->info->tempuser->maildisplay = $this->getContents();
                            break;
                        case "HTMLEDITOR":
                            $this->info->tempuser->htmleditor = $this->getContents();
                            break;
                        case "AJAX":
                            $this->info->tempuser->ajax = $this->getContents();
                            break;
                        case "AUTOSUBSCRIBE":
                            $this->info->tempuser->autosubscribe = $this->getContents();
                            break;
                        case "TRACKFORUMS":
                            $this->info->tempuser->trackforums = $this->getContents();
                            break;
                        case "MNETHOSTURL":
                            $this->info->tempuser->mnethosturl = $this->getContents();
                            break;
                        case "TIMEMODIFIED":
                            $this->info->tempuser->timemodified = $this->getContents();
                            break;
                        default:
                            break;
                    }
                }

                if ($this->level == 6 && $this->tree[5]!="ROLES_ASSIGNMENTS" && $this->tree[5]!="ROLES_OVERRIDES") {
                    switch ($tagName) {
                        case "ROLE":
                            //We've finalized a role, get it
                            $this->info->tempuser->roles[$this->info->temprole->type] = $this->info->temprole;
                            unset($this->info->temprole);
                            break;
                        case "USER_PREFERENCE":
                            //We've finalized a user_preference, get it
                            $this->info->tempuser->user_preferences[$this->info->tempuserpreference->name] = $this->info->tempuserpreference;
                            unset($this->info->tempuserpreference);
                            break;
                        case "USER_CUSTOM_PROFILE_FIELD":
                            //We've finalized a user_custom_profile_field, get it
                            $this->info->tempuser->user_custom_profile_fields[] = $this->info->tempusercustomprofilefield;
                            unset($this->info->tempusercustomprofilefield);
                            break;
                        case "USER_TAG":
                            //We've finalized a user_tag, get it
                            $this->info->tempuser->user_tags[] = $this->info->tempusertag;
                            unset($this->info->tempusertag);
                            break;
                        default:
                            break;
                    }
                }

                if ($this->level == 7 && $this->tree[5]!="ROLES_ASSIGNMENTS" && $this->tree[5]!="ROLES_OVERRIDES") {
                /// If we are reading roles
                    if($this->tree[6] == 'ROLE') {
                        switch ($tagName) {
                            case "TYPE":
                                $this->info->temprole->type = $this->getContents();
                                break;
                            case "AUTHORITY":
                                $this->info->temprole->authority = $this->getContents();
                                break;
                            case "TEA_ROLE":
                                $this->info->temprole->tea_role = $this->getContents();
                                break;
                            case "EDITALL":
                                $this->info->temprole->editall = $this->getContents();
                                break;
                            case "TIMESTART":
                                $this->info->temprole->timestart = $this->getContents();
                                break;
                            case "TIMEEND":
                                $this->info->temprole->timeend = $this->getContents();
                                break;
                            case "TIMEMODIFIED":
                                $this->info->temprole->timemodified = $this->getContents();
                                break;
                            case "TIMESTART":
                                $this->info->temprole->timestart = $this->getContents();
                                break;
                            case "TIMEEND":
                                $this->info->temprole->timeend = $this->getContents();
                                break;
                            case "TIME":
                                $this->info->temprole->time = $this->getContents();
                                break;
                            case "TIMEACCESS":
                                $this->info->temprole->timeaccess = $this->getContents();
                                break;
                            case "ENROL":
                                $this->info->temprole->enrol = $this->getContents();
                                break;
                            default:
                                break;
                        }
                /// If we are reading user_preferences
                    } else if ($this->tree[6] == 'USER_PREFERENCE') {
                        switch ($tagName) {
                            case "NAME":
                                $this->info->tempuserpreference->name = $this->getContents();
                                break;
                            case "VALUE":
                                $this->info->tempuserpreference->value = $this->getContents();
                                break;
                            default:
                                break;
                        }
                /// If we are reading user_custom_profile_fields
                    } else if ($this->tree[6] == 'USER_CUSTOM_PROFILE_FIELD') {
                        switch ($tagName) {
                            case "FIELD_NAME":
                                $this->info->tempusercustomprofilefield->field_name = $this->getContents();
                                break;
                            case "FIELD_TYPE":
                                $this->info->tempusercustomprofilefield->field_type = $this->getContents();
                                break;
                            case "FIELD_DATA":
                                $this->info->tempusercustomprofilefield->field_data = $this->getContents();
                                break;
                            default:
                                break;
                        }
                /// If we are reading user_tags
                    } else if ($this->tree[6] == 'USER_TAG') {
                        switch ($tagName) {
                            case "NAME":
                                $this->info->tempusertag->name = $this->getContents();
                                break;
                            case "RAWNAME":
                                $this->info->tempusertag->rawname = $this->getContents();
                                break;
                            default:
                                break;
                        }
                    }
                }

                if ($this->tree[5] == "ROLES_ASSIGNMENTS") {

                    if ($this->level == 7) {
                        switch ($tagName) {
                            case "NAME":
                                $this->info->tempname = $this->getContents();
                            break;
                            case "SHORTNAME":
                                $this->info->tempshortname = $this->getContents();
                            break;
                            case "ID":
                                $this->info->tempid = $this->getContents(); // temp roleid
                            break;
                        }
                    }

                    if ($this->level == 9) {

                        switch ($tagName) {
                            case "USERID":
                                $this->info->tempuser->roleassignments[$this->info->tempid]->name = $this->info->tempname;

                                $this->info->tempuser->roleassignments[$this->info->tempid]->shortname = $this->info->tempshortname;

                                $this->info->tempuserid = $this->getContents();

                                $this->info->tempuser->roleassignments[$this->info->tempid]->assignments[$this->info->tempuserid]->userid = $this->getContents();
                            break;
                            case "HIDDEN":
                                $this->info->tempuser->roleassignments[$this->info->tempid]->assignments[$this->info->tempuserid]->hidden = $this->getContents();
                            break;
                            case "TIMESTART":
                                $this->info->tempuser->roleassignments[$this->info->tempid]->assignments[$this->info->tempuserid]->timestart = $this->getContents();
                            break;
                            case "TIMEEND":
                                $this->info->tempuser->roleassignments[$this->info->tempid]->assignments[$this->info->tempuserid]->timeend = $this->getContents();
                            break;
                            case "TIMEMODIFIED":
                                $this->info->tempuser->roleassignments[$this->info->tempid]->assignments[$this->info->tempuserid]->timemodified = $this->getContents();
                            break;
                            case "MODIFIERID":
                                $this->info->tempuser->roleassignments[$this->info->tempid]->assignments[$this->info->tempuserid]->modifierid = $this->getContents();
                            break;
                            case "ENROL":
                                $this->info->tempuser->roleassignments[$this->info->tempid]->assignments[$this->info->tempuserid]->enrol = $this->getContents();
                            break;
                            case "SORTORDER":
                                $this->info->tempuser->roleassignments[$this->info->tempid]->assignments[$this->info->tempuserid]->sortorder = $this->getContents();
                            break;

                        }
                    }
                } /// ends role_assignments

                if ($this->tree[5] == "ROLES_OVERRIDES") {
                    if ($this->level == 7) {
                        switch ($tagName) {
                            case "NAME":
                                $this->info->tempname = $this->getContents();
                            break;
                            case "SHORTNAME":
                                $this->info->tempshortname = $this->getContents();
                            break;
                            case "ID":
                                $this->info->tempid = $this->getContents(); // temp roleid
                            break;
                        }
                    }

                    if ($this->level == 9) {
                        switch ($tagName) {
                            case "NAME":

                                $this->info->tempuser->roleoverrides[$this->info->tempid]->name = $this->info->tempname;
                                $this->info->tempuser->roleoverrides[$this->info->tempid]->shortname = $this->info->tempshortname;
                                $this->info->tempname = $this->getContents(); // change to name of capability
                                $this->info->tempuser->roleoverrides[$this->info->tempid]->overrides[$this->info->tempname]->name = $this->getContents();
                            break;
                            case "PERMISSION":
                                $this->info->tempuser->roleoverrides[$this->info->tempid]->overrides[$this->info->tempname]->permission = $this->getContents();
                            break;
                            case "TIMEMODIFIED":
                                $this->info->tempuser->roleoverrides[$this->info->tempid]->overrides[$this->info->tempname]->timemodified = $this->getContents();
                            break;
                            case "MODIFIERID":
                                $this->info->tempuser->roleoverrides[$this->info->tempid]->overrides[$this->info->tempname]->modifierid = $this->getContents();
                            break;
                        }
                    }
                } /// ends role_overrides

            } // closes if this->tree[3]=="users"

            //Stop parsing if todo = USERS and tagName = USERS (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($tagName == "USERS" and $this->level == 3) {
                $this->finished = true;
                $this->counter = 0;
            }

            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";

        }

        //This is the endTag handler we use where we are reading the messages zone (todo="MESSAGES")
        function endElementMessages($parser, $tagName) {
            //Check if we are into MESSAGES zone
            if ($this->tree[3] == "MESSAGES") {
                //if (trim($this->content))                                                             //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br />\n"; //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br />\n";//Debug
                //Acumulate data to info (content + close tag)
                //Reconvert: strip htmlchars again and trim to generate xml data
                if (!isset($this->temp)) {
                    $this->temp = "";
                }
                $this->temp .= htmlspecialchars(trim($this->content))."</".$tagName.">";
                //If we've finished a message, xmlize it an save to db
                if (($this->level == 4) and ($tagName == "MESSAGE")) {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    //Call to xmlize for this portion of xml data (one MESSAGE)
                    //echo "-XMLIZE: ".strftime ("%X",time()),"-";                                    //Debug
                    $data = xmlize($xml_data,0);
                    //echo strftime ("%X",time())."<p>";                                              //Debug
                    //traverse_xmlize($data);                                                         //Debug
                    //print_object ($GLOBALS['traverse_array']);                                      //Debug
                    //$GLOBALS['traverse_array']="";                                                  //Debug
                    //Now, save data to db. We'll use it later
                    //Get id and status from data
                    $message_id = $data["MESSAGE"]["#"]["ID"]["0"]["#"];
                    $message_status = $data["MESSAGE"]["#"]["STATUS"]["0"]["#"];
                    if ($message_status == "READ") {
                        $table = "message_read";
                    } else {
                        $table = "message";
                    }
                    $this->counter++;
                    //Save to db
                    $status = backup_putid($this->preferences->backup_unique_code, $table,$message_id,
                                           null,$data);
                    //Create returning info
                    $this->info = $this->counter;
                    //Reset temp
                    unset($this->temp);
                }
                //If we've finished a contact, xmlize it an save to db
                if (($this->level == 5) and ($tagName == "CONTACT")) {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    //Call to xmlize for this portion of xml data (one MESSAGE)
                    //echo "-XMLIZE: ".strftime ("%X",time()),"-";                                    //Debug
                    $data = xmlize($xml_data,0);
                    //echo strftime ("%X",time())."<p>";                                              //Debug
                    //traverse_xmlize($data);                                                         //Debug
                    //print_object ($GLOBALS['traverse_array']);                                      //Debug
                    //$GLOBALS['traverse_array']="";                                                  //Debug
                    //Now, save data to db. We'll use it later
                    //Get id and status from data
                    $contact_id = $data["CONTACT"]["#"]["ID"]["0"]["#"];
                    $this->counter++;
                    //Save to db
                    $status = backup_putid($this->preferences->backup_unique_code, 'message_contacts' ,$contact_id,
                                           null,$data);
                    //Create returning info
                    $this->info = $this->counter;
                    //Reset temp
                    unset($this->temp);
                }
            }

            //Stop parsing if todo = MESSAGES and tagName = MESSAGES (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($tagName == "MESSAGES" and $this->level == 3) {
                $this->finished = true;
                $this->counter = 0;
            }

            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";

        }

        //This is the endTag handler we use where we are reading the blogs zone (todo="BLOGS")
        function endElementBlogs($parser, $tagName) {
            //Check if we are into BLOGS zone
            if ($this->tree[3] == "BLOGS") {
                //if (trim($this->content))                                                             //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br />\n"; //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br />\n";//Debug
                //Acumulate data to info (content + close tag)
                //Reconvert: strip htmlchars again and trim to generate xml data
                if (!isset($this->temp)) {
                    $this->temp = "";
                }
                $this->temp .= htmlspecialchars(trim($this->content))."</".$tagName.">";
                //If we've finished a blog, xmlize it an save to db
                if (($this->level == 4) and ($tagName == "BLOG")) {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    //Call to xmlize for this portion of xml data (one BLOG)
                    //echo "-XMLIZE: ".strftime ("%X",time()),"-";                                    //Debug
                    $data = xmlize($xml_data,0);
                    //echo strftime ("%X",time())."<p>";                                              //Debug
                    //traverse_xmlize($data);                                                         //Debug
                    //print_object ($GLOBALS['traverse_array']);                                      //Debug
                    //$GLOBALS['traverse_array']="";                                                  //Debug
                    //Now, save data to db. We'll use it later
                    //Get id from data
                    $blog_id = $data["BLOG"]["#"]["ID"]["0"]["#"];
                    $this->counter++;
                    //Save to db
                    $status = backup_putid($this->preferences->backup_unique_code, 'blog', $blog_id,
                                           null,$data);
                    //Create returning info
                    $this->info = $this->counter;
                    //Reset temp
                    unset($this->temp);
                }
            }

            //Stop parsing if todo = BLOGS and tagName = BLOGS (end of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($tagName == "BLOGS" and $this->level == 3) {
                $this->finished = true;
                $this->counter = 0;
            }

            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";

        }

        //This is the endTag handler we use where we are reading the questions zone (todo="QUESTIONS")
        function endElementQuestions($parser, $tagName) {
            //Check if we are into QUESTION_CATEGORIES zone
            if ($this->tree[3] == "QUESTION_CATEGORIES") {
                //if (trim($this->content))                                                                     //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br />\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br />\n";          //Debug
                //Acumulate data to info (content + close tag)
                //Reconvert: strip htmlchars again and trim to generate xml data
                if (!isset($this->temp)) {
                    $this->temp = "";
                }
                $this->temp .= htmlspecialchars(trim($this->content))."</".$tagName.">";
                //If we've finished a mod, xmlize it an save to db
                if (($this->level == 4) and ($tagName == "QUESTION_CATEGORY")) {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    //Call to xmlize for this portion of xml data (one QUESTION_CATEGORY)
                    //echo "-XMLIZE: ".strftime ("%X",time()),"-";                                                //Debug
                    $data = xmlize($xml_data,0);
                    //echo strftime ("%X",time())."<p>";                                                          //Debug
                    //traverse_xmlize($data);                                                                     //Debug
                    //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                    //$GLOBALS['traverse_array']="";                                                              //Debug
                    //Now, save data to db. We'll use it later
                    //Get id from data
                    $category_id = $data["QUESTION_CATEGORY"]["#"]["ID"]["0"]["#"];
                    //Save to db
                    $status = backup_putid($this->preferences->backup_unique_code,"question_categories",$category_id,
                                     null,$data);
                    //Create returning info
                    $ret_info = new object();
                    $ret_info->id = $category_id;
                    $this->info[] = $ret_info;
                    //Reset temp
                    unset($this->temp);
                }
            }

            //Stop parsing if todo = QUESTION_CATEGORIES and tagName = QUESTION_CATEGORY (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($tagName == "QUESTION_CATEGORIES" and $this->level == 3) {
                $this->finished = true;
            }

            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";

        }

        //This is the endTag handler we use where we are reading the scales zone (todo="SCALES")
        function endElementScales($parser, $tagName) {
            //Check if we are into SCALES zone
            if ($this->tree[3] == "SCALES") {
                //if (trim($this->content))                                                                     //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br />\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br />\n";          //Debug
                //Acumulate data to info (content + close tag)
                //Reconvert: strip htmlchars again and trim to generate xml data
                if (!isset($this->temp)) {
                    $this->temp = "";
                }
                $this->temp .= htmlspecialchars(trim($this->content))."</".$tagName.">";
                //If we've finished a scale, xmlize it an save to db
                if (($this->level == 4) and ($tagName == "SCALE")) {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    //Call to xmlize for this portion of xml data (one SCALE)
                    //echo "-XMLIZE: ".strftime ("%X",time()),"-";                                                //Debug
                    $data = xmlize($xml_data,0);
                    //echo strftime ("%X",time())."<p>";                                                          //Debug
                    //traverse_xmlize($data);                                                                     //Debug
                    //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                    //$GLOBALS['traverse_array']="";                                                              //Debug
                    //Now, save data to db. We'll use it later
                    //Get id and from data
                    $scale_id = $data["SCALE"]["#"]["ID"]["0"]["#"];
                    //Save to db
                    $status = backup_putid($this->preferences->backup_unique_code,"scale",$scale_id,
                                     null,$data);
                    //Create returning info
                    $ret_info = new object();
                    $ret_info->id = $scale_id;
                    $this->info[] = $ret_info;
                    //Reset temp
                    unset($this->temp);
                }
            }

            //Stop parsing if todo = SCALES and tagName = SCALE (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($tagName == "SCALES" and $this->level == 3) {
                $this->finished = true;
            }

            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";

        }

        //This is the endTag handler we use where we are reading the groups zone (todo="GROUPS")
        function endElementGroups($parser, $tagName) {
            //Check if we are into GROUPS zone
            if ($this->tree[3] == "GROUPS") {
                //if (trim($this->content))                                                                     //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br />\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br />\n";          //Debug
                //Acumulate data to info (content + close tag)
                //Reconvert: strip htmlchars again and trim to generate xml data
                if (!isset($this->temp)) {
                    $this->temp = "";
                }
                $this->temp .= htmlspecialchars(trim($this->content))."</".$tagName.">";
                //If we've finished a group, xmlize it an save to db
                if (($this->level == 4) and ($tagName == "GROUP")) {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    //Call to xmlize for this portion of xml data (one GROUP)
                    //echo "-XMLIZE: ".strftime ("%X",time()),"-";                                                //Debug
                    $data = xmlize($xml_data,0);
                    //echo strftime ("%X",time())."<p>";                                                          //Debug
                    //traverse_xmlize($data);                                                                     //Debug
                    //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                    //$GLOBALS['traverse_array']="";                                                              //Debug
                    //Now, save data to db. We'll use it later
                    //Get id and from data
                    $group_id = $data["GROUP"]["#"]["ID"]["0"]["#"];
                    //Save to db
                    $status = backup_putid($this->preferences->backup_unique_code,"groups",$group_id,
                                     null,$data);
                    //Create returning info
                    $ret_info = new Object();
                    $ret_info->id = $group_id;
                    $this->info[] = $ret_info;
                    //Reset temp
                    unset($this->temp);
                }
            }

            //Stop parsing if todo = GROUPS and tagName = GROUP (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($tagName == "GROUPS" and $this->level == 3) {
                $this->finished = true;
            }

            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";

        }

        //This is the endTag handler we use where we are reading the groupings zone (todo="GROUPINGS")
        function endElementGroupings($parser, $tagName) {
            //Check if we are into GROUPINGS zone
            if ($this->tree[3] == "GROUPINGS") {
                //Acumulate data to info (content + close tag)
                //Reconvert: strip htmlchars again and trim to generate xml data
                if (!isset($this->temp)) {
                    $this->temp = "";
                }
                $this->temp .= htmlspecialchars(trim($this->content))."</".$tagName.">";
                //If we've finished a group, xmlize it an save to db
                if (($this->level == 4) and ($tagName == "GROUPING")) {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    //Call to xmlize for this portion of xml data (one GROUPING)
                    $data = xmlize($xml_data,0);
                    //Now, save data to db. We'll use it later
                    //Get id and from data
                    $grouping_id = $data["GROUPING"]["#"]["ID"]["0"]["#"];
                    //Save to db
                    $status = backup_putid($this->preferences->backup_unique_code,"groupings",$grouping_id,
                                     null,$data);
                    //Create returning info
                    $ret_info = new Object();
                    $ret_info->id = $grouping_id;
                    $this->info[] = $ret_info;
                    //Reset temp
                    unset($this->temp);
                }
            }

            //Stop parsing if todo = GROUPINGS and tagName = GROUPING (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($tagName == "GROUPINGS" and $this->level == 3) {
                $this->finished = true;
            }

            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";

        }

        //This is the endTag handler we use where we are reading the groupingsgroups zone (todo="GROUPINGGROUPS")
        function endElementGroupingsGroups($parser, $tagName) {
            //Check if we are into GROUPINGSGROUPS zone
            if ($this->tree[3] == "GROUPINGSGROUPS") {
                //Acumulate data to info (content + close tag)
                //Reconvert: strip htmlchars again and trim to generate xml data
                if (!isset($this->temp)) {
                    $this->temp = "";
                }
                $this->temp .= htmlspecialchars(trim($this->content))."</".$tagName.">";
                //If we've finished a group, xmlize it an save to db
                if (($this->level == 4) and ($tagName == "GROUPINGGROUP")) {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    //Call to xmlize for this portion of xml data (one GROUPING)
                    $data = xmlize($xml_data,0);
                    //Now, save data to db. We'll use it later
                    //Get id and from data
                    $groupinggroup_id = $data["GROUPINGGROUP"]["#"]["ID"]["0"]["#"];
                    //Save to db
                    $status = backup_putid($this->preferences->backup_unique_code,"groupingsgroups",$groupinggroup_id,
                                     null,$data);
                    //Create returning info
                    $ret_info = new Object();
                    $ret_info->id = $groupinggroup_id;
                    $this->info[] = $ret_info;
                    //Reset temp
                    unset($this->temp);
                }
            }

            //Stop parsing if todo = GROUPINGS and tagName = GROUPING (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($tagName == "GROUPINGSGROUPS" and $this->level == 3) {
                $this->finished = true;
            }

            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";

        }

        //This is the endTag handler we use where we are reading the events zone (todo="EVENTS")
        function endElementEvents($parser, $tagName) {
            //Check if we are into EVENTS zone
            if ($this->tree[3] == "EVENTS") {
                //if (trim($this->content))                                                                     //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br />\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br />\n";          //Debug
                //Acumulate data to info (content + close tag)
                //Reconvert: strip htmlchars again and trim to generate xml data
                if (!isset($this->temp)) {
                    $this->temp = "";
                }
                $this->temp .= htmlspecialchars(trim($this->content))."</".$tagName.">";
                //If we've finished a event, xmlize it an save to db
                if (($this->level == 4) and ($tagName == "EVENT")) {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    //Call to xmlize for this portion of xml data (one EVENT)
                    //echo "-XMLIZE: ".strftime ("%X",time()),"-";                                                //Debug
                    $data = xmlize($xml_data,0);
                    //echo strftime ("%X",time())."<p>";                                                          //Debug
                    //traverse_xmlize($data);                                                                     //Debug
                    //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                    //$GLOBALS['traverse_array']="";                                                              //Debug
                    //Now, save data to db. We'll use it later
                    //Get id and from data
                    $event_id = $data["EVENT"]["#"]["ID"]["0"]["#"];
                    //Save to db
                    $status = backup_putid($this->preferences->backup_unique_code,"event",$event_id,
                                     null,$data);
                    //Create returning info
                    $ret_info = new object();
                    $ret_info->id = $event_id;
                    $this->info[] = $ret_info;
                    //Reset temp
                    unset($this->temp);
                }
            }

            //Stop parsing if todo = EVENTS and tagName = EVENT (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($tagName == "EVENTS" and $this->level == 3) {
                $this->finished = true;
            }

            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";

        }

        //This is the endTag handler we use where we are reading the modules zone (todo="MODULES")
        function endElementModules($parser, $tagName) {
            //Check if we are into MODULES zone
            if ($this->tree[3] == "MODULES") {
                //if (trim($this->content))                                                                     //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br />\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br />\n";          //Debug
                //Acumulate data to info (content + close tag)
                //Reconvert: strip htmlchars again and trim to generate xml data
                if (!isset($this->temp)) {
                    $this->temp = "";
                }
                $this->temp .= htmlspecialchars(trim($this->content))."</".$tagName.">";
                //If we've finished a mod, xmlize it an save to db
                if (($this->level == 4) and ($tagName == "MOD")) {
                    //Only process the module if efectively it has been selected for restore. MDL-18482
                    if (empty($this->preferences->mods[$this->temp_mod_type]->granular)  // We don't care about per instance, i.e. restore all instances.
                        or (array_key_exists($this->temp_mod_id, $this->preferences->mods[$this->temp_mod_type]->instances)
                            and
                            !empty($this->preferences->mods[$this->temp_mod_type]->instances[$this->temp_mod_id]->restore))) {

                        //Prepend XML standard header to info gathered
                        $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                        //Call to xmlize for this portion of xml data (one MOD)
                        //echo "-XMLIZE: ".strftime ("%X",time()),"-";                                                  //Debug
                        $data = xmlize($xml_data,0);
                        //echo strftime ("%X",time())."<p>";                                                            //Debug
                        //traverse_xmlize($data);                                                                     //Debug
                        //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                        //$GLOBALS['traverse_array']="";                                                              //Debug
                        //Now, save data to db. We'll use it later
                        //Get id and modtype from data
                        $mod_id = $data["MOD"]["#"]["ID"]["0"]["#"];
                        $mod_type = $data["MOD"]["#"]["MODTYPE"]["0"]["#"];
                        //Only if we've selected to restore it
                        if  (!empty($this->preferences->mods[$mod_type]->restore)) {
                            //Save to db
                            $status = backup_putid($this->preferences->backup_unique_code,$mod_type,$mod_id,
                                         null,$data);
                            //echo "<p>id: ".$mod_id."-".$mod_type." len.: ".strlen($sla_mod_temp)." to_db: ".$status."<p>";   //Debug
                            //Create returning info
                            $ret_info = new object();
                            $ret_info->id = $mod_id;
                            $ret_info->modtype = $mod_type;
                            $this->info[] = $ret_info;
                        }
                    } else {
                        debugging("Info: skipping $this->temp_mod_type activity with mod id: $this->temp_mod_id. Not selected for restore", DEBUG_DEVELOPER);
                    }
                    //Reset current mod_type and mod_id
                    unset($this->temp_mod_type);
                    unset($this->temp_mod_id);
                    //Reset temp
                    unset($this->temp);
                }

            /// Grab current mod id and type when available
                if ($this->level == 5) {
                    if ($tagName == 'ID') {
                        $this->temp_mod_id = trim($this->content);
                    } else if ($tagName == 'MODTYPE') {
                        $this->temp_mod_type = trim($this->content);
                    }
                }

            }

            //Stop parsing if todo = MODULES and tagName = MODULES (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($tagName == "MODULES" and $this->level == 3) {
                $this->finished = true;
            }

            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";

        }

        //This is the endTag handler we use where we are reading the logs zone (todo="LOGS")
        function endElementLogs($parser, $tagName) {
            //Check if we are into LOGS zone
            if ($this->tree[3] == "LOGS") {
                //if (trim($this->content))                                                                     //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br />\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br />\n";          //Debug
                //Acumulate data to info (content + close tag)
                //Reconvert: strip htmlchars again and trim to generate xml data
                if (!isset($this->temp)) {
                    $this->temp = "";
                }
                $this->temp .= htmlspecialchars(trim($this->content))."</".$tagName.">";
                //If we've finished a log, xmlize it an save to db
                if (($this->level == 4) and ($tagName == "LOG")) {
                    //Prepend XML standard header to info gathered
                    $xml_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".$this->temp;
                    //Call to xmlize for this portion of xml data (one LOG)
                    //echo "-XMLIZE: ".strftime ("%X",time()),"-";                                                //Debug
                    $data = xmlize($xml_data,0);
                    //echo strftime ("%X",time())."<p>";                                                          //Debug
                    //traverse_xmlize($data);                                                                     //Debug
                    //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                    //$GLOBALS['traverse_array']="";                                                              //Debug
                    //Now, save data to db. We'll use it later
                    //Get id and modtype from data
                    $log_id = $data["LOG"]["#"]["ID"]["0"]["#"];
                    $log_module = $data["LOG"]["#"]["MODULE"]["0"]["#"];
                    //We only save log entries from backup file if they are:
                    // - Course logs
                    // - User logs
                    // - Module logs about one restored module
                    if  ($log_module == "course" or
                         $log_module == "user" or
                        $this->preferences->mods[$log_module]->restore) {
                        //Increment counter
                        $this->counter++;
                        //Save to db
                        $status = backup_putid($this->preferences->backup_unique_code,"log",$log_id,
                                     null,$data);
                        //echo "<p>id: ".$mod_id."-".$mod_type." len.: ".strlen($sla_mod_temp)." to_db: ".$status."<p>";   //Debug
                        //Create returning info
                        $this->info = $this->counter;
                    }
                    //Reset temp
                    unset($this->temp);
                }
            }

            //Stop parsing if todo = LOGS and tagName = LOGS (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($tagName == "LOGS" and $this->level == 3) {
                $this->finished = true;
                $this->counter = 0;
            }

            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";

        }

        //This is the endTag default handler we use when todo is undefined
        function endElement($parser, $tagName) {
            if (trim($this->content))                                                                     //Debug
                echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br />\n";           //Debug
            echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br />\n";          //Debug

            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";
        }

        //This is the handler to read data contents (simple accumule it)
        function characterData($parser, $data) {
            $this->content .= $data;
        }
    }

    //This function executes the MoodleParser
    function restore_read_xml ($xml_file,$todo,$preferences) {

        global $CFG;

        $status = true;

    /// If enabled in the site, use split files instead of original moodle.xml file
    /// This will speed parsing speed upto 20x.
        if (!empty($CFG->experimentalsplitrestore)) {
        /// Use splite file, else nothing to process (saves one full parsing for each non-existing todo)
            $splitfile= dirname($xml_file) . '/' . strtolower('split_' . $todo . '.xml');
            if (file_exists($splitfile)) {
                $xml_file = $splitfile;
                debugging("Info: todo=$todo, using split file", DEBUG_DEVELOPER);
            } else {
            /// For some todos, that are used in earlier restore steps (restore_precheck(), restore_form...
            /// allow fallback to monolithic moodle.xml. Those todos are at the beggining of the xml, so
            /// it doesn't hurts too much.
                if ($todo == 'INFO' || $todo == 'COURSE_HEADER' || $todo == 'ROLES') {
                    debugging("Info: todo=$todo, no split file. Fallback to moodle.xml", DEBUG_DEVELOPER);
                } else {
                    debugging("Info: todo=$todo, no split file. Parse skipped", DEBUG_DEVELOPER);
                    return true;
                }
            }
        }

        $xml_parser = xml_parser_create('UTF-8');
        $moodle_parser = new MoodleParser();
        $moodle_parser->todo = $todo;
        $moodle_parser->preferences = $preferences;
        xml_set_object($xml_parser,$moodle_parser);
        //Depending of the todo we use some element_handler or another
        if ($todo == "INFO") {
            xml_set_element_handler($xml_parser, "startElementInfo", "endElementInfo");
        } else if ($todo == "ROLES") {
            xml_set_element_handler($xml_parser, "startElementRoles", "endElementRoles");
        } else if ($todo == "COURSE_HEADER") {
            xml_set_element_handler($xml_parser, "startElementCourseHeader", "endElementCourseHeader");
        } else if ($todo == 'BLOCKS') {
            xml_set_element_handler($xml_parser, "startElementBlocks", "endElementBlocks");
        } else if ($todo == "SECTIONS") {
            xml_set_element_handler($xml_parser, "startElementSections", "endElementSections");
        } else if ($todo == 'FORMATDATA') {
            xml_set_element_handler($xml_parser, "startElementFormatData", "endElementFormatData");
        } else if ($todo == "METACOURSE") {
            xml_set_element_handler($xml_parser, "startElementMetacourse", "endElementMetacourse");
        } else if ($todo == "GRADEBOOK") {
            if ($preferences->backup_version > 2007090500) {
                xml_set_element_handler($xml_parser, "startElementGradebook", "endElementGradebook");
            } else {
                xml_set_element_handler($xml_parser, "startElementOldGradebook", "endElementOldGradebook");
            }
        } else if ($todo == "USERS") {
            xml_set_element_handler($xml_parser, "startElementUsers", "endElementUsers");
        } else if ($todo == "MESSAGES") {
            xml_set_element_handler($xml_parser, "startElementMessages", "endElementMessages");
        } else if ($todo == "BLOGS") {
            xml_set_element_handler($xml_parser, "startElementBlogs", "endElementBlogs");
        } else if ($todo == "QUESTIONS") {
            xml_set_element_handler($xml_parser, "startElementQuestions", "endElementQuestions");
        } else if ($todo == "SCALES") {
            xml_set_element_handler($xml_parser, "startElementScales", "endElementScales");
        } else if ($todo == "GROUPS") {
            xml_set_element_handler($xml_parser, "startElementGroups", "endElementGroups");
        } else if ($todo == "GROUPINGS") {
            xml_set_element_handler($xml_parser, "startElementGroupings", "endElementGroupings");
        } else if ($todo == "GROUPINGSGROUPS") {
            xml_set_element_handler($xml_parser, "startElementGroupingsGroups", "endElementGroupingsGroups");
        } else if ($todo == "EVENTS") {
            xml_set_element_handler($xml_parser, "startElementEvents", "endElementEvents");
        } else if ($todo == "MODULES") {
            xml_set_element_handler($xml_parser, "startElementModules", "endElementModules");
        } else if ($todo == "LOGS") {
            xml_set_element_handler($xml_parser, "startElementLogs", "endElementLogs");
        } else {
            //Define default handlers (must no be invoked when everything become finished)
            xml_set_element_handler($xml_parser, "startElementInfo", "endElementInfo");
        }
        xml_set_character_data_handler($xml_parser, "characterData");
        $fp = fopen($xml_file,"r")
            or $status = false;
        if ($status) {
            // MDL-9290 performance improvement on reading large xml
            $lasttime = time(); // crmas
            while ($data = fread($fp, 8192) and !$moodle_parser->finished) {
             
                if ((time() - $lasttime) > 5) {
                    $lasttime = time();
                    backup_flush(1);
                }
             
                xml_parse($xml_parser, $data, feof($fp))
                        or die(sprintf("XML error: %s at line %d",
                        xml_error_string(xml_get_error_code($xml_parser)),
                                xml_get_current_line_number($xml_parser)));
            }
            fclose($fp);
        }
        //Get info from parser
        $info = $moodle_parser->info;

        //Clear parser mem
        xml_parser_free($xml_parser);

        if ($status && !empty($info)) {
            return $info;
        } else {
            return $status;
        }
    }

    /**
     * @param string $errorstr passed by reference, if silent is true,
     * errorstr will be populated and this function will return false rather than calling error() or notify()
     * @param boolean $noredirect (optional) if this is passed, this function will not print continue, or
     * redirect to the next step in the restore process, instead will return $backup_unique_code
     */
    function restore_precheck($id,$file,&$errorstr,$noredirect=false) {

        global $CFG, $SESSION;

        //Prepend dataroot to variable to have the absolute path
        $file = $CFG->dataroot."/".$file;

        if (!defined('RESTORE_SILENTLY')) {
            //Start the main table
            echo "<table cellpadding=\"5\">";
            echo "<tr><td>";

            //Start the mail ul
            echo "<ul>";
        }

        //Check the file exists
        if (!is_file($file)) {
            if (!defined('RESTORE_SILENTLY')) {
                error ("File not exists ($file)");
            } else {
                $errorstr = "File not exists ($file)";
                return false;
            }
        }

        //Check the file name ends with .zip
        if (!substr($file,-4) == ".zip") {
            if (!defined('RESTORE_SILENTLY')) {
                error ("File has an incorrect extension");
            } else {
                $errorstr = 'File has an incorrect extension';
                return false;
            }
        }

        //Now calculate the unique_code for this restore
        $backup_unique_code = time();

        //Now check and create the backup dir (if it doesn't exist)
        if (!defined('RESTORE_SILENTLY')) {
            echo "<li>".get_string("creatingtemporarystructures").'</li>';
        }
        $status = check_and_create_backup_dir($backup_unique_code);
        //Empty dir
        if ($status) {
            $status = clear_backup_dir($backup_unique_code);
        }

        //Now delete old data and directories under dataroot/temp/backup
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("deletingolddata").'</li>';
            }
            $status = backup_delete_old_data();
        }

        //Now copy he zip file to dataroot/temp/backup/backup_unique_code
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("copyingzipfile").'</li>';
            }
            if (! $status = backup_copy_file($file,$CFG->dataroot."/temp/backup/".$backup_unique_code."/".basename($file))) {
                if (!defined('RESTORE_SILENTLY')) {
                    notify("Error copying backup file. Invalid name or bad perms.");
                } else {
                    $errorstr = "Error copying backup file. Invalid name or bad perms";
                    return false;
                }
            }
        }

        //Now unzip the file
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("unzippingbackup").'</li>';
            }
            if (! $status = restore_unzip ($CFG->dataroot."/temp/backup/".$backup_unique_code."/".basename($file))) {
                if (!defined('RESTORE_SILENTLY')) {
                    notify("Error unzipping backup file. Invalid zip file.");
                } else {
                    $errorstr = "Error unzipping backup file. Invalid zip file.";
                    return false;
                }
            }
        }

        // If experimental option is enabled (enableimsccimport)
        // check for Common Cartridge packages and convert to Moodle format
        if ($status && isset($CFG->enableimsccimport) && $CFG->enableimsccimport == 1) {
            require_once($CFG->dirroot. '/backup/cc/restore_cc.php');
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string('checkingforimscc', 'imscc').'</li>';
            }
            $status = cc_convert($CFG->dataroot. DIRECTORY_SEPARATOR .'temp'. DIRECTORY_SEPARATOR . 'backup'. DIRECTORY_SEPARATOR . $backup_unique_code);
        }

        //Check for Blackboard backups and convert
        if ($status){
            require_once("$CFG->dirroot/backup/bb/restore_bb.php");
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("checkingforbbexport").'</li>';
            }
            $status = blackboard_convert($CFG->dataroot."/temp/backup/".$backup_unique_code);
        }

        //Now check for the moodle.xml file
        if ($status) {
            $xml_file  = $CFG->dataroot."/temp/backup/".$backup_unique_code."/moodle.xml";
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("checkingbackup").'</li>';
            }
            if (! $status = restore_check_moodle_file ($xml_file)) {
                if (!is_file($xml_file)) {
                    $errorstr = 'Error checking backup file. moodle.xml not found at root level of zip file.';
                } else {
                    $errorstr = 'Error checking backup file. moodle.xml is incorrect or corrupted.';
                }
                if (!defined('RESTORE_SILENTLY')) {
                    notify($errorstr);
                } else {
                    return false;
                }
            }
        }

        $info = "";
        $course_header = "";

        //Now read the info tag (all)
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("readinginfofrombackup").'</li>';
            }
            //Reading info from file
            $info = restore_read_xml_info ($xml_file);
            //Reading course_header from file
            $course_header = restore_read_xml_course_header ($xml_file);

            if(!is_object($course_header)){
                // ensure we fail if there is no course header
                $course_header = false;
            }
        }

        if (!defined('RESTORE_SILENTLY')) {
            //End the main ul
            echo "</ul>\n";

            //End the main table
            echo "</td></tr>";
            echo "</table>";
        }

        //We compare Moodle's versions
        if ($status && $CFG->version < $info->backup_moodle_version) {
            $message = new object();
            $message->serverversion = $CFG->version;
            $message->serverrelease = $CFG->release;
            $message->backupversion = $info->backup_moodle_version;
            $message->backuprelease = $info->backup_moodle_release;
            print_simple_box(get_string('noticenewerbackup','',$message), "center", "70%", '', "20", "noticebox");

        }

        //Now we print in other table, the backup and the course it contains info
        if ($info and $course_header and $status) {
            //First, the course info
            if (!defined('RESTORE_SILENTLY')) {
                $status = restore_print_course_header($course_header);
            }
            //Now, the backup info
            if ($status) {
                if (!defined('RESTORE_SILENTLY')) {
                    $status = restore_print_info($info);
                }
            }
        }

        //Save course header and info into php session
        if ($status) {
            $SESSION->info = $info;
            $SESSION->course_header = $course_header;
        }

        //Finally, a little form to continue
        //with some hidden fields
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<br /><div style='text-align:center'>";
                $hidden["backup_unique_code"] = $backup_unique_code;
                $hidden["launch"]             = "form";
                $hidden["file"]               =  $file;
                $hidden["id"]                 =  $id;
                print_single_button("restore.php", $hidden, get_string("continue"),"post");
                echo "</div>";
            }
            else {
                if (empty($noredirect)) {
                    print_continue($CFG->wwwroot.'/backup/restore.php?backup_unique_code='.$backup_unique_code.'&launch=form&file='.$file.'&id='.$id.'&sesskey='.sesskey());
                    print_footer();
                    die;

                } else {
                    return $backup_unique_code;
                }
            }
        }

        if (!$status) {
            if (!defined('RESTORE_SILENTLY')) {
                error ("An error has ocurred");
            } else {
                $errorstr = "An error has occured"; // helpful! :P
                return false;
            }
        }
        return true;
    }

    function restore_setup_for_check(&$restore,$backup_unique_code) {
        global $SESSION;
        $restore->backup_unique_code=$backup_unique_code;
        $restore->users = 2; // yuk
        // we set these from restore object on silent restore and from info (backup) object on import
        $restore->course_files = isset($SESSION->restore->restore_course_files) ? $SESSION->restore->restore_course_files : $SESSION->info->backup_course_files;
        $restore->site_files = isset($SESSION->restore->restore_site_files) ? $SESSION->restore->restore_site_files : $SESSION->info->backup_site_files;
        if ($allmods = get_records("modules")) {
            foreach ($allmods as $mod) {
                $modname = $mod->name;
                $var = "restore_".$modname;
                //Now check that we have that module info in the backup file
                if (isset($SESSION->info->mods[$modname]) && $SESSION->info->mods[$modname]->backup == "true") {
                    $restore->$var = 1;
                }
            }
        }
        // Calculate all session objects checksum and store them in session too
        // so restore_execute.html (used by manual restore and import) will be
        // able to detect any problem in session info.
        restore_save_session_object_checksums($restore, $SESSION->info, $SESSION->course_header);

        return true;
    }

    /**
     * Save the checksum of the 3 main in-session restore objects (restore, info, course_header)
     * so restore_execute.html will be able to check that all them have arrived correctly, without
     * losing data for any type of session size limit/error. MDL-18469. Used both by manual restore
     * and import
     */
    function restore_save_session_object_checksums($restore, $info, $course_header) {
        global $SESSION;
        $restore_checksums = array();
        $restore_checksums['info']          = md5(serialize($info));
        $restore_checksums['course_header'] = md5(serialize($course_header));
        $restore_checksums['restore']       = md5(serialize($restore));
        $SESSION->restore_checksums = $restore_checksums;
    }

    function backup_to_restore_array($backup,$k=0) {
        if (is_array($backup) ) {
            $restore = array();
            foreach ($backup as $key => $value) {
                $newkey = str_replace('backup','restore',$key);
                $restore[$newkey] = backup_to_restore_array($value,$key);
            }
        }
        else if (is_object($backup)) {
            $restore = new stdClass();
            $tmp = get_object_vars($backup);
            foreach ($tmp as $key => $value) {
                $newkey = str_replace('backup','restore',$key);
                $restore->$newkey = backup_to_restore_array($value,$key);
            }
        }
        else {
            $newkey = str_replace('backup','restore',$k);
            $restore = $backup;
        }
        return $restore;
    }

    /**
     * compatibility function
     * checks for per-instance backups AND
     * older per-module backups
     * and returns whether userdata has been selected.
     */
    function restore_userdata_selected($restore,$modname,$modid) {
        // check first for per instance array
        if (!empty($restore->mods[$modname]->granular)) { // supports per instance
            return array_key_exists($modid,$restore->mods[$modname]->instances)
                && !empty($restore->mods[$modname]->instances[$modid]->userinfo);
        }

        //print_object($restore->mods[$modname]);
        return !empty($restore->mods[$modname]->userinfo);
    }

    function restore_execute(&$restore,$info,$course_header,&$errorstr) {

        global $CFG, $USER;
        $status = true;

        //Checks for the required files/functions to restore every module
        //and include them
        if ($allmods = get_records("modules") ) {
            foreach ($allmods as $mod) {
                $modname = $mod->name;
                $modfile = "$CFG->dirroot/mod/$modname/restorelib.php";
                //If file exists and we have selected to restore that type of module
                if ((file_exists($modfile)) and !empty($restore->mods[$modname]) and ($restore->mods[$modname]->restore)) {
                    include_once($modfile);
                }
            }
        }

        if (!defined('RESTORE_SILENTLY')) {
            //Start the main table
            echo "<table cellpadding=\"5\">";
            echo "<tr><td>";

            //Start the main ul
            echo "<ul>";
        }

        //Location of the xml file
        $xml_file = $CFG->dataroot."/temp/backup/".$restore->backup_unique_code."/moodle.xml";

        // Re-assure xml file is in place before any further process
        if (! $status = restore_check_moodle_file($xml_file)) {
            if (!is_file($xml_file)) {
                $errorstr = 'Error checking backup file. moodle.xml not found. Session problem?';
            } else {
                $errorstr = 'Error checking backup file. moodle.xml is incorrect or corrupted. Session problem?';
            }
            if (!defined('RESTORE_SILENTLY')) {
                notify($errorstr);
            }
            return false;
        }

        //Preprocess the moodle.xml file spliting into smaller chucks (modules, users, logs...)
        //for optimal parsing later in the restore process.
        if (!empty($CFG->experimentalsplitrestore)) {
            if (!defined('RESTORE_SILENTLY')) {
                echo '<li>'.get_string('preprocessingbackupfile') . '</li>';
            }
            //First of all, split moodle.xml into handy files
            if (!restore_split_xml ($xml_file, $restore)) {
                $errorstr = "Error proccessing moodle.xml file. Process ended.";
                if (!defined('RESTORE_SILENTLY')) {
                    notify($errorstr);
                }
                return false;
            }
        }

        // Precheck the users section, detecting various situations that can lead to problems, so
        // we stop restore before performing any further action
        if (!defined('RESTORE_SILENTLY')) {
            echo '<li>'.get_string('restoreusersprecheck').'</li>';
        }
        if (!restore_precheck_users($xml_file, $restore, $problems)) {
            $errorstr = get_string('restoreusersprecheckerror');
            if (!empty($problems)) {
                $errorstr .= ' (' . implode(', ', $problems)  . ')';
            }
            if (!defined('RESTORE_SILENTLY')) {
                notify($errorstr);
            }
            return false;
        }

        //If we've selected to restore into new course
        //create it (course)
        //Saving conversion id variables into backup_tables
        if ($restore->restoreto == RESTORETO_NEW_COURSE) {
            if (!defined('RESTORE_SILENTLY')) {
                echo '<li>'.get_string('creatingnewcourse') . '</li>';
            }
            $oldidnumber = $course_header->course_idnumber;
            if (!$status = restore_create_new_course($restore,$course_header)) {
                if (!defined('RESTORE_SILENTLY')) {
                    notify("Error while creating the new empty course.");
                } else {
                    $errorstr = "Error while creating the new empty course.";
                    return false;
                }
            }

            //Print course fullname and shortname and category
            if ($status) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<ul>";
                    echo "<li>".$course_header->course_fullname." (".$course_header->course_shortname.")".'</li>';
                    echo "<li>".get_string("category").": ".$course_header->category->name.'</li>';
                    if (!empty($oldidnumber)) {
                        echo "<li>".get_string("nomoreidnumber","moodle",$oldidnumber)."</li>";
                    }
                    echo "</ul>";
                    //Put the destination course_id
                }
                $restore->course_id = $course_header->course_id;
            }

            if ($status = restore_open_html($restore,$course_header)){
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>Creating the Restorelog.html in the course backup folder</li>";
                }
            }

        } else {
            $course = get_record("course","id",$restore->course_id);
            if ($course) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>".get_string("usingexistingcourse");
                    echo "<ul>";
                    echo "<li>".get_string("from").": ".$course_header->course_fullname." (".$course_header->course_shortname.")".'</li>';
                    echo "<li>".get_string("to").": ". format_string($course->fullname) ." (".format_string($course->shortname).")".'</li>';
                    if (($restore->deleting)) {
                        echo "<li>".get_string("deletingexistingcoursedata").'</li>';
                    } else {
                        echo "<li>".get_string("addingdatatoexisting").'</li>';
                    }
                    echo "</ul></li>";
                }
                //If we have selected to restore deleting, we do it now.
                if ($restore->deleting) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo "<li>".get_string("deletingolddata").'</li>';
                    }
                    $status = remove_course_contents($restore->course_id,false) and
                        delete_dir_contents($CFG->dataroot."/".$restore->course_id,"backupdata");
                    if ($status) {
                        //Now , this situation is equivalent to the "restore to new course" one (we
                        //have a course record and nothing more), so define it as "to new course"
                        $restore->restoreto = RESTORETO_NEW_COURSE;
                    } else {
                        if (!defined('RESTORE_SILENTLY')) {
                            notify("An error occurred while deleting some of the course contents.");
                        } else {
                            $errrostr = "An error occurred while deleting some of the course contents.";
                            return false;
                        }
                    }
                }
            } else {
                if (!defined('RESTORE_SILENTLY')) {
                    notify("Error opening existing course.");
                    $status = false;
                } else {
                    $errorstr = "Error opening existing course.";
                    return false;
                }
            }
        }

        //Now create users as needed
        if ($status and ($restore->users == 0 or $restore->users == 1)) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatingusers")."<br />";
            }
            if (!$status = restore_create_users($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    notify("Could not restore users.");
                } else {
                    $errorstr = "Could not restore users.";
                    return false;
                }
            }

            //Now print info about the work done
            if ($status) {
                $recs = get_records_sql("select old_id, new_id from {$CFG->prefix}backup_ids
                                     where backup_code = '$restore->backup_unique_code' and
                                     table_name = 'user'");
                //We've records
                if ($recs) {
                    $new_count = 0;
                    $exists_count = 0;
                    $student_count = 0;
                    $teacher_count = 0;
                    $counter = 0;
                    //Iterate, filling counters
                    foreach ($recs as $rec) {
                        //Get full record, using backup_getids
                        $record = backup_getid($restore->backup_unique_code,"user",$rec->old_id);
                        if (strpos($record->info,"new") !== false) {
                            $new_count++;
                        }
                        if (strpos($record->info,"exists") !== false) {
                            $exists_count++;
                        }
                        if (strpos($record->info,"student") !== false) {
                            $student_count++;
                        } else if (strpos($record->info,"teacher") !== false) {
                            $teacher_count++;
                        }
                        //Do some output
                        $counter++;
                        if ($counter % 10 == 0) {
                            if (!defined('RESTORE_SILENTLY')) {
                                echo ".";
                                if ($counter % 200 == 0) {
                                    echo "<br />";
                                }
                            }
                            backup_flush(300);
                        }
                    }
                    if (!defined('RESTORE_SILENTLY')) {
                        //Now print information gathered
                        echo " (".get_string("new").": ".$new_count.", ".get_string("existing").": ".$exists_count.")";
                        echo "<ul>";
                        echo "<li>".get_string("students").": ".$student_count.'</li>';
                        echo "<li>".get_string("teachers").": ".$teacher_count.'</li>';
                        echo "</ul>";
                    }
                } else {
                    if (!defined('RESTORE_SILENTLY')) {
                        notify("No users were found!");
                    } // no need to return false here, it's recoverable.
                }
            }

            if (!defined('RESTORE_SILENTLY')) {
                echo "</li>";
            }
        }


        //Now create groups as needed
        if ($status and ($restore->groups == RESTORE_GROUPS_ONLY or $restore->groups == RESTORE_GROUPS_GROUPINGS)) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatinggroups");
            }
            if (!$status = restore_create_groups($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    notify("Could not restore groups!");
                } else {
                    $errorstr = "Could not restore groups!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Now create groupings as needed
        if ($status and ($restore->groups == RESTORE_GROUPINGS_ONLY or $restore->groups == RESTORE_GROUPS_GROUPINGS)) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatinggroupings");
            }
            if (!$status = restore_create_groupings($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    notify("Could not restore groupings!");
                } else {
                    $errorstr = "Could not restore groupings!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Now create groupingsgroups as needed
        if ($status and $restore->groups == RESTORE_GROUPS_GROUPINGS) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatinggroupingsgroups");
            }
            if (!$status = restore_create_groupings_groups($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    notify("Could not restore groups in groupings!");
                } else {
                    $errorstr = "Could not restore groups in groupings!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }


        //Now create the course_sections and their associated course_modules
        //we have to do this after groups and groupings are restored, because we need the new groupings id
        if ($status) {
            //Into new course
            if ($restore->restoreto == RESTORETO_NEW_COURSE) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>".get_string("creatingsections");
                }
                if (!$status = restore_create_sections($restore,$xml_file)) {
                    if (!defined('RESTORE_SILENTLY')) {
                        notify("Error creating sections in the existing course.");
                    } else {
                        $errorstr = "Error creating sections in the existing course.";
                        return false;
                    }
                }
                if (!defined('RESTORE_SILENTLY')) {
                    echo '</li>';
                }
                //Into existing course
            } else if ($restore->restoreto != RESTORETO_NEW_COURSE) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>".get_string("checkingsections");
                }
                if (!$status = restore_create_sections($restore,$xml_file)) {
                    if (!defined('RESTORE_SILENTLY')) {
                        notify("Error creating sections in the existing course.");
                    } else {
                        $errorstr = "Error creating sections in the existing course.";
                        return false;
                    }
                }
                if (!defined('RESTORE_SILENTLY')) {
                    echo '</li>';
                }
                //Error
            } else {
                if (!defined('RESTORE_SILENTLY')) {
                    notify("Neither a new course or an existing one was specified.");
                    $status = false;
                } else {
                    $errorstr = "Neither a new course or an existing one was specified.";
                    return false;
                }
            }
        }

        //Now create metacourse info
        if ($status and $restore->metacourse) {
            //Only to new courses!
            if ($restore->restoreto == RESTORETO_NEW_COURSE) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>".get_string("creatingmetacoursedata");
                }
                if (!$status = restore_create_metacourse($restore,$xml_file)) {
                    if (!defined('RESTORE_SILENTLY')) {
                        notify("Error creating metacourse in the course.");
                    } else {
                        $errorstr = "Error creating metacourse in the course.";
                        return false;
                    }
                }
                if (!defined('RESTORE_SILENTLY')) {
                    echo '</li>';
                }
            }
        }


        //Now create categories and questions as needed
        if ($status) {
            include_once("$CFG->dirroot/question/restorelib.php");
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatingcategoriesandquestions");
                echo "<ul>";
            }
            if (!$status = restore_create_questions($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    notify("Could not restore categories and questions!");
                } else {
                    $errorstr = "Could not restore categories and questions!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo "</ul></li>";
            }
        }

        //Now create user_files as needed
        if ($status and ($restore->user_files)) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("copyinguserfiles");
            }
            if (!$status = restore_user_files($restore)) {
                if (!defined('RESTORE_SILENTLY')) {
                    notify("Could not restore user files!");
                } else {
                    $errorstr = "Could not restore user files!";
                    return false;
                }
            }
            //If all is ok (and we have a counter)
            if ($status and ($status !== true)) {
                //Inform about user dirs created from backup
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<ul>";
                    echo "<li>".get_string("userzones").": ".$status;
                    echo "</li></ul>";
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Now create course files as needed
        if ($status and ($restore->course_files)) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("copyingcoursefiles");
            }
            if (!$status = restore_course_files($restore)) {
                if (empty($status)) {
                    notify("Could not restore course files!");
                } else {
                    $errorstr = "Could not restore course files!";
                    return false;
                }
            }
            //If all is ok (and we have a counter)
            if ($status and ($status !== true)) {
                //Inform about user dirs created from backup
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<ul>";
                    echo "<li>".get_string("filesfolders").": ".$status.'</li>';
                    echo "</ul>";
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo "</li>";
            }
        }


        //Now create site files as needed
        if ($status and ($restore->site_files)) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string('copyingsitefiles');
            }
            if (!$status = restore_site_files($restore)) {
                if (empty($status)) {
                    notify("Could not restore site files!");
                } else {
                    $errorstr = "Could not restore site files!";
                    return false;
                }
            }
            //If all is ok (and we have a counter)
            if ($status and ($status !== true)) {
                //Inform about user dirs created from backup
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<ul>";
                    echo "<li>".get_string("filesfolders").": ".$status.'</li>';
                    echo "</ul>";
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo "</li>";
            }
        }

        //Now create messages as needed
        if ($status and ($restore->messages)) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatingmessagesinfo");
            }
            if (!$status = restore_create_messages($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    notify("Could not restore messages!");
                } else {
                    $errorstr = "Could not restore messages!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo "</li>";
            }
        }

        //Now create blogs as needed
        if ($status and ($restore->blogs)) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatingblogsinfo");
            }
            if (!$status = restore_create_blogs($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    notify("Could not restore blogs!");
                } else {
                    $errorstr = "Could not restore blogs!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo "</li>";
            }
        }

        //Now create scales as needed
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatingscales");
            }
            if (!$status = restore_create_scales($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    notify("Could not restore custom scales!");
                } else {
                    $errorstr = "Could not restore custom scales!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Now create events as needed
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatingevents");
            }
            if (!$status = restore_create_events($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    notify("Could not restore course events!");
                } else {
                    $errorstr = "Could not restore course events!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Now create course modules as needed
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatingcoursemodules");
            }
            if (!$status = restore_create_modules($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    notify("Could not restore modules!");
                } else {
                    $errorstr = "Could not restore modules!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Bring back the course blocks -- do it AFTER the modules!!!
        if ($status) {
            //If we are deleting and bringing into a course or making a new course, same situation
            if ($restore->restoreto == RESTORETO_CURRENT_DELETING ||
                $restore->restoreto == RESTORETO_EXISTING_DELETING ||
                $restore->restoreto == RESTORETO_NEW_COURSE) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo '<li>'.get_string('creatingblocks');
                }
                $course_header->blockinfo = !empty($course_header->blockinfo) ? $course_header->blockinfo : NULL;
                if (!$status = restore_create_blocks($restore, $info->backup_block_format, $course_header->blockinfo, $xml_file)) {
                    if (!defined('RESTORE_SILENTLY')) {
                        notify('Error while creating the course blocks');
                    } else {
                        $errorstr = "Error while creating the course blocks";
                        return false;
                    }
                }
                if (!defined('RESTORE_SILENTLY')) {
                    echo '</li>';
                }
            }
        }

        if ($status) {
            //If we are deleting and bringing into a course or making a new course, same situation
            if ($restore->restoreto == RESTORETO_CURRENT_DELETING ||
                $restore->restoreto == RESTORETO_EXISTING_DELETING ||
                $restore->restoreto == RESTORETO_NEW_COURSE) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo '<li>'.get_string('courseformatdata');
                }
                if (!$status = restore_set_format_data($restore, $xml_file)) {
                        $error = "Error while setting the course format data";
                    if (!defined('RESTORE_SILENTLY')) {
                        notify($error);
                    } else {
                        $errorstr=$error;
                        return false;
                    }
                }
                if (!defined('RESTORE_SILENTLY')) {
                    echo '</li>';
                }
            }
        }

        //Now create log entries as needed
        if ($status and ($info->backup_logs == 'true' && $restore->logs)) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatinglogentries");
            }
            if (!$status = restore_create_logs($restore,$xml_file)) {
                if (!defined('RESTORE_SILENTLY')) {
                    notify("Could not restore logs!");
                } else {
                    $errorstr = "Could not restore logs!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Now, if all is OK, adjust the instance field in course_modules !!
        //this also calculates the final modinfo information so, after this,
        //code needing it can be used (like role_assignments. MDL-13740)
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("checkinginstances");
            }
            if (!$status = restore_check_instances($restore)) {
                if (!defined('RESTORE_SILENTLY')) {
                    notify("Could not adjust instances in course_modules!");
                } else {
                    $errorstr = "Could not adjust instances in course_modules!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Now, if all is OK, adjust activity events
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("refreshingevents");
            }
            if (!$status = restore_refresh_events($restore)) {
                if (!defined('RESTORE_SILENTLY')) {
                    notify("Could not refresh events for activities!");
                } else {
                    $errorstr = "Could not refresh events for activities!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Now, if all is OK, adjust inter-activity links
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("decodinginternallinks");
            }
            if (!$status = restore_decode_content_links($restore)) {
                if (!defined('RESTORE_SILENTLY')) {
                    notify("Could not decode content links!");
                } else {
                    $errorstr = "Could not decode content links!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Now, with backup files prior to version 2005041100,
        //convert all the wiki texts in the course to markdown
        if ($status && $restore->backup_version < 2005041100) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("convertingwikitomarkdown");
            }
            if (!$status = restore_convert_wiki2markdown($restore)) {
                if (!defined('RESTORE_SILENTLY')) {
                    notify("Could not convert wiki texts to markdown!");
                } else {
                    $errorstr = "Could not convert wiki texts to markdown!";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Now create gradebook as needed -- AFTER modules and blocks!!!
        if ($status) {
            if ($restore->backup_version > 2007090500) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>".get_string("creatinggradebook");
                }
                if (!$status = restore_create_gradebook($restore,$xml_file)) {
                    if (!defined('RESTORE_SILENTLY')) {
                        notify("Could not restore gradebook!");
                    } else {
                        $errorstr = "Could not restore gradebook!";
                        return false;
                    }
                }

                if (!defined('RESTORE_SILENTLY')) {
                    echo '</li>';
                }

            } else {
                // for moodle versions before 1.9, those grades need to be converted to use the new gradebook
                // this code needs to execute *after* the course_modules are sorted out
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>".get_string("migratinggrades");
                }

            /// force full refresh of grading data before migration == crete all items first
                if (!$status = restore_migrate_old_gradebook($restore,$xml_file)) {
                    if (!defined('RESTORE_SILENTLY')) {
                        notify("Could not migrate gradebook!");
                    } else {
                        $errorstr = "Could not migrade gradebook!";
                        return false;
                    }
                }
                if (!defined('RESTORE_SILENTLY')) {
                    echo '</li>';
                }
            }
        /// force full refresh of grading data after all items are created
            grade_force_full_regrading($restore->course_id);
            grade_grab_course_grades($restore->course_id);
        }

        /*******************************************************************************
         ************* Restore of Roles and Capabilities happens here ******************
         *******************************************************************************/
         // try to restore roles even when restore is going to fail - teachers might have
         // at least some role assigned - this is not correct though
        $status = restore_create_roles($restore, $xml_file) && $status;
        $status = restore_roles_settings($restore, $xml_file) && $status;

        //Now if all is OK, update:
        //   - course modinfo field
        //   - categories table
        //   - add user as teacher
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("checkingcourse");
            }
            //categories table
            $course = get_record("course","id",$restore->course_id);
            fix_course_sortorder();
            // Check if the user has course update capability in the newly restored course
            // there is no need to load his capabilities again, because restore_roles_settings
            // would have loaded it anyway, if there is any assignments.
            // fix for MDL-6831
            $newcontext = get_context_instance(CONTEXT_COURSE, $restore->course_id);
            if (!has_capability('moodle/course:manageactivities', $newcontext)) {
                // fix for MDL-9065, use the new config setting if exists
                if ($CFG->creatornewroleid) {
                    role_assign($CFG->creatornewroleid, $USER->id, 0, $newcontext->id);
                } else {
                    if ($legacyteachers = get_roles_with_capability('moodle/legacy:editingteacher', CAP_ALLOW, get_context_instance(CONTEXT_SYSTEM))) {
                        if ($legacyteacher = array_shift($legacyteachers)) {
                            role_assign($legacyteacher->id, $USER->id, 0, $newcontext->id);
                        }
                    } else {
                        notify('Could not find a legacy teacher role. You might need your moodle admin to assign a role with editing privilages to this course.');
                    }
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        //Cleanup temps (files and db)
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("cleaningtempdata");
            }
            if (!$status = clean_temp_data ($restore)) {
                if (!defined('RESTORE_SILENTLY')) {
                    notify("Could not clean up temporary data from files and database");
                } else {
                    $errorstr = "Could not clean up temporary data from files and database";
                    return false;
                }
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        // this is not a critical check - the result can be ignored
        if (restore_close_html($restore)){
            if (!defined('RESTORE_SILENTLY')) {
                echo '<li>Closing the Restorelog.html file.</li>';
            }
        }
        else {
            if (!defined('RESTORE_SILENTLY')) {
                notify("Could not close the restorelog.html file");
            }
        }

        if (!defined('RESTORE_SILENTLY')) {
            //End the main ul
            echo "</ul>";

            //End the main table
            echo "</td></tr>";
            echo "</table>";
        }

        return $status;
    }
    //Create, open and write header of the html log file
    function restore_open_html($restore,$course_header) {

        global $CFG;

        $status = true;

        //Open file for writing
        //First, we check the course_id backup data folder exists and create it as necessary in CFG->dataroot
        if (!$dest_dir = make_upload_directory("$restore->course_id/backupdata")) {   // Backup folder
            error("Could not create backupdata folder.  The site administrator needs to fix the file permissions");
        }
        $status = check_dir_exists($dest_dir,true);
        $restorelog_file = fopen("$dest_dir/restorelog.html","a");
        //Add the stylesheet
        $stylesheetshtml = '';
        foreach ($CFG->stylesheets as $stylesheet) {
            $stylesheetshtml .= '<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />'."\n";
        }
        ///Accessibility: added the 'lang' attribute to $direction, used in theme <html> tag.
        $languagehtml = get_html_lang($dir=true);

        //Write the header in the new logging file
        fwrite ($restorelog_file,"<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"");
        fwrite ($restorelog_file," \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">  ");
        fwrite ($restorelog_file,"<html dir=\"ltr\".$languagehtml.");
        fwrite ($restorelog_file,"<head>");
        fwrite ($restorelog_file,$stylesheetshtml);
        fwrite ($restorelog_file,"<title>".$course_header->course_shortname." Restored </title>");
        fwrite ($restorelog_file,"</head><body><br/><h1>The following changes were made during the Restoration of this Course.</h1><br/><br/>");
        fwrite ($restorelog_file,"The Course ShortName is now - ".$course_header->course_shortname." The FullName is now - ".$course_header->course_fullname."<br/><br/>");
        $startdate = addslashes($course_header->course_startdate);
        $date = usergetdate($startdate);
        fwrite ($restorelog_file,"The Originating Courses Start Date was " .$date['weekday'].", ".$date['mday']." ".$date['month']." ".$date['year']."");
        $startdate += $restore->course_startdateoffset;
        $date = usergetdate($startdate);
        fwrite ($restorelog_file,"&nbsp;&nbsp;&nbsp;This Courses Start Date is now  " .$date['weekday'].",  ".$date['mday']." ".$date['month']." ".$date['year']."<br/><br/>");

        if ($status) {
            return $restorelog_file;
        } else {
            return false;
        }
    }
    //Create & close footer of the html log file
    function restore_close_html($restore) {

        global $CFG;

        $status = true;

        //Open file for writing
        //First, check that course_id/backupdata folder exists in CFG->dataroot
        $dest_dir = $CFG->dataroot."/".$restore->course_id."/backupdata";
        $status = check_dir_exists($dest_dir, true, true);
        $restorelog_file = fopen("$dest_dir/restorelog.html","a");
        //Write the footer to close the logging file
        fwrite ($restorelog_file,"<br/>This file was written to directly by each modules restore process.");
        fwrite ($restorelog_file,"<br/><br/>Log complete.</body></html>");

        if ($status) {
            return $restorelog_file;
        } else {
            return false;
        }
    }

/********************** Roles and Capabilities Related Functions *******************************/

    /* Yu: Note recovering of role assignments/overrides need to take place after
       users have been recovered, i.e. after we get their new_id, and after all
       roles have been recreated or mapped. Contexts can be created on the fly.
       The current order of restore is Restore (old) -> restore roles -> restore assignment/overrides
       the order of restore among different contexts, i.e. course, mod, blocks, users should not matter
       once roles and users have been restored.
     */

    /**
     * This function restores all the needed roles for this course
     * i.e. roles with an assignment in any of the mods or blocks,
     * roles assigned on any user (e.g. parent role) and roles
     * assigned at course levle
     * This function should check for duplicate roles first
     * It isn't now, just overwriting
     */
    function restore_create_roles($restore, $xmlfile) {
        global $CFG;
        if (!defined('RESTORE_SILENTLY')) {
            echo "<li>".get_string("creatingrolesdefinitions").'</li>';
        }
        $info = restore_read_xml_roles($xmlfile);

        $sitecontext = get_context_instance(CONTEXT_SYSTEM);

        // the following code creates new roles
        // but we could use more intelligent detection, and role mapping
        // get role mapping info from $restore
        $rolemappings = array();

        if (!empty($restore->rolesmapping)) {
            $rolemappings = $restore->rolesmapping;
        }
        // $info->roles will be empty for backups pre 1.7
        if (isset($info->roles) && $info->roles) {

            foreach ($info->roles as $oldroleid=>$roledata) {

                if (empty($restore->rolesmapping)) {
                    // if this is empty altogether, we came from import or there's no roles used in course at all
                    // in this case, write the same oldid as this is the same site
                    // no need to do mapping
                    $status = backup_putid($restore->backup_unique_code,"role",$oldroleid,
                                     $oldroleid); // adding a new id
                    continue;  // do not create additonal roles;
                }
            // first we check if the roles are in the mappings
            // if so, we just do a mapping i.e. update oldids table
                if (isset($rolemappings[$oldroleid]) && $rolemappings[$oldroleid]) {
                    $status = backup_putid($restore->backup_unique_code,"role",$oldroleid,
                                     $rolemappings[$oldroleid]); // adding a new id

                // check for permissions before create new roles
                } else if (has_capability('moodle/role:manage', get_context_instance(CONTEXT_SYSTEM))) {

                    // code to make new role name/short name if same role name or shortname exists
                    $fullname = $roledata->name;
                    $shortname = $roledata->shortname;
                    $currentfullname = "";
                    $currentshortname = "";
                    $counter = 0;

                    do {
                        if ($counter) {
                            $suffixfull = " ".get_string("copyasnoun")." ".$counter;
                            $suffixshort = "_".$counter;
                        } else {
                            $suffixfull = "";
                            $suffixshort = "";
                        }
                        $currentfullname = $fullname.$suffixfull;
                        // Limit the size of shortname - database column accepts <= 100 chars
                        $currentshortname = substr($shortname, 0, 100 - strlen($suffixshort)).$suffixshort;
                        $coursefull  = get_record("role","name",addslashes($currentfullname));
                        $courseshort = get_record("role","shortname",addslashes($currentshortname));
                        $counter++;
                    } while ($coursefull || $courseshort);

                    $roledata->name = $currentfullname;
                    $roledata->shortname= $currentshortname;

                    // done finding a unique name

                    $newroleid = create_role(addslashes($roledata->name),addslashes($roledata->shortname),'');
                    $status = backup_putid($restore->backup_unique_code,"role",$oldroleid,
                                     $newroleid); // adding a new id
                    foreach ($roledata->capabilities as $capability) {

                        $roleinfo = new object();
                        $roleinfo = (object)$capability;
                        $roleinfo->contextid = $sitecontext->id;
                        $roleinfo->capability = $capability->name;
                        $roleinfo->roleid = $newroleid;

                        insert_record('role_capabilities', $roleinfo);
                    }
                } else {
                    // map the new role to course default role
                    if (!$default_role = get_field("course", "defaultrole", "id", $restore->course_id)) {
                        $default_role = $CFG->defaultcourseroleid;
                    }
                    $status = backup_putid($restore->backup_unique_code, "role", $oldroleid, $default_role);
                }

            /// Now, restore role nameincourse (only if the role had nameincourse in backup)
                if (!empty($roledata->nameincourse)) {
                    $newrole = backup_getid($restore->backup_unique_code, 'role', $oldroleid); /// Look for target role
                    $coursecontext = get_context_instance(CONTEXT_COURSE, $restore->course_id); /// Look for target context
                    if (!empty($newrole->new_id) && !empty($coursecontext)) {
                    /// Check the role hasn't any custom name in context
                        if (!record_exists('role_names', 'roleid', $newrole->new_id, 'contextid', $coursecontext->id)) {
                            $rolename = new object();
                            $rolename->roleid = $newrole->new_id;
                            $rolename->contextid = $coursecontext->id;
                            $rolename->name = addslashes($roledata->nameincourse);

                            insert_record('role_names', $rolename);
                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * this function restores role assignments and role overrides
     * in course/user/block/mod level, it passed through
     * the xml file again
     */
    function restore_roles_settings($restore, $xmlfile) {
        // data pulls from course, mod, user, and blocks

        /*******************************************************
         * Restoring from course level assignments *
         *******************************************************/
        if (!defined('RESTORE_SILENTLY')) {
            echo "<li>".get_string("creatingcourseroles").'</li>';
        }
        $course = restore_read_xml_course_header($xmlfile);

        if (!isset($restore->rolesmapping)) {
            $isimport = true; // course import from another course, or course with no role assignments
        } else {
            $isimport = false; // course restore with role assignments
        }

        if (!empty($course->roleassignments) && !$isimport) {
            $courseassignments = $course->roleassignments;

            foreach ($courseassignments as $oldroleid => $courseassignment) {
                restore_write_roleassignments($restore, $courseassignment->assignments, "course", CONTEXT_COURSE, $course->course_id, $oldroleid);
            }
        }
        /*****************************************************
         * Restoring from course level overrides *
         *****************************************************/

        if (!empty($course->roleoverrides) && !$isimport) {
            $courseoverrides = $course->roleoverrides;
            foreach ($courseoverrides as $oldroleid => $courseoverride) {
                // if not importing into exiting course, or creating new role, we are ok
                // local course overrides to be respected (i.e. restored course overrides ignored)
                if (($restore->restoreto != RESTORETO_CURRENT_ADDING && $restore->restoreto != RESTORETO_EXISTING_ADDING) || empty($restore->rolesmapping[$oldroleid])) {
                    restore_write_roleoverrides($restore, $courseoverride->overrides, "course", CONTEXT_COURSE, $course->course_id, $oldroleid);
                }
            }
        }

        /*******************************************************
         * Restoring role assignments/overrdies                *
         * from module level assignments                       *
         *******************************************************/

        if (!defined('RESTORE_SILENTLY')) {
            echo "<li>".get_string("creatingmodroles").'</li>';
        }
        $sections = restore_read_xml_sections($xmlfile);
        $secs = $sections->sections;

        foreach ($secs as $section) {
            if (isset($section->mods)) {
                foreach ($section->mods as $modid=>$mod) {
                    if (isset($mod->roleassignments) && !$isimport) {
                        foreach ($mod->roleassignments as $oldroleid=>$modassignment) {
                            restore_write_roleassignments($restore, $modassignment->assignments, "course_modules", CONTEXT_MODULE, $modid, $oldroleid);
                        }
                    }
                    // role overrides always applies, in import or backup/restore
                    if (isset($mod->roleoverrides)) {
                        foreach ($mod->roleoverrides as $oldroleid=>$modoverride) {
                            restore_write_roleoverrides($restore, $modoverride->overrides, "course_modules", CONTEXT_MODULE, $modid, $oldroleid);
                        }
                    }
                }
            }
        }

        /*************************************************
         * Restoring assignments from blocks level       *
         * role assignments/overrides                    *
         *************************************************/

        if ($restore->restoreto != RESTORETO_CURRENT_ADDING && $restore->restoreto != RESTORETO_EXISTING_ADDING) { // skip altogether if restoring to exisitng course by adding
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatingblocksroles").'</li>';
            }
            $blocks = restore_read_xml_blocks($restore, $xmlfile);
            if (isset($blocks->instances)) {
                foreach ($blocks->instances as $instance) {
                    if (isset($instance->roleassignments) && !$isimport) {
                        foreach ($instance->roleassignments as $oldroleid=>$blockassignment) {
                            restore_write_roleassignments($restore, $blockassignment->assignments, "block_instance", CONTEXT_BLOCK, $instance->id, $oldroleid);

                        }
                    }
                    // likewise block overrides should always be restored like mods
                    if (isset($instance->roleoverrides)) {
                        foreach ($instance->roleoverrides as $oldroleid=>$blockoverride) {
                            restore_write_roleoverrides($restore, $blockoverride->overrides, "block_instance", CONTEXT_BLOCK, $instance->id, $oldroleid);
                        }
                    }
                }
            }
        }

        /************************************************
         * Restoring assignments from userid level      *
         * role assignments/overrides                   *
         ************************************************/
        if (!defined('RESTORE_SILENTLY')) {
            echo "<li>".get_string("creatinguserroles").'</li>';
        }
        $info = restore_read_xml_users($restore, $xmlfile);
        if (!empty($info->users) && !$isimport) { // no need to restore user assignments for imports (same course)
            //For each user, take its info from backup_ids
            foreach ($info->users as $userid) {
                $rec = backup_getid($restore->backup_unique_code,"user",$userid);
                if (isset($rec->info->roleassignments)) {
                    foreach ($rec->info->roleassignments as $oldroleid=>$userassignment) {
                       restore_write_roleassignments($restore, $userassignment->assignments, "user", CONTEXT_USER, $userid, $oldroleid);
                    }
                }
                if (isset($rec->info->roleoverrides)) {
                    foreach ($rec->info->roleoverrides as $oldroleid=>$useroverride) {
                       restore_write_roleoverrides($restore, $useroverride->overrides, "user", CONTEXT_USER, $userid, $oldroleid);
                    }
                }
            }
        }

        return true;
    }

    // auxillary function to write role assignments read from xml to db
    function restore_write_roleassignments($restore, $assignments, $table, $contextlevel, $oldid, $oldroleid) {

        $role = backup_getid($restore->backup_unique_code, "role", $oldroleid);

        foreach ($assignments as $assignment) {

            $olduser = backup_getid($restore->backup_unique_code,"user",$assignment->userid);
            //Oh dear, $olduser... can be an object, $obj->string or bool!
            if (!$olduser || (is_string($olduser->info) && $olduser->info == "notincourse")) { // it's possible that user is not in the course
                continue;
            }
            $assignment->userid = $olduser->new_id; // new userid here
            $oldmodifier = backup_getid($restore->backup_unique_code,"user",$assignment->modifierid);
            $assignment->modifierid = !empty($oldmodifier->new_id) ? $oldmodifier->new_id : 0; // new modifier id here
            $assignment->roleid = $role->new_id; // restored new role id

            // hack to make the correct contextid for course level imports
            if ($contextlevel == CONTEXT_COURSE) {
                $oldinstance->new_id = $restore->course_id;
            } else {
                $oldinstance = backup_getid($restore->backup_unique_code,$table,$oldid);
            }

            // new instance id not found (not restored module/block/user)... skip any assignment
            if (!$oldinstance || empty($oldinstance->new_id)) {
                continue;
            }

            $newcontext = get_context_instance($contextlevel, $oldinstance->new_id);
            $assignment->contextid = $newcontext->id; // new context id
            // might already have same assignment
            role_assign($assignment->roleid, $assignment->userid, 0, $assignment->contextid, $assignment->timestart, $assignment->timeend, $assignment->hidden, $assignment->enrol, $assignment->timemodified);

        }
    }

    // auxillary function to write role assignments read from xml to db
    function restore_write_roleoverrides($restore, $overrides, $table, $contextlevel, $oldid, $oldroleid) {

        // it is possible to have an override not relevant to this course context.
        // should be ignored(?)
        if (!$role = backup_getid($restore->backup_unique_code, "role", $oldroleid)) {
            return null;
        }

        foreach ($overrides as $override) {
            $override->capability = $override->name;
            $oldmodifier = backup_getid($restore->backup_unique_code,"user",$override->modifierid);
            $override->modifierid = !empty($oldmodifier->new_id)?$oldmodifier->new_id:0; // new modifier id here
            $override->roleid = $role->new_id; // restored new role id

            // hack to make the correct contextid for course level imports
            if ($contextlevel == CONTEXT_COURSE) {
                $oldinstance->new_id = $restore->course_id;
            } else {
                $oldinstance = backup_getid($restore->backup_unique_code,$table,$oldid);
            }

            // new instance id not found (not restored module/block/user)... skip any override
            if (!$oldinstance || empty($oldinstance->new_id)) {
                continue;
            }

            $newcontext = get_context_instance($contextlevel, $oldinstance->new_id);
            $override->contextid = $newcontext->id; // new context id
            // use assign capability instead so we can add context to context_rel
            assign_capability($override->capability, $override->permission, $override->roleid, $override->contextid);
        }
    }

    /**
     * true or false function to see if user can roll dates on restore (any course is enough)
     * @return bool
     */
    function restore_user_can_roll_dates() {
        global $USER;
        // if user has moodle/restore:rolldates capability at system or any course cat return true

        if (has_capability('moodle/restore:rolldates', get_context_instance(CONTEXT_SYSTEM))) {
            return true;
        }

        // Non-cached - get accessinfo
        if (isset($USER->access)) {
            $accessinfo = $USER->access;
        } else {
            $accessinfo = get_user_access_sitewide($USER->id);
        }
        $courses = get_user_courses_bycap($USER->id, 'moodle/restore:rolldates', $accessinfo, true);
        return !empty($courses);
    }

    //write activity date changes to the html log file, and update date values in the the xml array
    function restore_log_date_changes($recordtype, &$restore, &$xml, $TAGS, $NAMETAG='NAME') {

        global $CFG;
        $openlog = false;

        // loop through time fields in $TAGS
        foreach ($TAGS as $TAG) {

            // check $TAG has a sensible value
            if (!empty($xml[$TAG][0]['#']) && is_string($xml[$TAG][0]['#']) && is_numeric($xml[$TAG][0]['#'])) {

                if ($openlog==false) {
                    $openlog = true; // only come through here once

                    // open file for writing
                    $course_dir = "$CFG->dataroot/$restore->course_id/backupdata";
                    check_dir_exists($course_dir, true);
                    $restorelog = fopen("$course_dir/restorelog.html", "a");

                    // start output for this record
                    $msg = new stdClass();
                    $msg->recordtype = $recordtype;
                    $msg->recordname = $xml[$NAMETAG][0]['#'];
                    fwrite ($restorelog, get_string("backupdaterecordtype", "moodle", $msg));
                }

                // write old date to $restorelog
                $value = $xml[$TAG][0]['#'];
                $date = usergetdate($value);

                $msg = new stdClass();
                $msg->TAG = $TAG;
                $msg->weekday = $date['weekday'];
                $msg->mday = $date['mday'];
                $msg->month = $date['month'];
                $msg->year = $date['year'];
                fwrite ($restorelog, get_string("backupdateold", "moodle", $msg));

                // write new date to $restorelog
                $value += $restore->course_startdateoffset;
                $date = usergetdate($value);

                $msg = new stdClass();
                $msg->TAG = $TAG;
                $msg->weekday = $date['weekday'];
                $msg->mday = $date['mday'];
                $msg->month = $date['month'];
                $msg->year = $date['year'];
                fwrite ($restorelog, get_string("backupdatenew", "moodle", $msg));

                // update $value in $xml tree for calling module
                $xml[$TAG][0]['#'] = "$value";
            }
        }
        // close the restore log, if it was opened
        if ($openlog) {
           fclose($restorelog);
        }
    }
?>
