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

    //This module is special, because we make the restore in two steps:
    // 1.-We restore every category and their questions (complete structure). It includes this tables:
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
    //    before every module backup standard invocation. And only if to restore quizzes has been selected !!
    //    It's invoked with quiz_restore_question_categories. (course independent).

    // 2.-Standard module restore (Invoked via quiz_restore_mods). It includes this tables:
    //     - quiz
    //     - quiz_question_grades
    //     - quiz_attempts
    //     - quiz_grades
    //     - quiz_responses
    //    This step is the standard mod backup. (course dependent).

    //STEP 1. Restore categories/questions and associated structures
    //    (course independent)
    function quiz_restore_question_categories($category,$restore) {

        global $CFG;

        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,"quiz_categories",$category->id);
   
        if ($data) {
            //Now get completed xmlized object
            $info = $data->info;
            //traverse_xmlize($info);                                                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the QUIZ_CATEGORIES record structure
            $quiz_cat->course = $restore->course_id;
            $quiz_cat->name = backup_todb($info['QUESTION_CATEGORY']['#']['NAME']['0']['#']);
            $quiz_cat->info = backup_todb($info['QUESTION_CATEGORY']['#']['INFO']['0']['#']);
            $quiz_cat->publish = backup_todb($info['QUESTION_CATEGORY']['#']['PUBLISH']['0']['#']);

            //Now search it that category exists !!
            $cat = get_record("quiz_categories","name",$quiz_cat->name);
            //Create it if doesn't exists
            if (!$cat) {
                //The structure is equal to the db, so insert the quiz_categories
                $newid = insert_record ("quiz_categories",$quiz_cat);
            } else {
                //Exists, so get its id
                $newid = $cat->id;
            }

            //Do some output
            echo "<ul><li>Category \"".$quiz_cat->name."\"<br>";
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"quiz_categories",
                             $category->id, $newid);
                //Now restore quiz_questions
                $status = quiz_restore_questions ($category->id, $newid,$info,$restore);
            } else {
                $status = false;
            }
            

            //Finalize ul
            echo "</ul>";
        }

        return $status;
    }

    function quiz_restore_questions ($old_category_id,$new_category_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the questions array
        $questions = $info['QUESTION_CATEGORY']['#']['QUESTIONS']['0']['#']['QUESTION'];

        //Iterate over questions
        for($i = 0; $i < sizeof($questions); $i++) {
            $que_info = $questions[$i];
            //traverse_xmlize($que_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($que_info['#']['ID']['0']['#']);

            //Now, build the QUIZ_QUESTIONS record structure
            $question->category = $new_category_id;
            $question->name = backup_todb($que_info['#']['NAME']['0']['#']);
            $question->questiontext = backup_todb($que_info['#']['QUESTIONTEXT']['0']['#']);
            $question->image = backup_todb($que_info['#']['IMAGE']['0']['#']);
            $question->defaultgrade = backup_todb($que_info['#']['DEFAULTGRADE']['0']['#']);
            $question->qtype = backup_todb($que_info['#']['QTYPE']['0']['#']);

            //The structure is equal to the db, so insert the quiz_questions
            $newid = insert_record ("quiz_questions",$question);

            //Do some output
            if (($i+1) % 2 == 0) {
                echo ".";
                if (($i+1) % 40 == 0) {
                    echo "<br>";
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"quiz_questions",$oldid,
                             $newid);
                //Now, restore every quiz_answers in this question
                $status = quiz_restore_answers($oldid,$newid,$que_info,$restore);
                //Now, depending of the type of questions, invoke different functions
                if ($question->qtype == "1") {
                    $status = quiz_restore_shortanswer($oldid,$newid,$que_info,$restore);
                } else if ($question->qtype == "2") {
                    $status = quiz_restore_truefalse($oldid,$newid,$que_info,$restore);
                } else if ($question->qtype == "3") {
                    $status = quiz_restore_multichoice($oldid,$newid,$que_info,$restore);
                } else if ($question->qtype == "4") {
                    //Random question. Nothing to do.
                } else if ($question->qtype == "5") {
                    $status = quiz_restore_match($oldid,$newid,$que_info,$restore);
                } else if ($question->qtype == "6") {
                    $status = quiz_restore_randomsamatch($oldid,$newid,$que_info,$restore);
                }
            } else {
                $status = false;
            }
        }

        return $status;
    }

    function quiz_restore_answers ($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the answers array
        $answers = $info['#']['ANSWERS']['0']['#']['ANSWER'];

        //Iterate over answers
        for($i = 0; $i < sizeof($answers); $i++) {
            $ans_info = $answers[$i];
            //traverse_xmlize($ans_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($ans_info['#']['ID']['0']['#']);

            //Now, build the QUIZ_ANSWERS record structure
            $answer->question = $new_question_id;
            $answer->answer = backup_todb($ans_info['#']['ANSWER_TEXT']['0']['#']);
            $answer->fraction = backup_todb($ans_info['#']['FRACTION']['0']['#']);
            $answer->feedback = backup_todb($ans_info['#']['FEEDBACK']['0']['#']);

            //The structure is equal to the db, so insert the quiz_answers
            $newid = insert_record ("quiz_answers",$answer);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br>";
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"quiz_answers",$oldid,
                             $newid);
            } else {
                $status = false;
            }
        }

        return $status;
    }
   
    function quiz_restore_shortanswer ($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;          

        $status = true;

        //Get the shortanswers array 
        $shortanswers = $info['#']['SHORTANSWER'];

        //Iterate over shortanswers
        for($i = 0; $i < sizeof($shortanswers); $i++) { 
            $sho_info = $shortanswers[$i];
            //traverse_xmlize($sho_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug
 
            //Now, build the QUIZ_SHORTANSWER record structure
            $shortanswer->question = $new_question_id;
            $shortanswer->answers = backup_todb($sho_info['#']['ANSWERS']['0']['#']);
            $shortanswer->usecase = backup_todb($sho_info['#']['USECASE']['0']['#']);

            //We have to recode the answers field (a list of answers id)
            //Extracts answer id from sequence
            $answers_field = "";
            $in_first = true;
            $tok = strtok($shortanswer->answers,",");
            while ($tok) {
                //Get the answer from backup_ids
                $answer = backup_getid($restore->backup_unique_code,"quiz_answers",$tok);
                if ($answer) {
                    if ($in_first) {
                        $answers_field .= $answer->new_id;
                        $in_first = false;
                    } else {
                        $answers_field .= ",".$answer->new_id;
                    }
                }
                //check for next
                $tok = strtok(",");
            }
            //We have the answers field recoded to its new ids
            $shortanswer->answers = $answers_field;

            //The structure is equal to the db, so insert the quiz_shortanswer
            $newid = insert_record ("quiz_shortanswer",$shortanswer);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br>";
                }
                backup_flush(300);
            }

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }
 
    function quiz_restore_truefalse ($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the truefalse array
        $truefalses = $info['#']['TRUEFALSE'];

        //Iterate over truefalse
        for($i = 0; $i < sizeof($truefalses); $i++) {
            $tru_info = $truefalses[$i];
            //traverse_xmlize($tru_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the QUIZ_TRUEFALSE record structure
            $truefalse->question = $new_question_id;
            $truefalse->trueanswer = backup_todb($tru_info['#']['TRUEANSWER']['0']['#']);
            $truefalse->falseanswer = backup_todb($tru_info['#']['FALSEANSWER']['0']['#']);

            ////We have to recode the trueanswer field
            $answer = backup_getid($restore->backup_unique_code,"quiz_answers",$truefalse->trueanswer);
            if ($answer) {
                $truefalse->trueanswer = $answer->new_id;
            }

            ////We have to recode the falseanswer field
            $answer = backup_getid($restore->backup_unique_code,"quiz_answers",$truefalse->falseanswer);
            if ($answer) {
                $truefalse->falseanswer = $answer->new_id;
            }

            //The structure is equal to the db, so insert the quiz_truefalse
            $newid = insert_record ("quiz_truefalse",$truefalse);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br>";
                }
                backup_flush(300);
            }

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }

    function quiz_restore_multichoice ($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the multichoices array
        $multichoices = $info['#']['MULTICHOICE'];

        //Iterate over multichoices
        for($i = 0; $i < sizeof($multichoices); $i++) {
            $mul_info = $multichoices[$i];
            //traverse_xmlize($mul_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the QUIZ_MULTICHOICE record structure
            $multichoice->question = $new_question_id;
            $multichoice->layout = backup_todb($mul_info['#']['LAYOUT']['0']['#']);
            $multichoice->answers = backup_todb($mul_info['#']['ANSWERS']['0']['#']);
            $multichoice->single = backup_todb($mul_info['#']['SINGLE']['0']['#']);

            //We have to recode the answers field (a list of answers id)
            //Extracts answer id from sequence
            $answers_field = "";
            $in_first = true;
            $tok = strtok($multichoice->answers,",");
            while ($tok) {
                //Get the answer from backup_ids
                $answer = backup_getid($restore->backup_unique_code,"quiz_answers",$tok);
                if ($answer) {
                    if ($in_first) {
                        $answers_field .= $answer->new_id;
                        $in_first = false;
                    } else {
                        $answers_field .= ",".$answer->new_id;
                    }
                }
                //check for next
                $tok = strtok(",");
            }
            //We have the answers field recoded to its new ids
            $multichoice->answers = $answers_field;

            //The structure is equal to the db, so insert the quiz_shortanswer
            $newid = insert_record ("quiz_multichoice",$multichoice);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br>";
                }
                backup_flush(300);
            }

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }

    function quiz_restore_match ($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the matchs array
        $matchs = $info['#']['MATCHS']['0']['#']['MATCH'];

        //We have to build the subquestions field (a list of match_sub id)
        $subquestions_field = "";
        $in_first = true;

        //Iterate over matchs
        for($i = 0; $i < sizeof($matchs); $i++) {
            $mat_info = $matchs[$i];
            //traverse_xmlize($mat_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the QUIZ_MATCH_SUB record structure
            $match_sub->question = $new_question_id;
            $match_sub->questiontext = backup_todb($mat_info['#']['QUESTIONTEXT']['0']['#']);
            $match_sub->answertext = backup_todb($mat_info['#']['ANSWERTEXT']['0']['#']);

            //The structure is equal to the db, so insert the quiz_match_sub
            $newid = insert_record ("quiz_match_sub",$match_sub);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br>";
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have a new match_sub, append it to subquestions_field
                if ($in_first) {
                    $subquestions_field .= $newid;
                    $in_first = false;
                } else {
                    $subquestions_field .= ",".$newid;
                }
            } else {
                $status = false;
            }
        }

        //We have created every match_sub, now create the match
        $match->question = $new_question_id;
        $match->subquestions = $subquestions_field;

        //The structure is equal to the db, so insert the quiz_match_sub  
        $newid = insert_record ("quiz_match",$match);

        if (!$newid) {
            $status = false;
        }

        return $status;
    }

    function quiz_restore_randomsamatch ($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;      

        //Get the randomsamatchs array
        $randomsamatchs = $info['#']['RANDOMSAMATCH'];

        //Iterate over randomsamatchs
        for($i = 0; $i < sizeof($randomsamatchs); $i++) {
            $ran_info = $randomsamatchs[$i];
            //traverse_xmlize($ran_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the QUIZ_RANDOMSAMATCH record structure
            $randomsamatch->question = $new_question_id;
            $randomsamatch->choose = backup_todb($mul_info['#']['CHOOSE']['0']['#']);

            //The structure is equal to the db, so insert the quiz_randomsamatch
            $newid = insert_record ("quiz_randomsamatch",$randomsamatch);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br>";
                }
                backup_flush(300);
            }

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }




    //STEP 2. Restore quizzes and associated structures
    //    (course dependent)
    function quiz_restore_mods($mod,$restore) {

        global $CFG;

        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

        if ($data) {
            //Now get completed xmlized object
            $info = $data->info;
            //traverse_xmlize($info);                                                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the QUIZ record structure
            $quiz->course = $restore->course_id;
            $quiz->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $quiz->intro = backup_todb($info['MOD']['#']['INTRO']['0']['#']);
            $quiz->timeopen = backup_todb($info['MOD']['#']['TIMEOPEN']['0']['#']);
            $quiz->timeclose = backup_todb($info['MOD']['#']['TIMECLOSE']['0']['#']);
            $quiz->attempts = backup_todb($info['MOD']['#']['ATTEMPTS_NUMBER']['0']['#']);
            $quiz->feedback = backup_todb($info['MOD']['#']['FEEDBACK']['0']['#']);
            $quiz->correctanswers = backup_todb($info['MOD']['#']['CORRECTANSWERS']['0']['#']);
            $quiz->grademethod = backup_todb($info['MOD']['#']['GRADEMETHOD']['0']['#']);
            $quiz->review = backup_todb($info['MOD']['#']['REVIEW']['0']['#']);
            $quiz->shufflequestions = backup_todb($info['MOD']['#']['SHUFFLEQUESTIONS']['0']['#']);
            $quiz->shuffleanswers = backup_todb($info['MOD']['#']['SHUFFLEANSWERS']['0']['#']);
            $quiz->questions = backup_todb($info['MOD']['#']['QUESTIONS']['0']['#']);
            $quiz->sumgrades = backup_todb($info['MOD']['#']['SUMGRADES']['0']['#']);
            $quiz->grade = backup_todb($info['MOD']['#']['GRADE']['0']['#']);
            $quiz->timecreated = backup_todb($info['MOD']['#']['TIMECREATED']['0']['#']);
            $quiz->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);

            //We have to recode the questions field (a list of questions id)          
            //Extracts question id from sequence
            $questions_field = "";
            $in_first = true;
            $tok = strtok($quiz->questions,",");
            while ($tok) {
                //Get the question from backup_ids
                $question = backup_getid($restore->backup_unique_code,"quiz_questions",$tok);  
                if ($question) {
                    if ($in_first) {
                        $questions_field .= $question->new_id;
                        $in_first = false;
                    } else {
                        $questions_field .= ",".$question->new_id;
                    }
                }
                //check for next
                $tok = strtok(",");
            }
            //We have the questions field recoded to its new ids
            $quiz->questions = $questions_field;

            //The structure is equal to the db, so insert the quiz
            $newid = insert_record ("quiz",$quiz);

            //Do some output
            echo "<ul><li>Quiz \"".$quiz->name."\"<br>";
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //We have to restore the question_grades now (course level table)
                $status = quiz_question_grades_restore_mods($newid,$info,$restore);
                //Now check if want to restore user data and do it.
                if ($restore->mods[quiz]->userinfo) {
                    //Restore quiz_attempts
                    $status = quiz_attempts_restore_mods ($newid,$info,$restore);
                    if ($status) {
                        //Restore quiz_grades   
                        $status = quiz_grades_restore_mods ($newid,$info,$restore);
                    }  
                }
            } else {
                $status = false;
            }

            //Finalize ul
            echo "</ul>";

        } else {
            $status = false;
        }

        return $status;
    }

    //This function restores the quiz_question_grades 
    function quiz_question_grades_restore_mods($quiz_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the quiz_question_grades array
        $grades = $info['MOD']['#']['QUESTION_GRADES']['0']['#']['QUESTION_GRADE'];

        //Iterate over question_grades
        for($i = 0; $i < sizeof($grades); $i++) {
            $gra_info = $grades[$i];
            //traverse_xmlize($gra_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($gra_info['#']['ID']['0']['#']);

            //Now, build the QUESTION_GRADES record structure
            $grade->quiz = $quiz_id;
            $grade->question = backup_todb($gra_info['#']['QUESTION']['0']['#']);
            $grade->grade = backup_todb($gra_info['#']['GRADE']['0']['#']);

            //We have to recode the question field
            $question = backup_getid($restore->backup_unique_code,"quiz_questions",$grade->question);
            if ($question) {
                $grade->question = $question->new_id;
            }

            //The structure is equal to the db, so insert the quiz_question_grades
            $newid = insert_record ("quiz_question_grades",$grade);

            //Do some output
            if (($i+1) % 10 == 0) {
                echo ".";
                if (($i+1) % 200 == 0) {
                    echo "<br>";
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"quiz_question_grades",$oldid,
                             $newid);
            } else {
                $status = false;
            }
        }

        return $status;
    }

    //This function restores the quiz_attempts
    function quiz_attempts_restore_mods($quiz_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the quiz_attempts array
        $attempts = $info['MOD']['#']['ATTEMPTS']['0']['#']['ATTEMPT'];

        //Iterate over attempts
        for($i = 0; $i < sizeof($attempts); $i++) {
            $att_info = $attempts[$i];
            //traverse_xmlize($att_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($att_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($att_info['#']['USERID']['0']['#']);

            //Now, build the ATTEMPTS record structure
            $attempt->quiz = $quiz_id;
            $attempt->userid = backup_todb($att_info['#']['USERID']['0']['#']);
            $attempt->attempt = backup_todb($att_info['#']['ATTEMPTNUM']['0']['#']);
            $attempt->sumgrades = backup_todb($att_info['#']['SUMGRADES']['0']['#']);
            $attempt->timestart = backup_todb($att_info['#']['TIMESTART']['0']['#']);
            $attempt->timefinish = backup_todb($att_info['#']['TIMEFINISH']['0']['#']);
            $attempt->timemodified = backup_todb($att_info['#']['TIMEMODIFIED']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$attempt->userid);
            if ($user) {
                $attempt->userid = $user->new_id;
            }

            //The structure is equal to the db, so insert the quiz_attempts
            $newid = insert_record ("quiz_attempts",$attempt);

            //Do some output
            if (($i+1) % 10 == 0) {
                echo ".";
                if (($i+1) % 200 == 0) {
                    echo "<br>";
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"quiz_attempts",$oldid,
                             $newid);
                //Now process quiz_responses
                echo "process responses";
            } else {
                $status = false;
            }
        }

        return $status;
    }

    //This function restores the quiz_grades
    function quiz_grades_restore_mods($quiz_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the quiz_grades array
        $grades = $info['MOD']['#']['GRADES']['0']['#']['GRADE'];

        //Iterate over grades
        for($i = 0; $i < sizeof($grades); $i++) {
            $gra_info = $grades[$i];
            //traverse_xmlize($gra_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($gra_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($gra_info['#']['USERID']['0']['#']);

            //Now, build the GRADES record structure
            $grade->quiz = $quiz_id;
            $grade->userid = backup_todb($gra_info['#']['USERID']['0']['#']);
            $grade->grade = backup_todb($gra_info['#']['GRADEVAL']['0']['#']);
            $grade->timemodified = backup_todb($gra_info['#']['TIMEMODIFIED']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$grade->userid);
            if ($user) {
                $grade->userid = $user->new_id;
            }

            //The structure is equal to the db, so insert the quiz_grades
            $newid = insert_record ("quiz_grades",$grade);

            //Do some output
            if (($i+1) % 10 == 0) {
                echo ".";
                if (($i+1) % 200 == 0) {
                    echo "<br>";
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"quiz_grades",$oldid,
                             $newid);
            } else {
                $status = false;
            }
        }

        return $status;
    }


?>
