<?php //$Id$
    //This php script contains all the stuff to backup/restore
    //journal mods

    //This is the "graphical" structure of the journal mod:
    //
    //                      journal                                      
    //                    (CL,pk->id)
    //                        |
    //                        |
    //                        |
    //                   journal_entries 
    //               (UL,pk->id, fk->journal)     
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
    function journal_restore_mods($mod,$restore) {

        global $CFG;

        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

        if ($data) {
            //Now get completed xmlized object
            $info = $data->info;
            //if necessary, write to restorelog and adjust date/time fields
            if ($restore->course_startdateoffset) {
                restore_log_date_changes('Journal', $restore, $info['MOD']['#'], array('TIMEMODIFIED'));
            }
            //traverse_xmlize($info);                                                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the JOURNAL record structure
            $journal->course = $restore->course_id;
            $journal->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $journal->intro = backup_todb($info['MOD']['#']['INTRO']['0']['#']);
            $journal->introformat = backup_todb($info['MOD']['#']['INTROFORMAT']['0']['#']);
            $journal->days = backup_todb($info['MOD']['#']['DAYS']['0']['#']);
            $journal->assessed = backup_todb($info['MOD']['#']['ASSESSED']['0']['#']);
            $journal->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);

            //We have to recode the assessed field if it is <0 (scale)
            if ($journal->assessed < 0) {
                $scale = backup_getid($restore->backup_unique_code,"scale",abs($journal->assessed));
                if ($scale) {
                    $journal->assessed = -($scale->new_id);
                }
            }

            //The structure is equal to the db, so insert the journal
            $newid = insert_record ("journal",$journal);

            //Do some output
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("modulename","journal")." \"".format_string(stripslashes($journal->name),true)."\"</li>";
            }
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //Now check if want to restore user data and do it.
                if (restore_userdata_selected($restore,'journal',$mod->id)) {
                    //Restore journal_entries
                    $status = journal_entries_restore_mods ($mod->id, $newid,$info,$restore);
                }
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }

        return $status;
    }


    //This function restores the journal_entries
    function journal_entries_restore_mods($old_journal_id, $new_journal_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the entries array
        $entries = $info['MOD']['#']['ENTRIES']['0']['#']['ENTRY'];

        //Iterate over entries
        for($i = 0; $i < sizeof($entries); $i++) {
            $entry_info = $entries[$i];
            //traverse_xmlize($entry_info);                                                               //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!! $sub_info changed to $entry_info
            $oldid = backup_todb($entry_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($entry_info['#']['USERID']['0']['#']);

            //Now, build the JOURNAL_ENTRIES record structure
            $entry->journal = $new_journal_id;
            $entry->userid = backup_todb($entry_info['#']['USERID']['0']['#']);
            $entry->modified = backup_todb($entry_info['#']['MODIFIED']['0']['#']);
            $entry->modified += $restore->course_startdateoffset;
            $entry->text = backup_todb($entry_info['#']['TEXT']['0']['#']);
            $entry->format = backup_todb($entry_info['#']['FORMAT']['0']['#']);
            $entry->rating = backup_todb($entry_info['#']['RATING']['0']['#']);
            if (isset($entry_info['#']['COMMENT']['0']['#'])) {
                $entry->entrycomment = backup_todb($entry_info['#']['COMMENT']['0']['#']);
            } else {
                $entry->entrycomment = backup_todb($entry_info['#']['ENTRYCOMMENT']['0']['#']);
            }
            $entry->teacher = backup_todb($entry_info['#']['TEACHER']['0']['#']);
            $entry->timemarked = backup_todb($entry_info['#']['TIMEMARKED']['0']['#']);
            $entry->timemarked += $restore->course_startdateoffset;
            $entry->mailed = backup_todb($entry_info['#']['MAILED']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$entry->userid);
            if ($user) {
                $entry->userid = $user->new_id;
            }

            //We have to recode the teacher field
            $user = backup_getid($restore->backup_unique_code,"user",$entry->teacher);
            if ($user) {
                $entry->teacher = $user->new_id;
            }

            //The structure is equal to the db, so insert the journal_entry
            $newid = insert_record ("journal_entries",$entry);

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
                backup_putid($restore->backup_unique_code,"journal_entry",$oldid,
                             $newid);
            } else {
                $status = false;
            }
        }

        return $status;
    }

    //This function converts texts in FORMAT_WIKI to FORMAT_MARKDOWN for
    //some texts in the module
    function journal_restore_wiki2markdown ($restore) {

        global $CFG;

        $status = true;

        //Convert journal_entries->text
        if ($records = get_records_sql ("SELECT e.id, e.text, e.format
                                         FROM {$CFG->prefix}journal_entries e,
                                              {$CFG->prefix}journal j,
                                              {$CFG->prefix}backup_ids b
                                         WHERE j.id = e.journal AND
                                               j.course = $restore->course_id AND
                                               e.format = ".FORMAT_WIKI. " AND
                                               b.backup_code = $restore->backup_unique_code AND
                                               b.table_name = 'journal_entries' AND
                                               b.new_id = e.id")) {
            foreach ($records as $record) {
                //Rebuild wiki links
                $record->text = restore_decode_wiki_content($record->text, $restore);
                //Convert to Markdown
                $wtm = new WikiToMarkdown();
                $record->text = $wtm->convert($record->text, $restore->course_id);
                $record->format = FORMAT_MARKDOWN;
                $status = update_record('journal_entries', addslashes_object($record));
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

        //Convert journal->intro
        if ($records = get_records_sql ("SELECT j.id, j.intro, j.introformat
                                         FROM {$CFG->prefix}journal j,
                                              {$CFG->prefix}backup_ids b
                                         WHERE j.course = $restore->course_id AND
                                               j.introformat = ".FORMAT_WIKI. " AND
                                               b.backup_code = $restore->backup_unique_code AND
                                               b.table_name = 'journal' AND
                                               b.new_id = j.id")) {
            foreach ($records as $record) {
                //Rebuild wiki links
                $record->intro = restore_decode_wiki_content($record->intro, $restore);
                //Convert to Markdown
                $wtm = new WikiToMarkdown();
                $record->intro = $wtm->convert($record->intro, $restore->course_id);
                $record->introformat = FORMAT_MARKDOWN;
                $status = update_record('journal', addslashes_object($record));
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
    function journal_restore_logs($restore,$log) {
    
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
        case "add entry":
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
        case "update entry":
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
        case "view responses":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "report.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "update feedback":
            if ($log->cmid) {
                $log->url = "report.php?id=".$log->cmid;
                $status = true;
            }
            break;
        case "view all":
            $log->url = "index.php?id=".$log->course;
            $status = true;
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
