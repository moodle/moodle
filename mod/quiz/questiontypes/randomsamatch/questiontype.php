<?PHP  // $Id$

/////////////////////
/// RANDOMSAMATCH ///
/////////////////////

/// The use of this question type together with the
/// question type RANDOM within the same quiz can cause
/// a shortanswer question to appear in a RANDOM question
/// as well as one of the matcher questions in a question of this type

/// QUESTION TYPE CLASS //////////////////
class quiz_randomsamatch_qtype extends quiz_match_qtype {
/// Extends MATCH as there are quite a few simularities...

    // $catrandoms carries question ids for shortanswer questions
    // available as random questios.
    // They are sorted by category.
    var $catrandoms = array();

    function name() {
        return 'randomsamatch';
    }

    function save_question_options($question) {
        $options->question = $question->id;
        $options->choose = $question->choose;
        if ($existing = get_record("quiz_randomsamatch",
                                   "question", $options->question)) {
            $options->id = $existing->id;
            if (!update_record("quiz_randomsamatch", $options)) {
                $result->error = "Could not update quiz randomsamatch options!";
                return $result;
            }
        } else {
            if (!insert_record("quiz_randomsamatch", $options)) {
                $result->error = "Could not insert quiz randomsamatch options!";
                return $result;
            }
        }
        return true;
    }
    
    function wrapped_questions($question) {
        if (empty($question->response)) {
            return false;
        } else {
            $wrapped = '';
            $delimiter = '';
            foreach ($question->response as $rkey => $response) {
                $wrapped .= $delimiter.$this->extract_response_id($rkey);
                $delimiter = ',';
            }
            return $wrapped;
        }
    }

    function create_response($question, $nameprefix, $questionsinuse) {
    // It's for question types like RANDOMSAMATCH and RANDOM that
    // the true power of the pattern with this function comes to the surface.
    // This implementation will stand even after a possible exclusion of
    // the funtions extract_response and convert_to_response_answer_field

        if (!isset($this->catrandoms[$question->category])) {
            /// Need to fetch the shortanswer question ids for the category:

            $saquestions = get_records_select('quiz_questions',
                    " category='$question->category'
                      AND qtype='".SHORTANSWER."'
                      AND id NOT IN ($questionsinuse) ");
            $this->catrandoms[$question->category] = array_keys($saquestions);
            shuffle($this->catrandoms[$question->category]);
        }

        /// Access question options to find out how many short-answer
        /// questions we are supposed to pick...
        if ($options = get_record('quiz_randomsamatch',
                                  'question', $question->id)) {
            $questionstopick = $options->choose;
        } else {
            notify("Error: Missing question options! - Try to pick two shortanswer questions anyway");
            $questionstopick = 2;
        }

        /// Pick the short-answer question ids and create the $response array
        $response = array();
        while ($questionstopick) {
            $said = array_pop($this->catrandoms[$question->category]);
            if (!ereg("(^|,)$said(,|$)", $questionsinuse)) {
                $response[$nameprefix.$said] = '0';
                --$questionstopick;
            }
        }

        if ($questionstopick) {
            notify("Error: could not get enough Short-Answer questions!");
            $count = count($response);
            $wanted = $count + $questionstopick;
            notify("Got $count Short-Answer questions, but wanted $wanted.");
        }

        return $response;
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

    function print_question_formulation_and_controls($question,
            $quiz, $readonly, $answers, $correctanswers, $nameprefix) {

        // Print question formulation

        echo format_text($question->questiontext,
                         $question->questiontextformat, NULL, $quiz->course);
        quiz_print_possible_question_image($quiz->id, $question);

        // Summarize shortanswer questions answer alternatives:
        if (empty($correctanswers)) {
            // Get them using the grade_response method
            $tempresult = $this->grade_response($question, $nameprefix);
            $saanswers = $tempresult->correctanswers;
        } else {
            $saanswers = $correctanswers;
        }
        foreach ($saanswers as $key => $saanswer) {
            unset($saanswers[$key]); // Unsets the nameprefix occurence
            $saanswers[$saanswer->id] = trim($saanswer->answer);
        }
        $saanswers = draw_rand_array($saanswers, count($saanswers));

        // Print the shortanswer questions and input controls:
        echo '<table border="0" cellpadding="10">';
        foreach ($question->response as $inputname => $response) {
            if (!($saquestion = get_record('quiz_questions', 'id',
                    quiz_extract_posted_id($inputname, $nameprefix)))) {
                notify("Error: cannot find shortanswer question for $inputname ");
                continue;
            }
            
            echo '<tr><td align="left" valign="top">';
            echo $saquestion->questiontext;
            echo '</td>';
            echo '<td align="right" valign="top">';
            if (!empty($correctanswers)
                    && $correctanswers[$inputname]->id == $response) {
                echo '<span="highlight">';
                choose_from_menu($saanswers, $inputname, $response);
                echo '</span><br />';
            } else {
                choose_from_menu($saanswers, $inputname, $response);
                if ($readonly && $quiz->correctanswers
                        && isset($correctanswer[$inputname])) {
                    quiz_print_correctanswer($correctanswer[$inputname]->answer);
                }
            }
            if ($quiz->feedback && isset($answers[$inputname])
                    && $answers[$inputname]->feedback) {
                quiz_print_comment($answers[$inputname]->feedback);
            }
            echo '</td></tr>';
        }
        echo '</table>';
    }

    function grade_response($question, $nameprefix) {
        global $QUIZ_QTYPES;

        $result->answers = array();
        $result->correctanswers = array();
        $result->grade = 0.0;
        
        foreach ($question->response as $inputname => $subresponse) {
            if ($subquestion = get_record('quiz_questions',
                    'id', quiz_extract_posted_id($inputname, $nameprefix),
                    // These two query conditions are security checks that prevents cheating...
                    'qtype', SHORTANSWER,
                    'category', $question->category)) {

                if ($subresponse = get_record('quiz_answers',
                                              'id', $subresponse)) {
                    $subquestion->response[$inputname] = $subresponse->answer;
                } else {
                    $subquestion->response[$inputname] = '';
                }

                // Use the shortanswer framework to for grading...
                $subresult = $QUIZ_QTYPES[SHORTANSWER]
                            ->grade_response($subquestion, $inputname);

                // Summarize shortanswer results
                if (isset($subresult->answers[$inputname])) {
                    $result->answers[$inputname] =
                            $subresult->answers[$inputname];
                    $result->grade += $result->answers[$inputname]->fraction;
                    if ($result->answers[$inputname]->fraction >= 1.0) {
                        $result->correctanswers[$inputname] =
                                $result->answers[$inputname];
                        continue;
                    }
                }
                // Pick the first correctanswer:
                foreach ($subresult->correctanswers as $correct) {
                    $result->correctanswers[$inputname] = $correct;
                    break;
                }
            }
        }
        if ($result->grade) {
            $result->grade /= count($question->response);
        }
        return $result;
    }
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
$QUIZ_QTYPES[RANDOMSAMATCH]= new quiz_randomsamatch_qtype();

?>
