<?php  // $Id$

//////////////
/// RANDOM ///
//////////////

/// QUESTION TYPE CLASS //////////////////
class quiz_random_qtype extends quiz_default_questiontype {

    var $possiblerandomqtypes = array(SHORTANSWER,
                                      NUMERICAL,
                                      MULTICHOICE,
                                      MATCH,
                                   // RANDOMSAMATCH,// Can cause unexpected outcomes
                                      TRUEFALSE,
                                      MULTIANSWER);

    // Carries questions available as randoms sorted by category
    // This array is used when needed only
    var $catrandoms = array();

    function name() {
        return 'random';
    }

    function get_question_options(&$question) {
        // Don't do anything here, because the random question has no options.
        // Everything is handled by the create- or restore_session_and_responses
        // functions.
        return true;
    }

    function save_question_options($question) {
        // No options, but we use the parent field to hide random questions.
        // To avoid problems we set the parent field to the question id.
        return (set_field('quiz_questions', 'parent', $question->id, 'id',
         $question->id) ? true : false);
    }

    function create_session_and_responses(&$question, &$state, $quiz, $attempt) {
        // Choose a random question from the category:
        // We need to make sure that no question is used more than once in the
        // quiz. Therfore the following need to be excluded:
        // 1. All questions that are explicitly assigned to the quiz
        // 2. All random questions
        // 3. All questions that are already chosen by an other random question
        if (!isset($quiz->questionsinuse)) {
            $quiz->questionsinuse = $quiz->questions;
        }

        if (!isset($this->catrandoms[$question->category])) {
            // Need to fetch random questions from category $question->category"
            // (Note: $this refers to the questiontype, not the question.)
            global $CFG;
            $possiblerandomqtypes = "'"
                    . implode("','", $this->possiblerandomqtypes) . "'";
            if ($question->questiontext == "1") {
                // recurse into subcategories
                $categorylist = quiz_categorylist($question->category);
            } else {
                $categorylist = $question->category;
            }
            $this->catrandoms[$question->category] = get_records_sql
                    ("SELECT * FROM {$CFG->prefix}quiz_questions
                       WHERE category IN ($categorylist)
                         AND parent = '0'
                         AND id NOT IN ($quiz->questionsinuse)
                         AND qtype IN ($possiblerandomqtypes)");
            $this->catrandoms[$question->category] =
                  draw_rand_array($this->catrandoms[$question->category],
                            count($this->catrandoms[$question->category])); // from bug 1889
        }

        while ($wrappedquestion =
                array_pop($this->catrandoms[$question->category])) {
            if (!ereg("(^|,)$wrappedquestion->id(,|$)", $quiz->questionsinuse)) {
                /// $randomquestion is not in use and will therefore be used
                /// as the randomquestion here...

                global $QUIZ_QTYPES;
                $QUIZ_QTYPES[$wrappedquestion->qtype]
                 ->get_question_options($wrappedquestion);
                $QUIZ_QTYPES[$wrappedquestion->qtype]
                 ->create_session_and_responses($wrappedquestion, $state, $quiz,
                 $attempt);
                $wrappedquestion->name_prefix = $question->name_prefix;
                $wrappedquestion->maxgrade    = $question->maxgrade;
                $quiz->questionsinuse .= ",$wrappedquestion->id";

                $state->options->question = &$wrappedquestion;
                return true;
            }
        }
        notify(get_string('toomanyrandom', 'quiz', $question->category));
        return false;
    }

    function restore_session_and_responses(&$question, &$state) {
        global $QUIZ_QTYPES;
        if (!ereg('^random([0-9]+)-(.*)$', $state->responses[''], $answerregs)) {
            notify("The answer value '{$state->responses['']}' for the state with "
                    ."id=$state->id to the random question "
                    ."$question->id is malformated."
                    ." - No response can be extracted!");
            return false;
        }
        $state->responses[''] = $answerregs[2];

        if (!$wrappedquestion = get_record('quiz_questions', 'id', $answerregs[1])) {
            return false;
        }

        if (!$QUIZ_QTYPES[$wrappedquestion->qtype]
         ->get_question_options($wrappedquestion)) {
            return false;
        }

        if (!$QUIZ_QTYPES[$wrappedquestion->qtype]
         ->restore_session_and_responses($wrappedquestion, $state)) {
            return false;
        }
        $wrappedquestion->name_prefix = $question->name_prefix;
        $wrappedquestion->maxgrade    = $question->maxgrade;

        $state->options->question = &$wrappedquestion;
        return true;
    }

    function save_session_and_responses(&$question, &$state) {
        global $QUIZ_QTYPES;
        $wrappedquestion = &$state->options->question;

        // Trick the wrapped question into pretending to be the random one.
        $realqid = $wrappedquestion->id;
        $wrappedquestion->id = $question->id;
        $QUIZ_QTYPES[$wrappedquestion->qtype]
         ->save_session_and_responses($wrappedquestion, $state);

        // Read what the wrapped question has just set the answer field to
        // (if anything)
        $response = get_field('quiz_states', 'answer', 'id', $state->id);
        if(false === $response) {
            return false;
        }

        // Prefix the answer field...
        $response = "random$realqid-$response";

        // ... and save it again.
        if (!set_field('quiz_states', 'answer', $response, 'id', $state->id)) {
            return false;
        }

        // Restore the real id
        $wrappedquestion->id = $realqid;
        return true;
    }

    function get_correct_responses(&$question, &$state) {
        global $QUIZ_QTYPES;
        $wrappedquestion = &$state->options->question;
        return $QUIZ_QTYPES[$wrappedquestion->qtype]
         ->get_correct_responses($wrappedquestion, $state);
    }

    function print_question(&$question, &$state, &$number, $quiz, $options) {
        global $QUIZ_QTYPES;
        $wrappedquestion = &$state->options->question;
        $QUIZ_QTYPES[$wrappedquestion->qtype]
         ->print_question($wrappedquestion, $state, $number, $quiz, $options);
    }
/*
    function print_question_grading_details(&$question, &$state, $quiz,
     $options) {
        global $QUIZ_QTYPES;
        $wrappedquestion = &$state->options->question;
        $QUIZ_QTYPES[$wrappedquestion->qtype]
         ->print_question_grading_details($wrappedquestion, $state, $quiz,
         $options);
    }

    function print_question_formulation_and_controls(&$question, &$state, $quiz,
     $options) {
        global $QUIZ_QTYPES;
        $wrappedquestion = &$state->options->question;
        $QUIZ_QTYPES[$wrappedquestion->qtype]
         ->print_question_formulation_and_controls($wrappedquestion, $state,
         $quiz, $options);
    }

    function print_question_submit_buttons(&$question, &$state, $quiz,
     $options) {
        global $QUIZ_QTYPES;
        $wrappedquestion = &$state->options->question;
        $QUIZ_QTYPES[$wrappedquestion->qtype]
         ->print_question_submit_buttons($wrappedquestion, $state, $quiz,
         $options);
    }
*/
    function grade_responses(&$question, &$state, $quiz) {
        global $QUIZ_QTYPES;
        $wrappedquestion = &$state->options->question;
        return $QUIZ_QTYPES[$wrappedquestion->qtype]
         ->grade_responses($wrappedquestion, $state, $quiz);
    }

    function get_texsource(&$question, &$state, $quiz, $type) {
        global $QUIZ_QTYPES;
        $wrappedquestion = &$state->options->question;
        return $QUIZ_QTYPES[$wrappedquestion->qtype]
         ->get_texsource($wrappedquestion, $state, $quiz, $type);
    }

    function compare_responses(&$question, $state, $teststate) {
        global $QUIZ_QTYPES;
        $wrappedquestion = &$state->options->question;
        return $QUIZ_QTYPES[$wrappedquestion->qtype]
         ->compare_responses($wrappedquestion, $state, $teststate);
    }

    function print_replacement_options($question, $course, $quizid='0') {
        global $QUIZ_QTYPES;
        $wrappedquestion = &$state->options->question;
        return $QUIZ_QTYPES[$wrappedquestion->qtype]
         ->print_replacement_options($wrappedquestion, $state, $quizid);
    }

    function print_question_form_end($question, $submitscript='') {
        global $QUIZ_QTYPES;
        $wrappedquestion = &$state->options->question;
        return $QUIZ_QTYPES[$wrappedquestion->qtype]
         ->print_question_form_end($wrappedquestion, $state, $quizid);
    }
/*
    function convert_to_response_answer_field($questionresponse) {
        global $QUIZ_QTYPES;

        foreach ($questionresponse as $key => $response) {
            if (ereg('[^0-9][0-9]+random$', $key)) {
                unset($questionresponse[$key]);
                $randomquestion = get_record('quiz_questions',
                                             'id', $response);
                return "random$response-"
                        .$QUIZ_QTYPES[$randomquestion->qtype]
                        ->convert_to_response_answer_field($questionresponse);
            }
        }
        return '';
    }



    function create_response($question, $nameprefix, $questionsinuse) {
    // It's for question types like RANDOMSAMATCH and RANDOM that
    // the true power of the pattern with this function comes to the surface.


    }


*/
    /*
    function print_question_formulation_and_controls($question,
            $quiz, $readonly, $answers, $correctanswers, $nameprefix) {
        global $QUIZ_QTYPES;

        // Get the wrapped question...
        if ($actualquestion = $this->get_wrapped_question($question,
                                                          $nameprefix)) {
            echo '<input type="hidden" name="' . $nameprefix
                    . '" value="' . $actualquestion->id . '" />';
            return $QUIZ_QTYPES[$actualquestion->qtype]
                    ->print_question_formulation_and_controls($actualquestion,
                    $quiz, $readonly, $answers, $correctanswers,
                    quiz_qtype_nameprefix($actualquestion, $nameprefix));
        } else {
            echo '<p>' . get_string('random', 'quiz') . '</p>';
        }
    }


    function get_wrapped_question($question, $nameprefix) {
        if (!empty($question->response[$nameprefix])
                and $actualquestion = get_record('quiz_questions',
                'id', $question->response[$nameprefix])) {
            $actualquestion->response = $question->response;
            unset($actualquestion->response[$nameprefix]);
            $actualquestion->maxgrade = $question->maxgrade;
            return $actualquestion;
        } else {
            return false;
        }
    }

    function grade_response($question, $nameprefix) {
        global $QUIZ_QTYPES;

        // Get the wrapped question...
        if ($actualquestion = $this->get_wrapped_question($question,
                                                          $nameprefix)) {
            return $QUIZ_QTYPES[$actualquestion->qtype]->grade_response(
                    $actualquestion,
                    quiz_qtype_nameprefix($actualquestion, $nameprefix));
        } else {
            $result->grade = 0.0;
            $result->answers = array();
            $result->correctanswers = array();
            return $result;
        }
    }

*/

    function extract_response($rawresponse, $nameprefix) {
        global $QUIZ_QTYPES;

        /// The raw response records for random questions come in two flavours:
        /// ---- 1 ----
        /// For responses stored by Moodle version 1.5 and later the answer
        /// field has the pattern random#-* where the # part is the numeric
        /// question id of the actual question shown in the quiz attempt
        /// and * represents the student response to that actual question.
        /// ---- 2 ----
        /// For responses stored by older Moodle versions - the answer field is
        /// simply the question id of the actual question. The student response
        /// to the actual question is stored in a separate response record.
        /// -----------------------
        /// This means that prior to Moodle version 1.5, random questions needed
        /// two response records for storing the response to a single question.
        /// From version 1.5 and later the question type random works like all
        /// the other question types in that it now only needs one response
        /// record per question.
        /// Because updating the old response records to fit the new response
        /// record format could need hours of CPU time and the equivalent
        /// amount of down time for the Moodle site and because a response
        /// storage with two response formats for random question only effect
        /// this function, where the response record is translated, this
        /// function is now able to handle both types of response record.


        // Pick random question id from the answer field in a way that
        /// works for both formats:
        if (!ereg('^(random)?([0-9]+)(-(.*))?$', $rawresponse->answer, $answerregs)) {
            error("The answer value '$rawresponse->answer' for the response with "
                    ."id=$rawresponse->id to the random question "
                    ."$rawresponse->question is malformated."
                    ." - No response can be extracted!");
        }
        $randomquestionid = $answerregs[2];

        if ($randomquestion = get_record('quiz_questions',
                                         'id', $randomquestionid)) {

            if ($answerregs[1] && $answerregs[3]) {
                // The raw response is formatted according to
                // Moodle version 1.5 or later
                $randomresponse = $rawresponse;
                $randomresponse->question = $randomquestionid;
                $randomresponse->answer = $answerregs[4];

            } else if ($randomresponse = get_record
                    ('quiz_responses', 'question', $rawresponse->answer,
                                       'attempt', $rawresponse->attempt)) {
                // The response was stored by an older version  of Moodle
                // :-)

            } else {
                notify("Error: Cannot find response to random question $randomquestionid");
                unset($randomresponse);
            }

            if (isset($randomresponse)) {
                /// The prefered case:
                /// There is a random question and a response field, from
                /// which the response array can be extracted:


            } else {

                /// Instead: workaround by creating a new response:
                $response = $QUIZ_QTYPES[$randomquestion->qtype]
                        ->create_response($randomquestion,
                        quiz_qtype_nameprefix($randomquestion, $nameprefix),
                        "$rawresponse->question,$randomquestionid");
                // (That last argument is instead of $questionsinuse.
                // It is not correct but it would be very messy to
                // determine the correct value, while very few
                // question types actually use it and they who do have
                // good chances to execute properly anyway.)
            }
            $response[$nameprefix] = $randomquestionid;
            //return $response;
            return '';
        } else {
            notify("Error: Unable to find random question $rawresponse->question");
            /// No new random question is picked as this is probably
            /// not what the moodle user has in mind anyway
            return array();
        }
    }
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
$QUIZ_QTYPES[RANDOM]= new quiz_random_qtype();

?>
