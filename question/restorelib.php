<?php //$Id$
    //This php script contains all the stuff to restore questions

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

    function restore_question_categories($category,$restore) {

        global $CFG;

        $status = true;

        //Hook to call Moodle < 1.5 Quiz Restore
        if ($restore->backup_version < 2005043000) {
            include_once($CFG->dirroot.'/mod/quiz/restorelibpre15.php');
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
            $question_cat = new stdClass;
            $question_cat->course = $restore->course_id;
            $question_cat->name = backup_todb($info['QUESTION_CATEGORY']['#']['NAME']['0']['#']);
            $question_cat->info = backup_todb($info['QUESTION_CATEGORY']['#']['INFO']['0']['#']);
            $question_cat->publish = backup_todb($info['QUESTION_CATEGORY']['#']['PUBLISH']['0']['#']);
            $question_cat->stamp = backup_todb($info['QUESTION_CATEGORY']['#']['STAMP']['0']['#']);
            $question_cat->parent = backup_todb($info['QUESTION_CATEGORY']['#']['PARENT']['0']['#']);
            $question_cat->sortorder = backup_todb($info['QUESTION_CATEGORY']['#']['SORTORDER']['0']['#']);

            if ($catfound = restore_get_best_question_category($question_cat, $restore->course_id)) {
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
        } else {
            echo 'Could not get backup info for question category'. $category->id;
        }

        return $status;
    }

    function restore_questions ($old_category_id,$new_category_id,$info,$restore) {

        global $CFG, $QTYPES;

        $status = true;
        $restored_questions = array();

        //Get the questions array
        if (!empty($info['QUESTION_CATEGORY']['#']['QUESTIONS'])) {
            $questions = $info['QUESTION_CATEGORY']['#']['QUESTIONS']['0']['#']['QUESTION'];
        } else {
            $questions = array();
        }

        //Iterate over questions
        for($i = 0; $i < sizeof($questions); $i++) {
            $que_info = $questions[$i];
            //traverse_xmlize($que_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($que_info['#']['ID']['0']['#']);

            //Now, build the question record structure
            $question = new object;
            $question->category = $new_category_id;
            $question->parent = backup_todb($que_info['#']['PARENT']['0']['#']);
            $question->name = backup_todb($que_info['#']['NAME']['0']['#']);
            $question->questiontext = backup_todb($que_info['#']['QUESTIONTEXT']['0']['#']);
            $question->questiontextformat = backup_todb($que_info['#']['QUESTIONTEXTFORMAT']['0']['#']);
            $question->image = backup_todb($que_info['#']['IMAGE']['0']['#']);
            if (array_key_exists('GENERALFEEDBACK', $que_info['#'])) {
                $question->generalfeedback = backup_todb($que_info['#']['GENERALFEEDBACK']['0']['#']);
            } else {
                $question->generalfeedback = '';
            }
            $question->defaultgrade = backup_todb($que_info['#']['DEFAULTGRADE']['0']['#']);
            $question->penalty = backup_todb($que_info['#']['PENALTY']['0']['#']);
            $question->qtype = backup_todb($que_info['#']['QTYPE']['0']['#']);
            $question->length = backup_todb($que_info['#']['LENGTH']['0']['#']);
            $question->stamp = backup_todb($que_info['#']['STAMP']['0']['#']);
            $question->version = backup_todb($que_info['#']['VERSION']['0']['#']);
            $question->hidden = backup_todb($que_info['#']['HIDDEN']['0']['#']);

            if ($restore->backup_version < 2006032200) {
                // The qtype was an integer that now needs to be converted to the name
                $qtypenames = array(1=>'shortanswer',2=>'truefalse',3=>'multichoice',4=>'random',5=>'match',
                 6=>'randomsamatch',7=>'description',8=>'numerical',9=>'multianswer',10=>'calculated',
                 11=>'rqp',12=>'essay');
                $question->qtype = $qtypenames[$question->qtype];
            }

            //Check if the question exists
            //by category, stamp, and version
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
            $restored_questions[$i]->parent  = $question->parent;
            $restored_questions[$i]->is_new = $creatingnewquestion;
        }

        // Loop again, now all the question id mappings exist, so everything can
        // be restored.
        for($i = 0; $i < sizeof($questions); $i++) {
            $que_info = $questions[$i];

            $newid = $restored_questions[$i]->newid;
            $oldid = $restored_questions[$i]->oldid;

            $question = new object;
            $question->qtype = $restored_questions[$i]->qtype;
            $question->parent = $restored_questions[$i]->parent;


            //If it's a new question in the DB, restore it
            if ($restored_questions[$i]->is_new) {

                ////We have to recode the parent field
                if ($question->parent) {
                    if ($parent = backup_getid($restore->backup_unique_code,"question",$question->parent)) {
                        $question->parent = $parent->new_id;
                    } elseif ($question->parent = $oldid) {
                        $question->parent = $newid;
                    } else {
                        echo 'Could not recode parent '.$question->parent.' for question '.$oldid.'<br />';
                    }
                }
    
                //Now, restore every question_answers in this question
                $status = question_restore_answers($oldid,$newid,$que_info,$restore);
                // Restore questiontype specific data
                if (array_key_exists($question->qtype, $QTYPES)) {
                    $status = $QTYPES[$question->qtype]->restore($oldid,$newid,$que_info,$restore);
                } else {
                    echo 'Unknown question type '.$question->qtype.' for question '.$oldid.'<br />';
                    $status = false;
                }
            } else {
                //We are NOT creating the question, but we need to know every question_answers
                //map between the XML file and the database to be able to restore the states
                //in each attempt.
                $status = question_restore_map_answers($oldid,$newid,$que_info,$restore);
                // Do the questiontype specific mapping
                if (array_key_exists($question->qtype, $QTYPES)) {
                    $status = $QTYPES[$question->qtype]->restore_map($oldid,$newid,$que_info,$restore);
                } else {
                    echo 'Unknown question type '.$question->qtype.' for question '.$oldid.'<br />';
                    $status = false;
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
                $answer = new stdClass;
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
        if (!empty($info['#']['NUMERICAL_UNITS'])) {
            $numerical_units = $info['#']['NUMERICAL_UNITS']['0']['#']['NUMERICAL_UNIT'];
        } else {
            $numerical_units = array();
        }

        //Iterate over numerical_units
        for($i = 0; $i < sizeof($numerical_units); $i++) {
            $nu_info = $numerical_units[$i];
            //traverse_xmlize($nu_info);                                                                  //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //Now, build the question_numerical_UNITS record structure
            $numerical_unit = new stdClass;
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
            $dataset_definition = new stdClass;
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
                } else {
                    echo 'Could not recode category id '.$dataset_definition->category.' for dataset definition'.$dataset_definition->name.'<br />';
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
                $question_dataset = new stdClass;
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
            $dataset_item = new stdClass;
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


    //This function restores the question_states
    function question_states_restore_mods($attempt_id,$info,$restore) {

        global $CFG, $QTYPES;

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
            $state = new stdClass;
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
            $state->oldid = $oldid; // So it is available to restore_recode_answer.

            //We have to recode the question field
            $question = backup_getid($restore->backup_unique_code,"question",$state->question);
            if ($question) {
                $state->question = $question->new_id;
            } else {
                echo 'Could not recode question id '.$state->question.' for state '.$oldid.'<br />';
            }

            //We have to recode the originalquestion field if it is nonzero
            if ($state->originalquestion) {
                $question = backup_getid($restore->backup_unique_code,"question",$state->originalquestion);
                if ($question) {
                    $state->originalquestion = $question->new_id;
                } else {
                    echo 'Could not recode originalquestion id '.$state->question.' for state '.$oldid.'<br />';
                }
            }

            //We have to recode the answer field
            //It depends of the question type !!
            //We get the question first
            if (!$question = get_record("question","id",$state->question)) {
                error("Can't find the record for question $state->question for which I am trying to restore a state");
            }
            //Depending on the qtype, we make different recodes
            if ($state->answer) {
                $state->answer = $QTYPES[$question->qtype]->restore_recode_answer($state, $restore);
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
                $qtype = get_field('question', 'qtype', 'id', $state->question);
                $status = $QTYPES[$qtype]->restore_state($newid,$res_info,$restore);
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
            $session = new stdClass;
            $session->attemptid = $attempt_id;
            $session->questionid = backup_todb($res_info['#']['QUESTIONID']['0']['#']);
            $session->newest = backup_todb($res_info['#']['NEWEST']['0']['#']);
            $session->newgraded = backup_todb($res_info['#']['NEWGRADED']['0']['#']);
            $session->sumpenalty = backup_todb($res_info['#']['SUMPENALTY']['0']['#']);
            $session->comment = backup_todb($res_info['#']['COMMENT']['0']['#']);

            //We have to recode the question field
            $question = backup_getid($restore->backup_unique_code,"question",$session->questionid);
            if ($question) {
                $session->questionid = $question->new_id;
            } else {
                echo 'Could not recode question id '.$session->questionid.'<br />';
            }

            //We have to recode the newest field
            $state = backup_getid($restore->backup_unique_code,"question_states",$session->newest);
            if ($state) {
                $session->newest = $state->new_id;
            } else {
                echo 'Could not recode newest state id '.$session->newest.'<br />';
            }

            //If the session has been graded we have to recode the newgraded field
            if ($session->newgraded) {
                $state = backup_getid($restore->backup_unique_code,"question_states",$session->newgraded);
                if ($state) {
                    $session->newgraded = $state->new_id;
                } else {
                    echo 'Could not recode newest graded state id '.$session->newgraded.'<br />';
                }
            }

            //The structure is equal to the db, so insert the question_sessions
            $newid = insert_record ("question_sessions",$session);

        }

        return $status;
    }

?>
