<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //choice mods

    //This is the "graphical" structure of the choice mod:
    //
    //                      choice                                      
    //                    (CL,pk->id)
    //                        |
    //                        |
    //                        |
    //                   choice_answers 
    //               (UL,pk->id, fk->choice)     
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    function choice_restore_mods($bf,$preferences) {
        
        global $CFG;

        $status = true;

        //Iterate over choice table
        $choices = get_records ("choice","course",$preferences->backup_course,"id");
        if ($choices) {
            foreach ($choices as $choice) {
                //Start mod
                fwrite ($bf,start_tag("MOD",3,true));
                //Print choice data
                fwrite ($bf,full_tag("ID",4,false,$choice->id));
                fwrite ($bf,full_tag("MODTYPE",4,false,"choice"));
                fwrite ($bf,full_tag("NAME",4,false,$choice->name));
                fwrite ($bf,full_tag("TEXT",4,false,$choice->text));
                fwrite ($bf,full_tag("FORMAT",4,false,$choice->format));
                fwrite ($bf,full_tag("ANSWER1",4,false,$choice->answer1));
                fwrite ($bf,full_tag("ANSWER2",4,false,$choice->answer2));
                fwrite ($bf,full_tag("ANSWER3",4,false,$choice->answer3));
                fwrite ($bf,full_tag("ANSWER4",4,false,$choice->answer4));
                fwrite ($bf,full_tag("ANSWER5",4,false,$choice->answer5));
                fwrite ($bf,full_tag("ANSWER6",4,false,$choice->answer6));
                fwrite ($bf,full_tag("PUBLISH",4,false,$choice->publish));
                fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$choice->timemodified));
                //if we've selected to backup users info, then execute backup_choice_answers
                if ($preferences->mods["choice"]->userinfo) {
                    $status = backup_choice_answers($bf,$preferences,$choice->id);
                }
                //End mod
                $status =fwrite ($bf,end_tag("MOD",3,true));
            }
        }
        return $status;
    }

?>
