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

    function choice_backup_mods($bf,$preferences) {
        
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
                fwrite ($bf,full_tag("SHOWUNANSWERED",4,false,$choice->showunanswered));
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

    //Backup choice_answers contents (executed from choice_backup_mods)
    function backup_choice_answers ($bf,$preferences,$choice) {

        global $CFG;

        $status = true;

        $choice_answers = get_records("choice_answers","choice",$choice,"id");
        //If there is submissions
        if ($choice_answers) {
            //Write start tag
            $status =fwrite ($bf,start_tag("ANSWERS",4,true));
            //Iterate over each answer
            foreach ($choice_answers as $cho_ans) {
                //Start answer
                $status =fwrite ($bf,start_tag("ANSWER",5,true));
                //Print submission contents
                fwrite ($bf,full_tag("ID",6,false,$cho_ans->id));
                fwrite ($bf,full_tag("USERID",6,false,$cho_ans->userid));
                fwrite ($bf,full_tag("CHOICE_ANSWER",6,false,$cho_ans->answer));
                fwrite ($bf,full_tag("TIMEMODIFIED",6,false,$cho_ans->timemodified));
                //End answer
                $status =fwrite ($bf,end_tag("ANSWER",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("ANSWERS",4,true));
        }
        return $status;
    }
   
   ////Return an array of info (name,value)
   function choice_check_backup_mods($course,$user_data=false,$backup_unique_code) {
        //First the course data
        $info[0][0] = get_string("modulenameplural","choice");
        if ($ids = choice_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }

        //Now, if requested, the user_data
        if ($user_data) {
            $info[1][0] = get_string("responses","choice");
            if ($ids = choice_answer_ids_by_course ($course)) {
                $info[1][1] = count($ids);
            } else {
                $info[1][1] = 0;
            }
        }
        return $info;
    }






    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of choices id
    function choice_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT a.id, a.course
                                 FROM {$CFG->prefix}choice a
                                 WHERE a.course = '$course'");
    }
   
    //Returns an array of choice_answers id
    function choice_answer_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.choice
                                 FROM {$CFG->prefix}choice_answers s,
                                      {$CFG->prefix}choice a
                                 WHERE a.course = '$course' AND
                                       s.choice = a.id");
    }
?>
