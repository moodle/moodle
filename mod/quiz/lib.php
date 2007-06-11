<?php  // $Id$
/**
* Library of functions for the quiz module.
*
* This contains functions that are called also from outside the quiz module
* Functions that are only called by the quiz module itself are in {@link locallib.php}
* @version $Id$
* @author Martin Dougiamas and many others.
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package quiz
*/

require_once($CFG->libdir.'/pagelib.php');
require_once($CFG->libdir.'/questionlib.php');

/// CONSTANTS ///////////////////////////////////////////////////////////////////

/**#@+
 * The different review options are stored in the bits of $quiz->review
 * These constants help to extract the options
 */
/**
 * The first 6 bits refer to the time immediately after the attempt
 */
define('QUIZ_REVIEW_IMMEDIATELY', 0x3f);
/**
 * the next 6 bits refer to the time after the attempt but while the quiz is open
 */
define('QUIZ_REVIEW_OPEN', 0xfc0);
/**
 * the final 6 bits refer to the time after the quiz closes
 */
define('QUIZ_REVIEW_CLOSED', 0x3f000);

// within each group of 6 bits we determine what should be shown
define('QUIZ_REVIEW_RESPONSES',   1*0x1041); // Show responses
define('QUIZ_REVIEW_SCORES',      2*0x1041); // Show scores
define('QUIZ_REVIEW_FEEDBACK',    4*0x1041); // Show feedback
define('QUIZ_REVIEW_ANSWERS',     8*0x1041); // Show correct answers
// Some handling of worked solutions is already in the code but not yet fully supported
// and not switched on in the user interface.
define('QUIZ_REVIEW_SOLUTIONS',  16*0x1041); // Show solutions
define('QUIZ_REVIEW_GENERALFEEDBACK', 32*0x1041); // Show general feedback
/**#@-*/

/**
 * If start and end date for the quiz are more than this many seconds apart
 * they will be represented by two separate events in the calendar
 */
define("QUIZ_MAX_EVENT_LENGTH", "432000");   // 5 days maximum

/// FUNCTIONS ///////////////////////////////////////////////////////////////////

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod.html) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $quiz the data that came from the form.
 * @return mixed the id of the new instance on success,
 *          false or a string error message on failure.
 */
function quiz_add_instance($quiz) {

    // Process the options from the form.
    $quiz->created = time();
    $quiz->questions = '';
    $result = quiz_process_options($quiz);
    if ($result && is_string($result)) {
        return $result;
    }

    // Try to store it in the database.
    if (!$quiz->id = insert_record("quiz", $quiz)) {
        return false;
    }

    // Do the processing required after an add or an update.
    quiz_after_add_or_update($quiz);

    return $quiz->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod.html) this function
 * will update an existing instance with new data.
 *
 * @param object $quiz the data that came from the form.
 * @return mixed true on success, false or a string error message on failure.
 */
function quiz_update_instance($quiz) {

    // Process the options from the form.
    $result = quiz_process_options($quiz);
    if ($result && is_string($result)) {
        return $result;
    }

    // Update the database.
    $quiz->id = $quiz->instance;
    if (!update_record("quiz", $quiz)) {
        return false;  // some error occurred
    }

    // Do the processing required after an add or an update.
    quiz_after_add_or_update($quiz);

    // Delete any previous preview attempts
    delete_records('quiz_attempts', 'preview', '1', 'quiz', $quiz->id);

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
            // TODO: this should use the delete_attempt($attempt->uniqueid) function in questionlib.php
            if (! delete_records("question_states", "attempt", "$attempt->uniqueid")) {
                $result = false;
            }
            if (! delete_records("question_sessions", "attemptid", "$attempt->uniqueid")) {
                $result = false;
            }
        }
    }

    $tables_to_purge = array(
        'quiz_attempts' => 'quiz',
        'quiz_grades' => 'quiz',
        'quiz_question_instances' => 'quiz',
        'quiz_grades' => 'quiz',
        'quiz_feedback' => 'quizid',
        'quiz' => 'id'
    );
    foreach ($tables_to_purge as $table => $keyfield) {
        if (!delete_records($table, $keyfield, $quiz->id)) {
            $result = false;
        }
    }

    $pagetypes = page_import_types('mod/quiz/');
    foreach($pagetypes as $pagetype) {
        if(!delete_records('block_instance', 'pageid', $quiz->id, 'pagetype', $pagetype)) {
            $result = false;
        }
    }

    if ($events = get_records_select('event', "modulename = 'quiz' and instance = '$quiz->id'")) {
        foreach($events as $event) {
            delete_event($event->id);
        }
    }

    quiz_grade_item_delete($quiz);

    return $result;
}


function quiz_user_outline($course, $user, $mod, $quiz) {
/// Return a small object with summary information about what a
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description
    if ($grade = get_record('quiz_grades', 'userid', $user->id, 'quiz', $quiz->id)) {

        $result = new stdClass;
        if ((float)$grade->grade) {
            $result->info = get_string('grade').':&nbsp;'.round($grade->grade, $quiz->decimalpoints);
        }
        $result->time = $grade->timemodified;
        return $result;
    }
    return NULL;

}


function quiz_user_complete($course, $user, $mod, $quiz) {
/// Print a detailed representation of what a  user has done with
/// a given particular instance of this module, for user activity reports.

    if ($attempts = get_records_select('quiz_attempts', "userid='$user->id' AND quiz='$quiz->id'", 'attempt ASC')) {
        if ($quiz->grade  and $quiz->sumgrades && $grade = get_record('quiz_grades', 'userid', $user->id, 'quiz', $quiz->id)) {
            echo get_string('grade').': '.round($grade->grade, $quiz->decimalpoints).'/'.$quiz->grade.'<br />';
        }
        foreach ($attempts as $attempt) {
            echo get_string('attempt', 'quiz').' '.$attempt->attempt.': ';
            if ($attempt->timefinish == 0) {
                print_string('unfinished');
            } else {
                echo round($attempt->sumgrades, $quiz->decimalpoints).'/'.$quiz->sumgrades;
            }
            echo ' - '.userdate($attempt->timemodified).'<br />';
        }
    } else {
       print_string('noattempts', 'quiz');
    }

    return true;
}


function quiz_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such
/// as sending out mail, toggling flags etc ...

    global $CFG;

    return true;
}


/**
 * Return grade for given user or all users.
 *
 * @param int $quizid id of quiz
 * @param int $userid optional user id, 0 means all users
 * @return array array of grades, false if none
 */
function quiz_get_user_grades($quiz, $userid=0) {
    global $CFG;

    $user = $userid ? "AND u.id = $userid" : "";

    $sql = "SELECT u.id, u.id AS userid, g.grade AS gradevalue
              FROM {$CFG->prefix}user u, {$CFG->prefix}quiz_grades g
             WHERE u.id = g.userid AND g.quiz = $quiz->id
                   $user";

    return get_records_sql($sql);
}

/**
 * Update grades in central gradebook
 *
 * @param object $quiz null means all quizs
 * @param int $userid specific user only, 0 mean all
 */
function quiz_update_grades($quiz=null, $userid=0, $nullifnone=true) {
    global $CFG;
    if (!function_exists('grade_update')) { //workaround for buggy PHP versions
        require_once($CFG->libdir.'/gradelib.php');
    }

    if ($quiz != null) {
        if ($grades = quiz_get_user_grades($quiz, $userid)) {
            grade_update('mod/quiz', $quiz->course, 'mod', 'quiz', $quiz->id, 0, $grades);

        } else if ($userid and $nullifnone) {
            $grade = new object();
            $grade->itemid     = $quiz->id;
            $grade->userid     = $userid;
            $grade->gradevalue = NULL;
            grade_update('mod/quiz', $quiz->course, 'mod', 'quiz', $quiz->id, 0, $grade);
        }

    } else {
        $sql = "SELECT a.*, cm.idnumber as cmidnumber, a.course as courseid
                  FROM {$CFG->prefix}quiz a, {$CFG->prefix}course_modules cm, {$CFG->prefix}modules m
                 WHERE m.name='quiz' AND m.id=cm.module AND cm.instance=a.id";
        if ($rs = get_recordset_sql($sql)) {
            if ($rs->RecordCount() > 0) {
                while ($quiz = rs_fetch_next_record($rs)) {
                    quiz_grade_item_update($quiz);
                    if ($quiz->grade != 0) {
                        quiz_update_grades($quiz, 0, false);
                    }
                }
            }
            rs_close($rs);
        }
    }
}

/**
 * Create grade item for given quiz
 *
 * @param object $quiz object with extra cmidnumber
 * @return int 0 if ok, error code otherwise
 */
function quiz_grade_item_update($quiz) {
    global $CFG;
    if (!function_exists('grade_update')) { //workaround for buggy PHP versions
        require_once($CFG->libdir.'/gradelib.php');
    }

    if (array_key_exists('cmidnumber', $quiz)) { //it may not be always present
        $params = array('itemname'=>$quiz->name, 'idnumber'=>$quiz->cmidnumber);
    } else {
        $params = array('itemname'=>$quiz->name);
    }

    if ($quiz->grade > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $quiz->grade;
        $params['grademin']  = 0;

    } else {
        $params['gradetype'] = GRADE_TYPE_NONE;
    }

    return grade_update('mod/quiz', $quiz->course, 'mod', 'quiz', $quiz->id, 0, NULL, $params);
}

/**
 * Delete grade item for given quiz
 *
 * @param object $quiz object
 * @return object quiz
 */
function quiz_grade_item_delete($quiz) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    return grade_update('mod/quiz', $quiz->course, 'mod', 'quiz', $quiz->id, 0, NULL, array('deleted'=>1));
}


function quiz_get_participants($quizid) {
/// Returns an array of users who have data in a given quiz
/// (users with records in quiz_attempts and quiz_question_versions)

    global $CFG;

    //Get users from attempts
    $us_attempts = get_records_sql("SELECT DISTINCT u.id, u.id
                                    FROM {$CFG->prefix}user u,
                                         {$CFG->prefix}quiz_attempts a
                                    WHERE a.quiz = '$quizid' and
                                          u.id = a.userid");

    //Get users from question_versions
    $us_versions = get_records_sql("SELECT DISTINCT u.id, u.id
                                    FROM {$CFG->prefix}user u,
                                         {$CFG->prefix}quiz_question_versions v
                                    WHERE v.quiz = '$quizid' and
                                          u.id = v.userid");

    //Add us_versions to us_attempts
    if ($us_versions) {
        foreach ($us_versions as $us_version) {
            $us_attempts[$us_version->id] = $us_version;
        }
    }
    //Return us_attempts array (it contains an array of unique users)
    return ($us_attempts);

}

function quiz_refresh_events($courseid = 0) {
// This horrible function only seems to be called from mod/quiz/db/[dbtype].php.

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
        if (empty($groupid) || groups_is_member($groupid, $quiz->userid)) {

          $tmpactivity = new Object;

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
    global $CFG;

    echo '<table border="0" cellpadding="3" cellspacing="0">';

    echo "<tr><td class=\"forumpostpicture\" width=\"35\" valign=\"top\">";
    print_user_picture($activity->user->userid, $course, $activity->user->picture);
    echo "</td><td style=\"width:100%;\"><font size=\"2\">";

    if ($detail) {
        echo "<img src=\"$CFG->modpixpath/$activity->type/icon.gif\" ".
             "class=\"icon\" alt=\"$activity->type\" />  ";
        echo "<a href=\"$CFG->wwwroot/mod/quiz/view.php?id=" . $activity->instance . "\">"
             . format_string($activity->name,true) . "</a> - ";

    }

    if (has_capability('mod/quiz:grade', get_context_instance(CONTEXT_MODULE, $course))) {
        $grades = "(" .  $activity->content->sumgrades . " / " . $activity->content->maxgrade . ") ";
        echo "<a href=\"$CFG->wwwroot/mod/quiz/review.php?q="
             . $activity->instance . "&amp;attempt="
             . $activity->content->attemptid . "\">" . $grades . "</a> ";

        echo  get_string("attempt", "quiz") . " - " . $activity->content->attempt . "<br />";
    }
    echo "<a href=\"$CFG->wwwroot/user/view.php?id="
         . $activity->user->userid . "&amp;course=$course\">"
         . $activity->user->fullname . "</a> ";

    echo " - " . userdate($activity->timestamp);

    echo "</font></td></tr>";
    echo "</table>";

    return;
}

/**
 * Pre-process the quiz options form data, making any necessary adjustments.
 * Called by add/update instance in this file, and the save code in admin/module.php.
 * 
 * @param object $quiz The variables set on the form.
 */
function quiz_process_options(&$quiz) {
    $quiz->timemodified = time();

    // Quiz open time.
    if (empty($quiz->timeopen)) {
        $quiz->preventlate = 0;
    }

    // Quiz name. (Make up a default if one was not given.)
    if (empty($quiz->name)) {
        if (empty($quiz->intro)) {
            $quiz->name = get_string('modulename', 'quiz');
        } else {
            $quiz->name = shorten_text(strip_tags($quiz->intro));
        }
    }
    $quiz->name = trim($quiz->name);

    // Time limit. (Get rid of it if the checkbox was not ticked.)
    if (empty($quiz->timelimitenable)) {
        $quiz->timelimit = 0;
    }
    $quiz->timelimit = round($quiz->timelimit);

    // Password field - different in form to stop browsers that remember passwords
    // getting confused.
    $quiz->password = $quiz->quizpassword;
    unset($quiz->quizpassword);

    // Quiz feedback
    if (isset($quiz->feedbacktext)) {
        // Clean up the boundary text.
        for ($i = 0; $i < count($quiz->feedbacktext); $i += 1) {
            if (empty($quiz->feedbacktext[$i])) {
                $quiz->feedbacktext[$i] = '';
            } else {
                $quiz->feedbacktext[$i] = trim($quiz->feedbacktext[$i]);
            }
        }
    
        // Check the boundary value is a number or a percentage, and in range.
        $i = 0;
        while (!empty($quiz->feedbackboundaries[$i])) {
            $boundary = trim($quiz->feedbackboundaries[$i]);
            if (!is_numeric($boundary)) {
                if (strlen($boundary) > 0 && $boundary[strlen($boundary) - 1] == '%') {
                    $boundary = trim(substr($boundary, 0, -1));
                    if (is_numeric($boundary)) {
                        $boundary = $boundary * $quiz->grade / 100.0;
                    } else {
                        return get_string('feedbackerrorboundaryformat', 'quiz', $i + 1);
                    }
                }
            }
            if ($boundary <= 0 || $boundary >= $quiz->grade) {
                return get_string('feedbackerrorboundaryoutofrange', 'quiz', $i + 1);
            }
            if ($i > 0 && $boundary >= $quiz->feedbackboundaries[$i - 1]) {
                return get_string('feedbackerrororder', 'quiz', $i + 1);
            }
            $quiz->feedbackboundaries[$i] = $boundary;
            $i += 1;
        }
        $numboundaries = $i;
    
        // Check there is nothing in the remaining unused fields.
        for ($i = $numboundaries; $i < count($quiz->feedbackboundaries); $i += 1) {
            if (!empty($quiz->feedbackboundaries[$i]) && trim($quiz->feedbackboundaries[$i]) != '') {
                return get_string('feedbackerrorjunkinboundary', 'quiz', $i + 1);
            }
        }
        for ($i = $numboundaries + 1; $i < count($quiz->feedbacktext); $i += 1) {
            if (!empty($quiz->feedbacktext[$i]) && trim($quiz->feedbacktext[$i]) != '') {
                return get_string('feedbackerrorjunkinfeedback', 'quiz', $i + 1);
            }
        }
        $quiz->feedbackboundaries[-1] = $quiz->grade + 1; // Needs to be bigger than $quiz->grade because of '<' test in quiz_feedback_for_grade().
        $quiz->feedbackboundaries[$numboundaries] = 0;
        $quiz->feedbackboundarycount = $numboundaries;
    }

    // Settings that get combined to go into the optionflags column.
    $quiz->optionflags = 0;
    if (!empty($quiz->adaptive)) {
        $quiz->optionflags |= QUESTION_ADAPTIVE;
    }

    // Settings that get combined to go into the review column.
    $review = 0;
    if (isset($quiz->responsesimmediately)) {
        $review += (QUIZ_REVIEW_RESPONSES & QUIZ_REVIEW_IMMEDIATELY);
        unset($quiz->responsesimmediately);
    }
    if (isset($quiz->responsesopen)) {
        $review += (QUIZ_REVIEW_RESPONSES & QUIZ_REVIEW_OPEN);
        unset($quiz->responsesopen);
    }
    if (isset($quiz->responsesclosed)) {
        $review += (QUIZ_REVIEW_RESPONSES & QUIZ_REVIEW_CLOSED);
        unset($quiz->responsesclosed);
    }

    if (isset($quiz->scoreimmediately)) {
        $review += (QUIZ_REVIEW_SCORES & QUIZ_REVIEW_IMMEDIATELY);
        unset($quiz->scoreimmediately);
    }
    if (isset($quiz->scoreopen)) {
        $review += (QUIZ_REVIEW_SCORES & QUIZ_REVIEW_OPEN);
        unset($quiz->scoreopen);
    }
    if (isset($quiz->scoreclosed)) {
        $review += (QUIZ_REVIEW_SCORES & QUIZ_REVIEW_CLOSED);
        unset($quiz->scoreclosed);
    }

    if (isset($quiz->feedbackimmediately)) {
        $review += (QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_IMMEDIATELY);
        unset($quiz->feedbackimmediately);
    }
    if (isset($quiz->feedbackopen)) {
        $review += (QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_OPEN);
        unset($quiz->feedbackopen);
    }
    if (isset($quiz->feedbackclosed)) {
        $review += (QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_CLOSED);
        unset($quiz->feedbackclosed);
    }

    if (isset($quiz->answersimmediately)) {
        $review += (QUIZ_REVIEW_ANSWERS & QUIZ_REVIEW_IMMEDIATELY);
        unset($quiz->answersimmediately);
    }
    if (isset($quiz->answersopen)) {
        $review += (QUIZ_REVIEW_ANSWERS & QUIZ_REVIEW_OPEN);
        unset($quiz->answersopen);
    }
    if (isset($quiz->answersclosed)) {
        $review += (QUIZ_REVIEW_ANSWERS & QUIZ_REVIEW_CLOSED);
        unset($quiz->answersclosed);
    }

    if (isset($quiz->solutionsimmediately)) {
        $review += (QUIZ_REVIEW_SOLUTIONS & QUIZ_REVIEW_IMMEDIATELY);
        unset($quiz->solutionsimmediately);
    }
    if (isset($quiz->solutionsopen)) {
        $review += (QUIZ_REVIEW_SOLUTIONS & QUIZ_REVIEW_OPEN);
        unset($quiz->solutionsopen);
    }
    if (isset($quiz->solutionsclosed)) {
        $review += (QUIZ_REVIEW_SOLUTIONS & QUIZ_REVIEW_CLOSED);
        unset($quiz->solutionsclosed);
    }

    if (isset($quiz->generalfeedbackimmediately)) {
        $review += (QUIZ_REVIEW_GENERALFEEDBACK & QUIZ_REVIEW_IMMEDIATELY);
        unset($quiz->solutionsimmediately);
    }
    if (isset($quiz->generalfeedbackopen)) {
        $review += (QUIZ_REVIEW_GENERALFEEDBACK & QUIZ_REVIEW_OPEN);
        unset($quiz->solutionsopen);
    }
    if (isset($quiz->generalfeedbackclosed)) {
        $review += (QUIZ_REVIEW_GENERALFEEDBACK & QUIZ_REVIEW_CLOSED);
        unset($quiz->solutionsclosed);
    }

    $quiz->review = $review;
}

/**
 * This function is called at the end of quiz_add_instance
 * and quiz_update_instance, to do the common processing.
 *
 * @param object $quiz the quiz object.
 */
function quiz_after_add_or_update($quiz) {

    // Save the feedback
    delete_records('quiz_feedback', 'quizid', $quiz->id);

    for ($i = 0; $i <= $quiz->feedbackboundarycount; $i += 1) {
        $feedback = new stdClass;
        $feedback->quizid = $quiz->id;
        $feedback->feedbacktext = $quiz->feedbacktext[$i];
        $feedback->mingrade = $quiz->feedbackboundaries[$i];
        $feedback->maxgrade = $quiz->feedbackboundaries[$i - 1];
        if (!insert_record('quiz_feedback', $feedback, false)) {
            return "Could not save quiz feedback.";
        }
    }


    // Update the events relating to this quiz.
    // This is slightly inefficient, deleting the old events and creating new ones. However,
    // there are at most two events, and this keeps the code simpler.
    if ($events = get_records_select('event', "modulename = 'quiz' and instance = '$quiz->id'")) {
        foreach($events as $event) {
            delete_event($event->id);
        }
    }

    $event = new stdClass;
    $event->description = $quiz->intro;
    $event->courseid    = $quiz->course;
    $event->groupid     = 0;
    $event->userid      = 0;
    $event->modulename  = 'quiz';
    $event->instance    = $quiz->id;
    $event->timestart   = $quiz->timeopen;
    $event->timeduration = $quiz->timeclose - $quiz->timeopen;
    $event->visible     = instance_is_visible('quiz', $quiz);
    $event->eventtype   = 'open';

    if ($quiz->timeclose and $quiz->timeopen and $event->timeduration <= QUIZ_MAX_EVENT_LENGTH) {
        // Single event for the whole quiz.
        $event->name = $quiz->name;
        add_event($event);
    } else {
        // Separate start and end events.
        $event->timeduration  = 0;
        if ($quiz->timeopen) {
            $event->name = $quiz->name.' ('.get_string('quizopens', 'quiz').')';
            add_event($event);
            unset($event->id); // So we can use the same object for the close event.
        }
        if ($quiz->timeclose) {
            $event->name      = $quiz->name.' ('.get_string('quizcloses', 'quiz').')';
            $event->timestart = $quiz->timeclose;
            $event->eventtype = 'close';
            add_event($event);
        }
    }

    //update related grade item
    quiz_grade_item_update(stripslashes_recursive($quiz));
}

function quiz_get_view_actions() {
    return array('view','view all','report');
}

function quiz_get_post_actions() {
    return array('attempt','editquestions','review','submit');
}

/**
 * Returns an array of names of quizzes that use this question
 *
 * TODO: write this
 * @param object $questionid
 * @return array of strings
 */
function quiz_question_list_instances($questionid) {
    return array();
}

?>