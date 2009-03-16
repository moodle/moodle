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

    function extra_question_fields() {
        return array('question_shortanswer','answers','usecase');
    }

    function questionid_column_name() {
        return 'question';
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
            // Check for, and ingore, completely blank answer from the form.
            if (trim($dataanswer) == '' && $question->fraction[$key] == 0 &&
                    html_is_blank($question->feedback[$key])) {
                continue;
            }

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

        $question->answers = implode(',', $answers);
        $parentresult = parent::save_question_options($question);
        if($parentresult !== null) { // Parent function returns null if all is OK
            return $parentresult;
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

        if (isset($state->responses['']) && $state->responses[''] != '') {
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

    function check_response(&$question, &$state) {
        foreach($question->options->answers as $aid => $answer) {
            if ($this->test_response($question, $state, $answer)) {
                return $aid;
            }
        }
        return false;
    }

    function compare_responses($question, $state, $teststate) {
        if (isset($state->responses['']) && isset($teststate->responses[''])) {
            return $state->responses[''] === $teststate->responses[''];
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
        $excapedbits = array();
        foreach ($bits as $bit) {
            $excapedbits[] = preg_quote(str_replace('\*', '*', $bit));
        }
        // Put it back together to make the regexp.
        $regexp = '|^' . implode('.*', $excapedbits) . '$|u';

        // Make the match insensitive if requested to.
        if ($ignorecase) {
            $regexp .= 'i';
        }

        return preg_match($regexp, trim($string));
    }

    /*
     * Override the parent class method, to remove escaping from asterisks.
     */
    function get_correct_responses(&$question, &$state) {
        $response = parent::get_correct_responses($question, $state);
        if (is_array($response)) {
            $response[''] = addslashes(str_replace('\*', '*', stripslashes($response[''])));
        }
        return $response;
    }

/// RESTORE FUNCTIONS /////////////////

    /*
     * Restores the data in the question
     *
     * This is used in question/restorelib.php
     */
    function restore($old_question_id,$new_question_id,$info,$restore) {

        $status = parent::restore($old_question_id, $new_question_id, $info, $restore);

        if ($status) {
            $extraquestionfields = $this->extra_question_fields();
            $questionextensiontable = array_shift($extraquestionfields);

            //We have to recode the answers field (a list of answers id)
            $questionextradata = get_record($questionextensiontable, $this->questionid_column_name(), $new_question_id);
            if (isset($questionextradata->answers)) {
                $answers_field = "";
                $in_first = true;
                $tok = strtok($questionextradata->answers, ",");
                while ($tok) {
                    // Get the answer from backup_ids
                    $answer = backup_getid($restore->backup_unique_code,"question_answers",$tok);
                    if ($answer) {
                        if ($in_first) {
                            $answers_field .= $answer->new_id;
                            $in_first = false;
                        } else {
                            $answers_field .= ",".$answer->new_id;
                        }
                    }
                    // Check for next
                    $tok = strtok(",");
                }
                // We have the answers field recoded to its new ids
                $questionextradata->answers = $answers_field;
                // Update the question
                $status = $status && update_record($questionextensiontable, $questionextradata);
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
        global $QTYPES ;
        // MDL-7496 show correct answer after "Incorrect"
        $correctanswer = '';
        if ($correctanswers =  $QTYPES[$question->qtype]->get_correct_responses($question, $state)) {
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
                        print_string('correctansweris', 'quiz', s($correctanswer, true));
                        echo ('</div>');
                    }
                } else {
                    echo ' incorrect">';
                    // MDL-7496
                    print_string('incorrect', 'quiz');
                    if ($correctanswer) {
                        echo ('<div class="correctness">');
                        print_string('correctansweris', 'quiz', s($correctanswer, true));
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
                    if (($state->last_graded->raw_grade < $question->maxgrade) and (QUESTION_EVENTCLOSEANDGRADE != $state->event)) {
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

    /**
     * Runs all the code required to set up and save an essay question for testing purposes.
     * Alternate DB table prefix may be used to facilitate data deletion.
     */
    function generate_test($name, $courseid = null) {
        list($form, $question) = parent::generate_test($name, $courseid);
        $question->category = $form->category;

        $form->questiontext = "What is the purpose of life, the universe, and everything";
        $form->generalfeedback = "Congratulations, you may have solved my biggest problem!";
        $form->penalty = 0.1;
        $form->usecase = false;
        $form->defaultgrade = 1;
        $form->noanswers = 3;
        $form->answer = array('42', 'who cares?', 'Be happy');
        $form->fraction = array(1, 0.6, 0.8);
        $form->feedback = array('True, but what does that mean?', 'Well you do, dont you?', 'Yes, but thats not funny...');
        $form->correctfeedback = 'Excellent!';
        $form->incorrectfeedback = 'Nope!';
        $form->partiallycorrectfeedback = 'Not bad';

        if ($courseid) {
            $course = get_record('course', 'id', $courseid);
        }

        return $this->save_question($question, $form, $course);
    }
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
question_register_questiontype(new question_shortanswer_qtype());
?>
