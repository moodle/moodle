<?php //$Id$
    //This php script contains all the stuff to backup/restore
    //quiz mods

// Todo:
    // the restoration of the parent and sortorder fields in the category table needs
    // a lot more thought. We should probably use a library function to add the category
    // rather than just writing it to the database

    // whereever it says "/// We have to recode the .... field" we should put in a check
    // to see if the recoding was successful and throw an appropriate error otherwise

//This is the "graphical" structure of the quiz mod:
    //To see, put your terminal to 160cc

    //
    //                           quiz                                                      quiz_categories
    //                        (CL,pk->id)                                                   (CL,pk->id)
    //                            |                                                              |
    //           -------------------------------------------------------------------             |
    //           |                        |                    |                   |             |.......................................
    //           |               quiz_grades                   |        quiz_question_versions   |                                      .
    //           |           (UL,pk->id,fk->quiz)              |         (CL,pk->id,fk->quiz)    |                                      .
    //           |                                             |                         .       |    ----quiz_question_datasets----    .
    //      quiz_attempts                          quiz_question_instances               .       |    |  (CL,pk->id,fk->question,  |    .
    //  (UL,pk->id,fk->quiz)                    (CL,pk->id,fk->quiz,question)            .       |    |   fk->dataset_definition)  |    .
    //             |                                              |                      .       |    |                            |    .
    //             |               quiz_newest_states             |                      .       |    |                            |    .
    //             |---------(UL,pk->id,fk->attempt,question)-----|                      .       |    |                            |    .
    //             |                        .                     |                      .       |    |                       quiz_dataset_definitions
    //             |                        .                     |                      .       |    |                      (CL,pk->id,fk->category)
    //             |                    quiz_states               |                      quiz_questions                                 |
    //             ----------(UL,pk->id,fk->attempt,question)--------------------------(CL,pk->id,fk->category,files)                   |
    //                                      |                                                    |                             quiz_dataset_items
    //                                      |                                                    |                          (CL,pk->id,fk->definition)
    //                              ---------                                                    |
    //                              |                                                            |
    //                        quiz_rqp_states                                                    |
    //                    (UL,pk->id,fk->stateid)                                                |                                   quiz_rqp_type
    //                                                                                           |                                    (SL,pk->id)
    //                                                                                           |                                         |
    //             --------------------------------------------------------------------------------------------------------------          |
    //             |             |              |              |                       |                  |                     |        quiz_rqp
    //             |             |              |              |                       |                  |                     |--(CL,pk->id,fk->question)
    //             |             |              |              |                 quiz_calculated          |                     |
    //      quiz_truefalse       |       quiz_multichoice      |             (CL,pl->id,fk->question)     |                     |
    // (CL,pk->id,fk->question)  |   (CL,pk->id,fk->question)  |                       .                  |                     |    quiz_randomsamatch
    //             .             |              .              |                       .                  |                     |--(CL,pk->id,fk->question)
    //             .      quiz_shortanswer      .       quiz_numerical                 .            quiz_multianswer.           |
    //             .  (CL,pk->id,fk->question)  .  (CL,pk->id,fk->question)            .        (CL,pk->id,fk->question)        |
    //             .             .              .              .                       .                  .                     |         quiz_match
    //             .             .              .              .                       .                  .                     |--(CL,pk->id,fk->question)
    //             .             .              .              .                       .                  .                     |             .
    //             .             .              .              .                       .                  .                     |             .
    //             .             .              .              .                       .                  .                     |             .
    //             .             .              .              .                       .                  .                     |       quiz_match_sub
    //             ........................................................................................                     |--(CL,pk->id,fk->question)
    //                                                   .                                                                      |
    //                                                   .                                                                      |
    //                                                   .                                                                      |    quiz_numerical_units
    //                                                quiz_answers                                                              |--(CL,pk->id,fk->question)
    //                                         (CL,pk->id,fk->question)----------------------------------------------------------
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          SL->site level info
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files
    //
    //-----------------------------------------------------------

// Comments
    //This module is special, because we make the restore in two steps:
    // 1.-We restore every category and their questions (complete structure). It includes this tables:
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
    //     - quiz_calculated
    //     - quiz_answers
    //     - quiz_numerical_units
    //     - quiz_question_datasets
    //     - quiz_dataset_definitions
    //     - quiz_dataset_items
    //    All this backup info has its own section in moodle.xml (QUESTION_CATEGORIES) and it's generated
    //    before every module backup standard invocation. And only if to restore quizzes has been selected !!
    //    It's invoked with quiz_restore_question_categories. (course independent).

    // 2.-Standard module restore (Invoked via quiz_restore_mods). It includes thes tables:
    //     - quiz
    //     - quiz_question_versions
    //     - quiz_question_instances
    //     - quiz_attempts
    //     - quiz_grades
    //     - quiz_states
    //    This step is the standard mod backup. (course dependent).

//STEP 1. Restore categories/questions and associated structures (course independent)
    function quiz_restore_question_categories($category,$restore) {

        global $CFG;

        $status = true;

        //Hook to call Moodle < 1.5 Quiz Restore
        if ($restore->backup_version < 2005043000) {
            include_once("restorelibpre15.php");
            return quiz_restore_pre15_question_categories($category,$restore);
        }

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
            $quiz_cat->stamp = backup_todb($info['QUESTION_CATEGORY']['#']['STAMP']['0']['#']);
            $quiz_cat->parent = backup_todb($info['QUESTION_CATEGORY']['#']['PARENT']['0']['#']);
            $quiz_cat->sortorder = backup_todb($info['QUESTION_CATEGORY']['#']['SORTORDER']['0']['#']);

            if ($catfound = restore_get_best_quiz_category($quiz_cat, $restore->course)) {
                $newid = $catfound;
            } else {
                if (!$quiz_cat->stamp) {
                    $quiz_cat->stamp = make_unique_id_code();
                }
                $newid = insert_record ("quiz_categories",$quiz_cat);
            }

            //Do some output
            if ($newid) {
                echo "<li>".get_string('category', 'quiz')." \"".$quiz_cat->name."\"<br />";
            } else {
                //We must never arrive here !!
                echo "<li>".get_string('category', 'quiz')." \"".$quiz_cat->name."\" Error!<br />";
                $status = false;
            }
            backup_flush(300);

            //Here category has been created or selected, so save results in backup_ids and start with questions
            if ($newid and $status) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"quiz_categories",
                             $category->id, $newid);
                //Now restore quiz_questions
                $status = quiz_restore_questions ($category->id, $newid,$info,$restore);
            } else {
                $status = false;
            }
            echo '</li>';
        }

        return $status;
    }

    function quiz_restore_questions ($old_category_id,$new_category_id,$info,$restore) {

        global $CFG;

        $status = true;
        $restored_questions = array();

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
            $question->parent = backup_todb($que_info['#']['PARENT']['0']['#']);
            $question->name = backup_todb($que_info['#']['NAME']['0']['#']);
            $question->questiontext = backup_todb($que_info['#']['QUESTIONTEXT']['0']['#']);
            $question->questiontextformat = backup_todb($que_info['#']['QUESTIONTEXTFORMAT']['0']['#']);
            $question->image = backup_todb($que_info['#']['IMAGE']['0']['#']);
            $question->defaultgrade = backup_todb($que_info['#']['DEFAULTGRADE']['0']['#']);
            $question->penalty = backup_todb($que_info['#']['PENALTY']['0']['#']);
            $question->qtype = backup_todb($que_info['#']['QTYPE']['0']['#']);
            $question->length = backup_todb($que_info['#']['LENGTH']['0']['#']);
            $question->stamp = backup_todb($que_info['#']['STAMP']['0']['#']);
            $question->version = backup_todb($que_info['#']['VERSION']['0']['#']);
            $question->hidden = backup_todb($que_info['#']['HIDDEN']['0']['#']);

            ////We have to recode the parent field
            // This should work alright because we ordered the questions appropriately during backup so that
            // questions that can be parents are restored first
            if ($question->parent and $parent = backup_getid($restore->backup_unique_code,"quiz_questions",$question->parent)) {
                $question->parent = $parent->new_id;
            }

            //Check if the question exists
            //by category and stamp
            $question_exists = get_record ("quiz_questions","category",$question->category,
                                                            "stamp",$question->stamp);

            //If the question exists, only record its id
            if ($question_exists) {
                $newid = $question_exists->id;
                $creatingnewquestion = false;
            //Else, create a new question
            } else {
                //The structure is equal to the db, so insert the quiz_questions
                $newid = insert_record ("quiz_questions",$question);
                $creatingnewquestion = true;
            }

            //Save newid to backup tables
            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"quiz_questions",$oldid,
                             $newid);
            }

            $restored_questions[$i] = new stdClass;
            $restored_questions[$i]->newid  = $newid;
            $restored_questions[$i]->oldid  = $oldid;
            $restored_questions[$i]->qtype  = $question->qtype;
            $restored_questions[$i]->is_new = $creatingnewquestion;
        }

        // Loop again, now all the question id mappings exist, so everything can
        // be restored.
        for($i = 0; $i < sizeof($questions); $i++) {
            $que_info = $questions[$i];

            $newid = $restored_questions[$i]->newid;
            $oldid = $restored_questions[$i]->oldid;
            $question->qtype = $restored_questions[$i]->qtype;


            //If it's a new question in the DB, restore it
            if ($restored_questions[$i]->is_new) {
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
                } else if ($question->qtype == "7") {
                    //Description question. Nothing to do.
                } else if ($question->qtype == "8") {
                    $status = quiz_restore_numerical($oldid,$newid,$que_info,$restore);
                } else if ($question->qtype == "9") {
                    $status = quiz_restore_multianswer($oldid,$newid,$que_info,$restore);
                } else if ($question->qtype == "10") {
                    $status = quiz_restore_calculated($oldid,$newid,$que_info,$restore);
                } else if ($question->qtype == "11") {
                    $status = quiz_restore_rqp($oldid,$newid,$que_info,$restore);
                }
            } else {
                //We are NOT creating the question, but we need to know every quiz_answers
                //map between the XML file and the database to be able to restore the states
                //in each attempt.
                $status = quiz_restore_map_answers($oldid,$newid,$que_info,$restore);
                //Now, depending of the type of questions, invoke different functions
                //to create the necessary mappings in backup_ids, because we are not
                //creating the question, but need some records in backup table
                if ($question->qtype == "1") {
                    //Shortanswer question. Nothing to remap
                } else if ($question->qtype == "2") {
                    //Truefalse question. Nothing to remap
                } else if ($question->qtype == "3") {
                    //Multichoice question. Nothing to remap
                } else if ($question->qtype == "4") {
                    //Random question. Nothing to remap
                } else if ($question->qtype == "5") {
                    $status = quiz_restore_map_match($oldid,$newid,$que_info,$restore);
                } else if ($question->qtype == "6") {
                    //Randomsamatch question. Nothing to remap
                } else if ($question->qtype == "7") {
                    //Description question. Nothing to remap
                } else if ($question->qtype == "8") {
                    //Numerical question. Nothing to remap
                } else if ($question->qtype == "9") {
                    $status = quiz_restore_map_multianswer($oldid,$newid,$que_info,$restore);
                } else if ($question->qtype == "10") {
                    //Calculated question. Nothing to remap
                }
            }

            //Do some output
            if (($i+1) % 2 == 0) {
                echo ".";
                if (($i+1) % 40 == 0) {
                    echo "<br />";
                }
                backup_flush(300);
            }
        }
        return $status;
    }

    function quiz_restore_answers ($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the answers array
        if (isset($info['#']['ANSWERS']['0']['#']['ANSWER'])) {
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
                        echo "<br />";
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
        }

        return $status;
    }

    function quiz_restore_map_answers ($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        if (!isset($info['#']['ANSWERS'])) {    // No answers in this question (eg random)
            return $status;
        }

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

            //If we are in this method is because the question exists in DB, so its
            //answers must exist too.
            //Now, we are going to look for that answer in DB and to create the
            //mappings in backup_ids to use them later where restoring states (user level).

            //Get the answer from DB (by question and answer)
            $db_answer = get_record ("quiz_answers","question",$new_question_id,
                                                    "answer",$answer->answer);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br />";
                }
                backup_flush(300);
            }

            if ($db_answer) {
                //We have the database answer, update backup_ids
                backup_putid($restore->backup_unique_code,"quiz_answers",$oldid,
                             $db_answer->id);
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
                    echo "<br />";
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
                    echo "<br />";
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
                    echo "<br />";
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

            //We'll need this later!!
            $oldid = backup_todb($mat_info['#']['ID']['0']['#']);

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
                    echo "<br />";
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"quiz_match_sub",$oldid,
                             $newid);
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

    function quiz_restore_map_match ($old_question_id,$new_question_id,$info,$restore) {

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

            //We'll need this later!!
            $oldid = backup_todb($mat_info['#']['ID']['0']['#']);

            //Now, build the QUIZ_MATCH_SUB record structure
            $match_sub->question = $new_question_id;
            $match_sub->questiontext = backup_todb($mat_info['#']['QUESTIONTEXT']['0']['#']);
            $match_sub->answertext = backup_todb($mat_info['#']['ANSWERTEXT']['0']['#']);

            //If we are in this method is because the question exists in DB, so its
            //match_sub must exist too.
            //Now, we are going to look for that match_sub in DB and to create the
            //mappings in backup_ids to use them later where restoring states (user level).

            //Get the match_sub from DB (by question, questiontext and answertext)
            $db_match_sub = get_record ("quiz_match_sub","question",$new_question_id,
                                                      "questiontext",$match_sub->questiontext,
                                                      "answertext",$match_sub->answertext);
            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br />";
                }
                backup_flush(300);
            }

            //We have the database match_sub, so update backup_ids
            if ($db_match_sub) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"quiz_match_sub",$oldid,
                             $db_match_sub->id);
            } else {
                $status = false;
            }
        }

        return $status;
    }

    function quiz_restore_map_multianswer ($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the multianswers array
        $multianswers = $info['#']['MULTIANSWERS']['0']['#']['MULTIANSWER'];
        //Iterate over multianswers
        for($i = 0; $i < sizeof($multianswers); $i++) {
            $mul_info = $multianswers[$i];
            //traverse_xmlize($mul_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We need this later
            $oldid = backup_todb($mul_info['#']['ID']['0']['#']);

            //Now, build the QUIZ_MULTIANSWER record structure
            $multianswer->question = $new_question_id;
            $multianswer->answers = backup_todb($mul_info['#']['ANSWERS']['0']['#']);
            $multianswer->positionkey = backup_todb($mul_info['#']['POSITIONKEY']['0']['#']);
            $multianswer->answertype = backup_todb($mul_info['#']['ANSWERTYPE']['0']['#']);
            $multianswer->norm = backup_todb($mul_info['#']['NORM']['0']['#']);

            //If we are in this method is because the question exists in DB, so its
            //multianswer must exist too.
            //Now, we are going to look for that multianswer in DB and to create the
            //mappings in backup_ids to use them later where restoring states (user level).

            //Get the multianswer from DB (by question and positionkey)
            $db_multianswer = get_record ("quiz_multianswers","question",$new_question_id,
                                                      "positionkey",$multianswer->positionkey);
            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br />";
                }
                backup_flush(300);
            }

            //We have the database multianswer, so update backup_ids
            if ($db_multianswer) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"quiz_multianswers",$oldid,
                             $db_multianswer->id);
            } else {
                $status = false;
            }
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
            $randomsamatch->choose = backup_todb($ran_info['#']['CHOOSE']['0']['#']);

            //The structure is equal to the db, so insert the quiz_randomsamatch
            $newid = insert_record ("quiz_randomsamatch",$randomsamatch);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br />";
                }
                backup_flush(300);
            }

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }

    function quiz_restore_numerical ($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the numerical array
        $numericals = $info['#']['NUMERICAL'];

        //Iterate over numericals
        for($i = 0; $i < sizeof($numericals); $i++) {
            $num_info = $numericals[$i];
            //traverse_xmlize($num_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the QUIZ_NUMERICAL record structure
            $numerical->question = $new_question_id;
            $numerical->answer = backup_todb($num_info['#']['ANSWER']['0']['#']);
            $numerical->tolerance = backup_todb($num_info['#']['TOLERANCE']['0']['#']);

            ////We have to recode the answer field
            $answer = backup_getid($restore->backup_unique_code,"quiz_answers",$numerical->answer);
            if ($answer) {
                $numerical->answer = $answer->new_id;
            }

            //The structure is equal to the db, so insert the quiz_numerical
            $newid = insert_record ("quiz_numerical",$numerical);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br />";
                }
                backup_flush(300);
            }

            //Now restore numerical_units
            $status = quiz_restore_numerical_units ($old_question_id,$new_question_id,$num_info,$restore);

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }

    function quiz_restore_calculated ($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the calculated-s array
        $calculateds = $info['#']['CALCULATED'];

        //Iterate over calculateds
        for($i = 0; $i < sizeof($calculateds); $i++) {
            $cal_info = $calculateds[$i];
            //traverse_xmlize($cal_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the QUIZ_CALCULATED record structure
            $calculated->question = $new_question_id;
            $calculated->answer = backup_todb($cal_info['#']['ANSWER']['0']['#']);
            $calculated->tolerance = backup_todb($cal_info['#']['TOLERANCE']['0']['#']);
            $calculated->tolerancetype = backup_todb($cal_info['#']['TOLERANCETYPE']['0']['#']);
            $calculated->correctanswerlength = backup_todb($cal_info['#']['CORRECTANSWERLENGTH']['0']['#']);
            $calculated->correctanswerformat = backup_todb($cal_info['#']['CORRECTANSWERFORMAT']['0']['#']);

            ////We have to recode the answer field
            $answer = backup_getid($restore->backup_unique_code,"quiz_answers",$calculated->answer);
            if ($answer) {
                $calculated->answer = $answer->new_id;
            }

            //The structure is equal to the db, so insert the quiz_calculated
            $newid = insert_record ("quiz_calculated",$calculated);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br />";
                }
                backup_flush(300);
            }

            //Now restore numerical_units
            $status = quiz_restore_numerical_units ($old_question_id,$new_question_id,$cal_info,$restore);

            //Now restore dataset_definitions
            if ($status && $newid) {
                $status = quiz_restore_dataset_definitions ($old_question_id,$new_question_id,$cal_info,$restore);
            }

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }

    function quiz_restore_multianswer ($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the multianswers array
        $multianswers = $info['#']['MULTIANSWERS']['0']['#']['MULTIANSWER'];
        //Iterate over multianswers
        for($i = 0; $i < sizeof($multianswers); $i++) {
            $mul_info = $multianswers[$i];
            //traverse_xmlize($mul_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We need this later
            $oldid = backup_todb($mul_info['#']['ID']['0']['#']);

            //Now, build the QUIZ_MULTIANSWER record structure
            $multianswer->question = $new_question_id;
            $multianswer->sequence = backup_todb($mul_info['#']['SEQUENCE']['0']['#']);

            //We have to recode the sequence field (a list of question ids)
            //Extracts question id from sequence
            $sequence_field = "";
            $in_first = true;
            $tok = strtok($multianswer->sequence,",");
            while ($tok) {
                //Get the answer from backup_ids
                $question = backup_getid($restore->backup_unique_code,"quiz_questions",$tok);
                if ($question) {
                    if ($in_first) {
                        $sequence_field .= $question->new_id;
                        $in_first = false;
                    } else {
                        $sequence_field .= ",".$question->new_id;
                    }
                }
                //check for next
                $tok = strtok(",");
            }
            //We have the answers field recoded to its new ids
            $multianswer->sequence = $sequence_field;
            //The structure is equal to the db, so insert the quiz_multianswers
            $newid = insert_record ("quiz_multianswers",$multianswer);

            //Save ids in backup_ids
            if ($newid) {
                backup_putid($restore->backup_unique_code,"quiz_multianswers",
                             $oldid, $newid);
            }

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br />";
                }
                backup_flush(300);
            }
/*
            //If we have created the quiz_multianswers record, now, depending of the
            //answertype, delegate the restore to every qtype function
            if ($newid) {
                if ($multianswer->answertype == "1") {
                    $status = quiz_restore_shortanswer ($old_question_id,$new_question_id,$mul_info,$restore);
                } else if ($multianswer->answertype == "3") {
                    $status = quiz_restore_multichoice ($old_question_id,$new_question_id,$mul_info,$restore);
                } else if ($multianswer->answertype == "8") {
                    $status = quiz_restore_numerical ($old_question_id,$new_question_id,$mul_info,$restore);
                }
            } else {
                $status = false;
            }
*/
        }

        return $status;
    }

    function quiz_restore_rqp ($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the truefalse array
        $rqps = $info['#']['RQP'];

        //Iterate over rqp
        for($i = 0; $i < sizeof($rqps); $i++) {
            $tru_info = $rqps[$i];
            //traverse_xmlize($tru_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the QUIZ_RQP record structure
            $rqp->question = $new_question_id;
            $rqp->type = backup_todb($tru_info['#']['TYPE']['0']['#']);
            $rqp->source = backup_todb($tru_info['#']['SOURCE']['0']['#']);
            $rqp->format = backup_todb($tru_info['#']['FORMAT']['0']['#']);
            $rqp->flags = backup_todb($tru_info['#']['FLAGS']['0']['#']);
            $rqp->maxscore = backup_todb($tru_info['#']['MAXSCORE']['0']['#']);

            //The structure is equal to the db, so insert the quiz_rqp
            $newid = insert_record ("quiz_rqp",$rqp);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br />";
                }
                backup_flush(300);
            }

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }


    function quiz_restore_numerical_units ($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the numerical array
        $numerical_units = $info['#']['NUMERICAL_UNITS']['0']['#']['NUMERICAL_UNIT'];

        //Iterate over numerical_units
        for($i = 0; $i < sizeof($numerical_units); $i++) {
            $nu_info = $numerical_units[$i];
            //traverse_xmlize($nu_info);                                                                  //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the QUIZ_NUMERICAL_UNITS record structure
            $numerical_unit->question = $new_question_id;
            $numerical_unit->multiplier = backup_todb($nu_info['#']['MULTIPLIER']['0']['#']);
            $numerical_unit->unit = backup_todb($nu_info['#']['UNIT']['0']['#']);

            //The structure is equal to the db, so insert the quiz_numerical_units
            $newid = insert_record ("quiz_numerical_units",$numerical_unit);

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }

    function quiz_restore_dataset_definitions ($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the dataset_definitions array
        $dataset_definitions = $info['#']['DATASET_DEFINITIONS']['0']['#']['DATASET_DEFINITION'];

        //Iterate over dataset_definitions
        for($i = 0; $i < sizeof($dataset_definitions); $i++) {
            $dd_info = $dataset_definitions[$i];
            //traverse_xmlize($dd_info);                                                                  //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the QUIZ_DATASET_DEFINITION record structure
            $dataset_definition->category = backup_todb($dd_info['#']['CATEGORY']['0']['#']);
            $dataset_definition->name = backup_todb($dd_info['#']['NAME']['0']['#']);
            $dataset_definition->type = backup_todb($dd_info['#']['TYPE']['0']['#']);
            $dataset_definition->options = backup_todb($dd_info['#']['OPTIONS']['0']['#']);
            $dataset_definition->itemcount = backup_todb($dd_info['#']['ITEMCOUNT']['0']['#']);

            //We have to recode the category field (only if the category != 0)
            if ($dataset_definition->category != 0) {
                $category = backup_getid($restore->backup_unique_code,"quiz_categories",$dataset_definition->category);
                if ($category) {
                    $dataset_definition->category = $category->new_id;
                }
            }

            //Now, we hace to decide when to create the new records or reuse an existing one
            $create_definition = false;

            //If the dataset_definition->category = 0, it's a individual question dataset_definition, so we'll create it
            if ($dataset_definition->category == 0) {
                $create_definition = true;
            } else {
                //The category isn't 0, so it's a category question dataset_definition, we have to see if it exists
                //Look for a definition with the same category, name and type
                if ($definitionrec = get_record_sql("SELECT d.*
                                                     FROM {$CFG->prefix}quiz_dataset_definitions d
                                                     WHERE d.category = '$dataset_definition->category' AND
                                                           d.name = '$dataset_definition->name' AND
                                                           d.type = '$dataset_definition->type'")) {
                    //Such dataset_definition exist. Now we must check if it has enough itemcount
                    if ($definitionrec->itemcount < $dataset_definition->itemcount) {
                        //We haven't enough itemcount, so we have to create the definition as an individual question one.
                        $dataset_definition->category = 0;
                        $create_definition = true;
                    } else {
                        //We have enough itemcount, so we'll reuse the existing definition
                        $create_definition = false;
                        $newid = $definitionrec->id;
                    }
                } else {
                    //Such dataset_definition doesn't exist. We'll create it.
                    $create_definition = true;
                }
            }

            //If we've to create the definition, do it
            if ($create_definition) {
                //The structure is equal to the db, so insert the quiz_dataset_definitions
                $newid = insert_record ("quiz_dataset_definitions",$dataset_definition);
                if ($newid) {
                    //Restore quiz_dataset_items
                    $status = quiz_restore_dataset_items($newid,$dd_info,$restore);
                }
            }

            //Now, we must have a definition (created o reused). Its id is in newid. Create the quiz_question_datasets record
            //to join the question and the dataset_definition
            if ($newid) {
                $question_dataset->question = $new_question_id;
                $question_dataset->datasetdefinition = $newid;
                $newid = insert_record ("quiz_question_datasets",$question_dataset);
            }

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }

    function quiz_restore_dataset_items ($definitionid,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the items array
        $dataset_items = $info['#']['DATASET_ITEMS']['0']['#']['DATASET_ITEM'];

        //Iterate over dataset_items
        for($i = 0; $i < sizeof($dataset_items); $i++) {
            $di_info = $dataset_items[$i];
            //traverse_xmlize($di_info);                                                                  //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the QUIZ_DATASET_ITEMS record structure
            $dataset_item->definition = $definitionid;
            $dataset_item->number = backup_todb($di_info['#']['NUMBER']['0']['#']);
            $dataset_item->value = backup_todb($di_info['#']['VALUE']['0']['#']);

            //The structure is equal to the db, so insert the quiz_dataset_items
            $newid = insert_record ("quiz_dataset_items",$dataset_item);

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }

//STEP 2. Restore quizzes and associated structures (course dependent)
    function quiz_restore_mods($mod,$restore) {

        global $CFG;

        $status = true;

        //Hook to call Moodle < 1.5 Quiz Restore
        if ($restore->backup_version < 2005043000) {
            include_once("restorelibpre15.php");
            return quiz_restore_pre15_mods($mod,$restore);
        }

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
            $quiz->optionflags = backup_todb($info['MOD']['#']['OPTIONFLAGS']['0']['#']);
            $quiz->penaltyscheme = backup_todb($info['MOD']['#']['PENALTYSCHEME']['0']['#']);
            $quiz->attempts = backup_todb($info['MOD']['#']['ATTEMPTS_NUMBER']['0']['#']);
            $quiz->attemptonlast = backup_todb($info['MOD']['#']['ATTEMPTONLAST']['0']['#']);
            $quiz->grademethod = backup_todb($info['MOD']['#']['GRADEMETHOD']['0']['#']);
            $quiz->decimalpoints = backup_todb($info['MOD']['#']['DECIMALPOINTS']['0']['#']);
            $quiz->review = backup_todb($info['MOD']['#']['REVIEW']['0']['#']);
            $quiz->questionsperpage = backup_todb($info['MOD']['#']['QUESTIONSPERPAGE']['0']['#']);
            $quiz->shufflequestions = backup_todb($info['MOD']['#']['SHUFFLEQUESTIONS']['0']['#']);
            $quiz->shuffleanswers = backup_todb($info['MOD']['#']['SHUFFLEANSWERS']['0']['#']);
            $quiz->questions = backup_todb($info['MOD']['#']['QUESTIONS']['0']['#']);
            $quiz->sumgrades = backup_todb($info['MOD']['#']['SUMGRADES']['0']['#']);
            $quiz->grade = backup_todb($info['MOD']['#']['GRADE']['0']['#']);
            $quiz->timecreated = backup_todb($info['MOD']['#']['TIMECREATED']['0']['#']);
            $quiz->timemodified = backup_todb($info['MOD']['#']['TIMEMODIFIED']['0']['#']);
            $quiz->timelimit = backup_todb($info['MOD']['#']['TIMELIMIT']['0']['#']);
            $quiz->password = backup_todb($info['MOD']['#']['PASSWORD']['0']['#']);
            $quiz->subnet = backup_todb($info['MOD']['#']['SUBNET']['0']['#']);
            $quiz->popup = backup_todb($info['MOD']['#']['POPUP']['0']['#']);

            //We have to recode the questions field (a list of questions id and pagebreaks)
            $quiz->questions = quiz_recode_layout($quiz->questions, $restore);

            //The structure is equal to the db, so insert the quiz
            $newid = insert_record ("quiz",$quiz);

            //Do some output
            echo "<li>".get_string("modulename","quiz")." \"".format_string(stripslashes($quiz->name),true)."\"</li>";
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //We have to restore the question_instances now (course level table)
                $status = quiz_question_instances_restore_mods($newid,$info,$restore);
                //We have to restore the question_versions now (course level table)
                $status = quiz_question_versions_restore_mods($newid,$info,$restore);
                //Now check if want to restore user data and do it.
                if ($restore->mods['quiz']->userinfo) {
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
        } else {
            $status = false;
        }

        return $status;
    }

    //This function restores the quiz_question_instances
    function quiz_question_instances_restore_mods($quiz_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the quiz_question_instances array
        $instances = $info['MOD']['#']['QUESTION_INSTANCES']['0']['#']['QUESTION_INSTANCE'];

        //Iterate over question_instances
        for($i = 0; $i < sizeof($instances); $i++) {
            $gra_info = $instances[$i];
            //traverse_xmlize($gra_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($gra_info['#']['ID']['0']['#']);

            //Now, build the QUESTION_INSTANCES record structure
            $instance->quiz = $quiz_id;
            $instance->question = backup_todb($gra_info['#']['QUESTION']['0']['#']);
            $instance->grade = backup_todb($gra_info['#']['GRADE']['0']['#']);

            //We have to recode the question field
            $question = backup_getid($restore->backup_unique_code,"quiz_questions",$instance->question);
            if ($question) {
                $instance->question = $question->new_id;
            }

            //The structure is equal to the db, so insert the quiz_question_instances
            $newid = insert_record ("quiz_question_instances",$instance);

            //Do some output
            if (($i+1) % 10 == 0) {
                echo ".";
                if (($i+1) % 200 == 0) {
                    echo "<br />";
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"quiz_question_instances",$oldid,
                             $newid);
            } else {
                $status = false;
            }
        }

        return $status;
    }

    //This function restores the quiz_question_versions
    function quiz_question_versions_restore_mods($quiz_id,$info,$restore) {

        global $CFG, $USER;

        $status = true;

        //Get the quiz_question_versions array
        $versions = $info['MOD']['#']['QUESTION_VERSIONS']['0']['#']['QUESTION_VERSION'];

        //Iterate over question_versions
        for($i = 0; $i < sizeof($versions); $i++) {
            $ver_info = $versions[$i];
            //traverse_xmlize($ver_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($ver_info['#']['ID']['0']['#']);

            //Now, build the QUESTION_VERSIONS record structure
            $version->quiz = $quiz_id;
            $version->oldquestion = backup_todb($ver_info['#']['OLDQUESTION']['0']['#']);
            $version->newquestion = backup_todb($ver_info['#']['NEWQUESTION']['0']['#']);
            $version->userid = backup_todb($ver_info['#']['USERID']['0']['#']);
            $version->timestamp = backup_todb($ver_info['#']['TIMESTAMP']['0']['#']);

            //We have to recode the oldquestion field
            $question = backup_getid($restore->backup_unique_code,"quiz_questions",$version->oldquestion);
            if ($question) {
                $version->oldquestion = $question->new_id;
            }

            //We have to recode the newquestion field
            $question = backup_getid($restore->backup_unique_code,"quiz_questions",$version->newquestion);
            if ($question) {
                $version->newquestion = $question->new_id;
            }

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$version->userid);
            if ($user) {
                $version->userid = $user->new_id;
            } else {  //Assign to current user
                $version->userid = $USER->id;
            }

            //The structure is equal to the db, so insert the quiz_question_versions
            $newid = insert_record ("quiz_question_versions",$version);

            //Do some output
            if (($i+1) % 10 == 0) {
                echo ".";
                if (($i+1) % 200 == 0) {
                    echo "<br />";
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"quiz_question_versions",$oldid,
                             $newid);
            } else {
                $status = false;
            }
        }

        return $status;
    }

    //This function restores the quiz_attempts
    function quiz_attempts_restore_mods($quiz_id,$info,$restore) {

        notify("Restoring quiz without user attempts. Restoring of user attempts will be implemented in Moodle 1.5.1");
        return true;

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
            $attempt->layout = backup_todb($att_info['#']['LAYOUT']['0']['#']);
            $attempt->preview = backup_todb($att_info['#']['PREVIEW']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$attempt->userid);
            if ($user) {
                $attempt->userid = $user->new_id;
            }

            //We have to recode the layout field (a list of questions id and pagebreaks)
        $attempt->layout = quiz_recode_layout($attempt->layout, $restore);

            //The structure is equal to the db, so insert the quiz_attempts
            $newid = insert_record ("quiz_attempts",$attempt);

            //Do some output
            if (($i+1) % 10 == 0) {
                echo ".";
                if (($i+1) % 200 == 0) {
                    echo "<br />";
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"quiz_attempts",$oldid,
                             $newid);
                //Now process quiz_states
                $status = quiz_states_restore_mods($newid,$att_info,$restore);
            } else {
                $status = false;
            }
        }

        return $status;
    }

    //This function restores the quiz_states
    function quiz_states_restore_mods($attempt_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the quiz_states array
        $states = $info['#']['STATES']['0']['#']['STATE'];
        //Iterate over states
        for($i = 0; $i < sizeof($states); $i++) {
            $res_info = $states[$i];
            //traverse_xmlize($res_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($res_info['#']['ID']['0']['#']);

            //Now, build the STATES record structure
            $state->attempt = $attempt_id;
            $state->question = backup_todb($res_info['#']['QUESTION']['0']['#']);
            $state->originalquestion = backup_todb($res_info['#']['ORIGINALQUESTION']['0']['#']);
            $state->seq_number = backup_todb($res_info['#']['SEQ_NUMBER']['0']['#']);
            $state->answer = backup_todb($res_info['#']['ANSWER']['0']['#']);
            $state->timestamp = backup_todb($res_info['#']['TIMESTAMP']['0']['#']);
            $state->event = backup_todb($res_info['#']['EVENT']['0']['#']);
            $state->grade = backup_todb($res_info['#']['GRADE']['0']['#']);
            $state->raw_grade = backup_todb($res_info['#']['RAW_GRADE']['0']['#']);
            $state->penalty = backup_todb($res_info['#']['PENALTY']['0']['#']);

            //We have to recode the question field
            $question = backup_getid($restore->backup_unique_code,"quiz_questions",$state->question);
            if ($question) {
                $state->question = $question->new_id;
            }

            //We have to recode the originalquestion field
            $question = backup_getid($restore->backup_unique_code,"quiz_questions",$state->originalquestion);
            if ($question) {
                $state->originalquestion = $question->new_id;
            }

            //We have to recode the answer field
            //It depends of the question type !!
            //We get the question first
            $question = get_record("quiz_questions","id",$state->question);
            //It exists
            if ($question) {
                //Depending of the qtype, we make different recodes
                switch ($question->qtype) {
                    case 1:    //SHORTANSWER QTYPE
                        //Nothing to do. The response is a text.
                        break;
                    case 2:    //TRUEFALSE QTYPE
                        //The answer is one answer id. We must recode it
                        $answer = backup_getid($restore->backup_unique_code,"quiz_answers",$state->answer);
                        if ($answer) {
                            $state->answer = $answer->new_id;
                        }
                        break;
                    case 3:    //MULTICHOICE QTYPE
                        //The answer is a comma separated list of answers. We must recode them
                        $answer_field = "";
                        $in_first = true;
                        $tok = strtok($state->answer,",");
                        while ($tok) {
                            //Get the answer from backup_ids
                            $answer = backup_getid($restore->backup_unique_code,"quiz_answers",$tok);
                            if ($answer) {
                                if ($in_first) {
                                    $answer_field .= $answer->new_id;
                                    $in_first = false;
                                } else {
                                    $answer_field .= ",".$answer->new_id;
                                }
                            }
                            //check for next
                            $tok = strtok(",");
                        }
                        $state->answer = $answer_field;
                        break;
                    case 4:    //RANDOM QTYPE
                        //The answer links to another question id, we must recode it
                        $answer_link = backup_getid($restore->backup_unique_code,"quiz_questions",$state->answer);
                        if ($answer_link) {
                            $state->answer = $answer_link->new_id;
                        }
                        break;
                    case 5:    //MATCH QTYPE
                        //The answer is a comma separated list of hypen separated math_subs (for question and answer)
                        $answer_field = "";
                        $in_first = true;
                        $tok = strtok($state->answer,",");
                        while ($tok) {
                            //Extract the match_sub for the question and the answer
                            $exploded = explode("-",$tok);
                            $match_question_id = $exploded[0];
                            $match_answer_id = $exploded[1];
                            //Get the match_sub from backup_ids (for the question)
                            $match_que = backup_getid($restore->backup_unique_code,"quiz_match_sub",$match_question_id);
                            //Get the match_sub from backup_ids (for the answer)
                            $match_ans = backup_getid($restore->backup_unique_code,"quiz_match_sub",$match_answer_id);
                            if ($match_que) {
                                //It the question hasn't response, it must be 0
                                if (!$match_ans and $match_answer_id == 0) {
                                    $match_ans->new_id = 0;
                                }
                                if ($in_first) {
                                    $answer_field .= $match_que->new_id."-".$match_ans->new_id;
                                    $in_first = false;
                                } else {
                                    $answer_field .= ",".$match_que->new_id."-".$match_ans->new_id;
                                }
                            }
                            //check for next
                            $tok = strtok(",");
                        }
                        $state->answer = $answer_field;
                        break;
                    case 6:    //RANDOMSAMATCH QTYPE
                        //The answer is a comma separated list of hypen separated question_id and answer_id. We must recode them
                        $answer_field = "";
                        $in_first = true;
                        $tok = strtok($state->answer,",");
                        while ($tok) {
                            //Extract the question_id and the answer_id
                            $exploded = explode("-",$tok);
                            $question_id = $exploded[0];
                            $answer_id = $exploded[1];
                            //Get the question from backup_ids
                            $que = backup_getid($restore->backup_unique_code,"quiz_questions",$question_id);
                            //Get the answer from backup_ids
                            $ans = backup_getid($restore->backup_unique_code,"quiz_answers",$answer_id);
                            if ($que) {
                                //It the question hasn't response, it must be 0
                                if (!$ans and $answer_id == 0) {
                                    $ans->new_id = 0;
                                }
                                if ($in_first) {
                                    $answer_field .= $que->new_id."-".$ans->new_id;
                                    $in_first = false;
                                } else {
                                    $answer_field .= ",".$que->new_id."-".$ans->new_id;
                                }
                            }
                            //check for next
                            $tok = strtok(",");
                        }
                        $state->answer = $answer_field;
                        break;
                    case 7:    //DESCRIPTION QTYPE
                        //Nothing to do (there is no awser to this qtype)
                        //But this case must exist !!
                        break;
                    case 8:    //NUMERICAL QTYPE
                        //Nothing to do. The response is a text.
                        break;
                    case 9:    //MULTIANSWER QTYPE
                        //The answer is a comma separated list of hypen separated multianswer_id and answers. We must recode them.
                        $answer_field = "";
                        $in_first = true;
                        $tok = strtok($state->answer,",");
                        while ($tok) {
                            //Extract the multianswer_id and the answer
                            $exploded = explode("-",$tok);
                            $multianswer_id = $exploded[0];
                            $answer = $exploded[1];
                            //Get the multianswer from backup_ids
                            $mul = backup_getid($restore->backup_unique_code,"quiz_multianswers",$multianswer_id);
                            if ($mul) {
                                //Now, depending of the answertype field in quiz_multianswers
                                //we do diferent things
                                $mul_db = get_record ("quiz_multianswers","id",$mul->new_id);
                                if ($mul_db->answertype == "1") {
                                    //Shortanswer
                                    //The answer is text, do nothing
                                } else if ($mul_db->answertype == "3") {
                                    //Multichoice
                                    //The answer is an answer_id, look for it in backup_ids
                                    $ans = backup_getid($restore->backup_unique_code,"quiz_answers",$answer);
                                    $answer = $ans->new_id;
                                } else if ($mul_db->answertype == "8") {
                                    //Numeric
                                    //The answer is text, do nothing
                                }

                                //Finaly, build the new answer field for each pair
                                if ($in_first) {
                                    $answer_field .= $mul->new_id."-".$answer;
                                    $in_first = false;
                                } else {
                                    $answer_field .= ",".$mul->new_id."-".$answer;
                                }
                            }
                            //check for next
                            $tok = strtok(",");
                        }
                        $state->answer = $answer_field;
                        break;
                    case 10:    //CALCULATED QTYPE
                        //Nothing to do. The response is a text.
                        break;
                    default:   //UNMATCHED QTYPE.
                        //This is an error (unimplemented qtype)
                        $status = false;
                        break;
                }
            } else {
                $status = false;
            }

            //The structure is equal to the db, so insert the quiz_states
            $newid = insert_record ("quiz_states",$state);

            //Do some output
            if (($i+1) % 10 == 0) {
                echo ".";
                if (($i+1) % 200 == 0) {
                    echo "<br />";
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"quiz_states",$oldid,
                             $newid);
                //Now process question type specific state information
                $status = quiz_rqp_states_restore_mods($newid,$res_info,$restore);
            } else {
                $status = false;
            }
        }

        //Get the quiz_newest_states array
        $newest_states = $info['#']['NEWEST_STATES']['0']['#']['NEWEST_STATE'];
        //Iterate over newest_states
        for($i = 0; $i < sizeof($newest_states); $i++) {
            $res_info = $newest_states[$i];
            //traverse_xmlize($res_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the NEWEST_STATES record structure
            $newest_state->attemptid = $attempt_id;
            $newest_state->questionid = backup_todb($res_info['#']['QUESTIONID']['0']['#']);
            $newest_state->newest = backup_todb($res_info['#']['NEWEST']['0']['#']);
            $newest_state->newgraded = backup_todb($res_info['#']['NEWGRADED']['0']['#']);
            $newest_state->sumpenalty = backup_todb($res_info['#']['SUMPENALTY']['0']['#']);

            //We have to recode the question field
            $question = backup_getid($restore->backup_unique_code,"quiz_questions",$newest_state->question);
            if ($question) {
                $newest_state->question = $question->new_id;
            }

            //We have to recode the newest field
            $state = backup_getid($restore->backup_unique_code,"quiz_states",$newest_state->newest);
            if ($staten) {
                $newest_state->newest = $state->new_id;
            }

            //We have to recode the newgraded field
            $state = backup_getid($restore->backup_unique_code,"quiz_states",$newest_state->newgraded);
            if ($staten) {
                $newest_state->newgraded = $state->new_id;
            }

            //The structure is equal to the db, so insert the quiz_newest_states
            $newid = insert_record ("quiz_newest_states",$newest_state);

        }

        return $status;
    }

    //This function restores the quiz_rqp_states
    function quiz_rqp_states_restore_mods($state_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the quiz_rqp_state
        $rqp_state = $info['#']['RQP_STATE']['0'];
        if ($rqp_state) {

            //Now, build the RQP_STATES record structure
            $state->stateid = $state_id;
            $state->responses = backup_todb($rqp_state['#']['RESPONSES']['0']['#']);
            $state->persistent_data = backup_todb($rqp_state['#']['PERSISTENT_DATA']['0']['#']);
            $state->template_vars = backup_todb($rqp_state['#']['TEMPLATE_VARS']['0']['#']);

            //The structure is equal to the db, so insert the quiz_states
            $newid = insert_record ("quiz_rqp_states",$state);
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
                    echo "<br />";
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

    //Return a content decoded to support interactivities linking. Every module
    //should have its own. They are called automatically from
    //quiz_decode_content_links_caller() function in each module
    //in the restore process
    function quiz_decode_content_links ($content,$restore) {
            
        global $CFG;
            
        $result = $content;
                
        //Link to the list of quizs
                
        $searchstring='/\$@(QUIZINDEX)\*([0-9]+)@\$/';
        //We look for it
        preg_match_all($searchstring,$content,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //print_object($foundset);                                     //Debug
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (course id)
                $rec = backup_getid($restore->backup_unique_code,"course",$old_id);
                //Personalize the searchstring
                $searchstring='/\$@(QUIZINDEX)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/quiz/index.php?id='.$rec->new_id,$result);
                } else { 
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/quiz/index.php?id='.$old_id,$result);
                }
            }
        }

        //Link to quiz view by moduleid

        $searchstring='/\$@(QUIZVIEWBYID)\*([0-9]+)@\$/';
        //We look for it
        preg_match_all($searchstring,$result,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //print_object($foundset);                                     //Debug
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (course_modules id)
                $rec = backup_getid($restore->backup_unique_code,"course_modules",$old_id);
                //Personalize the searchstring
                $searchstring='/\$@(QUIZVIEWBYID)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/quiz/view.php?id='.$rec->new_id,$result);
                } else {
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/quiz/view.php?id='.$old_id,$result);
                }
            }
        }

        return $result;
    }

    //This function makes all the necessary calls to xxxx_decode_content_links()
    //function in each module, passing them the desired contents to be decoded
    //from backup format to destination site/course in order to mantain inter-activities
    //working in the backup/restore process. It's called from restore_decode_content_links()
    //function in restore process
    function quiz_decode_content_links_caller($restore) {
        global $CFG;
        $status = true;
        
        if ($quizs = get_records_sql ("SELECT q.id, q.intro
                                   FROM {$CFG->prefix}quiz q
                                   WHERE q.course = $restore->course_id")) {
                                               //Iterate over each quiz->intro
            $i = 0;   //Counter to send some output to the browser to avoid timeouts
            foreach ($quizs as $quiz) {
                //Increment counter
                $i++;
                $content = $quiz->intro;
                $result = restore_decode_content_links_worker($content,$restore);
                if ($result != $content) {
                    //Update record
                    $quiz->intro = addslashes($result);
                    $status = update_record("quiz",$quiz);
                    if ($CFG->debug>7) {
                        echo '<br /><hr />'.htmlentities($content).'<br />changed to<br />'.htmlentities($result).'<hr /><br />';
                    }
                }
                //Do some output
                if (($i+1) % 5 == 0) {
                    echo ".";
                    if (($i+1) % 100 == 0) {
                        echo "<br />";
                    }
                    backup_flush(300);
                    }
            }
        }

        return $status;
    }

    //This function converts texts in FORMAT_WIKI to FORMAT_MARKDOWN for
    //some texts in the module
    function quiz_restore_wiki2markdown ($restore) {

        global $CFG;

        $status = true;

        //Convert quiz_questions->questiontext
        if ($records = get_records_sql ("SELECT q.id, q.questiontext, q.questiontextformat
                                         FROM {$CFG->prefix}quiz_questions q,
                                              {$CFG->prefix}backup_ids b
                                         WHERE b.backup_code = $restore->backup_unique_code AND
                                               b.table_name = 'quiz_questions' AND
                                               q.id = b.new_id AND
                                               q.questiontextformat = ".FORMAT_WIKI)) {
            foreach ($records as $record) {
                //Rebuild wiki links
                $record->questiontext = restore_decode_wiki_content($record->questiontext, $restore);
                //Convert to Markdown
                $wtm = new WikiToMarkdown();
                $record->questiontext = $wtm->convert($record->questiontext, $restore->course_id);
                $record->questiontextformat = FORMAT_MARKDOWN;
                $status = update_record('quiz_questions', addslashes_object($record));
                //Do some output
                $i++;
                if (($i+1) % 1 == 0) {
                    echo ".";
                    if (($i+1) % 20 == 0) {
                        echo "<br />";
                    }
                    backup_flush(300);
                }
            }
        }
        return $status;
    }

    //This function returns a log record with all the necessay transformations
    //done. It's used by restore_log_module() to restore modules log.
    function quiz_restore_logs($restore,$log) {

        $status = false;

        //Depending of the action, we recode different things
        switch ($log->action) {
        case "add":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "update":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "view":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "view all":
            $log->url = "index.php?id=".$log->course;
            $status = true;
            break;
        case "report":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "report.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        case "attempt":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    //Extract the attempt id from the url field
                    $attid = substr(strrchr($log->url,"="),1);
                    //Get the new_id of the attempt (to recode the url field)
                    $att = backup_getid($restore->backup_unique_code,"quiz_attempts",$attid);
                    if ($att) {
                        $log->url = "review.php?id=".$log->cmid."&attempt=".$att->new_id;
                        $log->info = $mod->new_id;
                        $status = true;
                    }
                }
            }
            break;
        case "submit":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    //Extract the attempt id from the url field
                    $attid = substr(strrchr($log->url,"="),1);
                    //Get the new_id of the attempt (to recode the url field)
                    $att = backup_getid($restore->backup_unique_code,"quiz_attempts",$attid);
                    if ($att) {
                        $log->url = "review.php?id=".$log->cmid."&attempt=".$att->new_id;
                        $log->info = $mod->new_id;
                        $status = true;
                    }
                }
            }
            break;
        case "review":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the info field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    //Extract the attempt id from the url field
                    $attid = substr(strrchr($log->url,"="),1);
                    //Get the new_id of the attempt (to recode the url field)
                    $att = backup_getid($restore->backup_unique_code,"quiz_attempts",$attid);
                    if ($att) {
                        $log->url = "review.php?id=".$log->cmid."&attempt=".$att->new_id;
                        $log->info = $mod->new_id;
                        $status = true;
                    }
                }
            }
            break;
        case "editquestions":
            if ($log->cmid) {
                //Get the new_id of the module (to recode the url field)
                $mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
                if ($mod) {
                    $log->url = "view.php?id=".$log->cmid;
                    $log->info = $mod->new_id;
                    $status = true;
                }
            }
            break;
        default:
            echo "action (".$log->module."-".$log->action.") unknow. Not restored<br />";                 //Debug
            break;
        }

        if ($status) {
            $status = $log;
        }
        return $status;
    }

    function quiz_recode_layout($layout, $restore) {
        //Recodes the quiz layout (a list of questions id and pagebreaks)

    //Extracts question id from sequence
    if ($questionids = explode(',', $layout)) {
        foreach ($questionids as $id => $questionid) {
        if ($questionid) { // If it iss zero then this is a pagebreak, don't translate
            $newq = backup_getid($restore->backup_unique_code,"quiz_questions",$questionid);
            $questionids[$id] = $newq->new_id;
        }
        }
    }
    return implode(',', $questionids);
    }

?>
