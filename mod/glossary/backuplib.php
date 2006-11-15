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
    //----------------------------------------------------------------------------------

    function glossary_backup_mods($bf,$preferences) {
        
        global $CFG;

        $status = true;

        //Iterate over glossary table
        $glossaries = get_records ("glossary","course",$preferences->backup_course,"mainglossary");
        if ($glossaries) {
            foreach ($glossaries as $glossary) {
                if (backup_mod_selected($preferences,'glossary',$glossary->id)) {
                    $status = glossary_backup_one_mod($bf,$preferences,$glossary);
                }
            }
        }
        return $status;
    }

    function glossary_backup_one_mod($bf,$preferences,$glossary) {

        global $CFG;
    
        if (is_numeric($glossary)) {
            $glossary = get_record('glossary','id',$glossary);
        }
    
        $status = true;

        //Start mod
        fwrite ($bf,start_tag("MOD",3,true));
        //Print glossary data
        fwrite ($bf,full_tag("ID",4,false,$glossary->id));
        fwrite ($bf,full_tag("MODTYPE",4,false,"glossary"));
        fwrite ($bf,full_tag("NAME",4,false,$glossary->name));
        fwrite ($bf,full_tag("INTRO",4,false,$glossary->intro));
        fwrite ($bf,full_tag("ALLOWDUPLICATEDENTRIES",4,false,$glossary->allowduplicatedentries));
        fwrite ($bf,full_tag("DISPLAYFORMAT",4,false,$glossary->displayformat));
        fwrite ($bf,full_tag("MAINGLOSSARY",4,false,$glossary->mainglossary));
        fwrite ($bf,full_tag("SHOWSPECIAL",4,false,$glossary->showspecial));
        fwrite ($bf,full_tag("SHOWALPHABET",4,false,$glossary->showalphabet));
        fwrite ($bf,full_tag("SHOWALL",4,false,$glossary->showall));
        fwrite ($bf,full_tag("ALLOWCOMMENTS",4,false,$glossary->allowcomments));
        fwrite ($bf,full_tag("ALLOWPRINTVIEW",4,false,$glossary->allowprintview));
        fwrite ($bf,full_tag("USEDYNALINK",4,false,$glossary->usedynalink));
        fwrite ($bf,full_tag("DEFAULTAPPROVAL",4,false,$glossary->defaultapproval));
        fwrite ($bf,full_tag("GLOBALGLOSSARY",4,false,$glossary->globalglossary));
        fwrite ($bf,full_tag("ENTBYPAGE",4,false,$glossary->entbypage));
        fwrite ($bf,full_tag("EDITALWAYS",4,false,$glossary->editalways));
        fwrite ($bf,full_tag("RSSTYPE",4,false,$glossary->rsstype));
        fwrite ($bf,full_tag("RSSARTICLES",4,false,$glossary->rssarticles));
        fwrite ($bf,full_tag("TIMECREATED",4,false,$glossary->timecreated));
        fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$glossary->timemodified));
        fwrite ($bf,full_tag("ASSESSED",4,false,$glossary->assessed));
        fwrite ($bf,full_tag("ASSESSTIMESTART",4,false,$glossary->assesstimestart));
        fwrite ($bf,full_tag("ASSESSTIMEFINISH",4,false,$glossary->assesstimefinish));
        fwrite ($bf,full_tag("SCALE",4,false,$glossary->scale));

        //Only if preferences->backup_users != 2 (none users). Else, teachers entries will be included.
        if ($preferences->backup_users != 2) {
            backup_glossary_entries($bf,$preferences,$glossary->id, $preferences->mods["glossary"]->userinfo);
        }

        backup_glossary_categories($bf,$preferences,$glossary->id, $preferences->mods["glossary"]->userinfo);

        //End mod
        $status =fwrite ($bf,end_tag("MOD",3,true));

        return $status;
    }

    //Backup glossary_categories and entries_categories contents (executed from glossary_backup_mods)
    function backup_glossary_categories ($bf,$preferences,$glossary, $userinfo) {

        global $CFG;

        $status = true;

        $glossary_categories = get_records("glossary_categories","glossaryid",$glossary,"id");
        //If there is categories
        if ($glossary_categories) {
            $status =fwrite ($bf,start_tag("CATEGORIES",4,true));

            //Iterate over each category
            foreach ($glossary_categories as $glo_cat) {
                //Start category
                //Print category contents
                $status =fwrite ($bf,start_tag("CATEGORY",5,true));

                fwrite ($bf,full_tag("ID",6,false,$glo_cat->id));
                fwrite ($bf,full_tag("GLOSSARYID",6,false,$glo_cat->glossaryid));
                fwrite ($bf,full_tag("NAME",6,false,$glo_cat->name));
                fwrite ($bf,full_tag("USEDYNALINK",6,false,$glo_cat->usedynalink));

                //Only if preferences->backup_users != 2 (none users). Else, teachers entries will be included.
                if ($preferences->backup_users != 2) {
                    $status = backup_glossary_entries_categories ($bf,$preferences,$glo_cat->id);
                }

                $status =fwrite ($bf,end_tag("CATEGORY",5,true));

            }
            //Write end tag
            $status =fwrite ($bf,end_tag("CATEGORIES",4,true));
        }
        return $status;
    }

    //Backup entries_categories contents (executed from backup_glossary_categories)
    function backup_glossary_entries_categories ($bf,$preferences,$categoryid) {

        global $CFG;

        $status = true;

        $entries = get_records("glossary_entries_categories","categoryid",$categoryid);
        if ($entries) {
            $status =fwrite ($bf,start_tag("ENTRIES",6,true));
            foreach ($entries as $entry) {
                fwrite ($bf,start_tag("ENTRY",7,true));
                fwrite ($bf,full_tag("ENTRYID",8,false,$entry->entryid));
                $status =fwrite ($bf,end_tag("ENTRY",7,true));
            }
            $status =fwrite ($bf,end_tag("ENTRIES",6,true));
        }
        return $status;
    }

    //Backup glossary_entries contents (executed from glossary_backup_mods)
    function backup_glossary_entries ($bf,$preferences,$glossary, $userinfo) {

        global $CFG;

        $status = true;

        $glossary_entries = get_records("glossary_entries","glossaryid",$glossary,"id");
        //If there is entries
        if ($glossary_entries) {            
            $dumped_entries = 0;
            
            //Iterate over each entry
            foreach ($glossary_entries as $glo_ent) {
                //Start entry
                //Print submission contents
               if ($glo_ent->teacherentry or $userinfo) {
                    $dumped_entries++;
                    if ($dumped_entries == 1) {
                        //Write start tag
                        $status =fwrite ($bf,start_tag("ENTRIES",4,true));
                    }
                    $status =fwrite ($bf,start_tag("ENTRY",5,true));

                    fwrite ($bf,full_tag("ID",6,false,$glo_ent->id));
                    fwrite ($bf,full_tag("USERID",6,false,$glo_ent->userid));
                    fwrite ($bf,full_tag("CONCEPT",6,false,trim($glo_ent->concept)));
                    fwrite ($bf,full_tag("DEFINITION",6,false,$glo_ent->definition));
                    fwrite ($bf,full_tag("FORMAT",6,false,$glo_ent->format));
                    fwrite ($bf,full_tag("ATTACHMENT",6,false,$glo_ent->attachment));
                    fwrite ($bf,full_tag("SOURCEGLOSSARYID",6,false,$glo_ent->sourceglossaryid));
                    fwrite ($bf,full_tag("USEDYNALINK",6,false,$glo_ent->usedynalink));
                    fwrite ($bf,full_tag("CASESENSITIVE",6,false,$glo_ent->casesensitive));
                    fwrite ($bf,full_tag("FULLMATCH",6,false,$glo_ent->fullmatch));
                    fwrite ($bf,full_tag("APPROVED",6,false,$glo_ent->approved));
                    fwrite ($bf,full_tag("TIMECREATED",6,false,$glo_ent->timecreated));
                    fwrite ($bf,full_tag("TIMEMODIFIED",6,false,$glo_ent->timemodified));
                    fwrite ($bf,full_tag("TEACHERENTRY",6,false,$glo_ent->teacherentry));

                    $status = backup_glossary_aliases ($bf,$preferences,$glo_ent->id);

                    if ( $userinfo ) {
                        $status = backup_glossary_comments ($bf,$preferences,$glo_ent->id);
                        $status = backup_glossary_ratings ($bf,$preferences,$glo_ent->id);
                    }

                    $status =fwrite ($bf,end_tag("ENTRY",5,true));

                    //Now include entry attachment in backup (if it exists)
                    if ($glo_ent->attachment) {
                        $status = backup_glossary_files($bf,$preferences,$glossary,$glo_ent->id);
                    }
                }
            }
            if ( $dumped_entries > 0 ) {
                //Write end tag
                $status =fwrite ($bf,end_tag("ENTRIES",4,true));
            }
        }
        return $status;
    }

    //Backup glossary_comments contents (executed from backup_glossary_entries)
    function backup_glossary_comments ($bf,$preferences,$entryid) {

        global $CFG;

        $status = true;

        $comments = get_records("glossary_comments","entryid",$entryid);
        if ($comments) {
            $status =fwrite ($bf,start_tag("COMMENTS",6,true));
            foreach ($comments as $comment) {
                $status =fwrite ($bf,start_tag("COMMENT",7,true));

                fwrite ($bf,full_tag("ID",8,false,$comment->id));
                fwrite ($bf,full_tag("USERID",8,false,$comment->userid));
                fwrite ($bf,full_tag("ENTRYCOMMENT",8,false,$comment->entrycomment));
                fwrite ($bf,full_tag("FORMAT",8,false,$comment->format));
                fwrite ($bf,full_tag("TIMEMODIFIED",8,false,$comment->timemodified));

                $status =fwrite ($bf,end_tag("COMMENT",7,true));        
            }
            $status =fwrite ($bf,end_tag("COMMENTS",6,true));
        }
        return $status;
    }

   //Backup glossary_ratings contents (executed from backup_glossary_entries)
    function backup_glossary_ratings ($bf,$preferences,$entryid) {

        global $CFG;

        $status = true;

        $ratings = get_records("glossary_ratings","entryid",$entryid);
        if ($ratings) {
            $status =fwrite ($bf,start_tag("RATINGS",6,true));
            foreach ($ratings as $rating) {
                $status =fwrite ($bf,start_tag("RATING",7,true));

                fwrite ($bf,full_tag("ID",8,false,$rating->id));
                fwrite ($bf,full_tag("USERID",8,false,$rating->userid));
                fwrite ($bf,full_tag("TIME",8,false,$rating->time));
                fwrite ($bf,full_tag("RATING",8,false,$rating->rating));

                $status =fwrite ($bf,end_tag("RATING",7,true));
            }
            $status =fwrite ($bf,end_tag("RATINGS",6,true));
        }
        return $status;
    }
   
    //Backup glossary_alias contents (executed from backup_glossary_entries)
    function backup_glossary_aliases ($bf,$preferences,$entryid) {

        global $CFG;

        $status = true;

        $aliases = get_records("glossary_alias","entryid",$entryid);
        if ($aliases) {
            $status =fwrite ($bf,start_tag("ALIASES",6,true));
            foreach ($aliases as $alias) {
                $status =fwrite ($bf,start_tag("ALIAS",7,true));

                fwrite ($bf,full_tag("ALIAS_TEXT",8,false,trim($alias->alias)));

                $status =fwrite ($bf,end_tag("ALIAS",7,true));        
            }
            $status =fwrite ($bf,end_tag("ALIASES",6,true));
        }
        return $status;
    }

    //Backup glossary files because we've selected to backup user info
    //or current entry is a teacher entry
    function backup_glossary_files($bf,$preferences,$glossary,$entry) {

        global $CFG;

        $status = true;

        //First we check to moddata exists and create it as necessary
        //in temp/backup/$backup_code  dir
        $status = check_and_create_moddata_dir($preferences->backup_unique_code);

        //Now we check that moddata/glossary dir exists and create it as necessary
        //in temp/backup/$backup_code/moddata dir
        $glo_dir_to = $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code.
                      "/".$CFG->moddata."/glossary";
        //Let's create it as necessary
        $status = check_dir_exists($glo_dir_to,true);

        //Now we check that the moddata/glossary/$glossary dir exists and create it as necessary
        //in temp/backup/$backup_code/moddata/glossary
        $status = check_dir_exists($glo_dir_to."/".$glossary,true);

        //Now copy the moddata/glossary/$glossary/$entry to
        //temp/backup/$backup_code/moddata/glossary/$glossary/$entry
        if ($status) {
            //Calculate moddata/glossary dir
            $glo_dir_from = $CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/glossary";
            //Only if it exists !! 
            if (is_dir($glo_dir_from."/".$glossary."/".$entry)) {
                $status = backup_copy_file($glo_dir_from."/".$glossary."/".$entry,
                                           $glo_dir_to."/".$glossary."/".$entry);
            }
        }

        return $status;

    }

   ////Return an array of info (name,value)
   function glossary_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null) {
      if (!empty($instances) && is_array($instances) && count($instances)) {
           $info = array();
           foreach ($instances as $id => $instance) {
               $info += glossary_check_backup_mods_instances($instance,$backup_unique_code);
           }
           return $info;
       }
        //First the course data
        $info[0][0] = get_string("modulenameplural","glossary");
        if ($ids = glossary_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }

        //Now, if requested, the user_data
        if ($user_data) {
            $info[1][0] = get_string("concepts","glossary");
            if ($ids = glossary_entries_ids_by_course ($course)) {
                $info[1][1] = count($ids);
            } else {
                $info[1][1] = 0;
            }
        }
        return $info;
    }

   ////Return an array of info (name,value)
   function glossary_check_backup_mods_instances($instance,$backup_unique_code) {
        //First the course data
        $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
        $info[$instance->id.'0'][1] = '';

        //Now, if requested, the user_data
        if (!empty($instance->userdata)) {
            $info[$instance->id.'1'][0] = get_string("concepts","glossary");
            if ($ids = glossary_entries_ids_by_instance ($instance->id)) {
                $info[$instance->id.'1'][1] = count($ids);
            } else {
                $info[$instance->id.'1'][1] = 0;
            }
        }
        return $info;
    }

    //Return a content encoded to support interactivities linking. Every module
    //should have its own. They are called automatically from the backup procedure.
    function glossary_encode_content_links ($content,$preferences) {

        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        //Link to the list of glossarys
        $buscar="/(".$base."\/mod\/glossary\/index.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@GLOSSARYINDEX*$2@$',$content);

        //Link to glossary view by moduleid
        $buscar="/(".$base."\/mod\/glossary\/view.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@GLOSSARYVIEWBYID*$2@$',$result);

        return $result;
    }

    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of glossaries id
    function glossary_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT a.id, a.course
                                 FROM {$CFG->prefix}glossary a
                                 WHERE a.course = '$course'");
    }
   
    //Returns an array of glossary_answers id
    function glossary_entries_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.glossaryid
                                 FROM {$CFG->prefix}glossary_entries s,
                                      {$CFG->prefix}glossary a
                                 WHERE a.course = '$course' AND
                                       s.glossaryid = a.id");
    }

    //Returns an array of glossary_answers id
    function glossary_entries_ids_by_instance ($instanceid) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.glossaryid
                                 FROM {$CFG->prefix}glossary_entries s
                                 WHERE s.glossaryid = $instanceid");
    }
?>
