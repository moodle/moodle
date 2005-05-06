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
* The different penalty schemes
*/
define('QUIZ_PENALTYNONE',     '0');
define('QUIZ_PENALTYMULTIPLY', '1');
define('QUIZ_PENALTYSUBTRACT', '2');
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

/**
* Array of question types names translated to the user's language
*
* The $QUIZ_QUESTION_TYPE array holds the names of all the question types that the user should
* be able to create directly. Some internal question types like random questions are excluded.
* The complete list of question types can be found in {@link $QUIZ_QTYPES}.
*/
// Note: Commented-out questiontypes are disabled, because they have not been
//       upgraded to the new code yet.
$QUIZ_QUESTION_TYPE = array ( MULTICHOICE   => get_string("multichoice", "quiz"),
                              TRUEFALSE     => get_string("truefalse", "quiz"),
                              SHORTANSWER   => get_string("shortanswer", "quiz"),
                              NUMERICAL     => get_string("numerical", "quiz"),
                              CALCULATED    => get_string("calculated", "quiz"),
                              MATCH         => get_string("match", "quiz"),
                              DESCRIPTION   => get_string("description", "quiz"),
                              RANDOMSAMATCH => get_string("randomsamatch", "quiz"),
                              MULTIANSWER   => get_string("multianswer", "quiz"),
                              RQP           => get_string("rqp", "quiz")
                              );


define("QUIZ_PICTURE_MAX_HEIGHT", "600");   // Not currently implemented
define("QUIZ_PICTURE_MAX_WIDTH",  "600");   // Not currently implemented

define("QUIZ_MAX_NUMBER_ANSWERS", "10");

define("QUIZ_CATEGORIES_SORTORDER", "999");

/**
* Array holding question type objects
*/
$QUIZ_QTYPES= array();

/// Objects used by the quiz module /////////

/**
* Holds run-time information about a question used in a particular quiz
*
* In addition to the data from the quiz_questions table a question object
* has extra variables for data from the quiz_question_instances table
* and further run-time information.
*/
class question extends object {

    /**
    * The question id number
    * @var integer int(10)
    */
    var $id;

    /**
    * The id of the question category in the quiz_categories table
    * @var integer int(10)
    */
    var $category;

    /**
    * The name given to the question by the teacher.
    * This name is not shown to the student
    * @var string varchar(255)
    */
    var $name;

    /**
    * The text of the question shown to the student
    * @var string text
    */
    var $questiontext;

    /**
    * The text format for the question text.
    * Formats are defined at the top of {@link weblib.php}
    * @var integer tinyint(2)
    */
    var $questiontextformat;

    /**
    * URL to the question image
    * @var string varchar(255)
    */
    var $image;

    /**
    * The default maximal grade for the question.
    * This can be changed by the teacher when putting the question
    * into a particular quiz to {@link maxgrade}
    * @var integer int(10)
    */
    var $defaultgrade;

    /**
    * The question type.
    * @var integer smallint(6)
    */
    var $qtype;

    /**
    * A globally unique identifier used for backup for example
    * @var string varchar(255)
    */
    var $stamp;

    /**
    * A version number.
    * This is increased each time a question is modified
    * Since the introduction of the quiz_question_versions table
    * this field is no longer of any use
    * @var int(10)
    */
    var $version;

    /**
    * A flag that determines whether the question should be shown
    * in the list of questions on the right hand side of the
    * quiz editing page.
    * Usually hidden questions are old versions of questions
    * that are kept around because there are already student responses
    * for them
    * @var integer int(1)
    */
    var $hidden;

}



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
    * Saves options set by the teacher for a question
    *
    * Given some question info and some data about the answers
    * this function parses, organises and saves the question
    * It is used by {@link question.php} through {@link save_question()} when
    * saving new data from a form, and also by {@link import.php} when
    * importing questions
    * @return object $result->error or $result->noticeyesno or $result->notice
    * @param object $question
    */
    function save_question_options($question) {
    /// If this is an update, and old answers already exist, then
    /// these are overwritten using an update().  To do this, it
    /// it is assumed that the IDs in quiz_answers are in the same
    /// sort order as the new answers being saved.  This should always
    /// be true, but it's something to keep in mind if fiddling with
    /// question.php

        /// This default implementation must be overridden:

        $result->error = "Unsupported question type ($question->qtype)!";
        return $result;
    }

    /**
    * Saves or updates a question after editing by a teacher
    *
    * This is used by {@link question.php} to save the data from the
    * question editing form.
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
    * Loads the question type specific options for the question.
    *
    * This function should load any question type specific options for the
    * question from the database into the question object. This information
    * should be contained in the $question->options field. A questiontype is
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
           notify('Error: Missing question answers!');
           return false;
        }
        return true;
    }

    /**
    * Returns the number of question numbers which are used by the question
    *
    * This function should return the number of question numbers to be assigned
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
    * specific session data (if any) and empty response data should be added to the
    * state object. Session data is any data which must persist throughout the
    * quiz attempt possibly with updates as the user interacts with the
    * question. This function should NOT create new entries in the database for
    * the session; a call to the {@link save_session_and_responses} member will
    * occur to do this.
    * @return bool            Indicates success or failure.
    * @param object $question The question for which the session is to be
    *                         created. Question type specific information is
    *                         included.
    * @param object $state    The state to create the session for. Note that
    *                         this will not have been saved in the database so
    *                         there will be no id. This object should be updated
    *                         to include the question type specific information
    *                         (it is passed by reference). In particular, empty
    *                         responses must be created in the ->responses
    *                         field.
    * @param object $quiz     The quiz for which the session is to be started.
    *                         Questions may wish to initialise the session in
    *                         different ways depending on quiz settings.
    * @param object $attempt  The quiz attempt for which the session is to be
    *                         started. Questions may wish to initialise the
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
    * This function should load any session data associated with the question
    * session in the given state into the state object. It should also load
    * the responses given (or generated) for the given state into the
    * ->responses member of the state object.
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
    * Saves the session data and responses for the question in the newly created
    * state
    *
    * This function should save the question type specific session data from the
    * state object. In particular for most question types it should also save the
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
    * This function is obsolete. It is only used during the database
    * upgrade
    */
    function extract_response($rawresponse, $nameprefix) {
    /// This function is obsolete. It is only used during the database
    /// upgrade to version 2005030100 to extract the responses from the
    /// legacy answers field. Question types written after this date do not
    /// need to implement this member.

        /// Returning a single value indicates that the value should remain
        /// stored in the legacy answer field in the quiz_states table (was
        /// quiz_responses).

        /// Question types which implement their own response storage with
        /// a question type specific table must return an associative array
        /// of responses (without the name prefix) and in this case the update
        /// script will call the save_session_and_responses member to save
        /// the responses using the new mechanism.

        /// Default behaviour that works for singleton response question types
        /// like SHORTANSWER, NUMERICAL and TRUEFALSE and legacy question types
        /// which have not changed their response storage model

        return $rawresponse->answer;
    }

    /**
    * Return a value or array of values which will give full marks if graded as
    * the $state->responses field
    *
    * The correct answer to the question, or an example of a correct answer if
    * there are many correct answers, is found and the value of the ->responses
    * member of the state object which corresponds to that answer is returned.
    * @return mixed           An array of values giving the responses corresponding
    *                         to the (or a) correct answer to the question. If the
    *                         question type overrides the {@link grade_responses}
    *                         member and does not wish to provide this information
    *                         null can be returned.
    * @param object $question The question for which the correct answer is to
    *                         be retrieved. Question type specific information is
    *                         available.
    * @param object $state    The state object that corresponds to the question,
    *                         for which a correct answer is needed. Question
    *                         type specific information is included.
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
    * Prints the question including the number, grading details, content,
    * feedback and interactions
    *
    * This function should print the question including the question number,
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
    *                         ->penalty. The currently responses are in
    *                         ->responses. This will be an associative array
    *                         (except in the case of no responses submitted when
    *                         this will be an empty string rather than an empty
    *                         array; this might occur when radio buttons are the
    *                         only interactions for a question and none are
    *                         selected for example). The last graded state is in
    *                         ->last_graded (hence the most recently graded
    *                         responses are in ->last_graded->responses). The
    *                         question type specific information is also
    *                         included.
    * @param integer $number  The number for this question. This is passed by
    *                         reference and should be increased by this method
    *                         to the number of the next question.
    * @param object $quiz     The quiz to which the question belongs. The
    *                         question will likely be rendered differently
    *                         depending on the quiz settings.
    * @param object $options  An object describing the rendering options.
    */
    function print_question(&$question, &$state, &$number, $quiz, $options) {
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
             'editquestion', $question->id, 450, 550, get_string('edit', 'quiz'));
            echo ')</font>';
        }
        if ($question->maxgrade and $options->scores) {
            echo '<div class="grade">';
            echo get_string('marks', 'quiz').':<br />&nbsp;';
            echo ('' === $state->last_graded->grade) ? '--' : format_float($state->last_graded->grade, $quiz->decimalpoints);
            echo '/'.$question->maxgrade.'</div>';
        }

        echo '</td><td valign="top">';

        $this->print_question_formulation_and_controls($question, $state,
         $quiz, $options);

        echo '</td></tr><tr><td valign="top">';

        if ($options->scores) {
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
                        $b.format_float($st->raw_grade, $quiz->decimalpoints).$be,
                        $b.format_float($st->penalty, $quiz->decimalpoints).$be,
                        $b.format_float($st->grade, $quiz->decimalpoints).$be
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
    * Summary of the student response
    *
    * This function returns a short string of no more than 80 characters that
    * summarizes the student's response
    * @return string
    * @param object $state
    */
    function response_summary($state) {
        // This should almost certainly be overridden
        return substr($state->answer, 0, 80);
    }


    /**
    * Prints the score obtained and maximum score available plus any penalty
    * information
    *
    * This function should print a summary of the scoring in the most recently
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
    * @param object $options  An object describing the rendering options. The
    *                         fields are:
    *                         ->readonly          Review / interactive mode
    *                         ->feedback          Show feedback
    *                         ->validation        Show how the response was
    *                                             interpreted
    *                         ->correct_responses Show solutions
    *                         These are all boolean values.
    */
    function print_question_grading_details(&$question, &$state, $quiz, $options) {
        /* The default implementation prints the number of marks if no attempt
        has been made. Otherwise it displays the grade obtained out of the
        maximum grade available and a warning if a penalty was applied for the
        attempt and displays the overall grade obtained counting all previous
        responses (and penalties) */

        if (!empty($question->maxgrade)) {
            echo '<div class="grading_details">';
            if (!('' === $state->last_graded->grade)) {
                // Display the grading details from the last graded state
                $grade->cur = format_float($state->last_graded->grade, $quiz->decimalpoints);
                $grade->max = $question->maxgrade;
                $grade->raw = format_float($state->last_graded->raw_grade, $quiz->decimalpoints);
                if (QUIZ_EVENTCLOSE == $state->event) {
                    /* No further attempts are possible so don't bother
                    displaying the penalty */
                    print_string('gradingdetailsnopenalty', 'quiz', $grade);
                } else if ('' !== $state->last_graded->penalty && ((float)
                 $state->last_graded->penalty) > 0.0) {
                    // A penalty was applied so display it
                    $grade->penalty = $state->last_graded->penalty;
                    print_string('gradingdetailspenalty', 'quiz', $grade);
                } else if ($state->last_graded->raw_grade >=
                 $question->maxgrade) {
                    /* No penalty was applied because the response was
                    correct so don't bother noting that no penalty was
                    applied for the attempt */
                    print_string('gradingdetailsnopenalty', 'quiz', $grade);
                } else {
                    /* No penalty was applied even though the answer was
                    not correct (eg. a syntax error) so tell the student
                    that they were not penalised for the attempt */
                    print_string('gradingdetailszeropenalty', 'quiz', $grade);
                }
            }
            echo '</div>';
        }
    }

    /**
    * Prints the main content of the question including any interactions
    *
    * This function should print the main content of the question including the
    * interactions for the question in the state given (unless the readonly
    * option is set). The last graded responses should be printed or indicated
    * and (except when the readonly option is set) the current responses should
    * be selected or filled in. Any names (eg. for any form elements) should be
    * prefixed with the unique prefix for the question in
    * $question->name_prefix. This method is called from the print_question
    * method by default; the question type may override print_question so that
    * this method is not used.
    * @param object $question The question to be rendered. Question type
    *                         specific information is included. The name
    *                         prefix for any named elements is in ->name_prefix.
    * @param object $state    The state to render the question in. The grading
    *                         information is in ->grade, ->raw_grade and
    *                         ->penalty. The current responses are in
    *                         ->responses. This will be an associative array
    *                         (except in the case of no responses submitted when
    *                         this will be an empty string rather than an empty
    *                         array; this might occur when radio buttons are the
    *                         only interactions for a question and none are
    *                         selected for example). The last graded state is in
    *                         ->last_graded (hence the most recently graded
    *                         responses are in ->last_graded->responses). The
    *                         question type specific information is in $state->options.
    * @param object $quiz     The quiz to which the question belongs. The
    *                         question will likely be rendered differently
    *                         depending on the quiz settings.
    * @param object $options  An object describing the rendering options.
    *                         The fields are:
    *                         ->readonly          Review / interactive mode
    *                         ->feedback          Show feedback for the graded
    *                                             responses
    *                         ->validation        Show how the current responses
    *                                             responses were interpreted
    *                         ->correct_responses Show solutions
    *                         These are all boolean values.
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
    * This function should print the submit button(s) for the question in the
    * given state. The name of any button created should be prefixed with the
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
    *                         choice of buttons will likely depend on the quiz
    *                         settings.
    * @param object $options  An object describing the rendering options. The
    *                         fields are:
    *                         ->readonly          Review / interactive mode
    *                         ->feedback          Show feedback
    *                         ->validation        Show how the response was
    *                                             interpreted
    *                         ->correct_responses Show solutions
    *                         These are all boolean values.
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
    * @param object $state     The state of the question. The grading
    *                          information is in ->grade, ->raw_grade and
    *                          ->penalty. The currently responses are in
    *                          ->responses. For legacy question types with only
    *                          one response and use only the name prefix for the
    *                          name of the interaction this will be a single
    *                          value. Otherwise it will be an associative array
    *                          (except in the case of no responses submitted
    *                          when this will be an empty string rather than an
    *                          empty array; this might occur when radio buttons
    *                          are the only interactions for a question and none
    *                          are selected for example). The last graded state
    *                          is in ->last_graded (hence the most recently
    *                          graded responses are in
    *                          ->last_graded->responses). The question type
    *                          specific information is also included.
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
    * This function should perform response processing and grading and update
    * the state accordingly.
    * @return boolean         Indicates success or failure.
    * @param object $question The question to be graded. Question type
    *                         specific information is included.
    * @param object $state    The state of the question to grade. The grading
    *                         information is in ->grade, ->raw_grade and
    *                         ->penalty. The currently responses are in
    *                         ->responses. It will be an associative array
    *                         (except in the case of no responses submitted when
    *                         this will be an empty string rather than an empty
    *                         array; this might occur when radio buttons are the
    *                         only interactions for a question and none are
    *                         selected for example). The last graded state is in
    *                         ->last_graded (hence the most recently graded
    *                         responses are in ->last_graded->responses). The
    *                         question type specific information is also
    *                         included. The ->raw_grade and ->penalty fields
    *                         must be updated. The ->grade field is computed
    *                         automatically. The cumulative penalty must be set
    *                         in ->penalty by adding to the penalty from the
    *                         most recently graded state. The method is able to
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
        $state->penalty = 0;
        // Only allow one attempt at the question
        $state->event = QUIZ_EVENTCLOSE;
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
    * The  dataset dependent question-type, which is extended by the calculated
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
            echo '<input type="submit" name="makecopy" '.$submitscript.' value="'.get_string("makecopy", "quiz").'" /> ';
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
* @todo Allow new attempt to be based on previous attempt
* @todo Allow question shuffling
* @return object                The newly created attempt object.
* @param object $quiz           The quiz to create an attempt for.
* @param integer $attemptnumber The sequence number for the attempt.
*/
function quiz_create_attempt($quiz, $attemptnumber) {
    global $USER;

    $timenow = time();
    $attempt->quiz = $quiz->id;
    $attempt->userid = $USER->id;
    $attempt->attempt = $attemptnumber;
    $attempt->sumgrades = 0.0;
    $attempt->preview = 0;
    $attempt->timestart = $timenow;
    $attempt->timefinish = 0;
    $attempt->timemodified = $timenow;
    if ($quiz->shufflequestions) {
        $attempt->layout = quiz_repaginate($quiz->questions, $quiz->questionsperpage, true);
    } else {
        $attempt->layout = $quiz->questions;
    }
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
    $statefields = 'n.questionid as question, s.id, s.attempt, s.originalquestion, s.seq_number,'.
                   ' s.answer, s.event, s.grade, s.raw_grade, s.penalty, n.sumpenalty';
    // Load the newest states for the questions
    $sql = "SELECT $statefields".
           "  FROM {$CFG->prefix}quiz_states s,".
           "       {$CFG->prefix}quiz_newest_states n".
           " WHERE s.id = n.new".
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
                error('No graded state could be found!');
            }
        } else {
            // Create an empty state object
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

    return $states;
}


/**
* Creates the run-time fields for the states
*
* Extends the state objects for a question by calling
* {@link restore_session_and_responses()} or it creates a new one by
* calling {@link create_session_and_responses()}
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
    // ->seq_number; it can do this by calling
    // quiz_mark_session_change. The quiz_save_question_session
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
* to the answer field of the database table.
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

    // Save the state
    unset($state->id);
    if (!$state->id = insert_record('quiz_states', $state)) {
        unset($state->id);
        unset($state->answer);
        return false;
    }
    unset($state->answer);

    // this is the most recent state
    if (!record_exists('quiz_newest_states', 'attemptid',
     $state->attempt, 'questionid', $question->id)) {
        $new->attemptid = $state->attempt;
        $new->questionid = $question->id;
        $new->new = $state->id;
        $new->newgraded = $state->id;
        $new->sumpenalty = '0.0';
        if (!insert_record('quiz_newest_states', $new)) {
            error('Could not insert entry in quiz_newest_states');
        }
    } else {
        set_field('quiz_newest_states', 'new', $state->id, 'attemptid',
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
            $actions[$quid]->responses[$key] = $response;
        }
    }
    return $actions;
}


/**
* For a given question instance we walk the complete history of states for
* each user and recalculate the grades as we go along.
*
* This is used when a question in an existing quiz is changed and old student
* responses need to be marked with the new version of a question.
*
* TODO: Finish documenting this
* @return boolean            Indicates success/failure
* @param object $question    A question object
* @param array $quizlist     An array of quiz ids, in which the question should
*                            be regraded. If quizlist is the empty array, all
*                            quizzes are affected.
*/
function quiz_regrade_question_in_quizzes($question, $quizlist) {

    $quizlist = implode(',', $quizlist);
    if (empty($quizlist)) { // assume that all quizzes are affected
        if (! $instances = get_records('quiz_question_instances',
         'question', $question->id)) {
            // No instances were found, so it successfully regraded all of them
            return true;
        }
        $quizlist = implode(',', array_map(create_function('$val',
         'return $val->quiz;'), $instances));
        unset($instances);
    }

    // Get all affected quizzes
    if (! $quizzes = get_records_list('quiz', 'id', $quizlist)) {
        error('Couldn\'t get quizzes for regrading!');
    }

    foreach ($quizzes as $quiz) {
        // All the attempts that need to be changed
        if (! $attempts = get_records('quiz_attempts', 'quiz', $quiz->id)) {
            error("Couldn't get question instance for regrading!");
        }
        $attempts = array_values($attempts);
        if (! $instance = get_record('quiz_question_instances',
             'quiz', $quiz->id, 'question', $question->id)) {
                error("Couldn't get question instance for regrading!");
        }
        $question->maxgrade = $instance->grade;
        for ($i = 0; $i < count($attempts); $i++) {
            if ($states = get_records_select('quiz_states',
             "attempt = '{$attempts[$i]->id}' ".
             "AND question = '{$question->id}'",
             'seq_number ASC')) {
                $states = array_values($states);

                $attempts[$i]->sumgrades -= $states[count($states)-1]->grade;

                // Initialise the replaystate
                quiz_restore_state($question, $states[0]);
                $replaystate = clone($states[0]);
                $replaystate->last_graded = clone($states[0]);
                for($j = 1; $j < count($states); $j++) {
                    quiz_restore_state($question, $states[$j]);
                    $action = new stdClass;
                    $action->responses = $states[$j]->responses;
                    // Close the last state of a finished attempt
                    if (((count($states) - 1) === $j) && ($attempts[$i]->timefinish > 0)) {
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
                    quiz_process_responses($question, $replaystate, $action, $quiz,
                     $attempts[$i]);
                    $replaystate->id = $states[$j]->id;
                    update_record('quiz_states', $replaystate);
                }
                update_record('quiz_attempts', $attempts[$i]);
                quiz_save_best_grade($quiz, $attempts[$i]->userid);
            }
        }
    }
}

/**
* Processes an array of student responses, grading and saving them as appropriate
*
* @return boolean         Indicates success/failure
* @param object $question Full question object, passed by reference
* @param object $state    Full state object, passed by reference
* @param object $action   object with the fields ->responses which
*                         is an array holding the student responses and
*                         ->action which specifies the action, e.g., QUIZ_EVENTGRADE
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
    if (QUIZ_EVENTGRADE == $action->event || QUIZ_EVENTCLOSE == $action->event) {
        $state->responses = $state->last_graded->responses;
    }
    // Check for unchanged responses (exactly unchanged, not equivalent).
    // We also have to catch questions that the student has not yet attempted
    $sameresponses = (($state->responses == $action->responses) or
     ($state->responses == array(''=>'') && array_keys(array_count_values($action->responses))===array('')));

    if ($sameresponses && isset($action->event) and QUIZ_EVENTCLOSE != $action->event
     and QUIZ_EVENTVALIDATE != $action->event) {
        return true;
    }

    // Roll back grading information to last graded state and set the new
    // responses
    $newstate = clone($state->last_graded);
    $newstate->responses = $action->responses;
    $newstate->seq_number = $state->seq_number;
    $newstate->changed = false;
    $newstate->last_graded = $state->last_graded;
    $state = $newstate;

    // Set the event to the action we will perform. The question type specific
    // grading code may override this by setting it to QUIZ_EVENTCLOSE if the
    // attempt at the question causes the session to close
    $state->event = $action->event;

    if (QUIZ_EVENTSAVE == $action->event || QUIZ_EVENTVALIDATE == $action->event) {
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
                quiz_apply_penalty($question, $state, $quiz);
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
            quiz_apply_penalty($question, $state, $quiz);
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
    quiz_mark_session_change($state);
    $attempt->timemodified = time();
    return true;
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
* Applies the penalty for the previous attempts to the raw grade for the current
* attempt
*
* The grade for the question in the current state is computed by applying the
* penalty accumulated over the previous marked attempts at the question to the
* raw grade using the penalty scheme in use in the quiz. The ->grade field of
* the state object is modified to reflect the new grade.
* @param object $question The question for which the penalty is to be applied.
* @param object $state    The state for which the grade is to be set from the
*                         raw grade and the cumulative penalty from the last
*                         graded state. The ->grade field is updated by applying
*                         the penalty scheme for the quiz to the ->raw_grade and
*                         ->last_graded->penalty fields.
* @param object $quiz     The quiz to which the question belongs. The penalty
*                         scheme to apply is given by the ->penaltyscheme field.
*/
function quiz_apply_penalty(&$question, &$state, $quiz) {
    switch ($quiz->penaltyscheme) {
        case QUIZ_PENALTYMULTIPLY:
            $state->grade = (1 - $state->sumpenalty) * $state->raw_grade;
            $state->sumpenalty += $state->penalty * (1-$state->sumpenalty);
            break;
        case QUIZ_PENALTYSUBTRACT:
            $state->grade = $state->raw_grade - ($question->maxgrade * $state->sumpenalty);
            $state->sumpenalty += (float) $state->penalty;
            break;
        case QUIZ_PENALTYNONE:
        default:
            $state->grade = $state->raw_grade;
            break;
    }
    // Ensure that the grade does not go down
    $state->grade = max($state->grade, $state->last_graded->grade);
}


function quiz_print_comment($text) {
    echo "<span class=\"feedbacktext\">".format_text($text, true, false)."</span>";
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
*/
function quiz_print_question_icon($question, $editlink=true) {
// Prints a question icon

    global $QUIZ_QUESTION_TYPE;
    global $QUIZ_QTYPES;

    if ($editlink) {
        echo "<a href=\"question.php?id=$question->id\" title=\""
                .$QUIZ_QTYPES[$question->qtype]->name()."\">";
    }
    echo '<img border="0" height="16" width="16" src="questiontypes/';
    echo $QUIZ_QTYPES[$question->qtype]->name().'/icon.gif" alt="';
    echo get_string($QUIZ_QTYPES[$question->qtype]->name(), 'quiz').'" />';
    if ($editlink) {
        echo "</a>\n";
    }
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
    $start = strpos($quizlayout, $pagelayout)-3;
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

    if (!isadmin()) {
        $categories = get_records_select("quiz_categories","course=$courseid $publishsql", 'parent, sortorder, name ASC');
    } else {
        $categories = get_records_select("quiz_categories", '', 'parent, sortorder, name ASC');
    }

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

    return (format_float($grade->grade,$quiz->decimalpoints));
}

/**
* TODO: document this
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

    // Save the best grade in the database
    if ($grade = get_record('quiz_grades', 'quiz', $quiz->id, 'userid',
     $userid)) {
        $grade->grade = round($bestgrade, $quiz->decimalpoints);
        $grade->timemodified = time();
        if (!update_record('quiz_grades', $grade)) {
            notify('Could not update best grade');
            return false;
        }
    } else {
        $grade->quiz = $quiz->id;
        $grade->userid = $userid;
        $grade->grade = round($bestgrade, $quiz->decimalpoints);
        $grade->timemodified = time();
        if (!insert_record('quiz_grades', $grade)) {
            notify('Could not insert new best grade');
            return false;
        }
    }
    return true;
}


function quiz_calculate_best_grade($quiz, $attempts) {
/// Calculate the best grade for a quiz given a number of attempts by a particular user.

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


function quiz_calculate_best_attempt($quiz, $attempts) {
/// Return the attempt with the best grade for a quiz

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
* @author added by Howard Miller June 2004
*/
function get_questions_category( $category ) {

    // questions will be added to an array
    $qresults = array();

    // get the list of questions for the category
    if ($questions = get_records("quiz_questions","category",$category->id)) {

        // iterate through questions, getting stuff we need
        foreach($questions as $question) {
            $new_question = get_question_data( $question );
            $qresults[] = $new_question;
        }
    }

    return $qresults;
}

/**
* Get question data for export
*
* @todo This really needs to be handled by the question types rather
*       than by the switch statement below.
* @author presumably Howard Miller
* function to read single question, parameter is object view of
* quiz_categories record, results is a combined object
* defined as follows...
* ->id     quiz_questions id
* ->category   category
* ->name   q name
* ->questiontext
* ->image
* ->qtype  see defines at the top of this file
* ->stamp  not too sure
* ->version    not sure
* ----SHORTANSWER
* ->usecase
* ->answers    array of answers
* ----TRUEFALSE
* ->trueanswer truefalse answer
* ->falseanswer truefalse answer
* ----MULTICHOICE
* ->layout
* ->single many or just one correct answer
* ->answers    array of answer objects
* ----NUMERIC
* ->min  minimum answer span
* ->max  maximum answer span
* ->answer single answer
* ----MATCH
* ->subquestions array of sub questions
* ---->questiontext
* ---->answertext
function get_question_data( $question ) {
    // what to do next depends of question type (qtype)
    switch ($question->qtype)  {
        case SHORTANSWER:
            $shortanswer = get_record("quiz_shortanswer","question",$question->id);
            $question->usecase = $shortanswer->usecase;
            $question->answers = get_exp_answers( $question->id );
            break;
        case TRUEFALSE:
            if (!$truefalse = get_record("quiz_truefalse","question",$question->id)) {
                error( "quiz_truefalse record $question->id not found" );
            }
            $question->trueanswer = get_exp_answer( $truefalse->trueanswer );
            $question->falseanswer = get_exp_answer( $truefalse->falseanswer );
            break;
        case MULTICHOICE:
            if (!$multichoice = get_record("quiz_multichoice","question",$question->id)) {
                error( "quiz_multichoice $question->id not found" );
            }
            $question->layout = $multichoice->layout;
            $question->single = $multichoice->single;
            $question->answers = get_exp_answers( $multichoice->question );
            break;
        case NUMERICAL:
            if (!$numeric = get_record("quiz_numerical","question",$question->id)) {
                error( "quiz_numerical $question->id not found" );
            }
            $question->min = $numeric->min;
            $question->max = $numeric->max;
            $question->answer = get_exp_answer( $numeric->answer );
            break;
        case MATCH:
            if (!$subquestions = get_records("quiz_match_sub","question",$question->id)) {
                error( "quiz_match_sub $question->id not found" );
            }
            $question->subquestions = $subquestions;
            break;
        case DESCRIPTION:
            // nothing to do
            break;
        case MULTIANSWER:
            // nothing to do
            break;
        case RANDOM:
            // nothing to do
            break;
        default:
            notify("No handler for question type $question->qtype in get_question");
    }
    return $question;
}

/**
* function to return single record from quiz_answers table
*/
function get_exp_answer( $id ) {
    if (!$answer = get_record("quiz_answers","id",$id )) {
        error( "quiz_answers record $id not found" );
    }
    return $answer;
}

/**
* Function to return array of answers for export
*/
function get_exp_answers( $question_num ) {
    if (!$answers = get_records("quiz_answers","question",$question_num)) {
        error( "quiz_answers question $question_num not found" );
    }
    return $answers;
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
    $options->scores = true;

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
        $options->correct_responses = ($quiz->review & QUIZ_REVIEW_IMMEDIATELY & QUIZ_REVIEW_ANSWERS) ? 1 : 0;
        $options->solutions = ($quiz->review & QUIZ_REVIEW_OPEN & QUIZ_REVIEW_SOLUTIONS) ? 1 : 0;
    } else {
        $options->responses = ($quiz->review & QUIZ_REVIEW_CLOSED & QUIZ_REVIEW_RESPONSES) ? 1 : 0;
        $options->scores = ($quiz->review & QUIZ_REVIEW_CLOSED & QUIZ_REVIEW_SCORES) ? 1 : 0;
        $options->feedback = ($quiz->review & QUIZ_REVIEW_CLOSED & QUIZ_REVIEW_FEEDBACK) ? 1 : 0;
        $options->correct_responses = ($quiz->review & QUIZ_REVIEW_IMMEDIATELY & QUIZ_REVIEW_ANSWERS) ? 1 : 0;
        $options->solutions = ($quiz->review & QUIZ_REVIEW_CLOSED & QUIZ_REVIEW_SOLUTIONS) ? 1 : 0;
    }
    return $options;
}

/**
* Upgrade states for an attempt to Moodle 1.5 model
*
* @param object $attempt  The attempt whose states need upgrading
*/
function quiz_upgrade_states($attempt) {
    global $CFG;
    execute_sql("UPDATE {$CFG->prefix}quiz_states SET timestamp = '$attempt->timemodified' WHERE attempt = '$attempt->id'", false);
    $newest->attemptid = $attempt->id;
    if ($states = get_records('quiz_states', 'attempt', $attempt->id)) {
        foreach ($states as $state) {
            $newest->newgraded = $state->id;
            $newest->new = $state->id;
            $newest->questionid = $state->question;
            insert_record('quiz_newest_states', $newest, false);
        }
    }
}

/**
 * Get list of available import or export formats
**/
function get_import_export_formats( $type ) {

  global $CFG;
  $fileformats = get_list_of_plugins("mod/quiz/format");

  $fileformatname=array();
  include_once( "format.php" );
  foreach ($fileformats as $key => $fileformat) {
    require_once( $CFG->dirroot."/mod/quiz/format/$fileformat/format.php" );     
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
