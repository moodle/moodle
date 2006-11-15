<?php  // $Id$

///////////////////
/// SHORTANSWER ///
///////////////////

/// QUESTION TYPE CLASS //////////////////

///
/// This class contains some special features in order to make the
/// question type embeddable within a multianswer (cloze) question
///

class question_shortanswer_qtype extends default_questiontype {

    function name() {
        return 'shortanswer';
    }

    function get_question_options(&$question) {
        // Get additional information from database
        // and attach it to the question object
        if (!$question->options = get_record('question_shortanswer', 'question', $question->id)) {
            notify('Error: Missing question options!');
            return false;
        }

        if (!$question->options->answers = get_records('question_answers', 'question',
                $question->id, 'id ASC')) {
            notify('Error: Missing question answers!');
            return false;
        }
        return true;
    }

    function save_question_options($question) {
        if (!$oldanswers = get_records('question_answers', 'question', $question->id, 'id ASC')) {
            $oldanswers = array();
        }

        $answers = array();
        $maxfraction = -1;

        // Insert all the new answers
        foreach ($question->answer as $key => $dataanswer) {
            if ($dataanswer != "") {
                if ($oldanswer = array_shift($oldanswers)) {  // Existing answer, so reuse it
                    $answer = $oldanswer;
                    $answer->answer   = trim($dataanswer);
                    $answer->fraction = $question->fraction[$key];
                    $answer->feedback = $question->feedback[$key];
                    if (!update_record("question_answers", $answer)) {
                        $result->error = "Could not update quiz answer! (id=$answer->id)";
                        return $result;
                    }
                } else {    // This is a completely new answer
                    unset($answer);
                    $answer->answer   = trim($dataanswer);
                    $answer->question = $question->id;
                    $answer->fraction = $question->fraction[$key];
                    $answer->feedback = $question->feedback[$key];
                    if (!$answer->id = insert_record("question_answers", $answer)) {
                        $result->error = "Could not insert quiz answer!";
                        return $result;
                    }
                }
                $answers[] = $answer->id;
                if ($question->fraction[$key] > $maxfraction) {
                    $maxfraction = $question->fraction[$key];
                }
            }
        }

        if ($options = get_record("question_shortanswer", "question", $question->id)) {
            $options->answers = implode(",",$answers);
            $options->usecase = $question->usecase;
            if (!update_record("question_shortanswer", $options)) {
                $result->error = "Could not update quiz shortanswer options! (id=$options->id)";
                return $result;
            }
        } else {
            unset($options);
            $options->question = $question->id;
            $options->answers = implode(",",$answers);
            $options->usecase = $question->usecase;
            if (!insert_record("question_shortanswer", $options)) {
                $result->error = "Could not insert quiz shortanswer options!";
                return $result;
            }
        }

        // delete old answer records
        if (!empty($oldanswers)) {
            foreach($oldanswers as $oa) {
                delete_records('question_answers', 'id', $oa->id);
            }
        }

        /// Perform sanity checks on fractional grades
        if ($maxfraction != 1) {
            $maxfraction = $maxfraction * 100;
            $result->noticeyesno = get_string("fractionsnomax", "quiz", $maxfraction);
            return $result;
        } else {
            return true;
        }
    }

    /**
    * Deletes question from the question-type specific tables
    *
    * @return boolean Success/Failure
    * @param object $question  The question being deleted
    */
    function delete_question($questionid) {
        delete_records("question_shortanswer", "question", $questionid);
        return true;
    }

    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {
        global $CFG;
    /// This implementation is also used by question type 'numerical'
        $correctanswers = $this->get_correct_responses($question, $state);
        $readonly = empty($options->readonly) ? '' : 'readonly="readonly"';
        $formatoptions->noclean = true;
        $formatoptions->para = false;
        $nameprefix = $question->name_prefix;

        /// Print question text and media

        $questiontext =  format_text($question->questiontext,
                         $question->questiontextformat,
                         $formatoptions, $cmoptions->course);
        $image = get_question_image($question, $cmoptions->course);

        /// Print input controls

        if (isset($state->responses[''])) {
            $value = ' value="'.s($state->responses[''], true).'" ';
        } else {
            $value = ' value="" ';
        }
        $inputname = ' name="'.$nameprefix.'" ';

        $feedback = '';
        if ($options->feedback) {
            foreach($question->options->answers as $answer) {
                if($this->test_response($question, $state, $answer)) {
                    if ($answer->feedback) {
                        $feedback = format_text($answer->feedback, true, $formatoptions, $cmoptions->course);
                    }
                    break;
                }
            }
        }

        $correctanswer = '';
        if ($options->readonly && $options->correct_responses) {
            $delimiter = '';
            if ($correctanswers) {
                foreach ($correctanswers as $ca) {
                    $correctanswer .= $delimiter.$ca;
                    $delimiter = ', ';
                }
            }
        }
        include("$CFG->dirroot/question/type/shortanswer/display.html");
    }

    // ULPGC ecastro
    function check_response(&$question, &$state) {
        $answers = &$question->options->answers;
        $testedstate = clone($state);
        $teststate   = clone($state);
        foreach($answers as $aid => $answer) {
            $teststate->responses[''] = trim($answer->answer);
            if($this->compare_responses($question, $testedstate, $teststate)) {
                return $aid;
            }
        }
        return false;
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        
        $teststate = clone($state);
        $state->raw_grade = 0;
        // Compare the response with every teacher answer in turn
        // and return the first one that matches.
        foreach($question->options->answers as $answer) {
            // Now we use a bit of a hack: we put the answer into the response
            // of a teststate so that we can use the function compare_responses()
            $teststate->responses[''] = trim($answer->answer);
            if($this->compare_responses($question, $state, $teststate)) {
                $state->raw_grade = $answer->fraction;
                break;
            }
        }

        // Make sure we don't assign negative or too high marks
        $state->raw_grade = min(max((float) $state->raw_grade,
                            0.0), 1.0) * $question->maxgrade;
        $state->penalty = $question->penalty * $question->maxgrade;

        // mark the state as graded
        $state->event = ($state->event ==  QUESTION_EVENTCLOSE) ? QUESTION_EVENTCLOSEANDGRADE : QUESTION_EVENTGRADE;

        return true;
    }

    function compare_responses(&$question, &$state, &$teststate) {
        // In this questiontype this function is not only used to compare responses
        // between two different states but it is also used by grade_responses() and
        // by test_responses() to compare responses with answers.
        if (isset($state->responses[''])) {
            $response0 = trim(stripslashes($state->responses['']));
        } else {
            $response0 = '';
        }

        if (isset($teststate->responses[''])) {
            $response1 = trim($teststate->responses['']);
        } else {
            $response1 = '';
        }

        if (!$question->options->usecase) { // Don't compare case
            $response0 = strtolower($response0);
            $response1 = strtolower($response1);
        }

        /// These are things to protect in the strings when wildcards are used
        $search = array('\\', '+', '(', ')', '[', ']', '-');
        $replace = array('\\\\', '\+', '\(', '\)', '\[', '\]', '\-');

        if (strpos(' '.$response1, '*')) {
            $response1 = str_replace('\*','@@@@@@',$response1);
            $response1 = str_replace('*','.*',$response1);
            $response1 = str_replace($search, $replace, $response1);
            $response1 = str_replace('@@@@@@', '\*',$response1);

            if (ereg('^'.$response1.'$', $response0)) {
                return true;
            }

        } else if ($response1 == $response0) {
            return true;
        }

        return false;
    }

    function test_response(&$question, &$state, &$answer) {
        $teststate   = clone($state);
        $teststate->responses[''] = trim($answer->answer);
            if($this->compare_responses($question, $state, $teststate)) {
                return true;
            }
        return false;
    }

    /// BACKUP FUNCTIONS ////////////////////////////

    /*
     * Backup the data in the question
     *
     * This is used in question/backuplib.php
     */
    function backup($bf,$preferences,$question,$level=6) {

        $status = true;

        $shortanswers = get_records('question_shortanswer', 'question', $question, 'id ASC');
        //If there are shortanswers
        if ($shortanswers) {
            //Iterate over each shortanswer
            foreach ($shortanswers as $shortanswer) {
                $status = fwrite ($bf,start_tag("SHORTANSWER",$level,true));
                //Print shortanswer contents
                fwrite ($bf,full_tag("ANSWERS",$level+1,false,$shortanswer->answers));
                fwrite ($bf,full_tag("USECASE",$level+1,false,$shortanswer->usecase));
                $status = fwrite ($bf,end_tag("SHORTANSWER",$level,true));
            }
            //Now print question_answers
            $status = question_backup_answers($bf,$preferences,$question);
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

        //Get the shortanswers array
        $shortanswers = $info['#']['SHORTANSWER'];

        //Iterate over shortanswers
        for($i = 0; $i < sizeof($shortanswers); $i++) {
            $sho_info = $shortanswers[$i];

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
            $newid = insert_record ("question_shortanswer",$shortanswer);

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

}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
$QTYPES['shortanswer']= new question_shortanswer_qtype();
// The following adds the questiontype to the menu of types shown to teachers
$QTYPE_MENU['shortanswer'] = get_string("shortanswer", "quiz");

?>
