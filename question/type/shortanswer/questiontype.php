<?php  // $Id$

///////////////////
/// SHORTANSWER ///
///////////////////

/// QUESTION TYPE CLASS //////////////////

///
/// This class contains some special features in order to make the
/// question type embeddable within a multianswer (cloze) question
///
/**
 * @package questionbank
 * @subpackage questiontypes
 */
require_once("$CFG->dirroot/question/type/questiontype.php");

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
        $result = new stdClass;

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
                    $answer = new stdClass;
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
        $readonly = empty($options->readonly) ? '' : 'readonly="readonly"';
        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->para = false;
        $nameprefix = $question->name_prefix;

        /// Print question text and media

        $questiontext = format_text($question->questiontext,
                $question->questiontextformat,
                $formatoptions, $cmoptions->course);
        $image = get_question_image($question);

        /// Print input controls

        if (isset($state->responses[''])) {
            $value = ' value="'.s($state->responses[''], true).'" ';
        } else {
            $value = ' value="" ';
        }
        $inputname = ' name="'.$nameprefix.'" ';

        $feedback = '';
        $class = '';
        $feedbackimg = '';

        if ($options->feedback) {
            $class = question_get_feedback_class(0);
            $feedbackimg = question_get_feedback_image(0);
            foreach($question->options->answers as $answer) {

                if ($this->test_response($question, $state, $answer)) {
                    // Answer was correct or partially correct.
                    $class = question_get_feedback_class($answer->fraction);
                    $feedbackimg = question_get_feedback_image($answer->fraction);
                    if ($answer->feedback) {
                        $feedback = format_text($answer->feedback, true, $formatoptions, $cmoptions->course);
                    }
                    break;
                }
            }
        }

        /// Removed correct answer, to be displayed later MDL-7496
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

    function compare_responses($question, $state, $teststate) {
        if (isset($state->responses['']) && isset($teststate->responses[''])) {
            if ($question->options->usecase) {
                return strcmp($state->responses[''], $teststate->responses['']) == 0;
            } else {
                $textlib = textlib_get_instance();
                return strcmp($textlib->strtolower($state->responses['']),
                        $textlib->strtolower($teststate->responses[''])) == 0;
            }
        }
        return false;
    }

    function test_response(&$question, $state, $answer) {
        // Trim the response before it is saved in the database. See MDL-10709
        $state->responses[''] = trim($state->responses['']);
        return $this->compare_string_with_wildcard(stripslashes_safe($state->responses['']),
                $answer->answer, !$question->options->usecase);
    }

    function compare_string_with_wildcard($string, $pattern, $ignorecase) {
        // Break the string on non-escaped asterisks.
        $bits = preg_split('/(?<!\\\\)\*/', $pattern);
        // Escape regexp special characters in the bits.
        $bits = array_map('preg_quote', $bits);
        // Put it back together to make the regexp.
        $regexp = '|^' . implode('.*', $bits) . '$|u';

        // Make the match insensitive if requested to.
        if ($ignorecase) {
            $regexp .= 'i';
        }

        return preg_match($regexp, trim($string));
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
            $shortanswer = new stdClass;
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


        /**
    * Prints the score obtained and maximum score available plus any penalty
    * information
    *
    * This function prints a summary of the scoring in the most recently
    * graded state (the question may not have been submitted for marking at
    * the current state). The default implementation should be suitable for most
    * question types.
    * @param object $question The question for which the grading details are
    *                         to be rendered. Question type specific information
    *                         is included. The maximum possible grade is in
    *                         ->maxgrade.
    * @param object $state    The state. In particular the grading information
    *                          is in ->grade, ->raw_grade and ->penalty.
    * @param object $cmoptions
    * @param object $options  An object describing the rendering options.
    */
    function print_question_grading_details(&$question, &$state, $cmoptions, $options) {
        /* The default implementation prints the number of marks if no attempt
        has been made. Otherwise it displays the grade obtained out of the
        maximum grade available and a warning if a penalty was applied for the
        attempt and displays the overall grade obtained counting all previous
        responses (and penalties) */

        // MDL-7496 show correct answer after "Incorrect"
        $correctanswer = '';
        if ($correctanswers = $this->get_correct_responses($question, $state)) {
            if ($options->readonly && $options->correct_responses) {
                $delimiter = '';
                if ($correctanswers) {
                    foreach ($correctanswers as $ca) {
                        $correctanswer .= $delimiter.$ca;
                        $delimiter = ', ';
                    }
                }
            }
        }

        if (QUESTION_EVENTDUPLICATE == $state->event) {
            echo ' ';
            print_string('duplicateresponse', 'quiz');
        }
        if (!empty($question->maxgrade) && $options->scores) {
            if (question_state_is_graded($state->last_graded)) {
                // Display the grading details from the last graded state
                $grade = new stdClass;
                $grade->cur = round($state->last_graded->grade, $cmoptions->decimalpoints);
                $grade->max = $question->maxgrade;
                $grade->raw = round($state->last_graded->raw_grade, $cmoptions->decimalpoints);

                // let student know wether the answer was correct
                echo '<div class="correctness ';
                if ($state->last_graded->raw_grade >= $question->maxgrade/1.01) { // We divide by 1.01 so that rounding errors dont matter.
                    echo ' correct">';
                    print_string('correct', 'quiz');
                } else if ($state->last_graded->raw_grade > 0) {
                    echo ' partiallycorrect">';
                    print_string('partiallycorrect', 'quiz');
                    // MDL-7496
                    if ($correctanswer) {
                        echo ('<div class="correctness">');
                        print_string('correctansweris', 'quiz', s($correctanswer));
                        echo ('</div>');
                    }
                } else {
                    echo ' incorrect">';
                    // MDL-7496
                    print_string('incorrect', 'quiz');
                    if ($correctanswer) {
                        echo ('<div class="correctness">');
                        print_string('correctansweris', 'quiz', s($correctanswer));
                        echo ('</div>');
                    }
                }
                echo '</div>';

                echo '<div class="gradingdetails">';
                // print grade for this submission
                print_string('gradingdetails', 'quiz', $grade);
                if ($cmoptions->penaltyscheme) {
                    // print details of grade adjustment due to penalties
                    if ($state->last_graded->raw_grade > $state->last_graded->grade){
                        echo ' ';
                        print_string('gradingdetailsadjustment', 'quiz', $grade);
                    }
                    // print info about new penalty
                    // penalty is relevant only if the answer is not correct and further attempts are possible
                    if (($state->last_graded->raw_grade < $question->maxgrade) and (QUESTION_EVENTCLOSEANDGRADE !== $state->event)) {
                        if ('' !== $state->last_graded->penalty && ((float)$state->last_graded->penalty) > 0.0) {
                            // A penalty was applied so display it
                            echo ' ';
                            print_string('gradingdetailspenalty', 'quiz', $state->last_graded->penalty);
                        } else {
                            /* No penalty was applied even though the answer was
                            not correct (eg. a syntax error) so tell the student
                            that they were not penalised for the attempt */
                            echo ' ';
                            print_string('gradingdetailszeropenalty', 'quiz');
                        }
                    }
                }
                echo '</div>';
            }
        }
    }
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
question_register_questiontype(new question_shortanswer_qtype());
?>
