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
    //        quiz_attempts          quiz_grades         quiz_question_grades                    |
    //   (UL,pk->id, fk->quiz)   (UL,pk->id,fk->quiz)    (CL,pk->id,fk->quiz)                    |
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

    //THIS MOD BACKUP NEEDS TO USE THE mdl_backup_ids TABLE

    //This module is special, because we make the backup in two steps:
    // 1.-We backup every category and their questions (complete structure). It includes this tables:
    //     - quiz_categories
    //     - quiz_questions
    //     - quiz_truefalse
    //     - quiz_shortanswer
    //     - quiz_multichoice
    //     - quiz_randomsamatch
    //     - quiz_match
    //     - quiz_match_sub
    //     - quiz_answers
    //    All this backup info have its own section in moodle.xml (QUESTION_CATEGORIES) and it's generated
    //    before every module backup standard invocation. And only if to backup quizzes has been selected !!
    //    It's invoked with quiz_backup_question_categories. (course independent).

    // 2.-Standard module backup (Invoked via quiz_backup_mods). It includes this tables:
    //     - quiz
    //     - quiz_question_grades
    //     - quiz_attempts
    //     - quiz_grades
    //     - quiz_responses
    //    This step is the standard mod backup. (course dependent).

    //STEP 1. Backup categories/questions and associated structures
    //    (course independent)
    function quiz_backup_question_categories($bf,$preferences) {

    }

    //STEP 2. Backup quizzes and associated structures
    //    (course dependent)
    function quiz_backup_mods($bf,$preferences) {

        global $CFG;

        $status = true;

        //Iterate over quiz table
        $quizzes = get_records ("quiz","course",$preferences->backup_course,"id");
        if ($quizzes) {
            foreach ($quizzes as $quiz) {
                //Start mod
                fwrite ($bf,start_tag("MOD",3,true));
                //Print quiz data
                fwrite ($bf,full_tag("ID",4,false,$quiz->id));
                fwrite ($bf,full_tag("MODTYPE",4,false,"quiz"));
                fwrite ($bf,full_tag("NAME",4,false,$quiz->name));
                fwrite ($bf,full_tag("INTRO",4,false,$quiz->intro));
                fwrite ($bf,full_tag("TIMEOPEN",4,false,$quiz->timeopen));
                fwrite ($bf,full_tag("TIMECLOSE",4,false,$quiz->timeclose));
                fwrite ($bf,full_tag("ATTEMPTS",4,false,$quiz->attempts));
                fwrite ($bf,full_tag("FEEDBACK",4,false,$quiz->feedback));
                fwrite ($bf,full_tag("CORRECTANSWERS",4,false,$quiz->correctanswers));
                fwrite ($bf,full_tag("GRADEMETHOD",4,false,$quiz->grademethod));
                fwrite ($bf,full_tag("REVIEW",4,false,$quiz->review));
                fwrite ($bf,full_tag("SHUFFLEQUESTIONS",4,false,$quiz->shufflequestions));
                fwrite ($bf,full_tag("SHUFFLEANSWERS",4,false,$quiz->shuffleanswers));
                fwrite ($bf,full_tag("QUESTIONS",4,false,$quiz->questions));
                fwrite ($bf,full_tag("SUMGRADES",4,false,$quiz->sumgrades));
                fwrite ($bf,full_tag("GRADE",4,false,$quiz->grade));
                fwrite ($bf,full_tag("TIMECREATED",4,false,$quiz->timecreated));
                fwrite ($bf,full_tag("TIMEMODIFIED",4,false,$quiz->timemodified));
                //Now we print to xml question_grades (Course Level)
                $status = backup_quiz_question_grades($bf,$preferences,$quiz->id);
                //if we've selected to backup users info, then execute:
                //    - backup_quiz_grades
                //    - backup_quiz_attempts
                if ($preferences->mods["quiz"]->userinfo and $status) {
                    $status = backup_quiz_grades($bf,$preferences,$quiz->id);
                    if ($status) {
                        $status = backup_quiz_attempts($bf,$preferences,$quiz->id);
                    }
                }
                //End mod
                $status =fwrite ($bf,end_tag("MOD",3,true));
            }
        }
        return $status;
    }

    //Backup quiz_question_grades contents (executed from quiz_backup_mods)
    function backup_quiz_question_grades ($bf,$preferences,$quiz) {

        global $CFG;

        $status = true;

        $quiz_question_grades = get_records("quiz_question_grades","quiz",$quiz,"id");
        //If there are question_grades
        if ($quiz_question_grades) {
            //Write start tag
            $status =fwrite ($bf,start_tag("QUESTION_GRADES",4,true));
            //Iterate over each question_grade
            foreach ($quiz_question_grades as $que_gra) {
                //Start question grade
                $status =fwrite ($bf,start_tag("QUESTION_GRADE",5,true));
                //Print question_grade contents
                fwrite ($bf,full_tag("ID",6,false,$que_gra->id));
                fwrite ($bf,full_tag("QUESTION",6,false,$que_gra->question));
                fwrite ($bf,full_tag("GRADE",6,false,$que_gra->grade));
                //End question grade
                $status =fwrite ($bf,end_tag("QUESTION_GRADE",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("QUESTION_GRADES",4,true));
        }
        return $status;
    }

    //Backup quiz_grades contents (executed from quiz_backup_mods)
    function backup_quiz_grades ($bf,$preferences,$quiz) {

        global $CFG;

        $status = true;

        $quiz_grades = get_records("quiz_grades","quiz",$quiz,"id");
        //If there are grades
        if ($quiz_grades) {
            //Write start tag
            $status =fwrite ($bf,start_tag("GRADES",4,true));
            //Iterate over each grade
            foreach ($quiz_grades as $gra) {
                //Start grade
                $status =fwrite ($bf,start_tag("GRADE",5,true));
                //Print grade contents
                fwrite ($bf,full_tag("ID",6,false,$gra->id));
                fwrite ($bf,full_tag("USERID",6,false,$gra->userid));
                fwrite ($bf,full_tag("GRADE",6,false,$gra->grade));
                fwrite ($bf,full_tag("TIMEMODIFIED",6,false,$gra->timemodified));
                //End question grade
                $status =fwrite ($bf,end_tag("GRADE",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("GRADES",4,true));
        }
        return $status;
    }

    //Backup quiz_attempts contents (executed from quiz_backup_mods)
    function backup_quiz_attempts ($bf,$preferences,$quiz) {

        global $CFG;

        $status = true;

        $quiz_attempts = get_records("quiz_attempts","quiz",$quiz,"id");
        //If there are attempts
        if ($quiz_attempts) {
            //Write start tag
            $status =fwrite ($bf,start_tag("ATTEMPTS",4,true));
            //Iterate over each attempt
            foreach ($quiz_attempts as $attempt) {
                //Start attempt
                $status =fwrite ($bf,start_tag("ATTEMPT",5,true));
                //Print attempt contents
                fwrite ($bf,full_tag("ID",6,false,$attempt->id));
                fwrite ($bf,full_tag("USERID",6,false,$attempt->userid));
                fwrite ($bf,full_tag("ATTEMPT",6,false,$attempt->attempt));
                fwrite ($bf,full_tag("SUMGRADES",6,false,$attempt->sumgrades));
                fwrite ($bf,full_tag("TIMESTART",6,false,$attempt->timestart));
                fwrite ($bf,full_tag("TIMEFINISH",6,false,$attempt->timefinish));
                fwrite ($bf,full_tag("TIMEMODIFIED",6,false,$attempt->timemodified));
                //Now write to xml the responses (in this attempt)
                $status = backup_quiz_responses ($bf,$preferences,$attempt->id);
                //End attempt
                $status =fwrite ($bf,end_tag("ATTEMPT",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("ATTEMPTS",4,true));
        }
        return $status;
    }

    //Backup quiz_responses contents (executed from backup_quiz_attempts)
    function backup_quiz_responses ($bf,$preferences,$attempt) {

        global $CFG;

        $status = true;

        $quiz_responses = get_records("quiz_responses","attempt",$attempt,"id");
        //If there are responses
        if ($quiz_responses) {
            //Write start tag
            $status =fwrite ($bf,start_tag("RESPONSES",6,true));
            //Iterate over each response
            foreach ($quiz_responses as $response) {
                //Start response
                $status =fwrite ($bf,start_tag("RESPONSE",7,true));
                //Print response contents
                fwrite ($bf,full_tag("ID",8,false,$response->id));
                fwrite ($bf,full_tag("QUESTION",8,false,$response->question));
                fwrite ($bf,full_tag("ANSWER",8,false,$response->answer));
                fwrite ($bf,full_tag("GRADE",8,false,$response->grade));
                //End response
                $status =fwrite ($bf,end_tag("RESPONSE",7,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("RESPONSES",6,true));
        }
        return $status;
    }






   ////Return an array of info (name,value)
   function quiz_check_backup_mods($course,$user_data=false,$backup_unique_code) {
        //Deletes data from mdl_backup_ids (categories section)
        delete_category_ids ($backup_unique_code);
        //Create date into mdl_backup_ids (categories section)
        insert_category_ids ($course,$backup_unique_code);
        //First the course data
        $info[0][0] = get_string("modulenameplural","quiz");
        if ($ids = quiz_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }
        //Categories
        $info[1][0] = get_string("categories","quiz");
        if ($ids = quiz_category_ids_by_backup ($backup_unique_code)) {
            $info[1][1] = count($ids);
        } else {
            $info[1][1] = 0;
        }
        //Questions
        $info[2][0] = get_string("questions","quiz");
        if ($ids = quiz_question_ids_by_backup ($backup_unique_code)) {
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
    function quiz_category_ids_by_backup ($backup_unique_code) {

        global $CFG;

        return get_records_sql ("SELECT a.old_id, a.backup_code
                                 FROM {$CFG->prefix}backup_ids a
                                 WHERE a.backup_code = '$backup_unique_code'");
    }

    function quiz_question_ids_by_backup ($backup_unique_code) {

        global $CFG;

        return get_records_sql ("SELECT q.id, q.category
                                 FROM {$CFG->prefix}backup_ids a,
                                      {$CFG->prefix}quiz_questions q
                                 WHERE a.backup_code = '$backup_unique_code' and
                                       q.category = a.old_id");
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
