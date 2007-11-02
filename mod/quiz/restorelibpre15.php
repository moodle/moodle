<?php // $Id$
    //This php script contains all the stuff to backup/restore
    //quiz mods

    //To see, put your terminal to 160cc

    //This is the "graphical" structure of the quiz mod:
    //
    //                           quiz                                                      question_categories
    //                        (CL,pk->id)                                                   (CL,pk->id)
    //                            |                                                              |
    //           -------------------------------------------------------------------             |
    //           |                        |                    |                   |             |.......................................
    //           |                        |                    |                   |             |                                      .
    //           |                        |                    |                   |             |                                      .
    //      quiz_attempts        quiz_grades       quiz_question_grades   quiz_question_versions |    ----question_datasets----    .
    // (UL,pk->id, fk->quiz) (UL,pk->id,fk->quiz)  (CL,pk->id,fk->quiz)    (CL,pk->id,fk->quiz)  |    |  (CL,pk->id,fk->question,  |    .
    //             |                                              |                      .       |    |   fk->dataset_definition)  |    .
    //             |                                              |                      .       |    |                            |    .
    //             |                                              |                      .       |    |                            |    .
    //             |                                              |                      .       |    |                            |    .
    //       quiz_responses                                       |                      question                       question_dataset_definitions
    //  (UL,pk->id, fk->attempt)----------------------------------------------------(CL,pk->id,fk->category,files)            (CL,pk->id,fk->category)
    //                                                                                           |                                      |
    //                                                                                           |                                      |
    //                                                                                           |                                      |
    //                                                                                           |                               question_dataset_items
    //                                                                                           |                            (CL,pk->id,fk->definition)
    //                                                                                           |
    //                                                                                           |
    //                                                                                           |
    //             --------------------------------------------------------------------------------------------------------------
    //             |             |              |              |                       |                  |                     |
    //             |             |              |              |                       |                  |                     |
    //             |             |              |              |                 question_calculated          |                     |    question_randomsamatch
    //      question_truefalse       |       question_multichoice      |             (CL,pl->id,fk->question)     |                     |--(CL,pl->id,fk->question)
    // (CL,pl->id,fk->question)  |   (CL,pl->id,fk->question)  |                       .                  |                     |
    //             .             |              .              |                       .                  |                     |
    //             .      question_shortanswer      .       question_numerical                 .            question_multianswer.           |
    //             .  (CL,pl->id,fk->question)  .  (CL,pl->id,fk->question)            .        (CL,pl->id,fk->question)        |         question_match
    //             .             .              .              .                       .                  .                     |--(CL,pl->id,fk->question)
    //             .             .              .              .                       .                  .                     |             .
    //             .             .              .              .                       .                  .                     |             .
    //             .             .              .              .                       .                  .                     |             .
    //             .             .              .              .                       .                  .                     |       question_match_sub
    //             .             .              .              .                       .                  .                     |--(CL,pl->id,fk->question)
    //             ........................................................................................                     |
    //                                                   .                                                                      |
    //                                                   .                                                                      |
    //                                                   .                                                                      |    question_numerical_units
    //                                                question_answers                                                              |--(CL,pl->id,fk->question)
    //                                         (CL,pk->id,fk->question)----------------------------------------------------------
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
    //     - question_categories
    //     - question
    //     - question_truefalse
    //     - question_shortanswer
    //     - question_multianswer
    //     - question_multichoice
    //     - question_numerical
    //     - question_randomsamatch
    //     - question_match
    //     - question_match_sub
    //     - question_calculated
    //     - question_answers
    //     - question_numerical_units
    //     - question_datasets
    //     - question_dataset_definitions
    //     - question_dataset_items
    //    All this backup info have its own section in moodle.xml (QUESTION_CATEGORIES) and it's generated
    //    before every module backup standard invocation. And only if to restore quizzes has been selected !!
    //    It's invoked with quiz_restore_question_categories. (course independent).

    // 2.-Standard module restore (Invoked via quiz_restore_mods). It includes this tables:
    //     - quiz
    //     - quiz_question_versions
    //     - quiz_question_grades
    //     - quiz_attempts
    //     - quiz_grades
    //     - quiz_responses
    //    This step is the standard mod backup. (course dependent).

    //We are going to nedd quiz libs to be able to mimic the upgrade process
    require_once("$CFG->dirroot/mod/quiz/locallib.php");

    //STEP 1. Restore categories/questions and associated structures
    //    (course independent)
    function quiz_restore_pre15_question_categories($category,$restore) {

        global $CFG;

        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,"question_categories",$category->id);

        if ($data) {
            //Now get completed xmlized object
            $info = $data->info;
            //traverse_xmlize($info);                                                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the question_categories record structure
            $quiz_cat->course = $restore->course_id;
            $quiz_cat->name = backup_todb($info['QUESTION_CATEGORY']['#']['NAME']['0']['#']);
            $quiz_cat->info = backup_todb($info['QUESTION_CATEGORY']['#']['INFO']['0']['#']);
            $quiz_cat->publish = backup_todb($info['QUESTION_CATEGORY']['#']['PUBLISH']['0']['#']);
            $quiz_cat->stamp = backup_todb($info['QUESTION_CATEGORY']['#']['STAMP']['0']['#']);
            $quiz_cat->parent = backup_todb($info['QUESTION_CATEGORY']['#']['PARENT']['0']['#']);
            $quiz_cat->sortorder = backup_todb($info['QUESTION_CATEGORY']['#']['SORTORDER']['0']['#']);

            if ($catfound = restore_get_best_question_category($quiz_cat, $restore->course_id)) {
                $newid = $catfound;
            } else {
                if (!$quiz_cat->stamp) {
                    $quiz_cat->stamp = make_unique_id_code();   
                }
                $newid = insert_record ("question_categories",$quiz_cat);
            }

            //Do some output
            if ($newid) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>".get_string('category', 'quiz')." \"".$quiz_cat->name."\"<br />";
                }
            } else {
                if (!defined('RESTORE_SILENTLY')) {
                    //We must never arrive here !!
                    echo "<li>".get_string('category', 'quiz')." \"".$quiz_cat->name."\" Error!<br />";
                }
                $status = false;
            }
            backup_flush(300);

            //Here category has been created or selected, so save results in backup_ids and start with questions
            if ($newid and $status) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"question_categories",
                             $category->id, $newid);
                //Now restore question
                $status = quiz_restore_pre15_questions ($category->id, $newid,$info,$restore);
            } else {
                $status = false;
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        return $status;
    }

    function quiz_restore_pre15_questions ($old_category_id,$new_category_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the questions array
        $questions = $info['QUESTION_CATEGORY']['#']['QUESTIONS']['0']['#']['QUESTION'];

        //Iterate over questions
        for($i = 0; $i < sizeof($questions); $i++) {
            $question = new object;
            $que_info = $questions[$i];
            //traverse_xmlize($que_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($que_info['#']['ID']['0']['#']);

            //Now, build the question record structure
            $question->category = $new_category_id;
            $question->parent = backup_todb($que_info['#']['PARENT']['0']['#']);
            $question->name = backup_todb($que_info['#']['NAME']['0']['#']);
            $question->questiontext = backup_todb($que_info['#']['QUESTIONTEXT']['0']['#']);
            $question->questiontextformat = backup_todb($que_info['#']['QUESTIONTEXTFORMAT']['0']['#']);
            $question->image = backup_todb($que_info['#']['IMAGE']['0']['#']);
            $question->defaultgrade = backup_todb($que_info['#']['DEFAULTGRADE']['0']['#']);
            if (isset($que_info['#']['PENALTY']['0']['#'])) { //Only if it's set, to apply DB default else.
                $question->penalty = backup_todb($que_info['#']['PENALTY']['0']['#']);
            }
            $question->qtype = backup_todb($que_info['#']['QTYPE']['0']['#']);
            if (isset($que_info['#']['LENGTH']['0']['#'])) { //Only if it's set, to apply DB default else.
                $question->length = backup_todb($que_info['#']['LENGTH']['0']['#']);
            }
            $question->stamp = backup_todb($que_info['#']['STAMP']['0']['#']);
            if (isset($que_info['#']['VERSION']['0']['#'])) { //Only if it's set, to apply DB default else.
                $question->version = backup_todb($que_info['#']['VERSION']['0']['#']);
            }
            if (isset($que_info['#']['HIDDEN']['0']['#'])) { //Only if it's set, to apply DB default else.
                $question->hidden = backup_todb($que_info['#']['HIDDEN']['0']['#']);
            }

            //Although only a few backups can have questions with parent, we try to recode it
            //if it contains something
            if ($question->parent and $parent = backup_getid($restore->backup_unique_code,"question",$question->parent)) {
                $question->parent = $parent->new_id;
            }

            // If it is a random question then hide it
            if ($question->qtype == RANDOM) {
                $question->hidden = 1;
            }

            //If it is a description question, length = 0
            if ($question->qtype == DESCRIPTION) {
                $question->length = 0;
            }

            //Check if the question exists
            //by category and stamp
            $question_exists = get_record ("question","category",$question->category,
                                                            "stamp",$question->stamp);
            //If the stamp doesn't exists, check if question exists
            //by category, name and questiontext and calculate stamp
            //Mantains pre Beta 1.1 compatibility !!
            if (!$question->stamp) {
                $question->stamp = make_unique_id_code();
                $question->version = 1;
                $question_exists = get_record ("question","category",$question->category,
                                                                "name",$question->name,
                                                                "questiontext",$question->questiontext);
            }

            //If the question exists, only record its id
            if ($question_exists) {
                $newid = $question_exists->id;
                $creatingnewquestion = false;
            //Else, create a new question
            } else {
                //The structure is equal to the db, so insert the question
                $newid = insert_record ("question",$question);
                //If it is a random question, parent = id
                if ($newid && $question->qtype == RANDOM) {
                    set_field ('question', 'parent', $newid, 'id', $newid);
                }
                $creatingnewquestion = true;
            }

            //Do some output
            if (($i+1) % 2 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 40 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }
            //Save newid to backup tables
            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"question",$oldid,
                             $newid);
            }
            //If it's a new question in the DB, restore it
            if ($creatingnewquestion) {
                //Now, restore every question_answers in this question
                $status = quiz_restore_pre15_answers($oldid,$newid,$que_info,$restore);
                //Now, depending of the type of questions, invoke different functions
                if ($question->qtype == "1") {
                    $status = quiz_restore_pre15_shortanswer($oldid,$newid,$que_info,$restore);
                } else if ($question->qtype == "2") {
                    $status = quiz_restore_pre15_truefalse($oldid,$newid,$que_info,$restore);
                } else if ($question->qtype == "3") {
                    $status = quiz_restore_pre15_multichoice($oldid,$newid,$que_info,$restore);
                } else if ($question->qtype == "4") {
                    //Random question. Nothing to do.
                } else if ($question->qtype == "5") {
                    $status = quiz_restore_pre15_match($oldid,$newid,$que_info,$restore);
                } else if ($question->qtype == "6") {
                    $status = quiz_restore_pre15_randomsamatch($oldid,$newid,$que_info,$restore);
                } else if ($question->qtype == "7") {
                    //Description question. Nothing to do.
                } else if ($question->qtype == "8") {
                    $status = quiz_restore_pre15_numerical($oldid,$newid,$que_info,$restore);
                } else if ($question->qtype == "9") {
                    $status = quiz_restore_pre15_multianswer($oldid,$newid,$que_info,$restore);
                } else if ($question->qtype == "10") {
                    $status = quiz_restore_pre15_calculated($oldid,$newid,$que_info,$restore);
                }
            } else {
                //We are NOT creating the question, but we need to know every question_answers
                //map between the XML file and the database to be able to restore the responses
                //in each attempt.
                $status = quiz_restore_pre15_map_answers($oldid,$newid,$que_info,$restore);
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
                    $status = quiz_restore_pre15_map_match($oldid,$newid,$que_info,$restore);
                } else if ($question->qtype == "6") {
                    //Randomsamatch question. Nothing to remap
                } else if ($question->qtype == "7") {
                    //Description question. Nothing to remap
                } else if ($question->qtype == "8") {
                    //Numerical question. Nothing to remap
                } else if ($question->qtype == "9") {
                    //Multianswer question. Nothing to remap
                } else if ($question->qtype == "10") {
                    //Calculated question. Nothing to remap
                }
            }
        }
        return $status;
    }

    function quiz_restore_pre15_answers ($old_question_id,$new_question_id,$info,$restore) {

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

                //Now, build the question_answers record structure
                $answer->question = $new_question_id;
                $answer->answer = backup_todb($ans_info['#']['ANSWER_TEXT']['0']['#']);
                $answer->fraction = backup_todb($ans_info['#']['FRACTION']['0']['#']);
                $answer->feedback = backup_todb($ans_info['#']['FEEDBACK']['0']['#']);

                //The structure is equal to the db, so insert the question_answers
                $newid = insert_record ("question_answers",$answer);

                //Do some output
                if (($i+1) % 50 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 1000 == 0) {
                            echo "<br />";
                        }
                    }
                    backup_flush(300);
                }

                if ($newid) {
                    //We have the newid, update backup_ids
                    backup_putid($restore->backup_unique_code,"question_answers",$oldid,
                                 $newid);
                } else {
                    $status = false;
                }
            }
        }

        return $status;
    }

    function quiz_restore_pre15_map_answers ($old_question_id,$new_question_id,$info,$restore) {

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

            //Now, build the question_answers record structure
            $answer->question = $new_question_id;
            $answer->answer = backup_todb($ans_info['#']['ANSWER_TEXT']['0']['#']);
            $answer->fraction = backup_todb($ans_info['#']['FRACTION']['0']['#']);
            $answer->feedback = backup_todb($ans_info['#']['FEEDBACK']['0']['#']);

            //If we are in this method is because the question exists in DB, so its
            //answers must exist too.
            //Now, we are going to look for that answer in DB and to create the
            //mappings in backup_ids to use them later where restoring responses (user level).

            //Get the answer from DB (by question, answer and fraction)
            $db_answer = get_record ("question_answers","question",$new_question_id,
                                                    "answer",$answer->answer,
                                                    "fraction",$answer->fraction);

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }

            if ($db_answer) {
                //We have the database answer, update backup_ids
                backup_putid($restore->backup_unique_code,"question_answers",$oldid,
                             $db_answer->id);
            } else {
                $status = false;
            }
        }

        return $status;
    }

    function quiz_restore_pre15_shortanswer ($old_question_id,$new_question_id,$info,$restore,$restrictto = '') {

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

            //Now, build the question_shortanswer record structure
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
                $answer = backup_getid($restore->backup_unique_code,"question_answers",$tok);
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

            //The structure is equal to the db, so insert the question_shortanswer
            //Only if there aren't restrictions or there are restriction concordance
            if (empty($restrictto) || (!empty($restrictto) && $shortanswer->answers == $restrictto)) {
                $newid = insert_record ("question_shortanswer",$shortanswer);
            } 

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }

            if (!$newid && !$restrictto) {
                $status = false;
            }
        }

        return $status;
    }

    function quiz_restore_pre15_truefalse ($old_question_id,$new_question_id,$info,$restore) {

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

            //Now, build the question_truefalse record structure
            $truefalse->question = $new_question_id;
            $truefalse->trueanswer = backup_todb($tru_info['#']['TRUEANSWER']['0']['#']);
            $truefalse->falseanswer = backup_todb($tru_info['#']['FALSEANSWER']['0']['#']);

            ////We have to recode the trueanswer field
            $answer = backup_getid($restore->backup_unique_code,"question_answers",$truefalse->trueanswer);
            if ($answer) {
                $truefalse->trueanswer = $answer->new_id;
            }

            ////We have to recode the falseanswer field
            $answer = backup_getid($restore->backup_unique_code,"question_answers",$truefalse->falseanswer);
            if ($answer) {
                $truefalse->falseanswer = $answer->new_id;
            }

            //The structure is equal to the db, so insert the question_truefalse
            $newid = insert_record ("question_truefalse",$truefalse);

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }

    function quiz_restore_pre15_multichoice ($old_question_id,$new_question_id,$info,$restore, $restrictto = '') {

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

            //Now, build the question_multichoice record structure
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
                $answer = backup_getid($restore->backup_unique_code,"question_answers",$tok);
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

            //The structure is equal to the db, so insert the question_shortanswer
            //Only if there aren't restrictions or there are restriction concordance
            if (empty($restrictto) || (!empty($restrictto) && $multichoice->answers == $restrictto)) {
                $newid = insert_record ("question_multichoice",$multichoice);
            }

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }

            if (!$newid && !$restrictto) {
                $status = false;
            }
        }

        return $status;
    }

    function quiz_restore_pre15_match ($old_question_id,$new_question_id,$info,$restore) {

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

            //Now, build the question_match_SUB record structure
            $match_sub->question = $new_question_id;
            $match_sub->questiontext = backup_todb($mat_info['#']['QUESTIONTEXT']['0']['#']);
            $match_sub->answertext = backup_todb($mat_info['#']['ANSWERTEXT']['0']['#']);

            //The structure is equal to the db, so insert the question_match_sub
            $newid = insert_record ("question_match_sub",$match_sub);

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"question_match_sub",$oldid,
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

        //The structure is equal to the db, so insert the question_match_sub
        $newid = insert_record ("question_match",$match);

        if (!$newid) {
            $status = false;
        }

        return $status;
    }

    function quiz_restore_pre15_map_match ($old_question_id,$new_question_id,$info,$restore) {

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

            //Now, build the question_match_SUB record structure
            $match_sub->question = $new_question_id;
            $match_sub->questiontext = backup_todb($mat_info['#']['QUESTIONTEXT']['0']['#']);
            $match_sub->answertext = backup_todb($mat_info['#']['ANSWERTEXT']['0']['#']);

            //If we are in this method is because the question exists in DB, so its
            //match_sub must exist too.
            //Now, we are going to look for that match_sub in DB and to create the
            //mappings in backup_ids to use them later where restoring responses (user level).

            //Get the match_sub from DB (by question, questiontext and answertext)
            $db_match_sub = get_record ("question_match_sub","question",$new_question_id,
                                                      "questiontext",$match_sub->questiontext,
                                                      "answertext",$match_sub->answertext);
            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }

            //We have the database match_sub, so update backup_ids
            if ($db_match_sub) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"question_match_sub",$oldid,
                             $db_match_sub->id);
            } else {
                $status = false;
            }
        }

        return $status;
    }

    function quiz_restore_pre15_randomsamatch ($old_question_id,$new_question_id,$info,$restore) {

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

            //Now, build the question_randomsamatch record structure
            $randomsamatch->question = $new_question_id;
            $randomsamatch->choose = backup_todb($ran_info['#']['CHOOSE']['0']['#']);

            //The structure is equal to the db, so insert the question_randomsamatch
            $newid = insert_record ("question_randomsamatch",$randomsamatch);

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }

    function quiz_restore_pre15_numerical ($old_question_id,$new_question_id,$info,$restore, $restrictto = '') {

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

            //Now, build the question_numerical record structure
            $numerical->question = $new_question_id;
            $numerical->answer = backup_todb($num_info['#']['ANSWER']['0']['#']);
            $numerical->min = backup_todb($num_info['#']['MIN']['0']['#']);
            $numerical->max = backup_todb($num_info['#']['MAX']['0']['#']);

            ////We have to recode the answer field
            $answer = backup_getid($restore->backup_unique_code,"question_answers",$numerical->answer);
            if ($answer) {
                $numerical->answer = $answer->new_id;
            }

            //Answer goes to answers in 1.5 (although it continues being only one!)
            //Changed 12-05 (chating with Gustav and Julian this remains = pre15 = answer)
            //$numerical->answers = $numerical->answer;

            //We have to calculate the tolerance field of the numerical question
            $numerical->tolerance = ($numerical->max - $numerical->min)/2;

            //The structure is equal to the db, so insert the question_numerical
            //Only if there aren't restrictions or there are restriction concordance
            if (empty($restrictto) || (!empty($restrictto) && in_array($numerical->answer,explode(",",$restrictto)))) {
                $newid = insert_record ("question_numerical",$numerical);
            }

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }

            //Now restore numerical_units
            if ($newid) {
                $status = quiz_restore_pre15_numerical_units ($old_question_id,$new_question_id,$num_info,$restore);
            }

            if (!$newid && !$restrictto) {
                $status = false;
            }
        }

        return $status;
    }

    function quiz_restore_pre15_calculated ($old_question_id,$new_question_id,$info,$restore) {

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

            //Now, build the question_calculated record structure
            $calculated->question = $new_question_id;
            $calculated->answer = backup_todb($cal_info['#']['ANSWER']['0']['#']);
            $calculated->tolerance = backup_todb($cal_info['#']['TOLERANCE']['0']['#']);
            $calculated->tolerancetype = backup_todb($cal_info['#']['TOLERANCETYPE']['0']['#']);
            $calculated->correctanswerlength = backup_todb($cal_info['#']['CORRECTANSWERLENGTH']['0']['#']);
            $calculated->correctanswerformat = backup_todb($cal_info['#']['CORRECTANSWERFORMAT']['0']['#']);

            ////We have to recode the answer field
            $answer = backup_getid($restore->backup_unique_code,"question_answers",$calculated->answer);
            if ($answer) {
                $calculated->answer = $answer->new_id;
            }

            //If we haven't correctanswerformat, it defaults to 2 (in DB)
            if (empty($calculated->correctanswerformat)) {
                $calculated->correctanswerformat = 2;
            }

            //The structure is equal to the db, so insert the question_calculated
            $newid = insert_record ("question_calculated",$calculated);

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }

            //Now restore numerical_units
            $status = quiz_restore_pre15_numerical_units ($old_question_id,$new_question_id,$cal_info,$restore);

            //Now restore dataset_definitions
            if ($status && $newid) {
                $status = quiz_restore_pre15_dataset_definitions ($old_question_id,$new_question_id,$cal_info,$restore);
            }

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }

    function quiz_restore_pre15_multianswer ($old_question_id,$new_question_id,$info,$restore) {

        global $CFG;

        $status = true;

        //We need some question fields here so we get the full record from DB
        $parentquestion = get_record('question','id',$new_question_id);

        //We need to store all the positions with their created questions
        //to be able to calculate the sequence field
        $createdquestions = array();

        //Under 1.5, every multianswer record becomes a question itself
        //with its parent set to the cloze question. And there is only
        //ONE multianswer record with the sequence of questions used.

        //Get the multianswers array
        $multianswers_array = $info['#']['MULTIANSWERS']['0']['#']['MULTIANSWER'];
        //Iterate over multianswers_array
        for($i = 0; $i < sizeof($multianswers_array); $i++) {
            $mul_info = $multianswers_array[$i];
            //traverse_xmlize($mul_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We need this later
            $oldid = backup_todb($mul_info['#']['ID']['0']['#']);

            //Now, build the question_multianswer record structure
            $multianswer->question = $new_question_id;
            $multianswer->answers = backup_todb($mul_info['#']['ANSWERS']['0']['#']);
            $multianswer->positionkey = backup_todb($mul_info['#']['POSITIONKEY']['0']['#']);
            $multianswer->answertype = backup_todb($mul_info['#']['ANSWERTYPE']['0']['#']);
            $multianswer->norm = backup_todb($mul_info['#']['NORM']['0']['#']);

            //Saving multianswer and positionkey to use them later restoring states
            backup_putid ($restore->backup_unique_code,'multianswer-pos',$oldid,$multianswer->positionkey);

            //We have to recode all the answers to their new ids
            $ansarr = explode(",", $multianswer->answers);
            foreach ($ansarr as $key => $value) {
                //Get the answer from backup_ids
                $answer = backup_getid($restore->backup_unique_code,'question_answers',$value);
                $ansarr[$key] = $answer->new_id;
            }
            $multianswer->answers = implode(",",$ansarr);

            //Build the new question structure
            $question = new object;
            $question->category           = $parentquestion->category;
            $question->parent             = $parentquestion->id;
            $question->name               = $parentquestion->name;
            $question->questiontextformat = $parentquestion->questiontextformat;
            $question->defaultgrade       = $multianswer->norm;
            $question->penalty            = $parentquestion->penalty;
            $question->qtype              = $multianswer->answertype;
            $question->version            = $parentquestion->version;
            $question->hidden             = $parentquestion->hidden;
            $question->length             = 0;
            $question->questiontext       = '';
            $question->stamp              = make_unique_id_code();

            //Save the new question to DB
            $newid = insert_record('question', $question);

            if ($newid) {
                $createdquestions[$multianswer->positionkey] = $newid;
            }

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }

            //Remap question_answers records from the original multianswer question
            //to their newly created question
            if ($newid) {
                $answersdb = get_records_list('question_answers','id',$multianswer->answers);
                foreach ($answersdb as $answerdb) {
                    set_field('question_answers','question',$newid,'id',$answerdb->id);
                }
            }

            //If we have created the question record, now, depending of the
            //answertype, delegate the restore to every qtype function
            if ($newid) {
                if ($multianswer->answertype == "1") {
                    $status = quiz_restore_pre15_shortanswer ($old_question_id,$newid,$mul_info,$restore,$multianswer->answers);
                } else if ($multianswer->answertype == "3") {
                    $status = quiz_restore_pre15_multichoice ($old_question_id,$newid,$mul_info,$restore,$multianswer->answers);
                } else if ($multianswer->answertype == "8") {
                    $status = quiz_restore_pre15_numerical ($old_question_id,$newid,$mul_info,$restore,$multianswer->answers);
                }
            } else {
                $status = false;
            }
        }

        //Everything is created, just going to create the multianswer record
        if ($status) {
            ksort($createdquestions);
           
            $multianswerdb = new object;
            $multianswerdb->question = $parentquestion->id;
            $multianswerdb->sequence = implode(",",$createdquestions);
            $mid = insert_record('question_multianswer', $multianswerdb);
  
            if (!$mid) {
                $status = false;
            }
        }

        return $status;
    }

    function quiz_restore_pre15_numerical_units ($old_question_id,$new_question_id,$info,$restore) {

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

            //Now, build the question_numerical_UNITS record structure
            $numerical_unit->question = $new_question_id;
            $numerical_unit->multiplier = backup_todb($nu_info['#']['MULTIPLIER']['0']['#']);
            $numerical_unit->unit = backup_todb($nu_info['#']['UNIT']['0']['#']);

            //The structure is equal to the db, so insert the question_numerical_units
            $newid = insert_record ("question_numerical_units",$numerical_unit);

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }

    function quiz_restore_pre15_dataset_definitions ($old_question_id,$new_question_id,$info,$restore) {

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

            //Now, build the question_dataset_DEFINITION record structure
            $dataset_definition->category = backup_todb($dd_info['#']['CATEGORY']['0']['#']);
            $dataset_definition->name = backup_todb($dd_info['#']['NAME']['0']['#']);
            $dataset_definition->type = backup_todb($dd_info['#']['TYPE']['0']['#']);
            $dataset_definition->options = backup_todb($dd_info['#']['OPTIONS']['0']['#']);
            $dataset_definition->itemcount = backup_todb($dd_info['#']['ITEMCOUNT']['0']['#']);

            //We have to recode the category field (only if the category != 0)
            if ($dataset_definition->category != 0) {
                $category = backup_getid($restore->backup_unique_code,"question_categories",$dataset_definition->category);
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
                                                     FROM {$CFG->prefix}question_dataset_definitions d
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
                //The structure is equal to the db, so insert the question_dataset_definitions
                $newid = insert_record ("question_dataset_definitions",$dataset_definition);
                if ($newid) {
                    //Restore question_dataset_items
                    $status = quiz_restore_pre15_dataset_items($newid,$dd_info,$restore);
                }
            }

            //Now, we must have a definition (created o reused). Its id is in newid. Create the question_datasets record
            //to join the question and the dataset_definition
            if ($newid) {
                $question_dataset->question = $new_question_id;
                $question_dataset->datasetdefinition = $newid;
                $newid = insert_record ("question_datasets",$question_dataset);
            }

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }

    function quiz_restore_pre15_dataset_items ($definitionid,$info,$restore) {

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

            //Now, build the question_dataset_ITEMS record structure
            $dataset_item->definition = $definitionid;
            $dataset_item->itemnumber = backup_todb($di_info['#']['NUMBER']['0']['#']);
            $dataset_item->value = backup_todb($di_info['#']['VALUE']['0']['#']);

            //The structure is equal to the db, so insert the question_dataset_items
            $newid = insert_record ("question_dataset_items",$dataset_item);

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }

    //STEP 2. Restore quizzes and associated structures
    //    (course dependent)
    function quiz_restore_pre15_mods($mod,$restore) {

        global $CFG;

        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

        if ($data) {
            //Now get completed xmlized object
            $info = $data->info;
            //if necessary, write to restorelog and adjust date/time fields
            if ($restore->course_startdateoffset) {
                restore_log_date_changes('Quiz', $restore, $info['MOD']['#'], array('TIMEOPEN', 'TIMECLOSE'));
            }            
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
            $quiz->attemptonlast = backup_todb($info['MOD']['#']['ATTEMPTONLAST']['0']['#']);
            $quiz->feedback = backup_todb($info['MOD']['#']['FEEDBACK']['0']['#']);
            $quiz->correctanswers = backup_todb($info['MOD']['#']['CORRECTANSWERS']['0']['#']);
            $quiz->grademethod = backup_todb($info['MOD']['#']['GRADEMETHOD']['0']['#']);
            if (isset($info['MOD']['#']['DECIMALPOINTS']['0']['#'])) { //Only if it's set, to apply DB default else.
                $quiz->decimalpoints = backup_todb($info['MOD']['#']['DECIMALPOINTS']['0']['#']);
            }
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

            //We have to recode the questions field (a list of questions id)
            $newquestions = array();
            if ($questionsarr = explode (",",$quiz->questions)) {
                foreach ($questionsarr as $key => $value) {
                    if ($question = backup_getid($restore->backup_unique_code,"question",$value)) {
                        $newquestions[] = $question->new_id;
                    }
                }
            }
            $quiz->questions = implode (",", $newquestions);

            //Recalculate the questions field to include page breaks if necessary
            $quiz->questions = quiz_repaginate($quiz->questions, $quiz->questionsperpage);

            //Calculate the new review field contents (logic extracted from upgrade)
            $review = (QUIZ_REVIEW_IMMEDIATELY & (QUIZ_REVIEW_RESPONSES + QUIZ_REVIEW_SCORES));
            if ($quiz->feedback) {
                $review += (QUIZ_REVIEW_IMMEDIATELY & QUIZ_REVIEW_FEEDBACK);
            }
            if ($quiz->correctanswers) {
                $review += (QUIZ_REVIEW_IMMEDIATELY & QUIZ_REVIEW_ANSWERS);
            }
            if ($quiz->review & 1) {
                $review += QUIZ_REVIEW_CLOSED;
            }
            if ($quiz->review & 2) {
                $review += QUIZ_REVIEW_OPEN;
            }
            $quiz->review = $review;

            //The structure is equal to the db, so insert the quiz
            $newid = insert_record ("quiz",$quiz);

            //Do some output
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("modulename","quiz")." \"".format_string(stripslashes($quiz->name),true)."\"</li>";
            }
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //We have to restore the quiz_question_instances now (old quiz_question_grades, course level)
                $status = quiz_question_instances_restore_pre15_mods($newid,$info,$restore);
                //We have to restore the question_versions now (course level table)
                $status = quiz_question_versions_restore_pre15_mods($newid,$info,$restore);
                //Now check if want to restore user data and do it.
                if (restore_userdata_selected($restore,'quiz',$mod->id)) {
                    //Restore quiz_attempts
                    $status = quiz_attempts_restore_pre15_mods ($newid,$info,$restore, $quiz->questions);
                    if ($status) {
                        //Restore quiz_grades
                        $status = quiz_grades_restore_pre15_mods ($newid,$info,$restore);
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

    //This function restores the quiz_question_instances (old quiz_question_grades)
    function quiz_question_instances_restore_pre15_mods($quiz_id,$info,$restore) {

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
            $question = backup_getid($restore->backup_unique_code,"question",$grade->question);
            if ($question) {
                $grade->question = $question->new_id;
            }

            //The structure is equal to the db, so insert the quiz_question_grades
            $newid = insert_record ("quiz_question_instances",$grade);

            //Do some output
            if (($i+1) % 10 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 200 == 0) {
                        echo "<br />";
                    }
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
    function quiz_question_versions_restore_pre15_mods($quiz_id,$info,$restore) {

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
            $question = backup_getid($restore->backup_unique_code,"question",$version->oldquestion);
            if ($question) {
                $version->oldquestion = $question->new_id;
            }

            //We have to recode the newquestion field
            $question = backup_getid($restore->backup_unique_code,"question",$version->newquestion);
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
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 200 == 0) {
                        echo "<br />";
                    }
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
    function quiz_attempts_restore_pre15_mods($quiz_id,$info,$restore,$quizquestions) {

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

            //Set the layout field (inherited from quiz by default)
            $attempt->layout = $quizquestions;

            //Set the preview field (code from upgrade)
            $cm = get_coursemodule_from_instance('quiz', $quiz_id);
            if (has_capability('mod/quiz:preview', get_context_instance(CONTEXT_MODULE, $cm->id))) {
                $attempt->preview = 1;
            }

            //Set the uniqueid field
            $attempt->uniqueid = question_new_attempt_uniqueid();

            //The structure is equal to the db, so insert the quiz_attempts
            $newid = insert_record ("quiz_attempts",$attempt);

            //Do some output
            if (($i+1) % 10 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 200 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"quiz_attempts",$oldid,
                             $newid);
                //Now process question_states (old quiz_responses table)
                $status = question_states_restore_pre15_mods($newid,$att_info,$restore);
            } else {
                $status = false;
            }
        }

        return $status;
    }

    //This function restores the question_states (old quiz_responses)
    function question_states_restore_pre15_mods($attempt_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the quiz_responses array
        $responses = $info['#']['RESPONSES']['0']['#']['RESPONSE'];
        //Iterate over responses
        for($i = 0; $i < sizeof($responses); $i++) {
            $res_info = $responses[$i];
            //traverse_xmlize($res_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($res_info['#']['ID']['0']['#']);

            //Now, build the RESPONSES record structure
            $response->attempt = $attempt_id;
            $response->question = backup_todb($res_info['#']['QUESTION']['0']['#']);
            $response->originalquestion = backup_todb($res_info['#']['ORIGINALQUESTION']['0']['#']);
            $response->answer = backup_todb($res_info['#']['ANSWER']['0']['#']);
            $response->grade = backup_todb($res_info['#']['GRADE']['0']['#']);

            //We have to recode the question field
            $question = backup_getid($restore->backup_unique_code,"question",$response->question);
            if ($question) {
                $response->question = $question->new_id;
            }

            //We have to recode the originalquestion field
            $question = backup_getid($restore->backup_unique_code,"question",$response->originalquestion);
            if ($question) {
                $response->originalquestion = $question->new_id;
            }

            //Set the raw_grade field (default to the existing grade one, no penalty in pre15 backups)
            $response->raw_grade = $response->grade;

            //We have to recode the answer field
            //It depends of the question type !!
            //We get the question first
            $question = get_record("question","id",$response->question);
            //It exists
            if ($question) {
                //Depending of the qtype, we make different recodes
                switch ($question->qtype) {
                    case 1:    //SHORTANSWER QTYPE
                        //Nothing to do. The response is a text.
                        break;
                    case 2:    //TRUEFALSE QTYPE
                        //The answer is one answer id. We must recode it
                        $answer = backup_getid($restore->backup_unique_code,"question_answers",$response->answer);
                        if ($answer) {
                            $response->answer = $answer->new_id;
                        }
                        break;
                    case 3:    //MULTICHOICE QTYPE
                        //The answer is a comma separated list of answers. We must recode them
                        $answer_field = "";
                        $in_first = true;
                        $tok = strtok($response->answer,",");
                        while ($tok) {
                            //Get the answer from backup_ids
                            $answer = backup_getid($restore->backup_unique_code,"question_answers",$tok);
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
                        $response->answer = $answer_field;
                        break;
                    case 4:    //RANDOM QTYPE
                        //The answer links to another question id, we must recode it
                        $answer_link = backup_getid($restore->backup_unique_code,"question",$response->answer);
                        if ($answer_link) {
                            $response->answer = $answer_link->new_id;
                        }
                        break;
                    case 5:    //MATCH QTYPE
                        //The answer is a comma separated list of hypen separated math_subs (for question and answer)
                        $answer_field = "";
                        $in_first = true;
                        $tok = strtok($response->answer,",");
                        while ($tok) {
                            //Extract the match_sub for the question and the answer
                            $exploded = explode("-",$tok);
                            $match_question_id = $exploded[0];
                            $match_answer_id = $exploded[1];
                            //Get the match_sub from backup_ids (for the question)
                            $match_que = backup_getid($restore->backup_unique_code,"question_match_sub",$match_question_id);
                            //Get the match_sub from backup_ids (for the answer)
                            $match_ans = backup_getid($restore->backup_unique_code,"question_match_sub",$match_answer_id);
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
                        $response->answer = $answer_field;
                        break;
                    case 6:    //RANDOMSAMATCH QTYPE
                        //The answer is a comma separated list of hypen separated question_id and answer_id. We must recode them
                        $answer_field = "";
                        $in_first = true;
                        $tok = strtok($response->answer,",");
                        while ($tok) {
                            //Extract the question_id and the answer_id
                            $exploded = explode("-",$tok);
                            $question_id = $exploded[0];
                            $answer_id = $exploded[1];
                            //Get the question from backup_ids
                            $que = backup_getid($restore->backup_unique_code,"question",$question_id);
                            //Get the answer from backup_ids
                            $ans = backup_getid($restore->backup_unique_code,"question_answers",$answer_id);
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
                        $response->answer = $answer_field;
                        break;
                    case 7:    //DESCRIPTION QTYPE
                        //Nothing to do (there is no awser to this qtype)
                        //But this case must exist !!
                        break;
                    case 8:    //NUMERICAL QTYPE
                        //Nothing to do. The response is a text.
                        break;
                    case 9:    //MULTIANSWER QTYPE
                        //The answer is a comma separated list of hypen separated multianswer ids and answers. We must recode them.
                        //We need to have the sequence of questions here to be able to detect qtypes
                        $multianswerdb = get_record('question_multianswer','question',$response->question);
                        //Make an array of sequence to easy access
                        $sequencearr = explode(",",$multianswerdb->sequence);
                        $answer_field = "";
                        $in_first = true;
                        $tok = strtok($response->answer,",");
                        $counter = 1;
                        while ($tok) {
                            //Extract the multianswer_id and the answer
                            $exploded = explode("-",$tok);
                            $multianswer_id = $exploded[0];
                            $answer = $exploded[1];
                            //Get position key (if it fails, next iteration)
                            if ($oldposrec = backup_getid($restore->backup_unique_code,'multianswer-pos',$multianswer_id)) {
                                $positionkey = $oldposrec->new_id;
                            } else {
                                //Next iteration
                                $tok = strtok(",");
                                continue;
                            }
                            //Calculate question type
                            $questiondb = get_record('question','id',$sequencearr[$counter-1]);
                            $questiontype = $questiondb->qtype;
                            //Now, depending of the answertype field in question_multianswer
                            //we do diferent things
                            if ($questiontype == "1") {
                                //Shortanswer
                                //The answer is text, do nothing
                            } else if ($questiontype == "3") {
                                //Multichoice
                                //The answer is an answer_id, look for it in backup_ids
                                $ans = backup_getid($restore->backup_unique_code,"question_answers",$answer);
                                $answer = $ans->new_id;
                            } else if ($questiontype == "8") {
                                //Numeric
                                //The answer is text, do nothing
                            }

                            //Finaly, build the new answer field for each pair
                            if ($in_first) {
                                $answer_field .= $positionkey."-".$answer;
                                $in_first = false;
                            } else {
                                $answer_field .= ",".$positionkey."-".$answer;
                            }
                            //check for next
                            $tok = strtok(",");
                            $counter++;
                        }
                        $response->answer = $answer_field;
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

            //The structure is equal to the db, so insert the question_states
            $newid = insert_record ("question_states",$response);

            //Do some output
            if (($i+1) % 10 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 200 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"question_states",$oldid,
                             $newid);
            } else {
                $status = false;
            }
        }

        return $status;
    }

    //This function restores the quiz_grades
    function quiz_grades_restore_pre15_mods($quiz_id,$info,$restore) {

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
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 200 == 0) {
                        echo "<br />";
                    }
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

    //This function converts texts in FORMAT_WIKI to FORMAT_MARKDOWN for
    //some texts in the module
    function quiz_restore_pre15_wiki2markdown ($restore) {

        global $CFG;

        $status = true;

        //Convert question->questiontext
        if ($records = get_records_sql ("SELECT q.id, q.questiontext, q.questiontextformat
                                         FROM {$CFG->prefix}question q,
                                              {$CFG->prefix}backup_ids b
                                         WHERE b.backup_code = $restore->backup_unique_code AND
                                               b.table_name = 'question' AND
                                               q.id = b.new_id AND
                                               q.questiontextformat = ".FORMAT_WIKI)) {
            foreach ($records as $record) {
                //Rebuild wiki links
                $record->questiontext = restore_decode_wiki_content($record->questiontext, $restore);
                //Convert to Markdown
                $wtm = new WikiToMarkdown();
                $record->questiontext = $wtm->convert($record->questiontext, $restore->course_id);
                $record->questiontextformat = FORMAT_MARKDOWN;
                $status = update_record('question', addslashes_object($record));
                //Do some output
                $i++;
                if (($i+1) % 1 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 20 == 0) {
                            echo "<br />";
                        }
                    }
                    backup_flush(300);
                }
            }
        }
        return $status;
    }

    //This function returns a log record with all the necessay transformations
    //done. It's used by restore_log_module() to restore modules log.
    function quiz_restore_pre15_logs($restore,$log) {

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
            if (!defined('RESTORE_SILENTLY')) {
                echo "action (".$log->module."-".$log->action.") unknow. Not restored<br />";                 //Debug
            }
            break;
        }

        if ($status) {
            $status = $log;
        }
        return $status;
    }
?>
