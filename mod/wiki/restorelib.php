<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //wiki mods

    //This is the "graphical" structure of the wiki mod:
    //
    //                       wiki
    //                     (CL,pk->id)
    //
    //                    wiki_entries
    //                     (pk->id, fk->wikiid)
    //
    //                    wiki_pages
    //                     (pk->pagename,version,wiki, fk->wiki)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    function wiki_restore_mods($mod,$restore) {

        global $CFG;

        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

        if ($data) {
            //Now get completed xmlized object
            $info = $data->info;
            //if necessary, write to restorelog and adjust date/time fields
            if ($restore->course_startdateoffset) {
                restore_log_date_changes('Wiki', $restore, $info['MOD']['#'], array('TIMEMODIFIED'));
            }
            //traverse_xmlize($info);                                                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the wiki record structure
            $wiki->course = $restore->course_id;
            $wiki->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $wiki->summary = backup_todb($info['MOD']['#']['SUMMARY']['0']['#']);
            $wiki->pagename = backup_todb($info['MOD']['#']['PAGENAME']['0']['#']);
            $wiki->wtype = backup_todb($info['MOD']['#']['WTYPE']['0']['#']);
            $wiki->ewikiprinttitle = backup_todb($info['MOD']['#']['EWIKIPRINTTITLE']['0']['#']);
            $wiki->htmlmode = backup_todb($info['MOD']['#']['HTMLMODE']['0']['#']);
            $wiki->ewikiacceptbinary = backup_todb($info['MOD']['#']['EWIKIACCEPTBINARY']['0']['#']);
            $wiki->disablecamelcase = backup_todb($info['MOD']['#']['DISABLECAMELCASE']['0']['#']);
            $wiki->setpageflags = backup_todb($info['MOD']['#']['SETPAGEFLAGS']['0']['#']);
            $wiki->strippages = backup_todb($info['MOD']['#']['STRIPPAGES']['0']['#']);
            $wiki->removepages = backup_todb($info['MOD']['#']['REMOVEPAGES']['0']['#']);
            $wiki->revertchanges = backup_todb($info['MOD']['#']['REVERTCHANGES']['0']['#']);
            $wiki->initialcontent = backup_todb($info['MOD']['#']['INITIALCONTENT']['0']['#']);
            $wiki->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);

            //The structure is equal to the db, so insert the wiki
            $newid = insert_record ("wiki",$wiki);

            //Do some output
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("modulename","wiki")." \"".format_string(stripslashes($wiki->name),true)."\"</li>";
            }
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //Now check if want to restore user data and do it.
                if (restore_userdata_selected($restore,'wiki',$mod->id)) {
                    //Restore wiki_entries
                    $status = wiki_entries_restore_mods($mod->id,$newid,$info,$restore);
                }
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }

        return $status;
    }

    //This function restores the wiki_entries
    function wiki_entries_restore_mods($old_wiki_id,$new_wiki_id,$info,$restore) {

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

            //Now, build the wiki_ENTRIES record structure
            $entry = new object();
            $entry->wikiid = $new_wiki_id;
            $entry->course = $restore->course_id;
            $entry->userid = backup_todb($ent_info['#']['USERID']['0']['#']);
            $entry->groupid = backup_todb($ent_info['#']['GROUPID']['0']['#']);
            $entry->pagename = backup_todb($ent_info['#']['PAGENAME']['0']['#']);
            $entry->timemodified = backup_todb($ent_info['#']['TIMEMODIFIED']['0']['#']);
            $entry->timemodified += $restore->course_startdateoffset;
            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$entry->userid);
            if ($user) {
                $entry->userid = $user->new_id;
            }
            //We have to recode the groupid field
            $group = restore_group_getid($restore, $entry->groupid);
            if ($group) {
                $entry->groupid = $group->new_id;
            }

            //The structure is equal to the db, so insert the wiki_entries
            $newid = insert_record ("wiki_entries",$entry);

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
                backup_putid($restore->backup_unique_code,"wiki_entries",$oldid,$newid);

                //Restore wiki_pages
                $status = wiki_pages_restore_mods($oldid,$newid,$ent_info,$restore);

                //Now copy moddata associated files
                $status = wiki_restore_files ($old_wiki_id, $new_wiki_id, $oldid, $newid, $restore);
            } else {
                $status = false;
            }
        }
        return $status;
    }

    //This function restores the wiki_pages
    function wiki_pages_restore_mods($old_entry_id,$new_entry_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the comments array
        $pages = $info['#']['PAGES']['0']['#']['PAGE'];

        //Iterate over pages
        for($i = 0; $i < sizeof($pages); $i++) {
            $pag_info = $pages[$i];
            //traverse_xmlize($pag_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($pag_info['#']['ID']['0']['#']);

            //Now, build the wiki_page record structure
            $page->wiki = $new_entry_id;
            $page->pagename = backup_todb($pag_info['#']['PAGENAME']['0']['#']);
            $page->version = backup_todb($pag_info['#']['VERSION']['0']['#']);
            $page->flags = backup_todb($pag_info['#']['FLAGS']['0']['#']);
            $page->content = backup_todb($pag_info['#']['CONTENT']['0']['#']);
            $page->author = backup_todb($pag_info['#']['AUTHOR']['0']['#']);
            $page->userid = backup_todb($pag_info['#']['USERID']['0']['#']);
            $page->created = backup_todb($pag_info['#']['CREATED']['0']['#']);
            $page->created += $restore->course_startdateoffset;
            $page->lastmodified = backup_todb($pag_info['#']['LASTMODIFIED']['0']['#']);
            $page->lastmodified += $restore->course_startdateoffset;
            $page->refs = str_replace("$@LINEFEED@$","\n",backup_todb($pag_info['#']['REFS']['0']['#']));
            $page->meta = backup_todb($pag_info['#']['META']['0']['#']);
            $page->hits = backup_todb($pag_info['#']['HITS']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$page->userid);
            if ($user) {
                $page->userid = $user->new_id;
            }
            //The structure is equal to the db, so insert the wiki_pages
            $newid = insert_record ("wiki_pages",$page);

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
                backup_putid($restore->backup_unique_code,"wiki_pages",$oldid,$newid);
            } else {
                $status = false;
            }
        }
        return $status;
    }
    
    function wiki_restore_files ($oldwikiid, $newwikiid, $oldentryid, $newentryid, $restore) {

        global $CFG;

        $status = true;
        $todo = false;
        $moddata_path = "";
        $forum_path = "";
        $temp_path = "";

        //First, we check to "course_id" exists and create is as necessary
        //in CFG->dataroot
        $dest_dir = $CFG->dataroot."/".$restore->course_id;
        $status = check_dir_exists($dest_dir,true);

        //First, locate course's moddata directory
        $moddata_path = $CFG->dataroot."/".$restore->course_id."/".$CFG->moddata;
  
        //Check it exists and create it
        $status = check_dir_exists($moddata_path,true);

        //Now, locate wiki directory
        if ($status) {
            $wiki_path = $moddata_path."/wiki";
            //Check it exists and create it
            $status = check_dir_exists($wiki_path,true);
        }

        //Now locate the temp dir we are restoring from
        if ($status) {
            $temp_path = $CFG->dataroot."/temp/backup/".$restore->backup_unique_code.
                         "/moddata/wiki/".$oldwikiid."/".$oldentryid;
            //Check it exists
            if (is_dir($temp_path)) {
                $todo = true;
            }
        }

        //If todo, we create the neccesary dirs in course moddata/wiki
        if ($status and $todo) {
            //First this wiki id
            $this_wiki_path = $wiki_path."/".$newwikiid;
            $status = check_dir_exists($this_wiki_path,true);
            //Now this entry id
            $entry_wiki_path = $this_wiki_path."/".$newentryid;
            //And now, copy temp_path to entry_wiki_path
            $status = backup_copy_file($temp_path, $entry_wiki_path);
        }

        return $status;
    }

    //Return a content decoded to support interactivities linking. Every module
    //should have its own. They are called automatically from
    //wiki_decode_content_links_caller() function in each module
    //in the restore process
    function wiki_decode_content_links ($content,$restore) {
            
        global $CFG;
            
        $result = $content;
                
        //Link to the list of wikis
                
        $searchstring='/\$@(WIKIINDEX)\*([0-9]+)@\$/';
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
                $searchstring='/\$@(WIKIINDEX)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/wiki/index.php?id='.$rec->new_id,$result);
                } else { 
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/wiki/index.php?id='.$old_id,$result);
                }
            }
        }

        //Link to wiki view by moduleid

        $searchstring='/\$@(WIKIVIEWBYID)\*([0-9]+)@\$/';
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
                $searchstring='/\$@(WIKIVIEWBYID)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/wiki/view.php?id='.$rec->new_id,$result);
                } else {
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/wiki/view.php?id='.$old_id,$result);
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
    function wiki_decode_content_links_caller($restore) {
        global $CFG;
        $status = true;
        
        //Process every wiki PAGE in the course
        if ($pages = get_records_sql ("SELECT p.id, p.content
                                   FROM {$CFG->prefix}wiki_pages p,
                                        {$CFG->prefix}wiki w
                                   WHERE w.course = $restore->course_id AND
                                         p.wiki = w.id")) {
            //Iterate over each post->message
            $i = 0;   //Counter to send some output to the browser to avoid timeouts
            foreach ($pages as $page) {
                //Increment counter
                $i++;
                $content = $page->definition;
                $result = restore_decode_content_links_worker($content,$restore);
                if ($result != $content) {
                    //Update record
                    $page->content = addslashes($result);
                    $status = update_record("wiki_pages",$page);
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

        //Process every wiki (summary) in the course
        if ($wikis = get_records_sql ("SELECT w.id, w.summary
                                   FROM {$CFG->prefix}wiki w
                                   WHERE w.course = $restore->course_id")) {
            //Iterate over each wiki->summary
            $i = 0;   //Counter to send some output to the browser to avoid timeouts
            foreach ($wikis as $wiki) {
                //Increment counter
                $i++;
                $content = $wiki->summary;
                $result = restore_decode_content_links_worker($content,$restore);
                if ($result != $content) {
                    //Update record
                    $wiki->summary = addslashes($result);
                    $status = update_record("wiki",$wiki);
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
?>
