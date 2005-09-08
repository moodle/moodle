<?php  // $Id$

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

    function name() {
        return 'randomsamatch';
    }

    function get_question_options(&$question) {
        if (!$question->options->choose = get_field('quiz_randomsamatch',
         'choose', 'question', $question->id)) {
            notify('Error: Missing question options!');
            return false;
        }

        // This could be included as a flag in the database. It's already
        // supported by the code.
        // Recurse subcategories: 0 = no recursion, 1 = recursion
        $question->options->subcats = 1;
        return true;

    }

    function save_question_options($question) {
        $options->question = $question->id;
        $options->choose = $question->choose;

        if (2 > $question->choose) {
            $result->error = "At least two shortanswer questions need to be chosen!";
            return $result;
        }

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

    function create_session_and_responses(&$question, &$state, $quiz, $attempt) {
        // Choose a random shortanswer question from the category:
        // We need to make sure that no question is used more than once in the
        // quiz. Therfore the following need to be excluded:
        // 1. All questions that are explicitly assigned to the quiz
        // 2. All random questions
        // 3. All questions that are already chosen by an other random question
        global $QUIZ_QTYPES;
        if (!isset($quiz->questionsinuse)) {
            $quiz->questionsinuse = $quiz->questions;
        }

        if ($question->options->subcats) {
            // recurse into subcategories
            $categorylist = quiz_categorylist($question->category);
        } else {
            $categorylist = $question->category;
        }

        $saquestions = $this->get_sa_candidates($categorylist, $quiz->questionsinuse);

        $count  = count($saquestions);
        $wanted = $question->options->choose;
        $errorstr = '';
        if ($count < $wanted && isteacherinanycourse()) {
            if ($count >= 2) {
                $errorstr =  "Error: could not get enough Short-Answer questions!
                 Got $count Short-Answer questions, but wanted $wanted.
                 Reducing number to choose from to $count!";
                $wanted = $question->options->choose = $count;
            } else {
                $errorstr = "Error: could not get enough Short-Answer questions!
                 This can happen if all available Short-Answer questions are already
                 taken up by other Random questions or Random Short-Answer question.
                 Another possible cause for this error is that Short-Answer
                 questions were deleted after this Random Short-Answer question was
                 created.";
            }
            notify($errorstr);
            $errorstr = '<span class="notifyproblem">' . $errorstr . '</span>';
        }

        if ($count < $wanted) {
            $question->questiontext = "$errorstr<br /><br />Insufficient selection options are
             available for this question, therefore it is not available in  this
             quiz. Please inform your teacher.";
            // Treat this as a description from this point on
            $question->qtype = DESCRIPTION;
            return true;
        }

        $saquestions =
         draw_rand_array($saquestions, $question->options->choose); // from bug 1889

        foreach ($saquestions as $key => $wrappedquestion) {
            if (!$QUIZ_QTYPES[$wrappedquestion->qtype]
             ->get_question_options($wrappedquestion)) {
                return false;
            }

            // Now we overwrite the $question->options->answers field to only
            // *one* (the first) correct answer. This loop can be deleted to
            // take all answers into account (i.e. put them all into the
            // drop-down menu.
            $foundcorrect = false;
            foreach ($wrappedquestion->options->answers as $answer) {
                if ($foundcorrect || $answer->fraction != 1.0) {
                    unset($wrappedquestion->options->answers[$answer->id]);
                } else if (!$foundcorrect) {
                    $foundcorrect = true;
                }
            }

            if (!$QUIZ_QTYPES[$wrappedquestion->qtype]
             ->create_session_and_responses($wrappedquestion, $state, $quiz,
             $attempt)) {
                return false;
            }
            $wrappedquestion->name_prefix = $question->name_prefix;
            $wrappedquestion->maxgrade    = $question->maxgrade;
            $quiz->questionsinuse .= ",$wrappedquestion->id";
            $state->options->subquestions[$key] = clone($wrappedquestion);
        }

        // Shuffle the answers if required
        $subquestionids = array_values(array_map(create_function('$val',
         'return $val->id;'), $state->options->subquestions));
        if ($quiz->shuffleanswers) {
           $subquestionids = swapshuffle($subquestionids);
        }

        // Create empty responses
        foreach ($subquestionids as $val) {
            $state->responses[$val] = '';
        }
        return true;
    }

    function restore_session_and_responses(&$question, &$state) {
        global $QUIZ_QTYPES;
        if (empty($state->responses[''])) {
            $question->questiontext = "Insufficient selection options are
             available for this question, therefore it is not available in  this
             quiz. Please inform your teacher.";
            // Treat this as a description from this point on
            $question->qtype = DESCRIPTION;
        } else {
            $responses = explode(',', $state->responses['']);
            $responses = array_map(create_function('$val',
             'return explode("-", $val);'), $responses);

            // Restore the previous responses
            $state->responses = array();
            foreach ($responses as $response) {
                $state->responses[$response[0]] = $response[1];
                if (!$wrappedquestion = get_record('quiz_questions', 'id',
                 $response[0])) {
                    notify("Couldn't get question (id=$response[0])!");
                    return false;
                }
                if (!$QUIZ_QTYPES[$wrappedquestion->qtype]
                 ->get_question_options($wrappedquestion)) {
                    notify("Couldn't get question options (id=$response[0])!");
                    return false;
                }

                // Now we overwrite the $question->options->answers field to only
                // *one* (the first) correct answer. This loop can be deleted to
                // take all answers into account (i.e. put them all into the
                // drop-down menu.
                $foundcorrect = false;
                foreach ($wrappedquestion->options->answers as $answer) {
                    if ($foundcorrect || $answer->fraction != 1.0) {
                        unset($wrappedquestion->options->answers[$answer->id]);
                    } else if (!$foundcorrect) {
                        $foundcorrect = true;
                    }
                }

                if (!$QUIZ_QTYPES[$wrappedquestion->qtype]
                 ->restore_session_and_responses($wrappedquestion, $state)) {
                    notify("Couldn't restore session of question (id=$response[0])!");
                    return false;
                }
                $wrappedquestion->name_prefix = $question->name_prefix;
                $wrappedquestion->maxgrade    = $question->maxgrade;

                $state->options->subquestions[$wrappedquestion->id] =
                 clone($wrappedquestion);
            }
        }
        return true;
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

    function get_sa_candidates($categorylist, $questionsinuse=0) {
        return get_records_select('quiz_questions',
         "qtype = '".SHORTANSWER."' " .
         "AND category IN ($categorylist) " .
         "AND parent = '0' " .
         "AND hidden = '0'" .
         "AND id NOT IN ($questionsinuse)");
    }
}

//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
$QUIZ_QTYPES[RANDOMSAMATCH]= new quiz_randomsamatch_qtype();

?>
