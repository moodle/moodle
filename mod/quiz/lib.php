<?php  // $Id$
/**
* Library of functions for the quiz module.
*
* This contains functions that are called also from outside the quiz module
* Functions that are only called by the quiz module itself are in {@link locallib.php}
* @author Martin Dougiamas and many others.
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package quiz
*/

require_once($CFG->libdir.'/pagelib.php');

/// CONSTANTS ///////////////////////////////////////////////////////////////////

/**#@+
 * The different review options are stored in the bits of $quiz->review
 * These constants help to extract the options
 *
 * This is more of a mess than you might think necessary, because originally
 * it was though that 3x6 bits were enough, but then they ran out. PHP integers
 * are only reliably 32 bits signed, so the simplest solution was then to
 * add 4x3 more bits.
 */
/**
 * The first 6 + 4 bits refer to the time immediately after the attempt
 */
define('QUIZ_REVIEW_IMMEDIATELY', 0x3c003f);
/**
 * the next 6 + 4 bits refer to the time after the attempt but while the quiz is open
 */
define('QUIZ_REVIEW_OPEN',       0x3c00fc0);
/**
 * the final 6 + 4 bits refer to the time after the quiz closes
 */
define('QUIZ_REVIEW_CLOSED',    0x3c03f000);

// within each group of 6 bits we determine what should be shown
define('QUIZ_REVIEW_RESPONSES',       1*0x1041); // Show responses
define('QUIZ_REVIEW_SCORES',          2*0x1041); // Show scores
define('QUIZ_REVIEW_FEEDBACK',        4*0x1041); // Show question feedback
define('QUIZ_REVIEW_ANSWERS',         8*0x1041); // Show correct answers
// Some handling of worked solutions is already in the code but not yet fully supported
// and not switched on in the user interface.
define('QUIZ_REVIEW_SOLUTIONS',      16*0x1041); // Show solutions
define('QUIZ_REVIEW_GENERALFEEDBACK',32*0x1041); // Show question general feedback
define('QUIZ_REVIEW_OVERALLFEEDBACK', 1*0x4440000); // Show quiz overall feedback
// Multipliers 2*0x4440000, 4*0x4440000 and 8*0x4440000 are still available
/**#@-*/

/**
 * If start and end date for the quiz are more than this many seconds apart
 * they will be represented by two separate events in the calendar
 */
define("QUIZ_MAX_EVENT_LENGTH", 5*24*60*60);   // 5 days maximum

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
        // TODO: this should use the delete_attempt($attempt->uniqueid) function in questionlib.php
        // require_once($CFG->libdir.'/questionlib.php');
        foreach ($attempts as $attempt) {
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
        if(!blocks_delete_all_on_page($pagetype, $quiz->id)) {
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

/**
 * Get the best current grade for a particular user in a quiz.
 *
 * @param object $quiz the quiz object.
 * @param integer $userid the id of the user.
 * @return float the user's current grade for this quiz.
 */
function quiz_get_best_grade($quiz, $userid) {
    $grade = get_field('quiz_grades', 'grade', 'quiz', $quiz->id, 'userid', $userid);

    // Need to detect errors/no result, without catching 0 scores.
    if (is_numeric($grade)) {
        return round($grade, $quiz->decimalpoints);
    } else {
        return NULL;
    }
}

function quiz_user_outline($course, $user, $mod, $quiz) {
/// Return a small object with summary information about what a
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description
    global $CFG;
    require_once("$CFG->libdir/gradelib.php");
    $grades = grade_get_grades($course->id, 'mod', 'quiz', $quiz->id, $user->id);
    if (empty($grades->items[0]->grades)) {
        return null;
    } else {
        $grade = reset($grades->items[0]->grades);
    }

    $result = new stdClass;
    $result->info = get_string('grade') . ': ' . $grade->str_long_grade;
    $result->time = $grade->dategraded;
    return $result;
}

function quiz_user_complete($course, $user, $mod, $quiz) {
/// Print a detailed representation of what a  user has done with
/// a given particular instance of this module, for user activity reports.
    global $CFG;
    require_once("$CFG->libdir/gradelib.php");
    $grades = grade_get_grades($course->id, 'mod', 'quiz', $quiz->id, $user->id);
    if (!empty($grades->items[0]->grades)) {
        $grade = reset($grades->items[0]->grades);
        echo '<p>'.get_string('grade').': '.$grade->str_long_grade.'</p>';
        if ($grade->str_feedback) {
            echo '<p>'.get_string('feedback').': '.$grade->str_feedback.'</p>';
        }
    }

    if ($attempts = get_records_select('quiz_attempts', "userid='$user->id' AND quiz='$quiz->id'", 'attempt ASC')) {
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

function quiz_cron() {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such
/// as sending out mail, toggling flags etc ...

    global $CFG;

    return true;
}

/**
 * @param integer $quizid the quiz id.
 * @param integer $userid the userid.
 * @param string $status 'all', 'finished' or 'unfinished' to control
 * @return an array of all the user's attempts at this quiz. Returns an empty array if there are none.
 */
function quiz_get_user_attempts($quizid, $userid, $status = 'finished', $includepreviews = false) {
    $status_condition = array(
        'all' => '',
        'finished' => ' AND timefinish > 0',
        'unfinished' => ' AND timefinish = 0'
    );
    $previewclause = '';
    if (!$includepreviews) {
        $previewclause = ' AND preview = 0';
    }
    if ($attempts = get_records_select('quiz_attempts',
            "quiz = '$quizid' AND userid = '$userid'" . $previewclause . $status_condition[$status],
            'attempt ASC')) {
        return $attempts;
    } else {
        return array();
    }
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

    $sql = "SELECT u.id, u.id AS userid, g.grade AS rawgrade, g.timemodified AS dategraded, MAX(a.timefinish) AS datesubmitted
            FROM {$CFG->prefix}user u, {$CFG->prefix}quiz_grades g, {$CFG->prefix}quiz_attempts a
            WHERE u.id = g.userid AND g.quiz = {$quiz->id} AND a.quiz = g.quiz AND u.id = a.userid
                  $user
            GROUP BY u.id, g.grade, g.timemodified";

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
            quiz_grade_item_update($quiz, $grades);

        } else if ($userid and $nullifnone) {
            $grade = new object();
            $grade->userid   = $userid;
            $grade->rawgrade = NULL;
            quiz_grade_item_update($quiz, $grade);

        } else {
            quiz_grade_item_update($quiz);
        }

    } else {
        $sql = "SELECT a.*, cm.idnumber as cmidnumber, a.course as courseid
                  FROM {$CFG->prefix}quiz a, {$CFG->prefix}course_modules cm, {$CFG->prefix}modules m
                 WHERE m.name='quiz' AND m.id=cm.module AND cm.instance=a.id";
        if ($rs = get_recordset_sql($sql)) {
            while ($quiz = rs_fetch_next_record($rs)) {
                if ($quiz->grade != 0) {
                    quiz_update_grades($quiz, 0, false);
                } else {
                    quiz_grade_item_update($quiz);
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
 * @param mixed optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int 0 if ok, error code otherwise
 */
function quiz_grade_item_update($quiz, $grades=NULL) {
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

/* description by TJ:
1/ If the quiz is set to not show scores while the quiz is still open, and is set to show scores after
   the quiz is closed, then create the grade_item with a show-after date that is the quiz close date.
2/ If the quiz is set to not show scores at either of those times, create the grade_item as hidden.
3/ If the quiz is set to show scores, create the grade_item visible.
*/
    if (!($quiz->review & QUIZ_REVIEW_SCORES & QUIZ_REVIEW_CLOSED)
    and !($quiz->review & QUIZ_REVIEW_SCORES & QUIZ_REVIEW_OPEN)) {
        $params['hidden'] = 1;

    } else if ( ($quiz->review & QUIZ_REVIEW_SCORES & QUIZ_REVIEW_CLOSED)
           and !($quiz->review & QUIZ_REVIEW_SCORES & QUIZ_REVIEW_OPEN)) {
        if ($quiz->timeclose) {
            $params['hidden'] = $quiz->timeclose;
        } else {
            $params['hidden'] = 1;
        }

    } else {
        // a) both open and closed enabled
        // b) open enabled, closed disabled - we can not "hide after", grades are kept visible even after closing
        $params['hidden'] = 0;
    }

    if ($grades  === 'reset') {
        $params['reset'] = true;
        $grades = NULL;
    }
    
    $gradebook_grades = grade_get_grades($quiz->course, 'mod', 'quiz', $quiz->id);
    if (!empty($gradebook_grades->items)) {
        $grade_item = $gradebook_grades->items[0];
        if ($grade_item->locked) {
            $confirm_regrade = optional_param('confirm_regrade', 0, PARAM_INT);
            if (!$confirm_regrade) {
                $message = get_string('gradeitemislocked', 'grades');
                $back_link = $CFG->wwwroot . '/mod/quiz/report.php?q=' . $quiz->id . '&amp;mode=overview';
                $regrade_link = qualified_me() . '&amp;confirm_regrade=1';
                print_box_start('generalbox', 'notice');
                echo '<p>'. $message .'</p>';
                echo '<div class="buttons">';
                print_single_button($regrade_link, null, get_string('regradeanyway', 'grades'), 'post', $CFG->framename);
                print_single_button($back_link,  null,  get_string('cancel'),  'post',  $CFG->framename);
                echo '</div>';
                print_box_end();
    
                return GRADE_UPDATE_ITEM_LOCKED;
            }
        }
    }

    return grade_update('mod/quiz', $quiz->course, 'mod', 'quiz', $quiz->id, 0, $grades, $params);
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
        } else if (!empty($event2old->id)) {
            delete_event($event2old->id);
        }

        if (empty($event->id)) {
            if (!empty($event->timestart)) {
                add_event($event);
            }
        } else {
            update_event($event);
        }

    }
    return true;
}

/**
 * Returns all quiz graded users since a given time for specified quiz
 */
function quiz_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0)  {
    global $CFG, $COURSE, $USER;

    if ($COURSE->id == $courseid) {
        $course = $COURSE;
    } else {
        $course = get_record('course', 'id', $courseid);
    }

    $modinfo =& get_fast_modinfo($course);

    $cm = $modinfo->cms[$cmid];

    if ($userid) {
        $userselect = "AND u.id = $userid";
    } else {
        $userselect = "";
    }

    if ($groupid) {
        $groupselect = "AND gm.groupid = $groupid";
        $groupjoin   = "JOIN {$CFG->prefix}groups_members gm ON  gm.userid=u.id";
    } else {
        $groupselect = "";
        $groupjoin   = "";
    }

    if (!$attempts = get_records_sql("SELECT qa.*, q.sumgrades AS maxgrade,
                                             u.firstname, u.lastname, u.email, u.picture 
                                        FROM {$CFG->prefix}quiz_attempts qa
                                             JOIN {$CFG->prefix}quiz q ON q.id = qa.quiz
                                             JOIN {$CFG->prefix}user u ON u.id = qa.userid
                                             $groupjoin
                                       WHERE qa.timefinish > $timestart AND q.id = $cm->instance
                                             $userselect $groupselect
                                    ORDER BY qa.timefinish ASC")) {
         return;
    }

    $cm_context      = get_context_instance(CONTEXT_MODULE, $cm->id);
    $grader          = has_capability('moodle/grade:viewall', $cm_context);
    $accessallgroups = has_capability('moodle/site:accessallgroups', $cm_context);
    $viewfullnames   = has_capability('moodle/site:viewfullnames', $cm_context);
    $grader          = has_capability('mod/quiz:grade', $cm_context);
    $groupmode       = groups_get_activity_groupmode($cm, $course);

    if (is_null($modinfo->groups)) {
        $modinfo->groups = groups_get_user_groups($course->id); // load all my groups and cache it in modinfo
    }

    $aname = format_string($cm->name,true);
    foreach ($attempts as $attempt) {
        if ($attempt->userid != $USER->id) {
            if (!$grader) {
                // grade permission required
                continue;
            }

            if ($groupmode == SEPARATEGROUPS and !$accessallgroups) { 
                $usersgroups = groups_get_all_groups($course->id, $attempt->userid, $cm->groupingid);
                if (!is_array($usersgroups)) {
                    continue;
                }
                $usersgroups = array_keys($usersgroups);
                $interset = array_intersect($usersgroups, $modinfo->groups[$cm->id]);
                if (empty($intersect)) {
                    continue;
                }
            }
       }

        $tmpactivity = new object();

        $tmpactivity->type      = 'quiz';
        $tmpactivity->cmid      = $cm->id;
        $tmpactivity->name      = $aname;
        $tmpactivity->sectionnum= $cm->sectionnum;
        $tmpactivity->timestamp = $attempt->timefinish;
        
        $tmpactivity->content->attemptid = $attempt->id;
        $tmpactivity->content->sumgrades = $attempt->sumgrades;
        $tmpactivity->content->maxgrade  = $attempt->maxgrade;
        $tmpactivity->content->attempt   = $attempt->attempt;
        
        $tmpactivity->user->userid   = $attempt->userid;
        $tmpactivity->user->fullname = fullname($attempt, $viewfullnames);
        $tmpactivity->user->picture  = $attempt->picture;
        
        $activities[$index++] = $tmpactivity;
    }

  return;
}

function quiz_print_recent_mod_activity($activity, $courseid, $detail, $modnames) {
    global $CFG;

    echo '<table border="0" cellpadding="3" cellspacing="0" class="forum-recent">';

    echo "<tr><td class=\"userpicture\" valign=\"top\">";
    print_user_picture($activity->user->userid, $courseid, $activity->user->picture);
    echo "</td><td>";

    if ($detail) {
        $modname = $modnames[$activity->type];
        echo '<div class="title">';
        echo "<img src=\"$CFG->modpixpath/{$activity->type}/icon.gif\" ".
             "class=\"icon\" alt=\"$modname\" />";
        echo "<a href=\"$CFG->wwwroot/mod/quiz/view.php?id={$activity->cmid}\">{$activity->name}</a>";
        echo '</div>';
    }

    echo '<div class="grade">';
    echo  get_string("attempt", "quiz")." {$activity->content->attempt}: ";
    $grades = "({$activity->content->sumgrades} / {$activity->content->maxgrade})";
    echo "<a href=\"$CFG->wwwroot/mod/quiz/review.php?attempt={$activity->content->attemptid}\">$grades</a>";
    echo '</div>';

    echo '<div class="user">';
    echo "<a href=\"$CFG->wwwroot/user/view.php?id={$activity->user->userid}&amp;course=$courseid\">"
         ."{$activity->user->fullname}</a> - ".userdate($activity->timestamp);
    echo '</div>';

    echo "</td></tr></table>";

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

    // Quiz name.
    if (!empty($quiz->name)) {
        $quiz->name = trim($quiz->name);
    }

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
        if (!empty($quiz->feedbackboundaries)) {
            for ($i = $numboundaries; $i < count($quiz->feedbackboundaries); $i += 1) {
                if (!empty($quiz->feedbackboundaries[$i]) && trim($quiz->feedbackboundaries[$i]) != '') {
                    return get_string('feedbackerrorjunkinboundary', 'quiz', $i + 1);
                }
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
        unset($quiz->generalfeedbackimmediately);
    }
    if (isset($quiz->generalfeedbackopen)) {
        $review += (QUIZ_REVIEW_GENERALFEEDBACK & QUIZ_REVIEW_OPEN);
        unset($quiz->generalfeedbackopen);
    }
    if (isset($quiz->generalfeedbackclosed)) {
        $review += (QUIZ_REVIEW_GENERALFEEDBACK & QUIZ_REVIEW_CLOSED);
        unset($quiz->generalfeedbackclosed);
    }

    if (isset($quiz->overallfeedbackimmediately)) {
        $review += (QUIZ_REVIEW_OVERALLFEEDBACK & QUIZ_REVIEW_IMMEDIATELY);
        unset($quiz->overallfeedbackimmediately);
    }
    if (isset($quiz->overallfeedbackopen)) {
        $review += (QUIZ_REVIEW_OVERALLFEEDBACK & QUIZ_REVIEW_OPEN);
        unset($quiz->overallfeedbackopen);
    }
    if (isset($quiz->overallfeedbackclosed)) {
        $review += (QUIZ_REVIEW_OVERALLFEEDBACK & QUIZ_REVIEW_CLOSED);
        unset($quiz->overallfeedbackclosed);
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

/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the quiz.
 * @param $mform form passed by reference
 */
function quiz_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'forumheader', get_string('modulenameplural', 'quiz'));
    $mform->addElement('advcheckbox', 'reset_quiz_attempts', get_string('removeallquizattempts','quiz'));
}

/**
 * Course reset form defaults.
 */
function quiz_reset_course_form_defaults($course) {
    return array('reset_quiz_attempts'=>1);
}

/**
 * Removes all grades from gradebook
 * @param int $courseid
 * @param string optional type
 */
function quiz_reset_gradebook($courseid, $type='') {
    global $CFG;

    $sql = "SELECT q.*, cm.idnumber as cmidnumber, q.course as courseid
              FROM {$CFG->prefix}quiz q, {$CFG->prefix}course_modules cm, {$CFG->prefix}modules m
             WHERE m.name='quiz' AND m.id=cm.module AND cm.instance=q.id AND q.course=$courseid";

    if ($quizs = get_records_sql($sql)) {
        foreach ($quizs as $quiz) {
            quiz_grade_item_update($quiz, 'reset');
        }
    }
}

/**
 * Actual implementation of the rest coures functionality, delete all the
 * quiz attempts for course $data->courseid, if $data->reset_quiz_attempts is
 * set and true.
 *
 * Also, move the quiz open and close dates, if the course start date is changing.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function quiz_reset_userdata($data) {
    global $CFG, $QTYPES;
    
    if (empty($QTYPES)) {
        require_once($CFG->libdir . '/questionlib.php');
    }

    // TODO: this should use the delete_attempt($attempt->uniqueid) function in questionlib.php
    // require_once($CFG->libdir.'/questionlib.php');

    $componentstr = get_string('modulenameplural', 'quiz');
    $status = array();

    /// Delete attempts.
    if (!empty($data->reset_quiz_attempts)) {

        $stateslistsql = "SELECT s.id
                            FROM {$CFG->prefix}question_states s
                                 INNER JOIN {$CFG->prefix}quiz_attempts qza ON s.attempt=qza.uniqueid
                                 INNER JOIN {$CFG->prefix}quiz q ON qza.quiz=q.id
                           WHERE q.course={$data->courseid}";

        $attemptssql   = "SELECT a.uniqueid
                            FROM {$CFG->prefix}quiz_attempts a, {$CFG->prefix}quiz q
                           WHERE q.course={$data->courseid} AND a.quiz=q.id";

        $quizessql     = "SELECT q.id
                            FROM {$CFG->prefix}quiz q
                           WHERE q.course={$data->courseid}";

        if ($states = get_records_sql($stateslistsql)) {
            //TODO: not sure if this works
            $stateslist = implode(',', array_keys($states));
            foreach ($QTYPES as $qtype) {
                $qtype->delete_states($stateslist);
            }
        }

        delete_records_select('question_states', "attempt IN ($attemptssql)");
        delete_records_select('question_sessions', "attemptid IN ($attemptssql)");
        delete_records_select('question_attempts', "id IN ($attemptssql)");

        // remove all grades from gradebook
        if (empty($data->reset_gradebook_grades)) {
            quiz_reset_gradebook($data->courseid);
        }

        delete_records_select('quiz_grades', "quiz IN ($quizessql)");
        $status[] = array('component'=>$componentstr, 'item'=>get_string('gradesdeleted','quiz'), 'error'=>false);

        delete_records_select('quiz_attempts', "quiz IN ($quizessql)");
        $status[] = array('component'=>$componentstr, 'item'=>get_string('attemptsdeleted','quiz'), 'error'=>false);
    }

    /// updating dates - shift may be negative too
    if ($data->timeshift) {
        shift_course_mod_dates('quiz', array('timeopen', 'timeclose'), $data->timeshift, $data->courseid);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('openclosedatesupdated', 'quiz'), 'error'=>false);
    }

    return $status;
}

/**
 * Checks whether the current user is allowed to view a file uploaded in a quiz.
 * Teachers can view any from their courses, students can only view their own.
 *
 * @param int $attemptuniqueid int attempt id
 * @param int $questionid int question id
 * @return boolean to indicate access granted or denied
 */
function quiz_check_file_access($attemptuniqueid, $questionid) {
    global $USER;

    $attempt = get_record("quiz_attempts", 'uniqueid', $attemptuniqueid);
    $quiz = get_record("quiz", 'id', $attempt->quiz);
    $context = get_context_instance(CONTEXT_COURSE, $quiz->course);

    // access granted if the current user submitted this file
    if ($attempt->userid == $USER->id) {
        return true;
    // access granted if the current user has permission to grade quizzes in this course
    } else if (has_capability('mod/quiz:viewreports', $context) || has_capability('mod/quiz:grade', $context)) {
        return true;
    }

    // otherwise, this user does not have permission
    return false;
}

/**
 * Prints quiz summaries on MyMoodle Page
 */
function quiz_print_overview($courses, &$htmlarray) {
    global $USER, $CFG;
/// These next 6 Lines are constant in all modules (just change module name)
    if (empty($courses) || !is_array($courses) || count($courses) == 0) {
        return array();
    }

    if (!$quizzes = get_all_instances_in_courses('quiz', $courses)) {
        return;
    }

/// Fetch some language strings outside the main loop.
    $strquiz = get_string('modulename', 'quiz');
    $strnoattempts = get_string('noattempts', 'quiz');

/// We want to list quizzes that are currently available, and which have a close date.
/// This is the same as what the lesson does, and the dabate is in MDL-10568.
    $now = time();
    foreach ($quizzes as $quiz) {
        if ($quiz->timeclose >= $now && $quiz->timeopen < $now) {
        /// Give a link to the quiz, and the deadline.
            $str = '<div class="quiz overview">' .
                    '<div class="name">' . $strquiz . ': <a ' . ($quiz->visible ? '' : ' class="dimmed"') .
                    ' href="' . $CFG->wwwroot . '/mod/quiz/view.php?id=' . $quiz->coursemodule . '">' .
                    $quiz->name . '</a></div>';
            $str .= '<div class="info">' . get_string('quizcloseson', 'quiz', userdate($quiz->timeclose)) . '</div>';

        /// Now provide more information depending on the uers's role.
            $context = get_context_instance(CONTEXT_MODULE, $quiz->coursemodule);
            if (has_capability('mod/quiz:viewreports', $context)) {
            /// For teacher-like people, show a summary of the number of student attempts.
                // The $quiz objects returned by get_all_instances_in_course have the necessary $cm 
                // fields set to make the following call work.
                $str .= '<div class="info">' . quiz_num_attempt_summary($quiz, $quiz, true) . '</div>';
            } else if (has_any_capability(array('mod/quiz:reviewmyattempts', 'mod/quiz:attempt'), $context)) { // Student
            /// For student-like people, tell them how many attempts they have made.
                if (isset($USER->id) && ($attempts = quiz_get_user_attempts($quiz->id, $USER->id))) {
                    $numattempts = count($attempts);
                    $str .= '<div class="info">' . get_string('numattemptsmade', 'quiz', $numattempts) . '</div>';  
                } else {
                    $str .= '<div class="info">' . $strnoattempts . '</div>';
                }
            } else {
            /// For ayone else, there is no point listing this quiz, so stop processing.
                continue;
            }

        /// Add the output for this quiz to the rest.
            $str .= '</div>';
            if (empty($htmlarray[$quiz->course]['quiz'])) {
                $htmlarray[$quiz->course]['quiz'] = $str;
            } else {
                $htmlarray[$quiz->course]['quiz'] .= $str;
            }
        }
    }
}

/**
 * Return a textual summary of the number of attemtps that have been made at a particular quiz,
 * returns '' if no attemtps have been made yet, unless $returnzero is passed as true.
 * @param object $quiz the quiz object. Only $quiz->id is used at the moment.
 * @param object $cm the cm object. Only $cm->course, $cm->groupmode and $cm->groupingid fields are used at the moment.
 * @param boolean $returnzero if false (default), when no attempts have been made '' is returned instead of 'Attempts: 0'.
 * @param int $currentgroup if there is a concept of current group where this method is being called
 *         (e.g. a report) pass it in here. Default 0 which means no current group.
 * @return string a string like "Attempts: 123", "Attemtps 123 (45 from your groups)" or
 *          "Attemtps 123 (45 from this group)".
 */
function quiz_num_attempt_summary($quiz, $cm, $returnzero = false, $currentgroup = 0) {
    global $CFG, $USER;
    $numattempts = count_records('quiz_attempts', 'quiz', $quiz->id, 'preview', 0);
    if ($numattempts || $returnzero) {
        if (groups_get_activity_groupmode($cm)) {
            $a->total = $numattempts;
            if ($currentgroup) {
                $a->group = count_records_sql('SELECT count(1) FROM ' .
                        $CFG->prefix . 'quiz_attempts qa JOIN ' .
                        $CFG->prefix . 'groups_members gm ON qa.userid = gm.userid ' .
                        'WHERE quiz = ' . $quiz->id . ' AND preview = 0 AND groupid = ' . $currentgroup);
                return get_string('attemptsnumthisgroup', 'quiz', $a);
            } else if ($groups = groups_get_all_groups($cm->course, $USER->id, $cm->groupingid)) { 
                $a->group = count_records_sql('SELECT count(1) FROM ' .
                        $CFG->prefix . 'quiz_attempts qa JOIN ' .
                        $CFG->prefix . 'groups_members gm ON qa.userid = gm.userid ' .
                        'WHERE quiz = ' . $quiz->id . ' AND preview = 0 AND ' .
                        'groupid IN (' . implode(',', array_keys($groups)) . ')');
                return get_string('attemptsnumyourgroups', 'quiz', $a);
            }
        }
        return get_string('attemptsnum', 'quiz', $numattempts);
    }
    return '';
}

/**
 * Returns all other caps used in module
 */
function quiz_get_extra_capabilities() {
    return array(
        'moodle/site:accessallgroups',
        'moodle/question:add',
        'moodle/question:editmine',
        'moodle/question:editall',
        'moodle/question:viewmine',
        'moodle/question:viewall',
        'moodle/question:usemine',
        'moodle/question:useall',
        'moodle/question:movemine',
        'moodle/question:moveall',
        'moodle/question:managecategory',
    );
}

?>
