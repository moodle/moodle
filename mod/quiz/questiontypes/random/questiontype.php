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

    function save_question_options($question) {
        /// No options to be saved for this question type:
        return true;
    }

    function wrapped_questions($question) {
        global $QUIZ_QTYPES;
        
        foreach ($question->response as $key => $response) {
            if (ereg('[^0-9][0-9]+random$', $key)) {
                $randomquestion = get_record('quiz_questions',
                                             'id', $response);
                $randomquestion->response = $question->response;
                unset($randomquestion->response[$key]);
                if ($subwrapped = $QUIZ_QTYPES[$randomquestion->qtype]
                        ->wrapped_questions($randomquestion)) {
                    return "$response,$subwrapped";
                } else {
                    return $response;
                }
            }
        }
        return false;
    }

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

        global $CFG;

        if (!isset($this->catrandoms[$question->category])) {
            //Need to fetch random questions from category $question->category"

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
                         AND id NOT IN ($questionsinuse)
                         AND qtype IN ($possiblerandomqtypes)");
            $this->catrandoms[$question->category] = 
                  draw_rand_array($this->catrandoms[$question->category], 
                            count($this->catrandoms[$question->category])); // from bug 1889
        }

        while ($randomquestion =
                array_pop($this->catrandoms[$question->category])) {
            if (!ereg("(^|,)$randomquestion->id(,|$)", $questionsinuse)) {
                /// $randomquestion is not in use and will therefore be used
                /// as the randomquestion here...

                global $QUIZ_QTYPES;
                $response = $QUIZ_QTYPES[$randomquestion->qtype]
                        ->create_response($randomquestion, 
                        quiz_qtype_nameprefix($randomquestion, $nameprefix),
                        "$questionsinuse,$randomquestion->id");
                $response[$nameprefix] = $randomquestion->id;
                return $response;
            }
        }
        notify(get_string('toomanyrandom', 'quiz', $question->category));
        return array();
    }

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


        /*** Pick random question id from the answer field in a way that ***/
        /*** works for both formats: ***/
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
                /**** The raw response is formatted according to
                /**** Moodle version 1.5 or later ****/
                $randomresponse = $rawresponse;
                $randomresponse->question = $randomquestionid;
                $randomresponse->answer = $answerregs[4];

            } else if ($randomresponse = get_record
                    ('quiz_responses', 'question', $rawresponse->answer,
                                       'attempt', $rawresponse->attempt)) {
                /*** The response was stored by an older version  of Moodle */
                /*** :-)  ***/
                
            } else {
                notify("Error: Cannot find response to random question $randomquestionid");
                unset($randomresponse);
            }
            
            if (isset($randomresponse)) {
                /// The prefered case:
                /// There is a random question and a response field, from 
                /// which the response array can be extracted:

                $response = $QUIZ_QTYPES[$randomquestion->qtype]
                        ->extract_response($randomresponse,
                        quiz_qtype_nameprefix($randomquestion, $nameprefix));

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
            return $response;
        } else {
            notify("Error: Unable to find random question $rawresponse->question");
            /// No new random question is picked as this is probably
            /// not what the moodle user has in mind anyway
            return array();
        }
    }

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
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
$QUIZ_QTYPES[RANDOM]= new quiz_random_qtype();

?>
