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
    //             --------------------------------------------------------------------------------------
    //             |             |              |              |                |                       |    
    //             |             |              |              |                |                       |
    //             |             |              |              |                |                       |    quiz_randomsamatch
    //      quiz_truefalse       |       quiz_multichoice      |         quiz_multianswer               |--(CL,pl->id,fk->question)
    // (CL,pl->id,fk->question)  |   (CL,pl->id,fk->question)  |    (CL,pl->id,fk->question)            |
    //             .             |              .              |               .                        |
    //             .      quiz_shortanswer      .       quiz_numerical         .                        |
    //             .  (CL,pl->id,fk->question)  .  (CL,pl->id,fk->question)    .                        |         quiz_match 
    //             .             .              .              .               .                        |--(CL,pl->id,fk->question)
    //             .             .              .              .               .                        |             .
    //             .             .              .              .               .                        |             .
    //             .             .              .              .               .                        |             .
    //             .             .              .              .               .                        |       quiz_match_sub
    //             .             .              .              .               .                        |--(CL,pl->id,fk->question)
    //             .............................................................                        |
    //                                                   .                                              |
    //                                                   .                                              |
    //                                                   .                                              |
    //                                                quiz_answers                                      |
    //                                         (CL,pk->id,fk->question)----------------------------------
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
    //     - quiz_multianswer
    //     - quiz_multichoice
    //     - quiz_numerical
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

        global $CFG;

        $status = true;

        //First, we get the used categories from backup_ids
        $categories = quiz_category_ids_by_backup ($preferences->backup_unique_code);
      
        //If we've categories
        if ($categories) {
             //Write start tag
             $status = fwrite($bf,start_tag("QUESTION_CATEGORIES",2,true));
             //Iterate over each category
            foreach ($categories as $cat) {
                //Start category
                $status =fwrite ($bf,start_tag("QUESTION_CATEGORY",3,true));
                //Get category data from quiz_categories
                $category = get_record ("quiz_categories","id",$cat->old_id);
                //Print category contents
                fwrite($bf,full_tag("ID",4,false,$category->id));
                fwrite($bf,full_tag("NAME",4,false,$category->name));
                fwrite($bf,full_tag("INFO",4,false,$category->info));
                fwrite($bf,full_tag("PUBLISH",4,false,$category->publish));
                fwrite($bf,full_tag("STAMP",4,false,$category->stamp));
                //Now, backup their questions
                $status = quiz_backup_question($bf,$preferences,$category->id);
                //End category
                $status =fwrite ($bf,end_tag("QUESTION_CATEGORY",3,true));
            }
            //Write end tag    
            $status =fwrite ($bf,end_tag("QUESTION_CATEGORIES",2,true));
        }

        return $status;
    }
    
    //This function backups all the questions in selected category and their
    //asociated data 
    function quiz_backup_question($bf,$preferences,$category) {

        global $CFG;

        $status = true;

        $questions = get_records("quiz_questions","category",$category,"id");
        //If there is questions
        if ($questions) {
            //Write start tag
            $status =fwrite ($bf,start_tag("QUESTIONS",4,true));
            $counter = 0;
            //Iterate over each question
            foreach ($questions as $question) {
                //Start question
                $status =fwrite ($bf,start_tag("QUESTION",5,true));
                //Print question contents
                fwrite ($bf,full_tag("ID",6,false,$question->id));
                fwrite ($bf,full_tag("NAME",6,false,$question->name));
                fwrite ($bf,full_tag("QUESTIONTEXT",6,false,$question->questiontext));
                fwrite ($bf,full_tag("QUESTIONTEXTFORMAT",6,false,$question->questiontextformat));
                fwrite ($bf,full_tag("IMAGE",6,false,$question->image));
                fwrite ($bf,full_tag("DEFAULTGRADE",6,false,$question->defaultgrade));
                fwrite ($bf,full_tag("QTYPE",6,false,$question->qtype));
                fwrite ($bf,full_tag("STAMP",6,false,$question->stamp));
                fwrite ($bf,full_tag("VERSION",6,false,$question->version));
                //Now, depending of the qtype, call one function or other
                if ($question->qtype == "1") {
                    $status = quiz_backup_shortanswer($bf,$preferences,$question->id);
                } else if ($question->qtype == "2") {
                    $status = quiz_backup_truefalse($bf,$preferences,$question->id);
                } else if ($question->qtype == "3") {
                    $status = quiz_backup_multichoice($bf,$preferences,$question->id);
                } else if ($question->qtype == "4") {
                    //Random question. Nothing to write.
                } else if ($question->qtype == "5") {
                    $status = quiz_backup_match($bf,$preferences,$question->id);
                } else if ($question->qtype == "6") {
                    $status = quiz_backup_randomsamatch($bf,$preferences,$question->id);
                } else if ($question->qtype == "7") {
                    //Description question. Nothing to write.
                } else if ($question->qtype == "8") {
                    $status = quiz_backup_numerical($bf,$preferences,$question->id);
                } else if ($question->qtype == "9") {
                    $status = quiz_backup_multianswer($bf,$preferences,$question->id);
                }
                //End question
                $status =fwrite ($bf,end_tag("QUESTION",5,true));
                //Do some output
                $counter++;
                if ($counter % 10 == 0) {
                    echo ".";            
                    if ($counter % 200 == 0) {
                        echo "<br>";
                    }
                    backup_flush(300);
                }
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("QUESTIONS",4,true));
        }
        return $status;
    }

    //This function backups the data in a truefalse question (qtype=2) and its
    //asociated data
    function quiz_backup_truefalse($bf,$preferences,$question) {

        global $CFG;

        $status = true;

        $truefalses = get_records("quiz_truefalse","question",$question,"id");
        //If there are truefalses
        if ($truefalses) {
            //Iterate over each truefalse
            foreach ($truefalses as $truefalse) {
                $status =fwrite ($bf,start_tag("TRUEFALSE",6,true));
                //Print truefalse contents
                fwrite ($bf,full_tag("TRUEANSWER",7,false,$truefalse->trueanswer));
                fwrite ($bf,full_tag("FALSEANSWER",7,false,$truefalse->falseanswer));
                $status =fwrite ($bf,end_tag("TRUEFALSE",6,true));
            }
            //Now print quiz_answers
            $status = quiz_backup_answers($bf,$preferences,$question);
        }
        return $status;
    }

    //This function backups the data in a shortanswer question (qtype=1) and its
    //asociated data
    function quiz_backup_shortanswer($bf,$preferences,$question,$level=6,$include_answers=true) {

        global $CFG;

        $status = true;

        $shortanswers = get_records("quiz_shortanswer","question",$question,"id");
        //If there are shortanswers
        if ($shortanswers) {
            //Iterate over each shortanswer
            foreach ($shortanswers as $shortanswer) {
                $status =fwrite ($bf,start_tag("SHORTANSWER",$level,true));
                //Print shortanswer contents
                fwrite ($bf,full_tag("ANSWERS",$level+1,false,$shortanswer->answers));
                fwrite ($bf,full_tag("USECASE",$level+1,false,$shortanswer->usecase));
                $status =fwrite ($bf,end_tag("SHORTANSWER",$level,true));
            }
            //Now print quiz_answers
            if ($include_answers) {
                $status = quiz_backup_answers($bf,$preferences,$question);
            }
        }
        return $status;
    } 

    //This function backups the data in a multichoice question (qtype=3) and its
    //asociated data
    function quiz_backup_multichoice($bf,$preferences,$question,$level=6,$include_answers=true) {

        global $CFG;

        $status = true;

        $multichoices = get_records("quiz_multichoice","question",$question,"id");
        //If there are multichoices
        if ($multichoices) {
            //Iterate over each multichoice
            foreach ($multichoices as $multichoice) {
                $status =fwrite ($bf,start_tag("MULTICHOICE",$level,true));
                //Print multichoice contents
                fwrite ($bf,full_tag("LAYOUT",$level+1,false,$multichoice->layout));
                fwrite ($bf,full_tag("ANSWERS",$level+1,false,$multichoice->answers));
                fwrite ($bf,full_tag("SINGLE",$level+1,false,$multichoice->single));
                $status =fwrite ($bf,end_tag("MULTICHOICE",$level,true));
            }
            //Now print quiz_answers
            if ($include_answers) {
                $status = quiz_backup_answers($bf,$preferences,$question);
            }
        }
        return $status;
    }

    //This function backups the data in a randomsamatch question (qtype=6) and its
    //asociated data
    function quiz_backup_randomsamatch($bf,$preferences,$question) {

        global $CFG;

        $status = true;

        $randomsamatchs = get_records("quiz_randomsamatch","question",$question,"id");
        //If there are randomsamatchs
        if ($randomsamatchs) {
            //Iterate over each randomsamatch
            foreach ($randomsamatchs as $randomsamatch) {
                $status =fwrite ($bf,start_tag("RANDOMSAMATCH",6,true));
                //Print randomsamatch contents
                fwrite ($bf,full_tag("CHOOSE",7,false,$randomsamatch->choose));
                $status =fwrite ($bf,end_tag("RANDOMSAMATCH",6,true));
            }
        }
        return $status;
    }

    //This function backups the data in a match question (qtype=5) and its
    //asociated data
    function quiz_backup_match($bf,$preferences,$question) {

        global $CFG;

        $status = true;

        $matchs = get_records("quiz_match_sub","question",$question,"id");
        //If there are matchs
        if ($matchs) {
            $status =fwrite ($bf,start_tag("MATCHS",6,true));
            //Iterate over each match
            foreach ($matchs as $match) {
                $status =fwrite ($bf,start_tag("MATCH",7,true));
                //Print match contents
                fwrite ($bf,full_tag("ID",8,false,$match->id));
                fwrite ($bf,full_tag("QUESTIONTEXT",8,false,$match->questiontext));
                fwrite ($bf,full_tag("ANSWERTEXT",8,false,$match->answertext));
                $status =fwrite ($bf,end_tag("MATCH",7,true));
            }
            $status =fwrite ($bf,end_tag("MATCHS",6,true));
        }
        return $status;
    }

    //This function backups the data in a numerical question (qtype=8) and its
    //asociated data
    function quiz_backup_numerical($bf,$preferences,$question,$level=6,$include_answers=true) {

        global $CFG;

        $status = true;

        $numericals = get_records("quiz_numerical","question",$question,"id");
        //If there are numericals
        if ($numericals) {
            //Iterate over each numerical
            foreach ($numericals as $numerical) {
                $status =fwrite ($bf,start_tag("NUMERICAL",$level,true));
                //Print numerical contents
                fwrite ($bf,full_tag("ANSWER",$level+1,false,$numerical->answer));
                fwrite ($bf,full_tag("MIN",$level+1,false,$numerical->min));
                fwrite ($bf,full_tag("MAX",$level+1,false,$numerical->max));
                $status =fwrite ($bf,end_tag("NUMERICAL",$level,true));
            }
            //Now print quiz_answers
            if ($include_answers) {
                $status = quiz_backup_answers($bf,$preferences,$question);
            }
        }
        return $status;
    }

    //This function backups the data in a multianswer question (qtype=9) and its      
    //asociated data
    function quiz_backup_multianswer($bf,$preferences,$question) {

        global $CFG;

        $status = true;

        $multianswers = get_records("quiz_multianswers","question",$question,"id");
        //If there are multianswers
        if ($multianswers) {
            //Print multianswers header
            $status =fwrite ($bf,start_tag("MULTIANSWERS",6,true));
            //Iterate over each multianswer
            foreach ($multianswers as $multianswer) {
                $status =fwrite ($bf,start_tag("MULTIANSWER",7,true));
                //Print multianswer contents
                fwrite ($bf,full_tag("ID",8,false,$multianswer->id));
                fwrite ($bf,full_tag("ANSWERS",8,false,$multianswer->answers));
                fwrite ($bf,full_tag("POSITIONKEY",8,false,$multianswer->positionkey));
                fwrite ($bf,full_tag("ANSWERTYPE",8,false,$multianswer->answertype));
                fwrite ($bf,full_tag("NORM",8,false,$multianswer->norm));
                //Depending of the ANSWERTYPE, we must encode different info
                //to be able to re-create records in quiz_shortanswer, quiz_multichoice and
                //quiz_numerical
                if ($multianswer->answertype == "1") {
                    $status = quiz_backup_shortanswer($bf,$preferences,$question,8,false);
                } else if ($multianswer->answertype == "3") {
                    $status = quiz_backup_multichoice($bf,$preferences,$question,8,false);
                } else if ($multianswer->answertype == "8") {
                    $status = quiz_backup_numerical($bf,$preferences,$question,8,false);
                }

                $status =fwrite ($bf,end_tag("MULTIANSWER",7,true));
            }
            //Print multianswers footer
            $status =fwrite ($bf,end_tag("MULTIANSWERS",6,true));
            //Now print quiz_answers
            $status = quiz_backup_answers($bf,$preferences,$question);
        }
        return $status;
    }

    //This function backups the answers data in some question types
    //(truefalse, shortanswer,multichoice,numerical)
    function quiz_backup_answers($bf,$preferences,$question) {

        global $CFG;

        $status = true;

        $answers = get_records("quiz_answers","question",$question,"id");
        //If there are answers
        if ($answers) {
            $status =fwrite ($bf,start_tag("ANSWERS",6,true));
            //Iterate over each answer
            foreach ($answers as $answer) {
                $status =fwrite ($bf,start_tag("ANSWER",7,true));
                //Print answer contents
                fwrite ($bf,full_tag("ID",8,false,$answer->id));
                fwrite ($bf,full_tag("ANSWER_TEXT",8,false,$answer->answer));
                fwrite ($bf,full_tag("FRACTION",8,false,$answer->fraction));
                fwrite ($bf,full_tag("FEEDBACK",8,false,$answer->feedback));
                $status =fwrite ($bf,end_tag("ANSWER",7,true));
            }
            $status =fwrite ($bf,end_tag("ANSWERS",6,true));
        }
        return $status;
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
                fwrite ($bf,full_tag("ATTEMPTS_NUMBER",4,false,$quiz->attempts));
                fwrite ($bf,full_tag("ATTEMPTONLAST",4,false,$quiz->attemptonlast));
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
                fwrite ($bf,full_tag("GRADEVAL",6,false,$gra->grade));
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
                fwrite ($bf,full_tag("ATTEMPTNUM",6,false,$attempt->attempt));
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
            $info[3][0] = get_string("grades"); 
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
                                 WHERE a.backup_code = '$backup_unique_code' AND
                                       a.table_name = 'quiz_categories'");
    }

    function quiz_question_ids_by_backup ($backup_unique_code) {

        global $CFG;

        return get_records_sql ("SELECT q.id, q.category
                                 FROM {$CFG->prefix}backup_ids a,
                                      {$CFG->prefix}quiz_questions q
                                 WHERE a.backup_code = '$backup_unique_code' AND
                                       q.category = a.old_id AND 
                                       a.table_name = 'quiz_categories'");
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
