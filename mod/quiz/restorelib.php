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
            $quiz_cat->stamp = backup_todb($info['QUESTION_CATEGORY']['#']['STAMP']['0']['#']);

            //Now, we are going to do some calculations to decide when to create the category or no.
            //Based in the next logic:
            //  + If the category doesn't exists, create it in $restore->course_id course and remap questions here.
            //  + If the category exists:
            //      - If it belongs to $restore->course_id course simply remap questions here.
            //      - If it doesn't belongs to $restore->course_id course:
            //          - If its publish field is set to No (0), create a new category in $restore->course_id course and remap questions here.
            //          - If its publish field is set to Yes (1), simply remap questions here.
            //
            //This was decided 2003/08/26, Eloy and Martin

            //Eloy's NOTE: I could be done code below more compact, but I've preffered do this to allow
            //easy future modifications.

            //If backup contains category_stamps then everythig is done by stamp (current approach from 1.1 final)
            //else, everything is done by name (old approach). This mantains backward compatibility.

            if ($quiz_cat->stamp) {
                //STAMP exists, do things using it (1.1)

                //Check for categories and their properties, storing in temporary variables
                //Count categories with the same stamp
   
                $count_cat = count_records("quiz_categories","stamp",$quiz_cat->stamp);
                //Count categories with the same stamp in the same course
                $count_cat_same_course = count_records("quiz_categories","course",$restore->course_id,"stamp",$quiz_cat->stamp);
                //Count categories with the same stamp in other course
                $count_cat_other_course = $count_cat - $count_cat_same_course;
   
                //Get categories with the same stamp in the same course 
                if ($count_cat_same_course > 0) {
                    //Eloy's NOTE: Due to this select *must* be retrive only one record, we could have used get_record(), but
                    //             mantain this to be as simmilar as possible with old code (comparing by name) to be
                    //             able to modify both in the same manner.
                    $cats_same_course = get_records_sql("SELECT c.* FROM {$CFG->prefix}quiz_categories c
                                                         WHERE c.course = '$restore->course_id' AND
                                                               c.stamp = '$quiz_cat->stamp'
                                                         ORDER BY c.id DESC");

                } else {
                    $cats_same_course = false;
                }
                //Get category with the same stamp in other course
                //The last record will be the oldest category with publish=1
                if ($count_cat_other_course > 0) {
                    $cats_other_course = get_records_sql("SELECT c.* FROM {$CFG->prefix}quiz_categories c
                                                          WHERE c.course != '$restore->course_id' AND
                                                                c.stamp = '$quiz_cat->stamp'
                                                          ORDER BY c.publish ASC, c.id DESC");
                } else {
                    $cats_other_course = false;
                }
   
                if ($count_cat == 0) {
                    //The category doesn't exist, create it.
                    //The structure is equal to the db, so insert the quiz_categories
                    $newid = insert_record ("quiz_categories",$quiz_cat);
                } else {
                    //The category exist, check if it belongs to the same course
                    if ($count_cat_same_course > 0) {
                        //The category belongs to the same course, get the last record (oldest)
                        foreach ($cats_same_course as $cat) {
                            $newid = $cat->id;
                        }
                    } else if ($count_cat_other_course > 0) {
                        //The category belongs to other course, get the last record (oldest)
                        foreach ($cats_other_course as $cat) {
                            $other_course_cat = $cat;
                        }
                        //Now check the publish field
                        if ($other_course_cat->publish == 0) {
                            //The category has its publish to No (0). Create a new local one.
                            $newid = insert_record ("quiz_categories",$quiz_cat);
                        } else {
                            //The category has its publish to Yes(1). Use it.
                            $newid = $other_course_cat->id;
                        }
                    } else {
                        //We must never arrive here !!
                        $status = false;
                    }
                }
            } else {
                //STAMP doesn't exists, do things by name (pre 1.1)
                //and calculate and insert STAMP too !!
    
                //Check for categories and their properties, storing in temporary variables
                //Count categories with the same name
    
                $count_cat = count_records("quiz_categories","name",$quiz_cat->name);
                //Count categories with the same name in the same course
                $count_cat_same_course = count_records("quiz_categories","course",$restore->course_id,"name",$quiz_cat->name);
                //Count categories with the same name in other course
                $count_cat_other_course = $count_cat - $count_cat_same_course;
    
                //Get categories with the same name in the same course
                //The last record will be the oldest category
                if ($count_cat_same_course > 0) {
                    $cats_same_course = get_records_sql("SELECT c.* FROM {$CFG->prefix}quiz_categories c
                                                         WHERE c.course = '$restore->course_id' AND 
                                                               c.name = '$quiz_cat->name'
                                                         ORDER BY c.id DESC");
                } else {
                    $cats_same_course = false;
                }
                //Get categories with the same name in other course
                //The last record will be the oldest category with publish=1
                if ($count_cat_other_course > 0) {
                    $cats_other_course = get_records_sql("SELECT c.* FROM {$CFG->prefix}quiz_categories c                           
                                                          WHERE c.course != '$restore->course_id' AND 
                                                                c.name = '$quiz_cat->name'
                                                          ORDER BY c.publish ASC, c.id DESC");
                } else {
                    $cats_other_course = false;
                }
    
                if ($count_cat == 0) {
                    //The category doesn't exist, create it.
                    //First, calculate the STAMP field
                    $quiz_cat->stamp = make_unique_id_code();
                    //The structure is equal to the db, so insert the quiz_categories
                    $newid = insert_record ("quiz_categories",$quiz_cat);
                } else {
                    //The category exist, check if it belongs to the same course
                    if ($count_cat_same_course > 0) {
                        //The category belongs to the same course, get the last record (oldest)
                        foreach ($cats_same_course as $cat) {
                            $newid = $cat->id;
                        }
                    } else if ($count_cat_other_course > 0) {
                        //The category belongs to other course, get the last record (oldest)
                        foreach ($cats_other_course as $cat) {
                            $other_course_cat = $cat;
                        }
                        //Now check the publish field
                        if ($other_course_cat->publish == 0) {
                            //The category has its publish to No (0). Create a new local one.
                            //First, calculate the STAMP field
                            $quiz_cat->stamp = make_unique_id_code();
                            //The structure is equal to the db, so insert the quiz_categories
                            $newid = insert_record ("quiz_categories",$quiz_cat);
                        } else {
                            //The category has its publish to Yes(1). Use it.
                            $newid = $other_course_cat->id;
                        }
                    } else {
                        //We must never arrive here !!
                        $status = false;
                    }
                }
            }

            //Do some output
            if ($status) {
                echo "<ul><li>".get_string("category")." \"".$quiz_cat->name."\"<br>";
            } else {
                //We must never arrive here !!
                echo "<ul><li>".get_string("category")." \"".$quiz_cat->name."\" Error!<br>";
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
            $question->questiontextformat = backup_todb($que_info['#']['QUESTIONTEXTFORMAT']['0']['#']);
            $question->image = backup_todb($que_info['#']['IMAGE']['0']['#']);
            $question->defaultgrade = backup_todb($que_info['#']['DEFAULTGRADE']['0']['#']);
            $question->qtype = backup_todb($que_info['#']['QTYPE']['0']['#']);
            $question->stamp = backup_todb($que_info['#']['STAMP']['0']['#']);
            $question->version = backup_todb($que_info['#']['VERSION']['0']['#']);

            //Check if the question exists
            //by category and stamp
            $question_exists = get_record ("quiz_questions","category",$question->category,
                                                            "stamp",$question->stamp);
            //If the stamp doesn't exists, check if question exists
            //by category, name and questiontext and calculate stamp
            //Mantains pre Beta 1.1 compatibility !! 
            //TO TAKE OUT SOMETIME IN THE FUTURE !!
            if (!$question->stamp) {
                $question->stamp = make_unique_id_code();
                $question->version = 1;
                $question_exists = get_record ("quiz_questions","category",$question->category,
                                                                "name",$question->name,
                                                                "questiontext",$question->questiontext);
            }
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

            //Do some output
            if (($i+1) % 2 == 0) {
                echo ".";
                if (($i+1) % 40 == 0) {
                    echo "<br>";
                }
                backup_flush(300);
            }
            //Save newid to backup tables
            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"quiz_questions",$oldid,
                             $newid);
            }
            //If it's a new question in the DB, restore it
            if ($creatingnewquestion) {
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
                }
            } else {
                //We are NOT creating the question, but we need to know every quiz_answers
                //map between the XML file and the database to be able to restore the responses
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
                }
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
        }

        return $status;
    }

    function quiz_restore_map_answers ($old_question_id,$new_question_id,$info,$restore) {

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

            //If we are in this method is because the question exists in DB, so its
            //answers must exist too.
            //Now, we are going to look for that answer in DB and to create the 
            //mappings in backup_ids to use them later where restoring responses (user level).

            //Get the answer from DB (by question and answer)
            $db_answer = get_record ("quiz_answers","question",$new_question_id,
                                                    "answer",$answer->answer);

            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br>";
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
                    echo "<br>";
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
            //mappings in backup_ids to use them later where restoring responses (user level).

            //Get the match_sub from DB (by question, questiontext and answertext)
            $db_match_sub = get_record ("quiz_match_sub","question",$new_question_id,
                                                      "questiontext",$match_sub->questiontext,
                                                      "answertext",$match_sub->answertext);
            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br>";
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
            //mappings in backup_ids to use them later where restoring responses (user level).

            //Get the multianswer from DB (by question and positionkey)
            $db_multianswer = get_record ("quiz_multianswers","question",$new_question_id,
                                                      "positionkey",$multianswer->positionkey);
            //Do some output
            if (($i+1) % 50 == 0) {
                echo ".";
                if (($i+1) % 1000 == 0) {
                    echo "<br>";
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
            $numerical->min = backup_todb($num_info['#']['MIN']['0']['#']);
            $numerical->max = backup_todb($num_info['#']['MAX']['0']['#']);

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
            $multianswer->answers = backup_todb($mul_info['#']['ANSWERS']['0']['#']);
            $multianswer->positionkey = backup_todb($mul_info['#']['POSITIONKEY']['0']['#']);
            $multianswer->answertype = backup_todb($mul_info['#']['ANSWERTYPE']['0']['#']);
            $multianswer->norm = backup_todb($mul_info['#']['NORM']['0']['#']);

            //We have to recode the answers field (a list of answers id)
            //Extracts answer id from sequence
            $answers_field = "";
            $in_first = true;
            $tok = strtok($multianswer->answers,",");
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
            $multianswer->answers = $answers_field;

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
                    echo "<br>";
                }
                backup_flush(300);
            }
            
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
            $quiz->attemptonlast = backup_todb($info['MOD']['#']['ATTEMPTONLAST']['0']['#']);
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
            echo "<ul><li>".get_string("modulename","quiz")." \"".$quiz->name."\"<br>";
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //We have to restore the question_grades now (course level table)
                $status = quiz_question_grades_restore_mods($newid,$info,$restore);
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
                $status = quiz_responses_restore_mods($newid,$att_info,$restore);
            } else {
                $status = false;
            }
        }

        return $status;
    }

    //This function restores the quiz_responses       
    function quiz_responses_restore_mods($attempt_id,$info,$restore) {

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
            $response->answer = backup_todb($res_info['#']['ANSWER']['0']['#']);
            $response->grade = backup_todb($res_info['#']['GRADE']['0']['#']);

            //We have to recode the question field
            $question = backup_getid($restore->backup_unique_code,"quiz_questions",$response->question);
            if ($question) {
                $response->question = $question->new_id;
            }

            //We have to recode the answer field
            //It depends of the question type !!
            //We get the question first
            $question = get_record("quiz_questions","id",$response->question);
            //It exists
            if ($question) {
                //Depending of the qtype, we make different recodes
                switch ($question->qtype) {
                    case 1:    //SHORTANSWER QTYPE
                        //Nothing to do. The response is a text.
                        break;
                    case 2:    //TRUEFALSE QTYPE
                        //The answer is one answer id. We must recode it
                        $answer = backup_getid($restore->backup_unique_code,"quiz_answers",$response->answer);
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
                        $response->answer = $answer_field;
                        break;
                    case 4:    //RANDOM QTYPE
                        //The answer links to another question id, we must recode it
                        $answer_link = backup_getid($restore->backup_unique_code,"quiz_questions",$response->answer);
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
                        //The answer is a comma separated list of hypen separated multianswer_id and answers. We must recode them.
                        $answer_field = "";
                        $in_first = true;
                        $tok = strtok($response->answer,",");
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
                        $response->answer = $answer_field;
                        break;
                    default:   //UNMATCHED QTYPE.
                        //This is an error (unimplemented qtype)
                        $status = false;
                        break;
                }
            } else {
                $status = false;
            }

            //The structure is equal to the db, so insert the quiz_attempts
            $newid = insert_record ("quiz_responses",$response);

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
                backup_putid($restore->backup_unique_code,"quiz_responses",$oldid,
                             $newid);
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
        default:
            echo "action (".$log->module."-".$log->action.") unknow. Not restored<br>";                 //Debug
            break;
        }

        if ($status) {
            $status = $log;
        }
        return $status;
    }
?>
