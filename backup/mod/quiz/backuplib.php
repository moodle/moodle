<?PHP //$Id$
    //This php script contains all the stuff to backup/restore
    //quiz mods

   //To see, put your terminal to 132cc

    //This is the "graphical" structure of the quiz mod:
    //
    //                           quiz                                                      quiz_categories
    //                        (CL,pk->id)                                                   (CL,pk->id)
    //                            |                                                              |
    //             -----------------------------------------------                               |
    //             |                        |                    |                               |
    //             |                        |                    |                               |
    //             |                        |                    |                               |
    //        quiz_attemps           quiz_grades         quiz_question_grades                    |
    //   (UL,pk->id, fk->quiz)   (UL,pk->id,fk->quiz)    (UL,pk->id,fk->quiz)                    |
    //             |                                              |                              |
    //             |                                              |                              |
    //             |                                              |                              |
    //       quiz_responses                                       |                        quiz_questions     
    //  (UL,pk->id, fk->attempt)----------------------------------------------------(CL,pk->id,fk->category,files)
    //                                                                                           |
    //                                                                                           |
    //                                                                                           |
    //             ------------------------------------------------------------------------------------------------------
    //             |                         |                        |                |    
    //             |                         |                        |                |
    //             |                         |                        |                |           quiz_randomsamatch
    //       quiz_truefalse           quiz_shortanswer         quiz_multichoice        |---------(CL,pl->id,fk->question)
    //  (CL,pl->id,fk->question)  (CL,pl->id,fk->question)  (CL,pl->id,fk->question)   |
    //             .                         .                        .                |
    //             .                         .                        .                |
    //             .                         .                        .                |               quiz_match
    //             ....................................................                |---------(CL,pl->id,fk->question)
    //                                       .                                         |                    .
    //                                       .                                         |                    .
    //                                       .                                         |                    .
    //                                    quiz_answers                                 |              quiz_match_sub
    //                             (CL,pk->id,fk->question)----------------------------|---------(CL,pl->id,fk->question) 
    // 
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files
    //
    //-----------------------------------------------------------

    function quiz_backup_mods() {
        print "hola";
    }

   ////Return an array of info (name,value)
   function quiz_check_backup_mods($course,$user_data=false) {
        //First the course data
        $info[0][0] = get_string("modulenameplural","quiz");
        if ($ids = quiz_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }
        //Categories
        $info[1][0] = get_string("categories","quiz");
        if ($ids = quiz_category_ids_by_course ($course)) {
            $info[1][1] = count($ids);
        } else {
            $info[1][1] = 0;
        }
        //Questions
        $info[2][0] = get_string("questions","quiz");
        if ($ids = quiz_question_ids_by_course ($course)) {
            $info[2][1] = count($ids);
        } else {
            $info[2][1] = 0;
        }

        //Now, if requested, the user_data
        if ($user_data) {
            //Grades 
            $info[3][0] = get_string("grades","quiz"); 
            if ($ids = quiz_grade_ids_by_course ($course)) { 
                $info[3][1] = count($ids);
            } else {
                $info[3][1] = 0;
            }
        }

        return $info;
    }






    // INTERNAL FUNCTIONS. BASED IN THE MOD STRUCTURE

    //Returns an array of quiz id
    function quiz_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT a.id, a.course
                                 FROM {$CFG->prefix}quiz a
                                 WHERE a.course = '$course'");
    }

    //Returns an array of categories id
    function quiz_category_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT a.id, a.course
                                 FROM {$CFG->prefix}quiz_categories a
                                 WHERE a.course = '$course'");
    }

    function quiz_question_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT q.id, q.category
                                 FROM {$CFG->prefix}quiz_categories a,
                                      {$CFG->prefix}quiz_questions q
                                 WHERE a.course = '$course' and
                                       q.category = a.id");
    }

    function quiz_grade_ids_by_course ($course) {

        global $CFG;

        return get_records_sql ("SELECT g.id, g.quiz 
                                 FROM {$CFG->prefix}quiz a,
                                      {$CFG->prefix}quiz_grades g
                                 WHERE a.course = '$course' and
                                       g.quiz = a.id");
    }

?>
