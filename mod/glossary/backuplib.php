<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //glossary mods

    //This is the "graphical" structure of the glossary mod:
    //
    //                     glossary                                      
    //                    (CL,pk->id)
    //                        |
    //                        |
    //                        |
    //                  glossary_entries 
    //               (UL,pk->id, fk->glossaryid, files)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    function glossary_backup_mods($bf,$preferences) {
        
        global $CFG;

        $status = true;

        //Iterate over glossary table
        $glossaries = get_records ("glossary","course",$preferences->backup_course,"id");
        if ($glossaries) {
            foreach ($glossaries as $glossary) {
                //Start mod
                fwrite ($bf,start_tag("MOD",3,true));
                //Print glossary data
                fwrite ($bf,full_tag("ID",4,false,$glossary->id));
                fwrite ($bf,full_tag("MODTYPE",4,false,"glossary"));
                fwrite ($bf,full_tag("NAME",4,false,$glossary->name));
                fwrite ($bf,full_tag("STUDENTCANPOST",4,false,$glossary->studentcanpost));
                fwrite ($bf,full_tag("ALLOWDUPLICATEDENTRIES",4,false,$glossary->allowduplicatedentries));
                fwrite ($bf,full_tag("DISPLAYFORMAT",4,false,$glossary->displayformat));	
                fwrite ($bf,full_tag("MAINGLOSSARY",4,false,$glossary->mainglossary));
                fwrite ($bf,full_tag("SHOWSPECIAL",4,false,$glossary->showspecial));
                fwrite ($bf,full_tag("SHOWALPHABET",4,false,$glossary->showalphabet));
                fwrite ($bf,full_tag("SHOWALL",4,false,$glossary->showall));
                fwrite ($bf,full_tag("TIMECREATED",4,false,$glossary->timecreated));
                fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$glossary->timemodified));

                backup_glossary_entries($bf,$preferences,$glossary->id, $preferences->mods["glossary"]->userinfo);

                //End mod
                $status =fwrite ($bf,end_tag("MOD",3,true));
            }
        }
        return $status;
    }

    //Backup glossary_entries contents made by teachers (executed from glossary_backup_mods)
    function backup_glossary_entries ($bf,$preferences,$glossary, $userinfo) {

        global $CFG;

        $status = true;

        $glossary_entries = get_records("glossary_entries","glossaryid",$glossary,"id");
        //If there is submissions
        if ($glossary_entries) {            
		$dumped_entries = 0;
            
            //Iterate over each answer
            foreach ($glossary_entries as $glo_ent) {
                //Start answer
                
                //Print submission contents
		    if ( $glo_ent->teacherentry or $userinfo) {
			 $dumped_entries++;
			 if ($dumped_entries == 1) {
				//Write start tag
				$status =fwrite ($bf,start_tag("ENTRIES",4,true));
			 }
  			 $status =fwrite ($bf,start_tag("ENTRY",5,true));

                   fwrite ($bf,full_tag("ID",6,false,$glo_ent->id));
                   fwrite ($bf,full_tag("USERID",6,false,$glo_ent->userid));
                   fwrite ($bf,full_tag("CONCEPT",6,false,$glo_ent->concept));
                   fwrite ($bf,full_tag("DEFINITION",6,false,$glo_ent->definition));
                   fwrite ($bf,full_tag("FORMAT",6,false,$glo_ent->format));
                   fwrite ($bf,full_tag("ATTACHMENT",6,false,$glo_ent->attachment));
                   fwrite ($bf,full_tag("TEACHERENTRY",6,false,$glo_ent->teacherentry));

                   $status =fwrite ($bf,end_tag("ENTRY",5,true));
		    }
            }
		if ( $dumped_entries > 0 ) {
	            //Write end tag
      	      $status =fwrite ($bf,end_tag("ENTRIES",4,true));
		}
        }
        return $status;
    }
   

    //Backup glossary files because we've selected to backup user info
    //and files are user info's level
    function backup_glossary_files($bf,$preferences) {

        global $CFG;

        $status = true;

        //First we check to moddata exists and create it as necessary
        //in temp/backup/$backup_code  dir
        $status = check_and_create_moddata_dir($preferences->backup_unique_code);
        //Now copy the forum dir
        if ($status) {
            //Only if it exists !! Thanks to Daniel Miksik.
            if (is_dir($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/glossary")) {
                $status = backup_copy_file($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/glossary",
                                           $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/moddata/glossary");
            }
        }

        return $status;

    }

   ////Return an array of info (name,value)
   function glossary_check_backup_mods($course,$user_data=false,$backup_unique_code) {
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
?>
