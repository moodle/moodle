<?PHP  // $Id$

/////////////
/// MATCH ///
/////////////

/// QUESTION TYPE CLASS //////////////////
class quiz_match_qtype extends quiz_default_questiontype {

    function name() {
        return 'match';
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
    
    function convert_to_response_answer_field($questionresponse) {
    /// This method, together with extract_response, should be
    /// obsolete as soon as we get a better response storage

        $delimiter = '';
        $responseanswerfield = '';
        foreach ($questionresponse as $key => $value) {
            if ($matchid = $this->extract_response_id($key)) {
                $responseanswerfield .= "$delimiter$matchid-$value";
                $delimiter = ',';
            } else {
                notify("Error: Illegal match key $key detected");
            }
        }
        return $responseanswerfield;
    }

    function extract_response($rawresponse, $nameprefix) {
        if (!($options = get_record("quiz_match",
                                    "question", $rawresponse->question))) {
            notify("Error: Missing question options!");
            return array();
        }
        $subids = explode(',', $options->subquestions);
        foreach ($subids as $subid) {
            $response[$nameprefix.$subid] =
                    ereg("(^|,)$subid-([^,]+)", $rawresponse->answer, $regs)
                    ? $regs[2]
                    : '';
        }
        return $response;
    }

    function print_question_formulation_and_controls($question,
            $quiz, $readonly, $answers, $correctanswers, $nameprefix) {

        // Print question text and possible image
        if (!empty($question->questiontext)) {
            echo format_text($question->questiontext,
                             $question->questiontextformat,
                             NULL, $quiz->course);
        }
        quiz_print_possible_question_image($quiz->id, $question);

        // It so happens to be that $correctanswers for this question type also
        // contains the subqustions, which we need to make sure we have:
        if (empty($correctanswers)) {
            $options = get_record('quiz_match', 'question', $question->id)
            and $subquestions = get_records_list('quiz_match_sub', 'id',
                                                   $options->subquestions);
        } else {
            $subquestions = $correctanswers;
        }

        /// Check whether everything turned out alright:
        if (empty($subquestions)) {
            notify("Error: Missing subquestions for this question!");            

        } else {
            /// Everything is fine -
            /// Set up $subquestions and $answers and do the shuffling:

            if ($quiz->shuffleanswers) {
                $subquestions = draw_rand_array($subquestions,
                                                count($subquestions));
            }
            foreach ($subquestions as $key => $subquestion) {
                unset($answers[$key]);
                $answers[$subquestion->id] = $subquestion->answertext;
            }
            $answers = draw_rand_array($answers, count($answers));
        }

        ///// Ptint the input controls //////

        echo '<table border="0" cellpadding="10" align="right">';
        foreach ($subquestions as $subquestion) {

            /// Subquestion text:
            echo '<tr><td align="left" valign="top">';
            echo $subquestion->questiontext;
            echo '</td>';

            /// Drop-down list:
            $menuname = $nameprefix.$subquestion->id;
            $response = isset($question->response[$menuname])
                        ? $question->response[$menuname] : '0';
            if ($readonly 
                and $quiz->correctanswers 
                and isset($correctanswers[$menuname])
                and ($correctanswers[$menuname]->id == $response)) {
                $class = ' class="highlight" ';
            } else {
                $class = '';
            }
            echo "<td align=\"right\" valign=\"top\" $class>";
            choose_from_menu($answers, $menuname, $response);
            if ($quiz->feedback && isset($answers[$menuname])
                    && $answers[$menuname]->feedback) {
                quiz_print_comment($answers[$menuname]->feedback);
            }
            echo '</td></tr>';
        }
        echo '</table>';
    }

    function grade_response($question, $nameprefix) {
    /// This question type does not use the table quiz_answers
    /// but we will take some measures to emulate that record anyway.

        $result->grade = 0.0;
        $result->answers = array();
        $result->correctanswers = array();

        if (!($options = get_record('quiz_match', 'question', $question->id)
                and $subquestions = get_records_list('quiz_match_sub',
                                          'id', $options->subquestions))) {
            notify("Error: Cannot find match options and subquestions
                    for question $question->id");
            return $result;
        }

        $fraction = 1.0 / count($subquestions);

        /// Populate correctanswers arrays:
        foreach ($subquestions as $subquestion) {
            $subquestion->fraction = $fraction;
            $subquestion->answer = $subquestion->answertext;
            $subquestion->feedback = '';
            $result->correctanswers[$nameprefix.$subquestion->id] =
                    $subquestion;
        }

        foreach ($question->response as $responsekey => $answerid) {

            if ($answerid and $answer =
                    $result->correctanswers[$nameprefix.$answerid]) {

                if ($result->correctanswers[$responsekey]->answer
                        == $answer->answer) {

                    /// The response was correct!
                    $result->answers[$responsekey] =
                            $result->correctanswers[$responsekey];
                    $result->grade += $fraction;

                } else {
                    /// The response was incorrect:
                    $answer->fraction = 0.0;
                    $result->answers[$responsekey] = $answer;
                }

            }
        }
        return $result;
    }
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
$QUIZ_QTYPES[MATCH]= new quiz_match_qtype();

?>
