<?php  // $Id$

///////////////////
/// MULTICHOICE ///
///////////////////

/// QUESTION TYPE CLASS //////////////////

///
/// This class contains some special features in order to make the
/// question type embeddable within a multianswer (cloze) question
///

class question_multichoice_qtype extends quiz_default_questiontype {

    function name() {
        return 'multichoice';
    }

    function get_question_options(&$question) {
        // Get additional information from database
        // and attach it to the question object
        if (!$question->options = get_record('question_multichoice', 'question',
         $question->id)) {
            notify('Error: Missing question options for multichoice question'.$question->id.'!');
            return false;
        }

        if (!$question->options->answers = get_records_select('question_answers', 'id IN ('.$question->options->answers.')')) {
           notify('Error: Missing question answers for multichoice question'.$question->id.'!');
           return false;
        }

        return true;
    }

    function save_question_options($question) {

        if (!$oldanswers = get_records("question_answers", "question",
                                       $question->id, "id ASC")) {
            $oldanswers = array();
        }

        // following hack to check at least two answers exist
        $answercount = 0;
        foreach ($question->answer as $key=>$dataanswer) {
            if ($dataanswer != "") {
                $answercount++;
            }
        }
        $answercount += count($oldanswers);
        if ($answercount < 2) { // check there are at lest 2 answers for multiple choice
            $result->notice = get_string("notenoughanswers", "quiz", "2");
            return $result;
        }

        // Insert all the new answers

        $totalfraction = 0;
        $maxfraction = -1;

        $answers = array();

        foreach ($question->answer as $key => $dataanswer) {
            if ($dataanswer != "") {
                if ($answer = array_shift($oldanswers)) {  // Existing answer, so reuse it
                    $answer->answer     = $dataanswer;
                    $answer->fraction   = $question->fraction[$key];
                    $answer->feedback   = $question->feedback[$key];
                    if (!update_record("question_answers", $answer)) {
                        $result->error = "Could not update quiz answer! (id=$answer->id)";
                        return $result;
                    }
                } else {
                    unset($answer);
                    $answer->answer   = $dataanswer;
                    $answer->question = $question->id;
                    $answer->fraction = $question->fraction[$key];
                    $answer->feedback = $question->feedback[$key];
                    if (!$answer->id = insert_record("question_answers", $answer)) {
                        $result->error = "Could not insert quiz answer! ";
                        return $result;
                    }
                }
                $answers[] = $answer->id;

                if ($question->fraction[$key] > 0) {                 // Sanity checks
                    $totalfraction += $question->fraction[$key];
                }
                if ($question->fraction[$key] > $maxfraction) {
                    $maxfraction = $question->fraction[$key];
                }
            }
        }

        if ($options = get_record("question_multichoice", "question", $question->id)) {
            $options->answers = implode(",",$answers);
            $options->single = $question->single;
            $options->shuffleanswers = $question->shuffleanswers;
            if (!update_record("question_multichoice", $options)) {
                $result->error = "Could not update quiz multichoice options! (id=$options->id)";
                return $result;
            }
        } else {
            unset($options);
            $options->question = $question->id;
            $options->answers = implode(",",$answers);
            $options->single = $question->single;
            $options->shuffleanswers = $question->shuffleanswers;
            if (!insert_record("question_multichoice", $options)) {
                $result->error = "Could not insert quiz multichoice options!";
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
        if ($options->single) {
            if ($maxfraction != 1) {
                $maxfraction = $maxfraction * 100;
                $result->noticeyesno = get_string("fractionsnomax", "quiz", $maxfraction);
                return $result;
            }
        } else {
            $totalfraction = round($totalfraction,2);
            if ($totalfraction != 1) {
                $totalfraction = $totalfraction * 100;
                $result->noticeyesno = get_string("fractionsaddwrong", "quiz", $totalfraction);
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
    function delete_question($question) {
        delete_records("question_multichoice", "question", $question->id);
        return true;
    }

    function create_session_and_responses(&$question, &$state, $cmoptions, $attempt) {
        // create an array of answerids ??? why so complicated ???
        $answerids = array_values(array_map(create_function('$val',
         'return $val->id;'), $question->options->answers));
        // Shuffle the answers if required
        if ($cmoptions->shuffleanswers and $question->options->shuffleanswers) {
           $answerids = swapshuffle($answerids);
        }
        $state->options->order = $answerids;
        // Create empty responses
        if ($question->options->single) {
            $state->responses = array('' => '');
        } else {
            $state->responses = array();
        }
        return true;
    }


    function restore_session_and_responses(&$question, &$state) {
        // The serialized format for multiple choice quetsions
        // is an optional comma separated list of answer ids (the order of the
        // answers) followed by a colon, followed by another comma separated
        // list of answer ids, which are the radio/checkboxes that were
        // ticked.
        // E.g. 1,3,2,4:2,4 means that the answers were shown in the order
        // 1, 3, 2 and then 4 and the answers 2 and 4 were checked.

        $pos = strpos($state->responses[''], ':');
        if (false === $pos) { // No order of answers is given, so use the default
            $state->options->order = array_keys($question->options->answers);
        } else { // Restore the order of the answers
            $state->options->order = explode(',', substr($state->responses[''], 0,
             $pos));
            $state->responses[''] = substr($state->responses[''], $pos + 1);
        }
        // Restore the responses
        // This is done in different ways if only a single answer is allowed or
        // if multiple answers are allowed. For single answers the answer id is
        // saved in $state->responses[''], whereas for the multiple answers case
        // the $state->responses array is indexed by the answer ids and the
        // values are also the answer ids (i.e. key = value).
        if (empty($state->responses[''])) { // No previous responses
            if ($question->options->single) {
                $state->responses = array('' => '');
            } else {
                $state->responses = array();
            }
        } else {
            if ($question->options->single) {
                $state->responses = array('' => $state->responses['']);
            } else {
                // Get array of answer ids
                $state->responses = explode(',', $state->responses['']);
                // Create an array indexed by these answer ids
                $state->responses = array_flip($state->responses);
                // Set the value of each element to be equal to the index
                array_walk($state->responses, create_function('&$a, $b',
                 '$a = $b;'));
            }
        }
        return true;
    }

    function save_session_and_responses(&$question, &$state) {
        // Bundle the answer order and the responses into the legacy answer
        // field.
        // The serialized format for multiple choice quetsions
        // is (optionally) a comma separated list of answer ids
        // followed by a colon, followed by another comma separated
        // list of answer ids, which are the radio/checkboxes that were
        // ticked.
        // E.g. 1,3,2,4:2,4 means that the answers were shown in the order
        // 1, 3, 2 and then 4 and the answers 2 and 4 were checked.
        $responses  = implode(',', $state->options->order) . ':';
        $responses .= implode(',', $state->responses);

        // Set the legacy answer field
        if (!set_field('question_states', 'answer', $responses, 'id',
         $state->id)) {
            return false;
        }
        return true;
    }

    function get_correct_responses(&$question, &$state) {
        if ($question->options->single) {
            foreach ($question->options->answers as $answer) {
                if (((int) $answer->fraction) === 1) {
                    return array('' => $answer->id);
                }
            }
            return null;
        } else {
            $responses = array();
            foreach ($question->options->answers as $answer) {
                if (((float) $answer->fraction) > 0.0) {
                    $responses[$answer->id] = (string) $answer->id;
                }
            }
            return empty($responses) ? null : $responses;
        }
    }

    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {
        global $CFG;

        $answers = &$question->options->answers;
        $correctanswers = $this->get_correct_responses($question, $state);
        $readonly = empty($options->readonly) ? '' : 'readonly="readonly"';

        $formatoptions = new stdClass;
        $formatoptions->para = false;

        // Print formulation
        $questiontext = format_text($question->questiontext,
                         $question->questiontextformat,
                         NULL, $cmoptions->course);
        $image = get_question_image($question, $cmoptions->course);
        $answerprompt = ($question->options->single) ? get_string('singleanswer', 'quiz') :
            get_string('multipleanswers', 'quiz');

        // Print each answer in a separate row
        foreach ($state->options->order as $key => $aid) {
            $answer = &$answers[$aid];
            $qnumchar = chr(ord('a') + $key);
            $checked = '';

            if ($question->options->single) {
                $type = 'type="radio"';
                $name   = "name=\"{$question->name_prefix}\"";
                if (isset($state->responses['']) and $aid == $state->responses['']) {
                    $checked = 'checked="checked"';
                }
            } else {
                $type = ' type="checkbox" ';
                $name   = "name=\"{$question->name_prefix}{$aid}\"";
                $checked = isset($state->responses[$aid])
                         ? 'checked="checked"' : '';
            }

            $a->id   = $question->name_prefix . $aid;

            // Print the control
            $a->control = "<input $readonly id=\"$a->id\" $name $checked $type value=\"$aid\"" .
                 " alt=\"" . s($answer->answer) . '" />';

            // Print the text by the control highlighting if correct responses
            // should be shown and the current answer is the correct answer in
            // the single selection case or has a positive score in the multiple
            // selection case
            $a->class = ($options->readonly && $options->correct_responses &&
                is_array($correctanswers) && in_array($aid, $correctanswers)) ?
                'highlight' : '';

            // Print the answer text
            $a->text = format_text("$qnumchar. $answer->answer", FORMAT_MOODLE, $formatoptions);

            // Print feedback if feedback is on
            $a->feedback = (($options->feedback || $options->correct_responses) && $checked) ?
               $feedback = format_text($answer->feedback, true, false) : '';

            $anss[] = clone($a);
        }
        include("$CFG->dirroot/question/questiontypes/multichoice/display.html");
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        $answers      = $question->options->answers;
        $testedstate = clone($state);
        $teststate    = clone($state);
        $state->raw_grade = 0;
        foreach($answers as $answer) {
            $teststate->responses = array('' => $answer->id);
            if($question->options->single) {
                if($this->compare_responses($question, $testedstate,
                 $teststate)) {
                    $state->raw_grade = $answer->fraction;
                    break;
                }
            } else {
                foreach($state->responses as $response) {
                    $testedstate->responses = array('' => $response);
                    if($this->compare_responses($question, $testedstate,
                     $teststate)) {
                        $state->raw_grade += $answer->fraction;
                         break;
                    }
                }
            }
        }
        if (empty($state->raw_grade)) {
            $state->raw_grade = 0;
        }

        // Make sure we don't assign negative or too high marks
        $state->raw_grade = min(max((float) $state->raw_grade,
                            0.0), 1.0) * $question->maxgrade;
        // Apply the penalty for this attempt
        $state->penalty = $question->penalty * $question->maxgrade;

        // mark the state as graded
        $state->event = ($state->event ==  QUESTION_EVENTCLOSE) ? QUESTION_EVENTCLOSEANDGRADE : QUESTION_EVENTGRADE;

        return true;
    }

    // ULPGC ecastro
    function get_actual_response($question, $state) {
        $answers = $question->options->answers;
        if (!empty($state->responses)) {
            foreach ($state->responses as $aid =>$rid){
                $answer = $answers[$rid]->answer;
                $responses[] = $answer;
            }
        } else {
            $responses[] = '';
        }
        return $responses;
    }
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
// define("MULTICHOICE",   "3"); // already defined in questionlib.php
$QTYPES[MULTICHOICE]= new question_multichoice_qtype();
// The following adds the questiontype to the menu of types shown to teachers
$QTYPE_MENU[MULTICHOICE] = get_string("multichoice", "quiz");

?>
