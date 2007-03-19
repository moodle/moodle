<?php  // $Id$

/////////////////////
/// RANDOMSAMATCH ///
/////////////////////

/// TODO: Make sure short answer questions chosen by a randomsamatch question
/// can not also be used by a random question

/// QUESTION TYPE CLASS //////////////////
/**
 * @package questionbank
 * @subpackage questiontypes
*/
class question_randomsamatch_qtype extends question_match_qtype {
/// Extends 'match' as there are quite a few simularities...

    function name() {
        return 'randomsamatch';
    }

    function is_usable_by_random() {
        return false;
    }

    function get_question_options(&$question) {
        if (!$question->options = get_record('question_randomsamatch', 'question', $question->id)) {
            notify('Error: Missing question options for random short answer question '.$question->id.'!');
            return false;
        }

        // This could be included as a flag in the database. It's already
        // supported by the code.
        // Recurse subcategories: 0 = no recursion, 1 = recursion
        $question->options->subcats = 1;
        return true;

    }

    function save_question_options($question) {
        $options->question = $question->id;
        $options->choose = $question->choose;

        if (2 > $question->choose) {
            $result->error = "At least two shortanswer questions need to be chosen!";
            return $result;
        }

        if ($existing = get_record("question_randomsamatch",
                                   "question", $options->question)) {
            $options->id = $existing->id;
            if (!update_record("question_randomsamatch", $options)) {
                $result->error = "Could not update quiz randomsamatch options!";
                return $result;
            }
        } else {
            if (!insert_record("question_randomsamatch", $options)) {
                $result->error = "Could not insert quiz randomsamatch options!";
                return $result;
            }
        }
        return true;
    }

    /**
    * Deletes question from the question-type specific tables
    *
    * @return boolean Success/Failure
    * @param object $question  The question being deleted
    */
    function delete_question($questionid) {
        delete_records("question_randomsamatch", "question", $questionid);
        return true;
    }

    function create_session_and_responses(&$question, &$state, $cmoptions, $attempt) {
        // Choose a random shortanswer question from the category:
        // We need to make sure that no question is used more than once in the
        // quiz. Therfore the following need to be excluded:
        // 1. All questions that are explicitly assigned to the quiz
        // 2. All random questions
        // 3. All questions that are already chosen by an other random question
        global $QTYPES;
        if (!isset($cmoptions->questionsinuse)) {
            $cmoptions->questionsinuse = $cmoptions->questions;
        }

        if ($question->options->subcats) {
            // recurse into subcategories
            $categorylist = question_categorylist($question->category);
        } else {
            $categorylist = $question->category;
        }

        $saquestions = $this->get_sa_candidates($categorylist, $cmoptions->questionsinuse);

        $count  = count($saquestions);
        $wanted = $question->options->choose;
        $errorstr = '';
        if ($count < $wanted && isteacherinanycourse()) {
            if ($count >= 2) {
                $errorstr =  "Error: could not get enough Short-Answer questions!
                 Got $count Short-Answer questions, but wanted $wanted.
                 Reducing number to choose from to $count!";
                $wanted = $question->options->choose = $count;
            } else {
                $errorstr = "Error: could not get enough Short-Answer questions!
                 This can happen if all available Short-Answer questions are already
                 taken up by other Random questions or Random Short-Answer question.
                 Another possible cause for this error is that Short-Answer
                 questions were deleted after this Random Short-Answer question was
                 created.";
            }
            notify($errorstr);
            $errorstr = '<span class="notifyproblem">' . $errorstr . '</span>';
        }

        if ($count < $wanted) {
            $question->questiontext = "$errorstr<br /><br />Insufficient selection options are
             available for this question, therefore it is not available in  this
             quiz. Please inform your teacher.";
            // Treat this as a description from this point on
            $question->qtype = DESCRIPTION;
            return true;
        }

        $saquestions =
         draw_rand_array($saquestions, $question->options->choose); // from bug 1889

        foreach ($saquestions as $key => $wrappedquestion) {
            if (!$QTYPES[$wrappedquestion->qtype]
             ->get_question_options($wrappedquestion)) {
                return false;
            }

            // Now we overwrite the $question->options->answers field to only
            // *one* (the first) correct answer. This loop can be deleted to
            // take all answers into account (i.e. put them all into the
            // drop-down menu.
            $foundcorrect = false;
            foreach ($wrappedquestion->options->answers as $answer) {
                if ($foundcorrect || $answer->fraction != 1.0) {
                    unset($wrappedquestion->options->answers[$answer->id]);
                } else if (!$foundcorrect) {
                    $foundcorrect = true;
                }
            }

            if (!$QTYPES[$wrappedquestion->qtype]
             ->create_session_and_responses($wrappedquestion, $state, $cmoptions,
             $attempt)) {
                return false;
            }
            $wrappedquestion->name_prefix = $question->name_prefix;
            $wrappedquestion->maxgrade    = $question->maxgrade;
            $cmoptions->questionsinuse .= ",$wrappedquestion->id";
            $state->options->subquestions[$key] = clone($wrappedquestion);
        }

        // Shuffle the answers (Do this always because this is a random question type)
        $subquestionids = array_values(array_map(create_function('$val',
         'return $val->id;'), $state->options->subquestions));
        $subquestionids = swapshuffle($subquestionids);

        // Create empty responses
        foreach ($subquestionids as $val) {
            $state->responses[$val] = '';
        }
        return true;
    }

    function restore_session_and_responses(&$question, &$state) {
        global $QTYPES;
        if (empty($state->responses[''])) {
            $question->questiontext = "Insufficient selection options are
             available for this question, therefore it is not available in  this
             quiz. Please inform your teacher.";
            // Treat this as a description from this point on
            $question->qtype = DESCRIPTION;
        } else {
            $responses = explode(',', $state->responses['']);
            $responses = array_map(create_function('$val',
             'return explode("-", $val);'), $responses);

            // Restore the previous responses
            $state->responses = array();
            foreach ($responses as $response) {
                $state->responses[$response[0]] = $response[1];
                if (!$wrappedquestion = get_record('question', 'id',
                 $response[0])) {
                    notify("Couldn't get question (id=$response[0])!");
                    return false;
                }
                if (!$QTYPES[$wrappedquestion->qtype]
                 ->get_question_options($wrappedquestion)) {
                    notify("Couldn't get question options (id=$response[0])!");
                    return false;
                }

                // Now we overwrite the $question->options->answers field to only
                // *one* (the first) correct answer. This loop can be deleted to
                // take all answers into account (i.e. put them all into the
                // drop-down menu.
                $foundcorrect = false;
                foreach ($wrappedquestion->options->answers as $answer) {
                    if ($foundcorrect || $answer->fraction != 1.0) {
                        unset($wrappedquestion->options->answers[$answer->id]);
                    } else if (!$foundcorrect) {
                        $foundcorrect = true;
                    }
                }

                if (!$QTYPES[$wrappedquestion->qtype]
                 ->restore_session_and_responses($wrappedquestion, $state)) {
                    notify("Couldn't restore session of question (id=$response[0])!");
                    return false;
                }
                $wrappedquestion->name_prefix = $question->name_prefix;
                $wrappedquestion->maxgrade    = $question->maxgrade;

                $state->options->subquestions[$wrappedquestion->id] =
                 clone($wrappedquestion);
            }
        }
        return true;
    }

    function extract_response($rawresponse, $nameprefix) {
    /// Simple implementation that does not check with the database
    /// and thus - does not bother to check whether there has been
    /// any changes to the question options.
        $response = array();
        $rawitems = explode(',', $rawresponse->answer);
        foreach ($rawitems as $rawitem) {
            $splits = explode('-', $rawitem, 2);
            $response[$nameprefix.$splits[0]] = $splits[1];
        }
        return $response;
    }

    function get_sa_candidates($categorylist, $questionsinuse=0) {
        return get_records_select('question',
         "qtype = '".'shortanswer'."' " .
         "AND category IN ($categorylist) " .
         "AND parent = '0' " .
         "AND hidden = '0'" .
         "AND id NOT IN ($questionsinuse)");
    }
    
/// BACKUP FUNCTIONS ////////////////////////////

    /*
     * Backup the data in the question
     *
     * This is used in question/backuplib.php
     */
    function backup($bf,$preferences,$question,$level=6) {

        $status = true;

        $randomsamatchs = get_records("question_randomsamatch","question",$question,"id");
        //If there are randomsamatchs
        if ($randomsamatchs) {
            //Iterate over each randomsamatch
            foreach ($randomsamatchs as $randomsamatch) {
                $status = fwrite ($bf,start_tag("RANDOMSAMATCH",6,true));
                //Print randomsamatch contents
                fwrite ($bf,full_tag("CHOOSE",7,false,$randomsamatch->choose));
                $status = fwrite ($bf,end_tag("RANDOMSAMATCH",6,true));
            }
        }
        return $status;
    }

/// RESTORE FUNCTIONS /////////////////

    /*
     * Restores the data in the question
     *
     * This is used in question/restorelib.php
     */
    function restore($old_question_id,$new_question_id,$info,$restore) {

        $status = true;

        //Get the randomsamatchs array
        $randomsamatchs = $info['#']['RANDOMSAMATCH'];

        //Iterate over randomsamatchs
        for($i = 0; $i < sizeof($randomsamatchs); $i++) {
            $ran_info = $randomsamatchs[$i];

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

    function restore_recode_answer($state, $restore) {

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
            if (!$que = backup_getid($restore->backup_unique_code,"question",$question_id)) {
                echo 'Could not recode randomsamatch question '.$question_id.'<br />';
            }
            
            if ($answer_id == 0) { // no response yet
                $ans->new_id = 0;
            } else {
                //Get the answer from backup_ids
                if (!$ans = backup_getid($restore->backup_unique_code,"question_answers",$answer_id)) {
                    echo 'Could not recode randomsamatch answer '.$answer_id.'<br />';
                }
            }
            if ($in_first) {
                $answer_field .= $que->new_id."-".$ans->new_id;
                $in_first = false;
            } else {
                $answer_field .= ",".$que->new_id."-".$ans->new_id;
            }
            //check for next
            $tok = strtok(",");
        }
        return $answer_field;
    }

}

//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
question_register_questiontype(new question_randomsamatch_qtype());
?>
