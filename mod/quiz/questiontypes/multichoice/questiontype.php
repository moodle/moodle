<?php  // $Id$

///////////////////
/// MULTICHOICE ///
///////////////////

/// QUESTION TYPE CLASS //////////////////

///
/// This class contains some special features in order to make the
/// question type embeddable within a multianswer (cloze) question
///

class quiz_multichoice_qtype extends quiz_default_questiontype {

    function get_answers($question, $addedcondition= '') {
        // The added condition is one addition that has been added
        // to the behaviour of this question type in order to make
        // it embeddable within a multianswer (embedded cloze) question

        global $CFG;

        // There should be multiple answers
        return get_records_sql("SELECT a.*, mc.single
                                  FROM {$CFG->prefix}quiz_multichoice mc,
                                       {$CFG->prefix}quiz_answers a
                                 WHERE mc.question = '$question->id'
                                   AND mc.question = a.question "
                                       . $addedcondition);
    }

    function name() {
        return 'multichoice';
    }

    function save_question_options($question) {
        
        if (!$oldanswers = get_records("quiz_answers", "question",
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
                    $answer->answer   = $dataanswer;
                    $answer->fraction = $question->fraction[$key];
                    $answer->feedback = $question->feedback[$key];
                    if (!update_record("quiz_answers", $answer)) {
                        $result->error = "Could not update quiz answer! (id=$answer->id)";
                        return $result;
                    }
                } else {
                    unset($answer);
                    $answer->answer   = $dataanswer;
                    $answer->question = $question->id;
                    $answer->fraction = $question->fraction[$key];
                    $answer->feedback = $question->feedback[$key];
                    if (!$answer->id = insert_record("quiz_answers", $answer)) {
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

        if ($options = get_record("quiz_multichoice", "question", $question->id)) {
            $options->answers = implode(",",$answers);
            $options->single = $question->single;
            if (!update_record("quiz_multichoice", $options)) {
                $result->error = "Could not update quiz multichoice options! (id=$options->id)";
                return $result;
            }
        } else {
            unset($options);
            $options->question = $question->id;
            $options->answers = implode(",",$answers);
            $options->single = $question->single;
            if (!insert_record("quiz_multichoice", $options)) {
                $result->error = "Could not insert quiz multichoice options!";
                return $result;
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

    function extract_response($rawresponse, $nameprefix) {
        // Fetch additional details from the database...
        if (!$options = get_record("quiz_multichoice",
                                   "question", $rawresponse->question)) {
           notify("Error: Missing question options!");
        }

        if ($options->single) {
            return array($nameprefix => $rawresponse->answer);

        } else {
            $response = array();
            $answerids = explode(',', $options->answers);
            foreach ($answerids as $answerid) {
                $response[$nameprefix.$answerid] =
                        ereg("(,|^)$answerid(,|$)", $rawresponse->answer)
                        ? $answerid
                        : '';
            }
            return $response;
        }
    }

    function print_question_formulation_and_controls($question,
            $quiz, $readonly, $answers, $correctanswers, $nameprefix) {

        // Fetch additional details from the database...
        if (!$options = get_record("quiz_multichoice", "question", $question->id)) {
           notify("Error: Missing question options!");
        }
        if (!$answers = get_records_list("quiz_answers", "id", $options->answers)) {
           notify("Error: Missing question answers!");
        }

        // Print formulation
        echo format_text($question->questiontext,
                         $question->questiontextformat,
                         NULL, $quiz->course);
        quiz_print_possible_question_image($quiz->id, $question);

        // Print input controls and alternatives
        echo "<table align=\"right\">";
        $stranswer = get_string("answer", "quiz");
        echo "<tr><td valign=\"top\">$stranswer:&nbsp;&nbsp;</td><td>";
        echo "<table>";
        $answerids = explode(",", $options->answers);

        if ($quiz->shuffleanswers) {
           $answerids = swapshuffle($answerids);
        }

        // Handle the case of unanswered single-choice questions:
        if ($options->single) {
            $singleresponse = isset($question->response[$nameprefix])
                    ? $question->response[$nameprefix] : '0';
        }

        foreach ($answerids as $key => $aid) {
            $answer = $answers[$aid];
            $qnumchar = chr(ord('a') + $key);

            echo '<tr><td valign="top">';

            if ($options->single) {
                $type = ' type="radio" ';
                $name = " name=\"$nameprefix\" ";
                $checked = $singleresponse == $aid
                        ? ' checked="checked" ' : '';
            } else {
                $type = ' type="checkbox" ';
                $name = " name=\"$nameprefix$aid\" ";
                $checked = !empty($question->response[$nameprefix.$aid])
                        ? ' checked="checked" ' : '';
            }
            if ($readonly) {
                $readonly = ' readonly="readonly" disabled="disabled" ';
            }
            echo "<input $readonly $name $checked $type  value=\"$answer->id\" />";
           
            echo "</td>";
            if ($readonly and $quiz->correctanswers and !empty($correctanswers[$nameprefix.$aid])) {
                echo '<td valign="top" class="highlight">'.format_text("$qnumchar. $answer->answer").'</td>';
            } else {
                echo '<td valign="top">'.format_text("$qnumchar. $answer->answer").'</td>';
            }
            if ($quiz->feedback) {
               echo "<td valign=\"top\">&nbsp;";
               if ($checked) { // Simpliest condition to use here
                   quiz_print_comment($answer->feedback);
               }
               echo "</td>";
           }
           echo "</tr>";
        }
        echo "</table>";
        echo "</td></tr></table>";
    }

    function grade_response($question, $nameprefix, $addedanswercondition='') {

        $result->correctanswers = array();
        $result->answers = array();
        $result->grade = 0.0;

        $answers = $this->get_answers($question, $addedanswercondition);

        /// Set ->answers[] and ->grade
        if (!empty($question->response)) {
            foreach ($question->response as $name => $response) {
                if (isset($answers[$response])) {
                    $result->answers[$name] = $answers[$response];
                    $result->grade += $answers[$response]->fraction;
                }
            }
        }

        /// Set ->correctanswers[]
        foreach ($answers as $answer) {

            if ($answer->single) {
                $result->correctanswers =
                        quiz_extract_correctanswers($answers, $nameprefix);
                break;

            } else {
                if ($answer->fraction > 0.0) {
                    $result->correctanswers[$nameprefix.$answer->id] = $answer;
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
$QUIZ_QTYPES[MULTICHOICE]= new quiz_multichoice_qtype();

?>
