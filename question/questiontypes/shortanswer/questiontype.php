<?php  // $Id$

///////////////////
/// SHORTANSWER ///
///////////////////

/// QUESTION TYPE CLASS //////////////////

///
/// This class contains some special features in order to make the
/// question type embeddable within a multianswer (cloze) question
///

class quiz_shortanswer_qtype extends quiz_default_questiontype {

    function name() {
        return 'shortanswer';
    }

    function get_question_options(&$question) {
        // Get additional information from database
        // and attach it to the question object
        if (!$question->options = get_record('quiz_shortanswer', 'question', $question->id)) {
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
        if (!$oldanswers = get_records("question_answers", "question", $question->id, "id ASC")) {
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

        if ($options = get_record("quiz_shortanswer", "question", $question->id)) {
            $options->answers = implode(",",$answers);
            $options->usecase = $question->usecase;
            if (!update_record("quiz_shortanswer", $options)) {
                $result->error = "Could not update quiz shortanswer options! (id=$options->id)";
                return $result;
            }
        } else {
            unset($options);
            $options->question = $question->id;
            $options->answers = implode(",",$answers);
            $options->usecase = $question->usecase;
            if (!insert_record("quiz_shortanswer", $options)) {
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
    function delete_question($question) {
        delete_records("quiz_shortanswer", "question", $question->id);
        //TODO: delete also the states from quiz_rqp_states
        return true;
    }

    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {
        global $CFG;
    /// This implementation is also used by question type NUMERICAL
        $answers = &$question->options->answers;
        $correctanswers = $this->get_correct_responses($question, $state);
        $readonly = empty($options->readonly) ? '' : 'readonly="readonly"';
        $nameprefix = $question->name_prefix;

        /// Print question text and media

        $questiontext =  format_text($question->questiontext,
                         $question->questiontextformat,
                         NULL, $cmoptions->course);
        $image = get_question_image($question, $cmoptions->course);

        /// Print input controls

        if (isset($state->responses[''])) {
            $value = ' value="'.s($state->responses['']).'" ';
        } else {
            $value = ' value="" ';
        }
        $inputname = ' name="'.$nameprefix.'" ';

        $feedback = '';
        if ($options->feedback) {
            $testedstate = clone($state);
            $teststate   = clone($state);
            foreach($answers as $answer) {
                $teststate->responses[''] = trim($answer->answer);
                if($answer->feedback and $this->compare_responses($question, $testedstate, $teststate)) {
                    $feedback = format_text($answer->feedback, true, false);
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
        include("$CFG->dirroot/question/questiontypes/shortanswer/display.html");
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
        $answers = &$question->options->answers;
        $testedstate = clone($state);
        $teststate   = clone($state);
        $state->raw_grade = 0;

        foreach($answers as $answer) {
            $teststate->responses[''] = trim($answer->answer);
            if($this->compare_responses($question, $testedstate, $teststate)) {
                $state->raw_grade = $answer->fraction;
                break;
            }
        }

        // Make sure we don't assign negative or too high marks
        $state->raw_grade = min(max((float) $state->raw_grade,
                            0.0), 1.0) * $question->maxgrade;
        $state->penalty = $question->penalty * $question->maxgrade;

        return true;
    }

    function compare_responses(&$question, &$state, &$teststate) {
        if (isset($state->responses[''])) {
            $response0 = trim(stripslashes($state->responses['']));
        } else {
            $response0 = '';
        }

        if (isset($teststate->responses[''])) {
            $response1 = trim(stripslashes($teststate->responses['']));
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

}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
$QTYPES[SHORTANSWER]= new quiz_shortanswer_qtype();

?>
