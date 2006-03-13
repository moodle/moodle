<?php //$Id$
    //This php script contains all the stuff to backup/restore questions

// Todo:
    // the restoration of the parent and sortorder fields in the category table needs
    // a lot more thought. We should probably use a library function to add the category
    // rather than just writing it to the database

    // whereever it says "/// We have to recode the .... field" we should put in a check
    // to see if the recoding was successful and throw an appropriate error otherwise

//This is the "graphical" structure of the question database:
    //To see, put your terminal to 160cc

    // The following holds student-independent information about the questions
    //
    //          question_categories
    //             (CL,pk->id)
    //                  |
    //                  |
    //                  |.......................................
    //                  |                                      .
    //                  |                                      .
    //                  |    -------question_datasets------    .
    //                  |    |  (CL,pk->id,fk->question,  |    .
    //                  |    |   fk->dataset_definition)  |    .
    //                  |    |                            |    .
    //                  |    |                            |    .
    //                  |    |                            |    .
    //                  |    |                    question_dataset_definitions
    //                  |    |                      (CL,pk->id,fk->category)
    //              question                                   |
    //        (CL,pk->id,fk->category,files)                   |
    //                  |                             question_dataset_items
    //                  |                          (CL,pk->id,fk->definition)
    //                  |                                                                                                           question_rqp_type
    //                  |                                                                                                            (SL,pk->id)
    //                  |                                                                                                                  |
    //             --------------------------------------------------------------------------------------------------------------          |
    //             |             |              |              |                       |                  |                     |        question_rqp
    //             |             |              |              |                       |                  |                     |--(CL,pk->id,fk->question)
    //             |             |              |              |             question_calculated          |                     |
    //      question_truefalse   |     question_multichoice    |          (CL,pl->id,fk->question)        |                     |
    // (CL,pk->id,fk->question)  |   (CL,pk->id,fk->question)  |                       .                  |                     |  question_randomsamatch
    //             .             |              .              |                       .                  |                     |--(CL,pk->id,fk->question)
    //             .    question_shortanswer    .      question_numerical              .         question_multianswer.          |
    //             .  (CL,pk->id,fk->question)  .  (CL,pk->id,fk->question)            .        (CL,pk->id,fk->question)        |
    //             .             .              .              .                       .                  .                     |       question_match
    //             .             .              .              .                       .                  .                     |--(CL,pk->id,fk->question)
    //             .             .              .              .                       .                  .                     |             .
    //             .             .              .              .                       .                  .                     |             .
    //             .             .              .              .                       .                  .                     |             .
    //             .             .              .              .                       .                  .                     |    question_match_sub
    //             ........................................................................................                     |--(CL,pk->id,fk->question)
    //                                                   .                                                                      |
    //                                                   .                                                                      |
    //                                                   .                                                                      |  question_numerical_units
    //                                             question_answers                                                             |--(CL,pk->id,fk->question)
    //                                         (CL,pk->id,fk->question)----------------------------------------------------------
    //
    //
    // The following holds the information about student interaction with the questions
    //
    //             question_sessions
    //      (UL,pk->id,fk->attempt,question)
    //                    .
    //                    .
    //             question_states
    //       (UL,pk->id,fk->attempt,question)
    //                |
    //           question_rqp_states
    //        (UL,pk->id,fk->stateid)                       
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

    include_once($CFG->libdir.'/questionlib.php');
    // load questiontype-specific functions
    unset($restorefns);
    unset($restoremapfns);
    //if ($qtypes = get_records('question_types')) {
        if ($qtypes = get_list_of_plugins('question/questiontypes')) {
        foreach ($qtypes as $name) {
            $qtype->name = $name;
            $restorelib = $CFG->dirroot.'/question/questiontypes/'.$qtype->name.'/restorelib.php';
            if (file_exists($restorelib)) {
                include_once($restorelib);
                $restorefn = 'question_'.$qtype->name.'_restore';
                if (function_exists($restorefn)) {
                    $restorefns[$qtype->name] = $restorefn;
                }
                $restoremapfn = 'question_'.$qtype->name.'_restore_map';
                if (function_exists($restoremapfn)) {
                    $restoremapfns[$qtype->name] = $restoremapfn;
                }
                $restorestatefn = 'question_'.$qtype->name.'_states_restore';
                if (function_exists($restorestatefn)) {
                    $restorestatefns[$qtype->name] = $restorestatefn;
                }
            }
        }
    }

    function restore_question_categories($category,$restore) {

        global $CFG;

        $status = true;

        //Hook to call Moodle < 1.5 Quiz Restore
        if ($restore->backup_version < 2005043000) {
            include_once("restorelibpre15.php");
            return quiz_restore_pre15_question_categories($category,$restore);
        }

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,"question_categories",$category->id);

        if ($data) {
            //Now get completed xmlized object
            $info = $data->info;
            //traverse_xmlize($info);                                                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the question_categories record structure
            $question_cat->course = $restore->course_id;
            $question_cat->name = backup_todb($info['QUESTION_CATEGORY']['#']['NAME']['0']['#']);
            $question_cat->info = backup_todb($info['QUESTION_CATEGORY']['#']['INFO']['0']['#']);
            $question_cat->publish = backup_todb($info['QUESTION_CATEGORY']['#']['PUBLISH']['0']['#']);
            $question_cat->stamp = backup_todb($info['QUESTION_CATEGORY']['#']['STAMP']['0']['#']);
            $question_cat->parent = backup_todb($info['QUESTION_CATEGORY']['#']['PARENT']['0']['#']);
            $question_cat->sortorder = backup_todb($info['QUESTION_CATEGORY']['#']['SORTORDER']['0']['#']);

            if ($catfound = restore_get_best_question_category($question_cat, $restore->course)) {
                $newid = $catfound;
            } else {
                if (!$question_cat->stamp) {
                    $question_cat->stamp = make_unique_id_code();
                }
                $newid = insert_record ("question_categories",$question_cat);
            }

            //Do some output
            if ($newid) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>".get_string('category', 'quiz')." \"".$question_cat->name."\"<br />";
                }
            } else {
                //We must never arrive here !!
                if (!defined('RESTORE_SILENTLY')) {
                    echo "<li>".get_string('category', 'quiz')." \"".$question_cat->name."\" Error!<br />";
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
                $status = restore_questions ($category->id, $newid,$info,$restore);
            } else {
                $status = false;
            }
            if (!defined('RESTORE_SILENTLY')) {
                echo '</li>';
            }
        }

        return $status;
    }

    function restore_questions ($old_category_id,$new_category_id,$info,$restore) {

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

            //Now, build the question record structure
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
            if ($question->parent and $parent = backup_getid($restore->backup_unique_code,"question",$question->parent)) {
                $question->parent = $parent->new_id;
            }

            //Check if the question exists
            //by category and stamp
            $question_exists = get_record ("question","category",$question->category,
                                                 "stamp",$question->stamp,"version",$question->version);

            //If the question exists, only record its id
            if ($question_exists) {
                $newid = $question_exists->id;
                $creatingnewquestion = false;
            //Else, create a new question
            } else {
                //The structure is equal to the db, so insert the question
                $newid = insert_record ("question",$question);
                $creatingnewquestion = true;
            }

            //Save newid to backup tables
            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,"question",$oldid,
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
                //Now, restore every question_answers in this question
                $status = question_restore_answers($oldid,$newid,$que_info,$restore);
                //Now, depending of the type of questions, invoke different functions
                if (isset($restorefns[$question->type])) {
                    $status = $restorefns[$question->type]->restore($oldid,$newid,$que_info,$restore);
                }
            } else {
                //We are NOT creating the question, but we need to know every question_answers
                //map between the XML file and the database to be able to restore the states
                //in each attempt.
                $status = question_restore_map_answers($oldid,$newid,$que_info,$restore);
                //Now, depending of the type of questions, invoke different functions
                //to create the necessary mappings in backup_ids, because we are not
                //creating the question, but need some records in backup table
                if (isset($restoremapfns[$question->type])) {
                    $status = $restoremapfns[$question->type]->restore($oldid,$newid,$que_info,$restore);
                }
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
        }
        return $status;
    }

    function question_restore_answers ($old_question_id,$new_question_id,$info,$restore) {

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

    function question_restore_map_answers ($old_question_id,$new_question_id,$info,$restore) {

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
            //mappings in backup_ids to use them later where restoring states (user level).

            //Get the answer from DB (by question and answer)
            $db_answer = get_record ("question_answers","question",$new_question_id,
                                                    "answer",$answer->answer);

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

    function question_restore_numerical_units ($old_question_id,$new_question_id,$info,$restore) {

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

    function question_restore_dataset_definitions ($old_question_id,$new_question_id,$info,$restore) {

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
                    $status = question_restore_dataset_items($newid,$dd_info,$restore);
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

    function question_restore_dataset_items ($definitionid,$info,$restore) {

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
            $dataset_item->number = backup_todb($di_info['#']['NUMBER']['0']['#']);
            $dataset_item->value = backup_todb($di_info['#']['VALUE']['0']['#']);

            //The structure is equal to the db, so insert the question_dataset_items
            $newid = insert_record ("question_dataset_items",$dataset_item);

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }


    //This function restores the question_states
    function question_states_restore_mods($attempt_id,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the question_states array
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
            $question = backup_getid($restore->backup_unique_code,"question",$state->question);
            if ($question) {
                $state->question = $question->new_id;
            }

            //We have to recode the originalquestion field
            $question = backup_getid($restore->backup_unique_code,"question",$state->originalquestion);
            if ($question) {
                $state->originalquestion = $question->new_id;
            }

            //We have to recode the answer field
            //It depends of the question type !!
            //We get the question first
            $question = get_record("question","id",$state->question);
            //It exists
            if ($question) {
                //Depending of the qtype, we make different recodes
                switch ($question->qtype) {
                    case 1:    //SHORTANSWER QTYPE
                        //Nothing to do. The response is a text.
                        break;
                    case 2:    //TRUEFALSE QTYPE
                        //The answer is one answer id. We must recode it
                        $answer = backup_getid($restore->backup_unique_code,"question_answers",$state->answer);
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
                        $state->answer = $answer_field;
                        break;
                    case 4:    //RANDOM QTYPE
                        //The answer links to another question id, we must recode it
                        $answer_link = backup_getid($restore->backup_unique_code,"question",$state->answer);
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
                            $mul = backup_getid($restore->backup_unique_code,"question_multianswer",$multianswer_id);
                            if ($mul) {
                                //Now, depending of the answertype field in question_multianswer
                                //we do diferent things
                                $mul_db = get_record ("question_multianswer","id",$mul->new_id);
                                if ($mul_db->answertype == "1") {
                                    //Shortanswer
                                    //The answer is text, do nothing
                                } else if ($mul_db->answertype == "3") {
                                    //Multichoice
                                    //The answer is an answer_id, look for it in backup_ids
                                    $ans = backup_getid($restore->backup_unique_code,"question_answers",$answer);
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

            //The structure is equal to the db, so insert the question_states
            $newid = insert_record ("question_states",$state);

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
                //Now process question type specific state information
                foreach ($restorestatefns as $restorestatefn) {
                    $restorestatefn($newid,$res_info,$restore);
                }
            } else {
                $status = false;
            }
        }

        //Get the question_sessions array
        $sessions = $info['#']['NEWEST_STATES']['0']['#']['NEWEST_STATE'];
        //Iterate over question_sessions
        for($i = 0; $i < sizeof($sessions); $i++) {
            $res_info = $sessions[$i];
            //traverse_xmlize($res_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the NEWEST_STATES record structure
            $session->attemptid = $attempt_id;
            $session->questionid = backup_todb($res_info['#']['QUESTIONID']['0']['#']);
            $session->newest = backup_todb($res_info['#']['NEWEST']['0']['#']);
            $session->newgraded = backup_todb($res_info['#']['NEWGRADED']['0']['#']);
            $session->sumpenalty = backup_todb($res_info['#']['SUMPENALTY']['0']['#']);

            //We have to recode the question field
            $question = backup_getid($restore->backup_unique_code,"question",$session->questionid);
            if ($question) {
                $session->questionid = $question->new_id;
            }

            //We have to recode the newest field
            $state = backup_getid($restore->backup_unique_code,"question_states",$session->newest);
            if ($state) {
                $session->newest = $state->new_id;
            }

            //We have to recode the newgraded field
            $state = backup_getid($restore->backup_unique_code,"question_states",$session->newgraded);
            if ($state) {
                $session->newgraded = $state->new_id;
            }

            //The structure is equal to the db, so insert the question_sessions
            $newid = insert_record ("question_sessions",$session);

        }

        return $status;
    }

?>
