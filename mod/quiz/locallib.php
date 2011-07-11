<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * Library of functions used by the quiz module.
 *
 * This contains functions that are called from within the quiz module only
 * Functions that are also called by core Moodle are in {@link lib.php}
 * This script also loads the code in {@link questionlib.php} which holds
 * the module-indpendent code for handling questions and which in turn
 * initialises all the questiontype classes.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); /// It must be included from a Moodle page.
}

/**
 * Include those library functions that are also used by core Moodle or other modules
 */
require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . '/mod/quiz/accessrules.php');
require_once($CFG->dirroot . '/mod/quiz/attemptlib.php');
require_once($CFG->dirroot . '/question/editlib.php');
require_once($CFG->libdir  . '/eventslib.php');
require_once($CFG->libdir . '/filelib.php');

/// Constants ///////////////////////////////////////////////////////////////////

/**#@+
 * Constants to describe the various states a quiz attempt can be in.
 */
define('QUIZ_STATE_DURING', 'during');
define('QUIZ_STATE_IMMEDIATELY', 'immedately');
define('QUIZ_STATE_OPEN', 'open');
define('QUIZ_STATE_CLOSED', 'closed');
define('QUIZ_STATE_TEACHERACCESS', 'teacheraccess'); // State only relevant if you are in a studenty role.
/**#@-*/

/**
 * We show the countdown timer if there is less than this amount of time left before the
 * the quiz close date. (1 hour)
 */
define('QUIZ_SHOW_TIME_BEFORE_DEADLINE', '3600');

/// Functions related to attempts /////////////////////////////////////////

/**
 * Creates an object to represent a new attempt at a quiz
 *
 * Creates an attempt object to represent an attempt at the quiz by the current
 * user starting at the current time. The ->id field is not set. The object is
 * NOT written to the database.
 *
 * @param object $quiz the quiz to create an attempt for.
 * @param integer $attemptnumber the sequence number for the attempt.
 * @param object $lastattempt the previous attempt by this user, if any. Only needed
 *         if $attemptnumber > 1 and $quiz->attemptonlast is true.
 * @param integer $timenow the time the attempt was started at.
 * @param boolean $ispreview whether this new attempt is a preview.
 *
 * @return object the newly created attempt object.
 */
function quiz_create_attempt($quiz, $attemptnumber, $lastattempt, $timenow, $ispreview = false) {
    global $USER;

    if ($attemptnumber == 1 || !$quiz->attemptonlast) {
    /// We are not building on last attempt so create a new attempt.
        $attempt = new stdClass;
        $attempt->quiz = $quiz->id;
        $attempt->userid = $USER->id;
        $attempt->preview = 0;
        if ($quiz->shufflequestions) {
            $attempt->layout = quiz_clean_layout(quiz_repaginate($quiz->questions, $quiz->questionsperpage, true),true);
        } else {
            $attempt->layout = quiz_clean_layout($quiz->questions,true);
        }
    } else {
    /// Build on last attempt.
        if (empty($lastattempt)) {
            print_error('cannotfindprevattempt', 'quiz');
        }
        $attempt = $lastattempt;
    }

    $attempt->attempt = $attemptnumber;
    $attempt->sumgrades = 0.0;
    $attempt->timestart = $timenow;
    $attempt->timefinish = 0;
    $attempt->timemodified = $timenow;
    $attempt->uniqueid = question_new_attempt_uniqueid();

/// If this is a preview, mark it as such.
    if ($ispreview) {
        $attempt->preview = 1;
    }

    return $attempt;
}

/**
 * Returns the unfinished attempt for the given
 * user on the given quiz, if there is one.
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
 * Load an attempt by id. You need to use this method instead of $DB->get_record, because
 * of some ancient history to do with the upgrade from Moodle 1.4 to 1.5, See the comment
 * after CREATE TABLE `prefix_quiz_newest_states` in mod/quiz/db/mysql.php.
 *
 * @param integer $attemptid the id of the attempt to load.
 */
function quiz_load_attempt($attemptid) {
    global $DB;
    $attempt = $DB->get_record('quiz_attempts', array('id' => $attemptid));
    if (!$attempt) {
        return false;
    }

    if (!$DB->record_exists('question_sessions', array('attemptid' => $attempt->uniqueid))) {
    /// this attempt has not yet been upgraded to the new model
        quiz_upgrade_states($attempt);
    }

    return $attempt;
}

/**
 * Delete a quiz attempt.
 * @param mixed $attempt an integer attempt id or an attempt object (row of the quiz_attempts table).
 * @param object $quiz the quiz object.
 */
function quiz_delete_attempt($attempt, $quiz) {
    global $DB;
    if (is_numeric($attempt)) {
        if (!$attempt = $DB->get_record('quiz_attempts', array('id' => $attempt))) {
            return;
        }
    }

    if ($attempt->quiz != $quiz->id) {
        debugging("Trying to delete attempt $attempt->id which belongs to quiz $attempt->quiz " .
                "but was passed quiz $quiz->id.");
        return;
    }

    $DB->delete_records('quiz_attempts', array('id' => $attempt->id));
    delete_attempt($attempt->uniqueid);

    // Search quiz_attempts for other instances by this user.
    // If none, then delete record for this quiz, this user from quiz_grades
    // else recalculate best grade

    $userid = $attempt->userid;
    if (!$DB->record_exists('quiz_attempts', array('userid' => $userid, 'quiz' => $quiz->id))) {
        $DB->delete_records('quiz_grades', array('userid' => $userid,'quiz' => $quiz->id));
    } else {
        quiz_save_best_grade($quiz, $userid);
    }

    quiz_update_grades($quiz, $userid);
}

/**
 * Delete all the preview attempts at a quiz, or possibly all the attempts belonging
 * to one user.
 * @param object $quiz the quiz object.
 * @param integer $userid (optional) if given, only delete the previews belonging to this user.
 */
function quiz_delete_previews($quiz, $userid = null) {
    global $DB;
    $conditions = array('quiz' => $quiz->id, 'preview' => 1);
    if (!empty($userid)) {
        $conditions['userid'] = $userid;
    }
    $previewattempts = $DB->get_records('quiz_attempts', $conditions);
    foreach ($previewattempts as $attempt) {
        quiz_delete_attempt($attempt, $quiz);
    }
}

/**
 * @param integer $quizid The quiz id.
 * @return boolean whether this quiz has any (non-preview) attempts.
 */
function quiz_has_attempts($quizid) {
    global $DB;
    return $DB->record_exists('quiz_attempts', array('quiz' => $quizid, 'preview' => 0));
}

/// Functions to do with quiz layout and pages ////////////////////////////////

/**
 * Returns a comma separated list of question ids for the current page
 *
 * @param string $layout the string representing the quiz layout. Each page is represented as a
 *      comma separated list of question ids and 0 indicating page breaks.
 *      So 5,2,0,3,0 means questions 5 and 2 on page 1 and question 3 on page 2
 * @param integer $page the number of the current page.
 * @return string comma separated list of question ids
 */
function quiz_questions_on_page($layout, $page) {
    $pages = explode(',0', $layout);
    return trim($pages[$page], ',');
}

/**
 * Returns a comma separated list of question ids for the quiz
 *
 * @param string $layout The string representing the quiz layout. Each page is
 *      represented as a comma separated list of question ids and 0 indicating
 *      page breaks. So 5,2,0,3,0 means questions 5 and 2 on page 1 and question
 *      3 on page 2
 * @return string comma separated list of question ids, without page breaks.
 */
function quiz_questions_in_quiz($layout) {
    $layout = preg_replace('/,(0+,)+/', ',', $layout); // Remove page breaks from the middle.
    $layout = preg_replace('/^0+,/', '', $layout); // And from the start.
    $layout = preg_replace('/(^|,)0+$/', '', $layout); // And from the end.
    return $layout;
}

/**
 * Returns the number of pages in a quiz layout
 *
 * @param string $layout The string representing the quiz layout. Always ends in ,0
 * @return integer The number of pages in the quiz.
 */
function quiz_number_of_pages($layout) {
    $count = 0;
    if ($layout !== '') {
        //if the first page is empty, include it, too
        if (strcmp($layout[0], '0') === 0) {
            $count++;
        }
        $count += substr_count($layout, ',0');
    }
    return $count;
}
/**
 * Returns the number of questions in the quiz layout
 *
 * @param string $layout the string representing the quiz layout.
 * @return integer The number of questions in the quiz.
 */
function quiz_number_of_questions_in_quiz($layout) {
    $layout = quiz_questions_in_quiz(quiz_clean_layout($layout));
    $count = substr_count($layout, ',');
    if ($layout !== '') {
        $count++;
    }
    return $count;
}

/**
 * Returns the first question number for the current quiz page
 *
 * @param string $quizlayout The string representing the layout for the whole quiz
 * @param string $pagelayout The string representing the layout for the current page
 * @return integer the number of the first question
 */
function quiz_first_questionnumber($quizlayout, $pagelayout) {
    // this works by finding all the questions from the quizlayout that
    // come before the current page and then adding up their lengths.
    global $CFG, $DB;
    $start = strpos($quizlayout, ','.$pagelayout.',')-2;
    if ($start > 0) {
        $prevlist = substr($quizlayout, 0, $start);
        list($usql, $params) = $DB->get_in_or_equal(explode(',', $prevlist));
        return $DB->get_field_sql("SELECT sum(length)+1 FROM {question}
         WHERE id $usql", $params);
    } else {
        return 1;
    }
}

/**
 * Re-paginates the quiz layout
 *
 * @param string $layout  The string representing the quiz layout.
 * @param integer $perpage The number of questions per page
 * @param boolean $shuffle Should the questions be reordered randomly?
 * @return string the new layout string
 */
function quiz_repaginate($layout, $perpage, $shuffle = false) {
    $layout = str_replace(',0', '', $layout); // remove existing page breaks
    $questions = explode(',', $layout);
    //remove empty pages from beginning
    while (reset($questions) === '0') {
        array_shift($questions);
    }
    if ($shuffle) {
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

/// Functions to do with quiz grades //////////////////////////////////////////

/**
 * Creates an array of maximum grades for a quiz
 * The grades are extracted from the quiz_question_instances table.
 *
 * @param integer $quiz The quiz object
 * @return array Array of grades indexed by question id. These are the maximum
 *      possible grades that students can achieve for each of the questions.
 */
function quiz_get_all_question_grades($quiz) {
    global $CFG, $DB;

    $questionlist = quiz_questions_in_quiz($quiz->questions);
    if (empty($questionlist)) {
        return array();
    }

    $params = array($quiz->id);
    $wheresql = '';
    if (!is_null($questionlist)) {
        list($usql, $question_params) = $DB->get_in_or_equal(explode(',', $questionlist));
        $wheresql = " AND question $usql ";
        $params = array_merge($params, $question_params);
    }

    $instances = $DB->get_records_sql("SELECT question,grade,id
                                    FROM {quiz_question_instances}
                                    WHERE quiz = ? $wheresql", $params);

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
 * Update the sumgrades field of the quiz. This needs to be called whenever
 * the grading structure of the quiz is changed. For example if a question is
 * added or removed, or a question weight is changed.
 *
 * @param object $quiz a quiz.
 */
function quiz_update_sumgrades($quiz) {
    global $DB;
    $grades = quiz_get_all_question_grades($quiz);
    $sumgrades = 0;
    foreach ($grades as $grade) {
        $sumgrades += $grade;
    }
    if (!isset($quiz->sumgrades) || $quiz->sumgrades != $sumgrades) {
        $DB->set_field('quiz', 'sumgrades', $sumgrades, array('id' => $quiz->id));
        $quiz->sumgrades = $sumgrades;
    }
}

/**
 * Convert the raw grade stored in $attempt into a grade out of the maximum
 * grade for this quiz.
 *
 * @param float $rawgrade the unadjusted grade, fof example $attempt->sumgrades
 * @param object $quiz the quiz object. Only the fields grade, sumgrades, decimalpoints and questiondecimalpoints are used.
 * @param mixed $round false = don't round, true = round using quiz_format_grade, 'question' = round using quiz_format_question_grade.
 * @return float the rescaled grade.
 */
function quiz_rescale_grade($rawgrade, $quiz, $round = true) {
    if ($quiz->sumgrades != 0) {
        $grade = $rawgrade * $quiz->grade / $quiz->sumgrades;
        if ($round === 'question') { // === really necessary here true == 'question' is true in PHP!
            $grade = quiz_format_question_grade($quiz, $grade);
        } else if ($round) {
            $grade = quiz_format_grade($quiz, $grade);
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
function quiz_feedback_for_grade($grade, $quiz, $context, $cm=null) {
    global $DB;

    $feedback = $DB->get_record_select('quiz_feedback', "quizid = ? AND mingrade <= ? AND $grade < maxgrade", array($quiz->id, $grade));

    if (empty($feedback->feedbacktext)) {
        return '';
    }

    // Clean the text, ready for display.
    $formatoptions = new stdClass;
    $formatoptions->noclean = true;
    $feedbacktext = file_rewrite_pluginfile_urls($feedback->feedbacktext, 'pluginfile.php', $context->id, 'mod_quiz', 'feedback', $feedback->id);
    $feedbacktext = format_text($feedbacktext, $feedback->feedbacktextformat, $formatoptions);

    return $feedbacktext;
}

/**
 * @param object $quiz the quiz database row.
 * @return boolean Whether this quiz has any non-blank feedback text.
 */
function quiz_has_feedback($quiz) {
    global $DB;
    static $cache = array();
    if (!array_key_exists($quiz->id, $cache)) {
        $cache[$quiz->id] = quiz_has_grades($quiz) &&
                $DB->record_exists_select('quiz_feedback', "quizid = ? AND " .
                    $DB->sql_isnotempty('quiz_feedback', 'feedbacktext', false, true),
                array($quiz->id));
    }
    return $cache[$quiz->id];
}

/**
 * The quiz grade is the score that student's results are marked out of. When it
 * changes, the corresponding data in quiz_grades and quiz_feedback needs to be
 * rescaled.
 *
 * @param float $newgrade the new maximum grade for the quiz.
 * @param object $quiz the quiz we are updating. Passed by reference so its grade field can be updated too.
 * @return boolean indicating success or failure. TODO: MDL-20625
 */
function quiz_set_grade($newgrade, &$quiz) {
    global $DB;
    // This is potentially expensive, so only do it if necessary.
    if (abs($quiz->grade - $newgrade) < 1e-7) {
        // Nothing to do.
        return true;
    }

    // Use a transaction, so that on those databases that support it, this is safer.
    $transaction = $DB->start_delegated_transaction();

    try {
        // Update the quiz table.
        $DB->set_field('quiz', 'grade', $newgrade, array('id' => $quiz->instance));

        // Rescaling the other data is only possible if the old grade was non-zero.
        if ($quiz->grade > 1e-7) {
            global $CFG;

            $factor = $newgrade/$quiz->grade;
            $quiz->grade = $newgrade;

            // Update the quiz_grades table.
            $timemodified = time();
            $DB->execute("
                    UPDATE {quiz_grades}
                    SET grade = ? * grade, timemodified = ?
                    WHERE quiz = ?
            ", array($factor, $timemodified, $quiz->id));

            // Update the quiz_feedback table.
            $DB->execute("
                    UPDATE {quiz_feedback}
                    SET mingrade = ? * mingrade, maxgrade = ? * maxgrade
                    WHERE quizid = ?
            ", array($factor, $factor, $quiz->id));
        }

        // update grade item and send all grades to gradebook
        quiz_grade_item_update($quiz);
        quiz_update_grades($quiz);

        $transaction->allow_commit();
        return true;

    } catch (Exception $e) {
        //TODO: MDL-20625 this part was returning false, but now throws exception
        $transaction->rollback($e);
    }
}

/**
 * Save the overall grade for a user at a quiz in the quiz_grades table
 *
 * @param object $quiz The quiz for which the best grade is to be calculated and then saved.
 * @param integer $userid The userid to calculate the grade for. Defaults to the current user.
 * @param array $attempts The attempts of this user. Useful if you are
 * looping through many users. Attempts can be fetched in one master query to
 * avoid repeated querying.
 * @return boolean Indicates success or failure.
 */
function quiz_save_best_grade($quiz, $userid = null, $attempts = array()) {
    global $DB;
    global $USER, $OUTPUT;

    if (empty($userid)) {
        $userid = $USER->id;
    }

    if (!$attempts){
        // Get all the attempts made by the user
        if (!$attempts = quiz_get_user_attempts($quiz->id, $userid)) {
            echo $OUTPUT->notification('Could not find any user attempts');
            return false;
        }
    }

    // Calculate the best grade
    $bestgrade = quiz_calculate_best_grade($quiz, $attempts);
    $bestgrade = quiz_rescale_grade($bestgrade, $quiz, false);

    // Save the best grade in the database
    if ($grade = $DB->get_record('quiz_grades', array('quiz' => $quiz->id, 'userid' => $userid))) {
        $grade->grade = $bestgrade;
        $grade->timemodified = time();
        $DB->update_record('quiz_grades', $grade);
    } else {
        $grade->quiz = $quiz->id;
        $grade->userid = $userid;
        $grade->grade = $bestgrade;
        $grade->timemodified = time();
        $DB->insert_record('quiz_grades', $grade);
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
    global $DB;
    global $CFG;
    // The old quiz model only allowed a single response per quiz attempt so that there will be
    // only one state record per question for this attempt.

    // We set the timestamp of all states to the timemodified field of the attempt.
    $DB->execute("UPDATE {question_states} SET timestamp = ? WHERE attempt = ?", array($attempt->timemodified, $attempt->uniqueid));

    // For each state we create an entry in the question_sessions table, with both newest and
    // newgraded pointing to this state.
    // Actually we only do this for states whose question is actually listed in $attempt->layout.
    // We do not do it for states associated to wrapped questions like for example the questions
    // used by a RANDOM question
    $session = new stdClass;
    $session->attemptid = $attempt->uniqueid;
    $questionlist = quiz_questions_in_quiz($attempt->layout);
    $params = array($attempt->uniqueid);
    list($usql, $question_params) = $DB->get_in_or_equal(explode(',',$questionlist));
    $params = array_merge($params, $question_params);

    if ($questionlist and $states = $DB->get_records_select('question_states', "attempt = ? AND question $usql", $params)) {
        foreach ($states as $state) {
            $session->newgraded = $state->id;
            $session->newest = $state->id;
            $session->questionid = $state->question;
            $DB->insert_record('question_sessions', $session, false);
        }
    }
}

/**
 * @param object $quiz the quiz.
 * @param integer $cmid the course_module object for this quiz.
 * @param object $question the question.
 * @param string $returnurl url to return to after action is done.
 * @return string html for a number of icons linked to action pages for a
 * question - preview and edit / view icons depending on user capabilities.
 */
function quiz_question_action_icons($quiz, $cmid, $question, $returnurl) {
    $html = quiz_question_preview_button($quiz, $question) . ' ' .
            quiz_question_edit_button($cmid, $question, $returnurl);
    return $html;
}

/**
 * @param integer $cmid the course_module.id for this quiz.
 * @param object $question the question.
 * @param string $returnurl url to return to after action is done.
 * @param string $contentbeforeicon some HTML content to be added inside the link, before the icon.
 * @return the HTML for an edit icon, view icon, or nothing for a question (depending on permissions).
 */
function quiz_question_edit_button($cmid, $question, $returnurl, $contentaftericon = '') {
    global $CFG, $OUTPUT;

    // Minor efficiency saving. Only get strings once, even if there are a lot of icons on one page.
    static $stredit = null;
    static $strview = null;
    if ($stredit === null){
        $stredit = get_string('edit');
        $strview = get_string('view');
    }

    // What sort of icon should we show?
    $action = '';
    if (question_has_capability_on($question, 'edit', $question->category) ||
            question_has_capability_on($question, 'move', $question->category)) {
        $action = $stredit;
        $icon = '/t/edit';
    } else if (question_has_capability_on($question, 'view', $question->category)) {
        $action = $strview;
        $icon = '/i/info';
    }

    // Build the icon.
    if ($action) {
        $questionparams = array('returnurl' => $returnurl, 'cmid' => $cmid, 'id' => $question->id);
        $questionurl = new moodle_url("$CFG->wwwroot/question/question.php", $questionparams);
        return '<a title="' . $action . '" href="' . $questionurl->out() . '"><img src="' .
                $OUTPUT->pix_url($icon) . '" alt="' . $action . '" />' . $contentaftericon .
                '</a>';
    } else {
        return $contentaftericon;
    }
}

/**
 * @param object $quiz the quiz
 * @param object $question the question
 * @param boolean $label if true, show the previewquestion label after the icon
 * @return the HTML for a preview question icon.
 */
function quiz_question_preview_button($quiz, $question, $label = false) {
    global $CFG, $COURSE, $OUTPUT;
    if (!question_has_capability_on($question, 'use', $question->category)) {
        return '';
    }

    // Minor efficiency saving. Only get strings once, even if there are a lot of icons on one page.
    static $strpreview = null;
    static $strpreviewquestion = null;
    if ($strpreview === null){
        $strpreview = get_string('preview', 'quiz');
        $strpreviewquestion = get_string('previewquestion', 'quiz');
    }

    // Do we want a label?
    $strpreviewlabel="";
    if ($label) {
        $strpreviewlabel = $strpreview;
    }

    // Build the icon.
    $image = $OUTPUT->pix_icon('t/preview', $strpreviewquestion);

    $link = new moodle_url($CFG->wwwroot."/question/preview.php?id=$question->id&quizid=$quiz->id");
    parse_str(QUESTION_PREVIEW_POPUP_OPTIONS, $options);
    $action = new popup_action('click', $link, 'questionpreview', $options);

    return $OUTPUT->action_link($link, $image, $action, array('title' => $strpreviewquestion));
}

/**
 * @param object $attempt the attempt.
 * @param object $context the quiz context.
 * @return integer whether flags should be shown/editable to the current user for this attempt.
 */
function quiz_get_flag_option($attempt, $context) {
    global $USER;
    static $flagmode = null;
    if (is_null($flagmode)) {
        if (!has_capability('moodle/question:flag', $context)) {
            $flagmode = QUESTION_FLAGSHIDDEN;
        } else if ($attempt->userid == $USER->id) {
            $flagmode = QUESTION_FLAGSEDITABLE;
        } else {
            $flagmode = QUESTION_FLAGSSHOWN;
        }
    }
    return $flagmode;
}

/**
 * Determine render options
 *
 * @param int $reviewoptions
 * @param object $state
 */
function quiz_get_renderoptions($quiz, $attempt, $context, $state) {
    $reviewoptions = $quiz->review;
    $options = new stdClass;

    $options->flags = quiz_get_flag_option($attempt, $context);

    // Show the question in readonly (review) mode if the question is in
    // the closed state
    $options->readonly = question_state_is_closed($state);

    // Show feedback once the question has been graded (if allowed by the quiz)
    $options->feedback = question_state_is_graded($state) && ($reviewoptions & QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_IMMEDIATELY);

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
    $options->history = false;

    return $options;
}

/**
 * Determine review options
 *
 * @param object $quiz the quiz instance.
 * @param object $attempt the attempt in question.
 * @param $context the quiz module context.
 *
 * @return object an object with boolean fields responses, scores, feedback,
 *          correct_responses, solutions and general feedback
 */
function quiz_get_reviewoptions($quiz, $attempt, $context) {
    global $USER;

    $options = new stdClass;
    $options->readonly = true;

    $options->flags = quiz_get_flag_option($attempt, $context);

    // Provide the links to the question review and comment script
    if (!empty($attempt->id)) {
        $options->questionreviewlink = '/mod/quiz/reviewquestion.php?attempt=' . $attempt->id;
    }

    // Show a link to the comment box only for closed attempts
    if (!empty($attempt->id) && $attempt->timefinish &&
            has_capability('mod/quiz:grade', $context)) {
        $options->questioncommentlink = new moodle_url('/mod/quiz/comment.php', array('attempt' => $attempt->id));
    }

    // Whether to display a response history.
    $canviewreports = has_capability('mod/quiz:viewreports', $context);
    $options->history = ($canviewreports && !$attempt->preview) ? 'all' : 'graded';

    if ($canviewreports && has_capability('moodle/grade:viewhidden', $context) && !$attempt->preview) {
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
function quiz_get_combined_reviewoptions($quiz, $attempts, $context) {
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
 * @return bool
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
    $eventdata = new stdClass();
    $eventdata->component        = 'mod_quiz';
    $eventdata->name             = 'confirmation';
    $eventdata->notification      = 1;

    $eventdata->userfrom          = get_admin();
    $eventdata->userto            = $USER;
    $eventdata->subject           = $subject;
    $eventdata->fullmessage       = $body;
    $eventdata->fullmessageformat = FORMAT_PLAIN;
    $eventdata->fullmessagehtml   = '';

    $eventdata->smallmessage      = get_string('emailconfirmsmall', 'quiz', $a);
    $eventdata->contexturl        = $a->quizurl;
    $eventdata->contexturlname    = $a->quizname;

    return (bool)message_send($eventdata); // returns message id or false
}

/**
 * Sends notification messages to the interested parties that assign the role capability
 *
 * @param object $recipient user object of the intended recipient
 * @param stdClass $a associative array of replaceable fields for the templates
 *
 * @return bool
 */
function quiz_send_notification($recipient, $a) {

    global $USER;

    // recipient info for template
    $a->username = fullname($recipient);
    $a->userusername = $recipient->username;
    //$a->userusername = $recipient->username;

    // fetch the subject and body from strings
    $subject = get_string('emailnotifysubject', 'quiz', $a);
    $body = get_string('emailnotifybody', 'quiz', $a);

    // send email and analyse result
    $eventdata = new stdClass();
    $eventdata->component        = 'mod_quiz';
    $eventdata->name             = 'submission';
    $eventdata->notification      = 1;

    $eventdata->userfrom          = $USER;
    $eventdata->userto            = $recipient;
    $eventdata->subject           = $subject;
    $eventdata->fullmessage       = $body;
    $eventdata->fullmessageformat = FORMAT_PLAIN;
    $eventdata->fullmessagehtml   = '';

    $eventdata->smallmessage      = get_string('emailnotifysmall', 'quiz', $a);
    $eventdata->contexturl        = $a->quizreviewurl;
    $eventdata->contexturlname    = $a->quizname;

    return (bool)message_send($eventdata);
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
    $emailresult = array('good' => 0, 'fail' => 0);

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
    $notifyfields = 'u.id, u.username, u.firstname, u.lastname, u.email, u.lang, u.timezone, u.mailformat, u.maildisplay';
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
        $a->quizreporturl = $CFG->wwwroot . '/mod/quiz/report.php?id=' . $cm->id;
        $a->quizreportlink = '<a href="' . $a->quizreporturl . '">' . format_string($quiz->name) . ' report</a>';
        $a->quizreviewurl = $CFG->wwwroot . '/mod/quiz/review.php?attempt=' . $attempt->id;
        $a->quizreviewlink = '<a href="' . $a->quizreviewurl . '">' . format_string($quiz->name) . ' review</a>';
        $a->quizurl = $CFG->wwwroot . '/mod/quiz/view.php?id=' . $cm->id;
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
            }
        }
    }

    // log errors sending emails if any
    if (! empty($emailresult['fail'])) {
        debugging('quiz_send_notification_emails:: '.$emailresult['fail'].' email(s) failed to be sent.', DEBUG_DEVELOPER);
    }

    // return the number of successfully sent emails
    return $emailresult['good'];
}

/**
 * Clean the question layout from various possible anomalies:
 * - Remove consecutive ","'s
 * - Remove duplicate question id's
 * - Remove extra "," from beginning and end
 * - Finally, add a ",0" in the end if there is none
 *
 * @param $string $layout the quiz layout to clean up, usually from $quiz->questions.
 * @param boolean $removeemptypages If true, remove empty pages from the quiz. False by default.
 * @return $string the cleaned-up layout
 */
function quiz_clean_layout($layout, $removeemptypages = false){
    // Remove duplicate "," (or triple, or...)
    $layout = preg_replace('/,{2,}/', ',', trim($layout, ','));

    // Remove duplicate question ids
    $layout = explode(',', $layout);
    $cleanerlayout = array();
    $seen = array();
    foreach ($layout as $item) {
        if ($item == 0) {
            $cleanerlayout[] = '0';
        } else if (!in_array($item, $seen)) {
            $cleanerlayout[] = $item;
            $seen[] = $item;
        }
    }

    if ($removeemptypages) {
        // Avoid duplicate page breaks
        $layout = $cleanerlayout;
        $cleanerlayout = array();
        $stripfollowingbreaks = true; // Ensure breaks are stripped from the start.
        foreach ($layout as $item) {
            if ($stripfollowingbreaks && $item == 0) {
                continue;
            }
            $cleanerlayout[] = $item;
            $stripfollowingbreaks = $item == 0;
        }
    }

    // Add a page break at the end if there is none
    if (end($cleanerlayout) !== '0') {
        $cleanerlayout[] = '0';
    }

    return implode(',', $cleanerlayout);
}
/**
 * Print a quiz error message. This is a thin wrapper around print_error, for convinience.
 *
 * @param mixed $quiz either the quiz object, or the interger quiz id.
 * @param string $errorcode the name of the string from quiz.php to print.
 * @param object $a any extra data required by the error string.
 */
function quiz_error($quiz, $errorcode, $a = null) {
    global $CFG;
    if (is_object($quiz)) {
        $quiz = $quiz->id;
    }
    print_error($errorcode, 'quiz', $CFG->wwwroot . '/mod/quiz/view.php?q=' . $quiz, $a);
}

/**
 * Checks if browser is safe browser
 *
 * @return true, if browser is safe browser else false
*/
function quiz_check_safe_browser() {
    return strpos($_SERVER['HTTP_USER_AGENT'], "SEB") !== false;
}

function quiz_get_js_module() {
    global $PAGE;
    return array(
        'name' => 'mod_quiz',
        'fullpath' => '/mod/quiz/module.js',
        'requires' => array('base', 'dom', 'event-delegate', 'event-key', 'core_question_engine'),
        'strings' => array(
            array('timesup', 'quiz'),
            array('functiondisabledbysecuremode', 'quiz'),
            array('flagged', 'question'),
        ),
    );
}
