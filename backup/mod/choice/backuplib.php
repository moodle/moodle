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

    function choice_backup_mods($course,$user_data=false,$backup_unique_code) {
        print "hola";
    }
   
   ////Return an array of info (name,value)
   function NO_choice_check_backup_mods($course,$user_data=false,$backup_unique_code) {
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
