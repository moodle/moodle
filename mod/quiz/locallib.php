<?php  // $Id$
/**
* Library of functions used by the quiz module.
*
* This contains functions that are called from within the quiz module only
* Functions that are also called by core Moodle are in {@link lib.php}
* @version $Id$
* @author Martin Dougiamas and many others. This has recently been completely
*         rewritten by Alex Smith, Julian Sedding and Gustav Delius as part of
*         the Serving Mathematics project
*         {@link http://maths.york.ac.uk/serving_maths}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package quiz
*/

/**
* Include those library functions that are also used by core Moodle
*/
require_once("$CFG->dirroot/mod/quiz/lib.php");

/// CONSTANTS ///////////////////////////////////////////////////////////////////

/**#@+
* Options determining how the grades from individual attempts are combined to give
* the overall grade for a user
*/
define("QUIZ_GRADEHIGHEST", "1");
define("QUIZ_GRADEAVERAGE", "2");
define("QUIZ_ATTEMPTFIRST", "3");
define("QUIZ_ATTEMPTLAST",  "4");
$QUIZ_GRADE_METHOD = array ( QUIZ_GRADEHIGHEST => get_string("gradehighest", "quiz"),
                             QUIZ_GRADEAVERAGE => get_string("gradeaverage", "quiz"),
                             QUIZ_ATTEMPTFIRST => get_string("attemptfirst", "quiz"),
                             QUIZ_ATTEMPTLAST  => get_string("attemptlast", "quiz"));
/**#@-*/

/**#@+
* The different types of events that can create question states
*/
define('QUIZ_EVENTOPEN', '0');
define('QUIZ_EVENTNAVIGATE', '1');
define('QUIZ_EVENTSAVE', '2');
define('QUIZ_EVENTGRADE', '3');
define('QUIZ_EVENTDUPLICATEGRADE', '4');
define('QUIZ_EVENTVALIDATE', '5');
define('QUIZ_EVENTCLOSE', '6');
/**#@-*/

/**#@+
* The defined question types
*
* @todo It would be nicer to have a fully automatic plug-in system
*/
define("SHORTANSWER",   "1");
define("TRUEFALSE",     "2");
define("MULTICHOICE",   "3");
define("RANDOM",        "4");
define("MATCH",         "5");
define("RANDOMSAMATCH", "6");
define("DESCRIPTION",   "7");
define("NUMERICAL",     "8");
define("MULTIANSWER",   "9");
define("CALCULATED",   "10");
define("RQP",          "11");
/**#@-*/


define("QUIZ_PICTURE_MAX_HEIGHT", "600");   // Not currently implemented
define("QUIZ_PICTURE_MAX_WIDTH",  "600");   // Not currently implemented

define("QUIZ_MAX_NUMBER_ANSWERS", "10");

define("QUIZ_CATEGORIES_SORTORDER", "999");

/**
* Array holding question type objects
*/
$QUIZ_QTYPES= array();


/// Question type class //////////////////////////////////////////////

class quiz_default_questiontype {

    /**
    * Name of the question type
    *
    * The name returned should coincide with the name of the directory
    * in which this questiontype is located
    * @ return string
    */
    function name() {
        return 'default';
    }

    /**
    * Checks whether a given file is used by a particular question
    *
    * This is used by {@see quizfile.php} to determine whether a file
    * should be served to the user
    * @return boolean
    * @param question $question
    * @param string $relativefilepath
    */
    function uses_quizfile($question, $relativefilepath) {
        // The default does only check whether the file is used as image:
        return $question->image == $relativefilepath;
    }

    /**
    * Saves or updates a question after editing by a teacher
    *
    * Given some question info and some data about the answers
    * this function parses, organises and saves the question
    * It is used by {@link question.php} when saving new data from
    * a form, and also by {@link import.php} when importing questions
    * This function in turn calls {@link save_question_options}
    * to save question-type specific options
    * @return object A {@link question} object
    * @param object $question   The question object which should be updated
    * @param object $form       The form submitted by the teacher
    * @param object $course     The course we are in
    */
    function save_question($question, $form, $course) {
        // This default implementation is suitable for most
        // question types.

        // First, save the basic question itself

        $question->name               = trim($form->name);
        $question->questiontext       = trim($form->questiontext);
        $question->questiontextformat = $form->questiontextformat;
        $question->parent             = isset($form->parent)? $form->parent : 0;
        $question->length = $this->actual_number_of_questions($question);
        $question->penalty = isset($form->penalty) ? $form->penalty : 0;

        if (empty($form->image)) {
            $question->image = "";
        } else {
            $question->image = $form->image;
        }

        if (empty($question->name)) {
            $question->name = strip_tags($question->questiontext);
            if (empty($question->name)) {
                $question->name = '-';
            }
        }

        if ($question->penalty > 1 or $question->penalty < 0) {
            $question->errors['penalty'] = get_string('invalidpenalty', 'quiz');
        }

        if (isset($form->defaultgrade)) {
            $question->defaultgrade = $form->defaultgrade;
        }

        if (!empty($question->id)) { // Question already exists
            $question->version ++;    // Update version number of question
            if (!update_record("quiz_questions", $question)) {
                error("Could not update question!");
            }
        } else {         // Question is a new one
            $question->stamp = make_unique_id_code();  // Set the unique code (not to be changed)
            $question->version = 1;
            if (!$question->id = insert_record("quiz_questions", $question)) {
                error("Could not insert new question!");
            }
        }

        // Now to save all the answers and type-specific options

        $form->id       = $question->id;
        $form->qtype    = $question->qtype;
        $form->category = $question->category;

        $result = $this->save_question_options($form);

        if (!empty($result->error)) {
            error($result->error);
        }

        if (!empty($result->notice)) {
            notice($result->notice, "question.php?id=$question->id");
        }

        if (!empty($result->noticeyesno)) {
            notice_yesno($result->noticeyesno, "question.php?id=$question->id", "edit.php");
            print_footer($course);
            exit;
        }

        return $question;
    }

    /**
    * Saves question-type specific options
    *
    * This is called by {@link save_question()} to save the question-type specific data
    * @return object $result->error or $result->noticeyesno or $result->notice
    * @param object $question  This holds the information from the editing form,
    *                          it is not a standard question object.
    */
    function save_question_options($question) {
        /// This default implementation must be overridden:

        $result->error = "Unsupported question type ($question->qtype)!";
        return $result;
    }

    /**
    * Changes all states for the given attempts over to a new question
    *
    * This is used by the versioning code if the teacher requests that a question
    * gets replaced by the new version. In order for the attempts to be regraded
    * properly all data in the states referring to the old question need to be
    * changed to refer to the new version instead. In particular for question types
    * that use the answers table the answers belonging to the old question have to
    * be changed to those belonging to the new version.
    *
    * @param integer $oldquestionid  The id of the old question
    * @param object $newquestion    The new question
    * @param array  $attempts       An array of all attempt objects in whose states
    *                               replacement should take place
    */
    function replace_question_in_attempts($oldquestionid, $newquestion, $attemtps) {
        echo 'Not yet implemented';
        return;
    }

    /**
    * Loads the question type specific options for the question.
    *
    * This function loads any question type specific options for the
    * question from the database into the question object. This information
    * is placed in the $question->options field. A question type is
    * free, however, to decide on a internal structure of the options field.
    * @return bool            Indicates success or failure.
    * @param object $question The question object for the question. This object
    *                         should be updated to include the question type
    *                         specific information (it is passed by reference).
    */
    function get_question_options(&$question) {
        if (!isset($question->options)) {
            $question->options = new object;
        }
        // The default implementation attaches all answers for this question
        if (!$question->options->answers = get_records('quiz_answers', 'question',
         $question->id)) {
           //notify('Error: Missing question answers!');
           return false;
        }
        return true;
    }

    /**
    * Returns the number of question numbers which are used by the question
    *
    * This function returns the number of question numbers to be assigned
    * to the question. Most question types will have length one; they will be
    * assigned one number. The DESCRIPTION type, however does not use up a
    * number and so has a length of zero. Other question types may wish to
    * handle a bundle of questions and hence return a number greater than one.
    * @return integer         The number of question numbers which should be
    *                         assigned to the question.
    * @param object $question The question whose length is to be determined.
    *                         Question type specific information is included.
    */
    function actual_number_of_questions($question) {
        // By default, each question is given one number
        return 1;
    }

    /**
    * Creates empty session and response information for the question
    *
    * This function is called to start a question session. Empty question type
    * specific session data (if any) and empty response data will be added to the
    * state object. Session data is any data which must persist throughout the
    * quiz attempt possibly with updates as the user interacts with the
    * question. This function does NOT create new entries in the database for
    * the session; a call to the {@link save_session_and_responses} member will
    * occur to do this.
    * @return bool            Indicates success or failure.
    * @param object $question The question for which the session is to be
    *                         created. Question type specific information is
    *                         included.
    * @param object $state    The state to create the session for. Note that
    *                         this will not have been saved in the database so
    *                         there will be no id. This object will be updated
    *                         to include the question type specific information
    *                         (it is passed by reference). In particular, empty
    *                         responses will be created in the ->responses
    *                         field.
    * @param object $quiz     The quiz for which the session is to be started.
    *                         Questions may wish to initialize the session in
    *                         different ways depending on quiz settings.
    * @param object $attempt  The quiz attempt for which the session is to be
    *                         started. Questions may wish to initialize the
    *                         session in different ways depending on the user id
    *                         or time available for the attempt.
    */
    function create_session_and_responses(&$question, &$state, $quiz, $attempt) {
        // The default implementation should work for the legacy question types.
        // Most question types with only a single form field for the student's response
        // will use the empty string '' as the index for that one response. This will
        // automatically be stored in and restored from the answer field in the
        // quiz_states table.
        $state->responses = array('' => '');
        return true;
    }

    /**
    * Restores the session data and most recent responses for the given state
    *
    * This function loads any session data associated with the question
    * session in the given state from the database into the state object.
    * In particular it loads the responses that have been saved for the given
    * state into the ->responses member of the state object.
    *
    * Question types with only a single form field for the student's response
    * will not need not restore the responses; the value of the answer
    * field in the quiz_states table is restored to ->responses['']
    * before this function is called. Question types with more response fields
    * should override this method and set the ->responses field to an
    * associative array of responses.
    * @return bool            Indicates success or failure.
    * @param object $question The question object for the question including any
    *                         question type specific information.
    * @param object $state    The saved state to load the session for. This
    *                         object should be updated to include the question
    *                         type specific session information and responses
    *                         (it is passed by reference).
    */
    function restore_session_and_responses(&$question, &$state) {
        // The default implementation does nothing (successfully)
        return true;
    }

    /**
    * Saves the session data and responses for the given question and state
    *
    * This function saves the question type specific session data from the
    * state object to the database. In particular for most question types it saves the
    * responses from the ->responses member of the state object. The question type
    * non-specific data for the state has already been saved in the quiz_states
    * table and the state object contains the corresponding id and
    * sequence number which may be used to index a question type specific table.
    *
    * Question types with only a single form field for the student's response
    * which is contained in ->responses[''] will not have to save this response,
    * it will already have been saved to the answer field of the quiz_states table.
    * Question types with more response fields should override this method and save
    * the responses in their own database tables.
    * @return bool            Indicates success or failure.
    * @param object $question The question object for the question including
    *                         the question type specific information.
    * @param object $state    The state for which the question type specific
    *                         data and responses should be saved.
    */
    function save_session_and_responses(&$question, &$state) {
        // The default implementation does nothing (successfully)
        return true;
    }

    /**
    * Returns an array of values which will give full marks if graded as
    * the $state->responses field
    *
    * The correct answer to the question in the given state, or an example of
    * a correct answer if there are many, is returned. This is used by some question
    * types in the {@link grade_responses()} function but it is also used by the
    * question preview screen to fill in correct responses.
    * @return mixed           An array of values giving the responses corresponding
    *                         to the (or a) correct answer to the question. If there is
    *                         no correct answer that scores 100% then null is returned.
    * @param object $question The question for which the correct answer is to
    *                         be retrieved. Question type specific information is
    *                         available.
    * @param object $state    The state of the question, for which a correct answer is
    *                         needed. Question type specific information is included.
    */
    function get_correct_responses(&$question, &$state) {
        /* The default implementation returns the response for the first answer
        that gives full marks. */
        foreach ($question->options->answers as $answer) {
            if (((int) $answer->fraction) === 1) {
                return array('' => $answer->answer);
            }
        }
        return null;
    }

    /**
    * Return an array of values with the texts for all possible responses stored
    * for the question
    *
    * All answers are found and their text values isolated
    * @return object          A mixed object
    *             ->id        question id. Needed to manage random questions:
    *                         it's the id of the actual question presented to user in a given attempt
    *             ->responses An array of values giving the responses corresponding
    *                         to all answers to the question. Answer ids are used as keys.
    *                         The text and partial credit are the object components
    * @param object $question The question for which the answers are to
    *                         be retrieved. Question type specific information is
    *                         available.
    */
    // ULPGC ecastro
    function get_all_responses(&$question, &$state) {
        unset($answers);
        if (is_array($question->options->answers)) {
            foreach ($question->options->answers as $aid=>$answer) {
                unset ($r);
                $r->answer = $answer->answer;
                $r->credit = $answer->fraction;
                $answers[$aid] = $r;
            }
        } else {
            $answers[]="error"; // just for debugging, eliminate
        }
        $result->id = $question->id;
        $result->responses = $answers;
        return $result;
    }

    /**
    * Return the actual response to the question in a given state
    * for the question
    *
    * @return mixed           An array containing the response or reponses (multiple answer, match)
    *                         given by the user in a particular attempt.
    * @param object $question The question for which the correct answer is to
    *                         be retrieved. Question type specific information is
    *                         available.
    * @param object $state    The state object that corresponds to the question,
    *                         for which a correct answer is needed. Question
    *                         type specific information is included.
    */
    // ULPGC ecastro
    function get_actual_response(&$question, &$state) {
        /* The default implementation only returns the raw ->responses.
          may be overridden by each type*/
        //unset($resp);
        if (isset($state->responses)) {
            return $state->responses;
        } else {
            return null;
        }
    }

    // ULPGC ecastro
    function get_fractional_grade(&$question, &$state) {
        $maxgrade = $question->maxgrade;
        $grade = $state->grade;
        if ($maxgrade) {
            return (float)($grade/$maxgrade);
        } else {
            return (float)$grade;
        }
    }


    /**
    * Checks if the response given is correct and returns the id
    *
    * @return int             The ide number for the stored answer that matches the response
    *                         given by the user in a particular attempt.
    * @param object $question The question for which the correct answer is to
    *                         be retrieved. Question type specific information is
    *                         available.
    * @param object $state    The state object that corresponds to the question,
    *                         for which a correct answer is needed. Question
    *                         type specific information is included.
    */
    // ULPGC ecastro
    function check_response(&$question, &$state){
        return false;
    }

    /**
    * Prints the question including the number, grading details, content,
    * feedback and interactions
    *
    * This function prints the question including the question number,
    * grading details, content for the question, any feedback for the previously
    * submitted responses and the interactions. The default implementation calls
    * various other methods to print each of these parts and most question types
    * will just override those methods.
    * @todo Use CSS stylesheet
    * @param object $question The question to be rendered. Question type
    *                         specific information is included. The
    *                         maximum possible grade is in ->maxgrade. The name
    *                         prefix for any named elements is in ->name_prefix.
    * @param object $state    The state to render the question in. The grading
    *                         information is in ->grade, ->raw_grade and
    *                         ->penalty. The current responses are in
    *                         ->responses. This is an associative array (or the
    *                         empty string or null in the case of no responses
    *                         submitted). The last graded state is in
    *                         ->last_graded (hence the most recently graded
    *                         responses are in ->last_graded->responses). The
    *                         question type specific information is also
    *                         included.
    * @param integer $number  The number for this question.
    * @param object $quiz     The quiz to which the question belongs. The
    *                         question will likely be rendered differently
    *                         depending on the quiz settings.
    * @param object $options  An object describing the rendering options.
    */
    function print_question(&$question, &$state, $number, $quiz, $options) {
        /* The default implementation should work for most question types
        provided the member functions it calls are overridden where required.
        The question number is printed in the first cell of a table.

        The main content is printed below in the top row of the second column
        using {@link print_question_formulation_and_controls}.
        The grading details are printed in the second row in the second column
        using {@print_question_grading_details}.
        The {@link print_question_submit_buttons} member is invoked to add a third
        row containing the submit button(s) when $options->readonly is false. */

        print_simple_box_start('center', '90%');
        echo '<table width="100%" cellspacing="10"><tr>';
        if ($options->readonly) {
            echo '<td nowrap="nowrap" width="80" valign="top" rowspan="2">';
        } else {
            echo '<td nowrap="nowrap" width="80" valign="top" rowspan="3">';
        }

        // Print question number
        echo '<b><font size="+1">' . $number . '</font></b>';
        if (isteacher($quiz->course)) {
            echo ' <font size="1">( ';
            link_to_popup_window ('/mod/quiz/question.php?id=' . $question->id,
             'editquestion', $question->id, 450, 550, get_string('edit'));
            echo ')</font>';
        }
        if ($question->maxgrade and $options->scores) {
            echo '<div class="grade">';
            echo get_string('marks', 'quiz').': ';
            if ($quiz->optionflags & QUIZ_ADAPTIVE) {
                echo '<br />';
                echo ('' === $state->last_graded->grade) ? '--/' : round($state->last_graded->grade, $quiz->decimalpoints).'/';
            }
            echo $question->maxgrade.'</div>';
        }

        echo '</td><td valign="top">';

        $this->print_question_formulation_and_controls($question, $state,
         $quiz, $options);

        echo '</td></tr><tr><td valign="top">';

        if ($question->maxgrade and $options->scores) {
            $this->print_question_grading_details($question, $state, $quiz, $options);
        }

        if (QUIZ_EVENTDUPLICATEGRADE == $state->event) {
            echo ' ';
            print_string('duplicateresponse', 'quiz');
        }

        if(!$options->readonly) {
            echo '</td></tr><tr><td align="right">';
            $this->print_question_submit_buttons($question, $state,
             $quiz, $options);
        }

        if(isset($options->history) and $options->history) {
            if ($options->history == 'all') {
                // show all states
                $states = get_records_select('quiz_states', "attempt = '$state->attempt' AND question = '$question->id' AND event > '0'", 'seq_number DESC');
            } else {
                // show only graded states
                $states = get_records_select('quiz_states', "attempt = '$state->attempt' AND question = '$question->id' AND event = '".QUIZ_EVENTGRADE."'", 'seq_number DESC');
            }
            if (count($states) > 1) {
                $strreviewquestion = get_string('reviewresponse', 'quiz');
                unset($table);
                $table->head  = array (
                    get_string('numberabbr', 'quiz'),
                    get_string('action', 'quiz'),
                    get_string('response', 'quiz'),
                    get_string('time'),
                    get_string('score', 'quiz'),
                    get_string('penalty', 'quiz'),
                    get_string('grade', 'quiz'),
                );
                $table->align = array ('center', 'center', 'left', 'left', 'left', 'left', 'left');
                $table->size = array ('', '', '', '', '', '', '');
                $table->width = '100%';
                foreach ($states as $st) {
                    $b = ($state->id == $st->id) ? '<b>' : '';
                    $be = ($state->id == $st->id) ? '</b>' : '';
                    $table->data[] = array (
                        ($state->id == $st->id) ? '<b>'.$st->seq_number.'</b>' : link_to_popup_window ('/mod/quiz/reviewquestion.php?state='.$st->id.'&amp;number='.$number, 'reviewquestion', $st->seq_number, 450, 650, $strreviewquestion, 'none', true),
                        $b.get_string('event'.$st->event, 'quiz').$be,
                        $b.$this->response_summary($st).$be,
                        $b.userdate($st->timestamp, get_string('timestr', 'quiz')).$be,
                        $b.round($st->raw_grade, $quiz->decimalpoints).$be,
                        $b.round($st->penalty, $quiz->decimalpoints).$be,
                        $b.round($st->grade, $quiz->decimalpoints).$be
                    );
                }
                echo '</td></tr><tr><td colspan="2" valign="top">';
                print_table($table);
            }
        }
        echo '</td></tr></table>';
        print_simple_box_end();
    }


    /**
    * Prints the score obtained and maximum score available plus any penalty
    * information
    *
    * This function prints a summary of the scoring in the most recently
    * graded state (the question may not have been submitted for marking at
    * the current state). The default implementation should be suitable for most
    * question types.
    * @param object $question The question for which the grading details are
    *                         to be rendered. Question type specific information
    *                         is included. The maximum possible grade is in
    *                         ->maxgrade.
    * @param object $state    The state. In particular the grading information
    *                          is in ->grade, ->raw_grade and ->penalty.
    * @param object $quiz     The quiz to which the question belongs. The
    *                         grading details may be rendered differently
    *                         depending on the quiz settings.
    * @param object $options  An object describing the rendering options.
    */
    function print_question_grading_details(&$question, &$state, $quiz, $options) {
        /* The default implementation prints the number of marks if no attempt
        has been made. Otherwise it displays the grade obtained out of the
        maximum grade available and a warning if a penalty was applied for the
        attempt and displays the overall grade obtained counting all previous
        responses (and penalties) */

        if (!empty($question->maxgrade) && $options->scores) {
            if (!('' === $state->last_graded->grade)) {
                // Display the grading details from the last graded state
                $grade->cur = round($state->last_graded->grade, $quiz->decimalpoints);
                $grade->max = $question->maxgrade;
                $grade->raw = round($state->last_graded->raw_grade, $quiz->decimalpoints);

                echo '<div class="correctness">';
                if ($grade->raw >= $grade->max) {
                    print_string('correct', 'quiz');
                } else if ($grade->raw > 0) {
                    print_string('partiallycorrect', 'quiz');
                } else {
                    print_string('incorrect', 'quiz');
                }
                echo '</div>';

                echo '<div class="gradingdetails">';
                // print grade for this submission
                print_string('gradingdetails', 'quiz', $grade);
                if ($quiz->penaltyscheme) {
                    // print details of grade adjustment due to penalties
                    if ($state->last_graded->raw_grade > $state->last_graded->grade){
                        print_string('gradingdetailsadjustment', 'quiz', $grade);
                    }
                    // print info about new penalty
                    // penalty is relevant only if the answer is not correct and further attempts are possible
                    if (($state->last_graded->raw_grade < $question->maxgrade) and (QUIZ_EVENTCLOSE !== $state->event)) {
                        if ('' !== $state->last_graded->penalty && ((float)$state->last_graded->penalty) > 0.0) {
                            // A penalty was applied so display it
                            print_string('gradingdetailspenalty', 'quiz', $state->last_graded->penalty);
                        } else {
                            /* No penalty was applied even though the answer was
                            not correct (eg. a syntax error) so tell the student
                            that they were not penalised for the attempt */
                            print_string('gradingdetailszeropenalty', 'quiz');
                        }
                    }
                }
                echo '</div>';
            }
        }
    }

    /**
    * Prints the main content of the question including any interactions
    *
    * This function prints the main content of the question including the
    * interactions for the question in the state given. The last graded responses
    * are printed or indicated and the current responses are selected or filled in.
    * Any names (eg. for any form elements) are prefixed with $question->name_prefix.
    * This method is called from the print_question method.
    * @param object $question The question to be rendered. Question type
    *                         specific information is included. The name
    *                         prefix for any named elements is in ->name_prefix.
    * @param object $state    The state to render the question in. The grading
    *                         information is in ->grade, ->raw_grade and
    *                         ->penalty. The current responses are in
    *                         ->responses. This is an associative array (or the
    *                         empty string or null in the case of no responses
    *                         submitted). The last graded state is in
    *                         ->last_graded (hence the most recently graded
    *                         responses are in ->last_graded->responses). The
    *                         question type specific information is also
    *                         included.
    *                         The state is passed by reference because some adaptive
    *                         questions may want to update it during rendering
    * @param object $quiz     The quiz to which the question belongs. The
    *                         question might be rendered differently
    *                         depending on the quiz settings.
    * @param object $options  An object describing the rendering options.
    */
    function print_question_formulation_and_controls(&$question, &$state, $quiz, $options) {
        /* This default implementation prints an error and must be overridden
        by all question type implementations, unless the default implementation
        of print_question has been overridden. */

        notify('Error: Question formulation and input controls has not'
               .'  been implemented for question type '.$this->name());
    }

    /**
    * Prints the submit button(s) for the question in the given state
    *
    * This function prints the submit button(s) for the question in the
    * given state. The name of any button created will be prefixed with the
    * unique prefix for the question in $question->name_prefix. The suffix
    * 'mark' is reserved for the single question mark button and the suffix
    * 'validate' is reserved for the single question validate button (for
    * question types which support it). Other suffixes will result in a response
    * of that name in $state->responses which the printing and grading methods
    * can then use.
    * @param object $question The question for which the submit button(s) are to
    *                         be rendered. Question type specific information is
    *                         included. The name prefix for any
    *                         named elements is in ->name_prefix.
    * @param object $state    The state to render the buttons for. The
    *                         question type specific information is also
    *                         included.
    * @param object $quiz     The quiz to which the question belongs. The
    *                         choice of buttons may depend on the quiz
    *                         settings.
    * @param object $options  An object describing the rendering options.
    */
    function print_question_submit_buttons(&$question, &$state, $quiz, $options) {
        /* The default implementation should be suitable for most question
        types. It prints a mark button in the case where individual marking is
        allowed in the quiz. */

        if($quiz->optionflags & QUIZ_ADAPTIVE) {
            echo '<input type="submit" name="';
            echo $question->name_prefix;
            echo 'mark" value="';
            print_string('mark', 'quiz');
            echo '" />';
        }
    }


    /**
    * Return a summary of the student response
    *
    * This function returns a short string of no more than a given length that
    * summarizes the student's response in the given $state. This is used for
    * example in the response history table
    * @return string         The summary of the student response
    * @param object $state   The state whose responses are to be summarized
    * @param int $length     The maximum length of the returned string
    */
    function response_summary($state, $length=80) {
        // This should almost certainly be overridden
        return substr($state->answer, 0, $length);
    }

    /**
    * Renders the question for printing and returns the LaTeX source produced
    *
    * This function should render the question suitable for a printed problem
    * or solution sheet in LaTeX and return the rendered output.
    * @return string          The LaTeX output.
    * @param object $question The question to be rendered. Question type
    *                         specific information is included.
    * @param object $state    The state to render the question in. The
    *                         question type specific information is also
    *                         included.
    * @param object $quiz     The quiz to which the question belongs. The
    *                         question will likely be rendered differently
    *                         depending on the quiz settings.
    * @param string $type     Indicates if the question or the solution is to be
    *                         rendered with the values 'question' and
    *                         'solution'.
    */
    function get_texsource(&$question, &$state, $quiz, $type) {
        // The default implementation simply returns a string stating that
        // the question is only available online.

        return get_string('onlineonly', 'texsheet');
    }

    /**
    * Compares two question states for equivalence of the student's responses
    *
    * The responses for the two states must be examined to see if they represent
    * equivalent answers to the question by the student. This method will be
    * invoked for each of the previous states of the question before grading
    * occurs. If the student is found to have already attempted the question
    * with equivalent responses then the attempt at the question is ignored;
    * grading does not occur and the state does not change. Thus they are not
    * penalized for this case.
    * @return boolean
    * @param object $question  The question for which the states are to be
    *                          compared. Question type specific information is
    *                          included.
    * @param object $state     The state of the question. The responses are in
    *                          ->responses.
    * @param object $teststate The state whose responses are to be
    *                          compared. The state will be of the same age or
    *                          older than $state.
    */
    function compare_responses(&$question, $state, $teststate) {
        // The default implementation performs a comparison of the response
        // arrays. The ordering of the arrays does not matter.
        // Question types may wish to override this (eg. to ignore trailing
        // white space or to make "7.0" and "7" compare equal).
        return $state->responses == $teststate->responses;
    }

    /**
    * Performs response processing and grading
    *
    * This function performs response processing and grading and updates
    * the state accordingly.
    * @return boolean         Indicates success or failure.
    * @param object $question The question to be graded. Question type
    *                         specific information is included.
    * @param object $state    The state of the question to grade. The current
    *                         responses are in ->responses. The last graded state
    *                         is in ->last_graded (hence the most recently graded
    *                         responses are in ->last_graded->responses). The
    *                         question type specific information is also
    *                         included. The ->raw_grade and ->penalty fields
    *                         must be updated. The method is able to
    *                         close the question session (preventing any further
    *                         attempts at this question) by setting
    *                         $state->event to QUIZ_EVENTCLOSE.
    * @param object $quiz     The quiz to which the question belongs. The
    *                         question might be graded differently depending on
    *                         the quiz settings.
    */
    function grade_responses(&$question, &$state, $quiz) {
        /* The default implementation uses the comparison method to check if
        the responses given are equivalent to the responses for each answer
        in turn and sets the marks and penalty accordingly. This works for the
        most simple question types. */

        $teststate = clone($state);
        $teststate->raw_grade = 0;
        foreach($question->options->answers as $answer) {
            $teststate->responses[''] = $answer->answer;

            if($this->compare_responses($question, $state, $teststate)) {
                $state->raw_grade = min(max((float) $answer->fraction,
                 0.0), 1.0) * $question->maxgrade;
                break;
            }
        }
        if (empty($state->raw_grade)) {
            $state->raw_grade = 0.0;
        }
        // Only allow one attempt at the question
        $state->penalty = 1;

        return true;
    }


    /**
    * Includes configuration settings for the question type on the quiz admin
    * page
    *
    * Returns an array of objects describing the options for the question type
    * to be included on the quiz module admin page.
    * Configuration options can be included by setting the following fields in
    * the object:
    * ->name           The name of the option within this question type.
    *                  The full option name will be constructed as
    *                  "quiz_{$this->name()}_$name", the human readable name
    *                  will be displayed with get_string($name, 'quiz').
    * ->code           The code to display the form element, help button, etc.
    *                  i.e. the content for the central table cell. Be sure
    *                  to name the element "quiz_{$this->name()}_$name" and
    *                  set the value to $CFG->{"quiz_{$this->name()}_$name"}.
    * ->help           Name of the string from the quiz module language file
    *                  to be used for the help message in the third column of
    *                  the table. An empty string (or the field not set)
    *                  means to leave the box empty.
    * Links to custom settings pages can be included by setting the following
    * fields in the object:
    * ->name           The name of the link text string.
    *                  get_string($name, 'quiz') will be called.
    * ->link           The filename part of the URL for the link. The full URL
    *                  is contructed as
    *                  "$CFG->wwwroot/mod/quiz/questiontypes/{$this->name()}/$link?sesskey=$sesskey"
    *                  [but with the relavant calls to the s and rawurlencode
    *                  functions] where $sesskey is the sesskey for the user.
    * @return array    Array of objects describing the configuration options to
    *                  be included on the quiz module admin page.
    */
    function get_config_options() {
        // No options by default

        return false;
    }

    /**
    * Returns true if the editing wizard is finished, false otherwise. The
    * default implementation returns true, which is suitable for all question-
    * types that only use one editing form. This function is used in
    * question.php to decide whether we can regrade any states of the edited
    * question and redirect to edit.php.
    *
    * The dataset dependent question-type, which is extended by the calculated
    * question-type, overwrites this method because it uses multiple pages (i.e.
    * a wizard) to set up the question and associated datasets.
    *
    * @param object $form  The data submitted by the previous page.
    *
    * @return boolean      Whether the wizard's last page was submitted or not.
    */
    function finished_edit_wizard(&$form) {
        //In the default case there is only one edit page.
        return true;
    }

    function print_replacement_options($question, $course, $quizid='0') {
    // This function is used near the end of the question edit forms in all question types
    // It prints the table of quizzes in which the question is used
    // containing checkboxes to allow the teacher to replace the old question version

        // Disable until the versioning code has been fixed
        return;

        // no need to display replacement options if the question is new
        if(empty($question->id)) {
            return true;
        }

        // get quizzes using the question (using the question_instances table)
        $quizlist = array();
        if(!$instances = get_records('quiz_question_instances', 'question', $question->id)) {
            $instances = array();
        }
        foreach($instances as $instance) {
            $quizlist[$instance->quiz] = $instance->quiz;
        }
        $quizlist = implode(',', $quizlist);
        if(empty($quizlist) or !$quizzes = get_records_list('quiz', 'id', $quizlist)) {
            $quizzes = array();
        }

        // do the printing
        if(count($quizzes) > 0) {
            // print the table
            $strquizname  = get_string('modulename', 'quiz');
            $strdoreplace = get_string('replace', 'quiz');
            $straffectedstudents = get_string('affectedstudents', 'quiz', $course->students);
            echo "<tr valign=\"top\">\n";
            echo "<td align=\"right\"><b>".get_string("replacementoptions", "quiz").":</b></td>\n";
            echo "<td align=\"left\">\n";
            echo "<table cellpadding=\"5\" align=\"left\" class=\"generalbox\" width=\"100%\">\n";
            echo "<tr>\n";
            echo "<th align=\"left\" valign=\"top\" nowrap=\"nowrap\" class=\"generaltableheader c0\">$strquizname</th>\n";
            echo "<th align=\"center\" valign=\"top\" nowrap=\"nowrap\" class=\"generaltableheader c0\">$strdoreplace</th>\n";
            echo "<th align=\"left\" valign=\"top\" nowrap=\"nowrap\" class=\"generaltableheader c0\">$straffectedstudents</th>\n";
            echo "</tr>\n";
            foreach($quizzes as $quiz) {
                // work out whethere it should be checked by default
                $checked = '';
                if((int)$quizid === (int)$quiz->id
                    or empty($quiz->usercount)) {
                    $checked = "checked=\"checked\"";
                }

                // find how many different students have already attempted this quiz
                $students = array();
                if($attempts = get_records_select('quiz_attempts', "quiz = '$quiz->id' AND preview = '0'")) {
                    foreach($attempts as $attempt) {
                        if (record_exists('quiz_states', 'attempt', $attempt->id, 'question', $question->id, 'originalquestion', 0)) {
                            $students[$attempt->userid] = 1;
                        }
                    }
                }
                $studentcount = count($students);

                $strstudents = $studentcount === 1 ? $course->student : $course->students;
                echo "<tr>\n";
                echo "<td align=\"left\" class=\"generaltablecell c0\">".format_string($quiz->name)."</td>\n";
                echo "<td align=\"center\" class=\"generaltablecell c0\"><input name=\"q{$quiz->id}replace\" type=\"checkbox\" ".$checked." /></td>\n";
                echo "<td align=\"left\" class=\"generaltablecell c0\">".(($studentcount) ? $studentcount.' '.$strstudents : '-')."</td>\n";
                echo "</tr>\n";
            }
            echo "</table>\n";
        }
        echo "</td></tr>\n";
    }

    function print_question_form_end($question, $submitscript='') {
    // This function is used at the end of the question edit forms in all question types
    // It prints the submit, copy, and cancel buttons and the standard hidden form fields
        global $USER;
        echo '<tr valign="top">
              <td colspan="2" align="center">
              <input type="submit" '.$submitscript.' value="'.get_string('savechanges').'" /> ';
        if ($question->id) {
// Switched off until bug 3445 is fixed
//            echo '<input type="submit" name="makecopy" '.$submitscript.' value="'.get_string("makecopy", "quiz").'" /> ';
        }
        echo '<input type="submit" name="cancel" value="'.get_string("cancel").'" />
              <input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />
              <input type="hidden" name="id" value="'.$question->id.'" />
              <input type="hidden" name="qtype" value="'.$question->qtype.'" />';
        // The following hidden field indicates that the versioning code should be turned on, i.e.,
        // that old versions should be kept if necessary
        echo '<input type="hidden" name="versioning" value="on" />
              </td></tr>';
    }

}

/// QUIZ_QTYPES INITIATION //////////////////

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




//////////////////////////////////////////////////////////////////////////////////////
/// Any other quiz functions go here.  Each of them must have a name that
/// starts with quiz_

/**
* Move all questions in $category1 to $category2
*
* @return boolean    indicate Success/Failure
* @param $category1  the id of the category to move away from
* @param $category2  the id of the category to move to
*/
function quiz_move_questions($category1, $category2) {
    global $CFG;
    return execute_sql("UPDATE {$CFG->prefix}quiz_questions
                           SET category = '$category2'
                         WHERE category = '$category1'",
                       false);
}

/**
* Construct name prefixes for question form element names
*
* Construct the name prefix that should be used for example in the
* names of form elements created by questions for inclusion in the
* quiz page. This is called by {@link quiz_get_question_options()}
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

/**
* Updates the question objects in an array with the question type specific
* information for each one by calling {@link get_question_options()}
*
* The get_question_options method of the question type of each question in the
* array is called to add the options field to the question object.
* @return bool            Indicates success or failure.
* @param array $questions The array of question objects to be updated.
*/
function quiz_get_question_options(&$questions) {
    global $QUIZ_QTYPES;

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
}

/**
* Creates an object to represent a new attempt at a quiz
*
* Creates an attempt object to represent an attempt at the quiz by the current
* user starting at the current time. The ->id field is not set. The object is
* NOT written to the database.
* @return object                The newly created attempt object.
* @param object $quiz           The quiz to create an attempt for.
* @param integer $attemptnumber The sequence number for the attempt.
*/
function quiz_create_attempt($quiz, $attemptnumber) {
    global $USER;

    if (!$attemptnumber > 1 or !$quiz->attemptonlast or !$attempt = get_record('quiz_attempts', 'quiz', $quiz->id, 'userid', $USER->id, 'attempt', $attemptnumber-1)) {
        // we are not building on last attempt so create a new attempt
        $attempt->quiz = $quiz->id;
        $attempt->userid = $USER->id;
        $attempt->preview = 0;
        if ($quiz->shufflequestions) {
            $attempt->layout = quiz_repaginate($quiz->questions, $quiz->questionsperpage, true);
        } else {
            $attempt->layout = $quiz->questions;
        }
    }

    $timenow = time();
    $attempt->attempt = $attemptnumber;
    $attempt->sumgrades = 0.0;
    $attempt->timestart = $timenow;
    $attempt->timefinish = 0;
    $attempt->timemodified = $timenow;

    return $attempt;
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
* @param object $quiz     The quiz to which the questions belong.
* @param object $attempt  The quiz attempt for which the question sessions are
*                         to be restored or created.
*/
function quiz_restore_question_sessions(&$questions, $quiz, $attempt) {
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
           "   AND n.attemptid = '$attempt->id'".
           "   AND n.questionid IN ($questionlist)";
    $states = get_records_sql($sql);

    // Load the newest graded states for the questions
    $sql = "SELECT $statefields".
           "  FROM {$CFG->prefix}quiz_states s,".
           "       {$CFG->prefix}quiz_newest_states n".
           " WHERE s.id = n.newgraded".
           "   AND n.attemptid = '$attempt->id'".
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
            if ($quiz->attemptonlast and $attempt->attempt > 1 and !$attempt->preview) {
                // build on states from last attempt
                if (!$lastattemptid = get_field('quiz_attempts', 'id', 'quiz', $attempt->quiz, 'userid', $attempt->userid, 'attempt', $attempt->attempt-1)) {
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
                $states[$i]->attempt = $attempt->id;
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
                $states[$i]->attempt = $attempt->id;
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
                 ->create_session_and_responses($questions[$i], $states[$i], $quiz, $attempt)) {
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
* @param array $questions The questions for which states are needed
* @param array $states    The states as loaded from the database, indexed
*                         by question id
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

    // addslashes that have been stripped in quiz_extract_response
    foreach ($state->responses as $key => $response) {
        $state->responses[$key] = addslashes($response);
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

    // stripslashes again, that have been added at the top of this function
    foreach ($state->responses as $key => $response) {
        $state->responses[$key] = stripslashes($response);
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
            $key = substr($key, strlen($questions[$quid]->name_prefix));
            if (false === $key) {
                $key = '';
            }

            // Check for question validate and mark buttons & set events
            if ($key === 'validate') {
                $actions[$quid]->event = QUIZ_EVENTVALIDATE;
            } else if ($key === 'mark') {
                $actions[$quid]->event = QUIZ_EVENTGRADE;
            } else {
                $actions[$quid]->event = $defaultevent;
            }

            // Update the state with the new response
            $actions[$quid]->responses[$key] = stripslashes($response);
        }
    }
    return $actions;
}



/**
* For a given question in an attempt we walk the complete history of states
* and recalculate the grades as we go along.
*
* This is used when a question in an existing quiz is changed and old student
* responses need to be marked with the new version of a question.
*
* TODO: Finish documenting this
* @return boolean            Indicates success/failure
* @param object  $question   A question object
* @param object  $attempt    The attempt, in which the question needs to be regraded.
* @param object  $quiz       Optional. The quiz object that the attempt corresponds to.
* @param boolean $verbose    Optional. Whether to print progress information or not.
*/
function quiz_regrade_question_in_attempt($question, $attempt, $quiz=false, $verbose=false) {
    if (!$quiz &&  !($quiz = get_record('quiz', 'id', $attempt->quiz))) {
        $verbose && notify("Regrading of quiz #{$attempt->quiz} failed; " .
         "Couldn't load quiz record from database!");
        return false;
    }

    if ($states = get_records_select('quiz_states',
     "attempt = '{$attempt->id}' AND question = '{$question->id}'", 'seq_number ASC')) {
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
            if (!quiz_process_responses($question, $replaystate, $action, $quiz,
             $attempt)) {
                $verbose && notify("Couldn't regrade state #{$state->id}!");
            }

            // We need rounding here because grades in the DB get truncated
            // e.g. 0.33333 != 0.3333333, but we want them to be equal here
            if (round((float)$replaystate->grade, 5) != round((float)$states[$j]->grade, 5)) {
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
            echo "\n"; @flush(); @ob_flush();
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
* @param object $quiz     The quiz object
* @param object $attempt  The attempt is passed by reference so that
*                         during grading its ->sumgrades field can be updated
*
* @todo There is a variable $quiz->ignoredupresp which makes the function go through
*       all previous states when checking if a response is duplicated. There is no user
*       interface for this yet.
*/
function quiz_process_responses(&$question, &$state, $action, $quiz, &$attempt) {
    global $QUIZ_QTYPES;

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
         $question, $state, $quiz);
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
            if ($quiz->optionflags & QUIZ_IGNORE_DUPRESP) {
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
                 $question, $state, $quiz);
                // Calculate overall grade using correct penalty method
                quiz_apply_penalty_and_timelimit($question, $state, $attempt, $quiz);
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
             $question, $state, $quiz);
            // Calculate overall grade using correct penalty method
            quiz_apply_penalty_and_timelimit($question, $state, $attempt, $quiz);
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
* the QUIZ_IGNORE_DUPRESP bit in $quiz->optionflags is set
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
* Applies the penalty from the previous attempts to the raw grade for the current
* attempt
*
* The grade for the question in the current state is computed by subtracting the
* penalty accumulated over the previous marked attempts at the question from the
* raw grade. If the timestamp is more than 1 minute beyond the start of the attempt
* the grade is set to zero. The ->grade field of the state object is modified to
* reflect the new grade but is never allowed to decrease.
* @param object $question The question for which the penalty is to be applied.
* @param object $state    The state for which the grade is to be set from the
*                         raw grade and the cumulative penalty from the last
*                         graded state. The ->grade field is updated by applying
*                         the penalty scheme for the quiz to the ->raw_grade and
*                         ->last_graded->penalty fields.
* @param object $quiz     The quiz to which the question belongs. The penalty
*                         scheme to apply is given by the ->penaltyscheme field.
*/
function quiz_apply_penalty_and_timelimit(&$question, &$state, $attempt, $quiz) {
    // deal with penaly
    if ($quiz->penaltyscheme) {
            $state->grade = $state->raw_grade - $state->sumpenalty;
            $state->sumpenalty += (float) $state->penalty;
    } else {
        $state->grade = $state->raw_grade;
    }

    // deal with timeimit
    if ($quiz->timelimit) {
        // We allow for 5% uncertainty in the following test
        if (($state->timestamp - $attempt->timestart) > ($quiz->timelimit * 63)) {
            $state->grade = 0;
        }
    }

    // deal with quiz closing time
    if ($state->timestamp > ($quiz->timeclose + 60)) { // allowing 1 minute lateness
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

// ULPGc ecastro
function quiz_get_question_review($quiz, $question) {
// returns a question icon
    global $CFG;
    $qnum = $question->id;
    $strpreview = get_string('previewquestion', 'quiz');
    $context = $quiz->id ? '&amp;contextquiz='.$quiz->id : '';
    $quiz_id = $quiz->id ? '&amp;quizid=' . $quiz->id : '';
    return "<a title=\"$strpreview\" href=\"javascript:void();\" onClick=\"openpopup('/mod/quiz/preview.php?id=$qnum$quiz_id','$strpreview','scrollbars=yes,resizable=yes,width=700,height=480', false)\">
          <img src=\"$CFG->pixpath/t/preview.gif\" border=\"0\" alt=\"$strpreview\" /></a>";

}




/**
* Print the question image if there is one
*
* @param integer $quizid  The id of the quiz
* @param object $question The question object
*/
function quiz_print_possible_question_image($quizid, $question) {

    global $CFG;

    if ($quizid == '') {
        $quizid = '0';
    }

    if ($question->image) {
        echo '<img border="0" src="';

        if (substr(strtolower($question->image), 0, 7) == 'http://') {
            echo $question->image;

        } else if ($CFG->slasharguments) {        // Use this method if possible for better caching
            echo "$CFG->wwwroot/mod/quiz/quizfile.php/$quizid/$question->id/$question->image";

        } else {
            echo "$CFG->wwwroot/mod/quiz/quizfile.php?file=/$quizid/$question->id/$question->image";
        }
        echo '" alt="" />';

    }
}

/**
* Returns a comma separated list of question ids for the current page
*
* @return string         Comma separated list of question ids
* @param string $layout  The string representing the quiz layout. Each page is represented as a
*                        comma separated list of question ids and 0 indicating page breaks.
*                        So 5,2,0,3,0 means questions 5 and 2 on page 1 and question 3 on page 2
* @param integer $page   The number of the current page.
*/
function quiz_questions_on_page($layout, $page) {
    $pages = explode(',0', $layout);
    return trim($pages[$page], ',');
}

/**
* Returns a comma separated list of question ids for the quiz
*
* @return string         Comma separated list of question ids
* @param string $layout  The string representing the quiz layout. Each page is represented as a
*                        comma separated list of question ids and 0 indicating page breaks.
*                        So 5,2,0,3,0 means questions 5 and 2 on page 1 and question 3 on page 2
*/
function quiz_questions_in_quiz($layout) {
    return str_replace(',0', '', $layout);
}

/**
* Returns the number of pages in the quiz layout
*
* @return integer         Comma separated list of question ids
* @param string $layout  The string representing the quiz layout.
*/
function quiz_number_of_pages($layout) {
    return substr_count($layout, ',0');
}

/**
* Returns the first question number for the current quiz page
*
* @return integer  The number of the first question
* @param string $quizlayout The string representing the layout for the whole quiz
* @param string $pagelayout The string representing the layout for the current page
*/
function quiz_first_questionnumber($quizlayout, $pagelayout) {
    // this works by finding all the questions from the quizlayout that
    // come before the current page and then adding up their lengths.
    global $CFG;
    $start = strpos($quizlayout, ','.$pagelayout.',')-2;
    if ($start > 0) {
        $prevlist = substr($quizlayout, 0, $start);
        return get_field_sql("SELECT sum(length)+1 FROM {$CFG->prefix}quiz_questions
         WHERE id IN ($prevlist)");
    } else {
        return 1;
    }
}

/**
* Re-paginates the quiz layout
*
* @return string         The new layout string
* @param string $layout  The string representing the quiz layout.
* @param integer $perpage The number of questions per page
* @param boolean $shuffle Should the questions be reordered randomly?
*/
function quiz_repaginate($layout, $perpage, $shuffle=false) {
    $layout = str_replace(',0', '', $layout); // remove existing page breaks
    $questions = explode(',', $layout);
    if ($shuffle) {
        srand((float)microtime() * 1000000); // for php < 4.2
        shuffle($questions);
    }
    $i = 1;
    $layout = '';
    foreach ($questions as $question) {
        if ($perpage and $i > $perpage) {
            $layout .= '0,';
            $i = 1;
        }
        $layout .= $question.',';
        $i++;
    }
    return $layout.'0';
}

/**
* Print navigation panel for quiz attempt and review pages
*
* @param integer $page     The number of the current page (counting from 0).
* @param integer $pages    The total number of pages.
*/
function quiz_print_navigation_panel($page, $pages) {
    //$page++;
    echo '<div class="pagingbar">';
    echo '<span class="title">' . get_string('page') . ':</span>';
    if ($page > 0) {
        // Print previous link
        $strprev = get_string('previous');
        echo '<a href="javascript:navigate(' . ($page - 1) . ');" title="'
         . $strprev . '">(' . $strprev . ')</a>';
    }
    for ($i = 0; $i < $pages; $i++) {
        if ($i == $page) {
            echo '<span class="thispage">'.($i+1).'</span>';
        } else {
            echo '<a href="javascript:navigate(' . ($i) . ');">'.($i+1).'</a>';
        }
    }

    if ($page < $pages - 1) {
        // Print next link
        $strnext = get_string('next');
        echo '<a href="javascript:navigate(' . ($page + 1) . ');" title="'
         . $strnext . '">(' . $strnext . ')</a>';
    }
    echo '</div>';
}

/**
* Prints a question for a quiz page
*
* Simply calls the question type specific print_question() method.
*/
function quiz_print_quiz_question(&$question, &$state, $number, $quiz, $options=null) {
    global $QUIZ_QTYPES;

    $QUIZ_QTYPES[$question->qtype]->print_question($question, $state, $number,
     $quiz, $options);
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
* Creates an array of maximum grades for a quiz
*
* The grades are extracted from the quiz_question_instances table.
* @return array        Array of grades indexed by question id
*                      These are the maximum possible grades that
*                      students can achieve for each of the questions
* @param integer $quiz The quiz object
*/
function quiz_get_all_question_grades($quiz) {
    global $CFG;

    $questionlist = quiz_questions_in_quiz($quiz->questions);
    if (empty($questionlist)) {
        return array();
    }

    $instances = get_records_sql("SELECT question,grade,id
                            FROM {$CFG->prefix}quiz_question_instances
                            WHERE quiz = '$quiz->id'" .
                            (is_null($questionlist) ? '' :
                            "AND question IN ($questionlist)"));

    $list = explode(",", $questionlist);
    $grades = array();

    foreach ($list as $qid) {
        if (isset($instances[$qid])) {
            $grades[$qid] = $instances[$qid]->grade;
        } else {
            $grades[$qid] = 1;
        }
    }
    return $grades;
}

function quiz_get_user_attempt_unfinished($quizid, $userid) {
// Returns an object containing an unfinished attempt (if there is one)
    return get_record("quiz_attempts", "quiz", $quizid, "userid", $userid, "timefinish", 0);
}

function quiz_get_user_attempts($quizid, $userid) {
// Returns a list of all attempts by a user
    return get_records_select("quiz_attempts", "quiz = '$quizid' AND userid = '$userid' AND timefinish > 0",
                              "attempt ASC");
}


function quiz_get_best_grade($quiz, $userid) {
/// Get the best current grade for a particular user in a quiz
if (!$grade = get_record('quiz_grades', 'quiz', $quiz->id, 'userid', $userid)) {
        return NULL;
    }

    return (round($grade->grade,$quiz->decimalpoints));
}

/**
* Save the overall grade for a user at a quiz in the quiz_grades table
*
* @return boolean        Indicates success or failure.
* @param object $quiz    The quiz for which the best grade is to be calculated
*                        and then saved.
* @param integer $userid The id of the user to save the best grade for. Can be
*                        null in which case the current user is assumed.
*/
function quiz_save_best_grade($quiz, $userid=null) {
    global $USER;

    // Assume the current user if $userid is null
    if (is_null($userid)) {
        $userid = $USER->id;
    }

    // Get all the attempts made by the user
    if (!$attempts = quiz_get_user_attempts($quiz->id, $userid)) {
        notify('Could not find any user attempts');
        return false;
    }

    // Calculate the best grade
    $bestgrade = quiz_calculate_best_grade($quiz, $attempts);
    $bestgrade = (($bestgrade / $quiz->sumgrades) * $quiz->grade);
    $bestgrade = round($bestgrade, $quiz->decimalpoints);

    // Save the best grade in the database
    if ($grade = get_record('quiz_grades', 'quiz', $quiz->id, 'userid',
     $userid)) {
        $grade->grade = $bestgrade;
        $grade->timemodified = time();
        if (!update_record('quiz_grades', $grade)) {
            notify('Could not update best grade');
            return false;
        }
    } else {
        $grade->quiz = $quiz->id;
        $grade->userid = $userid;
        $grade->grade = $bestgrade;
        $grade->timemodified = time();
        if (!insert_record('quiz_grades', $grade)) {
            notify('Could not insert new best grade');
            return false;
        }
    }
    return true;
}

/**
* Calculate the overall grade for a quiz given a number of attempts by a particular user.
*
* @return float          The overall grade
* @param object $quiz    The quiz for which the best grade is to be calculated
* @param array $attempts An array of all the attempts of the user at the quiz
*/
function quiz_calculate_best_grade($quiz, $attempts) {

    switch ($quiz->grademethod) {

        case QUIZ_ATTEMPTFIRST:
            foreach ($attempts as $attempt) {
                return $attempt->sumgrades;
            }
            break;

        case QUIZ_ATTEMPTLAST:
            foreach ($attempts as $attempt) {
                $final = $attempt->sumgrades;
            }
            return $final;

        case QUIZ_GRADEAVERAGE:
            $sum = 0;
            $count = 0;
            foreach ($attempts as $attempt) {
                $sum += $attempt->sumgrades;
                $count++;
            }
            return (float)$sum/$count;

        default:
        case QUIZ_GRADEHIGHEST:
            $max = 0;
            foreach ($attempts as $attempt) {
                if ($attempt->sumgrades > $max) {
                    $max = $attempt->sumgrades;
                }
            }
            return $max;
    }
}

/**
* Return the attempt with the best grade for a quiz
*
* Which attempt is the best depends on $quiz->grademethod. If the grade
* method is GRADEAVERAGE then this function simply returns the last attempt.
* @return object         The attempt with the best grade
* @param object $quiz    The quiz for which the best grade is to be calculated
* @param array $attempts An array of all the attempts of the user at the quiz
*/
function quiz_calculate_best_attempt($quiz, $attempts) {

    switch ($quiz->grademethod) {

        case QUIZ_ATTEMPTFIRST:
            foreach ($attempts as $attempt) {
                return $attempt;
            }
            break;

        case QUIZ_GRADEAVERAGE: // need to do something with it :-)
        case QUIZ_ATTEMPTLAST:
            foreach ($attempts as $attempt) {
                $final = $attempt;
            }
            return $final;

        default:
        case QUIZ_GRADEHIGHEST:
            $max = -1;
            foreach ($attempts as $attempt) {
                if ($attempt->sumgrades > $max) {
                    $max = $attempt->sumgrades;
                    $maxattempt = $attempt;
                }
            }
            return $maxattempt;
    }
}


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

// this function creates default export filename
function default_export_filename($course,$category) {
    //Take off some characters in the filename !!
    $takeoff = array(" ", ":", "/", "\\", "|");
    $export_word = str_replace($takeoff,"_",strtolower(get_string("exportfilename","quiz")));
    //If non-translated, use "export"
    if (substr($export_word,0,1) == "[") {
        $export_word= "export";
    }

    //Calculate the date format string
    $export_date_format = str_replace(" ","_",get_string("exportnameformat","quiz"));
    //If non-translated, use "%Y%m%d-%H%M"
    if (substr($export_date_format,0,1) == "[") {
        $export_date_format = "%%Y%%m%%d-%%H%%M";
    }

    //Calculate the shortname
    $export_shortname = clean_filename($course->shortname);
    if (empty($export_shortname) or $export_shortname == '_' ) {
        $export_shortname = $course->id;
    }

    //Calculate the category name
    $export_categoryname = clean_filename($category->name);

    //Calculate the final export filename
    //The export word
    $export_name = $export_word."-";
    //The shortname
    $export_name .= strtolower($export_shortname)."-";
    //The category name
    $export_name .= strtolower($export_categoryname)."-";
    //The date format
    $export_name .= userdate(time(),$export_date_format,99,false);
    //The extension - no extension, supplied by format
    // $export_name .= ".txt";

    return $export_name;
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
* Array of names of quizzes a question appears in
*
* @return array   Array of quiz names
* @param integer  Question id
*/
function quizzes_question_used($id) {

    $quizlist = array();
    if ($instances = get_records('quiz_question_instances', 'question', $id)) {
        foreach($instances as $instance) {
            $quizlist[$instance->quiz] = get_field('quiz', 'name', 'id', $instance->quiz);
        }
    }

    return $quizlist;
}

/**
* Array of names of quizzes a category (and optionally its childs) appears in
*
* @return array   Array of quiz names (with quiz->id as array keys)
* @param integer  Quiz category id
* @param boolean  Examine category childs recursively
*/
function quizzes_category_used($id, $recursive = false) {

    $quizlist = array();

    //Look for each question in the category
    if ($questions = get_records('quiz_questions', 'category', $id)) {
        foreach ($questions as $question) {
            $qlist = quizzes_question_used($question->id);
            $quizlist = $quizlist + $qlist;
        }
    }

    //Look under child categories recursively
    if ($recursive) {
        if ($childs = get_records('quiz_categories', 'parent', $id)) {
            foreach ($childs as $child) {
                $quizlist = $quizlist + quizzes_category_used($child->id, $recursive);
            }
        }
    }

    return $quizlist;
}

/**
* Parse field names used for the replace options on question edit forms
*/
function quiz_parse_fieldname($name, $nameprefix='question') {
    $reg = array();
    if (preg_match("/$nameprefix(\\d+)(\w+)/", $name, $reg)) {
        return array('mode' => $reg[2], 'id' => (int)$reg[1]);
    } else {
        return false;
    }
}


/**
* Determine render options
*/
function quiz_get_renderoptions($quiz, $state) {
    // Show the question in readonly (review) mode if the quiz is in
    // the closed state
    $options->readonly = QUIZ_EVENTCLOSE === $state->event;

    // Show feedback once the question has been graded (if allowed by the quiz)
    $options->feedback = ('' !== $state->grade) && ($quiz->review & QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_IMMEDIATELY);

    // Show validation only after a validation event
    $options->validation = QUIZ_EVENTVALIDATE === $state->event;

    // Show correct responses in readonly mode if the quiz allows it
    $options->correct_responses = $options->readonly && ($quiz->review & QUIZ_REVIEW_ANSWERS & QUIZ_REVIEW_IMMEDIATELY);

    // Always show responses and scores
    $options->responses = true;
    $options->scores = ($quiz->review & QUIZ_REVIEW_SCORES & QUIZ_REVIEW_IMMEDIATELY) ? 1 : 0;

    return $options;
}


/**
* Determine review options
*/
function quiz_get_reviewoptions($quiz, $attempt, $isteacher=false) {
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
        $options->responses = ($quiz->review & QUIZ_REVIEW_IMMEDIATELY & QUIZ_REVIEW_RESPONSES) ? 1 : 0;
        $options->scores = ($quiz->review & QUIZ_REVIEW_IMMEDIATELY & QUIZ_REVIEW_SCORES) ? 1 : 0;
        $options->feedback = ($quiz->review & QUIZ_REVIEW_IMMEDIATELY & QUIZ_REVIEW_FEEDBACK) ? 1 : 0;
        $options->correct_responses = ($quiz->review & QUIZ_REVIEW_IMMEDIATELY & QUIZ_REVIEW_ANSWERS) ? 1 : 0;
        $options->solutions = ($quiz->review & QUIZ_REVIEW_IMMEDIATELY & QUIZ_REVIEW_SOLUTIONS) ? 1 : 0;
    } else if (time() < $quiz->timeclose) {
        $options->responses = ($quiz->review & QUIZ_REVIEW_OPEN & QUIZ_REVIEW_RESPONSES) ? 1 : 0;
        $options->scores = ($quiz->review & QUIZ_REVIEW_OPEN & QUIZ_REVIEW_SCORES) ? 1 : 0;
        $options->feedback = ($quiz->review & QUIZ_REVIEW_OPEN & QUIZ_REVIEW_FEEDBACK) ? 1 : 0;
        $options->correct_responses = ($quiz->review & QUIZ_REVIEW_OPEN & QUIZ_REVIEW_ANSWERS) ? 1 : 0;
        $options->solutions = ($quiz->review & QUIZ_REVIEW_OPEN & QUIZ_REVIEW_SOLUTIONS) ? 1 : 0;
    } else {
        $options->responses = ($quiz->review & QUIZ_REVIEW_CLOSED & QUIZ_REVIEW_RESPONSES) ? 1 : 0;
        $options->scores = ($quiz->review & QUIZ_REVIEW_CLOSED & QUIZ_REVIEW_SCORES) ? 1 : 0;
        $options->feedback = ($quiz->review & QUIZ_REVIEW_CLOSED & QUIZ_REVIEW_FEEDBACK) ? 1 : 0;
        $options->correct_responses = ($quiz->review & QUIZ_REVIEW_CLOSED & QUIZ_REVIEW_ANSWERS) ? 1 : 0;
        $options->solutions = ($quiz->review & QUIZ_REVIEW_CLOSED & QUIZ_REVIEW_SOLUTIONS) ? 1 : 0;
    }
    return $options;
}

/**
* Upgrade states for an attempt to Moodle 1.5 model
*
* Any state that does not yet have its timestamp set to nonzero has not yet been upgraded from Moodle 1.4
* The reason these are still around is that for large sites it would have taken too long to
* upgrade all states at once. This function sets the timestamp field and creates an entry in the
* quiz_newest_states table.
* @param object $attempt  The attempt whose states need upgrading
*/
function quiz_upgrade_states($attempt) {
    global $CFG;
    // The old quiz model only allowed a single response per quiz attempt so that there will be
    // only one state record per question for this attempt.

    // We set the timestamp of all states to the timemodified field of the attempt.
    execute_sql("UPDATE {$CFG->prefix}quiz_states SET timestamp = '$attempt->timemodified' WHERE attempt = '$attempt->id'", false);

    // For each state we create an entry in the quiz_newest_states table, with both newest and
    // newgraded pointing to this state.
    // Actually we only do this for states whose question is actually listed in $attempt->layout.
    // We do not do it for states associated to wrapped questions like for example the questions
    // used by a RANDOM question
    $newest->attemptid = $attempt->id;
    $questionlist = quiz_questions_in_quiz($attempt->layout);
    if ($states = get_records_select('quiz_states', "attempt = '$attempt->id' AND question IN ($questionlist)")) {
        foreach ($states as $state) {
            $newest->newgraded = $state->id;
            $newest->newest = $state->id;
            $newest->questionid = $state->question;
            insert_record('quiz_newest_states', $newest, false);
        }
    }
}

/**
 * Get list of available import or export formats
 * @param string $type 'import' if import list, otherwise export list assumed
 * @return array sorted list of import/export formats available
**/
function get_import_export_formats( $type ) {

    global $CFG;
    $fileformats = get_list_of_plugins("mod/quiz/format");

    $fileformatname=array();
    require_once( "format.php" );
    foreach ($fileformats as $key => $fileformat) {
        $format_file = $CFG->dirroot . "/mod/quiz/format/$fileformat/format.php";
        if (file_exists( $format_file ) ) {
            require_once( $format_file );
        }
        else {
            continue;
        }
        $classname = "quiz_format_$fileformat";
        $format_class = new $classname();
        if ($type=='import') {
            $provided = $format_class->provide_import();
        }
        else {
            $provided = $format_class->provide_export();
        }
        if ($provided) {
            $formatname = get_string($fileformat, 'quiz');
            if ($formatname == "[[$fileformat]]") {
                $formatname = $fileformat;  // Just use the raw folder name
            }
            $fileformatnames[$fileformat] = $formatname;
        }
    }
    natcasesort($fileformatnames);

    return $fileformatnames;
}
?>
