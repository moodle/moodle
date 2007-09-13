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
define('QUIZ_REVIEW_IMMEDIATELY', 64-1);
/**
 * the next 6 bits refer to the time after the attempt but while the quiz is open
 */
define('QUIZ_REVIEW_OPEN', 4096-64);
/**
 * the final 6 bits refer to the time after the quiz closes
 */
define('QUIZ_REVIEW_CLOSED', 262144-4096);

// within each group of 6 bits we determine what should be shown
define('QUIZ_REVIEW_RESPONSES', 1+64+4096);    // Show responses
define('QUIZ_REVIEW_SCORES', 2*4161);   // Show scores
define('QUIZ_REVIEW_FEEDBACK', 4*4161); // Show feedback
define('QUIZ_REVIEW_ANSWERS', 8*4161);  // Show correct answers
// Some handling of worked solutions is already in the code but not yet fully supported
// and not switched on in the user interface.
define('QUIZ_REVIEW_SOLUTIONS', 16*4161);  // Show solutions
// the 6th bit is as yet unused
/**#@-*/

/**
 * If start and end date for the quiz are more than this many seconds apart
 * they will be represented by two separate events in the calendar
 */
define("QUIZ_MAX_EVENT_LENGTH", "432000");   // 5 days maximum

/// FUNCTIONS ///////////////////////////////////////////////////////////////////

function quiz_add_instance($quiz) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will create a new instance and return the id number
/// of the new instance.

    quiz_process_options($quiz);

    $quiz->created      = time();
    $quiz->timemodified = time();
    // The following is adapted from the assignment module
    if (empty($quiz->dueenable)) {
        $quiz->timeclose = 0;
    } else {
        $quiz->timeclose = make_timestamp($quiz->dueyear, $quiz->duemonth, 
                                              $quiz->dueday, $quiz->duehour, 
                                              $quiz->dueminute);
    }
    if (empty($quiz->availableenable)) {
        $quiz->timeopen = 0;
        $quiz->preventlate = 0;
    } else {
        $quiz->timeopen = make_timestamp($quiz->availableyear, $quiz->availablemonth, 
                                                    $quiz->availableday, $quiz->availablehour, 
                                                    $quiz->availableminute);
    }

    $quiz->timelimit = round($quiz->timelimit);

    if (empty($quiz->name)) {
        if (empty($quiz->intro)) {
            $quiz->name = get_string('modulename', 'quiz');
        } else {
            $quiz->name = strip_tags($quiz->intro);
        }
    }
    $quiz->name = trim($quiz->name);
    $quiz->timelimit = round($quiz->timelimit);
    $quiz->questions = '';
    if (!$quiz->id = insert_record("quiz", $quiz)) {
        return false;  // some error occurred
    }

    if (isset($quiz->optionsettingspref)) {
        set_user_preference('quiz_optionsettingspref', $quiz->optionsettingspref);
    }

    delete_records('event', 'modulename', 'quiz', 'instance', $quiz->id);  // Just in case

    unset($event);
    $event->description = $quiz->intro;
    $event->courseid    = $quiz->course;
    $event->groupid     = 0;
    $event->userid      = 0;
    $event->modulename  = 'quiz';
    $event->instance    = $quiz->id;
    $event->timestart   = $quiz->timeopen;
    $event->visible     = instance_is_visible('quiz', $quiz);

    if ($quiz->timeclose and $quiz->timeopen) {
        // we have both a start and an end date
        $event->eventtype   = 'open';
        $event->timeduration = ($quiz->timeclose - $quiz->timeopen);

        if ($event->timeduration > QUIZ_MAX_EVENT_LENGTH) {  /// Long durations create two events
    
            $event->name          = $quiz->name.' ('.get_string('quizopens', 'quiz').')';
            $event->timeduration  = 0;
            add_event($event);
    
            $event->timestart    = $quiz->timeclose;
            $event->eventtype    = 'close';
            $event->name         = $quiz->name.' ('.get_string('quizcloses', 'quiz').')';
            unset($event->id);
            add_event($event);
        } else { // single event with duration
            $event->name        = $quiz->name;
            add_event($event);
        }
    } elseif ($quiz->timeopen) { // only an open date
        $event->name          = $quiz->name.' ('.get_string('quizopens', 'quiz').')';
        $event->eventtype   = 'open';
        $event->timeduration = 0;
        add_event($event);
    } elseif ($quiz->timeclose) { // only a closing date
        $event->name         = $quiz->name.' ('.get_string('quizcloses', 'quiz').')';
        $event->timestart    = $quiz->timeclose;
        $event->eventtype    = 'close';
        $event->timeduration = 0;
        add_event($event);
    }

    return $quiz->id;
}


function quiz_update_instance($quiz) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html or edit.php) this function
/// will update an existing instance with new data.

    quiz_process_options($quiz);

    $quiz->timemodified = time();
    // The following is adapted from the assignment module
    if (empty($quiz->dueenable)) {
        $quiz->timeclose = 0;
    } else {
        $quiz->timeclose = make_timestamp($quiz->dueyear, $quiz->duemonth, 
                                              $quiz->dueday, $quiz->duehour, 
                                              $quiz->dueminute);
    }
    if (empty($quiz->availableenable)) {
        $quiz->timeopen = 0;
        $quiz->preventlate = 0;
    } else {
        $quiz->timeopen = make_timestamp($quiz->availableyear, $quiz->availablemonth, 
                                                    $quiz->availableday, $quiz->availablehour, 
                                                    $quiz->availableminute);
    }

    $quiz->timelimit = round($quiz->timelimit);

    $quiz->id = $quiz->instance;
    if (!update_record("quiz", $quiz)) {
        return false;  // some error occurred
    }

    // Delete any preview attempts
    delete_records('quiz_attempts', 'preview', '1', 'quiz', $quiz->id);

    if (isset($quiz->optionsettingspref)) {
        set_user_preference('quiz_optionsettingspref', $quiz->optionsettingspref);
    }

    // currently this code deletes all existing events and adds new ones
    // this should be improved to update existing events only
    if ($events = get_records_select('event', "modulename = 'quiz' and instance = '$quiz->id'")) {
        foreach($events as $event) {
            delete_event($event->id);
        }
    }

    unset($event);
    $event->description = $quiz->intro;
    $event->courseid    = $quiz->course;
    $event->groupid     = 0;
    $event->userid      = 0;
    $event->modulename  = 'quiz';
    $event->instance    = $quiz->id;
    $event->timestart   = $quiz->timeopen;
    $event->visible     = instance_is_visible('quiz', $quiz);
    if ($quiz->timeclose and $quiz->timeopen) {
        // we have both a start and an end date
        $event->eventtype   = 'open';
        $event->timeduration = ($quiz->timeclose - $quiz->timeopen);

        if ($event->timeduration > QUIZ_MAX_EVENT_LENGTH) {  /// Long durations create two events
    
            $event->name          = $quiz->name.' ('.get_string('quizopens', 'quiz').')';
            $event->timeduration  = 0;
            add_event($event);
    
            $event->timestart    = $quiz->timeclose;
            $event->eventtype    = 'close';
            $event->name         = $quiz->name.' ('.get_string('quizcloses', 'quiz').')';
            unset($event->id);
            add_event($event);
        } else { // single event with duration
            $event->name        = $quiz->name;
            add_event($event);
        }
    } elseif ($quiz->timeopen) { // only an open date
        $event->name          = $quiz->name.' ('.get_string('quizopens', 'quiz').')';
        $event->eventtype   = 'open';
        $event->timeduration = 0;
        add_event($event);
    } elseif ($quiz->timeclose) { // only a closing date
        $event->name         = $quiz->name.' ('.get_string('quizcloses', 'quiz').')';
        $event->timestart    = $quiz->timeclose;
        $event->eventtype    = 'close';
        $event->timeduration = 0;
        add_event($event);
    }

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
            // TODO: this should use function in questionlib.php
            if (! delete_records("question_states", "attempt", "$attempt->uniqueid")) {
                $result = false;
            }
            if (! delete_records("question_sessions", "attemptid", "$attempt->uniqueid")) {
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

    if (! delete_records("quiz_question_instances", "quiz", "$quiz->id")) {
        $result = false;
    }

    if (! delete_records("quiz", "id", "$quiz->id")) {
        $result = false;
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

    return $result;
}


function quiz_user_outline($course, $user, $mod, $quiz) {
/// Return a small object with summary information about what a
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description
    if ($grade = get_record('quiz_grades', 'userid', $user->id, 'quiz', $quiz->id)) {

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

function quiz_grades($quizid) {
/// Must return an array of grades, indexed by user, and a max grade.

    $quiz = get_record('quiz', 'id', intval($quizid));
    if (empty($quiz) || empty($quiz->grade)) {
        return NULL;
    }

    $return->grades = get_records_menu('quiz_grades', 'quiz', $quiz->id, '', 'userid, grade');
    $return->maxgrade = get_field('quiz', 'grade', 'id', $quiz->id);
    return $return;
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
        if (empty($groupid) || ismember($groupid, $quiz->userid)) {

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
    echo "</td><td width=\"100%\"><font size=\"2\">";

    if ($detail) {
        echo "<img src=\"$CFG->modpixpath/$activity->type/icon.gif\" ".
             "height=\"16\" width=\"16\" alt=\"$activity->type\" />  ";
        echo "<a href=\"$CFG->wwwroot/mod/quiz/view.php?id=" . $activity->instance . "\">"
             . format_string($activity->name,true) . "</a> - ";

    }

    if (isteacher($course)) {
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
* Pre-process the options form data
*
* Encode the review options from the setup form into the bits of $form->review
* and other options into $form->optionflags
* The form data is passed by reference and modified by this function
* @return integer
* @param object $form  The variables set on the form.
*/
function quiz_process_options(&$form) {
    $optionflags = 0;
    $review = 0;

    if (!empty($form->adaptive)) {
        $optionflags |= QUESTION_ADAPTIVE;
    }

    if (isset($form->responsesimmediately)) {
        $review += (QUIZ_REVIEW_RESPONSES & QUIZ_REVIEW_IMMEDIATELY);
        unset($form->responsesimmediately);
    }
    if (isset($form->responsesopen)) {
        $review += (QUIZ_REVIEW_RESPONSES & QUIZ_REVIEW_OPEN);
        unset($form->responsesopen);
    }
    if (isset($form->responsesclosed)) {
        $review += (QUIZ_REVIEW_RESPONSES & QUIZ_REVIEW_CLOSED);
        unset($form->responsesclosed);
    }

    if (isset($form->scoreimmediately)) {
        $review += (QUIZ_REVIEW_SCORES & QUIZ_REVIEW_IMMEDIATELY);
        unset($form->scoreimmediately);
    }
    if (isset($form->scoreopen)) {
        $review += (QUIZ_REVIEW_SCORES & QUIZ_REVIEW_OPEN);
        unset($form->scoreopen);
    }
    if (isset($form->scoreclosed)) {
        $review += (QUIZ_REVIEW_SCORES & QUIZ_REVIEW_CLOSED);
        unset($form->scoreclosed);
    }

    if (isset($form->feedbackimmediately)) {
        $review += (QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_IMMEDIATELY);
        unset($form->feedbackimmediately);
    }
    if (isset($form->feedbackopen)) {
        $review += (QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_OPEN);
        unset($form->feedbackopen);
    }
    if (isset($form->feedbackclosed)) {
        $review += (QUIZ_REVIEW_FEEDBACK & QUIZ_REVIEW_CLOSED);
        unset($form->feedbackclosed);
    }

    if (isset($form->answersimmediately)) {
        $review += (QUIZ_REVIEW_ANSWERS & QUIZ_REVIEW_IMMEDIATELY);
        unset($form->answersimmediately);
    }
    if (isset($form->answersopen)) {
        $review += (QUIZ_REVIEW_ANSWERS & QUIZ_REVIEW_OPEN);
        unset($form->answersopen);
    }
    if (isset($form->answersclosed)) {
        $review += (QUIZ_REVIEW_ANSWERS & QUIZ_REVIEW_CLOSED);
        unset($form->answersclosed);
    }

    if (isset($form->solutionsimmediately)) {
        $review += (QUIZ_REVIEW_SOLUTIONS & QUIZ_REVIEW_IMMEDIATELY);
        unset($form->solutionsimmediately);
    }
    if (isset($form->solutionsopen)) {
        $review += (QUIZ_REVIEW_SOLUTIONS & QUIZ_REVIEW_OPEN);
        unset($form->solutionsopen);
    }
    if (isset($form->solutionsclosed)) {
        $review += (QUIZ_REVIEW_SOLUTIONS & QUIZ_REVIEW_CLOSED);
        unset($form->solutionsclosed);
    }

    $form->review = $review;
    $form->optionflags = $optionflags;
    
    // The following implements the time limit check box
    if (empty($form->timelimitenable) or $form->timelimit < 1) {
        $form->timelimit = 0;
    }

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
 * @param object $questionid
 * @return array of strings
 */
function quiz_question_list_instances($questionid) {
    global $CFG;

    // TODO: we should also consider other questions that are used by
    // random questions in this quiz, but that is very hard.

    $sql = "SELECT q.id, q.name
            FROM {$CFG->prefix}quiz q
            INNER JOIN
                 {$CFG->prefix}quiz_question_instances qqi
            ON q.id = qqi.quiz
            WHERE qqi.question = '$questionid'";

    if ($instances = get_records_sql_menu($sql)) {
        return $instances;
    }
    return array();
}

?>
