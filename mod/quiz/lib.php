<?php  // $Id$

/// Library of function for module quiz

/// CONSTANTS ///////////////////////////////////////////////////////////////////

define("GRADEHIGHEST", "1");
define("GRADEAVERAGE", "2");
define("ATTEMPTFIRST", "3");
define("ATTEMPTLAST",  "4");
$QUIZ_GRADE_METHOD = array ( GRADEHIGHEST => get_string("gradehighest", "quiz"),
                             GRADEAVERAGE => get_string("gradeaverage", "quiz"),
                             ATTEMPTFIRST => get_string("attemptfirst", "quiz"),
                             ATTEMPTLAST  => get_string("attemptlast", "quiz"));

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

$QUIZ_QUESTION_TYPE = array ( MULTICHOICE   => get_string("multichoice", "quiz"),
                              TRUEFALSE     => get_string("truefalse", "quiz"),
                              SHORTANSWER   => get_string("shortanswer", "quiz"),
                              NUMERICAL     => get_string("numerical", "quiz"),
                              CALCULATED    => get_string("calculated", "quiz"),
                              MATCH         => get_string("match", "quiz"),
                              DESCRIPTION   => get_string("description", "quiz"),
                              RANDOM        => get_string("random", "quiz"),
                              RANDOMSAMATCH => get_string("randomsamatch", "quiz"),
                              MULTIANSWER   => get_string("multianswer", "quiz")
                              );


define("QUIZ_PICTURE_MAX_HEIGHT", "600");   // Not currently implemented
define("QUIZ_PICTURE_MAX_WIDTH",  "600");   // Not currently implemented

define("QUIZ_MAX_NUMBER_ANSWERS", "10");

define("QUIZ_MAX_EVENT_LENGTH", "432000");   // 5 days maximum

$QUIZ_QTYPES= array();

/// QUIZ_QTYPES INITIATION //////////////////
class quiz_default_questiontype {

    function name() {
        return 'default';
    }

    function uses_quizfile($question, $relativefilepath) {
        // The default does only check whether the file is used as image:
        return $question->image == $relativefilepath;
    }

    function save_question_options($question) {
    /// Given some question info and some data about the the answers
    /// this function parses, organises and saves the question
    /// It is used by question.php through ->save_question when
    /// saving new data from a form, and also by import.php when
    /// importing questions
    ///
    /// If this is an update, and old answers already exist, then
    /// these are overwritten using an update().  To do this, it
    /// it is assumed that the IDs in quiz_answers are in the same
    /// sort order as the new answers being saved.  This should always
    /// be true, but it's something to keep in mind if fiddling with
    /// question.php
    ///
    /// Returns $result->error or $result->noticeyesno or $result->notice

        /// This default implementation must be overridden:    
        
        $result->error = "Unsupported question type ($question->qtype)!";
        return $result;
    }

    function save_question($question, $form, $course) {
        // This default implementation is suitable for most
        // question types.
        
        // First, save the basic question itself

        $question->name               = trim($form->name);
        $question->questiontext       = trim($form->questiontext);
        $question->questiontextformat = $form->questiontextformat;

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
    
        redirect("edit.php");
    }
    
    /// Convenience function that is used within the question types only
    function extract_response_id($responsekey) {
        if (ereg('[0-9]'.$this->name().'([0-9]+)', $responsekey, $regs)) {
            return $regs[1];
        } else {
            return false;
        }
    }

    function wrapped_questions($question) {
    /// Overridden only by question types, whose questions can
    /// wrap other questions. Two question types that do this
    /// are RANDOMSAMATCH and RANDOM
    
    /// If there are wrapped questions, than this method returns
    /// comma separated list of them...

        return false;
    }

    function convert_to_response_answer_field($questionresponse) {
    /// This function is very much the inverse of extract_response
    /// This function and extract_response, should be
    /// obsolete as soon as we get a better response storage
    /// Right now they are a bridge between a consistent
    /// response model and the old field answer in quiz_responses

    /// This is the default implemention...
        return implode(',', $questionresponse);
    }

    function get_answers($question) {
        // Returns the answers for the specified question

        // The default behaviour that signals that something is wrong
        return false;
    }

    function create_response($question, $nameprefix, $questionsinuse) {
        /// This rather smart solution works for most cases:
        $rawresponse->question = $question->id;
        $rawresponse->answer = '';
        return $this->extract_response($rawresponse, $nameprefix);
    }

    function extract_response($rawresponse, $nameprefix) {
    /// This function is very mcuh the inverse of convert_to_response_answer
    /// This function and convert_to_response_answer, should be
    /// obsolete as soon as we get a better response storage
    /// Right now they are a bridge between a consistent
    /// response model and the old field answer in quiz_responses

        /// Default behaviour that works for singlton response question types
        /// like SHORTANSWER, NUMERICAL and TRUEFALSE

        return array($nameprefix => $rawresponse->answer);
    }

    function print_question_number_and_grading_details
            ($number, $grade, $actualgrade=false, $recentlyadded=false) {

        /// Print question number and grade:

        echo '<p align="center"><b>' . $number . '</b></p>';
        if (false !== $grade) {
            $strmarks  = get_string("marks", "quiz");
            echo '<p align="center"><font size="1">';
            if (false !== $actualgrade) {
                echo "$strmarks: $actualgrade/$grade</font></p>";
            } else {
                echo "$grade $strmarks</font></p>";
            }
        }
        print_spacer(1,100);

        /// Print possible recently-added information:

        if ($recentlyadded) {
            echo '</td><td valign="top" align="right">';
            // Notify the user of this recently added question
            echo '<font color="red">';
            echo get_string('recentlyaddedquestion', 'quiz');
            echo '</font>';
            echo '</td></tr><tr><td></td><td valign="top">';

        } else { // The normal case
            echo '</td><td valign="top">';
        }
    }

    function print_question($currentnumber, $quiz, $question,
                            $readonly, $resultdetails) {
        /// Note that this method must return the number of the next
        /// question, making it possible not to increase the number when
        /// overriding this method (as for qtype=DESCRIPTION).

        echo '<table width="100%" cellspacing="10">';
        echo '<tr><td nowrap="nowrap" width="100" valign="top">';

        $this->print_question_number_and_grading_details
                ($currentnumber,
                 $quiz->grade ? $question->maxgrade : false,
                 empty($resultdetails) ? false : $resultdetails->grade,
                 isset($question->recentlyadded) ? $question->recentlyadded : false);
        
        $this->print_question_formulation_and_controls(
                $question, $quiz, $readonly,
                empty($resultdetails) ? false : $resultdetails->answers,
                empty($resultdetails) ? false : $resultdetails->correctanswers,
                quiz_qtype_nameprefix($question));

        echo "</td></tr></table>";
        return $currentnumber + 1;
    }

    function print_question_formulation_and_controls($question,
            $quiz, $readonly, $answers, $correctanswers, $nameprefix) {
        /// This default implementation must be overridden by all
        /// question type implemenations, unless the default
        /// implementation of print_question has been overridden...

        notify('Error: Question formulation and input controls has not'
               .'  been implemented for question type '.$this->name());
    }

    function grade_response($question, $nameprefix) {
    // Analyzes $question->response[] and determines the result
    // The result is to be returned in this structure:
    // ->grade          (The fraction of maxgrade awarded on the question)
    // ->answers        (result answer records)
    // ->correctanswers (potential answer records for best ->response[])

        error('grade_response has not been implemented for question type '
                .$this->name());
    }
}
 
quiz_load_questiontypes();
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

/// FUNCTIONS ///////////////////////////////////////////////////////////////////

function quiz_add_instance($quiz) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will create a new instance and return the id number
/// of the new instance.

    global $SESSION;

    unset($SESSION->modform);

    $quiz->created      = time();
    $quiz->timemodified = time();
    $quiz->timeopen = make_timestamp($quiz->openyear, $quiz->openmonth, $quiz->openday,
                                     $quiz->openhour, $quiz->openminute, 0);
    $quiz->timeclose = make_timestamp($quiz->closeyear, $quiz->closemonth, $quiz->closeday,
                                      $quiz->closehour, $quiz->closeminute, 0);

    if (!$quiz->id = insert_record("quiz", $quiz)) {
        return false;  // some error occurred
    }

    // The grades for every question in this quiz are stored in an array
    if ($quiz->grades) {
        foreach ($quiz->grades as $question => $grade) {
            if ($question) {
                unset($questiongrade);
                $questiongrade->quiz = $quiz->id;
                $questiongrade->question = $question;
                $questiongrade->grade = $grade;
                if (!insert_record("quiz_question_grades", $questiongrade)) {
                    return false;
                }
            }
        }
    }

    delete_records('event', 'modulename', 'quiz', 'instance', $quiz->id);  // Just in case

    $event = NULL;
    $event->name        = $quiz->name;
    $event->description = $quiz->intro;
    $event->courseid    = $quiz->course;
    $event->groupid     = 0;
    $event->userid      = 0;
    $event->modulename  = 'quiz';
    $event->instance    = $quiz->id;
    $event->eventtype   = 'open';
    $event->timestart   = $quiz->timeopen;
    $event->visible     = instance_is_visible('quiz', $quiz);
    $event->timeduration = ($quiz->timeclose - $quiz->timeopen);

    if ($event->timeduration > QUIZ_MAX_EVENT_LENGTH) {  /// Long durations create two events
        $event2 = $event;

        $event->name         .= ' ('.get_string('quizopens', 'quiz').')';
        $event->timeduration  = 0;

        $event2->timestart    = $quiz->timeclose;
        $event2->eventtype    = 'close';
        $event2->timeduration = 0;
        $event2->name        .= ' ('.get_string('quizcloses', 'quiz').')';

        add_event($event2);
    }

    add_event($event);

    return $quiz->id;
}


function quiz_update_instance($quiz) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will update an existing instance with new data.

    global $SESSION;

    unset($SESSION->modform);

    $quiz->timemodified = time();
    $quiz->timeopen = make_timestamp($quiz->openyear, $quiz->openmonth, $quiz->openday,
                                     $quiz->openhour, $quiz->openminute, 0);
    $quiz->timeclose = make_timestamp($quiz->closeyear, $quiz->closemonth, $quiz->closeday,
                                      $quiz->closehour, $quiz->closeminute, 0);
    $quiz->id = $quiz->instance;

    if (!update_record("quiz", $quiz)) {
        return false;  // some error occurred
    }


    // The grades for every question in this quiz are stored in an array
    // Insert or update records as appropriate

    $existing = get_records("quiz_question_grades", "quiz", $quiz->id, "", "question,grade,id");

    if ($quiz->grades) {
        foreach ($quiz->grades as $question => $grade) {
            if ($question) {
                unset($questiongrade);
                $questiongrade->quiz = $quiz->id;
                $questiongrade->question = $question;
                $questiongrade->grade = $grade;
                if (isset($existing[$question])) {
                    if ($existing[$question]->grade != $grade) {
                        $questiongrade->id = $existing[$question]->id;
                        if (!update_record("quiz_question_grades", $questiongrade)) {
                            return false;
                        }
                    }
                } else {
                    if (!insert_record("quiz_question_grades", $questiongrade)) {
                        return false;
                    }
                }
            }
        }
    }

    delete_records('event', 'modulename', 'quiz', 'instance', $quiz->id);  // Delete old and add new

    $event = NULL;
    $event->name        = $quiz->name;
    $event->description = $quiz->intro;
    $event->courseid    = $quiz->course;
    $event->groupid     = 0;
    $event->userid      = 0;
    $event->modulename  = 'quiz';
    $event->instance    = $quiz->id;
    $event->eventtype   = 'open';
    $event->timestart   = $quiz->timeopen;
    $event->visible     = instance_is_visible('quiz', $quiz);
    $event->timeduration = ($quiz->timeclose - $quiz->timeopen);

    if ($event->timeduration > QUIZ_MAX_EVENT_LENGTH) {  /// Long durations create two events
        $event2 = $event;

        $event->name         .= ' ('.get_string('quizopens', 'quiz').')';
        $event->timeduration  = 0;

        $event2->timestart    = $quiz->timeclose;
        $event2->eventtype    = 'close';
        $event2->timeduration = 0;
        $event2->name        .= ' ('.get_string('quizcloses', 'quiz').')';

        add_event($event2);
    }

    add_event($event);

    return true;
}


function quiz_delete_instance($id) {
/// Given an ID of an instance of this module,
/// this function will permanently delete the instance
/// and any data that depends on it.

    if (! $quiz = get_record("quiz", "id", "$id")) {
        return false;
    }

    $result = true;

    if ($attempts = get_records("quiz_attempts", "quiz", "$quiz->id")) {
        foreach ($attempts as $attempt) {
            if (! delete_records("quiz_responses", "attempt", "$attempt->id")) {
                $result = false;
            }
        }
    }

    if (! delete_records("quiz_attempts", "quiz", "$quiz->id")) {
        $result = false;
    }

    if (! delete_records("quiz_grades", "quiz", "$quiz->id")) {
        $result = false;
    }

    if (! delete_records("quiz_question_grades", "quiz", "$quiz->id")) {
        $result = false;
    }

    if (! delete_records("quiz", "id", "$quiz->id")) {
        $result = false;
    }

    if (! delete_records('event', 'modulename', 'quiz', 'instance', $quiz->id)) {
        $result = false;
    }

    return $result;
}

function quiz_delete_course($course) {
/// Given a course object, this function will clean up anything that
/// would be leftover after all the instances were deleted
/// In this case, all non-publish quiz categories and questions

    if ($categories = get_records_select("quiz_categories", "course = '$course->id' AND publish = '0'")) {
        foreach ($categories as $category) {
            if ($questions = get_records("quiz_questions", "category", $category->id)) {
                foreach ($questions as $question) {
                    delete_records("quiz_answers", "question", $question->id);
                    delete_records("quiz_match", "question", $question->id);
                    delete_records("quiz_match_sub", "question", $question->id);
                    delete_records("quiz_multianswers", "question", $question->id);
                    delete_records("quiz_multichoice", "question", $question->id);
                    delete_records("quiz_numerical", "question", $question->id);
                    delete_records("quiz_randommatch", "question", $question->id);
                    delete_records("quiz_responses", "question", $question->id);
                    delete_records("quiz_shortanswer", "question", $question->id);
                    delete_records("quiz_truefalse", "question", $question->id);
                }
                delete_records("quiz_questions", "category", $category->id);
            }
        }
        return delete_records("quiz_categories", "course", $course->id);
    }
    return true;
}


function quiz_user_outline($course, $user, $mod, $quiz) {
/// Return a small object with summary information about what a
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description
    if ($grade = get_record("quiz_grades", "userid", $user->id, "quiz", $quiz->id)) {

        if ($grade->grade) {
            $result->info = get_string("grade").": $grade->grade";
        }
        $result->time = $grade->timemodified;
        return $result;
    }
    return NULL;

    return $return;
}

function quiz_user_complete($course, $user, $mod, $quiz) {
/// Print a detailed representation of what a  user has done with
/// a given particular instance of this module, for user activity reports.

    return true;
}

function quiz_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such
/// as sending out mail, toggling flags etc ...

    global $CFG;

    return true;
}

function quiz_grades($quizid) {
/// Must return an array of grades, indexed by user, and a max grade.

    $quiz = get_record("quiz", "id", $quizid);
    if (empty($quiz) or empty($quiz->grade)) {
        return NULL;
    }

    $return->grades = get_records_menu("quiz_grades", "quiz", $quizid, "", "userid,grade");
    $return->maxgrade = get_field("quiz", "grade", "id", "$quizid");
    return $return;
}

function quiz_get_participants($quizid) {
/// Returns an array of users who have data in a given quiz
/// (users with records in quiz_attempts, students)

    global $CFG;

    return get_records_sql("SELECT DISTINCT u.*
                            FROM {$CFG->prefix}user u,
                                 {$CFG->prefix}quiz_attempts a
                            WHERE a.quiz = '$quizid' and
                                  u.id = a.userid");
}

function quiz_refresh_events($courseid = 0) {
// This standard function will check all instances of this module
// and make sure there are up-to-date events created for each of them.
// If courseid = 0, then every quiz event in the site is checked, else
// only quiz events belonging to the course specified are checked.
// This function is used, in its new format, by restore_refresh_events()

    if ($courseid == 0) {
        if (! $quizzes = get_records("quiz")) {
            return true;
        }
    } else {
        if (! $quizzes = get_records("quiz", "course", $courseid)) {
            return true;
        }
    }
    $moduleid = get_field('modules', 'id', 'name', 'quiz');

    foreach ($quizzes as $quiz) {
        $event = NULL;
        $event2 = NULL;
        $event2old = NULL;

        if ($events = get_records_select('event', "modulename = 'quiz' AND instance = '$quiz->id' ORDER BY timestart")) {
            $event = array_shift($events);
            if (!empty($events)) {
                $event2old = array_shift($events);
                if (!empty($events)) {
                    foreach ($events as $badevent) {
                        delete_records('event', 'id', $badevent->id);
                    }
                }
            }
        }

        $event->name        = addslashes($quiz->name);
        $event->description = addslashes($quiz->intro);
        $event->courseid    = $quiz->course;
        $event->groupid     = 0;
        $event->userid      = 0;
        $event->modulename  = 'quiz';
        $event->instance    = $quiz->id;
        $event->visible     = instance_is_visible('quiz', $quiz);
        $event->timestart   = $quiz->timeopen;
        $event->eventtype   = 'open';
        $event->timeduration = ($quiz->timeclose - $quiz->timeopen);

        if ($event->timeduration > QUIZ_MAX_EVENT_LENGTH) {  /// Set up two events

            $event2 = $event;

            $event->name         = addslashes($quiz->name).' ('.get_string('quizopens', 'quiz').')';
            $event->timeduration = 0;

            $event2->name        = addslashes($quiz->name).' ('.get_string('quizcloses', 'quiz').')';
            $event2->timestart   = $quiz->timeclose;
            $event2->eventtype   = 'close';
            $event2->timeduration = 0;

            if (empty($event2old->id)) {
                unset($event2->id);
                add_event($event2);
            } else {
                $event2->id = $event2old->id;
                update_event($event2);
            }
        } else if (!empty($event2->id)) {
            delete_event($event2->id);
        }

        if (empty($event->id)) {
            add_event($event);
        } else {
            update_event($event);
        }

    }
    return true;
}

/// SQL FUNCTIONS ////////////////////////////////////////////////////////////////////

function quiz_move_questions($category1, $category2) {
    global $CFG;
    return execute_sql("UPDATE {$CFG->prefix}quiz_questions
                           SET category = '$category2'
                         WHERE category = '$category1'",
                       false);
}

function quiz_get_question_grades($quizid, $questionlist) {
    global $CFG;

    return get_records_sql("SELECT question,grade
                            FROM {$CFG->prefix}quiz_question_grades
                            WHERE quiz = '$quizid'
                            AND question IN ($questionlist)");
}

function quiz_get_grade_records($quiz) {
/// Gets all info required to display the table of quiz results
/// for report.php
    global $CFG;

    return get_records_sql("SELECT qg.*, u.firstname, u.lastname, u.picture
                            FROM {$CFG->prefix}quiz_grades qg,
                                 {$CFG->prefix}user u
                            WHERE qg.quiz = '$quiz->id'
                              AND qg.userid = u.id");
}

function quiz_get_answers($question) {
// Given a question, returns the correct answers for a given question
    global $QUIZ_QTYPES;

    return $QUIZ_QTYPES[$question->qtype]->get_answers($question);
}

function quiz_get_attempt_questions($quiz, $attempt, $attempting = false) {
    /// Returns the questions of the quiz attempt at a format used for
    /// grading and printing them...
    /// On top of the ordinary persistent question fields,
    /// this function also set these properties

    /// ->response   -   contains names (as keys) and values (as values)
    ///                            for all question html-form inputs
    /// ->recentlyadded - true only if the question has been added to the quiz
    ///                   after the responses for the attempt were saved;
    ///                   false otherwise
    /// ->maxgrade   - the max grade the question has on the quiz if grades
    ///                 are used on the quiz; false otherwise

    global $QUIZ_QTYPES;
    global $CFG;

    /// Get the questions:
    if (!($questions =
            get_records_list('quiz_questions', 'id', $quiz->questions))) {
        notify('Error when reading questions from the database!');
        return false;
    }

    /// Retrieve ->maxgrade for all questions
    If (!($grades = quiz_get_question_grades($quiz->id, $quiz->questions))) {
        $grades = array();
    }

    /// Get any existing responses on this attempt:
    if (!($rawresponses = get_records_sql
            ("SELECT question, answer, attempt FROM {$CFG->prefix}quiz_responses
               WHERE attempt = '$attempt->id'
                 AND question IN ($quiz->questions)"))
            and $quiz->attemptonlast
                    // Try to get responses from the previous attempt:
            and $lastattemptnum = $attempt->attempt - 1) {
        do {
            $lastattempt = get_record('quiz_attempts',
                                      'quiz', $quiz->id,
                                      'userid', $attempt->userid,
                                      'attempt', $lastattemptnum);
        } while(empty($lastattempt) && --$lastattemptnum); 

        if (0 == $lastattemptnum or
                !($rawresponses = get_records_sql
                ("SELECT question, answer, attempt
                    FROM {$CFG->prefix}quiz_responses
                   WHERE attempt = '$lastattempt->id'
                     AND question IN ($quiz->questions)"))) {
            $rawresponses = array();
        } else {
            /// We found a last attempt that is now to be used:

            /// This line can be uncommented for debuging
            // echo "Last attempt is $lastattempt->id with number $lastattemptnum";
        }
    }

    /// Set the additional question properties
    /// response, recentlyadded and grade
    foreach ($questions as $qid => $question) {

        if (isset($grades[$qid])) {
            $questions[$qid]->maxgrade = $grades[$qid]->grade;
        } else {
            $questions[$qid]->maxgrade = 0.0;
        }

        if (isset($rawresponses[$qid])) {
            $questions[$qid]->response = $QUIZ_QTYPES[$question->qtype]
                    ->extract_response($rawresponses[$qid],
                                       quiz_qtype_nameprefix($question));
            $questions[$qid]->recentlyadded = false;
        } else {
            $questions[$qid]->response = array();
            $questions[$qid]->recentlyadded = !empty($rawresponses);
        }
    }
    
    if ($attempting) {
        /// Questions are requested for a test attempt that is
        /// about to start and there are no responses to reuse
        /// for current question, so we need to create new ones...
        
        /// For the case of wrapping question types that can
        /// wrap other arbitrary questions, there is a need
        /// to make sure that no question will appear twice
        /// in the quiz attempt:
        
        $questionsinuse = $quiz->questions;
        foreach ($questions as $question) {
            if ($wrapped = $QUIZ_QTYPES[$question->qtype]->wrapped_questions
                    ($question, quiz_qtype_nameprefix($question))) {
                $questionsinuse .= ",$wrapped";
            }
        }

        /// Make sure all the questions will have responses:
        foreach ($questions as $question) {
            if (empty($question->response)) {
                $nameprefix = quiz_qtype_nameprefix($question);
                $questions[$question->id]->response =
                        $QUIZ_QTYPES[$question->qtype]->create_response
                        ($question, $nameprefix, $questionsinuse);

                //////////////////////////////////////////////////
                // In the future, a nice feature could be to save
                // the created response right here, so that if a
                // student quits the quiz without saving, the
                // student will have the oppertunity to go back
                // to same quiz if he/she restarts the attempt.
                // Today, the student gets new RANDOM questions
                // whenever he/she restarts the quiz attempt.
                //////////////////////////////////////////////////
                // The above would also open the door for a new 
                // quiz feature that allows the student to save
                // all responses if he/she needs to switch computer
                // or have any other break in the middle of the quiz.
                // (Or simply because the student feels more secure
                // if he/she has the chance to save the responses
                // a number of times during the quiz.)
                //////////////////////////////////////////////////

                /// Catch any additional wrapped questions:
                if ($wrapped = $QUIZ_QTYPES[$question->qtype]
                        ->wrapped_questions($questions[$question->id],
                                            $nameprefix)) {
                    $questionsinuse .= ",$wrapped";
                }
            }
        }
    }

    return $questions;
}


function get_list_of_questions($questionlist) {
/// Returns an ordered list of questions, including course for each

    global $CFG;

    return get_records_sql("SELECT q.*,c.course
                              FROM {$CFG->prefix}quiz_questions q,
                                   {$CFG->prefix}quiz_categories c
                             WHERE q.id in ($questionlist)
                               AND q.category = c.id");
}

//////////////////////////////////////////////////////////////////////////////////////
/// Any other quiz functions go here.  Each of them must have a name that
/// starts with quiz_

function quiz_qtype_nameprefix($question, $prefixstart='question') {
    global $QUIZ_QTYPES;
    return $prefixstart.$question->id.$QUIZ_QTYPES[$question->qtype]->name();
}
function quiz_extract_posted_id($name, $nameprefix='question') {
    if (ereg("^$nameprefix([0-9]+)", $name, $regs)) {
        return $regs[1];
    } else {
        return false;
    }
}

function quiz_print_comment($text) {
    global $THEME;

    echo "<span class=\"feedbacktext\">".format_text($text, true, false)."</span>";
}

function quiz_print_correctanswer($text) {
    global $THEME;

    echo "<p align=\"right\"><span class=\"highlight\">$text</span></p>";
}

function quiz_print_question_icon($question, $editlink=true) {
// Prints a question icon

    global $QUIZ_QUESTION_TYPE;
    global $QUIZ_QTYPES;

    if ($editlink) {
        echo "<a href=\"question.php?id=$question->id\" title=\""
                .$QUIZ_QUESTION_TYPE[$question->qtype]."\">";
    }
    echo '<img border="0" height="16" width="16" src="questiontypes/';
    echo $QUIZ_QTYPES[$question->qtype]->name().'/icon.gif"/>';
    if ($editlink) {
        echo "</a>\n";
    }
}

function quiz_print_possible_question_image($quizid, $question) {
// Includes the question image if there is one

    global $CFG;

    if ($question->image) {
        echo '<img border="0" src="';

        if (substr(strtolower($question->image), 0, 7) == 'http://') {
            echo $question->image;

        } else if ($CFG->slasharguments) {        // Use this method if possible for better caching
            echo "$CFG->wwwroot/mod/quiz/quizfile.php/$quizid/$question->id/$question->image";

        } else {
            echo "$CFG->wwwroot/mod/quiz/quizfile.php?file=/$quizid/$question->id/$question->image";
        }
        echo '" />';

    }
}

function quiz_print_quiz_questions($quiz, $questions,
                                   $results=NULL, $shuffleorder=NULL) {
// Prints a whole quiz on one page.

    global $QUIZ_QTYPES;

    /// Check arguments

    if (empty($questions)) {
        notify("No questions have been defined!");
        return false;
    }

    if (!$shuffleorder) {
        if (!empty($quiz->shufflequestions)) {              // Mix everything up
            $questions = swapshuffle_assoc($questions);
        } else {
            $shuffleorder = explode(",", $quiz->questions);  // Use originally defined order
        }
    }

    if ($shuffleorder) { // Order has been defined, so reorder questions
        $oldquestions = $questions;
        $questions = array();
        foreach ($shuffleorder as $key) {
            $questions[] = $oldquestions[$key];      // This loses the index key, but doesn't matter
        }
    }

    $strconfirmattempt = addslashes(get_string("readytosend", "quiz"));

    if (empty($quiz->grade)) {
        $onsubmit = "";
    } else {
        $onsubmit = "onsubmit=\"return confirm('$strconfirmattempt');\"";
    }
    // BEGIN EDIT
    if($quiz->timelimit > 0) {
        ?>
        <script language="javascript" type="text/javascript">
        <!--
            document.write("<form method=\"post\" action=\"attempt.php\" <?php print(addslashes($onsubmit));?>>\n");
        // -->
        </script>
        <noscript>
        <center><p><strong><?php print_string("noscript","quiz"); ?></strong></p></center>
        </noscript>
        <?php
    } else {
    echo "<form method=\"post\" action=\"attempt.php\" $onsubmit>\n";
    }
    // END EDIT
    echo "<input type=\"hidden\" name=\"q\" value=\"$quiz->id\" />\n";

    // $count = 0;
    $nextquestionnumber = 1;
    $questionorder = array();

    // $readonly determines if it is an attempt or an review,
    // The condition used here is unfortunatelly somewhat confusing...
    $readonly = !empty($results) && !isset($results->attemptbuildsonthelast)
            ? ' readonly="readonly" ' : '';

    foreach ($questions as $question) {

        $questionorder[] = $question->id;

        if ($results && isset($results->details[$question->id])) {
            $details = $results->details[$question->id];
        } else {
            $details = false;
        }

        print_simple_box_start("center", "90%");
        $nextquestionnumber = $QUIZ_QTYPES[$question->qtype]->print_question
                ($nextquestionnumber, $quiz, $question, $readonly, $details);
        print_simple_box_end();
        echo "<br />";
    }

    if (empty($readonly)) {
        if (!empty($quiz->shufflequestions)) {  // Things have been mixed up, so pass the question order
            $shuffleorder = implode(',', $questionorder);
            echo "<input type=\"hidden\" name=\"shuffleorder\" value=\"$shuffleorder\" />\n";
        }
        if($quiz->timelimit > 0) {
            echo "<script language=\"javascript\" type=\"text/javascript\">\n";
            echo "<!--\n";
            echo "document.write('<center><input type=\"button\" value=\"".get_string("savemyanswers", "quiz")."\" onclick=\"return send_data();\" /></center>');\n";
            echo "// -->\n";
            echo "</script>\n";
            echo "<noscript>\n";
            echo "<center><strong>".get_string("noscript","quiz")."</strong></center>\n";
            echo "</noscript>\n";
        } else {
        echo "<center>\n<input type=\"submit\" value=\"".get_string("savemyanswers", "quiz")."\" />\n</center>";
    }
    }
    echo "</form>";

    return true;
}



function quiz_get_default_category($courseid) {
/// Returns the current category

    if ($categories = get_records("quiz_categories", "course", $courseid, "id")) {
        foreach ($categories as $category) {
            return $category;   // Return the first one (lowest id)
        }
    }

    // Otherwise, we need to make one
    $category->name = get_string("default", "quiz");
    $category->info = get_string("defaultinfo", "quiz");
    $category->course = $courseid;
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
    return get_records_select_menu("quiz_categories", "course='$courseid' $publish", "name ASC", "id,name");
}

function quiz_print_category_form($course, $current) {
// Prints a form to choose categories

    if (!$categories = get_records_select("quiz_categories", "course = '$course->id' OR publish = '1'", "name ASC")) {
        if (!$category = quiz_get_default_category($course->id)) {
            notify("Error creating a default category!");
            return false;
        }
        $categories[$category->id] = $category;
    }
    foreach ($categories as $key => $category) {
       if ($catcourse = get_record("course", "id", $category->course)) {
           if ($category->publish) {
               $category->name .= " ($catcourse->shortname)";
           }
           $catmenu[$category->id] = $category->name;
       }
    }
    $strcategory = get_string("category", "quiz");
    $strshow = get_string("show", "quiz");
    $streditcats = get_string("editcategories", "quiz");

    echo "<table width=\"100%\"><tr><td width=\"20\" nowrap=\"nowrap\">";
    echo "<b>$strcategory:</b>&nbsp;";
    echo "</td><td>";
    popup_form ("edit.php?cat=", $catmenu, "catmenu", $current, "choose", "", "", false, "self");
    echo "</td><td align=\"right\">";
    echo "<form method=\"get\" action=\"category.php\">";
    echo "<input type=\"hidden\" name=\"id\" value=\"$course->id\" />";
    echo "<input type=\"submit\" value=\"$streditcats\" />";
    echo "</form>";
    echo "</td></tr></table>";
}

function quiz_category_select_menu($courseid,$published=false,$only_editable=false,$selected="") {
/// displays a select menu of categories with appended coursenames
/// optionaly non editable categories may be excluded
/// added Howard Miller June '04
    // get sql fragment for published
    $publishsql="";
    if ($published) {
        $publishsql = "or publish=1";
    }
    $categories = get_records_select("quiz_categories","course=$courseid $publishsql");
    echo "<select name=\"category\">\n";
    foreach ($categories as $category) {
        $cid = $category->id;
        $cname = quiz_get_category_coursename( $category );
        $seltxt = "";
        if ($cid==$selected) {
            $seltxt = "selected=\"true\"";
        }
        if ((!$only_editable) || isteacheredit($category->course)) {
            echo "    <option value=\"$cid\" $seltxt>$cname</option>\n";
        }
    }
    echo "</select>\n";
}

function quiz_get_category_coursename($category) {
/// if the category is published, adds on the course
/// name
    $cname=$category->name;
    if ($category->publish) {
        if ($catcourse=get_record("course","id",$category->id)) {
            $cname .= " ($catcourse->shortname) ";
        }
    }
    return $cname;
}

function quiz_get_all_question_grades($questionlist, $quizid) {
// Given a list of question IDs, finds grades or invents them to
// create an array of matching grades

    if (empty($questionlist)) {
        return array();
    }

    $questions = quiz_get_question_grades($quizid, $questionlist);

    $list = explode(",", $questionlist);
    $grades = array();

    foreach ($list as $qid) {
        if (isset($questions[$qid])) {
            $grades[$qid] = $questions[$qid]->grade;
        } else {
            $grades[$qid] = 1;
        }
    }
    return $grades;
}

function quiz_gradesmenu_options($defaultgrade) {
// Especially for multianswer questions it is often
// desirable to have the grade of the question in a quiz
// larger than the earlier maximum of 10 points.
// This function makes quiz question list grade selector drop-down
// have the maximum grade option set to the highest value between 10
// and the defaultgrade of the question.

    if ($defaultgrade && $defaultgrade>10) {
        $maxgrade = $defaultgrade;
    } else {
        $maxgrade = 10;
    }

    unset($gradesmenu);
    for ($i=$maxgrade ; $i>=0 ; --$i) {
        $gradesmenu[$i] = $i;
    }
    return $gradesmenu;
}

function quiz_print_question_list($questionlist, $grades) {
// Prints a list of quiz questions in a small layout form with knobs
// $questionlist is comma-separated list
// $grades is an array of corresponding grades

    global $THEME;

    if (!$questionlist) {
        echo "<p align=\"center\">";
        print_string("noquestions", "quiz");
        echo "</p>";
        return;
    }

    $order = explode(",", $questionlist);

    if (!$questions = get_list_of_questions($questionlist)) {
        echo "<p align=\"center\">";
        print_string("noquestions", "quiz");
        echo "</p>";
        return;

    }

    $strorder = get_string("order");
    $strquestionname = get_string("questionname", "quiz");
    $strgrade = get_string("grade");
    $strdelete = get_string("delete");
    $stredit = get_string("edit");
    $strmoveup = get_string("moveup");
    $strmovedown = get_string("movedown");
    $strsavegrades = get_string("savegrades", "quiz");
    $strtype = get_string("type", "quiz");
    $strpreview = get_string("preview", "quiz");

    $count = 0;
    $sumgrade = 0;
    $total = count($order);
    echo "<form method=\"post\" action=\"edit.php\">";
    echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"2\" width=\"100%\">\n";
    echo "<tr><th width=\"*\" colspan=\"3\" nowrap=\"nowrap\">$strorder</th><th align=\"left\" width=\"100%\" nowrap=\"nowrap\">$strquestionname</th><th width=\"*\" nowrap=\"nowrap\">$strtype</th><th width=\"*\" nowrap=\"nowrap\">$strgrade</th><th width=\"*\" nowrap=\"nowrap\">$stredit</th></tr>\n";
    foreach ($order as $qnum) {
        if (empty($questions[$qnum])) {
            continue;
        }
        $question = $questions[$qnum];
        $canedit = isteacheredit($question->course);
        $count++;
        echo "<tr bgcolor=\"$THEME->cellcontent\">";
        echo "<td>$count</td>";
        echo "<td>";
        if ($count != 1) {
            echo "<a title=\"$strmoveup\" href=\"edit.php?up=$qnum\"><img
                 src=\"../../pix/t/up.gif\" border=\"0\"></a>";
        }
        echo "</td>";
        echo "<td>";
        if ($count != $total) {
            echo "<a title=\"$strmovedown\" href=\"edit.php?down=$qnum\"><img
                 src=\"../../pix/t/down.gif\" border=\"0\"></a>";
        }
        echo "</td>";
        echo "<td>$question->name</td>";
        echo "<td align=\"center\">";
        quiz_print_question_icon($question, $canedit);
        echo "</td>";
        echo "<td>";
        if ($question->qtype == DESCRIPTION) {
            echo "<input type=\"hidden\" name=\"q$qnum\" value=\"0\" /> \n";
        } else {
            choose_from_menu(quiz_gradesmenu_options($question->defaultgrade),
                             "q$qnum", (string)$grades[$qnum], "");
        }
        echo "<td>";
            echo "<a title=\"$strdelete\" href=\"edit.php?delete=$qnum\"><img
                 src=\"../../pix/t/delete.gif\" border=\"0\"></a>&nbsp;";
            echo "<a title=\"$strpreview\" href=\"#\" onClick=\"openpopup('/mod/quiz/preview.php?id=$qnum','$strpreview','scrollbars=yes,resizable=yes,width=700,height=480', false)\"><img
                  src=\"../../pix/i/search.gif\" border=\"0\"></a>&nbsp;";

            if ($canedit) {
                echo "<a title=\"$stredit\" href=\"question.php?id=$qnum\"><img
                     src=\"../../pix/t/edit.gif\" border=\"0\"></a>\n";
            }
        echo "</td>";

        $sumgrade += $grades[$qnum];
    }
    echo "<tr><td colspan=\"5\" align=\"right\">\n";
    echo "<input type=\"submit\" value=\"$strsavegrades:\" />\n";
    echo "<input type=\"hidden\" name=\"setgrades\" value=\"save\" />\n";
    echo "<td align=\"left\" bgcolor=\"$THEME->cellcontent\">\n";
    echo "<b>$sumgrade</b>";
    echo "</td><td>\n</td></tr>\n";
    echo "</table>\n";
    echo "</form>\n";

    return $sumgrade;
}


function quiz_print_cat_question_list($categoryid, $quizselected=true) {
// Prints a form to choose categories

    global $THEME, $QUIZ_QUESTION_TYPE;

    $strcategory = get_string("category", "quiz");
    $strquestion = get_string("question", "quiz");
    $straddquestions = get_string("addquestions", "quiz");
    $strimportquestions = get_string("importquestions", "quiz");
    $strexportquestions = get_string("exportquestions", "quiz");
    $strnoquestions = get_string("noquestions", "quiz");
    $strselect = get_string("select", "quiz");
    $strselectall = get_string("selectall", "quiz");
    $strcreatenewquestion = get_string("createnewquestion", "quiz");
    $strquestionname = get_string("questionname", "quiz");
    $strdelete = get_string("delete");
    $stredit = get_string("edit");
    $straddselectedtoquiz = get_string("addselectedtoquiz", "quiz");
    $strtype = get_string("type", "quiz");
    $strcreatemultiple = get_string("createmultiple", "quiz");
    $strpreview = get_string("preview","quiz");

    if (!$categoryid) {
        echo "<p align=\"center\"><b>";
        print_string("selectcategoryabove", "quiz");
        echo "</b></p>";
        if ($quizselected) {
            echo "<p>";
            print_string("addingquestions", "quiz");
            echo "</p>";
        }
        return;
    }

    if (!$category = get_record("quiz_categories", "id", "$categoryid")) {
        notify("Category not found!");
        return;
    }
    echo "<center>";
    echo format_text($category->info, FORMAT_MOODLE);

    echo '<table><tr>';

    // check if editing of this category is allowed
    if (isteacheredit($category->course)) {
        echo "<td valign=\"top\"><b>$strcreatenewquestion:</b></td>";
        echo '<td valign="top" align="right">';
        popup_form ("question.php?category=$category->id&qtype=", $QUIZ_QUESTION_TYPE, "addquestion",
                    "", "choose", "", "", false, "self");
        echo '<td width="10" valign="top" align="right">';
        helpbutton("questiontypes", $strcreatenewquestion, "quiz");
        echo '</td></tr>';

        echo '<tr><td colspan="3" align="right">';
        echo '<form method="get" action="import.php">';
        echo "<input type=\"hidden\" name=\"category\" value=\"$category->id\" />";
        echo "<input type=\"submit\" value=\"$strimportquestions\" />";
        helpbutton("import", $strimportquestions, "quiz");
        echo '</form>';
        echo '</td></tr>';
    }
    else {
        echo '<tr><td>';
        print_string("publishedit","quiz");
        echo '</td></tr>';
    }

    echo '<tr><td colspan="3" align="right">';
    echo '<form method="get" action="export.php">';
    echo "<input type=\"hidden\" name=\"category\" value=\"$category->id\" />";
    echo "<input type=\"submit\" value=\"$strexportquestions\" />";
    helpbutton("export", $strexportquestions, "quiz");
    echo '</form>';
    echo '</td></tr>';

    if (isteacheredit($category->course)) {
        echo '<tr><td colspan="3" align="right">';
        echo '<form method="get" action="multiple.php">';
        echo "<input type=\"hidden\" name=\"category\" value=\"$category->id\" />";
        echo "<input type=\"submit\" value=\"$strcreatemultiple\" />";
        helpbutton("createmultiple", $strcreatemultiple, "quiz");
        echo '</form>';
        echo '</td></tr>';
    }

    echo '</table>';

    echo '</center>';

    if (!$questions = get_records("quiz_questions", "category", $category->id, "qtype ASC")) {
        echo "<p align=\"center\">";
        print_string("noquestions", "quiz");
        echo "</p>";
        return;
    }

    $canedit = isteacheredit($category->course);

    echo "<form method=\"post\" action=\"edit.php\">";
    echo "<table border=\"0\" cellpadding=\"5\" cellspacing=\"2\" width=\"100%\">";
    echo "<tr>";
    if ($quizselected) {
        echo "<th width=\"*\" nowrap=\"nowrap\">$strselect</th>";
    }
    echo "<th width=\"100%\" align=\"left\" nowrap=\"nowrap\">$strquestionname</th><th width=\"*\" nowrap=\"nowrap\">$strtype</th>";
    if ($canedit) {
        echo "<th width=\"*\" nowrap=\"nowrap\">$stredit</th>";
    }
    echo "</tr>\n";
    foreach ($questions as $question) {
        echo "<tr bgcolor=\"$THEME->cellcontent\">\n";
        if ($quizselected) {
            echo "<td align=\"center\">";
            echo "<input type=\"checkbox\" name=\"q$question->id\" value=\"1\" />\n";
            echo "</td>";
        }
        echo "<td>".$question->name."</td>\n";
        echo "<td align=\"center\">\n";
        quiz_print_question_icon($question, $canedit);
        echo "</td>\n";
        if ($canedit) {
            echo "<td>\n";
                echo "<a title=\"$strdelete\" href=\"question.php?id=$question->id&delete=$question->id\">\n<img
                     src=\"../../pix/t/delete.gif\" border=\"0\"></a>&nbsp;";
                echo "<a title=\"$strpreview\" href=\"#\" onClick=\"openpopup('/mod/quiz/preview.php?id=$question->id','$strpreview','scrollbars=yes,resizable=yes,width=700,height=480', false)\"><img
                      src=\"../../pix/i/search.gif\" border=\"0\"></a>&nbsp;";
                echo "<a title=\"$stredit\" href=\"question.php?id=$question->id\"><img
                     src=\"../../pix/t/edit.gif\" border=\"0\"></a>";
            echo "</td>\n";// deleted </tr> jm
        }
        echo "</tr>\n";
    }
    if ($quizselected) {
        echo "<tr>\n<td colspan=\"3\">";
        echo "<input type=\"submit\" name=\"add\" value=\"<< $straddselectedtoquiz\" />\n";
        //echo "<input type=\"submit\" name=\"delete\" value=\"XX Delete selected\">";
        echo "<input type=\"button\" onclick=\"checkall()\" value=\"$strselectall\" />\n";
        echo "</td></tr>";
    }
    echo "</table>\n";
    echo "</form>\n";
}


function quiz_start_attempt($quizid, $userid, $numattempt) {
    $attempt->quiz = $quizid;
    $attempt->userid = $userid;
    $attempt->attempt = $numattempt;
    $attempt->timestart = time();
    $attempt->timefinish = 0;
    $attempt->timemodified = time();
    $attempt->id = insert_record("quiz_attempts", $attempt);
    return $attempt;
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


function quiz_get_user_attempts_string($quiz, $attempts, $bestgrade) {
/// Returns a simple little comma-separated list of all attempts,
/// with each grade linked to the feedback report and with the best grade highlighted

    $bestgrade = format_float($bestgrade);
    foreach ($attempts as $attempt) {
        $attemptgrade = format_float(($attempt->sumgrades / $quiz->sumgrades) * $quiz->grade);
        if ($attemptgrade == $bestgrade) {
            $userattempts[] = "<span class=\"highlight\"><a href=\"review.php?q=$quiz->id&attempt=$attempt->id\">$attemptgrade</a></span>";
        } else {
            $userattempts[] = "<a href=\"review.php?q=$quiz->id&attempt=$attempt->id\">$attemptgrade</a>";
        }
    }
    return implode(",", $userattempts);
}

function quiz_get_best_grade($quizid, $userid) {
/// Get the best current grade for a particular user in a quiz
    if (!$grade = get_record("quiz_grades", "quiz", $quizid, "userid", $userid)) {
        return "";
    }

    return (round($grade->grade,0));
}

function quiz_save_best_grade($quiz, $userid) {
/// Calculates the best grade out of all attempts at a quiz for a user,
/// and then saves that grade in the quiz_grades table.

    if (!$attempts = quiz_get_user_attempts($quiz->id, $userid)) {
        notify("Could not find any user attempts");
        return false;
    }

    $bestgrade = quiz_calculate_best_grade($quiz, $attempts);
    $bestgrade = (($bestgrade / $quiz->sumgrades) * $quiz->grade);

    if ($grade = get_record("quiz_grades", "quiz", $quiz->id, "userid", $userid)) {
        $grade->grade = round($bestgrade, 2);
        $grade->timemodified = time();
        if (!update_record("quiz_grades", $grade)) {
            notify("Could not update best grade");
            return false;
        }
    } else {
        $grade->quiz = $quiz->id;
        $grade->userid = $userid;
        $grade->grade = round($bestgrade, 2);
        $grade->timemodified = time();
        if (!insert_record("quiz_grades", $grade)) {
            notify("Could not insert new best grade");
            return false;
        }
    }
    return true;
}


function quiz_calculate_best_grade($quiz, $attempts) {
/// Calculate the best grade for a quiz given a number of attempts by a particular user.

    switch ($quiz->grademethod) {

        case ATTEMPTFIRST:
            foreach ($attempts as $attempt) {
                return $attempt->sumgrades;
            }
            break;

        case ATTEMPTLAST:
            foreach ($attempts as $attempt) {
                $final = $attempt->sumgrades;
            }
            return $final;

        case GRADEAVERAGE:
            $sum = 0;
            $count = 0;
            foreach ($attempts as $attempt) {
                $sum += $attempt->sumgrades;
                $count++;
            }
            return (float)$sum/$count;

        default:
        case GRADEHIGHEST:
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

        case ATTEMPTFIRST:
            foreach ($attempts as $attempt) {
                return $attempt;
            }
            break;

        case GRADEAVERAGE: // need to do something with it :-)
        case ATTEMPTLAST:
            foreach ($attempts as $attempt) {
                $final = $attempt;
            }
            return $final;

        default:
        case GRADEHIGHEST:
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


function quiz_save_attempt($quiz, $questions, $result, $attemptnum) {
/// Given a quiz, a list of attempted questions and a total grade
/// this function saves EVERYTHING so it can be reconstructed later
/// if necessary.

    global $USER;
    global $QUIZ_QTYPES;

    // First find the attempt in the database (start of attempt)

    if (!$attempt = quiz_get_user_attempt_unfinished($quiz->id, $USER->id)) {
        notify("Trying to save an attempt that was not started!");
        return false;
    }

    // Not usually necessary, but there's some sort of very rare glitch 
    // I've seen where the number wasn't already the same.  In these cases
    // We upgrade the database to match the attemptnum we calculated
    $attempt->attempt = $attemptnum;

    // Now let's complete this record and save it

    $attempt->sumgrades = $result->sumgrades;
    $attempt->timefinish = time();
    $attempt->timemodified = time();

    if (! update_record("quiz_attempts", $attempt)) {
        notify("Error while saving attempt");
        return false;
    }

    // Now let's save all the questions for this attempt

    foreach ($questions as $question) {
        $response->attempt = $attempt->id;
        $response->grade = $result->details[$question->id]->grade;
        $response->question = $question->id;

        if (!empty($question->response)) {
            $response->answer = $QUIZ_QTYPES[$question->qtype]
                    ->convert_to_response_answer_field($question->response);

            ///////////////////////////////////////////
            // WORKAROUND for question type RANDOM:
            ///////////////////////////////////////////
            if ($question->qtype == RANDOM and
                    ereg('^random([0-9]+)-(.*)$', $response->answer, $afields)) {
                $response->answer = $afields[1];
                if (!insert_record("quiz_responses", $response)) {
                    notify("Error while saving response");
                    return false;
                }
                $response->question = $response->answer;
                $response->answer = $afields[2];
            } ///   End of WORKAROUND //////////////////////

        } else {
            $response->answer = "";
        }
        if (!insert_record("quiz_responses", $response)) {
            notify("Error while saving response");
            return false;
        }
    }
    return $attempt;
}

function quiz_extract_correctanswers($answers, $nameprefix) {
/// Convinience function that is used by some single-response
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

function quiz_grade_responses($quiz, $questions) {
/// Given a list of questions (including ->response[] and ->maxgrade
/// on each question) this function does all the hard work of calculating the
/// score for each question, as well as a total grade for
/// for the whole quiz. It returns everything in a structure
/// that lookas like this
/// ->sumgrades     (sum of all grades for all questions)
/// ->grade         (final grade result for the whole quiz)
/// ->percentage    (Percentage of the max grade achieved)
/// ->details[]
/// The array ->details[] is indexed like the $questions argument
/// and contains scoring information per question. Each element has
/// this structure:
/// []->grade            (Grade awarded on the specifik question)
/// []->answers[]        (result answer records for the question response(s))
/// []->correctanswers[] (answer records if question response(s) had been correct)
/// The array ->answers[] is indexed like ->respoonse[] on its corresponding
/// element in $questions. It is the case for ->correctanswers[] when
/// there can be multiple responses per question but if there can be only one
/// response per question then all possible correctanswers will be
/// represented, indexed like the response index concatinated with the ->id
/// of its answer record.

    global $QUIZ_QTYPES;

    if (!$questions) {
        error("No questions!");
    }

    $result->sumgrades = 0.0;
    foreach ($questions as $qid => $question) {

        $resultdetails = $QUIZ_QTYPES[$question->qtype]->grade_response
                                ($question, quiz_qtype_nameprefix($question));

        // Negative grades will not do:
        if (((float)($resultdetails->grade)) <= 0.0) {
            $resultdetails->grade = 0.0;

        // Neither will extra credit:
        } else if (((float)($resultdetails->grade)) >= 1.0) {
            $resultdetails->grade = $question->maxgrade;
            
        } else {
            $resultdetails->grade *= $question->maxgrade;
        }

        // if time limit is enabled and exceeded, return zero grades
        if ($quiz->timelimit > 0) {
            if (($quiz->timelimit + 60) <= $quiz->timesincestart) {
                $resultdetails->grade = 0;
            }
        }

        $result->sumgrades += $resultdetails->grade;
        $resultdetails->grade = round($resultdetails->grade, 2);
        $result->details[$qid] = $resultdetails;
    }

    $fraction = (float)($result->sumgrades / $quiz->sumgrades);
    $result->percentage = format_float($fraction * 100.0);
    $result->grade      = format_float($fraction * $quiz->grade);
    $result->sumgrades = round($result->sumgrades, 2);

    return $result;
}

function quiz_get_recent_mod_activity(&$activities, &$index, $sincetime, $courseid, $quiz="0", $user="", $groupid="") {
// Returns all quizzes since a given time.  If quiz is specified then
// this restricts the results

    global $CFG;

    if ($quiz) {
        $quizselect = " AND cm.id = '$quiz'";
    } else {
        $quizselect = "";
    }
    if ($user) {
        $userselect = " AND u.id = '$user'";
    } else {
        $userselect = "";
    }

    $quizzes = get_records_sql("SELECT qa.*, q.name, u.firstname, u.lastname, u.picture,
                                       q.course, q.sumgrades as maxgrade, cm.instance, cm.section
                                  FROM {$CFG->prefix}quiz_attempts qa,
                                       {$CFG->prefix}quiz q,
                                       {$CFG->prefix}user u,
                                       {$CFG->prefix}course_modules cm
                                 WHERE qa.timefinish > '$sincetime'
                                   AND qa.userid = u.id $userselect
                                   AND qa.quiz = q.id $quizselect
                                   AND cm.instance = q.id
                                   AND cm.course = '$courseid'
                                   AND q.course = cm.course
                                 ORDER BY qa.timefinish ASC");

    if (empty($quizzes))
      return;

    foreach ($quizzes as $quiz) {
        if (empty($groupid) || ismember($groupid, $quiz->userid)) {

          $tmpactivity->type = "quiz";
          $tmpactivity->defaultindex = $index;
          $tmpactivity->instance = $quiz->quiz;

          $tmpactivity->name = $quiz->name;
          $tmpactivity->section = $quiz->section;

          $tmpactivity->content->attemptid = $quiz->id;
          $tmpactivity->content->sumgrades = $quiz->sumgrades;
          $tmpactivity->content->maxgrade = $quiz->maxgrade;
          $tmpactivity->content->attempt = $quiz->attempt;

          $tmpactivity->user->userid = $quiz->userid;
          $tmpactivity->user->fullname = fullname($quiz);
          $tmpactivity->user->picture = $quiz->picture;

          $tmpactivity->timestamp = $quiz->timefinish;

          $activities[] = $tmpactivity;

          $index++;
        }
    }

  return;
}


function quiz_print_recent_mod_activity($activity, $course, $detail=false) {
    global $CFG, $THEME;

    echo '<table border="0" cellpadding="3" cellspacing="0">';

    echo "<tr><td bgcolor=\"$THEME->cellcontent2\" class=\"forumpostpicture\" width=\"35\" valign=\"top\">";
    print_user_picture($activity->user->userid, $course, $activity->user->picture);
    echo "</td><td width=\"100%\"><font size=\"2\">";

    if ($detail) {
        echo "<img src=\"$CFG->modpixpath/$activity->type/icon.gif\" ".
             "height=\"16\" width=\"16\" alt=\"$activity->type\">  ";
        echo "<a href=\"$CFG->wwwroot/mod/quiz/view.php?id=" . $activity->instance . "\">"
             . $activity->name . "</a> - ";

    }

    if (isteacher($USER)) {
        $grades = "(" .  $activity->content->sumgrades . " / " . $activity->content->maxgrade . ") ";
        echo "<a href=\"$CFG->wwwroot/mod/quiz/review.php?q="
             . $activity->instance . "&attempt="
             . $activity->content->attemptid . "\">" . $grades . "</a> ";

        echo  get_string("attempt", "quiz") . " - " . $activity->content->attempt . "<br />";
    }
    echo "<a href=\"$CFG->wwwroot/user/view.php?id="
         . $activity->user->userid . "&course=$course\">"
         . $activity->user->fullname . "</a> ";

    echo " - " . userdate($activity->timestamp);

    echo "</font></td></tr>";
    echo "</table>";

    return;
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

// function to read all questions for category into big array
// added by Howard Miller June 2004
function get_questions_category( $category ) {

    // questions will be added to an array
    $qresults = array();

    // get the list of questions for the category
    $questions = get_records("quiz_questions","category",$category->id);

    // iterate through questions, getting stuff we need
    foreach($questions as $question) {
        $new_question = get_question_data( $question );
        $qresults[] = $new_question;
    }

    return $qresults;
}

// function to read single question, parameter is object view of
// quiz_categories record, results is a combined object
// defined as follows...
// ->id     quiz_questions id
// ->category   category
// ->name   q name
// ->questiontext
// ->image
// ->qtype  see defines at the top of this file
// ->stamp  not too sure
// ->version    not sure
// ----SHORTANSWER
// ->usecase
// ->answers    array of answers
// ----TRUEFALSE
// ->trueanswer truefalse answer
// ->falseanswer truefalse answer
// ----MULTICHOICE
// ->layout
// ->single many or just one correct answer
// ->answers    array of answer objects
// ----NUMERIC
// ->min	minimum answer span
// ->max	maximum answer span
// ->answer	single answer
// ----MATCH
// ->subquestions array of sub questions
// ---->questiontext
// ---->answertext
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
        default:
            notify("No handler for question type $question->qtype in get_question");
    }
    return $question;
}

// function to return single answer
// ->id     answer id
// ->question   question number
// ->answer
// ->fraction
// ->feedback
function get_exp_answer( $id ) {
    if (!$answer = get_record("quiz_answers","id",$id )) {
        error( "quiz_answers record $id not found" );
    }
    return $answer;
}

// function to return array of answers for export
function get_exp_answers( $question_num ) {
    if (!$answers = get_records("quiz_answers","question",$question_num)) {
        error( "quiz_answers question $question_num not found" );
    }
    return $answers;
}

?>
