<?PHP //$Id$
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
            //traverse_xmlize($info);                                                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the glossary record structure
            $glossary->course = $restore->course_id;
            $glossary->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $glossary->intro = backup_todb($info['MOD']['#']['INTRO']['0']['#']);
            $glossary->studentcanpost = backup_todb($info['MOD']['#']['STUDENTCANPOST']['0']['#']);
            $glossary->allowduplicatedentries = backup_todb($info['MOD']['#']['ALLOWDUPLICATEDENTRIES']['0']['#']);
            $glossary->displayformat = backup_todb($info['MOD']['#']['DISPLAYFORMAT']['0']['#']);
            $glossary->mainglossary = backup_todb($info['MOD']['#']['MAINGLOSSARY']['0']['#']);
            $glossary->showspecial = backup_todb($info['MOD']['#']['SHOWSPECIAL']['0']['#']);
            $glossary->showalphabet = backup_todb($info['MOD']['#']['SHOWALPHABET']['0']['#']);
            $glossary->showall = backup_todb($info['MOD']['#']['SHOWALL']['0']['#']);
            $glossary->allowcomments = backup_todb($info['MOD']['#']['ALLOWCOMMENTS']['0']['#']);
            $glossary->usedynalink = backup_todb($info['MOD']['#']['USEDYNALINK']['0']['#']);
            $glossary->defaultapproval = backup_todb($info['MOD']['#']['DEFAULTAPPROVAL']['0']['#']);
            $glossary->globalglossary = backup_todb($info['MOD']['#']['GLOBALGLOSSARY']['0']['#']);
            $glossary->entbypage = backup_todb($info['MOD']['#']['ENTBYPAGE']['0']['#']);
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

            //We are going to mantain here backwards compatibility with 1.4 glossaries (exception!!)
            //so we have to make some conversions
            //If the displayformat field isn't numeric we are restoring a newer (1.4) glossary
            if (!is_numeric($glossary->displayformat)) {
                //Hardcode the conversions
                if ($glossary->displayformat == 'dictionary') {
                    $glossary->displayformat = '0';
                } else if ($glossary->displayformat == 'continuous') {
                    $glossary->displayformat = '1';
                } else if ($glossary->displayformat == 'fullwithauthor') {
                    $glossary->displayformat = '2';
                } else if ($glossary->displayformat == 'encyclopedia') {
                    $glossary->displayformat = '3';
                } else if ($glossary->displayformat == 'faq') {
                    $glossary->displayformat = '4';
                } else if ($glossary->displayformat == 'fullwithoutauthor') {
                    $glossary->displayformat = '5';
                } else if ($glossary->displayformat == 'entrylist') {
                    $glossary->displayformat = '6';
                } else {
                    $glossary->displayformat = '0';
                }
            }

            //The structure is equal to the db, so insert the glossary
            $newid = insert_record ("glossary",$glossary);

            //Do some output
            echo "<ul><li>".get_string("modulename","glossary")." \"".$glossary->name."\"<br>";
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

            //Finalize ul
            echo "</ul>";

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
        $entries = $info['MOD']['#']['ENTRIES']['0']['#']['ENTRY'];

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
            if ($entry->teacherentry or $restore->mods['glossary']->userinfo) {
                //The structure is equal to the db, so insert the glossary_entries
      	        $newid = insert_record ("glossary_entries",$entry);

            	//Do some output
	        if (($i+1) % 50 == 0) {
      	            echo ".";
            	    if (($i+1) % 1000 == 0) {
                        echo "<br>";
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
        $comments = $info['#']['COMMENTS']['0']['#']['COMMENT'];

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
            $comment->comment = backup_todb($com_info['#']['COMMENT']['0']['#']);
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
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br>";
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
        $ratings = $info['#']['RATINGS']['0']['#']['RATING'];

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

    //This function restores the glossary_alias table
    function glossary_alias_restore_mods($old_entry_id,$new_entry_id,$info,$restore) {

        global $CFG;

        $status = true;    

        //Get the comments array
        $aliases = $info['#']['ALIASES']['0']['#']['ALIAS'];

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

    //This function restores the glossary_categories
    function glossary_categories_restore_mods($old_glossary_id,$new_glossary_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the categories array
        $categories = $info['MOD']['#']['CATEGORIES']['0']['#']['CATEGORY'];

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
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br>";
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
        $entryids = $info['#']['ENTRIES']['0']['#']['ENTRY'];

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
            echo "action (".$log->module."-".$log->action.") unknow. Not restored<br>";                 //Debug
            break;
        }

        if ($status) {
            $status = $log;
        }
        return $status;
    }
?>
