<?php  // $Id$

/////////////
/// MATCH ///
/////////////

/// QUESTION TYPE CLASS //////////////////
class quiz_match_qtype extends quiz_default_questiontype {

    function name() {
        return 'match';
    }

    function get_question_options(&$question) {
        $subquestions = get_records("quiz_match_sub", "question", $question->id, "id ASC" );
        $question->options->subquestions = $subquestions;
        return true;
    }

    function save_question_options($question) {

        if (!$oldsubquestions = get_records("quiz_match_sub", "question", $question->id, "id ASC")) {
            $oldsubquestions = array();
        }

        // following hack to check at least three answers exist
        $answercount = 0;
        foreach ($question->subquestions as $key=>$questiontext) {
            $answertext = $question->subanswers[$key];
            if (!empty($questiontext) and !empty($answertext)) {
                $answercount++;
            }
        }
        $answercount += count($oldsubquestions);
        if ($answercount < 3) { // check there are at lest 3 answers for matching type questions
            $result->notice = get_string("notenoughanswers", "quiz", "3");
            return $result;
        }

        $subquestions = array();

        // Insert all the new question+answer pairs
        foreach ($question->subquestions as $key => $questiontext) {
            $answertext = $question->subanswers[$key];
            if (!empty($questiontext) and !empty($answertext)) {
                if ($subquestion = array_shift($oldsubquestions)) {  // Existing answer, so reuse it
                    $subquestion->questiontext = $questiontext;
                    $subquestion->answertext   = $answertext;
                    if (!update_record("quiz_match_sub", $subquestion)) {
                        $result->error = "Could not insert quiz match subquestion! (id=$subquestion->id)";
                        return $result;
                    }
                } else {
                    unset($subquestion);
                    $subquestion->question = $question->id;
                    $subquestion->questiontext = $questiontext;
                    $subquestion->answertext   = $answertext;
                    if (!$subquestion->id = insert_record("quiz_match_sub", $subquestion)) {
                        $result->error = "Could not insert quiz match subquestion!";
                        return $result;
                    }
                }
                $subquestions[] = $subquestion->id;
            }
        }

        if (count($subquestions) < 3) {
            $result->noticeyesno = get_string("notenoughsubquestions", "quiz");
            return $result;
        }

        if ($options = get_record("quiz_match", "question", $question->id)) {
            $options->subquestions = implode(",",$subquestions);
            if (!update_record("quiz_match", $options)) {
                $result->error = "Could not update quiz match options! (id=$options->id)";
                return $result;
            }
        } else {
            unset($options);
            $options->question = $question->id;
            $options->subquestions = implode(",",$subquestions);
            if (!insert_record("quiz_match", $options)) {
                $result->error = "Could not insert quiz match options!";
                return $result;
            }
        }
        return true;
    }

    function create_session_and_responses(&$question, &$state, $cmoptions, $attempt) {
        if (!$state->options->subquestions = get_records('quiz_match_sub',
         'question', $question->id)) {
           notify('Error: Missing subquestions!');
           return false;
        }

        foreach ($state->options->subquestions as $key => $subquestion) {
            // This seems rather over complicated, but it is useful for the
            // randomsamatch questiontype, which can then inherit the print
            // and grading functions. This way it is possible to define multiple
            // answers per question, each with different marks and feedback.
            $answer = new stdClass();
            $answer->id       = $subquestion->id;
            $answer->answer   = $subquestion->answertext;
            $answer->fraction = 1.0;
            $state->options->subquestions[$key]->options
             ->answers[$subquestion->id] = clone($answer);
        }

        // Shuffle the answers if required
        $subquestionids = array_values(array_map(create_function('$val',
         'return $val->id;'), $state->options->subquestions));
        if ($cmoptions->shuffleanswers) {
           $subquestionids = swapshuffle($subquestionids);
        }
        $state->options->order = $subquestionids;
        // Create empty responses
        foreach ($subquestionids as $val) {
            $state->responses[$val] = '';
        }
        return true;
    }

    function restore_session_and_responses(&$question, &$state) {
        // The serialized format for matching questions is a comma separated
        // list of question answer pairs (e.g. 1-1,2-3,3-2), where the ids of
        // both refer to the id in the table quiz_match_sub.
        $responses = explode(',', $state->responses['']);
        $responses = array_map(create_function('$val',
         'return explode("-", $val);'), $responses);

        // Restore the previous responses
        $state->responses = array();
        if ($responses) {
            foreach ($responses as $response) {
                $state->responses[$response[0]] = $response[1];
            }
        }

        if (!$state->options->subquestions = get_records('quiz_match_sub',
         'question', $question->id)) {
           notify('Error: Missing subquestions!');
           return false;
        }

        foreach ($state->options->subquestions as $key => $subquestion) {
            // This seems rather over complicated, but it is useful for the
            // randomsamatch questiontype, which can then inherit the print
            // and grading functions. This way it is possible to define multiple
            // answers per question, each with different marks and feedback.
            $answer = new stdClass();
            $answer->id       = $subquestion->id;
            $answer->answer   = $subquestion->answertext;
            $answer->fraction = 1.0;
            $state->options->subquestions[$key]->options
             ->answers[$subquestion->id] = clone($answer);
        }

        return true;
    }

    function save_session_and_responses(&$question, &$state) {
        // Serialize responses
        $responses = array();
        foreach ($state->responses as $key => $val) {
            if ($key != '') {
                $responses[] = "$key-$val";
            }
        }
        $responses = implode(',', $responses);

        // Set the legacy answer field
        if (!set_field('quiz_states', 'answer', $responses, 'id',
         $state->id)) {
            return false;
        }
        return true;
    }

    function get_correct_responses(&$question, &$state) {
        $responses = array();
        foreach ($state->options->subquestions as $sub) {
            foreach ($sub->options->answers as $answer) {
                if (1 == $answer->fraction) {
                    $responses[$sub->id] = $answer->id;
                }
            }
        }
        return empty($responses) ? null : $responses;
    }

    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {
        $subquestions   = $state->options->subquestions;
        $correctanswers = $this->get_correct_responses($question, $state);
        $nameprefix     = $question->name_prefix;
        $answers        = array();
        $responses      = &$state->responses;

        foreach ($subquestions as $subquestion) {
            foreach ($subquestion->options->answers as $sub) {
                $answers[$sub->id] = $sub->answer;
            }
        }

        // Shuffle the answers
        $answers = draw_rand_array($answers, count($answers));

        // Print question text and possible image
        if (!empty($question->questiontext)) {
            echo format_text($question->questiontext,
                             $question->questiontextformat,
                             NULL, $cmoptions->course);
        }
        quiz_print_possible_question_image($question);

        ///// Print the input controls //////
        echo '<table border="0" cellpadding="10" align="right">';
        foreach ($state->options->order as $key) {
            $subquestion = $subquestions[$key];

            /// Subquestion text:
            echo '<tr><td align="left" valign="top">';
            echo format_text($subquestion->questiontext,
                $question->questiontextformat, NULL, $cmoptions->course);
            echo '</td>';

            /// Drop-down list:
            $menuname = $nameprefix.$subquestion->id;
            $response = isset($state->responses[$subquestion->id])
                        ? $state->responses[$subquestion->id] : '0';
            if ($options->readonly
                and $options->correct_responses
                and isset($correctanswers[$subquestion->id])
                and ($correctanswers[$subquestion->id] == $response)) {
                $class = ' class="highlight" ';
            } else {
                $class = '';
            }
            echo "<td align=\"right\" valign=\"top\" $class>";

            choose_from_menu($answers, $menuname, $response, 'choose', '', 0,
             false, $options->readonly);

            // Neither the editing interface or the database allow to provide
            // fedback for this question type.
            // However (as was pointed out in bug bug 3294) the randomsamatch
            // type which reuses this method can have feedback defined for
            // the wrapped shortanswer questions.
            if ($options->feedback
             && !empty($subquestion->options->answers[$responses[$key]]->feedback)) {
                quiz_print_comment($subquestion->options->answers[$responses[$key]]->feedback);
            }
            echo '</td></tr>';
        }
        echo '</table>';
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        $subquestions = $state->options->subquestions;
        $responses    = &$state->responses;

        $answers = array();
        foreach ($subquestions as $subquestion) {
            foreach ($subquestion->options->answers as $sub) {
                $answers[$sub->id] = $sub->answer;
            }
        }

        $sumgrade = 0;
        foreach ($subquestions as $key => $sub) {
            if (isset($sub->options->answers[$responses[$key]])) {
                $sumgrade += $sub->options->answers[$responses[$key]]->fraction;
            }
        }

        $state->raw_grade = $sumgrade/count($subquestions);
        if (empty($state->raw_grade)) {
            $state->raw_grade = 0;
        }

        // Make sure we don't assign negative or too high marks
        $state->raw_grade = min(max((float) $state->raw_grade,
                            0.0), 1.0) * $question->maxgrade;
        $state->penalty = $question->penalty * $question->maxgrade;

        return true;
    }

    // ULPGC ecastro for stats report
    function get_all_responses($question, $state) {
        unset($answers);
        if (is_array($question->options->subquestions)) {
            foreach ($question->options->subquestions as $aid=>$answer) {
                unset ($r);
                $r->answer = $answer->questiontext." : ".$answer->answertext;
                $r->credit = 1;
                $answers[$aid] = $r;
            }
        } else {
            $answers[]="error"; // just for debugging, eliminate
        }
        $result->id = $question->id;
        $result->responses = $answers;
        return $result;
    }

    // ULPGC ecastro
    function get_actual_response($question, $state) {
        unset($results);
        if (isset($state->responses)) {
            foreach($state->responses as $left=>$right){
                $lpair = $question->options->subquestions[$left]->questiontext;
                $rpair = $question->options->subquestions[$right]->answertext;
                $results[$left] = $lpair." : ".$rpair;
            }
            return $results;
        } else {
            return null;
        }
    }


}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
$QUIZ_QTYPES[MATCH]= new quiz_match_qtype();

?>
