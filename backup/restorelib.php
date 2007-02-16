<?php //$Id$
    //Functions used in restore
   
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
    //function in each module, passing them the desired contents to be decoded
    //from backup format to destination site/course in order to mantain inter-activities
    //working in the backup/restore process
    function restore_decode_content_links($restore) {
        global $CFG;

        $status = true;

        if (!defined('RESTORE_SILENTLY')) {
            echo "<ul>";
        }
        foreach ($restore->mods as $name => $info) {
            //If the module is being restored
            if (isset($info->restore) && $info->restore == 1) {
                //Check if the xxxx_decode_content_links_caller exists
                include_once("$CFG->dirroot/mod/$name/restorelib.php");
                $function_name = $name."_decode_content_links_caller";
                if (function_exists($function_name)) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo "<li>".get_string ("from")." ".get_string("modulenameplural",$name);
                        echo '</li>';
                    }
                    $status = $function_name($restore);
                }
            }
        }
        if (!defined('RESTORE_SILENTLY')) {
            echo "</ul>";
        }
        
        // TODO: process all html text also in blocks too
        
        return $status;
    }
    
    //This function is called from all xxxx_decode_content_links_caller(),
    //its task is to ask all modules (maybe other linkable objects) to restore
    //links to them.
    function restore_decode_content_links_worker($content,$restore) {
        foreach($restore->mods as $name => $info) {
            $function_name = $name."_decode_content_links";
            if (function_exists($function_name)) {
                $content = $function_name($content,$restore);
            }
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
    function restore_read_xml_blocks ($xml_file) {

        //We call the main read_xml function, with todo = BLOCKS
        $info = restore_read_xml ($xml_file,'BLOCKS',false);

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
            //Messages info (only showed if present)
            if ($info->backup_messages == 'true') {
                $tab[$elem][0] = "<b>".get_string('messages','message').":</b>";
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

    //This function create a new course record.
    //When finished, course_header contains the id of the new course
    function restore_create_new_course($restore,&$course_header) {

        global $CFG;
 
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
            // Limit the size of shortname - database column accepts <= 15 chars
            $currentshortname = substr($shortname, 0, 15 - strlen($suffixshort)).$suffixshort;
            $coursefull  = get_record("course","fullname",addslashes($currentfullname));
            $courseshort = get_record("course","shortname",addslashes($currentshortname));
            $counter++;
        } while ($coursefull || $courseshort);

        //New name = currentname
        $course_header->course_fullname = $currentfullname;
        $course_header->course_shortname = $currentshortname;
        
        //Now calculate the category
        $category = get_record("course_categories","id",$course_header->category->id,
                                                   "name",addslashes($course_header->category->name));
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
            $course->category = addslashes($course_header->category->id);
            $course->password = addslashes($course_header->course_password);
            $course->fullname = addslashes($course_header->course_fullname);
            $course->shortname = addslashes($course_header->course_shortname);
            $course->idnumber = addslashes($course_header->course_idnumber);
            $course->idnumber = ''; //addslashes($course_header->course_idnumber); // we don't want this at all.
            $course->summary = restore_decode_absolute_links(addslashes($course_header->course_summary));
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
            $course->enrolperiod = addslashes($course_header->course_enrolperiod);
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
            } else {
                $status = false;
            }
        }

        return $status;
    }

   /**
    * Returns the best question category (id) found to restore one
    * question category from a backup file. Works by stamp (since Moodle 1.1)
    * or by name (for older versions).
    *
    * @param object  $cat      the question_categories record to be searched
    * @param integer $courseid the course where we are restoring
    * @return integer the id of a existing question_category or 0 (not found)
    */
    function restore_get_best_question_category($cat, $courseid) {
        
        $found = 0;

        //Decide how to work (by stamp or name)
        if ($cat->stamp) {
            $searchfield = 'stamp';
            $searchvalue = $cat->stamp;
        } else {
            $searchfield = 'name';
            $searchvalue = $cat->name;
        }
        
        //First shot. Try to get the category from the course being restored
        if ($fcat = get_record('question_categories','course',$courseid,$searchfield,$searchvalue)) {
            $found = $fcat->id;
        //Second shot. Try to obtain any concordant category and check its publish status and editing rights
        } else if ($fcats = get_records('question_categories', $searchfield, $searchvalue, 'id', 'id, publish, course')) {
            foreach ($fcats as $fcat) {
                if ($fcat->publish == 1 && has_capability('moodle/site:restore', get_context_instance(CONTEXT_COURSE, $fcat->course))) {
                    $found = $fcat->id;
                    break;
                }
            }
        }

        return $found;
    }

    //This function creates all the block stuff when restoring courses
    //It calls selectively to  restore_create_block_instances() for 1.5 
    //and above backups. Upwards compatible with old blocks.
    function restore_create_blocks($restore, $backup_block_format, $blockinfo, $xml_file) {

        $status = true;

        delete_records('block_instance', 'pageid', $restore->course_id, 'pagetype', PAGE_COURSE_VIEW);
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

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            $info = restore_read_xml_blocks($xml_file);
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
            }
            else {
                $parts = explode('-', $instance->pagetype);
                if($parts[0] == 'mod') {
                    if(!$restore->mods[$parts[1]]->restore) {
                        continue;
                    }
                    $getid = backup_getid($restore->backup_unique_code, $parts[1], $instance->pageid);
                    $instance->pageid = $getid->new_id;
                }
                else {
                    // Not invented here ;-)
                    continue;
                }
            }

            if(!isset($pageinstances[$instance->pagetype])) {
                $pageinstances[$instance->pagetype] = array();
            }
            if(!isset($pageinstances[$instance->pagetype][$instance->pageid])) {
                $pageinstances[$instance->pagetype][$instance->pageid] = array();
            }

            $pageinstances[$instance->pagetype][$instance->pageid][] = $instance;
        }

        $blocks = get_records_select('block', '', '', 'name, id, multiple');

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
                    
                    if ($newid = insert_record('block_instance', $instance)) {
                        if (!empty($instance->id)) { // this will only be set if we come from 1.7 and above backups
                            backup_putid ($restore->backup_unique_code,"block_instance",$instance->id,$newid);
                        }
                    } else {
                        $status = false;
                        break;                      
                    }
                    
                    //Get an object for the block and tell it it's been restored so it can update dates
                    //etc. if necessary
                    $blockobj=block_instance($instance->name,$instance);
                    $blockobj->after_restore($restore);
    
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
                $section->course = $restore->course_id;
                $section->section = $sect->number;
                $section->summary = restore_decode_absolute_links(addslashes($sect->summary));
                $section->visible = $sect->visible;
                $section->sequence = "";
                //Now calculate the section's newid
                $newid = 0;
                if ($restore->restoreto == 2) {
                    //Save it to db (only if restoring to new course)
                    $newid = insert_record("course_sections",$section);
                } else {
                    //Get section id when restoring in existing course
                    $rec = get_record("course_sections","course",$restore->course_id,
                                                        "section",$section->section);
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
                            if (!isset($restore->mods[$mod->type]->granular) && isset($restore->mods[$mod->type]->instances) && is_array($restore->mods[$mod->type]->instances)) {
                                // This defines whether we want to restore specific
                                // instances of the modules (granular restore), or
                                // whether we don't care and just want to restore
                                // all module instances (non-granular).
                                $restore->mods[$mod->type]->granular = true;
                            } else {
                                $restore->mods[$mod->type]->granular = false;
                            }
                          
                            //Check if we've to restore this module (and instance) 
                            if (!empty($restore->mods[$mod->type]->restore)) {                      
                                if (empty($restore->mods[$mod->type]->granular)  // we don't care about per instance
                                    || (array_key_exists($mod->instance,$restore->mods[$mod->type]->instances) 
                                        && !empty($restore->mods[$mod->type]->instances[$mod->instance]->restore))) {
                                    
                                    //Get the module id from modules
                                    $module = get_record("modules","name",$mod->type);
                                    if ($module) {
                                        $course_module->course = $restore->course_id;
                                        $course_module->module = $module->id;
                                        $course_module->section = $newid;
                                        $course_module->added = $mod->added;
                                        $course_module->score = $mod->score;
                                        $course_module->indent = $mod->indent;
                                        $course_module->visible = $mod->visible;
                                        if (isset($mod->groupmode)) {
                                            $course_module->groupmode = $mod->groupmode;
                                        }
                                        $course_module->instance = 0;
                                        //NOTE: The instance (new) is calculated and updated in db in the
                                        //      final step of the restore. We don't know it yet.
                                        //print_object($course_module);                    //Debug
                                        //Save it to db

                                        $newidmod = insert_record("course_modules",$course_module); 
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
                    if ($restore->original_wwwroot == $CFG->wwwroot) {
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
                    if ($restore->original_wwwroot == $CFG->wwwroot) {
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
    
    //This function creates all the gradebook data from xml, notifying 
    //about each incidence
    function restore_create_gradebook($restore,$xml_file) {

        global $CFG,$db;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            //info will contain the number of record to process
            $info = restore_read_xml_gradebook($restore, $xml_file);

        //If we have info, then process preferences, letters & categories
            if ($info > 0) {
                //Count how many we have
                $preferencescount = count_records ('backup_ids', 'backup_code', $restore->backup_unique_code, 'table_name', 'grade_preferences');
                $letterscount = count_records ('backup_ids', 'backup_code', $restore->backup_unique_code, 'table_name', 'grade_letter');
                $categoriescount = count_records ('backup_ids', 'backup_code', $restore->backup_unique_code, 'table_name', 'grade_category');

                if ($preferencescount || $letterscount || $categoriescount) {
                    //Start ul
                    if (!defined('RESTORE_SILENTLY')) {
                        echo '<ul>';
                    }
                    //Number of records to get in every chunk
                    $recordset_size = 2;
                    //Flag to mark if we must continue
                    $continue = true;

                    //If there aren't preferences, stop
                    if (!$preferencescount) {
                        $continue = false;
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<li>'.get_string('backupwithoutgradebook','grade').'</li>';
                        }
                    }

                    //If we are restoring to an existing course and it has advanced disabled, stop
                    if ($restore->restoreto != 2) {
                        //Fetch the 'use_advanced' preferences (id = 0)
                        if ($pref_rec = get_record('grade_preferences','courseid',$restore->course_id,'preference',0)) {
                            if ($pref_rec->value == 0) {
                                $continue = false;
                                if (!defined('RESTORE_SILENTLY')) {
                                    echo '<li>'.get_string('respectingcurrentdata','grade').'</li>';
                                }
                            }
                        }
                    }

                    //Process preferences
                    if ($preferencescount && $continue) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<li>'.get_string('preferences','grades').'</li>';
                        }
                        $counter = 0;
                        while ($counter < $preferencescount) {
                            //Fetch recordset_size records in each iteration
                            $recs = get_records_select("backup_ids","table_name = 'grade_preferences' AND backup_code = '$restore->backup_unique_code'",
                                                       "old_id",
                                                       "old_id, old_id",
                                                       $counter,
                                                       $recordset_size);
                            if ($recs) {
                                foreach ($recs as $rec) {
                                    //Get the full record from backup_ids
                                    $data = backup_getid($restore->backup_unique_code,'grade_preferences',$rec->old_id);
                                    if ($data) {
                                        //Now get completed xmlized object
                                        $info = $data->info;
                                        //traverse_xmlize($info);                            //Debug
                                        //print_object ($GLOBALS['traverse_array']);         //Debug
                                        //$GLOBALS['traverse_array']="";                     //Debug
                                        //Now build the GRADE_PREFERENCES record structure
                                        $dbrec->courseid   = $restore->course_id;
                                        $dbrec->preference = backup_todb($info['GRADE_PREFERENCE']['#']['PREFERENCE']['0']['#']);
                                        $dbrec->value      = backup_todb($info['GRADE_PREFERENCE']['#']['VALUE']['0']['#']);
   
                                        //Structure is equal to db, insert record
                                        //if the preference doesn't exist
                                        if (!$prerec = get_record('grade_preferences','courseid',$dbrec->courseid,'preference',$dbrec->preference)) {
                                            $status = insert_record('grade_preferences',$dbrec);
                                        }
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

                    //Process letters
                    //If destination course has letters, skip restoring letters
                    $hasletters = get_records('grade_letter', 'courseid', $restore->course_id);

                    if ($letterscount && $continue && !$hasletters) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<li>'.get_string('letters','grades').'</li>';
                        }
                        $counter = 0;
                        while ($counter < $letterscount) {
                            //Fetch recordset_size records in each iteration
                            $recs = get_records_select("backup_ids","table_name = 'grade_letter' AND backup_code = '$restore->backup_unique_code'",
                                                       "old_id",
                                                       "old_id, old_id",
                                                       $counter,
                                                       $recordset_size);
                            if ($recs) {
                                foreach ($recs as $rec) {
                                    //Get the full record from backup_ids
                                    $data = backup_getid($restore->backup_unique_code,'grade_letter',$rec->old_id);
                                    if ($data) {
                                        //Now get completed xmlized object
                                        $info = $data->info;
                                        //traverse_xmlize($info);                            //Debug
                                        //print_object ($GLOBALS['traverse_array']);         //Debug
                                        //$GLOBALS['traverse_array']="";                     //Debug
                                        //Now build the GRADE_LETTER record structure
                                        $dbrec->courseid   = $restore->course_id;
                                        $dbrec->letter     = backup_todb($info['GRADE_LETTER']['#']['LETTER']['0']['#']);
                                        $dbrec->grade_high = backup_todb($info['GRADE_LETTER']['#']['GRADE_HIGH']['0']['#']);
                                        $dbrec->grade_low  = backup_todb($info['GRADE_LETTER']['#']['GRADE_LOW']['0']['#']);

                                        //Structure is equal to db, insert record
                                        $status = insert_record('grade_letter',$dbrec);
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

                    //Process categories
                    if ($categoriescount && $continue) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<li>'.get_string('categories','grades').'</li>';
                        }
                        $counter = 0;
                        $countercat = 0;
                        while ($countercat < $categoriescount) {
                            //Fetch recordset_size records in each iteration
                            $recs = get_records_select("backup_ids","table_name = 'grade_category' AND backup_code = '$restore->backup_unique_code'",
                                                       "old_id",
                                                       "old_id, old_id",
                                                       $countercat,
                                                       $recordset_size);
                            if ($recs) {
                                foreach ($recs as $rec) {
                                    //Get the full record from backup_ids
                                    $data = backup_getid($restore->backup_unique_code,'grade_category',$rec->old_id);
                                    if ($data) {
                                        //Now get completed xmlized object
                                        $info = $data->info;
                                        //traverse_xmlize($info);                            //Debug
                                        //print_object ($GLOBALS['traverse_array']);         //Debug
                                        //$GLOBALS['traverse_array']="";                     //Debug
                                        //Now build the GRADE_CATEGORY record structure
                                        $dbrec->courseid     = $restore->course_id;
                                        $dbrec->name         = backup_todb($info['GRADE_CATEGORY']['#']['NAME']['0']['#']);
                                        $dbrec->drop_x_lowest= backup_todb($info['GRADE_CATEGORY']['#']['DROP_X_LOWEST']['0']['#']);
                                        $dbrec->bonus_points = backup_todb($info['GRADE_CATEGORY']['#']['BONUS_POINTS']['0']['#']);
                                        $dbrec->hidden       = backup_todb($info['GRADE_CATEGORY']['#']['HIDDEN']['0']['#']);
                                        $dbrec->weight       = backup_todb($info['GRADE_CATEGORY']['#']['WEIGHT']['0']['#']);

                                        //If the grade_category exists in the course (by name), don't insert anything
                                        $catex = get_record('grade_category','courseid',$dbrec->courseid,'name',$dbrec->name);

                                        if (!$catex) {
                                            //Structure is equal to db, insert record
                                            $categoryid = insert_record('grade_category',$dbrec);
                                        } else {
                                            //Simply remap category
                                            $categoryid = $catex->id;
                                        }

                                        //Now, restore grade_item
                                        $items = $info['GRADE_CATEGORY']['#']['GRADE_ITEMS']['0']['#']['GRADE_ITEM'];

                                        //Iterate over items
                                        for($i = 0; $i < sizeof($items); $i++) {
                                            $ite_info = $items[$i];
                                            //traverse_xmlize($ite_info);                                                                 //Debug
                                            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                                            //$GLOBALS['traverse_array']="";                                                              //Debug

                                            //Now build the GRADE_ITEM record structure
                                            $item->courseid     = $restore->course_id;
                                            $item->category     = $categoryid;
                                            $item->module_name  = backup_todb($ite_info['#']['MODULE_NAME']['0']['#']);
                                            $item->cminstance   = backup_todb($ite_info['#']['CMINSTANCE']['0']['#']);
                                            $item->scale_grade  = backup_todb($ite_info['#']['SCALE_GRADE']['0']['#']);
                                            $item->extra_credit = backup_todb($ite_info['#']['EXTRA_CREDIT']['0']['#']);
                                            $item->sort_order   = backup_todb($ite_info['#']['SORT_ORDER']['0']['#']);

                                            //Check that the module has been included in the restore
                                            if ($restore->mods[$item->module_name]->restore) {
                                                //Get the id of the moduletype
                                                if ($module = get_record('modules','name',$item->module_name)) {
                                                    $item->modid = $module->id;
                                                    //Get the instance id
                                                    if ($instance = backup_getid($restore->backup_unique_code,$item->module_name,$item->cminstance)) {
                                                        $item->cminstance = $instance->new_id;
                                                        //Structure is equal to db, insert record
                                                        $itemid = insert_record('grade_item',$item);

                                                        //Now process grade_exceptions
                                                        if (!empty($ite_info['#']['GRADE_EXCEPTIONS'])) {
                                                            $exceptions = $ite_info['#']['GRADE_EXCEPTIONS']['0']['#']['GRADE_EXCEPTION'];
                                                        } else {
                                                            $exceptions = array();
                                                        }

                                                        //Iterate over exceptions
                                                        for($j = 0; $j < sizeof($exceptions); $j++) {
                                                            $exc_info = $exceptions[$j];
                                                            //traverse_xmlize($exc_info);                                                                 //Debug
                                                            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                                                            //$GLOBALS['traverse_array']="";                                                              //Debug

                                                            //Now build the GRADE_EXCEPTIONS record structure
                                                            $exception->courseid     = $restore->course_id;
                                                            $exception->grade_itemid = $itemid;
                                                            $exception->userid       = backup_todb($exc_info['#']['USERID']['0']['#']);

                                                            //We have to recode the useridto field
                                                            $user = backup_getid($restore->backup_unique_code,"user",$exception->userid);
                                                            if ($user) {
                                                                $exception->userid = $user->new_id;
                                                                //Structure is equal to db, insert record
                                                                $exceptionid = insert_record('grade_exceptions',$exception);
                                                                //Do some output
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

                                        //Re-sort all the grade_items because we can have duplicates
                                        if ($grade_items = get_records ('grade_item','courseid',$restore->course_id,'sort_order','id,sort_order')) {
                                            $order = 1;
                                            foreach ($grade_items as $grade_item) {
                                                $grade_item->sort_order = $order;
                                                set_field ('grade_item','sort_order',$grade_item->sort_order,'id',$grade_item->id);
                                                $order++;
                                            }
                                        }
                                    }
                                    //Increment counters
                                    $countercat++;
                                    //Do some output
                                    if ($countercat % 1 == 0) {
                                        if (!defined('RESTORE_SILENTLY')) {
                                            echo ".";
                                            if ($countercat % 20 == 0) {
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

    //This function creates all the user, user_students, user_teachers
    //user_course_creators and user_admins from xml
    function restore_create_users($restore,$xml_file) {

        global $CFG, $db;

        $status = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            //info will contain the old_id of every user
            //in backup_ids->info will be the real info (serialized)
            $info = restore_read_xml_users($restore,$xml_file);
        }

        //Now, get evey user_id from $info and user data from $backup_ids
        //and create the necessary records (users, user_students, user_teachers
        //user_course_creators and user_admins
        if (!empty($info->users)) {
            // Grab mnethosts keyed by wwwroot, to map to id
            $mnethosts = get_records('mnethost', '', '', 
                                     'wwwroot', 'wwwroot, id');

            $languages = get_list_of_languages();

            foreach ($info->users as $userid) {
                $rec = backup_getid($restore->backup_unique_code,"user",$userid); 
                $user = $rec->info;

                //Now, recode some languages (Moodle 1.5)
                if ($user->lang == 'ma_nt') {
                    $user->lang = 'mi_nt';
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

                //Calculate mnethostid
                if (empty($user->mnethosturl) || $user->mnethosturl===$CFG->wwwroot) {
                    $user->mnethostid = $CFG->mnet_localhost_id;
                } else {
                    // fast url-to-id lookups
                    if (isset($mnethosts[$user->mnethosturl])) {
                        $user->mnethostid = $mnethosts[$user->mnethosturl]->id;
                    } else { 
                        // should not happen, as we check in restore_chech.php 
                        // but handle the error if it does
                        error("This backup file contains external Moodle Network Hosts that are not configured locally.");
                    }
                }
                unset($user->mnethosturl);

                //To store new ids created
                $newid=null;
                //check if it exists (by username) and get its id
                $user_exists = true;
                $user_data = get_record("user","username",addslashes($user->username), 
                                        'mnethostid', $user->mnethostid);
                if (!$user_data) {
                    $user_exists = false;
                } else {
                    $newid = $user_data->id;
                }
                //Flags to see if we have to create the user, roles and preferences
                $create_user = true;
                $create_roles = true;
                $create_preferences = true;

                //If we are restoring course users and it isn't a course user
                if ($restore->users == 1 and !$is_course_user) {
                    //If only restoring course_users and user isn't a course_user, inform to $backup_ids
                    $status = backup_putid($restore->backup_unique_code,"user",$userid,null,'notincourse');
                    $create_user = false;
                    $create_roles = false;
                    $create_preferences = false;
                }

                if ($user_exists and $create_user) {
                    //If user exists mark its newid in backup_ids (the same than old)
                    $status = backup_putid($restore->backup_unique_code,"user",$userid,$newid,'exists');
                    $create_user = false;
                }

                //Here, if create_user, do it
                if ($create_user) {
                    //Unset the id because it's going to be inserted with a new one
                    unset ($user->id);
                    //We addslashes to necessary fields
                    $user->username = addslashes($user->username);
                    $user->firstname = addslashes($user->firstname);
                    $user->lastname = addslashes($user->lastname);
                    $user->email = addslashes($user->email);
                    $user->institution = addslashes($user->institution);
                    $user->department = addslashes($user->department);
                    $user->address = addslashes($user->address);
                    $user->city = addslashes($user->city);
                    $user->url = addslashes($user->url);
                    $user->description = restore_decode_absolute_links(addslashes($user->description));

                    //We need to analyse the AUTH field to recode it:
                    //   - if the field isn't set, we are in a pre 1.4 backup and we'll 
                    //     use $CFG->auth
                    //   - if the destination site has any kind of INTERNAL authentication, 
                    //     then apply it to the new user.
                    //   - if the destination site has any kind of EXTERNAL authentication, 
                    //     then leave the original authentication of the user.

                    if ((! isset($user->auth)) || is_internal_auth($CFG->auth)) {
                        $user->auth = $CFG->auth;
                    }

                    //We need to process the POLICYAGREED field to recalculate it:
                    //    - if the destination site is different (by wwwroot) reset it.
                    //    - if the destination site is the same (by wwwroot), leave it unmodified

                    if ($restore->original_wwwroot != $CFG->wwwroot) {
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
                    $newid = insert_record ("user",$user);
                    //Put the new id
                    $status = backup_putid($restore->backup_unique_code,"user",$userid,$newid,"new");
                }

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
                        } else if ($restore->original_wwwroot != $CFG->wwwroot) {
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
                        } else if ($restore->original_wwwroot != $CFG->wwwroot) {
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

                //Here, if create_preferences, do it as necessary
                if ($create_preferences) {
                    //echo "Checking for preferences of user ".$user->username."<br />";         //Debug
                    //Get user new id from backup_ids
                    $data = backup_getid($restore->backup_unique_code,"user",$userid);
                    $newid = $data->new_id;
                    if (isset($user->user_preferences)) {
                        //echo "Preferences exist in backup file<br />";                         //Debug
                        foreach($user->user_preferences as $user_preference) {
                            //echo $user_preference->name." = ".$user_preference->value."<br />";    //Debug
                            //We check if that user_preference exists in DB
                            if (!record_exists("user_preferences","userid",$newid,"name",$user_preference->name)) {
                                //echo "Creating it<br />";                                              //Debug
                                //Prepare the record and insert it
                                $user_preference->userid = $newid;
                                $status = insert_record("user_preferences",$user_preference);
                            }
                        }
                    }
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
                            $recs = get_records_select("backup_ids","table_name = 'message' AND backup_code = '$restore->backup_unique_code'","old_id","old_id, old_id",$counter,$recordset_size);
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
                            $recs = get_records_select("backup_ids","table_name = 'message_read' AND backup_code = '$restore->backup_unique_code'","old_id","old_id, old_id",$counter,$recordset_size);
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
                            $recs = get_records_select("backup_ids","table_name = 'message_contacts' AND backup_code = '$restore->backup_unique_code'","old_id","old_id, old_id",$counter,$recordset_size);
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
                //Iterate over each category
                foreach ($info as $category) {
                    //Skip empty categories (some backups can contain them)
                    if (!empty($category->id)) {
                        $catrestore = "restore_question_categories";
                        if (function_exists($catrestore)) {
                            //print_object ($category);                                                //Debug
                            $status = $catrestore($category,$restore);
                        } else {
                            //Something was wrong. Function should exist.
                            $status = false;
                        }
                    }
                }

                //Now we have to recode the parent field of each restored category
                $categories = get_records_sql("SELECT old_id, new_id 
                                               FROM {$CFG->prefix}backup_ids
                                               WHERE backup_code = $restore->backup_unique_code AND
                                                     table_name = 'question_categories'");
                if ($categories) {
                    foreach ($categories as $category) {
                        $restoredcategory = get_record('question_categories','id',$category->new_id);
                        $restoredcategory = addslashes_object($restoredcategory);
                        if ($restoredcategory->parent != 0) {
                            $idcat = backup_getid($restore->backup_unique_code,'question_categories',$restoredcategory->parent);
                            if ($idcat->new_id) {
                                $restoredcategory->parent = $idcat->new_id;
                            } else {
                                $restoredcategory->parent = 0;
                            }
                            update_record('question_categories', $restoredcategory);
                        }
                    }
                }
            }
        } else {
            $status = false;
        }   
        return $status;
    }

    //This function creates all the scales
    function restore_create_scales($restore,$xml_file) {

        global $CFG, $db;

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
            //Get admin->id for later use
            $admin = get_admin();
            $adminid = $admin->id;
            if ($scales !== true) {
                //Iterate over each scale
                foreach ($scales as $scale) {
                    //Get record from backup_ids
                    $data = backup_getid($restore->backup_unique_code,"scale",$scale->id);
                    //Init variables
                    $create_scale = false;

                    if ($data) {
                        //Now get completed xmlized object
                        $info = $data->info;
                        //traverse_xmlize($info);                                                                     //Debug
                        //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                        //$GLOBALS['traverse_array']="";                                                              //Debug

                        //Now build the SCALE record structure
                        $sca->courseid = backup_todb($info['SCALE']['#']['COURSEID']['0']['#']);
                        $sca->userid = backup_todb($info['SCALE']['#']['USERID']['0']['#']);
                        $sca->name = backup_todb($info['SCALE']['#']['NAME']['0']['#']);
                        $sca->scale = backup_todb($info['SCALE']['#']['SCALETEXT']['0']['#']);
                        $sca->description = backup_todb($info['SCALE']['#']['DESCRIPTION']['0']['#']);
                        $sca->timemodified = backup_todb($info['SCALE']['#']['TIMEMODIFIED']['0']['#']);

                        //Now search if that scale exists (by scale field) in course 0 (Standar scale)
                        //or in restore->course_id course (Personal scale)
                        if ($sca->courseid == 0) {
                            $course_to_search = 0;
                        } else {
                            $course_to_search = $restore->course_id;
                        }
                        $sca_db = get_record("scale","scale",$sca->scale,"courseid",$course_to_search);
                        //If it doesn't exist, create
                        if (!$sca_db) {
                            $create_scale = true;
                        } 
                        //If we must create the scale
                        if ($create_scale) {
                            //Me must recode the courseid if it's <> 0 (common scale)
                            if ($sca->courseid != 0) {
                                $sca->courseid = $restore->course_id;
                            }
                            //We must recode the userid
                            $user = backup_getid($restore->backup_unique_code,"user",$sca->userid);
                            if ($user) {
                                $sca->userid = $user->new_id;
                            } else {
                                //Assign it to admin
                                $sca->userid = $adminid;
                            }
                            //The structure is equal to the db, so insert the scale
                            $newid = insert_record ("scale",$sca);
                        } else {
                            //get current scale id
                            $newid = $sca_db->id;
                        }
                        if ($newid) {
                            //We have the newid, update backup_ids
                            backup_putid($restore->backup_unique_code,"scale",
                                         $scale->id, $newid);
                        }
                    }
                }
            }
        } else {
            $status = false;
        }  
        return $status;
    }

    //This function creates all the groups
    function restore_create_groups($restore,$xml_file) {

        global $CFG, $db;

        $status = true;
        $status2 = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            //groups will contain the old_id of every group
            //in backup_ids->info will be the real info (serialized)
            $groups = restore_read_xml_groups($restore,$xml_file);
        }
        //Now, if we have anything in groups, we have to restore that
        //groups
        if ($groups) {
            if ($groups !== true) {
                //Iterate over each group
                foreach ($groups as $group) {
                    //Get record from backup_ids
                    $data = backup_getid($restore->backup_unique_code,"groups",$group->id);
                    //Init variables
                    $create_group = false;

                    if ($data) {
                        //Now get completed xmlized object
                        $info = $data->info;
                        //traverse_xmlize($info);                                                                     //Debug
                        //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                        //$GLOBALS['traverse_array']="";                                                              //Debug
                        //Now build the GROUP record structure
                        $gro = new Object();
                        ///$gro->courseid = backup_todb($info['GROUP']['#']['COURSEID']['0']['#']);
                        $gro->name = backup_todb($info['GROUP']['#']['NAME']['0']['#']);
                        $gro->description = backup_todb($info['GROUP']['#']['DESCRIPTION']['0']['#']);
                        if (isset($info['GROUP']['#']['ENROLMENTKEY']['0']['#'])) {
                            $gro->enrolmentkey = backup_todb($info['GROUP']['#']['ENROLMENTKEY']['0']['#']);
                        } else { //if (! isset($gro->enrolment)) {
                            $gro->enrolmentkey = backup_todb($info['GROUP']['#']['PASSWORD']['0']['#']);
                        }
                        $gro->lang = backup_todb($info['GROUP']['#']['LANG']['0']['#']);
                        $gro->theme = backup_todb($info['GROUP']['#']['THEME']['0']['#']);
                        $gro->picture = backup_todb($info['GROUP']['#']['PICTURE']['0']['#']);
                        $gro->hidepicture = backup_todb($info['GROUP']['#']['HIDEPICTURE']['0']['#']);
                        $gro->timecreated = backup_todb($info['GROUP']['#']['TIMECREATED']['0']['#']);
                        $gro->timemodified = backup_todb($info['GROUP']['#']['TIMEMODIFIED']['0']['#']);
                
                        //Now search if that group exists (by name and description field) in 
                        //restore->course_id course
                        $gro_db = groups_group_matches($restore->course_id, $gro->name, $gro->description); 
                        //If it doesn't exist, create
                        if (!$gro_db) {
                            $create_group = true;
                        }
                        //If we must create the group
                        if ($create_group) {
                            //Me must recode the courseid to the restore->course_id 
                            $gro->courseid = $restore->course_id;

                            //Check if the theme exists in destination server
                            $themes = get_list_of_themes();
                            if (!in_array($gro->theme, $themes)) {
                                $gro->theme = '';
                            }

                            //The structure is equal to the db, so insert the group
                            $newid = groups_restore_group($restore->course_id, $gro);
                        } else { 
                            //get current group id
                            $newid = $gro_db->id;
                        }
                        if ($newid) {
                            //We have the newid, update backup_ids
                            backup_putid($restore->backup_unique_code,"groups",
                                         $group->id, $newid);
                        }
                        //Now restore members in the groups_members, only if
                        //users are included
                        if ($restore->users != 2) {
                            $status2 = restore_create_groups_members($newid,$info,$restore);
                        }
                    }   
                }
                //Now, restore group_files
                if ($status && $status2) {
                    $status2 = restore_group_files($restore); 
                }
            }
        } else {
            $status = false;
        } 
        return ($status && $status2);
    }

    //This function restores the groups_members
    function restore_create_groups_members($group_id,$info,$restore) {

        global $CFG;

        $status = true;

        if (! isset($info['GROUP']['#']['MEMBERS']['0']['#']['MEMBER'])) {
            //OK, some groups have no members.
            return $status;
        }
        //Get the members array
        $members = $info['GROUP']['#']['MEMBERS']['0']['#']['MEMBER'];

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

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$group_member->userid);
            if ($user) {
                $group_member->userid = $user->new_id;
            }

            //The structure is equal to the db, so insert the groups_members
            $newid = groups_restore_member($group_member);
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
            
            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }
    
    //This function creates all the groupings
    function restore_create_groupings($restore,$xml_file) {

        global $CFG, $db;

        $status = true;
        $status2 = true;
        //Check it exists
        if (!file_exists($xml_file)) {
            $status = false;
        }
        //Get info from xml
        if ($status) {
            //groupings will contain the old_id of every group
            //in backup_ids->info will be the real info (serialized)
            $groupings = restore_read_xml_groupings($restore,$xml_file);
        }
        //Now, if we have anything in groupings, we have to restore that grouping
        if ($groupings) {
            if ($groupings !== true) {
                //Iterate over each group
                foreach ($groupings as $grouping) {
                    //Get record from backup_ids
                    $data = backup_getid($restore->backup_unique_code,"groupings",$grouping->id);
                    //Init variables
                    $create_grouping = false;

                    if ($data) {
                        //Now get completed xmlized object
                        $info = $data->info;
                        //Now build the GROUPING record structure
                        $gro = new Object();
                        ///$gro->id = backup_todb($info['GROUPING']['#']['ID']['0']['#']);
                        $gro->name = backup_todb($info['GROUPING']['#']['NAME']['0']['#']);
                        $gro->description = backup_todb($info['GROUPING']['#']['DESCRIPTION']['0']['#']);
                        $gro->timecreated = backup_todb($info['GROUPING']['#']['TIMECREATED']['0']['#']);
                
                        //Now search if that group exists (by name and description field) in 
                        //restore->course_id course
                        $gro_db = groups_grouping_matches($restore->course_id, $gro->name, $gro->description); 
                        //If it doesn't exist, create
                        if (!$gro_db) {
                            $create_grouping = true;
                        }
                        //If we must create the group
                        if ($create_grouping) {

                            //The structure is equal to the db, so insert the grouping TODO: RESTORE.
                            $newid = groups_create_grouping($restore->course_id, $gro);
                        } else { 
                            //get current group id
                            $newid = $gro_db->id;
                        }
                        if ($newid) {
                            //We have the newid, update backup_ids
                            backup_putid($restore->backup_unique_code,"groupings",
                                         $grouping->id, $newid);
                        }
                        //Now restore links from groupings to groups
                        $status2 = restore_create_groupings_groups($newid,$info,$restore);
                    }   
                }
                //(Now, restore grouping_files)
            }
        } else {
            $status = false;
        } 
        return ($status && $status2);
    }
    
    //This function restores the groups_members
    function restore_create_groupings_groups($grouping_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the members array
        $members = $info['GROUPING']['#']['GROUPS']['0']['#']['GROUP'];

        //Iterate over members
        for($i = 0; $i < sizeof($members); $i++) {
            $mem_info = $members[$i];
            //Now, build the GROUPINGS_GROUPS record structure
            $gro_member = new Object();
            $gro_member->groupingid = $grouping_id;
            $gro_member->groupid = backup_todb($mem_info['#']['GROUPID']['0']['#']);
            $gro_member->timeadded = backup_todb($mem_info['#']['TIMEADDED']['0']['#']);

            //We have to recode the userid field
            ///$user = backup_getid($restore->backup_unique_code,"user",$group_member->userid);
            $group = backup_getid($restore->backup_unique_code,"group",$gro_member->groupid);
            if ($group) {
                $gro_member->groupid = $group->new_id;
            }

            //The structure is equal to the db, so link the groups to the groupings. TODO: RESTORE.
            $newid = groups_add_group_to_grouping($gro_member->groupid, $gro_member->groupingid);
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
            
            if (!$newid) {
                $status = false;
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
                        $eve->instance = 0;
                        $eve->eventtype = backup_todb($info['EVENT']['#']['EVENTTYPE']['0']['#']);  
                        $eve->timestart = backup_todb($info['EVENT']['#']['TIMESTART']['0']['#']);
                        $eve->timeduration = backup_todb($info['EVENT']['#']['TIMEDURATION']['0']['#']);
                        $eve->visible = backup_todb($info['EVENT']['#']['VISIBLE']['0']['#']);
                        $eve->timemodified = backup_todb($info['EVENT']['#']['TIMEMODIFIED']['0']['#']);

                        //Now search if that event exists (by description and timestart field) in
                        //restore->course_id course 
                        $eve_db = get_record("event","courseid",$eve->courseid,"description",$eve->description,"timestart",$eve->timestart);
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

                            //We must recode the repeatid if the event has it
                            if (!empty($eve->repeatid)) {
                                $repeat_rec = backup_getid($restore->backup_unique_code,"event_repeatid",$eve->repeatid);
                                if ($repeat_rec) {    //Exists, so use it...
                                    $eve->repeatid = $repeat_rec->new_id;
                                } else {              //Doesn't exists, calculate the next and save it
                                    $oldrepeatid = $eve->repeatid;
                                    $max_rec = get_record_sql('SELECT 1, MAX(repeatid) AS repeatid FROM '.$CFG->prefix.'event');
                                    $eve->repeatid = empty($max_rec) ? 1 : $max_rec->repeatid + 1;
                                    backup_putid($restore->backup_unique_code,"event_repeatid", $oldrepeatid, $eve->repeatid);
                                }
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
    //Note: Inter-activities linking is being implemented as a final
    //step in the restore execution, because we need to have it 
    //finished to know all the oldid, newid equivaleces
    function restore_decode_absolute_links($content) {
                                     
        global $CFG,$restore;    

        //Now decode wwwroot and file.php calls
        $search = array ("$@FILEPHP@$");

        //Check for the status of the slasharguments config variable
        $slash = $CFG->slasharguments;
        
        //Build the replace string as needed
        if ($slash == 1) {
            $replace = array ($CFG->wwwroot."/file.php/".$restore->course_id);
        } else {
            $replace = array ($CFG->wwwroot."/file.php?file=/".$restore->course_id);
        }
    
        $result = str_replace($search,$replace,$content);

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

        //First, we check to "users" exists and create is as necessary
        //in CFG->dataroot
        $dest_dir = $CFG->dataroot."/users";
        $status = check_dir_exists($dest_dir,true);

        //Now, we iterate over "user_files" records to check if that user dir must be
        //copied (and renamed) to the "users" dir.
        $rootdir = $CFG->dataroot."/temp/backup/".$restore->backup_unique_code."/user_files";
        //Check if directory exists
        if (is_dir($rootdir)) {
            $list = list_directories ($rootdir);
            if ($list) {
                //Iterate
                $counter = 0;
                foreach ($list as $dir) {
                    //Look for dir like username in backup_ids
                    $data = get_record ("backup_ids","backup_code",$restore->backup_unique_code,
                                                     "table_name","user",
                                                     "old_id",$dir);
                    //If thar user exists in backup_ids
                    if ($data) {
                        //Only it user has been created now
                        //or if it existed previously, but he hasn't image (see bug 1123)
                        if ((strpos($data->info,"new") !== false) or 
                            (!check_dir_exists($dest_dir."/".$data->new_id,false))) {
                            //Copy the old_dir to its new location (and name) !!
                            //Only if destination doesn't exists
                            if (!file_exists($dest_dir."/".$data->new_id)) {
                                $status = backup_copy_file($rootdir."/".$dir,
                                              $dest_dir."/".$data->new_id,true);
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
                            $status = $status and $modrestore($mod,$restore); //bit operator & not reliable here!
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
                $logs = get_records_select("backup_ids","table_name = 'log' AND backup_code = '$restore->backup_unique_code'","old_id","old_id,old_id",$counter,$recordset_size);
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
            }
        }


        return $status;
    }

    //=====================================================================================
    //==                                                                                 ==
    //==                         XML Functions (SAX)                                     ==
    //==                                                                                 ==
    //=====================================================================================

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
            backup_flush();

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
            backup_flush();

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
            backup_flush();

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
            backup_flush();

            //Check if we are into BLOCKS zone
            //if ($this->tree[3] == "BLOCKS")                                                         //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";   //Debug
        }

        //This is the startTag handler we use where we are reading the sections zone (todo="SECTIONS")
        function startElementSections($parser, $tagName, $attrs) {
            //Refresh properties     
            $this->level++;
            $this->tree[$this->level] = $tagName;   

            //Output something to avoid browser timeouts...
            backup_flush();

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
            backup_flush();

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
            backup_flush();

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
            backup_flush();

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
            backup_flush();

            //Check if we are into MESSAGES zone
            //if ($this->tree[3] == "MESSAGES")                                                          //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br />\n";  //Debug

            //If we are under a MESSAGE tag under a MESSAGES zone, accumule it
            if (isset($this->tree[4]) and isset($this->tree[3])) {
                if (($this->tree[4] == "MESSAGE" || $this->tree[5] == "CONTACT" ) and ($this->tree[3] == "MESSAGES")) {
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
            backup_flush();

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
            backup_flush();

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
            backup_flush();

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
        
        function startElementGroupings($parser, $tagName, $attrs) { //TODO:
            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //if ($tagName == "GROUPING" && $this->tree[3] == "GROUPINGS") {                                 //Debug
            //    echo "<P>GROUPING: ".strftime ("%X",time()),"-";                                        //Debug
            //}                                                                                        //Debug

            //Output something to avoid browser timeouts...
            backup_flush();

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

        //This is the startTag handler we use where we are reading the events zone (todo="EVENTS")
        function startElementEvents($parser, $tagName, $attrs) {
            //Refresh properties
            $this->level++;
            $this->tree[$this->level] = $tagName;

            //if ($tagName == "EVENT" && $this->tree[3] == "EVENTS") {                                 //Debug
            //    echo "<P>EVENT: ".strftime ("%X",time()),"-";                                        //Debug
            //}                                                                                        //Debug

            //Output something to avoid browser timeouts...
            backup_flush();

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
            backup_flush();

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
            backup_flush();

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
            backup_flush();

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
                        case "MNET_EXTERNALUSERS":
                            $this->info->mnet_externalusers = $this->getContents();
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
                            case "MESSAGES":
                                $this->info->backup_messages = $this->getContents();
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
            //Check if we are into INFO zone
            if ($this->tree[2] == "ROLES") {

                if ($this->tree[3] == "ROLE") {
                    if ($this->level == 4) {
                        switch ($tagName) {
                            case "NAME":
                                $this->info->tempname = $this->getContents();
                                
                                break;
                            case "SHORTNAME":
                                $this->info->tempshortname = $this->getContents();
                                break;
                            case "ID": // this is the old id
                                $this->info->tempid = $this->getContents();
                                break;
                        }
                    }
                    if ($this->level == 6) {
                        switch ($tagName) {
                            case "NAME":
                                $this->info->roles[$this->info->tempid]->name = $this->info->tempname;
                                $this->info->roles[$this->info->tempid]->shortname = $this->info->tempshortname;
                                
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

            //Stop parsing if todo = INFO and tagName = INFO (en of the tag, of course)
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
                        case "ENROLPERIOD":
                            $this->info->course_enrolperiod = $this->getContents();
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
                //Dependig of different combinations, do different things
                if ($this->level == 4) {
                    switch ($tagName) {
                        case 'BLOCK':
                            //We've finalized a block, get it
                            $this->info->instances[] = $this->info->tempinstance;
                            unset($this->info->tempinstance);
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
                        case "SECRET": 
                            $this->info->tempuser->secret = $this->getContents();
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
                        case "TIMEMODIFIED": 
                            $this->info->tempuser->timemodified = $this->getContents();
                            break;
                        default:
                            break;
                    }
                }
                if ($this->level == 6 && $this->tree[5]!="ROLE_ASSIGNMENTS" && $this->tree[5]!="ROLE_OVERRIDES") {
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
                    }
                }
                              
                if ($this->level == 7 && $this->tree[5]!="ROLE_ASSIGNMENTS" && $this->tree[5]!="ROLE_OVERRIDES") {
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
                        case "NAME":
                            $this->info->tempuserpreference->name = $this->getContents();
                            break;
                        case "VALUE":
                            $this->info->tempuserpreference->value = $this->getContents();
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

        //This is the endTag handler we use where we are reading the groups zone (todo="GROUPINGS")
        function endElementGroupings($parser, $tagName) { //TODO:
            //Check if we are into GROUPS zone
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
                        $ret_info->id = $mod_id;
                        $ret_info->modtype = $mod_type;
                        $this->info[] = $ret_info;
                    }
                    //Reset temp
                    unset($this->temp);
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
        
        $status = true;

        $xml_parser = xml_parser_create('UTF-8');
        $moodle_parser = new MoodleParser();
        $moodle_parser->todo = $todo;
        $moodle_parser->preferences = $preferences;
        xml_set_object($xml_parser,$moodle_parser);
        //Depending of the todo we use some element_handler or another
        if ($todo == "INFO") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementInfo", "endElementInfo");
        } else if ($todo == "ROLES") {
            // Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementRoles", "endElementRoles");
        } else if ($todo == "COURSE_HEADER") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementCourseHeader", "endElementCourseHeader");
        } else if ($todo == 'BLOCKS') {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementBlocks", "endElementBlocks");
        } else if ($todo == "SECTIONS") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementSections", "endElementSections");
        } else if ($todo == 'FORMATDATA') {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementFormatData", "endElementFormatData");
        } else if ($todo == "METACOURSE") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementMetacourse", "endElementMetacourse");
        } else if ($todo == "GRADEBOOK") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementGradebook", "endElementGradebook");
        } else if ($todo == "USERS") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementUsers", "endElementUsers");
        } else if ($todo == "MESSAGES") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementMessages", "endElementMessages");
        } else if ($todo == "QUESTIONS") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementQuestions", "endElementQuestions");
        } else if ($todo == "SCALES") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementScales", "endElementScales");
        } else if ($todo == "GROUPS") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementGroups", "endElementGroups");
        } else if ($todo == "GROUPINGS") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementGroupings", "endElementGroupings");
        } else if ($todo == "EVENTS") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementEvents", "endElementEvents");
        } else if ($todo == "MODULES") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementModules", "endElementModules");
        } else if ($todo == "LOGS") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementLogs", "endElementLogs");
        } else {
            //Define default handlers (must no be invoked when everything become finished)
            xml_set_element_handler($xml_parser, "startElementInfo", "endElementInfo");
        }
        xml_set_character_data_handler($xml_parser, "characterData");
        $fp = fopen($xml_file,"r")
            or $status = false;
        if ($status) {
            while ($data = fread($fp, 4096) and !$moodle_parser->finished)
                    xml_parse($xml_parser, $data, feof($fp))
                            or die(sprintf("XML error: %s at line %d",
                            xml_error_string(xml_get_error_code($xml_parser)),
                                    xml_get_current_line_number($xml_parser)));
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
        }
        
        if (!defined('RESTORE_SILENTLY')) {
            //End the main ul
            echo "</ul>\n";
            
            //End the main table
            echo "</td></tr>";
            echo "</table>";
        }
        
        //We compare Moodle's versions
        if ($CFG->version < $info->backup_moodle_version && $status) {
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
                echo "<br /><center>";
                $hidden["backup_unique_code"] = $backup_unique_code;
                $hidden["launch"]             = "form";
                $hidden["file"]               =  $file;
                $hidden["id"]                 =  $id;
                print_single_button("restore.php", $hidden, get_string("continue"),"post");
                echo "</center>";
            }
            else {
                if (empty($noredirect)) {
                    redirect($CFG->wwwroot.'/backup/restore.php?backup_unique_code='.$backup_unique_code.'&launch=form&file='.$file.'&id='.$id);
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
        $restore->course_files = $SESSION->restore->restore_course_files;
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
        return true;
    }

    function backup_to_restore_array($backup,$k=0) {
        if (is_array($backup) ) {
            foreach ($backup as $key => $value) {
                $newkey = str_replace('backup','restore',$key);
                $restore[$newkey] = backup_to_restore_array($value,$key);
            }
        }
        else if (is_object($backup)) { 
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
                if ((file_exists($modfile)) and ($restore->mods[$modname]->restore)) {
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
        
        //Localtion of the xml file
        $xml_file = $CFG->dataroot."/temp/backup/".$restore->backup_unique_code."/moodle.xml";
        
        //If we've selected to restore into new course
        //create it (course)
        //Saving conversion id variables into backup_tables
        if ($restore->restoreto == 2) {
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
                echo "<li>Creating the Restorelog.html in the course backup folder</li>";
            }

        } else {
            $course = get_record("course","id",$restore->course_id);
            if ($course) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>".get_string("usingexistingcourse"); 
                    echo "<ul>";
                    echo "<li>".get_string("from").": ".$course_header->course_fullname." (".$course_header->course_shortname.")".'</li>';
                    echo "<li>".get_string("to").": ".$course->fullname." (".$course->shortname.")".'</li>';
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
                        $restore->restoreto = 2;
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
        
        //Now create the course_sections and their associated course_modules
        if ($status) {
            //Into new course
            if ($restore->restoreto == 2) {
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
            } else if ($restore->restoreto == 0 or $restore->restoreto == 1) {
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
        }
        
        //Now create metacourse info
        if ($status and $restore->metacourse) {
            //Only to new courses!
            if ($restore->restoreto == 2) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo "</li><li>".get_string("creatingmetacoursedata");
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
        if ($status and ($restore->mods['quiz']->restore)) {
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
                echo "<li>".get_string("copyingcoursefiles")."</li>";
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

        //Now create groups as needed
        if ($status) {
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
        if ($status) {
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

        //Now create gradebook as needed -- AFTER modules!!!
        if ($status) {
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
        }

        //Bring back the course blocks -- do it AFTER the modules!!!
        if($status) {
            //If we are deleting and bringing into a course or making a new course, same situation
            if($restore->restoreto == 0 || $restore->restoreto == 2) {
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

        if($status) {
            //If we are deleting and bringing into a course or making a new course, same situation
            if($restore->restoreto == 0 || $restore->restoreto == 2) {
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
        if ($status and ($restore->logs)) {
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

        /*******************************************************************************
         ************* Restore of Roles and Capabilities happens here ******************
         *******************************************************************************/
        
        $status = restore_create_roles($restore, $xml_file);
        $status = restore_roles_settings($restore, $xml_file);

        //Now if all is OK, update:
        //   - course modinfo field 
        //   - categories table
        //   - add user as teacher
        if ($status) {
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("checkingcourse");
            } 
            //modinfo field
            rebuild_course_cache($restore->course_id);
            //categories table
            $course = get_record("course","id",$restore->course_id); 
            fix_course_sortorder();
            // Check if the user has course update capability in the newly restored course
            // there is no need to load his capabilities again, because restore_roles_settings
            // would have loaded it anyway, if there is any assignments. 
            // fix for MDL-6831
            $newcontext = get_context_instance(CONTEXT_COURSE, $restore->course_id);
            if (!has_capability('moodle/course:manageactivities', $newcontext)) {
                if ($legacyteachers = get_roles_with_capability('moodle/legacy:editingteacher', CAP_ALLOW, get_context_instance(CONTEXT_SYSTEM, SITEID))) {
                    if ($legacyteacher = array_shift($legacyteachers)) {
                        role_assign($legacyteacher->id, $USER->id, 0, $newcontext->id);
                    }
                } else {
                    notify('Could not find a legacy teacher role. You might need your moodle admin to assign a role with editing privilages to this course.');
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

        if ($status = restore_close_html($restore)){
            echo '<li>Closing the Restorelog.html file.</li>';
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
        $language = str_replace('_utf8','',$CFG->lang);
        $languagehtml = '';
        $languagehtml .= ' lang="'.$language.'" xml:lang="'.$language.'"';
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
        if (!defined('RESTORE_SILENTLY')) {
            echo "<li>".get_string("creatingrolesdefinitions").'</li>';
        }
        $info = restore_read_xml_roles($xmlfile);

        $sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);

        // the following code creates new roles
        // but we could use more intelligent detection, and role mapping
        // get role mapping info from $restore
        $rolemappings = array();

        if (!empty($restore->rolesmapping)) {
            $rolemappings = $restore->rolesmapping;
        }
        if (isset($info->roles) && $info->roles) {
            foreach ($info->roles as $oldroleid=>$roledata) {
            
            /// first we check if the roles are in the mappings
            // if so, we just do a mapping i.e. update oldids table
                if (isset($rolemappings[$oldroleid]) && $rolemappings[$oldroleid]) {
                    $status = backup_putid($restore->backup_unique_code,"role",$oldroleid,
                                     $rolemappings[$oldroleid]); // adding a new id     
              
                } else {
            
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
                        // Limit the size of shortname - database column accepts <= 15 chars
                        $currentshortname = substr($shortname, 0, 15 - strlen($suffixshort)).$suffixshort;
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
        
        if (!empty($course->roleassignments)) {
            $courseassignments = $course->roleassignments;

            foreach ($courseassignments as $oldroleid => $courseassignment) {    
                restore_write_roleassignments($restore, $courseassignment->assignments, "course", CONTEXT_COURSE, $course->course_id, $oldroleid);
            }
        }
        /*****************************************************
         * Restoring from course level overrides *
         *****************************************************/     
        
        if (!empty($course->roleoverrides)) {
            $courseoverrides = $course->roleoverrides;
            foreach ($courseoverrides as $oldroleid => $courseoverride) {
                // if not importing into exiting course, or creating new role, we are ok
                // local course overrides to be respected (i.e. restored course overrides ignored)
                if ($restore->restoreto != 1 || empty($restore->rolesmapping[$oldroleid])) {
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
                    if (isset($mod->roleassignments)) {
                        foreach ($mod->roleassignments as $oldroleid=>$modassignment) {
                            restore_write_roleassignments($restore, $modassignment->assignments, "course_modules", CONTEXT_MODULE, $modid, $oldroleid);                       
                        }  
                    } 
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

        if ($restore->restoreto != 1) { // skip altogether if restoring to exisitng course by adding
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("creatingblocksroles").'</li>';
            }
            $blocks = restore_read_xml_blocks($xmlfile);
            if (isset($blocks->instances)) {
                foreach ($blocks->instances as $instance) {
                    if (isset($instance->roleassignments)) {
                        foreach ($instance->roleassignments as $oldroleid=>$blockassignment) {
                            restore_write_roleassignments($restore, $blockassignment->assignments, "block_instance", CONTEXT_BLOCK, $instance->id, $oldroleid);
                      
                        }
                    }
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
        if (!empty($info->users)) {
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

            $newcontext = get_context_instance($contextlevel, $oldinstance->new_id);
            $assignment->contextid = $newcontext->id; // new context id
            // might already have same assignment           
            role_assign($assignment->roleid, $assignment->userid, 0, $assignment->contextid, $assignment->timestart, $assignment->timeend, $assignment->hidden, $assignment->enrol);
            
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
            $override->modifierid = $oldmodifier->new_id?$oldmodifier->new_id:0; // new modifier id here
            $override->roleid = $role->new_id; // restored new role id
            
            // hack to make the correct contextid for course level imports
            if ($contextlevel == CONTEXT_COURSE) {
                $oldinstance->new_id = $restore->course_id;
            } else {
                $oldinstance = backup_getid($restore->backup_unique_code,$table,$oldid);
            }
            
            $newcontext = get_context_instance($contextlevel, $oldinstance->new_id);
            $override->contextid = $newcontext->id; // new context id                 
            // might already have same override
            if (!get_record('role_capabilities', 'capability', $override->capability, 'roleid', $override->roleid, 'contextid', $override->contextid)) {
                insert_record('role_capabilities', $override);
            }
        }  
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