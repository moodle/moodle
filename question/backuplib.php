<?php //$Id$
    //This php script contains all the stuff to backup questions

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

    require_once("$CFG->libdir/questionlib.php");

    function backup_question_categories($bf,$preferences) {

        global $CFG;

        $status = true;

        //First, we get the used categories from backup_ids
        $categories = question_category_ids_by_backup ($preferences->backup_unique_code);

        //If we've categories
        if ($categories) {
             //Write start tag
             $status = fwrite($bf,start_tag("QUESTION_CATEGORIES",2,true));
             //Iterate over each category
            foreach ($categories as $cat) {
                //Start category
                $status = fwrite ($bf,start_tag("QUESTION_CATEGORY",3,true));
                //Get category data from question_categories
                $category = get_record ("question_categories","id",$cat->old_id);
                //Print category contents
                fwrite($bf,full_tag("ID",4,false,$category->id));
                fwrite($bf,full_tag("NAME",4,false,$category->name));
                fwrite($bf,full_tag("INFO",4,false,$category->info));
                fwrite($bf,full_tag("PUBLISH",4,false,$category->publish));
                fwrite($bf,full_tag("STAMP",4,false,$category->stamp));
                fwrite($bf,full_tag("PARENT",4,false,$category->parent));
                fwrite($bf,full_tag("SORTORDER",4,false,$category->sortorder));
                //Now, backup their questions
                $status = backup_question($bf,$preferences,$category->id);
                //End category
                $status = fwrite ($bf,end_tag("QUESTION_CATEGORY",3,true));
            }
            //Write end tag
            $status = fwrite ($bf,end_tag("QUESTION_CATEGORIES",2,true));
        }

        return $status;
    }

    //This function backups all the questions in selected category and their
    //asociated data
    function backup_question($bf,$preferences,$category, $level = 4) {

        global $CFG, $QTYPES;

        $status = true;

        // We'll fetch the questions sorted by parent so that questions with no parents
        // (these are the ones which could be parents themselves) are backed up first. This
        // is important for the recoding of the parent field during the restore process
        $questions = get_records("question","category",$category,"parent ASC, id");
        //If there are questions
        if ($questions) {
            //Write start tag
            $status = fwrite ($bf,start_tag("QUESTIONS",$level,true));
            $counter = 0;
            //Iterate over each question
            foreach ($questions as $question) {
                //Start question
                $status = fwrite ($bf,start_tag("QUESTION",$level + 1,true));
                //Print question contents
                fwrite ($bf,full_tag("ID",$level + 2,false,$question->id));
                fwrite ($bf,full_tag("PARENT",$level + 2,false,$question->parent));
                fwrite ($bf,full_tag("NAME",$level + 2,false,$question->name));
                fwrite ($bf,full_tag("QUESTIONTEXT",$level + 2,false,$question->questiontext));
                fwrite ($bf,full_tag("QUESTIONTEXTFORMAT",$level + 2,false,$question->questiontextformat));
                fwrite ($bf,full_tag("IMAGE",$level + 2,false,$question->image));
                fwrite ($bf,full_tag("GENERALFEEDBACK",$level + 2,false,$question->generalfeedback));
                fwrite ($bf,full_tag("DEFAULTGRADE",$level + 2,false,$question->defaultgrade));
                fwrite ($bf,full_tag("PENALTY",$level + 2,false,$question->penalty));
                fwrite ($bf,full_tag("QTYPE",$level + 2,false,$question->qtype));
                fwrite ($bf,full_tag("LENGTH",$level + 2,false,$question->length));
                fwrite ($bf,full_tag("STAMP",$level + 2,false,$question->stamp));
                fwrite ($bf,full_tag("VERSION",$level + 2,false,$question->version));
                fwrite ($bf,full_tag("HIDDEN",$level + 2,false,$question->hidden));
                // Backup question type specific data
                $status = $QTYPES[$question->qtype]->backup($bf,$preferences,$question->id, $level + 2);
                //End question
                $status = fwrite ($bf,end_tag("QUESTION",$level + 1,true));
                //Do some output
                $counter++;
                if ($counter % 10 == 0) {
                    echo ".";
                    if ($counter % 200 == 0) {
                        echo "<br />";
                    }
                    backup_flush(300);
                }
            }
            //Write end tag
            $status = fwrite ($bf,end_tag("QUESTIONS",$level,true));
        }
        return $status;
    }

    //This function backups the answers data in some question types
    //(truefalse, shortanswer,multichoice,numerical,calculated)
    function question_backup_answers($bf,$preferences,$question, $level = 6) {

        global $CFG;

        $status = true;

        $answers = get_records("question_answers","question",$question,"id");
        //If there are answers
        if ($answers) {
            $status = fwrite ($bf,start_tag("ANSWERS",$level,true));
            //Iterate over each answer
            foreach ($answers as $answer) {
                $status = fwrite ($bf,start_tag("ANSWER",$level + 1,true));
                //Print answer contents
                fwrite ($bf,full_tag("ID",$level + 2,false,$answer->id));
                fwrite ($bf,full_tag("ANSWER_TEXT",$level + 2,false,$answer->answer));
                fwrite ($bf,full_tag("FRACTION",$level + 2,false,$answer->fraction));
                fwrite ($bf,full_tag("FEEDBACK",$level + 2,false,$answer->feedback));
                $status = fwrite ($bf,end_tag("ANSWER",$level + 1,true));
            }
            $status = fwrite ($bf,end_tag("ANSWERS",$level,true));
        }
        return $status;
    }

    //This function backups question_numerical_units from different question types
    function question_backup_numerical_units($bf,$preferences,$question,$level=7) {

        global $CFG;

        $status = true;

        $numerical_units = get_records("question_numerical_units","question",$question,"id");
        //If there are numericals_units
        if ($numerical_units) {
            $status = fwrite ($bf,start_tag("NUMERICAL_UNITS",$level,true));
            //Iterate over each numerical_unit
            foreach ($numerical_units as $numerical_unit) {
                $status = fwrite ($bf,start_tag("NUMERICAL_UNIT",$level+1,true));
                //Print numerical_unit contents
                fwrite ($bf,full_tag("MULTIPLIER",$level+2,false,$numerical_unit->multiplier));
                fwrite ($bf,full_tag("UNIT",$level+2,false,$numerical_unit->unit));
                //Now backup numerical_units
                $status = fwrite ($bf,end_tag("NUMERICAL_UNIT",$level+1,true));
            }
            $status = fwrite ($bf,end_tag("NUMERICAL_UNITS",$level,true));
        }

        return $status;

    }

    //This function backups dataset_definitions (via question_datasets) from different question types
    function question_backup_datasets($bf,$preferences,$question,$level=7) {

        global $CFG;

        $status = true;

        //First, we get the used datasets for this question
        $question_datasets = get_records("question_datasets","question",$question,"id");
        //If there are question_datasets
        if ($question_datasets) {
            $status = $status &&fwrite ($bf,start_tag("DATASET_DEFINITIONS",$level,true));
            //Iterate over each question_dataset
            foreach ($question_datasets as $question_dataset) {
                $def = NULL;
                //Get dataset_definition
                if ($def = get_record("question_dataset_definitions","id",$question_dataset->datasetdefinition)) {;
                    $status = $status &&fwrite ($bf,start_tag("DATASET_DEFINITION",$level+1,true));
                    //Print question_dataset contents
                    fwrite ($bf,full_tag("CATEGORY",$level+2,false,$def->category));
                    fwrite ($bf,full_tag("NAME",$level+2,false,$def->name));
                    fwrite ($bf,full_tag("TYPE",$level+2,false,$def->type));
                    fwrite ($bf,full_tag("OPTIONS",$level+2,false,$def->options));
                    fwrite ($bf,full_tag("ITEMCOUNT",$level+2,false,$def->itemcount));
                    //Now backup dataset_entries
                    $status = question_backup_dataset_items($bf,$preferences,$def->id,$level+2);
                    //End dataset definition
                    $status = $status &&fwrite ($bf,end_tag("DATASET_DEFINITION",$level+1,true));
                }
            }
            $status = $status &&fwrite ($bf,end_tag("DATASET_DEFINITIONS",$level,true));
        }

        return $status;

    }

    //This function backups datases_items from dataset_definitions
    function question_backup_dataset_items($bf,$preferences,$datasetdefinition,$level=9) {

        global $CFG;

        $status = true;

        //First, we get the datasets_items for this dataset_definition
        $dataset_items = get_records("question_dataset_items","definition",$datasetdefinition,"id");
        //If there are dataset_items
        if ($dataset_items) {
            $status = $status &&fwrite ($bf,start_tag("DATASET_ITEMS",$level,true));
            //Iterate over each dataset_item
            foreach ($dataset_items as $dataset_item) {
                $status = $status &&fwrite ($bf,start_tag("DATASET_ITEM",$level+1,true));
                //Print question_dataset contents
                fwrite ($bf,full_tag("NUMBER",$level+2,false,$dataset_item->itemnumber));
                fwrite ($bf,full_tag("VALUE",$level+2,false,$dataset_item->value));
                //End dataset definition
                $status = $status &&fwrite ($bf,end_tag("DATASET_ITEM",$level+1,true));
            }
            $status = $status &&fwrite ($bf,end_tag("DATASET_ITEMS",$level,true));
        }

        return $status;

    }


    //Backup question_states contents (executed from backup_quiz_attempts)
    function backup_question_states ($bf,$preferences,$attempt, $level = 6) {

        global $CFG;

        $status = true;

        $question_states = get_records("question_states","attempt",$attempt,"id");
        //If there are states
        if ($question_states) {
            //Write start tag
            $status = fwrite ($bf,start_tag("STATES",$level,true));
            //Iterate over each state
            foreach ($question_states as $state) {
                //Start state
                $status = fwrite ($bf,start_tag("STATE",$level + 1,true));
                //Print state contents
                fwrite ($bf,full_tag("ID",$level + 2,false,$state->id));
                fwrite ($bf,full_tag("QUESTION",$level + 2,false,$state->question));
                fwrite ($bf,full_tag("ORIGINALQUESTION",$level + 2,false,$state->originalquestion));
                fwrite ($bf,full_tag("SEQ_NUMBER",$level + 2,false,$state->seq_number));
                fwrite ($bf,full_tag("ANSWER",$level + 2,false,$state->answer));
                fwrite ($bf,full_tag("TIMESTAMP",$level + 2,false,$state->timestamp));
                fwrite ($bf,full_tag("EVENT",$level + 2,false,$state->event));
                fwrite ($bf,full_tag("GRADE",$level + 2,false,$state->grade));
                fwrite ($bf,full_tag("RAW_GRADE",$level + 2,false,$state->raw_grade));
                fwrite ($bf,full_tag("PENALTY",$level + 2,false,$state->penalty));
                // now back up question type specific state information
                $status = backup_question_rqp_state ($bf,$preferences,$state->id, $level + 2);
                //End state
                $status = fwrite ($bf,end_tag("STATE",$level + 1,true));
            }
            //Write end tag
            $status = fwrite ($bf,end_tag("STATES",$level,true));
        }
    }

    //Backup question_sessions contents (executed from backup_quiz_attempts)
    function backup_question_sessions ($bf,$preferences,$attempt, $level = 6) {
        global $CFG;

        $status = true;

        $question_sessions = get_records("question_sessions","attemptid",$attempt,"id");
        //If there are sessions
        if ($question_sessions) {
            //Write start tag (the funny name 'newest states' has historical reasons)
            $status = fwrite ($bf,start_tag("NEWEST_STATES",$level,true));
            //Iterate over each newest_state
            foreach ($question_sessions as $newest_state) {
                //Start newest_state
                $status = fwrite ($bf,start_tag("NEWEST_STATE",$level + 1,true));
                //Print newest_state contents
                fwrite ($bf,full_tag("ID",$level + 2,false,$newest_state->id));
                fwrite ($bf,full_tag("QUESTIONID",$level + 2,false,$newest_state->questionid));
                fwrite ($bf,full_tag("NEWEST",$level + 2,false,$newest_state->newest));
                fwrite ($bf,full_tag("NEWGRADED",$level + 2,false,$newest_state->newgraded));
                fwrite ($bf,full_tag("SUMPENALTY",$level + 2,false,$newest_state->sumpenalty));
                fwrite ($bf,full_tag("MANUALCOMMENT",$level + 2,false,$newest_state->manualcomment));                
                //End newest_state
                $status = fwrite ($bf,end_tag("NEWEST_STATE",$level + 1,true));
            }
            //Write end tag
            $status = fwrite ($bf,end_tag("NEWEST_STATES",$level,true));
        }
        return $status;
    }

    //Backup question_rqp_state contents (executed from backup_question_states)
    function backup_question_rqp_state ($bf,$preferences,$state, $level = 8) {

        global $CFG;

        $status = true;

        $rqp_state = get_record("question_rqp_states","stateid",$state);
        //If there is a state
        if ($rqp_state) {
            //Write start tag
            $status = fwrite ($bf,start_tag("RQP_STATE",$level,true));
            //Print state contents
            fwrite ($bf,full_tag("RESPONSES",$level + 1,false,$rqp_state->responses));
            fwrite ($bf,full_tag("PERSISTENT_DATA",$level + 1,false,$rqp_state->persistent_data));
            fwrite ($bf,full_tag("TEMPLATE_VARS",$level + 1,false,$rqp_state->template_vars));
            //Write end tag
            $status = fwrite ($bf,end_tag("RQP_STATE",$level,true));
        }
        return $status;
    }

    //Returns an array of categories id
    function question_category_ids_by_backup ($backup_unique_code) {

        global $CFG;

        return get_records_sql ("SELECT a.old_id, a.backup_code
                                 FROM {$CFG->prefix}backup_ids a
                                 WHERE a.backup_code = '$backup_unique_code' AND
                                       a.table_name = 'question_categories'");
    }

    function question_ids_by_backup ($backup_unique_code) {

        global $CFG;

        return get_records_sql ("SELECT q.id, q.category
                                 FROM {$CFG->prefix}backup_ids a,
                                      {$CFG->prefix}question q
                                 WHERE a.backup_code = '$backup_unique_code' AND
                                       q.category = a.old_id AND
                                       a.table_name = 'question_categories'");
    }

    //Delete category ids from backup_ids table
    function delete_category_ids ($backup_unique_code) {
        global $CFG;
        $status = true;
        $status = execute_sql("DELETE FROM {$CFG->prefix}backup_ids
                               WHERE backup_code = '$backup_unique_code'",false);
        return $status;
    }

?>
