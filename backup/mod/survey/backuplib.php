<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //survey mods

    //This is the "graphical" structure of the survey mod:
    //                                                    --------------------
    //                           survey                   | survey_questions |
    //                        (CL,pk->id)                 |(CL,pk->id,?????) |
    //                            |                       --------------------
    //                            |
    //             -----------------------------------        
    //             |                                 |
    //        survey_analysis                   survey_answers
    //    (UL,pk->id, fk->survey)           (UL,pk->id, fk->survey)
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

    function survey_backup_mods() {
        print "hola";
    }

   ////Return an array of info (name,value)
   function NO_survey_check_backup_mods($course,$user_data=false,$backup_unique_code) {
        //First the course data
        $info[0][0] = get_string("modulenameplural","survey");
        if ($ids = survey_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }

        //Now, if requested, the user_data
        if ($user_data) {
            //Subscriptions
            $info[1][0] = get_string("answers","survey");
            if ($ids = survey_answer_ids_by_course ($course)) {
                $info[1][1] = count($ids);
            } else {
                $info[1][1] = 0;
            }
        }
        return $info;
    }






    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of surveys id
    function survey_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT a.id, a.course
                                 FROM {$CFG->prefix}survey a
                                 WHERE a.course = '$course'");
    }

    //Returns an array of survey answer id
    function survey_answer_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT s.id , s.survey
                                 FROM {$CFG->prefix}survey_answers s,
                                      {$CFG->prefix}forum a
                                 WHERE a.course = '$course' AND
                                       s.survey = a.id");
    }

?>
