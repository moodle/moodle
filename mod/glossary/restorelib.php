<?php //$Id$
    //This php script contains all the stuff to backup/restore
    //glossary mods

    //This is the "graphical" structure of the glossary mod:
    //
    //                     glossary ----------------------------------------- glossary_categories
    //                    (CL,pk->id)                                     (CL,pk->id,fk->glossaryid)
    //                        |                                                       |
    //                        |                                                       |
    //                        |                                                       |
    //                  glossary_entries --------------------------------glossary_entries_categories
    //         (UL,pk->id, fk->glossaryid, files)         |               (UL, pk->categoryid,entryid)
    //                        |                           |
    //                        |                           |--------------------glossary_ratings
    //                        |                           |               (UL, pk->id, pk->entryid)
    //                  glossary_comments                 |
    //              (UL,pk->id, fk->entryid)              |---------------------glossary_alias
    //                                                                     (UL, pk->id, pk->entryid)
    //
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    //This function executes all the restore procedure about this mod
    function glossary_restore_mods($mod,$restore) {

        global $CFG;

        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

        if ($data) {
            //Now get completed xmlized object
            $info = $data->info;
            // if necessary, write to restorelog and adjust date/time fields
            if ($restore->course_startdateoffset) {
                restore_log_date_changes('Glossary', $restore, $info['MOD']['#'], array('ASSESSTIMESTART', 'ASSESSTIMEFINISH'));
            }
            //traverse_xmlize($info);                                                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the glossary record structure
            $glossary->course = $restore->course_id;
            $glossary->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $glossary->intro = backup_todb($info['MOD']['#']['INTRO']['0']['#']);
            $glossary->allowduplicatedentries = backup_todb($info['MOD']['#']['ALLOWDUPLICATEDENTRIES']['0']['#']);
            $glossary->displayformat = backup_todb($info['MOD']['#']['DISPLAYFORMAT']['0']['#']);
            $glossary->mainglossary = backup_todb($info['MOD']['#']['MAINGLOSSARY']['0']['#']);
            $glossary->showspecial = backup_todb($info['MOD']['#']['SHOWSPECIAL']['0']['#']);
            $glossary->showalphabet = backup_todb($info['MOD']['#']['SHOWALPHABET']['0']['#']);
            $glossary->showall = backup_todb($info['MOD']['#']['SHOWALL']['0']['#']);
            $glossary->allowcomments = backup_todb($info['MOD']['#']['ALLOWCOMMENTS']['0']['#']);
            $glossary->allowprintview = backup_todb($info['MOD']['#']['ALLOWPRINTVIEW']['0']['#']);
            $glossary->usedynalink = backup_todb($info['MOD']['#']['USEDYNALINK']['0']['#']);
            $glossary->defaultapproval = backup_todb($info['MOD']['#']['DEFAULTAPPROVAL']['0']['#']);
            $glossary->globalglossary = backup_todb($info['MOD']['#']['GLOBALGLOSSARY']['0']['#']);
            $glossary->entbypage = backup_todb($info['MOD']['#']['ENTBYPAGE']['0']['#']);
            $glossary->editalways = backup_todb($info['MOD']['#']['EDITALWAYS']['0']['#']);
            $glossary->rsstype = backup_todb($info['MOD']['#']['RSSTYPE']['0']['#']);
            $glossary->rssarticles = backup_todb($info['MOD']['#']['RSSARTICLES']['0']['#']);
            $glossary->timecreated = backup_todb($info['MOD']['#']['TIMECREATED']['0']['#']);
            $glossary->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);

            $glossary->assessed = backup_todb($info['MOD']['#']['ASSESSED']['0']['#']);
            $glossary->assesstimestart = backup_todb($info['MOD']['#']['ASSESSTIMESTART']['0']['#']);
            $glossary->assesstimefinish = backup_todb($info['MOD']['#']['ASSESSTIMEFINISH']['0']['#']);
            $glossary->scale = backup_todb($info['MOD']['#']['SCALE']['0']['#']);

            //We have to recode the scale field if it's <0 (positive is a grade, not a scale)
            if ($glossary->scale < 0) {
                $scale = backup_getid($restore->backup_unique_code,"scale",abs($glossary->scale));
                if ($scale) {
                    $glossary->scale = -($scale->new_id);
                }
            }

            //To mantain upwards compatibility (pre 1.4) we have to check the displayformat field
            //If it's numeric (0-6) we have to convert it to its new formatname.
            //Define current 0-6 format names
            $formatnames = array('dictionary','continuous','fullwithauthor','encyclopedia',
                                 'faq','fullwithoutauthor','entrylist');
            //If it's numeric, we are restoring a pre 1.4 course, do the conversion
            if (is_numeric($glossary->displayformat)) {
                $displayformat = 'dictionary';  //Default format
                if ($glossary->displayformat >= 0 && $glossary->displayformat <= 6) {
                  $displayformat = $formatnames[$glossary->displayformat];
                }
                $glossary->displayformat = $displayformat;
            }

            //Now check that the displayformat exists in the server, else default to dictionary
            $formats = get_list_of_plugins('mod/glossary/formats');
            if (!in_array($glossary->displayformat,$formats)) {
                $glossary->displayformat = 'dictionary';
            }

            //If the backup file doesn't include the editalways field, activate it
            //in secondary glossaries (old behaviour, pre 1.4)
            if (! isset($info['MOD']['#']['EDITALWAYS']['0']['#'])) { //It's a pre-14 backup file
                if ($glossary->mainglossary == '0') {
                    $glossary->editalways = '1';
                }
            }

            //The structure is equal to the db, so insert the glossary
            $newid = insert_record ("glossary",$glossary);

            //Do some output
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("modulename","glossary")." \"".format_string(stripslashes($glossary->name),true)."\"</li>";
            }
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //Restore glossary_entries
                $status = glossary_entries_restore_mods($mod->id,$newid,$info,$restore);
                //Restore glossary_categories and glossary_category_entries
                $status = glossary_categories_restore_mods($mod->id,$newid,$info,$restore);
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }

        return $status;
    }

    //This function restores the glossary_entries
    function glossary_entries_restore_mods($old_glossary_id,$new_glossary_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the entries array
        $entries = isset($info['MOD']['#']['ENTRIES']['0']['#']['ENTRY'])?$info['MOD']['#']['ENTRIES']['0']['#']['ENTRY']:array();

        //Iterate over entries
        for($i = 0; $i < sizeof($entries); $i++) {
            $ent_info = $entries[$i];
            //traverse_xmlize($ent_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($ent_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($ent_info['#']['USERID']['0']['#']);

            //Now, build the GLOSSARY_ENTRIES record structure
            $entry->glossaryid = $new_glossary_id;
            $entry->userid = backup_todb($ent_info['#']['USERID']['0']['#']);
            $entry->concept = backup_todb(trim($ent_info['#']['CONCEPT']['0']['#']));
            $entry->definition = backup_todb($ent_info['#']['DEFINITION']['0']['#']);
            $entry->format = backup_todb($ent_info['#']['FORMAT']['0']['#']);
            $entry->attachment = backup_todb($ent_info['#']['ATTACHMENT']['0']['#']);
            $entry->sourceglossaryid = backup_todb($ent_info['#']['SOURCEGLOSSARYID']['0']['#']);
            $entry->usedynalink = backup_todb($ent_info['#']['USEDYNALINK']['0']['#']);
            $entry->casesensitive = backup_todb($ent_info['#']['CASESENSITIVE']['0']['#']);
            $entry->fullmatch = backup_todb($ent_info['#']['FULLMATCH']['0']['#']);
            $entry->approved = backup_todb($ent_info['#']['APPROVED']['0']['#']);
            $entry->timecreated = backup_todb($ent_info['#']['TIMECREATED']['0']['#']);
            $entry->timemodified = backup_todb($ent_info['#']['TIMEMODIFIED']['0']['#']);
            $entry->teacherentry = backup_todb($ent_info['#']['TEACHERENTRY']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$entry->userid);
            if ($user) {
                $entry->userid = $user->new_id;
            }
            //We have to recode the sourceglossaryid field
            $source = backup_getid($restore->backup_unique_code,"glossary",$entry->sourceglossaryid);
            if ($source) {
                $entry->sourceglossaryid = $source->new_id;
            }
            //If it's a teacher entry or userinfo was selected, restore the entry
            if ($entry->teacherentry or restore_userdata_selected($restore,'glossary',$old_glossary_id)) {
                //The structure is equal to the db, so insert the glossary_entries
                $newid = insert_record ("glossary_entries",$entry);

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
                if ($newid) {
                    //We have the newid, update backup_ids
                    backup_putid($restore->backup_unique_code,"glossary_entries",$oldid,$newid);
                    //Restore glossary_alias
                    $status = glossary_alias_restore_mods($oldid,$newid,$ent_info,$restore);
                    //Now restore glossary_comments
                    $status = glossary_comments_restore_mods($oldid,$newid,$ent_info,$restore);
                    //Now restore glossary_ratings
                    $status = glossary_ratings_restore_mods($oldid,$newid,$ent_info,$restore);
                    //Now copy moddata associated files if needed
                    if ($entry->attachment) {
                        $status = glossary_restore_files ($old_glossary_id, $new_glossary_id,
                                                          $oldid, $newid, $restore);
                    }
                } else {
                    $status = false;
                }
            }
        }

        return $status;
    }

    //This function restores the glossary_comments
    function glossary_comments_restore_mods($old_entry_id,$new_entry_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the comments array
        $comments = isset($info['#']['COMMENTS']['0']['#']['COMMENT'])?$info['#']['COMMENTS']['0']['#']['COMMENT']:array();

        //Iterate over comments
        for($i = 0; $i < sizeof($comments); $i++) {
            $com_info = $comments[$i];
            //traverse_xmlize($com_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($com_info['#']['ID']['0']['#']);

            //Now, build the GLOSSARY_COMMENTS record structure
            $comment->entryid = $new_entry_id;
            $comment->userid = backup_todb($com_info['#']['USERID']['0']['#']);
            if (isset($com_info['#']['COMMENT']['0']['#'])) {
                $comment->entrycomment = backup_todb($com_info['#']['COMMENT']['0']['#']);
            } else {
                $comment->entrycomment = backup_todb($com_info['#']['ENTRYCOMMENT']['0']['#']);
            }
            $comment->timemodified = backup_todb($com_info['#']['TIMEMODIFIED']['0']['#']);
            $comment->format = backup_todb($com_info['#']['FORMAT']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$comment->userid);
            if ($user) {
                $comment->userid = $user->new_id;
            }

            //The structure is equal to the db, so insert the glossary_comments
            $newid = insert_record ("glossary_comments",$comment);

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
            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"glossary_comments",$oldid,$newid);
            } else {
                $status = false;
            }
        }

        return $status;
    }

    //This function restores the glossary_ratings
    function glossary_ratings_restore_mods($old_entry_id,$new_entry_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the ratings array
        $ratings = isset($info['#']['RATINGS']['0']['#']['RATING'])?$info['#']['RATINGS']['0']['#']['RATING']:array();

        //Iterate over ratings
        for($i = 0; $i < sizeof($ratings); $i++) {
            $rat_info = $ratings[$i];
            //traverse_xmlize($rat_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the GLOSSARY_RATINGS record structure
            $rating->entryid = $new_entry_id;
            $rating->userid = backup_todb($rat_info['#']['USERID']['0']['#']);
            $rating->time = backup_todb($rat_info['#']['TIME']['0']['#']);
            $rating->rating = backup_todb($rat_info['#']['RATING']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$rating->userid);
            if ($user) {
                $rating->userid = $user->new_id;
            }

            //The structure is equal to the db, so insert the glossary_ratings
            $newid = insert_record ("glossary_ratings",$rating);

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

    //This function restores the glossary_alias table
    function glossary_alias_restore_mods($old_entry_id,$new_entry_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the comments array
        $aliases = isset($info['#']['ALIASES']['0']['#']['ALIAS'])?$info['#']['ALIASES']['0']['#']['ALIAS']:array();

        //Iterate over comments
        for($i = 0; $i < sizeof($aliases); $i++) {
            $alias_info = $aliases[$i];

            //Now, build the GLOSSARY_ALIAS record structure
            $alias->entryid = $new_entry_id;
            $alias->alias = backup_todb(trim($alias_info['#']['ALIAS_TEXT']['0']['#']));

            //The structure is equal to the db, so insert the glossary_comments
            $newid = insert_record ("glossary_alias",$alias);

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

    //This function restores the glossary_categories
    function glossary_categories_restore_mods($old_glossary_id,$new_glossary_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the categories array
        $categories = isset($info['MOD']['#']['CATEGORIES']['0']['#']['CATEGORY'])?$info['MOD']['#']['CATEGORIES']['0']['#']['CATEGORY']:array();

        //Iterate over categories
        for($i = 0; $i < sizeof($categories); $i++) {
            $cat_info = $categories[$i];
            //traverse_xmlize($cat_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($cat_info['#']['ID']['0']['#']);

            //Now, build the GLOSSARY_CATEGORIES record structure
            $category->glossaryid = $new_glossary_id;
            $category->name = backup_todb($cat_info['#']['NAME']['0']['#']);
            $category->usedynalink = backup_todb($cat_info['#']['USEDYNALINK']['0']['#']);

            $newid = insert_record ("glossary_categories",$category);

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

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"glossary_categories",$oldid,$newid);
                //Now restore glossary_entries_categories
                $status = glossary_entries_categories_restore_mods($oldid,$newid,$cat_info,$restore);
            } else {
                $status = false;
            }
        }

        return $status;
    }


    //This function restores the glossary_entries_categories
    function glossary_entries_categories_restore_mods($old_category_id,$new_category_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the entryids array
        $entryids = isset($info['#']['ENTRIES']['0']['#']['ENTRY'])?$info['#']['ENTRIES']['0']['#']['ENTRY']:array();

        //Iterate over entryids
        for($i = 0; $i < sizeof($entryids); $i++) {
            $ent_info = $entryids[$i];
            //traverse_xmlize($ent_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the GLOSSARY_ENTRIES_CATEGORIES record structure
            $entry_category->categoryid = $new_category_id;
            $entry_category->entryid = backup_todb($ent_info['#']['ENTRYID']['0']['#']);

            //We have to recode the entryid field
            $entry = backup_getid($restore->backup_unique_code,"glossary_entries",$entry_category->entryid);
            if ($entry) {
                $entry_category->entryid = $entry->new_id;
             }

            $newid = insert_record ("glossary_entries_categories",$entry_category);

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

    //This function copies the glossary related info from backup temp dir to course moddata folder,
    //creating it if needed and recoding everything (glossary id and entry id)
    function glossary_restore_files ($oldgloid, $newgloid, $oldentryid, $newentryid, $restore) {

        global $CFG;

        $status = true;
        $todo = false;
        $moddata_path = "";
        $glossary_path = "";
        $temp_path = "";

        //First, we check to "course_id" exists and create is as necessary
        //in CFG->dataroot
        $dest_dir = $CFG->dataroot."/".$restore->course_id;
        $status = check_dir_exists($dest_dir,true);

        //Now, locate course's moddata directory
        $moddata_path = $CFG->dataroot."/".$restore->course_id."/".$CFG->moddata;

        //Check it exists and create it
        $status = check_dir_exists($moddata_path,true);

        //Now, locate glossary directory
        if ($status) {
            $glossary_path = $moddata_path."/glossary";
            //Check it exists and create it
            $status = check_dir_exists($glossary_path,true);
        }

        //Now locate the temp dir we are restoring from
        if ($status) {
            $temp_path = $CFG->dataroot."/temp/backup/".$restore->backup_unique_code.
                         "/moddata/glossary/".$oldgloid."/".$oldentryid;
            //Check it exists
            if (is_dir($temp_path)) {
                $todo = true;
            }
        }

        //If todo, we create the neccesary dirs in course moddata/glossary
        if ($status and $todo) {
            //First this glossary id
            $this_glossary_path = $glossary_path."/".$newgloid;
            $status = check_dir_exists($this_glossary_path,true);
            //Now this entry id
            $entry_glossary_path = $this_glossary_path."/".$newentryid;
            //And now, copy temp_path to entry_glossary_path
            $status = backup_copy_file($temp_path, $entry_glossary_path);
        }

        return $status;
    }

    //Return a content decoded to support interactivities linking. Every module
    //should have its own. They are called automatically from
    //glossary_decode_content_links_caller() function in each module
    //in the restore process
    function glossary_decode_content_links ($content,$restore) {
            
        global $CFG;
            
        $result = $content;
                
        //Link to the list of glossarys
                
        $searchstring='/\$@(GLOSSARYINDEX)\*([0-9]+)@\$/';
        //We look for it
        preg_match_all($searchstring,$content,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //print_object($foundset);                                     //Debug
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (course id)
                $rec = backup_getid($restore->backup_unique_code,"course",$old_id);
                //Personalize the searchstring
                $searchstring='/\$@(GLOSSARYINDEX)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/glossary/index.php?id='.$rec->new_id,$result);
                } else { 
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/glossary/index.php?id='.$old_id,$result);
                }
            }
        }

        //Link to glossary view by moduleid

        $searchstring='/\$@(GLOSSARYVIEWBYID)\*([0-9]+)@\$/';
        //We look for it
        preg_match_all($searchstring,$result,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //print_object($foundset);                                     //Debug
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (course_modules id)
                $rec = backup_getid($restore->backup_unique_code,"course_modules",$old_id);
                //Personalize the searchstring
                $searchstring='/\$@(GLOSSARYVIEWBYID)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/glossary/view.php?id='.$rec->new_id,$result);
                } else {
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/glossary/view.php?id='.$old_id,$result);
                }
            }
        }

        return $result;
    }

    //This function makes all the necessary calls to xxxx_decode_content_links()
    //function in each module, passing them the desired contents to be decoded
    //from backup format to destination site/course in order to mantain inter-activities
    //working in the backup/restore process. It's called from restore_decode_content_links()
    //function in restore process
    function glossary_decode_content_links_caller($restore) {
        global $CFG;
        $status = true;
        
        //Process every glossary ENTRY in the course
        if ($entries = get_records_sql ("SELECT e.id, e.definition
                                   FROM {$CFG->prefix}glossary_entries e,
                                        {$CFG->prefix}glossary g
                                   WHERE g.course = $restore->course_id AND
                                         e.glossaryid = g.id")) {
            //Iterate over each post->message
            $i = 0;   //Counter to send some output to the browser to avoid timeouts
            foreach ($entries as $entry) {
                //Increment counter
                $i++;
                $content = $entry->definition;
                $result = restore_decode_content_links_worker($content,$restore);
                if ($result != $content) {
                    //Update record
                    $entry->definition = addslashes($result);
                    $status = update_record("glossary_entries",$entry);
                    if (debugging()) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<br /><hr />'.s($content).'<br />changed to<br />'.s($result).'<hr /><br />';
                        }
                    }
                }
                //Do some output
                if (($i+1) % 5 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 100 == 0) {
                            echo "<br />";
                        }
                    }
                    backup_flush(300);
                }
            }
        }

        //Process every glossary (intro) in the course
        if ($glossarys = get_records_sql ("SELECT g.id, g.intro
                                   FROM {$CFG->prefix}glossary g
                                   WHERE g.course = $restore->course_id")) {
            //Iterate over each glossary->intro
            $i = 0;   //Counter to send some output to the browser to avoid timeouts
            foreach ($glossarys as $glossary) {
                //Increment counter
                $i++;
                $content = $glossary->intro;
                $result = restore_decode_content_links_worker($content,$restore);
                if ($result != $content) {
                    //Update record
                    $glossary->intro = addslashes($result);
                    $status = update_record("glossary",$glossary);
                    if (debugging()) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<br /><hr />'.s($content).'<br />changed to<br />'.s($result).'<hr /><br />';
                        }
                    }
                }
                //Do some output
                if (($i+1) % 5 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 100 == 0) {
                            echo "<br />";
                        }
                    }
                    backup_flush(300);
                }
            }
        }

        return $status;
    }

    //This function converts texts in FORMAT_WIKI to FORMAT_MARKDOWN for
    //some texts in the module
    function glossary_restore_wiki2markdown ($restore) {

        global $CFG;

        $status = true;

        //Convert glossary_comments->entrycomment
        if ($records = get_records_sql ("SELECT c.id, c.entrycomment, c.format
                                         FROM {$CFG->prefix}glossary_comments c,
                                              {$CFG->prefix}glossary_entries e,
                                              {$CFG->prefix}glossary g,
                                              {$CFG->prefix}backup_ids b
                                         WHERE e.id = c.entryid AND
                                               g.id = e.glossaryid AND
                                               g.course = $restore->course_id AND
                                               c.format = ".FORMAT_WIKI. " AND
                                               b.backup_code = $restore->backup_unique_code AND
                                               b.table_name = 'glossary_comments' AND
                                               b.new_id = c.id")) {
            foreach ($records as $record) {
                //Rebuild wiki links
                $record->entrycomment = restore_decode_wiki_content($record->entrycomment, $restore);
                //Convert to Markdown
                $wtm = new WikiToMarkdown();
                $record->entrycomment = $wtm->convert($record->entrycomment, $restore->course_id);
                $record->format = FORMAT_MARKDOWN;
                $status = update_record('glossary_comments', addslashes_object($record));
                //Do some output
                $i++;
                if (($i+1) % 1 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 20 == 0) {
                            echo "<br />";
                        }
                    }
                    backup_flush(300);
                }
            }

        }

        //Convert glossary_entries->definition
        if ($records = get_records_sql ("SELECT e.id, e.definition, e.format
                                         FROM {$CFG->prefix}glossary_entries e,
                                              {$CFG->prefix}glossary g,
                                              {$CFG->prefix}backup_ids b
                                         WHERE g.id = e.glossaryid AND
                                               g.course = $restore->course_id AND
                                               e.format = ".FORMAT_WIKI. " AND
                                               b.backup_code = $restore->backup_unique_code AND
                                               b.table_name = 'glossary_entries' AND
                                               b.new_id = e.id")) {
            foreach ($records as $record) {
                //Rebuild wiki links
                $record->definition = restore_decode_wiki_content($record->definition, $restore);
                //Convert to Markdown
                $wtm = new WikiToMarkdown();
                $record->definition = $wtm->convert($record->definition, $restore->course_id);
                $record->format = FORMAT_MARKDOWN;
                $status = update_record('glossary_entries', addslashes_object($record));
                //Do some output
                $i++;
                if (($i+1) % 1 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 20 == 0) {
                            echo "<br />";
                        }
                    }
                    backup_flush(300);
                }
            }

        }
        
        return $status;
    }

    //This function returns a log record with all the necessay transformations
    //done. It's used by restore_log_module() to restore modules log.
    function glossary_restore_logs($restore,$log) {

        $status = false;

        //Depending of the action, we recode different things
        switch ($log->action) {
        case "add":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "update":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "view":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "view all":
            $log->url = "index.php?id=".$log->course;
            $status = true;
            break;
        case "add category":
            if ($log->cmid) {
                //Get the new_id of the glossary_category (to recode the info field)
                $cat = backup_getid($restore->backup_unique_code,"glossary_categories",$log->info);
                if ($cat) {
                    $log->url = "editcategories.php?id=".$log->cmid;
                    $log->info = $cat->new_id;
                    $status = true;
                }
            }
            break;
        case "edit category":
            if ($log->cmid) {
                //Get the new_id of the glossary_category (to recode the info field)
                $cat = backup_getid($restore->backup_unique_code,"glossary_categories",$log->info);
                if ($cat) {
                    $log->url = "editcategories.php?id=".$log->cmid;
                    $log->info = $cat->new_id;
                    $status = true;
                }
            }
            break;
        case "delete category":
            if ($log->cmid) {
                //Get the new_id of the glossary_category (to recode the info field)
                $cat = backup_getid($restore->backup_unique_code,"glossary_categories",$log->info);
                if ($cat) {
                    $log->url = "editcategories.php?id=".$log->cmid;
                    $log->info = $cat->new_id;
                    $status = true;
                }
            }
            break;
        case "add entry":
            if ($log->cmid) {
                //Get the new_id of the glossary_entry (to recode the info and url field)
                $ent = backup_getid($restore->backup_unique_code,"glossary_entries",$log->info);
                if ($ent) {
                    $log->url = "view.php?id=".$log->cmid."&mode=entry&hook=".$ent->new_id;
                    $log->info = $ent->new_id;
                    $status = true;
                }
            }
            break;
        case "update entry":
            if ($log->cmid) {
                //Get the new_id of the glossary_entry (to recode the info and url field)
                $ent = backup_getid($restore->backup_unique_code,"glossary_entries",$log->info);
                if ($ent) {
                    $log->url = "view.php?id=".$log->cmid."&mode=entry&hook=".$ent->new_id;
                    $log->info = $ent->new_id;
                    $status = true;
                }
            }
            break;
        case "delete entry":
            if ($log->cmid) {
                //Get the new_id of the glossary_entry (to recode the info and url field)
                $ent = backup_getid($restore->backup_unique_code,"glossary_entries",$log->info);
                if ($ent) {
                    $log->url = "view.php?id=".$log->cmid."&mode=entry&hook=".$ent->new_id;
                    $log->info = $ent->new_id;
                    $status = true;
                }
            }
            break;
        case "approve entry":
            if ($log->cmid) {
                //Get the new_id of the glossary_entry (to recode the info and url field)
                $ent = backup_getid($restore->backup_unique_code,"glossary_entries",$log->info);
                if ($ent) {
                    $log->url = "showentry.php?id=".$log->cmid."&eid=".$ent->new_id;
                    $log->info = $ent->new_id;
                    $status = true;
                }
            }
            break;
        case "view entry":
            if ($log->cmid) {
                //Get the new_id of the glossary_entry (to recode the info and url field)
                $ent = backup_getid($restore->backup_unique_code,"glossary_entries",$log->info);
                if ($ent) {
                    $log->url = "showentry.php?&eid=".$ent->new_id;
                    $log->info = $ent->new_id;
                    $status = true;
                }
            }
            break;
        case "add comment":
            if ($log->cmid) {
                //Extract the entryid from the url field
                $entid = substr(strrchr($log->url,"="),1);
                //Get the new_id of the glossary_entry (to recode the url field)
                $ent = backup_getid($restore->backup_unique_code,"glossary_entries",$entid);
                //Get the new_id of the glossary_comment (to recode the info field)
                $com = backup_getid($restore->backup_unique_code,"glossary_comments",$log->info);
                if ($ent and $com) {
                    $log->url = "comments.php?id=".$log->cmid."&eid=".$ent->new_id;
                    $log->info = $com->new_id;
                    $status = true;
                }
            }
            break;
        case "update comment":
            if ($log->cmid) {
                //Extract the entryid from the url field
                $entid = substr(strrchr($log->url,"="),1);
                //Get the new_id of the glossary_entry (to recode the url field)
                $ent = backup_getid($restore->backup_unique_code,"glossary_entries",$entid);
                //Get the new_id of the glossary_comment (to recode the info field)
                $com = backup_getid($restore->backup_unique_code,"glossary_comments",$log->info);
                if ($ent and $com) {
                    $log->url = "comments.php?id=".$log->cmid."&eid=".$ent->new_id;
                    $log->info = $com->new_id;
                    $status = true;
                }
            }
            break;
        case "delete comment":
            if ($log->cmid) {
                //Extract the entryid from the url field
                $entid = substr(strrchr($log->url,"="),1);
                //Get the new_id of the glossary_entry (to recode the url field)
                $ent = backup_getid($restore->backup_unique_code,"glossary_entries",$entid);
                //Get the new_id of the glossary_comment (to recode the info field)
                $com = backup_getid($restore->backup_unique_code,"glossary_comments",$log->info);
                if ($ent and $com) {
                    $log->url = "comments.php?id=".$log->cmid."&eid=".$ent->new_id;
                    $log->info = $com->new_id;
                    $status = true;
                }
            }
            break;
        default:
            if (!defined('RESTORE_SILENTLY')) {
                echo "action (".$log->module."-".$log->action.") unknown. Not restored<br />";                 //Debug
            }
            break;
        }

        if ($status) {
            $status = $log;
        }
        return $status;
    }
?>
