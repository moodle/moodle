<?php  // $Id$
/**
* Code for handling and processing questions
*
* This is code that is module independent, i.e., can be used by any module that
* uses questions, like quiz, lesson, ..
* This script also loads the questiontype classes
* Code for handling the editing of questions is in {@link editlib.php}
* @version $Id$
* @author Martin Dougiamas and many others. This has recently been completely
*         rewritten by Alex Smith, Julian Sedding and Gustav Delius as part of
*         the Serving Mathematics project
*         {@link http://maths.york.ac.uk/serving_maths}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package quiz
*/

require_once($CFG->dirroot.'/mod/quiz/constants.php');

/// QUIZ_QTYPES INITIATION //////////////////

/**
* Array holding question type objects
*/
$QUIZ_QTYPES= array();

require_once("$CFG->dirroot/mod/quiz/questiontypes/questiontype.php");
quiz_load_questiontypes();

/**
* Loads the questiontype.php file for each question type
*
* These files in turn instantiate the corresponding question type class
* and adds it to the $QUIZ_QTYPES array
*/
function quiz_load_questiontypes() {
    global $QUIZ_QTYPES;
    global $CFG;

    $qtypenames= get_list_of_plugins('mod/quiz/questiontypes');
    foreach($qtypenames as $qtypename) {
        // Instanciates all plug-in question types
        $qtypefilepath= "$CFG->dirroot/mod/quiz/questiontypes/$qtypename/questiontype.php";

        // echo "Loading $qtypename<br/>"; // Uncomment for debugging
        if (is_readable($qtypefilepath)) {
            require_once($qtypefilepath);
        }
    }
}

/// OTHER CLASSES /////////////////////////////////////////////////////////

/**
* This holds the options that are determined by the course module
*/
class cmoptions {
    /**
    * Whether a new attempt should be based on the previous one. If true
    * then a new attempt will start in a state where all responses are set
    * to the last responses from the previous attempt.
    */
    var $attemptonlast = false;

    /**
    * Various option flags. The flags are accessed via bitwise operations
    * using the constants defined in the CONSTANTS section above.
    */
    var $optionflags = QUIZ_ADAPTIVE;

    /**
    * Determines whether in the calculation of the score for a question
    * penalties for earlier wrong responses within the same attempt will
    * be subtracted.
    */
    var $penaltyscheme = true;

    /**
    * The maximum time the user is allowed to answer the questions withing
    * an attempt. This is measured in minutes so needs to be multiplied by
    * 60 before compared to timestamps. If set to 0 no timelimit will be applied
    */
    var $timelimit = 0;

    /**
    * Timestamp for the closing time. Responses submitted after this time will
    * be saved but no credit will be given for them.
    */
    var $timeclose = 9999999999;

    /**
    * The id of the course from withing which the question is currently being used
    */
    var $course = SITEID;

    /**
    * Whether the answers in a multiple choice question should be randomly
    * shuffled when a new attempt is started.
    */
    var $shuffleanswers = false;

    /**
    * The number of decimals to be shown when scores are printed
    */
    var $decimalpoints = 2;

    /**
    * Determines when a student is allowed to review. The information is read
    * out from the bits with the help of the constants defined earlier
    * We initialise this to allow the student to see everything (all bits set)
    */
    var $review = 16777215;
}



/// FUNCTIONS //////////////////////////////////////////////////////

/**
* Updates the question objects with question type specific
* information by calling {@link get_question_options()}
*
* Can be called either with an array of question objects or with a single
* question object.
* @return bool            Indicates success or failure.
* @param mixed $questions Either an array of question objects to be updated
*                         or just a single question object
*/
function quiz_get_question_options(&$questions) {
    global $QUIZ_QTYPES;

    if (is_array($questions)) { // deal with an array of questions
        // get the keys of the input array
        $keys = array_keys($questions);
        // update each question object
        foreach ($keys as $i) {
            // set name prefix
            $questions[$i]->name_prefix = quiz_make_name_prefix($i);

            if (!$QUIZ_QTYPES[$questions[$i]->qtype]->get_question_options($questions[$i]))
                return false;
        }
        return true;
    } else { // deal with single question
        $questions->name_prefix = quiz_make_name_prefix($questions->id);
        return $QUIZ_QTYPES[$questions->qtype]->get_question_options($questions);
    }
}

/**
* Loads the most recent state of each question session from the database
* or create new one.
*
* For each question the most recent session state for the current attempt
* is loaded from the quiz_states table and the question type specific data and
* responses are added by calling {@link quiz_restore_state()} which in turn
* calls {@link restore_session_and_responses()} for each question.
* If no states exist for the question instance an empty state object is
* created representing the start of a session and empty question
* type specific information and responses are created by calling
* {@link create_session_and_responses()}.
* @todo Allow new attempt to be based on last attempt
*
* @return array           An array of state objects representing the most recent
*                         states of the question sessions.
* @param array $questions The questions for which sessions are to be restored or
*                         created.
* @param object $cmoptions
* @param object $attempt  The attempt for which the question sessions are
*                         to be restored or created.
*/
function quiz_get_states(&$questions, $cmoptions, $attempt) {
    global $CFG, $QUIZ_QTYPES;

    // get the question ids
    $ids = array_keys($questions);
    $questionlist = implode(',', $ids);

    // The question field must be listed first so that it is used as the
    // array index in the array returned by get_records_sql
    $statefields = 'n.questionid as question, s.*, n.sumpenalty';
    // Load the newest states for the questions
    $sql = "SELECT $statefields".
           "  FROM {$CFG->prefix}quiz_states s,".
           "       {$CFG->prefix}quiz_newest_states n".
           " WHERE s.id = n.newest".
           "   AND n.attemptid = '$attempt->uniqueid'".
           "   AND n.questionid IN ($questionlist)";
    $states = get_records_sql($sql);

    // Load the newest graded states for the questions
    $sql = "SELECT $statefields".
           "  FROM {$CFG->prefix}quiz_states s,".
           "       {$CFG->prefix}quiz_newest_states n".
           " WHERE s.id = n.newgraded".
           "   AND n.attemptid = '$attempt->uniqueid'".
           "   AND n.questionid IN ($questionlist)";
    $gradedstates = get_records_sql($sql);

    // loop through all questions and set the last_graded states
    foreach ($ids as $i) {
        if (isset($states[$i])) {
            quiz_restore_state($questions[$i], $states[$i]);
            if (isset($gradedstates[$i])) {
                quiz_restore_state($questions[$i], $gradedstates[$i]);
                $states[$i]->last_graded = $gradedstates[$i];
            } else {
                $states[$i]->last_graded = clone($states[$i]);
                $states[$i]->last_graded->responses = array('' => '');
            }
        } else {
            // Create a new state object
            if ($cmoptions->attemptonlast and $attempt->attempt > 1 and !$attempt->preview) {
                // build on states from last attempt
                if (!$lastattemptid = get_field('quiz_attempts', 'uniqueid', 'quiz', $attempt->quiz, 'userid', $attempt->userid, 'attempt', $attempt->attempt-1)) {
                    error('Could not find previous attempt to build on');
                }
                // Load the last graded state for the question
                $sql = "SELECT $statefields".
                       "  FROM {$CFG->prefix}quiz_states s,".
                       "       {$CFG->prefix}quiz_newest_states n".
                       " WHERE s.id = n.newgraded".
                       "   AND n.attemptid = '$lastattemptid'".
                       "   AND n.questionid = '$i'";
                if (!$states[$i] = get_record_sql($sql)) {
                    error('Could not find state for previous attempt to build on');
                }
                quiz_restore_state($questions[$i], $states[$i]);
                $states[$i]->attempt = $attempt->uniqueid;
                $states[$i]->question = (int) $i;
                $states[$i]->seq_number = 0;
                $states[$i]->timestamp = $attempt->timestart;
                $states[$i]->event = ($attempt->timefinish) ? QUIZ_EVENTCLOSE : QUIZ_EVENTOPEN;
                $states[$i]->grade = '';
                $states[$i]->raw_grade = '';
                $states[$i]->penalty = '';
                $states[$i]->sumpenalty = '0.0';
                $states[$i]->changed = true;
                $states[$i]->last_graded = clone($states[$i]);
                $states[$i]->last_graded->responses = array('' => '');

            } else {
                // create a new empty state
                $states[$i] = new object;
                $states[$i]->attempt = $attempt->uniqueid;
                $states[$i]->question = (int) $i;
                $states[$i]->seq_number = 0;
                $states[$i]->timestamp = $attempt->timestart;
                $states[$i]->event = ($attempt->timefinish) ? QUIZ_EVENTCLOSE : QUIZ_EVENTOPEN;
                $states[$i]->grade = '';
                $states[$i]->raw_grade = '';
                $states[$i]->penalty = '';
                $states[$i]->sumpenalty = '0.0';
                $states[$i]->responses = array('' => '');
                // Prevent further changes to the session from incrementing the
                // sequence number
                $states[$i]->changed = true;

                // Create the empty question type specific information
                if (!$QUIZ_QTYPES[$questions[$i]->qtype]
                 ->create_session_and_responses($questions[$i], $states[$i], $cmoptions, $attempt)) {
                    return false;
                }
                $states[$i]->last_graded = clone($states[$i]);
            }
        }
    }
    return $states;
}


/**
* Creates the run-time fields for the states
*
* Extends the state objects for a question by calling
* {@link restore_session_and_responses()}
* @return boolean         Represents success or failure
* @param object $question The question for which the state is needed
* @param object $state   The state as loaded from the database
*/
function quiz_restore_state(&$question, &$state) {
    global $QUIZ_QTYPES;

    // initialise response to the value in the answer field
    $state->responses = array('' => $state->answer);
    unset($state->answer);

    // Set the changed field to false; any code which changes the
    // question session must set this to true and must increment
    // ->seq_number. The quiz_save_question_session
    // function will save the new state object database if the field is
    // set to true.
    $state->changed = false;

    // Load the question type specific data
    return $QUIZ_QTYPES[$question->qtype]
     ->restore_session_and_responses($question, $state);

}

/**
* Saves the current state of the question session to the database
*
* The state object representing the current state of the session for the
* question is saved to the quiz_states table with ->responses[''] saved
* to the answer field of the database table. The information in the
* quiz_newest_states table is updated.
* The question type specific data is then saved.
* @return boolean         Indicates success or failure.
* @param object $question The question for which session is to be saved.
* @param object $state    The state information to be saved. In particular the
*                         most recent responses are in ->responses. The object
*                         is updated to hold the new ->id.
*/
function quiz_save_question_session(&$question, &$state) {
    global $QUIZ_QTYPES;
    // Check if the state has changed
    if (!$state->changed && isset($state->id)) {
        return true;
    }
    // Set the legacy answer field
    $state->answer = isset($state->responses['']) ? $state->responses[''] : '';

    // Round long grade
    if (strlen($state->grade) > 10 && floatval($state->grade)) {
        $state->grade = strval(round($state->grade,10-1-strlen(floor($state->grade))));
    }

    // Round long raw_grade
    if (strlen($state->raw_grade) > 10 && floatval($state->raw_grade)) {
        $state->raw_grade = strval(round($state->raw_grade,10-1-strlen(floor($state->raw_grade))));
    }

    // Save the state
    if (isset($state->update)) {
        update_record('quiz_states', $state);
    } else {
        if (!$state->id = insert_record('quiz_states', $state)) {
            unset($state->id);
            unset($state->answer);
            return false;
        }

        // this is the most recent state
        if (!record_exists('quiz_newest_states', 'attemptid',
         $state->attempt, 'questionid', $question->id)) {
            $new->attemptid = $state->attempt;
            $new->questionid = $question->id;
            $new->newest = $state->id;
            $new->sumpenalty = $state->sumpenalty;
            if (!insert_record('quiz_newest_states', $new)) {
                error('Could not insert entry in quiz_newest_states');
            }
        } else {
            set_field('quiz_newest_states', 'newest', $state->id, 'attemptid',
             $state->attempt, 'questionid', $question->id);
        }
        if (quiz_state_is_graded($state)) {
            // this is also the most recent graded state
            if ($newest = get_record('quiz_newest_states', 'attemptid',
             $state->attempt, 'questionid', $question->id)) {
                $newest->newgraded = $state->id;
                $newest->sumpenalty = $state->sumpenalty;
                update_record('quiz_newest_states', $newest);
            }
        }
    }

    unset($state->answer);

    // Save the question type specific state information and responses
    if (!$QUIZ_QTYPES[$question->qtype]->save_session_and_responses(
     $question, $state)) {
        return false;
    }
    // Reset the changed flag
    $state->changed = false;
    return true;
}

/**
* Determines whether a state has been graded by looking at the event field
*
* @return boolean         true if the state has been graded
* @param object $state
*/
function quiz_state_is_graded($state) {
    return ($state->event == QUIZ_EVENTGRADE or $state->event == QUIZ_EVENTCLOSE);
}

/**
* Updates a state object for the next new state to record the fact that the
* question session has changed
*
* If the question session is not already marked as having changed (via the
* ->changed field of the state object), then this is done, the sequence
* number in ->seq_number is incremented and the timestamp in ->timestamp is
* updated. This should be called before or after any code which changes the
* question session.
* @param object $state The state object representing the state of the session.
*/
function quiz_mark_session_change(&$state) {
   if (!$state->changed) {
       $state->changed = true;
       $state->seq_number++;
       $state->timestamp = time();
   }
}


/**
* Extracts responses from submitted form
*
* TODO: Finish documenting this
* @return array            array of action objects, indexed by question ids.
* @param array $questions  an array containing at least all questions that are used on the form
* @param array $responses
* @param integer $defaultevent
*/
function quiz_extract_responses($questions, $responses, $defaultevent) {

    $actions = array();
    foreach ($responses as $key => $response) {
        // Get the question id from the response name
        if (false !== ($quid = quiz_get_id_from_name_prefix($key))) {
            // check if this is a valid id
            if (!isset($questions[$quid])) {
                error('Form contained question that is not in questionids');
            }

            // Remove the name prefix from the name
            //decrypt trying
            $key = rc4decrypt(substr($key, strlen($questions[$quid]->name_prefix)));
            if (false === $key) {
                $key = '';
            }
            // Check for question validate and mark buttons & set events
            /// added encryption
            if (rc4encrypt($key) === 'validate') {
                $actions[$quid]->event = QUIZ_EVENTVALIDATE;
                $key = 'validate';
            } else if (rc4encrypt($key) === 'mark') {
                $actions[$quid]->event = QUIZ_EVENTGRADE;
                $key = 'mark';
            } else {
                $actions[$quid]->event = $defaultevent;
            }

            // Update the state with the new response
            $actions[$quid]->responses[$key] = $response;
        }
    }
    return $actions;
}



/**
* For a given question in an attempt we walk the complete history of states
* and recalculate the grades as we go along.
*
* This is used when a question is changed and old student
* responses need to be marked with the new version of a question.
*
* TODO: Finish documenting this
* @return boolean            Indicates success/failure
* @param object  $question   A question object
* @param object  $attempt    The attempt, in which the question needs to be regraded.
* @param object  $cmoptions
* @param boolean $verbose    Optional. Whether to print progress information or not.
*/
function quiz_regrade_question_in_attempt($question, $attempt, $cmoptions, $verbose=false) {

    if ($states = get_records_select('quiz_states',
     "attempt = '{$attempt->uniqueid}' AND question = '{$question->id}'", 'seq_number ASC')) {
        $states = array_values($states);

        $attempt->sumgrades -= $states[count($states)-1]->grade;

        // Initialise the replaystate
        $state = clone($states[0]);
        quiz_restore_state($question, $state);
        $state->sumpenalty = 0.0;
        $state->raw_grade = 0;
        $state->grade = 0;
        $state->responses = array(''=>'');
        $state->event = QUIZ_EVENTOPEN;
        $replaystate = clone($state);
        $replaystate->last_graded = $state;

        $changed = 0;
        for($j = 0; $j < count($states); $j++) {
            quiz_restore_state($question, $states[$j]);
            $action = new stdClass;
            $action->responses = $states[$j]->responses;
            $action->timestamp = $states[$j]->timestamp;

            // Close the last state of a finished attempt
            if (((count($states) - 1) === $j) && ($attempt->timefinish > 0)) {
                $action->event = QUIZ_EVENTCLOSE;

            // Grade instead of closing, quiz_process_responses will then
            // work out whether to close it
            } else if (QUIZ_EVENTCLOSE == $states[$j]->event) {
                $action->event = QUIZ_EVENTGRADE;

            // By default take the event that was saved in the database
            } else {
                $action->event = $states[$j]->event;
            }
            // Reprocess (regrade) responses
            if (!quiz_process_responses($question, $replaystate, $action, $cmoptions,
             $attempt)) {
                $verbose && notify("Couldn't regrade state #{$state->id}!");
            }
            if ((float)$replaystate->raw_grade != (float)$states[$j]->raw_grade) {
                $changed++;

            }
            $replaystate->id = $states[$j]->id;
            $replaystate->update = true;
            quiz_save_question_session($question, $replaystate);
        }
        if ($verbose) {
            if ($changed) {
                link_to_popup_window ('/mod/quiz/reviewquestion.php?attempt='.$attempt->id.'&amp;question='.$question->id,
                 'reviewquestion', ' #'.$attempt->id, 450, 550, get_string('reviewresponse', 'quiz'));
                update_record('quiz_attempts', $attempt);
            } else {
                echo ' #'.$attempt->id;
            }
            echo "\n"; flush(); ob_flush();
        }

        return true;
    }
    return true;
}

/**
* Processes an array of student responses, grading and saving them as appropriate
*
* @return boolean         Indicates success/failure
* @param object $question Full question object, passed by reference
* @param object $state    Full state object, passed by reference
* @param object $action   object with the fields ->responses which
*                         is an array holding the student responses,
*                         ->action which specifies the action, e.g., QUIZ_EVENTGRADE,
*                         and ->timestamp which is a timestamp from when the responses
*                         were submitted by the student.
* @param object $cmoptions
* @param object $attempt  The attempt is passed by reference so that
*                         during grading its ->sumgrades field can be updated
*/
function quiz_process_responses(&$question, &$state, $action, $cmoptions, &$attempt) {
    global $QUIZ_QTYPES;

    // if no responses are set initialise to empty response
    if (!isset($action->responses)) {
        $action->responses = array('' => '');
    }

    // make sure these are gone!
    unset($action->responses['mark'], $action->responses['validate']);

    // Check the question session is still open
    if (QUIZ_EVENTCLOSE == $state->event) {
        return true;
    }
    // If $action->event is not set that implies saving
    if (! isset($action->event)) {
        $action->event = QUIZ_EVENTSAVE;
    }
    // Check if we are grading the question; compare against last graded
    // responses, not last given responses in this case
    if (quiz_isgradingevent($action->event)) {
        $state->responses = $state->last_graded->responses;
    }
    // Check for unchanged responses (exactly unchanged, not equivalent).
    // We also have to catch questions that the student has not yet attempted
    $sameresponses = (($state->responses == $action->responses) or
     ($state->responses == array(''=>'') && array_keys(array_count_values($action->responses))===array('')));

    if ($sameresponses and QUIZ_EVENTCLOSE != $action->event
     and QUIZ_EVENTVALIDATE != $action->event) {
        return true;
    }

    // Roll back grading information to last graded state and set the new
    // responses
    $newstate = clone($state->last_graded);
    $newstate->responses = $action->responses;
    $newstate->seq_number = $state->seq_number + 1;
    $newstate->changed = true; // will assure that it gets saved to the database
    $newstate->last_graded = $state->last_graded;
    $newstate->timestamp = $action->timestamp;
    $state = $newstate;

    // Set the event to the action we will perform. The question type specific
    // grading code may override this by setting it to QUIZ_EVENTCLOSE if the
    // attempt at the question causes the session to close
    $state->event = $action->event;

    if (!quiz_isgradingevent($action->event)) {
        // Grade the response but don't update the overall grade
        $QUIZ_QTYPES[$question->qtype]->grade_responses(
         $question, $state, $cmoptions);
        // Force the event to save or validate (even if the grading caused the
        // state to close)
        $state->event = $action->event;

    } else if (QUIZ_EVENTGRADE == $action->event) {

        // Work out if the current responses (or equivalent responses) were
        // already given in
        // a. the last graded attempt
        // b. any other graded attempt
        if($QUIZ_QTYPES[$question->qtype]->compare_responses(
         $question, $state, $state->last_graded)) {
            $state->event = QUIZ_EVENTDUPLICATEGRADE;
        } else {
            if ($cmoptions->optionflags & QUIZ_IGNORE_DUPRESP) {
                /* Walk back through the previous graded states looking for
                one where the responses are equivalent to the current
                responses. If such a state is found, set the current grading
                details to those of that state and set the event to
                QUIZ_EVENTDUPLICATEGRADE */
                quiz_search_for_duplicate_responses($question, $state);
            }
            // If we did not find a duplicate, perform grading
            if (QUIZ_EVENTDUPLICATEGRADE != $state->event) {
                // Decrease sumgrades by previous grade and then later add new grade
                $attempt->sumgrades -= (float)$state->last_graded->grade;

                $QUIZ_QTYPES[$question->qtype]->grade_responses(
                 $question, $state, $cmoptions);
                // Calculate overall grade using correct penalty method
                quiz_apply_penalty_and_timelimit($question, $state, $attempt, $cmoptions);
                // Update the last graded state (don't simplify!)
                unset($state->last_graded);
                $state->last_graded = clone($state);
                unset($state->last_graded->changed);

                $attempt->sumgrades += (float)$state->last_graded->grade;
            }
        }
    } else if (QUIZ_EVENTCLOSE == $action->event) {
        // decrease sumgrades by previous grade and then later add new grade
        $attempt->sumgrades -= (float)$state->last_graded->grade;

        // Only mark if they haven't been marked already
        if (!$sameresponses) {
            $QUIZ_QTYPES[$question->qtype]->grade_responses(
             $question, $state, $cmoptions);
            // Calculate overall grade using correct penalty method
            quiz_apply_penalty_and_timelimit($question, $state, $attempt, $cmoptions);
        }
        // Force the state to close (as the attempt is closing)
        $state->event = QUIZ_EVENTCLOSE;
        // If there is no valid grade, set it to zero
        if ('' === $state->grade) {
            $state->raw_grade = 0;
            $state->penalty = 0;
            $state->grade = 0;
        }
        // Update the last graded state (don't simplify!)
        unset($state->last_graded);
        $state->last_graded = clone($state);
        unset($state->last_graded->changed);

        $attempt->sumgrades += (float)$state->last_graded->grade;
    }
    $attempt->timemodified = $action->timestamp;
    // Round long sumgrades
    if (strlen($attempt->sumgrades) > 10 && floatval($attempt->sumgrades)) {
        $attempt->sumgrades = strval(round($attempt->sumgrades,10-1-strlen(floor($state->sumgrades))));
    }

    return true;
}

/**
* Determine if event requires grading
*/
function quiz_isgradingevent($event) {
    return (QUIZ_EVENTGRADE == $event || QUIZ_EVENTCLOSE == $event);
}

/**
* Compare current responses to all previous graded responses
*
* This is used by {@link quiz_process_responses()} to determine whether
* to ignore the marking request for the current response. However this
* check against all previous graded responses is only performed if
* the QUIZ_IGNORE_DUPRESP bit in $cmoptions->optionflags is set
* @return boolean         Indicates if a state with duplicate responses was
*                         found.
* @param object $question
* @param object $state
*/
function quiz_search_for_duplicate_responses(&$question, &$state) {
    // get all previously graded question states
    global $QUIZ_QTYPES;
    if (!$oldstates = get_records('quiz_question_states', "event = '" .
     QUIZ_EVENTGRADE . "' AND " . "question = '" . $question->id .
     "'", 'seq_number DESC')) {
        return false;
    }
    foreach ($oldstates as $oldstate) {
        if ($QUIZ_QTYPES[$question->qtype]->restore_session_and_responses(
         $question, $oldstate)) {
            if(!$QUIZ_QTYPES[$question->qtype]->compare_responses(
             $question, $state, $oldstate)) {
                $state->event = QUIZ_EVENTDUPLICATEGRADE;
                break;
            }
        }
    }
    return (QUIZ_EVENTDUPLICATEGRADE == $state->event);
}

/**
* Applies the penalty from the previous graded responses to the raw grade
* for the current responses
*
* The grade for the question in the current state is computed by subtracting the
* penalty accumulated over the previous graded responses at the question from the
* raw grade. If the timestamp is more than 1 minute beyond the end of the attempt
* the grade is set to zero. The ->grade field of the state object is modified to
* reflect the new grade but is never allowed to decrease.
* @param object $question The question for which the penalty is to be applied.
* @param object $state    The state for which the grade is to be set from the
*                         raw grade and the cumulative penalty from the last
*                         graded state. The ->grade field is updated by applying
*                         the penalty scheme determined in $cmoptions to the ->raw_grade and
*                         ->last_graded->penalty fields.
* @param object $cmoptions  The options set by the course module.
*                           The ->penaltyscheme field determines whether penalties
*                           for incorrect earlier responses are subtracted.
*/
function quiz_apply_penalty_and_timelimit(&$question, &$state, $attempt, $cmoptions) {
    // deal with penaly
    if ($cmoptions->penaltyscheme) {
            $state->grade = $state->raw_grade - $state->sumpenalty;
            $state->sumpenalty += (float) $state->penalty;
    } else {
        $state->grade = $state->raw_grade;
    }

    // deal with timeimit
    if ($cmoptions->timelimit) {
        // We allow for 5% uncertainty in the following test
        if (($state->timestamp - $attempt->timestart) > ($cmoptions->timelimit * 63)) {
            $state->grade = 0;
        }
    }

    // deal with closing time
    if ($cmoptions->timeclose and $state->timestamp > ($cmoptions->timeclose + 60)) { // allowing 1 minute lateness
        $state->grade = 0;
    }

    // Ensure that the grade does not go down
    $state->grade = max($state->grade, $state->last_graded->grade);
}


function quiz_print_comment($text) {
    echo "<span class=\"feedbacktext\">&nbsp;".format_text($text, true, false)."</span>";
}

function quiz_print_correctanswer($text) {
    echo "<p align=\"right\"><span class=\"highlight\">$text</span></p>";
}

/**
* Print the icon for the question type
*
* @param object $question  The question object for which the icon is required
* @param boolean $editlink If true then the icon is a link to the question
*                          edit page.
* @param boolean $return   If true the functions returns the link as a string
*/
function quiz_print_question_icon($question, $editlink=true, $return = false) {
// returns a question icon

    global $QUIZ_QUESTION_TYPE;
    global $QUIZ_QTYPES;

    $html = '<img border="0" height="16" width="16" src="questiontypes/'.
            $QUIZ_QTYPES[$question->qtype]->name().'/icon.gif" alt="'.
            get_string($QUIZ_QTYPES[$question->qtype]->name(), 'quiz').'" />';

    if ($editlink) {
        $html =  "<a href=\"question.php?id=$question->id\" title=\""
                .$QUIZ_QTYPES[$question->qtype]->name()."\">".
                $html."</a>\n";
    }
    if ($return) {
        return $html;
    } else {
        echo $html;
    }
}


/**
* Print the question image if there is one
*
* @param object $question The question object
*/
function quiz_print_possible_question_image($question) {

    global $CFG;

    if ($question->image) {
        echo '<img border="0" src="';

        if (substr(strtolower($question->image), 0, 7) == 'http://') {
            echo $question->image;

        } else if ($CFG->slasharguments) {        // Use this method if possible for better caching
            echo "$CFG->wwwroot/file.php/$question->image";

        } else {
            echo "$CFG->wwwroot/file.php?file=$question->image";
        }
        echo '" alt="" />';

    }
}


/**
* Construct name prefixes for question form element names
*
* Construct the name prefix that should be used for example in the
* names of form elements created by questions.
* This is called by {@link quiz_get_question_options()}
* to set $question->name_prefix.
* This name prefix includes the question id which can be
* extracted from it with {@link quiz_get_id_from_name_prefix()}.
*
* @return string
* @param integer $id  The question id
*/
function quiz_make_name_prefix($id) {
    return 'resp' . $id . '_';
}

/**
* Extract question id from the prefix of form element names
*
* @return integer      The question id
* @param string $name  The name that contains a prefix that was
*                      constructed with {@link quiz_make_name_prefix()}
*/
function quiz_get_id_from_name_prefix($name) {
    if (!preg_match('/^resp([0-9]+)_/', $name, $matches))
        return false;
    return (integer) $matches[1];
}

function quiz_new_attempt_uniqueid() {
    global $CFG;
    set_config('attemptuniqueid', $CFG->attemptuniqueid + 1);
    return $CFG->attemptuniqueid;
}

/**
* Determine render options
*/
function quiz_get_renderoptions($cmoptions, $state) {
    // Show the question in readonly (review) mode if the question is in
    // the closed state
    $options->readonly = QUIZ_EVENTCLOSE === $state->event;

    // Show feedback once the question has been graded (if allowed by the quiz)
    $options->feedback = ('' !== $state->grade) && ($cmoptions->review & QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_IMMEDIATELY);

    // Show validation only after a validation event
    $options->validation = QUIZ_EVENTVALIDATE === $state->event;

    // Show correct responses in readonly mode if the quiz allows it
    $options->correct_responses = $options->readonly && ($cmoptions->review & QUIZ_REVIEW_ANSWERS & QUIZ_REVIEW_IMMEDIATELY);

    // Always show responses and scores
    $options->responses = true;
    $options->scores = true;

    return $options;
}


/**
* Determine review options
*/
function quiz_get_reviewoptions($cmoptions, $attempt, $isteacher=false) {
    $options->readonly = true;
    if ($isteacher and !$attempt->preview) {
        // The teacher should be shown everything except during preview when the teachers
        // wants to see just what the students see
        $options->responses = true;
        $options->scores = true;
        $options->feedback = true;
        $options->correct_responses = true;
        $options->solutions = false;
        return $options;
    }
    if ((time() - $attempt->timefinish) < 120) {
        $options->responses = ($cmoptions->review & QUIZ_REVIEW_IMMEDIATELY & QUIZ_REVIEW_RESPONSES) ? 1 : 0;
        $options->scores = ($cmoptions->review & QUIZ_REVIEW_IMMEDIATELY & QUIZ_REVIEW_SCORES) ? 1 : 0;
        $options->feedback = ($cmoptions->review & QUIZ_REVIEW_IMMEDIATELY & QUIZ_REVIEW_FEEDBACK) ? 1 : 0;
        $options->correct_responses = ($cmoptions->review & QUIZ_REVIEW_IMMEDIATELY & QUIZ_REVIEW_ANSWERS) ? 1 : 0;
        $options->solutions = ($cmoptions->review & QUIZ_REVIEW_IMMEDIATELY & QUIZ_REVIEW_SOLUTIONS) ? 1 : 0;
    } else if (!$cmoptions->timeclose or time() < $cmoptions->timeclose) {
        $options->responses = ($cmoptions->review & QUIZ_REVIEW_OPEN & QUIZ_REVIEW_RESPONSES) ? 1 : 0;
        $options->scores = ($cmoptions->review & QUIZ_REVIEW_OPEN & QUIZ_REVIEW_SCORES) ? 1 : 0;
        $options->feedback = ($cmoptions->review & QUIZ_REVIEW_OPEN & QUIZ_REVIEW_FEEDBACK) ? 1 : 0;
        $options->correct_responses = ($cmoptions->review & QUIZ_REVIEW_OPEN & QUIZ_REVIEW_ANSWERS) ? 1 : 0;
        $options->solutions = ($cmoptions->review & QUIZ_REVIEW_OPEN & QUIZ_REVIEW_SOLUTIONS) ? 1 : 0;
    } else {
        $options->responses = ($cmoptions->review & QUIZ_REVIEW_CLOSED & QUIZ_REVIEW_RESPONSES) ? 1 : 0;
        $options->scores = ($cmoptions->review & QUIZ_REVIEW_CLOSED & QUIZ_REVIEW_SCORES) ? 1 : 0;
        $options->feedback = ($cmoptions->review & QUIZ_REVIEW_CLOSED & QUIZ_REVIEW_FEEDBACK) ? 1 : 0;
        $options->correct_responses = ($cmoptions->review & QUIZ_REVIEW_CLOSED & QUIZ_REVIEW_ANSWERS) ? 1 : 0;
        $options->solutions = ($cmoptions->review & QUIZ_REVIEW_CLOSED & QUIZ_REVIEW_SOLUTIONS) ? 1 : 0;
    }
    return $options;
}

/// FUNCTIONS THAT ARE USED BY SOME QUESTIONTYPES ///////////////////

function quiz_extract_correctanswers($answers, $nameprefix) {
/// Convenience function that is used by some single-response
/// question-types for determining correct answers.

    $bestanswerfraction = 0.0;
    $correctanswers = array();
    foreach ($answers as $answer) {
        if ($answer->fraction > $bestanswerfraction) {
            $correctanswers = array($nameprefix.$answer->id => $answer);
            $bestanswerfraction = $answer->fraction;
        } else if ($answer->fraction == $bestanswerfraction) {
            $correctanswers[$nameprefix.$answer->id] = $answer;
        }
    }
    return $correctanswers;
}

/// FUNCTIONS THAT SIMPLY WRAP QUESTIONTYPE METHODS //////////////////////////////////

/**
* Prints a question
*
* Simply calls the question type specific print_question() method.
*/
function quiz_print_quiz_question(&$question, &$state, $number, $cmoptions, $options=null) {
    global $QUIZ_QTYPES;

    $QUIZ_QTYPES[$question->qtype]->print_question($question, $state, $number,
     $cmoptions, $options);
}

/**
* Gets all teacher stored answers for a given question
*
* Simply calls the question type specific get_all_responses() method.
*/
// ULPGC ecastro
function quiz_get_question_responses($question, $state) {
    global $QUIZ_QTYPES;
    $r = $QUIZ_QTYPES[$question->qtype]->get_all_responses($question, $state);
    return $r;
}


/**
* Gets the response given by the user in a particular attempt
*
* Simply calls the question type specific get_actual_response() method.
*/
// ULPGC ecastro
function quiz_get_question_actual_response($question, $state) {
    global $QUIZ_QTYPES;

    $r = $QUIZ_QTYPES[$question->qtype]->get_actual_response($question, $state);
    return $r;
}

/**
* Gets the response given by the user in a particular attempt
*
* Simply calls the question type specific get_actual_response() method.
*/
// ULPGc ecastro
function quiz_get_question_fraction_grade($question, $state) {
    global $QUIZ_QTYPES;

    $r = $QUIZ_QTYPES[$question->qtype]->get_fractional_grade($question, $state);
    return $r;
}


/// CATEGORY FUNCTIONS /////////////////////////////////////////////////////////////////

/**
* Gets the default category in a course
*
* It returns the first category with no parent category. If no categories
* exist yet then one is created.
* @return object The default category
* @param integer $courseid  The id of the course whose default category is wanted
*/
function quiz_get_default_category($courseid) {
/// Returns the current category

    if ($categories = get_records_select("quiz_categories", "course = '$courseid' AND parent = '0'", "id")) {
        foreach ($categories as $category) {
            return $category;   // Return the first one (lowest id)
        }
    }

    // Otherwise, we need to make one
    $category->name = get_string("default", "quiz");
    $category->info = get_string("defaultinfo", "quiz");
    $category->course = $courseid;
    $category->parent = 0;
    $category->sortorder = QUIZ_CATEGORIES_SORTORDER;
    $category->publish = 0;
    $category->stamp = make_unique_id_code();

    if (!$category->id = insert_record("quiz_categories", $category)) {
        notify("Error creating a default category!");
        return false;
    }
    return $category;
}

function quiz_get_category_menu($courseid, $published=false) {
/// Returns the list of categories
    $publish = "";
    if ($published) {
        $publish = "OR publish = '1'";
    }

    if (!isadmin()) {
        $categories = get_records_select("quiz_categories", "course = '$courseid' $publish", 'parent, sortorder, name ASC');
    } else {
        $categories = get_records_select("quiz_categories", '', 'parent, sortorder, name ASC');
    }
    if (!$categories) {
        return false;
    }
    $categories = add_indented_names($categories);

    foreach ($categories as $category) {
       if ($catcourse = get_record("course", "id", $category->course)) {
           if ($category->publish && ($category->course != $courseid)) {
               $category->indentedname .= " ($catcourse->shortname)";
           }
           $catmenu[$category->id] = $category->indentedname;
       }
    }
    return $catmenu;
}

function sort_categories_by_tree(&$categories, $id = 0, $level = 1) {
// returns the categories with their names ordered following parent-child relationships
// finally it tries to return pending categories (those being orphaned, whose parent is
// incorrect) to avoid missing any category from original array.
    $children = array();
    $keys = array_keys($categories);

    foreach ($keys as $key) {
        if (!isset($categories[$key]->processed) && $categories[$key]->parent == $id) {
            $children[$key] = $categories[$key];
            $categories[$key]->processed = true;
            $children = $children + sort_categories_by_tree($categories, $children[$key]->id, $level+1);
        }
    }
    //If level = 1, we have finished, try to look for non processed categories (bad parent) and sort them too
    if ($level == 1) {
        foreach ($keys as $key) {
            //If not processed and it's a good candidate to start (because its parent doesn't exist in the course)
            if (!isset($categories[$key]->processed) && !record_exists('quiz_categories', 'course', $categories[$key]->course, 'id', $categories[$key]->parent)) {
                $children[$key] = $categories[$key];
                $categories[$key]->processed = true;
                $children = $children + sort_categories_by_tree($categories, $children[$key]->id, $level+1);
            }
        }
    }
    return $children;
}

function add_indented_names(&$categories, $id = 0, $indent = 0) {
// returns the categories with their names indented to show parent-child relationships
    $fillstr = '&nbsp;&nbsp;&nbsp;';
    $fill = str_repeat($fillstr, $indent);
    $children = array();
    $keys = array_keys($categories);

    foreach ($keys as $key) {
        if (!isset($categories[$key]->processed) && $categories[$key]->parent == $id) {
            $children[$key] = $categories[$key];
            $children[$key]->indentedname = $fill . $children[$key]->name;
            $categories[$key]->processed = true;
            $children = $children + add_indented_names($categories, $children[$key]->id, $indent + 1);
        }
    }
    return $children;
}

/**
* Displays a select menu of categories with appended course names
*
* Optionaly non editable categories may be excluded.
* @author Howard Miller June '04
*/
function quiz_category_select_menu($courseid,$published=false,$only_editable=false,$selected="") {

    // get sql fragment for published
    $publishsql="";
    if ($published) {
        $publishsql = "or publish=1";
    }

    $categories = get_records_select("quiz_categories","course=$courseid $publishsql", 'parent, sortorder, name ASC');

    $categories = add_indented_names($categories);

    echo "<select name=\"category\">\n";
    foreach ($categories as $category) {
        $cid = $category->id;
        $cname = quiz_get_category_coursename($category, $courseid);
        $seltxt = "";
        if ($cid==$selected) {
            $seltxt = "selected=\"selected\"";
        }
        if ((!$only_editable) || isteacheredit($category->course)) {
            echo "    <option value=\"$cid\" $seltxt>$cname</option>\n";
        }
    }
    echo "</select>\n";
}

function quiz_get_category_coursename($category, $courseid = 0) {
/// if the category is not from this course and is published , adds on the course
/// name
    $cname = (isset($category->indentedname)) ? $category->indentedname : $category->name;
    if ($category->course != $courseid && $category->publish) {
        if ($catcourse=get_record("course","id",$category->course)) {
            $cname .= " ($catcourse->shortname) ";
        }
    }
    return $cname;
}


/**
* Returns a comma separated list of ids of the category and all subcategories
*/
function quiz_categorylist($categoryid) {
    // returns a comma separated list of ids of the category and all subcategories
    $categorylist = $categoryid;
    if ($subcategories = get_records('quiz_categories', 'parent', $categoryid, 'sortorder ASC', 'id, id')) {
        foreach ($subcategories as $subcategory) {
            $categorylist .= ','. quiz_categorylist($subcategory->id);
        }
    }
    return $categorylist;
}


/**
* Function to read all questions for category into big array
*
* @param int $category category number
* @param bool @noparent if true only questions with NO parent will be selected
* @author added by Howard Miller June 2004
*/
function get_questions_category( $category, $noparent=false ) {

    global $QUIZ_QTYPES;

    // questions will be added to an array
    $qresults = array();

    // build sql bit for $noparent
    $npsql = '';
    if ($noparent) {
      $npsql = " and parent='0' ";
    }

    // get the list of questions for the category
    if ($questions = get_records_select("quiz_questions","category={$category->id} $npsql", "qtype, name ASC")) {

        // iterate through questions, getting stuff we need
        foreach($questions as $question) {
            $questiontype = $QUIZ_QTYPES[$question->qtype];
            $questiontype->get_question_options( $question );
            $qresults[] = $question;
        }
    }

    return $qresults;
}

?>
