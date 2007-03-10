<?php  // $Id$

//////////////
/// RANDOM ///
//////////////

/// QUESTION TYPE CLASS //////////////////
class quiz_random_qtype extends quiz_default_questiontype {

    var $excludedtypes = array(RANDOM, RANDOMSAMATCH);

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
        // 4. Deleted questions
        if (!isset($quiz->questionsinuse)) {
            $quiz->questionsinuse = $attempt->layout;
        }

        if (!isset($this->catrandoms[$question->category][$question->questiontext])) {
            // Need to fetch random questions from category $question->category"
            // (Note: $this refers to the questiontype, not the question.)
            global $CFG;
            $excludedtypes = implode(',', $this->excludedtypes);
            if ($question->questiontext == "1") {
                // recurse into subcategories
                $categorylist = quiz_categorylist($question->category);
            } else {
                $categorylist = $question->category;
            }
            if ($catrandoms = get_records_select('quiz_questions',
                    "category IN ($categorylist)
                         AND parent = '0'
                         AND hidden = '0'
                         AND id NOT IN ($quiz->questionsinuse)
                         AND qtype NOT IN ($excludedtypes)", '', 'id, id')) {
                $this->catrandoms[$question->category][$question->questiontext] =
                        draw_rand_array($catrandoms, count($catrandoms));
            } else {
                $this->catrandoms[$question->category][$question->questiontext] = array();
            }
        }

        while ($wrappedquestion =
                array_pop($this->catrandoms[$question->category][$question->questiontext])) {
            if (!ereg("(^|,)$wrappedquestion->id(,|$)", $quiz->questionsinuse)) {
                /// $randomquestion is not in use and will therefore be used
                /// as the randomquestion here...
                $wrappedquestion = get_record('quiz_questions', 'id', $wrappedquestion->id);
                global $QUIZ_QTYPES;
                $QUIZ_QTYPES[$wrappedquestion->qtype]
                 ->get_question_options($wrappedquestion);
                $QUIZ_QTYPES[$wrappedquestion->qtype]
                 ->create_session_and_responses($wrappedquestion,
                 $state, $quiz, $attempt);
                $wrappedquestion->name_prefix = $question->name_prefix;
                $wrappedquestion->maxgrade    = $question->maxgrade;
                $quiz->questionsinuse .= ",$wrappedquestion->id";
                $state->options->question = &$wrappedquestion;
                return true;
            }
        }
        $question->questiontext = '<span class="notifyproblem">'.
         get_string('toomanyrandom', 'quiz', $question->category). '</span>';
        $question->qtype = DESCRIPTION;
        $state->responses = array('' => '');
        return true;
    }

    function restore_session_and_responses(&$question, &$state) {
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
        global $QUIZ_QTYPES;
        if (!ereg('^random([0-9]+)-(.*)$', $state->responses[''], $answerregs)) {
            if (empty($state->responses[''])) {
                // This is the case if there weren't enough questions available in the category.
                $question->questiontext = '<span class="notifyproblem">'.
                 get_string('toomanyrandom', 'quiz', $question->category). '</span>';
                $question->qtype = DESCRIPTION;
                return true;
            }
            // this must be an old-style state which stores only the id for the wrapped question
            if (!$wrappedquestion = get_record('quiz_questions', 'id', $state->responses[''])) {
                notify("Can not find wrapped question {$state->responses['']}");
            }
            // In the old model the actual response was stored in a separate entry in
            // the state table and fortunately there was only a single state per question
            if (!$state->responses[''] = get_field('quiz_states', 'answer', 'attempt', $state->attempt, 'question', $wrappedquestion->id)) {
                notify("Wrapped state missing");
            }
        } else {
            if (!$wrappedquestion = get_record('quiz_questions', 'id', $answerregs[1])) {
                return false;
            }
            $state->responses[''] = (false === $answerregs[2]) ? '' : $answerregs[2];
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
        if (!set_field('quiz_states', 'answer', addslashes($response), 'id', $state->id)) {
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

    // ULPGC ecastro
    function get_all_responses(&$question, &$state){
        global $QUIZ_QTYPES;
        $wrappedquestion = &$state->options->question;
        return $QUIZ_QTYPES[$wrappedquestion->qtype]
         ->get_all_responses($wrappedquestion, $state);
    }

    // ULPGC ecastro
    function get_actual_response(&$question, &$state){
        global $QUIZ_QTYPES;
        $wrappedquestion = &$state->options->question;
        return $QUIZ_QTYPES[$wrappedquestion->qtype]
         ->get_actual_response($wrappedquestion, $state);
    }


    function print_question(&$question, &$state, &$number, $quiz, $options) {
        global $QUIZ_QTYPES;
        $wrappedquestion = &$state->options->question;
        $QUIZ_QTYPES[$wrappedquestion->qtype]
         ->print_question($wrappedquestion, $state, $number, $quiz, $options);
    }

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

}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
$QUIZ_QTYPES[RANDOM]= new quiz_random_qtype();

?>
