<?PHP //$Id$
    //Functions used in restore
   
    //This function unzips a zip file in the same directory that it is
    //It automatically uses pclzip or command line unzip
    function restore_unzip ($file) {
        
        global $CFG;

        $status = true;

        if (empty($CFG->unzip)) {    // Use built-in php-based unzip function
            include_once("$CFG->dirroot/lib/pclzip/pclzip.lib.php");
            //include_once("$CFG->dirroot/lib/pclzip/pclerror.lib.php");    //Debug
            //include_once("$CFG->dirroot/lib/pclzip/pcltrace.lib.php");    //Debug
            //PclTraceOn(2);                                          //Debug
            $archive = new PclZip($file);
            if (!$list = $archive->extract(dirname($file))) {
                $status = false;
            }
            //PclTraceDisplay();                                       //Debug
            //PclTraceOff();                                           //Debug
        } else {                     // Use external unzip program
            $command = "cd ".dirname($file)."; $CFG->unzip -o ".basename($file);
            Exec($command);
        }

        return $status;
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
            if ($module->restore == 1) {
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

        echo "<ul>";
        foreach ($restore->mods as $name => $info) {
            //If the module is being restored
            if ($info->restore == 1) {
                //Check if the xxxx_decode_content_links_caller exists
                $function_name = $name."_decode_content_links_caller";
                if (function_exists($function_name)) {
                    echo "<li>".get_string ("to")." ".get_string("modulenameplural",$name);
                    $status = $function_name($restore);
                }
            }
        }

        //Now I'm going to decode to their new location all the links in wiki texts
        //having the syntax " modulename:moduleid".
        echo "<li>wiki";
        $status = restore_decode_wiki_texts($restore);

        echo "</ul>";

        return $status;
    }

    //This function search for some wiki texts in differenct parts of Moodle to
    //decode them to their new ids.
    function restore_decode_wiki_texts($restore) {

        global $CFG;

        $status = true;

        echo "<ul>";

        if (file_exists("$CFG->dirroot/mod/resource/lib.php")) {
            include_once("$CFG->dirroot/mod/resource/lib.php");
        }

        $formatwiki = FORMAT_WIKI;
        $typewiki = WIKITEXT;
 
        //FORUM: Decode every POST (message) in the course
        //Check we are restoring forums
        if ($restore->mods['forum']->restore == 1) {
            echo "<li>".get_string("from")." ".get_string("modulenameplural","forum");
            //Get all course posts being restored
            if ($posts = get_records_sql ("SELECT p.id, p.message
                                       FROM {$CFG->prefix}forum_posts p,
                                            {$CFG->prefix}forum_discussions d,
                                            {$CFG->prefix}backup_ids b
                                       WHERE d.course = $restore->course_id AND
                                             p.discussion = d.id AND
                                             p.format = $formatwiki AND
                                             b.backup_code = $restore->backup_unique_code AND
                                             b.table_name = 'forum_posts' AND
                                             b.new_id = p.id")) {
                //Iterate over each post->message
                $i = 0;   //Counter to send some output to the browser to avoid timeouts
                foreach ($posts as $post) {
                    //Increment counter
                    $i++;
                    $content = $post->message;
                    //Decode it 
                    $result = restore_decode_wiki_content($content,$restore);

                    if ($result != $content) {
                        //Update record
                        $post->message = addslashes($result);
                        $status = update_record("forum_posts",$post);
                        if ($CFG->debug>7) {
                            echo "<br><hr>".$content."<br>changed to</br>".$result."<hr><br>";
                        }
                    }
                    //Do some output
                    if (($i+1) % 5 == 0) {
                        echo ".";
                        if (($i+1) % 100 == 0) {
                            echo "<br>";
                        }
                        backup_flush(300);
                    }
                }
            }
        }

        //RESOURCE: Decode every RESOURCE (alltext) in the coure

        //Check we are restoring resources
        if ($restore->mods['resource']->restore == 1) {
            echo "<li>".get_string("from")." ".get_string("modulenameplural","resource");
            //Get all course resources of type=8 WIKITEXT being restored
            if ($resources = get_records_sql ("SELECT r.id, r.alltext
                                       FROM {$CFG->prefix}resource r,
                                            {$CFG->prefix}backup_ids b
                                       WHERE r.course = $restore->course_id AND
                                             r.type = $typewiki AND
                                             b.backup_code = $restore->backup_unique_code AND
                                             b.table_name = 'resource' AND
                                             b.new_id = r.id")) {
                //Iterate over each resource->alltext
                $i = 0;   //Counter to send some output to the browser to avoid timeouts
                foreach ($resources as $resource) {
                    //Increment counter
                    $i++;
                    $content = $resource->alltext;
                    //Decode it
                    $result = restore_decode_wiki_content($content,$restore);
                    if ($result != $content) {
                        //Update record
                        $resource->alltext = addslashes($result);
                        $status = update_record("resource",$resource);
                        if ($CFG->debug>7) {
                            echo "<br><hr>".$content."<br>changed to</br>".$result."<hr><br>";
                        }
                    }
                    //Do some output
                    if (($i+1) % 5 == 0) {
                        echo ".";
                        if (($i+1) % 100 == 0) {
                            echo "<br>";
                        }
                        backup_flush(300);
                    }
                }
            }
        }
        echo "</ul>";

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

    //This function read the xml file and store its data from the sections in a object
    function restore_read_xml_sections ($xml_file) {

        //We call the main read_xml function, with todo = SECTIONS
        $info = restore_read_xml ($xml_file,"SECTIONS",false);

        return $info;
    }
    
    //This function read the xml file and store its data from the users in 
    //backup_ids->info db (and user's id in $info)
    function restore_read_xml_users ($restore,$xml_file) {

        //We call the main read_xml function, with todo = USERS
        $info = restore_read_xml ($xml_file,"USERS",$restore);

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

    //This function prints the contents from the info parammeter passed
    function restore_print_info ($info) {

        $status = true;
        if ($info) {
            //This is tha align to every ingo table      
            $table->align = array ("RIGHT","LEFT");
            //This is the nowrap clause 
            $table->wrap = array ("","NOWRAP");
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
                }
                $elem++;
            }
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
            $table->align = array ("RIGHT","LEFT");
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
                $suffixfull = " ".get_string("copy")." ".$counter;
                $suffixshort = "_".$counter;
            } else {
                $suffixfull = "";
                $suffixshort = "";
            }
            $currentfullname = $fullname.$suffixfull;
            $currentshortname = $shortname.$suffixshort;
            $course = get_record("course","fullname",addslashes($currentfullname));
            $counter++;
        } while ($course);

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
            $course->summary = restore_decode_absolute_links(addslashes($course_header->course_summary));
            $course->format = addslashes($course_header->course_format);
            $course->showgrades = addslashes($course_header->course_showgrades);
            $course->blockinfo = addslashes($course_header->blockinfo);
            $course->newsitems = addslashes($course_header->course_newsitems);
            $course->teacher = addslashes($course_header->course_teacher);
            $course->teachers = addslashes($course_header->course_teachers);
            $course->student = addslashes($course_header->course_student);
            $course->students = addslashes($course_header->course_students);
            $course->guest = addslashes($course_header->course_guest);
            $course->startdate = addslashes($course_header->course_startdate);
            $course->numsections = addslashes($course_header->course_numsections);
            //$course->showrecent = addslashes($course_header->course_showrecent);   INFO: This is out in 1.3
            $course->maxbytes = addslashes($course_header->course_maxbytes);
            $course->showreports = addslashes($course_header->course_showreports);
            $course->groupmode = addslashes($course_header->course_groupmode);
            $course->groupmodeforce = addslashes($course_header->course_groupmodeforce);
            $course->lang = addslashes($course_header->course_lang);
            $course->marker = addslashes($course_header->course_marker);
            $course->visible = addslashes($course_header->course_visible);
            $course->hiddensections = addslashes($course_header->course_hiddensections);
            $course->timecreated = addslashes($course_header->course_timecreated);
            $course->timemodified = addslashes($course_header->course_timemodified);
            //Adjust blockinfo field.
            //If the info doesn't exist in backup, we create defaults, else we recode it 
            //to current site blocks.
            if (!$course->blockinfo) {
                //Create blockinfo default content
                if ($course->format == "social") {
                    $course->blockinfo = blocks_get_default_blocks (NULL,"participants,search_forums,calendar_month,calendar_upcoming,social_activities,recent_activity,admin,course_list");
                } else {
                    //For topics and weeks formats (default built in the function)
                    $course->blockinfo = blocks_get_default_blocks();
                }
            } else {
                $course->blockinfo = blocks_get_block_ids($course->blockinfo);
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

    //This function creates all the course_sections and course_modules from xml
    //when restoring in a new course or simply checks sections and create records
    //in backup_ids when restoring in a existing course
    function restore_create_sections($restore,$xml_file) {

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
                            //Check if we've to restore this module
                            if ($restore->mods[$mod->type]->restore) {
                                //Get the module id from modules
                                $module = get_record("modules","name",$mod->type);
                                if ($module) {
                                    $course_module->course = $restore->course_id;
                                    $course_module->module = $module->id;
                                    $course_module->section = $newid;
                                    $course_module->added = $mod->added;
                                    $course_module->deleted = $mod->deleted;
                                    $course_module->score = $mod->score;
                                    $course_module->indent = $mod->indent;
                                    $course_module->visible = $mod->visible;
                                    $course_module->groupmode = $mod->groupmode;
                                    $course_module->instance = null;
                                    //NOTE: The instance (new) is calculated and updated in db in the
                                    //      final step of the restore. We don't know it yet.
                                    //print_object($course_module);					//Debug
                                    //Save it to db
                                    $newidmod = insert_record("course_modules",$course_module); 
                                    if ($newidmod) {
                                        //save old and new module id
                                        //In the info field, we save the original instance of the module
                                        //to use it later
                                        backup_putid ($restore->backup_unique_code,"course_modules",
                                                                $keym,$newidmod,$mod->instance);
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
            //For each user, take its info from backup_ids
            foreach ($info->users as $userid) {
                $rec = backup_getid($restore->backup_unique_code,"user",$userid); 
                $user = $rec->info;

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

                //To store new ids created
                $newid=null;
                //check if it exists (by username) and get its id
                $user_exists = true;
                $user_data = get_record("user","username",addslashes($user->username));
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
                        if (!record_exists("user_admins","userid",$newid)) {
                            //Only put status in backup_ids
                            $currinfo = $currinfo."admin,";
                            $status = backup_putid($restore->backup_unique_code,"user",$userid,$newid,$currinfo);
                        }
                    } 
                    if ($is_coursecreator) {
                        //If the record (user_coursecreators) doesn't exists
                        if (!record_exists("user_coursecreators","userid",$newid)) {
                            //Only put status in backup_ids
                            $currinfo = $currinfo."coursecreator,";
                            $status = backup_putid($restore->backup_unique_code,"user",$userid,$newid,$currinfo);
                        }
                    } 
                    if ($is_needed) {
                        //Only put status in backup_ids
                        $currinfo = $currinfo."needed,";
                        $status = backup_putid($restore->backup_unique_code,"user",$userid,$newid,$currinfo);
                    }
                    if ($is_teacher) {
                        //If the record (teacher) doesn't exists
                        if (!record_exists("user_teachers","userid",$newid,"course", $restore->course_id)) {
                            //Put status in backup_ids 
                            $currinfo = $currinfo."teacher,";
                            $status = backup_putid($restore->backup_unique_code,"user",$userid,$newid,$currinfo);
                            //Set course and user
                            $user->roles['teacher']->course = $restore->course_id;
                            $user->roles['teacher']->userid = $newid;
                            //Insert data in user_teachers
                            //The structure is exactly as we need
                            $status = insert_record("user_teachers",$user->roles['teacher']);
                        }
                    } 
                    if ($is_student) {
                        //If the record (student) doesn't exists
                        if (!record_exists("user_students","userid",$newid,"course", $restore->course_id)) {
                            //Put status in backup_ids
                            $currinfo = $currinfo."student,";
                            $status = backup_putid($restore->backup_unique_code,"user",$userid,$newid,$currinfo);
                            //Set course and user
                            $user->roles['student']->course = $restore->course_id;
                            $user->roles['student']->userid = $newid;
                            //Insert data in user_students
                            //The structure is exactly as we need
                            $status = insert_record("user_students",$user->roles['student']);
                        }
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
                    //echo "Checking for preferences of user ".$user->username."<br>";         //Debug
                    //Get user new id from backup_ids
                    $data = backup_getid($restore->backup_unique_code,"user",$userid);
                    $newid = $data->new_id;
                    if (isset($user->user_preferences)) {
                        //echo "Preferences exist in backup file<br>";                         //Debug
                        foreach($user->user_preferences as $user_preference) {
                            //echo $user_preference->name." = ".$user_preference->value."<br>";    //Debug
                            //We check if that user_preference exists in DB
                            if (!record_exists("user_preferences","userid",$newid,"name",$user_preference->name)) {
                                //echo "Creating it<br>";                                              //Debug
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

    //This function creates all the categories and questions
    //from xml (STEP1 of quiz restore)
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
                    $catrestore = "quiz_restore_question_categories";
                    if (function_exists($catrestore)) {
                        //print_object ($category);                                                //Debug
                        $status = $catrestore($category,$restore);
                    } else {
                        //Something was wrong. Function should exist.
                        $status = false;
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
                                $sca->userid = 1;
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
                    $data = backup_getid($restore->backup_unique_code,"group",$group->id);
                    //Init variables
                    $create_group = false;

                    if ($data) {
                        //Now get completed xmlized object
                        $info = $data->info;
                        //traverse_xmlize($info);                                                                     //Debug
                        //print_object ($GLOBALS['traverse_array']);                                                  //Debug
                        //$GLOBALS['traverse_array']="";                                                              //Debug
                        //Now build the GROUP record structure
                        $gro->courseid = backup_todb($info['GROUP']['#']['COURSEID']['0']['#']);
                        $gro->name = backup_todb($info['GROUP']['#']['NAME']['0']['#']);
                        $gro->description = backup_todb($info['GROUP']['#']['DESCRIPTION']['0']['#']);
                        $gro->lang = backup_todb($info['GROUP']['#']['LANG']['0']['#']);
                        $gro->picture = backup_todb($info['GROUP']['#']['PICTURE']['0']['#']);
                        $gro->hidepicture = backup_todb($info['GROUP']['#']['HIDEPICTURE']['0']['#']);
                        $gro->timecreated = backup_todb($info['GROUP']['#']['TIMECREATED']['0']['#']);
                        $gro->timemodified = backup_todb($info['GROUP']['#']['TIMEMODIFIED']['0']['#']);
                
                        //Now search if that group exists (by name and description field) in 
                        //restore->course_id course 
                        $gro_db = get_record("groups","courseid",$restore->course_id,"name",$gro->name,"description",$gro->description);
                        //If it doesn't exist, create
                        if (!$gro_db) {
                            $create_group = true;
                        }
                        //If we must create the group
                        if ($create_group) {
                            //Me must recode the courseid to the restore->course_id 
                            $gro->courseid = $restore->course_id;
                            //The structure is equal to the db, so insert the group
                            $newid = insert_record ("groups",$gro);
                        } else { 
                            //get current group id
                            $newid = $gro_db->id;
                        }
                        if ($newid) {
                            //We have the newid, update backup_ids
                            backup_putid($restore->backup_unique_code,"group",
                                         $group->id, $newid);
                        }
                        //Now restore members in the groups_members
                        $status2 = restore_create_groups_members($newid,$info,$restore);
                    }   
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

        //Get the members array
        $members = $info['GROUP']['#']['MEMBERS']['0']['#']['MEMBER'];

        //Iterate over members
        for($i = 0; $i < sizeof($members); $i++) {
            $mem_info = $members[$i];
            //traverse_xmlize($mem_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the GROUPS_MEMBERS record structure
            $group_member->groupid = $group_id;
            $group_member->userid = backup_todb($mem_info['#']['USERID']['0']['#']);
            $group_member->timeadded = backup_todb($mem_info['#']['TIMEADDED']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$group_member->userid);
            if ($user) {
                $group_member->userid = $user->new_id;
            }

            //The structure is equal to the db, so insert the groups_members
            $newid = insert_record ("groups_members",$group_member);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br>";
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

                        //Now build the EVENT record structure
                        $eve->name = backup_todb($info['EVENT']['#']['NAME']['0']['#']);
                        $eve->description = backup_todb($info['EVENT']['#']['DESCRIPTION']['0']['#']);
                        $eve->format = backup_todb($info['EVENT']['#']['FORMAT']['0']['#']);
                        $eve->courseid = $restore->course_id;
                        $eve->groupid = backup_todb($info['EVENT']['#']['GROUPID']['0']['#']);
                        $eve->userid = backup_todb($info['EVENT']['#']['USERID']['0']['#']);
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
                                $eve->userid = 1;
                            }
                            //We have to recode the groupid field
                            $group = backup_getid($restore->backup_unique_code,"group",$eve->groupid);
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

        if ($result != $content && $CFG->debug>7) {                                  //Debug
            echo "<br><hr>".$content."<br>changed to<br>".$result."<hr><br>";        //Debug
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
                                              $dest_dir."/".$data->new_id);
                                $counter ++;
                            }
                            //Do some output
                            if ($counter % 2 == 0) {
                                echo ".";
                                if ($counter % 40 == 0) {
                                echo "<br>";
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
                                      $dest_dir."/".$dir);
                        $counter ++;
                    }
                    //Do some output
                    if ($counter % 2 == 0) {       
                        echo ".";
                        if ($counter % 40 == 0) {       
                        echo "<br>";
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
                //Iterate over each module
                foreach ($info as $mod) {
                    $modrestore = $mod->modtype."_restore_mods";
                    if (function_exists($modrestore)) {
                        //print_object ($mod);                                                //Debug
                        $status = $modrestore($mod,$restore);
                    } else {
                        //Something was wrong. Function should exist.
                        $status = false;
                    }
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
                                //echo "User ".$dblog->userid." to user ".$user->new_id."<br>";                             //Debug
                                $dblog->userid = $user->new_id;
                            }
                            //We have to recode the cmid field (if module isn't "course" or "user")
                            if ($dblog->module != "course" and $dblog->module != "user") {
                                $cm = backup_getid($restore->backup_unique_code,"course_modules",$dblog->cmid);
                                if ($cm) {
                                    //echo "Module ".$dblog->cmid." to module ".$cm->new_id."<br>";                         //Debug
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
                            echo ".";
                            if ($counter % 200 == 0) {
                                echo "<br>";
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

        //echo "<hr>Before transformations<br>";                                        //Debug
        //print_object($log);                                                           //Debug
        //Depending of the action, we recode different things
        switch ($log->action) {
        case "view":
            $log->url = "view.php?id=".$log->course;
            $log->info = $log->course;
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
        default:
            echo "action (".$log->module."-".$log->action.") unknow. Not restored<br>";                 //Debug
            break;
        }

        //echo "After transformations<br>";                                             //Debug
        //print_object($log);                                                           //Debug

        //Now if $toinsert is set, insert the record
        if ($toinsert) {
            //echo "Inserting record<br>";                                              //Debug
            $status = insert_record("log",$log);
        }
        return $status;
    }

    //This function inserts a user log record, calculating the URL field as necessary
    function restore_log_user($restore,$log) {

        $status = true;
        $toinsert = false;
        
        //echo "<hr>Before transformations<br>";                                        //Debug
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
            echo "action (".$log->module."-".$log->action.") unknow. Not restored<br>";                 //Debug
            break;
        }

        //echo "After transformations<br>";                                             //Debug
        //print_object($log);                                                           //Debug

        //Now if $toinsert is set, insert the record
        if ($toinsert) {
            //echo "Inserting record<br>";                                              //Debug
            $status = insert_record("log",$log);
        }
        return $status;
    }

    //This function inserts a module log record, calculating the URL field as necessary
    function restore_log_module($restore,$log) {

        $status = true;
        $toinsert = false;

        //echo "<hr>Before transformations<br>";                                        //Debug
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

        //echo "After transformations<br>";                                             //Debug
        //print_object($log);                                                           //Debug

        //Now if $toinsert is set, insert the record
        if ($toinsert) {
            //echo "Inserting record<br>";                                              //Debug
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
                            //print_object ($module); 							//Debug
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
        //They are trimed and converted from utf8
        function getContents() {
            return trim(utf8_decode($this->content));
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
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br>\n";   //Debug
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
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br>\n";   //Debug
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
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br>\n";   //Debug
        }
        
        //This is the startTag handler we use where we are reading the user zone (todo="USERS")
        function startElementUsers($parser, $tagName, $attrs) {
            //Refresh properties     
            $this->level++;
            $this->tree[$this->level] = $tagName;   

            //Check if we are into USERS zone  
            //if ($this->tree[3] == "USERS")                                                            //Debug
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br>\n";   //Debug
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
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br>\n";   //Debug

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
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br>\n";   //Debug

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
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br>\n";   //Debug

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
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br>\n";   //Debug

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
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br>\n";   //Debug

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
            //    echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br>\n";   //Debug

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

            echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;".$tagName."&gt;<br>\n";   //Debug
        }
 
        //This is the endTag handler we use where we are reading the info zone (todo="INFO")
        function endElementInfo($parser, $tagName) {
            //Check if we are into INFO zone
            if ($this->tree[2] == "INFO") {
                //if (trim($this->content))                                                                     //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br>\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br>\n";          //Debug
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
                    }
                }
                if ($this->tree[3] == "DETAILS") {
                    if ($this->level == 4) {
                        switch ($tagName) {
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
                }
            }


            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";

            //Stop parsing if todo = INFO and tagName = INFO (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($tagName == "INFO") {
                $this->finished = true;
            }
        }

        //This is the endTag handler we use where we are reading the course_header zone (todo="COURSE_HEADER")
        function endElementCourseHeader($parser, $tagName) {
            //Check if we are into COURSE_HEADER zone
            if ($this->tree[3] == "HEADER") {
                //if (trim($this->content))                                                                     //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br>\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br>\n";          //Debug
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
                        case "LANG":
                            $this->info->course_lang = $this->getContents();
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

            }
            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";

            //Stop parsing if todo = COURSE_HEADER and tagName = HEADER (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($tagName == "HEADER") {
                $this->finished = true;
            }
        }

        //This is the endTag handler we use where we are reading the sections zone (todo="SECTIONS")
        function endElementSections($parser, $tagName) {
            //Check if we are into SECTIONS zone
            if ($this->tree[3] == "SECTIONS") {
                //if (trim($this->content))                                                                     //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br>\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br>\n";          //Debug
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
                            $this->info->tempsection->mods[$this->info->tempmod->id]->deleted = 
                                $this->info->tempmod->deleted;
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
                        case "DELETED":
                            $this->info->tempmod->deleted = $this->getContents();
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
                    }
                }
            }
            //Clear things
            $this->tree[$this->level] = "";
            $this->level--;
            $this->content = "";

            //Stop parsing if todo = SECTIONS and tagName = SECTIONS (en of the tag, of course)
            //Speed up a lot (avoid parse all)
            if ($tagName == "SECTIONS") {
                $this->finished = true;
            }
        }
        
        //This is the endTag handler we use where we are reading the users zone (todo="USERS")
        function endElementUsers($parser, $tagName) {
            //Check if we are into USERS zone
            if ($this->tree[3] == "USERS") {
                //if (trim($this->content))                                                                     //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br>\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br>\n";          //Debug
                //Dependig of different combinations, do different things
                if ($this->level == 4) {
                    switch ($tagName) {
                        case "USER":
                            //Increment counter
                            $this->counter++;
                            //Save to db
                            backup_putid($this->preferences->backup_unique_code,"user",$this->info->tempuser->id,
                                          null,$this->info->tempuser);

                            //Do some output   
                            if ($this->counter % 10 == 0) {
                                echo ".";
                                if ($this->counter % 200 == 0) {
                                echo "<br>";
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
                        case "CONFIRMED": 
                            $this->info->tempuser->confirmed = $this->getContents();
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
                        case "MAILDISPLAY": 
                            $this->info->tempuser->maildisplay = $this->getContents();
                            break;
                        case "HTMLEDITOR": 
                            $this->info->tempuser->htmleditor = $this->getContents();
                            break;
                        case "AUTOSUBSCRIBE": 
                            $this->info->tempuser->autosubscribe = $this->getContents();
                            break;
                        case "TIMEMODIFIED": 
                            $this->info->tempuser->timemodified = $this->getContents();
                            break;
                    }
                }
                if ($this->level == 6) {
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
                if ($this->level == 7) {
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
                        case "NAME":
                            $this->info->tempuserpreference->name = $this->getContents();
                            break;
                        case "VALUE":
                            $this->info->tempuserpreference->value = $this->getContents();
                            break;
                    }
                }
            }

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

        //This is the endTag handler we use where we are reading the questions zone (todo="QUESTIONS")  
        function endElementQuestions($parser, $tagName) {
            //Check if we are into QUESTION_CATEGORIES zone
            if ($this->tree[3] == "QUESTION_CATEGORIES") {
                //if (trim($this->content))                                                                     //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br>\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br>\n";          //Debug
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
                    $status = backup_putid($this->preferences->backup_unique_code,"quiz_categories",$category_id,
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
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br>\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br>\n";          //Debug
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
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br>\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br>\n";          //Debug
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
                    $status = backup_putid($this->preferences->backup_unique_code,"group",$group_id,
                                     null,$data);
                    //Create returning info
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

        //This is the endTag handler we use where we are reading the events zone (todo="EVENTS")
        function endElementEvents($parser, $tagName) {
            //Check if we are into EVENTS zone
            if ($this->tree[3] == "EVENTS") {
                //if (trim($this->content))                                                                     //Debug
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br>\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br>\n";          //Debug
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
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br>\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br>\n";          //Debug
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
                    if  ($this->preferences->mods[$mod_type]->restore) {
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
                //    echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br>\n";           //Debug
                //echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br>\n";          //Debug
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
                echo "C".str_repeat("&nbsp;",($this->level+2)*2).$this->getContents()."<br>\n";           //Debug
            echo $this->level.str_repeat("&nbsp;",$this->level*2)."&lt;/".$tagName."&gt;<br>\n";          //Debug

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

        $xml_parser = xml_parser_create();
        $moodle_parser = new MoodleParser();
        $moodle_parser->todo = $todo;
        $moodle_parser->preferences = $preferences;
        xml_set_object($xml_parser,$moodle_parser);
        //Depending of the todo we use some element_handler or another
        if ($todo == "INFO") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementInfo", "endElementInfo");
        } else if ($todo == "COURSE_HEADER") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementCourseHeader", "endElementCourseHeader");
        } else if ($todo == "SECTIONS") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementSections", "endElementSections");
        } else if ($todo == "USERS") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementUsers", "endElementUsers");
        } else if ($todo == "QUESTIONS") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementQuestions", "endElementQuestions");
        } else if ($todo == "SCALES") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementScales", "endElementScales");
        } else if ($todo == "GROUPS") {
            //Define handlers to that zone
            xml_set_element_handler($xml_parser, "startElementGroups", "endElementGroups");
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

        if ($status && $info) {
            return $info;
        } else {
            return $status;
        }
    }
?>
