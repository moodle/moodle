<?php  // $Id$
/**
 * Library of functions used by the quiz module.
 *
 * This contains functions that are called from within the quiz module only
 * Functions that are also called by core Moodle are in {@link lib.php}
 * This script also loads the code in {@link questionlib.php} which holds
 * the module-indpendent code for handling questions and which in turn
 * initialises all the questiontype classes.
 *
 * @author Martin Dougiamas and many others. This has recently been completely
 *         rewritten by Alex Smith, Julian Sedding and Gustav Delius as part of
 *         the Serving Mathematics project
 *         {@link http://maths.york.ac.uk/serving_maths}
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */

/**
 * Include those library functions that are also used by core Moodle or other modules
 */
require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . '/question/editlib.php');

/// Constants ///////////////////////////////////////////////////////////////////

/**#@+
 * Options determining how the grades from individual attempts are combined to give
 * the overall grade for a user
 */
define("QUIZ_GRADEHIGHEST", "1");
define("QUIZ_GRADEAVERAGE", "2");
define("QUIZ_ATTEMPTFIRST", "3");
define("QUIZ_ATTEMPTLAST",  "4");
/**#@-*/

/**#@+
 * Constants to describe the various states a quiz attempt can be in.
 */
define('QUIZ_STATE_DURING', 'during'); 
define('QUIZ_STATE_IMMEDIATELY', 'immedately'); 
define('QUIZ_STATE_OPEN', 'open'); 
define('QUIZ_STATE_CLOSED', 'closed'); 
define('QUIZ_STATE_TEACHERACCESS', 'teacheraccess'); // State only relevant if you are in a studenty role.
/**#@-*/

/// Functions related to attempts /////////////////////////////////////////

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
    global $USER, $CFG;

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
    $attempt->uniqueid = question_new_attempt_uniqueid();

    return $attempt;
}

/**
 * Returns an unfinished attempt (if there is one) for the given
 * user on the given quiz. This function does not return preview attempts.
 *
 * @param integer $quizid the id of the quiz.
 * @param integer $userid the id of the user.
 *
 * @return mixed the unfinished attempt if there is one, false if not.
 */
function quiz_get_user_attempt_unfinished($quizid, $userid) {
    $attempts = quiz_get_user_attempts($quizid, $userid, 'unfinished', true);
    if ($attempts) {
        return array_shift($attempts);
    } else {
        return false;
    }
}

/**
 * Delete a quiz attempt.
 * @param mixed $attempt an integer attempt id or an attempt object (row of the quiz_attempts table).
 * @param object $quiz the quiz object.
 */
function quiz_delete_attempt($attempt, $quiz) {
    if (is_numeric($attempt)) {
        if (!$attempt = get_record('quiz_attempts', 'id', $attempt)) {
            return;
        }
    }

    if ($attempt->quiz != $quiz->id) {
        debugging("Trying to delete attempt $attempt->id which belongs to quiz $attempt->quiz " .
                "but was passed quiz $quiz->id.");
        return;
    }

    delete_records('quiz_attempts', 'id', $attempt->id);
    delete_attempt($attempt->uniqueid);

    // Search quiz_attempts for other instances by this user.
    // If none, then delete record for this quiz, this user from quiz_grades
    // else recalculate best grade

    $userid = $attempt->userid;
    if (!record_exists('quiz_attempts', 'userid', $userid, 'quiz', $quiz->id)) {
        delete_records('quiz_grades', 'userid', $userid,'quiz', $quiz->id);
    } else {
        quiz_save_best_grade($quiz, $userid);
    }

    quiz_update_grades($quiz, $userid);
}

/// Functions to do with quiz layout and pages ////////////////////////////////

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
        return get_field_sql("SELECT sum(length)+1 FROM {$CFG->prefix}question
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
    echo '<div class="paging pagingbar">';
    echo '<span class="title">' . get_string('page') . ':</span>&nbsp;';
    if ($page > 0) {
        // Print previous link
        $strprev = get_string('previous');
        echo '&nbsp;<a class="previous" href="javascript:navigate(' . ($page - 1) . ');" title="'
         . $strprev . '">(' . $strprev . ')</a>&nbsp;';
    }
    for ($i = 0; $i < $pages; $i++) {
        if ($i == $page) {
            echo '&nbsp;<span class="thispage">'.($i+1).'</span>&nbsp;';
        } else {
            echo '&nbsp;<a href="javascript:navigate(' . ($i) . ');">'.($i+1).'</a>&nbsp;';
        }
    }

    if ($page < $pages - 1) {
        // Print next link
        $strnext = get_string('next');
        echo '&nbsp;<a class="next" href="javascript:navigate(' . ($page + 1) . ');" title="'
         . $strnext . '">(' . $strnext . ')</a>&nbsp;';
    }
    echo '</div>';
}

/// Functions to do with quiz grades //////////////////////////////////////////

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

/**
 * Convert the raw grade stored in $attempt into a grade out of the maximum
 * grade for this quiz.
 *
 * @param float $rawgrade the unadjusted grade, fof example $attempt->sumgrades
 * @param object $quiz the quiz object. Only the fields grade, sumgrades and decimalpoints are used.
 * @return float the rescaled grade.
 */
function quiz_rescale_grade($rawgrade, $quiz, $round = true) {
    if ($quiz->sumgrades) {
        $grade = $rawgrade * $quiz->grade / $quiz->sumgrades;
        if ($round) {
            $grade = round($grade, $quiz->decimalpoints);
        }
    } else {
        $grade = 0;
    }
    return $grade;
}

/**
 * Get the feedback text that should be show to a student who
 * got this grade on this quiz. The feedback is processed ready for diplay.
 *
 * @param float $grade a grade on this quiz.
 * @param integer $quizid the id of the quiz object.
 * @return string the comment that corresponds to this grade (empty string if there is not one.
 */
function quiz_feedback_for_grade($grade, $quizid) {
    $feedback = get_field_select('quiz_feedback', 'feedbacktext',
            "quizid = $quizid AND mingrade <= $grade AND $grade < maxgrade");

    if (empty($feedback)) {
        $feedback = '';
    }

    // Clean the text, ready for display.
    $formatoptions = new stdClass;
    $formatoptions->noclean = true;
    $feedback = format_text($feedback, FORMAT_MOODLE, $formatoptions);

    return $feedback;
}

/**
 * @param integer $quizid the id of the quiz object.
 * @return boolean Whether this quiz has any non-blank feedback text.
 */
function quiz_has_feedback($quizid) {
    static $cache = array();
    if (!array_key_exists($quizid, $cache)) {
        $cache[$quizid] = record_exists_select('quiz_feedback',
                "quizid = $quizid AND " . sql_isnotempty('quiz_feedback', 'feedbacktext', false, true));
    }
    return $cache[$quizid];
}

/**
 * The quiz grade is the score that student's results are marked out of. When it
 * changes, the corresponding data in quiz_grades and quiz_feedback needs to be
 * rescaled.
 *
 * @param float $newgrade the new maximum grade for the quiz.
 * @param object $quiz the quiz we are updating. Passed by reference so its grade field can be updated too.
 * @return boolean indicating success or failure.
 */
function quiz_set_grade($newgrade, &$quiz) {
    // This is potentially expensive, so only do it if necessary.
    if (abs($quiz->grade - $newgrade) < 1e-7) {
        // Nothing to do.
        return true;
    }

    // Use a transaction, so that on those databases that support it, this is safer.
    begin_sql();

    // Update the quiz table.
    $success = set_field('quiz', 'grade', $newgrade, 'id', $quiz->instance);

    // Rescaling the other data is only possible if the old grade was non-zero.
    if ($quiz->grade > 1e-7) {
        global $CFG;

        $factor = $newgrade/$quiz->grade;
        $quiz->grade = $newgrade;

        // Update the quiz_grades table.
        $timemodified = time();
        $success = $success && execute_sql("
                UPDATE {$CFG->prefix}quiz_grades
                SET grade = $factor * grade, timemodified = $timemodified
                WHERE quiz = $quiz->id
        ", false);

        // Update the quiz_feedback table.
        $success = $success && execute_sql("
                UPDATE {$CFG->prefix}quiz_feedback
                SET mingrade = $factor * mingrade, maxgrade = $factor * maxgrade
                WHERE quizid = $quiz->id
        ", false);
    }

    // update grade item and send all grades to gradebook
    quiz_grade_item_update($quiz);
    quiz_update_grades($quiz);

    if ($success) {
        return commit_sql();
    } else {
        rollback_sql();
        return false;
    }
}

/**
 * Save the overall grade for a user at a quiz in the quiz_grades table
 *
 * @param object $quiz The quiz for which the best grade is to be calculated and then saved.
 * @param integer $userid The userid to calculate the grade for. Defaults to the current user.
 * @return boolean Indicates success or failure.
 */
function quiz_save_best_grade($quiz, $userid = null) {
    global $USER;

    if (empty($userid)) {
        $userid = $USER->id;
    }

    // Get all the attempts made by the user
    if (!$attempts = quiz_get_user_attempts($quiz->id, $userid)) {
        notify('Could not find any user attempts');
        return false;
    }

    // Calculate the best grade
    $bestgrade = quiz_calculate_best_grade($quiz, $attempts);
    $bestgrade = quiz_rescale_grade($bestgrade, $quiz);

    // Save the best grade in the database
    if ($grade = get_record('quiz_grades', 'quiz', $quiz->id, 'userid', $userid)) {
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

    quiz_update_grades($quiz, $userid);
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

/**
 * @return the options for calculating the quiz grade from the individual attempt grades.
 */
function quiz_get_grading_options() {
    return array (
            QUIZ_GRADEHIGHEST => get_string('gradehighest', 'quiz'),
            QUIZ_GRADEAVERAGE => get_string('gradeaverage', 'quiz'),
            QUIZ_ATTEMPTFIRST => get_string('attemptfirst', 'quiz'),
            QUIZ_ATTEMPTLAST  => get_string('attemptlast', 'quiz'));
}

/**
 * @param int $option one of the values QUIZ_GRADEHIGHEST, QUIZ_GRADEAVERAGE, QUIZ_ATTEMPTFIRST or QUIZ_ATTEMPTLAST.
 * @return the lang string for that option.
 */
function quiz_get_grading_option_name($option) {
    $strings = quiz_get_grading_options();
    return $strings[$option];
}

/// Other quiz functions ////////////////////////////////////////////////////

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
 * Upgrade states for an attempt to Moodle 1.5 model
 *
 * Any state that does not yet have its timestamp set to nonzero has not yet been upgraded from Moodle 1.4
 * The reason these are still around is that for large sites it would have taken too long to
 * upgrade all states at once. This function sets the timestamp field and creates an entry in the
 * question_sessions table.
 * @param object $attempt  The attempt whose states need upgrading
 */
function quiz_upgrade_states($attempt) {
    global $CFG;
    // The old quiz model only allowed a single response per quiz attempt so that there will be
    // only one state record per question for this attempt.

    // We set the timestamp of all states to the timemodified field of the attempt.
    execute_sql("UPDATE {$CFG->prefix}question_states SET timestamp = '$attempt->timemodified' WHERE attempt = '$attempt->uniqueid'", false);

    // For each state we create an entry in the question_sessions table, with both newest and
    // newgraded pointing to this state.
    // Actually we only do this for states whose question is actually listed in $attempt->layout.
    // We do not do it for states associated to wrapped questions like for example the questions
    // used by a RANDOM question
    $session = new stdClass;
    $session->attemptid = $attempt->uniqueid;
    $questionlist = quiz_questions_in_quiz($attempt->layout);
    if ($questionlist and $states = get_records_select('question_states', "attempt = '$attempt->uniqueid' AND question IN ($questionlist)")) {
        foreach ($states as $state) {
            $session->newgraded = $state->id;
            $session->newest = $state->id;
            $session->questionid = $state->question;
            insert_record('question_sessions', $session, false);
        }
    }
}

/**
 * @param object $quiz the quiz
 * @param object $question the question
 * @return the HTML for a preview question icon.
 */
function quiz_question_preview_button($quiz, $question) {
    global $CFG, $COURSE;
    if (!question_has_capability_on($question, 'use', $question->category)){
        return '';
    }
    $strpreview = get_string('previewquestion', 'quiz');
    $quizorcourseid = $quiz->id?('&amp;quizid=' . $quiz->id):('&amp;courseid=' .$COURSE->id);
    return link_to_popup_window('/question/preview.php?id=' . $question->id . $quizorcourseid, 'questionpreview',
            "<img src=\"$CFG->pixpath/t/preview.gif\" class=\"iconsmall\" alt=\"$strpreview\" />",
            0, 0, $strpreview, QUESTION_PREVIEW_POPUP_OPTIONS, true);
}

/**
 * Determine render options
 *
 * @param int $reviewoptions
 * @param object $state
 */
function quiz_get_renderoptions($reviewoptions, $state) {
    $options = new stdClass;

    // Show the question in readonly (review) mode if the question is in
    // the closed state
    $options->readonly = question_state_is_closed($state);

    // Show feedback once the question has been graded (if allowed by the quiz)
    $options->feedback = question_state_is_graded($state) && ($reviewoptions & QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_IMMEDIATELY);

    // Show validation only after a validation event
    $options->validation = QUESTION_EVENTVALIDATE === $state->event;

    // Show correct responses in readonly mode if the quiz allows it
    $options->correct_responses = $options->readonly && ($reviewoptions & QUIZ_REVIEW_ANSWERS & QUIZ_REVIEW_IMMEDIATELY);

    // Show general feedback if the question has been graded and the quiz allows it.
    $options->generalfeedback = question_state_is_graded($state) && ($reviewoptions & QUIZ_REVIEW_GENERALFEEDBACK & QUIZ_REVIEW_IMMEDIATELY);

    // Show overallfeedback once the attempt is over.
    $options->overallfeedback = false;

    // Always show responses and scores
    $options->responses = true;
    $options->scores = true;
    $options->quizstate = QUIZ_STATE_DURING;

    return $options;
}

/**
 * Determine review options
 *
 * @param object $quiz the quiz instance.
 * @param object $attempt the attempt in question.
 * @param $context the roles and permissions context,
 *          normally the context for the quiz module instance.
 *
 * @return object an object with boolean fields responses, scores, feedback,
 *          correct_responses, solutions and general feedback
 */
function quiz_get_reviewoptions($quiz, $attempt, $context=null) {
    $options = new stdClass;
    $options->readonly = true;

    // Provide the links to the question review and comment script
    $options->questionreviewlink = '/mod/quiz/reviewquestion.php';

    // Show a link to the comment box only for closed attempts
    if ($attempt->timefinish && !is_null($context) && has_capability('mod/quiz:grade', $context)) {
        $options->questioncommentlink = '/mod/quiz/comment.php';
    }

    if (!is_null($context) && has_capability('mod/quiz:viewreports', $context) && 
            has_capability('moodle/grade:viewhidden', $context) && !$attempt->preview) {
        // People who can see reports and hidden grades should be shown everything,
        // except during preview when teachers want to see what students see.
        $options->responses = true;
        $options->scores = true; 
        $options->feedback = true;
        $options->correct_responses = true;
        $options->solutions = false;
        $options->generalfeedback = true;
        $options->overallfeedback = true;
        $options->quizstate = QUIZ_STATE_TEACHERACCESS;
    } else {
        // Work out the state of the attempt ...
        if (((time() - $attempt->timefinish) < 120) || $attempt->timefinish==0) {
            $quiz_state_mask = QUIZ_REVIEW_IMMEDIATELY;
            $options->quizstate = QUIZ_STATE_IMMEDIATELY;
        } else if (!$quiz->timeclose or time() < $quiz->timeclose) {
            $quiz_state_mask = QUIZ_REVIEW_OPEN;
            $options->quizstate = QUIZ_STATE_OPEN;
        } else {
            $quiz_state_mask = QUIZ_REVIEW_CLOSED;
            $options->quizstate = QUIZ_STATE_CLOSED;
        }

        // ... and hence extract the appropriate review options. 
        $options->responses = ($quiz->review & $quiz_state_mask & QUIZ_REVIEW_RESPONSES) ? 1 : 0;
        $options->scores = ($quiz->review & $quiz_state_mask & QUIZ_REVIEW_SCORES) ? 1 : 0;
        $options->feedback = ($quiz->review & $quiz_state_mask & QUIZ_REVIEW_FEEDBACK) ? 1 : 0;
        $options->correct_responses = ($quiz->review & $quiz_state_mask & QUIZ_REVIEW_ANSWERS) ? 1 : 0;
        $options->solutions = ($quiz->review & $quiz_state_mask & QUIZ_REVIEW_SOLUTIONS) ? 1 : 0;
        $options->generalfeedback = ($quiz->review & $quiz_state_mask & QUIZ_REVIEW_GENERALFEEDBACK) ? 1 : 0;
        $options->overallfeedback = $attempt->timefinish && ($quiz->review & $quiz_state_mask & QUIZ_REVIEW_OVERALLFEEDBACK);
    }

    return $options;
}

/**
 * Combines the review options from a number of different quiz attempts.
 * Returns an array of two ojects, so he suggested way of calling this
 * funciton is:
 * list($someoptions, $alloptions) = quiz_get_combined_reviewoptions(...)
 *
 * @param object $quiz the quiz instance.
 * @param array $attempts an array of attempt objects.
 * @param $context the roles and permissions context,
 *          normally the context for the quiz module instance.
 *
 * @return array of two options objects, one showing which options are true for
 *          at least one of the attempts, the other showing which options are true
 *          for all attempts.
 */
function quiz_get_combined_reviewoptions($quiz, $attempts, $context=null) {
    $fields = array('readonly', 'scores', 'feedback', 'correct_responses', 'solutions', 'generalfeedback', 'overallfeedback');
    $someoptions = new stdClass;
    $alloptions = new stdClass;
    foreach ($fields as $field) {
        $someoptions->$field = false;
        $alloptions->$field = true;
    }
    foreach ($attempts as $attempt) {
        $attemptoptions = quiz_get_reviewoptions($quiz, $attempt, $context);
        foreach ($fields as $field) {
            $someoptions->$field = $someoptions->$field || $attemptoptions->$field;
            $alloptions->$field = $alloptions->$field && $attemptoptions->$field;
        }
    }
    return array($someoptions, $alloptions);
}

/// FUNCTIONS FOR SENDING NOTIFICATION EMAILS ///////////////////////////////

/**
 * Sends confirmation email to the student taking the course
 *
 * @param stdClass $a associative array of replaceable fields for the templates
 *
 * @return bool|string result of email_to_user()
 */
function quiz_send_confirmation($a) {

    global $USER;

    // recipient is self
    $a->useridnumber = $USER->idnumber;
    $a->username = fullname($USER);
    $a->userusername = $USER->username;

    // fetch the subject and body from strings
    $subject = get_string('emailconfirmsubject', 'quiz', $a);
    $body = get_string('emailconfirmbody', 'quiz', $a);

    // send email and analyse result
    return email_to_user($USER, get_admin(), $subject, $body);
}

/**
 * Sends notification email to the interested parties that assign the role capability
 *
 * @param object $recipient user object of the intended recipient
 * @param stdClass $a associative array of replaceable fields for the templates
 *
 * @return bool|string result of email_to_user()
 */
function quiz_send_notification($recipient, $a) {

    global $USER;

    // recipient info for template
    $a->username = fullname($recipient);
    $a->userusername = $recipient->username;
    $a->userusername = $recipient->username;

    // fetch the subject and body from strings
    $subject = get_string('emailnotifysubject', 'quiz', $a);
    $body = get_string('emailnotifybody', 'quiz', $a);

    // send email and analyse result
    return email_to_user($recipient, $USER, $subject, $body);
}

/**
 * Takes a bunch of information to format into an email and send
 * to the specified recipient.
 *
 * @param object $course the course
 * @param object $quiz the quiz
 * @param object $attempt this attempt just finished
 * @param object $context the quiz context
 * @param object $cm the coursemodule for this quiz
 *
 * @return int number of emails sent
 */
function quiz_send_notification_emails($course, $quiz, $attempt, $context, $cm) {
    global $CFG, $USER;
    // we will count goods and bads for error logging
    $emailresult = array('good' => 0, 'block' => 0, 'fail' => 0);

    // do nothing if required objects not present
    if (empty($course) or empty($quiz) or empty($attempt) or empty($context)) {
        debugging('quiz_send_notification_emails: Email(s) not sent due to program error.',
                DEBUG_DEVELOPER);
        return $emailresult['fail'];
    }

    // check for confirmation required
    $sendconfirm = false;
    $notifyexcludeusers = '';
    if (has_capability('mod/quiz:emailconfirmsubmission', $context, NULL, false)) {
        // exclude from notify emails later
        $notifyexcludeusers = $USER->id;
        // send the email
        $sendconfirm = true;
    }

    // check for notifications required
    $notifyfields = 'u.id, u.username, u.firstname, u.lastname, u.email, u.emailstop, u.lang, u.timezone, u.mailformat, u.maildisplay';
    $groups = groups_get_all_groups($course->id, $USER->id);
    if (is_array($groups) && count($groups) > 0) {
        $groups = array_keys($groups);
    } else if (groups_get_activity_groupmode($cm, $course) != NOGROUPS) {
        // If the user is not in a group, and the quiz is set to group mode,
        // then set $gropus to a non-existant id so that only users with
        // 'moodle/site:accessallgroups' get notified.
        $groups = -1;
    } else {
        $groups = '';
    }
    $userstonotify = get_users_by_capability($context, 'mod/quiz:emailnotifysubmission',
            $notifyfields, '', '', '', $groups, $notifyexcludeusers, false, false, true);

    // if something to send, then build $a
    if (! empty($userstonotify) or $sendconfirm) {
        $a = new stdClass;
        // course info
        $a->coursename = $course->fullname;
        $a->courseshortname = $course->shortname;
        // quiz info
        $a->quizname = $quiz->name;
        $a->quizreporturl = $CFG->wwwroot . '/mod/quiz/report.php?q=' . $quiz->id;
        $a->quizreportlink = '<a href="' . $a->quizreporturl . '">' . format_string($quiz->name) . ' report</a>';
        $a->quizreviewurl = $CFG->wwwroot . '/mod/quiz/review.php?attempt=' . $attempt->id;
        $a->quizreviewlink = '<a href="' . $a->quizreviewurl . '">' . format_string($quiz->name) . ' review</a>';
        $a->quizurl = $CFG->wwwroot . '/mod/quiz/view.php?q=' . $quiz->id;
        $a->quizlink = '<a href="' . $a->quizurl . '">' . format_string($quiz->name) . '</a>';
        // attempt info
        $a->submissiontime = userdate($attempt->timefinish);
        $a->timetaken = format_time($attempt->timefinish - $attempt->timestart);
        // student who sat the quiz info
        $a->studentidnumber = $USER->idnumber;
        $a->studentname = fullname($USER);
        $a->studentusername = $USER->username;
    }

    // send confirmation if required
    if ($sendconfirm) {
        // send the email and update stats
        switch (quiz_send_confirmation($a)) {
            case true:
                $emailresult['good']++;
                break;
            case false:
                $emailresult['fail']++;
                break;
            case 'emailstop':
                $emailresult['block']++;
                break;
        }
    }

    // send notifications if required
    if (!empty($userstonotify)) {
        // loop through recipients and send an email to each and update stats
        foreach ($userstonotify as $recipient) {
            switch (quiz_send_notification($recipient, $a)) {
                case true:
                    $emailresult['good']++;
                    break;
                case false:
                    $emailresult['fail']++;
                    break;
                case 'emailstop':
                    $emailresult['block']++;
                    break;
            }
        }
    }

    // log errors sending emails if any
    if (! empty($emailresult['fail'])) {
        debugging('quiz_send_notification_emails:: '.$emailresult['fail'].' email(s) failed to be sent.', DEBUG_DEVELOPER);
    }
    if (! empty($emailresult['block'])) {
        debugging('quiz_send_notification_emails:: '.$emailresult['block'].' email(s) were blocked by the user.', DEBUG_DEVELOPER);
    }

    // return the number of successfully sent emails
    return $emailresult['good'];
}

/**
 * Checks if browser is safe browser
 * 
 * @return true, if browser is safe browser else false
 */
function quiz_check_safe_browser() {
    return strpos($_SERVER['HTTP_USER_AGENT'], "SEB") !== false;
}

?>