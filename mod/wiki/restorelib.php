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
            $wiki->ewikiallowsafehtml = backup_todb($info['MOD']['#']['HTMLMODE']['0']['#']);
            $wiki->ewikiacceptbinary = backup_todb($info['MOD']['#']['EWIKIACCEPTBINARY']['0']['#']);
            $wiki->initialcontent = backup_todb($info['MOD']['#']['INITIALCONTENT']['0']['#']);
            $wiki->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);


            //The structure is equal to the db, so insert the wiki
            $newid = insert_record ("wiki",$wiki);

            //Do some output
            echo "<ul><li>".get_string("modulename","wiki")." \"".$wiki->name."\"<br>";
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //Restore wiki_entries
                $status = wiki_entries_restore_mods($mod->id,$newid,$info,$restore);
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
            $olduserid = backup_todb($ent_info['#']['USERID']['0']['#']);
            $oldgroupid = backup_todb($ent_info['#']['GROUPID']['0']['#']);

            //Now, build the wiki_ENTRIES record structure
            $entry->wikiid = $new_wiki_id;
            $entry->course= $restore->course_id;
            $entry->userid = backup_todb($ent_info['#']['USERID']['0']['#']);
            $entry->groupid = backup_todb($ent_info['#']['GROUPID']['0']['#']);
            $entry->pagename = backup_todb($ent_info['#']['PAGENAME']['0']['#']);
            $entry->timemodified = backup_todb($ent_info['#']['TIMEMODIFIED']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$entry->userid);
            if ($user) {
                $entry->userid = $user->new_id;
            }
            $group = backup_getid($restore->backup_unique_code,"group",$entry->groupid);
            if ($group) {
                $entry->groupid = $group->new_id;
            }
            //If userinfo was selected, restore the entry
            if ($restore->mods['wiki']->userinfo) {
                //The structure is equal to the db, so insert the wiki_entries
      	        $newid = insert_record ("wiki_entries",$entry);

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
                    backup_putid($restore->backup_unique_code,"wiki_entries",$oldid,$newid);
                    //Get old wiki id from backup_ids
                    $rec = get_record("backup_ids","backup_code",$restore->backup_unique_code,
                                                  "table_name","wiki",
                                                  "new_id",$new_wiki_id);
                    //Now copy moddata associated files
                    $status = wiki_restore_files ($rec->old_id, $new_wiki_id, $oldid, $newid, $restore);

                    //Restore wiki_pages
                    $status = wiki_pages_restore_mods($oldid,$newid,$ent_info,$restore);
                } else {
      	            $status = false;
	        }
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
            //traverse_xmlize($com_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($pag_info['#']['PAGENAME']['0']['#']."_".$pag_info['#']['VERSION']['0']['#']."_".$pag_info['#']['WIKI']['0']['#']);

            //Now, build the wiki_page record structure
            $page->wiki = $new_entry_id;
            $page->pagename = backup_todb($pag_info['#']['PAGENAME']['0']['#']);
            $page->version = backup_todb($pag_info['#']['VERSION']['0']['#']);
            $page->flags = backup_todb($pag_info['#']['FLAGS']['0']['#']);
            $page->content = backup_todb($pag_info['#']['CONTENT']['0']['#']);
            $page->author = backup_todb($pag_info['#']['AUTHOR']['0']['#']);
            $page->userid = backup_todb($pag_info['#']['USERID']['0']['#']);
            $page->created = backup_todb($pag_info['#']['CREATED']['0']['#']);
            $page->lastmodified = backup_todb($pag_info['#']['LASTMODIFIED']['0']['#']);
            $page->refs = backup_todb($pag_info['#']['REFS']['0']['#']);
            $page->meta = backup_todb($pag_info['#']['META']['0']['#']);
            $page->hits = backup_todb($pag_info['#']['HITS']['0']['#']);
            //The structure is equal to the db, so insert the wiki_comments
            insert_record ("wiki_pages",$page, false,"pagename");
#print "<pre>"; print_r($page); print "</pre>";            
            print ($r?"TRUE":"FALSE")."<br>\n";
            #$newid = insert_record ("wiki_pages",$page);
            #if($newid) {
            #  $newid = backup_todb($pag_info['#']['PAGENAME']['0']['#']."_".$pag_info['#']['VERSION']['0']['#']."_".$new_entry_id);
            #}
            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br>";
                }
                backup_flush(300);
            }
            #if ($newid) {
            #    //We have the newid, update backup_ids
            #    backup_putid($restore->backup_unique_code,"wiki_pages",$oldid,$newid);
            #} else {
            #    $status = false;
            #}
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

        //Now, locate forum directory
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

        //If todo, we create the neccesary dirs in course moddata/forum
        if ($status and $todo) {
            //First this forum id
            $this_wiki_path = $wiki_path."/".$newwikiid;
            $status = check_dir_exists($this_wiki_path,true);
            //Now this post id
            $entry_wiki_path = $this_wiki_path."/".$newentryid;
            //And now, copy temp_path to post_forum_path
            $status = backup_copy_file($temp_path, $entry_wiki_path);
        }

        return $status;
    }
?>
