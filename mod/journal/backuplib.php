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

    function journal_backup_mods($bf,$preferences) {

        global $CFG;

        $status = true;

        //Iterate over journal table
        $journals = get_records ("journal","course",$preferences->backup_course,"id");
        if ($journals) {
            foreach ($journals as $journal) {
                if (backup_mod_selected($preferences,'journal',$journal->id)) {
                    $status = journal_backup_one_mod($bf,$preferences,$journal);
                }
            }
        }
        return $status;
    }

    function journal_backup_one_mod($bf,$preferences,$journal) {

        global $CFG;
    
        if (is_numeric($journal)) {
            $journal = get_record('journal','id',$journal);
        }
    
        $status = true;

        //Start mod
        fwrite ($bf,start_tag("MOD",3,true));
        //Print journal data
        fwrite ($bf,full_tag("ID",4,false,$journal->id));
        fwrite ($bf,full_tag("MODTYPE",4,false,"journal"));
        fwrite ($bf,full_tag("NAME",4,false,$journal->name));
        fwrite ($bf,full_tag("INTRO",4,false,$journal->intro));
        fwrite ($bf,full_tag("INTROFORMAT",4,false,$journal->introformat));
        fwrite ($bf,full_tag("DAYS",4,false,$journal->days));
        fwrite ($bf,full_tag("ASSESSED",4,false,$journal->assessed));
        fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$journal->timemodified));

        //if we've selected to backup users info, then execute backup_journal_entries
        if (backup_userdata_selected($preferences,'journal',$journal->id)) {
            $status = backup_journal_entries($bf,$preferences,$journal->id);
        }
        //End mod
        $status =fwrite ($bf,end_tag("MOD",3,true));

        return $status;
    }

    //Backup journal_entries contents (executed from journal_backup_mods)
    function backup_journal_entries ($bf,$preferences,$journal) {

        global $CFG;

        $status = true;

        $journal_entries = get_records("journal_entries","journal",$journal,"id");
        //If there is entries
        if ($journal_entries) {
            //Write start tag
            $status =fwrite ($bf,start_tag("ENTRIES",4,true));
            //Iterate over each entry
            foreach ($journal_entries as $jou_ent) {
                //Start entry
                $status =fwrite ($bf,start_tag("ENTRY",5,true));
                //Print journal_entries contents
                fwrite ($bf,full_tag("ID",6,false,$jou_ent->id));
                fwrite ($bf,full_tag("USERID",6,false,$jou_ent->userid));
                fwrite ($bf,full_tag("MODIFIED",6,false,$jou_ent->modified));
                fwrite ($bf,full_tag("TEXT",6,false,$jou_ent->text));
                fwrite ($bf,full_tag("FORMAT",6,false,$jou_ent->format));
                fwrite ($bf,full_tag("RATING",6,false,$jou_ent->rating));
                fwrite ($bf,full_tag("ENTRYCOMMENT",6,false,$jou_ent->entrycomment));
                fwrite ($bf,full_tag("TEACHER",6,false,$jou_ent->teacher));
                fwrite ($bf,full_tag("TIMEMARKED",6,false,$jou_ent->timemarked));
                fwrite ($bf,full_tag("MAILED",6,false,$jou_ent->mailed));
                //End entry
                $status =fwrite ($bf,end_tag("ENTRY",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("ENTRIES",4,true));
        }
        return $status;
    }
 
   ////Return an array of info (name,value)
   function journal_check_backup_mods($course,$user_data=false,$backup_unique_code, $instances=null) {
       if (!empty($instances) && is_array($instances) && count($instances)) {
           $info = array();
           foreach ($instances as $id => $instance) {
               $info += journal_check_backup_mods_instances($instance,$backup_unique_code);
           }
           return $info;
       }
        //First the course data
        $info[0][0] = get_string("modulenameplural","journal");
        if ($ids = journal_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }

        //Now, if requested, the user_data
        if ($user_data) {
            $info[1][0] = get_string("entries","journal");
            if ($ids = journal_entry_ids_by_course ($course)) {
                $info[1][1] = count($ids);
            } else {
                $info[1][1] = 0;
            }
        }
        return $info;
    }

   ////Return an array of info (name,value)
   function journal_check_backup_mods_instances($instance,$backup_unique_code) {
        //First the course data
        $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
        $info[$instance->id.'0'][1] = '';

        //Now, if requested, the user_data
        if (!empty($instance->userdata)) {
            $info[$instance->id.'1'][0] = get_string("entries","journal");
            if ($ids = journal_entry_ids_by_instance ($instance->id)) {
                $info[$instance->id.'1'][1] = count($ids);
            } else {
                $info[$instance->id.'1'][1] = 0;
            }
        }
        return $info;
    }





    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of journals id
    function journal_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT a.id, a.course
                                 FROM {$CFG->prefix}journal a
                                 WHERE a.course = '$course'");
    }
   
    //Returns an array of journal entries id
    function journal_entry_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.journal
                                 FROM {$CFG->prefix}journal_entries s,
                                      {$CFG->prefix}journal a
                                 WHERE a.course = '$course' AND
                                       s.journal = a.id");
    }

    //Returns an array of journal entries id
    function journal_entry_ids_by_instance ($instanceid) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.journal
                                 FROM {$CFG->prefix}journal_entries s
                                 WHERE s.journal = $instanceid");
    }
?>
