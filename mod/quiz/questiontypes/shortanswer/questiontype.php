<?PHP  // $Id$

///////////////////
/// SHORTANSWER ///
///////////////////

/// QUESTION TYPE CLASS //////////////////

///
/// This class contains some special features in order to make the
/// question type embeddable within a multianswer (cloze) question
///

class quiz_shortanswer_qtype extends quiz_default_questiontype {

    function get_answers($question, $addedcondition='') {
        // The added condition is one addition that has been added
        // to the behaviour of this question type in order to make
        // it embeddable within a multianswer (embedded cloze) question

        global $CFG;

        // There can be multiple answers
        return get_records_sql("SELECT a.*, sa.usecase
                                FROM {$CFG->prefix}quiz_shortanswer sa,
                                     {$CFG->prefix}quiz_answers a
                                WHERE sa.question = '$question->id'
                                  AND sa.question = a.question "
                                      . $addedcondition);

    }

    function name() {
        return 'shortanswer';
    }

    function save_question_options($question) {
        if (!$oldanswers = get_records("quiz_answers", "question", $question->id, "id ASC")) {
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
                    if (!update_record("quiz_answers", $answer)) {
                        $result->error = "Could not update quiz answer! (id=$answer->id)";
                        return $result;
                    }
                } else {    // This is a completely new answer
                    unset($answer);
                    $answer->answer   = trim($dataanswer);
                    $answer->question = $question->id;
                    $answer->fraction = $question->fraction[$key];
                    $answer->feedback = $question->feedback[$key];
                    if (!$answer->id = insert_record("quiz_answers", $answer)) {
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

        /// Perform sanity checks on fractional grades
        if ($maxfraction != 1) {
            $maxfraction = $maxfraction * 100;
            $result->noticeyesno = get_string("fractionsnomax", "quiz", $maxfraction);
            return $result;
        } else {
            return true;
        }
    }

    function print_question_formulation_and_controls($question,
            $quiz, $readonly, $answers, $correctanswers, $nameprefix) {
    /// This implementation is also used by question type NUMERICAL

        /// Print question text and media

        echo format_text($question->questiontext,
                         $question->questiontextformat,
                         NULL, $quiz->course);
        quiz_print_possible_question_image($quiz->id, $question);

        /// Print input controls

        $stranswer = get_string("answer", "quiz");
        if (isset($question->response[$nameprefix])) {
            $value = ' value="'.htmlSpecialChars($question->response[$nameprefix]).'" ';
        } else {
            $value = ' value="" ';
        }
        $inputname = ' name="'.$nameprefix.'" ';
        echo "<p align=\"right\">$stranswer: <input type=\"text\" $readonly $inputname size=\"80\" $value /></p>";

        if ($quiz->feedback && isset($answers[$nameprefix])
                && $feedback = $answers[$nameprefix]->feedback) {
           quiz_print_comment("<p align=\"right\">$feedback</p>");
        }
        if ($readonly && $quiz->correctanswers) {
            $delimiter = '';
            $correct = '';
            foreach ($correctanswers as $correctanswer) {
                $correct .= $delimiter.$correctanswer->answer;
                $delimiter = ', ';
            }
            quiz_print_correctanswer($correct);
        }
    }

    function grade_response($question, $nameprefix, $addedanswercondition='') {

        if (isset($question->response[$nameprefix])) {
            $response0 = trim(stripslashes($question->response[$nameprefix]));
        } else {
            $response0 = '';
        }
        $answers = $this->get_answers($question, $addedanswercondition);

        /// Determine ->answers[]
        $result->answers = array();
        if ('' !== $response0) {

            /// These are things to protect in the strings when wildcards are used
            $search = array('\\', '+', '(', ')', '[', ']', '-');
            $replace = array('\\\\', '\+', '\(', '\)', '\[', '\]', '\-');

            foreach ($answers as $answer) {

                $answer->answer = trim($answer->answer);  // Just in case

                if (empty($result->answers) || $answer->fraction
                        > $result->answers[$nameprefix]->fraction) {

                    if (!$answer->usecase) { // Don't compare case
                        $response0 = strtolower($response0);
                        $answer0 = strtolower($answer->answer);
                    } else {
                        $answer0 = $answer->answer;
                    }

                    if (strpos(' '.$answer0, '*')) {
                        $answer0 = str_replace('\*','@@@@@@',$answer0);
                        $answer0 = str_replace('*','.*',$answer0);
                        $answer0 = str_replace($search, $replace, $answer0);
                        $answer0 = str_replace('@@@@@@', '\*',$answer0);

                        if (ereg('^'.$answer0.'$', $response0)) {
                            $result->answers[$nameprefix] = $answer;
                        }

                    } else if ($answer0 == $response0) {
                        $result->answers[$nameprefix] = $answer;
                    }
                }
            }
        }


        $result->grade = isset($result->answers[$nameprefix])
                ?   $result->answers[$nameprefix]->fraction
                :   0.0;
        $result->correctanswers = quiz_extract_correctanswers($answers,
                                                              $nameprefix);
        return $result;
    }
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
$QUIZ_QTYPES[SHORTANSWER]= new quiz_shortanswer_qtype();

?>
