<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Library of functions for the quiz module.
 *
 * This contains functions that are called also from outside the quiz module
 * Functions that are only called by the quiz module itself are in {@link locallib.php}
 *
 * @package    mod_quiz
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/eventslib.php');
require_once($CFG->dirroot . '/calendar/lib.php');


/**#@+
 * Option controlling what options are offered on the quiz settings form.
 */
define('QUIZ_MAX_ATTEMPT_OPTION', 10);
define('QUIZ_MAX_QPP_OPTION', 50);
define('QUIZ_MAX_DECIMAL_OPTION', 5);
define('QUIZ_MAX_Q_DECIMAL_OPTION', 7);
/**#@-*/

/**#@+
 * Options determining how the grades from individual attempts are combined to give
 * the overall grade for a user
 */
define('QUIZ_GRADEHIGHEST', '1');
define('QUIZ_GRADEAVERAGE', '2');
define('QUIZ_ATTEMPTFIRST', '3');
define('QUIZ_ATTEMPTLAST',  '4');
/**#@-*/

/**
 * @var int If start and end date for the quiz are more than this many seconds apart
 * they will be represented by two separate events in the calendar
 */
define('QUIZ_MAX_EVENT_LENGTH', 5*24*60*60); // 5 days.

/**#@+
 * Options for navigation method within quizzes.
 */
define('QUIZ_NAVMETHOD_FREE', 'free');
define('QUIZ_NAVMETHOD_SEQ',  'sequential');
/**#@-*/

/**
 * Event types.
 */
define('QUIZ_EVENT_TYPE_OPEN', 'open');
define('QUIZ_EVENT_TYPE_CLOSE', 'close');

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $quiz the data that came from the form.
 * @return mixed the id of the new instance on success,
 *          false or a string error message on failure.
 */
function quiz_add_instance($quiz) {
    global $DB;
    $cmid = $quiz->coursemodule;

    // Process the options from the form.
    $quiz->created = time();
    $result = quiz_process_options($quiz);
    if ($result && is_string($result)) {
        return $result;
    }

    // Try to store it in the database.
    $quiz->id = $DB->insert_record('quiz', $quiz);

    // Create the first section for this quiz.
    $DB->insert_record('quiz_sections', array('quizid' => $quiz->id,
            'firstslot' => 1, 'heading' => '', 'shufflequestions' => 0));

    // Do the processing required after an add or an update.
    quiz_after_add_or_update($quiz);

    return $quiz->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $quiz the data that came from the form.
 * @return mixed true on success, false or a string error message on failure.
 */
function quiz_update_instance($quiz, $mform) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/mod/quiz/locallib.php');

    // Process the options from the form.
    $result = quiz_process_options($quiz);
    if ($result && is_string($result)) {
        return $result;
    }

    // Get the current value, so we can see what changed.
    $oldquiz = $DB->get_record('quiz', array('id' => $quiz->instance));

    // We need two values from the existing DB record that are not in the form,
    // in some of the function calls below.
    $quiz->sumgrades = $oldquiz->sumgrades;
    $quiz->grade     = $oldquiz->grade;

    // Update the database.
    $quiz->id = $quiz->instance;
    $DB->update_record('quiz', $quiz);

    // Do the processing required after an add or an update.
    quiz_after_add_or_update($quiz);

    if ($oldquiz->grademethod != $quiz->grademethod) {
        quiz_update_all_final_grades($quiz);
        quiz_update_grades($quiz);
    }

    $quizdateschanged = $oldquiz->timelimit   != $quiz->timelimit
                     || $oldquiz->timeclose   != $quiz->timeclose
                     || $oldquiz->graceperiod != $quiz->graceperiod;
    if ($quizdateschanged) {
        quiz_update_open_attempts(array('quizid' => $quiz->id));
    }

    // Delete any previous preview attempts.
    quiz_delete_previews($quiz);

    // Repaginate, if asked to.
    if (!empty($quiz->repaginatenow)) {
        quiz_repaginate_questions($quiz->id, $quiz->questionsperpage);
    }

    return true;
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id the id of the quiz to delete.
 * @return bool success or failure.
 */
function quiz_delete_instance($id) {
    global $DB;

    $quiz = $DB->get_record('quiz', array('id' => $id), '*', MUST_EXIST);

    quiz_delete_all_attempts($quiz);
    quiz_delete_all_overrides($quiz);

    // Look for random questions that may no longer be used when this quiz is gone.
    $sql = "SELECT q.id
              FROM {quiz_slots} slot
              JOIN {question} q ON q.id = slot.questionid
             WHERE slot.quizid = ? AND q.qtype = ?";
    $questionids = $DB->get_fieldset_sql($sql, array($quiz->id, 'random'));

    // We need to do this before we try and delete randoms, otherwise they would still be 'in use'.
    $DB->delete_records('quiz_slots', array('quizid' => $quiz->id));
    $DB->delete_records('quiz_sections', array('quizid' => $quiz->id));

    foreach ($questionids as $questionid) {
        question_delete_question($questionid);
    }

    $DB->delete_records('quiz_feedback', array('quizid' => $quiz->id));

    quiz_access_manager::delete_settings($quiz);

    $events = $DB->get_records('event', array('modulename' => 'quiz', 'instance' => $quiz->id));
    foreach ($events as $event) {
        $event = calendar_event::load($event);
        $event->delete();
    }

    quiz_grade_item_delete($quiz);
    $DB->delete_records('quiz', array('id' => $quiz->id));

    return true;
}

/**
 * Deletes a quiz override from the database and clears any corresponding calendar events
 *
 * @param object $quiz The quiz object.
 * @param int $overrideid The id of the override being deleted
 * @param bool $log Whether to trigger logs.
 * @return bool true on success
 */
function quiz_delete_override($quiz, $overrideid, $log = true) {
    global $DB;

    if (!isset($quiz->cmid)) {
        $cm = get_coursemodule_from_instance('quiz', $quiz->id, $quiz->course);
        $quiz->cmid = $cm->id;
    }

    $override = $DB->get_record('quiz_overrides', array('id' => $overrideid), '*', MUST_EXIST);

    // Delete the events.
    if (isset($override->groupid)) {
        // Create the search array for a group override.
        $eventsearcharray = array('modulename' => 'quiz',
            'instance' => $quiz->id, 'groupid' => (int)$override->groupid);
    } else {
        // Create the search array for a user override.
        $eventsearcharray = array('modulename' => 'quiz',
            'instance' => $quiz->id, 'userid' => (int)$override->userid);
    }
    $events = $DB->get_records('event', $eventsearcharray);
    foreach ($events as $event) {
        $eventold = calendar_event::load($event);
        $eventold->delete();
    }

    $DB->delete_records('quiz_overrides', array('id' => $overrideid));

    if ($log) {
        // Set the common parameters for one of the events we will be triggering.
        $params = array(
            'objectid' => $override->id,
            'context' => context_module::instance($quiz->cmid),
            'other' => array(
                'quizid' => $override->quiz
            )
        );
        // Determine which override deleted event to fire.
        if (!empty($override->userid)) {
            $params['relateduserid'] = $override->userid;
            $event = \mod_quiz\event\user_override_deleted::create($params);
        } else {
            $params['other']['groupid'] = $override->groupid;
            $event = \mod_quiz\event\group_override_deleted::create($params);
        }

        // Trigger the override deleted event.
        $event->add_record_snapshot('quiz_overrides', $override);
        $event->trigger();
    }

    return true;
}

/**
 * Deletes all quiz overrides from the database and clears any corresponding calendar events
 *
 * @param object $quiz The quiz object.
 * @param bool $log Whether to trigger logs.
 */
function quiz_delete_all_overrides($quiz, $log = true) {
    global $DB;

    $overrides = $DB->get_records('quiz_overrides', array('quiz' => $quiz->id), 'id');
    foreach ($overrides as $override) {
        quiz_delete_override($quiz, $override->id, $log);
    }
}

/**
 * Updates a quiz object with override information for a user.
 *
 * Algorithm:  For each quiz setting, if there is a matching user-specific override,
 *   then use that otherwise, if there are group-specific overrides, return the most
 *   lenient combination of them.  If neither applies, leave the quiz setting unchanged.
 *
 *   Special case: if there is more than one password that applies to the user, then
 *   quiz->extrapasswords will contain an array of strings giving the remaining
 *   passwords.
 *
 * @param object $quiz The quiz object.
 * @param int $userid The userid.
 * @return object $quiz The updated quiz object.
 */
function quiz_update_effective_access($quiz, $userid) {
    global $DB;

    // Check for user override.
    $override = $DB->get_record('quiz_overrides', array('quiz' => $quiz->id, 'userid' => $userid));

    if (!$override) {
        $override = new stdClass();
        $override->timeopen = null;
        $override->timeclose = null;
        $override->timelimit = null;
        $override->attempts = null;
        $override->password = null;
    }

    // Check for group overrides.
    $groupings = groups_get_user_groups($quiz->course, $userid);

    if (!empty($groupings[0])) {
        // Select all overrides that apply to the User's groups.
        list($extra, $params) = $DB->get_in_or_equal(array_values($groupings[0]));
        $sql = "SELECT * FROM {quiz_overrides}
                WHERE groupid $extra AND quiz = ?";
        $params[] = $quiz->id;
        $records = $DB->get_records_sql($sql, $params);

        // Combine the overrides.
        $opens = array();
        $closes = array();
        $limits = array();
        $attempts = array();
        $passwords = array();

        foreach ($records as $gpoverride) {
            if (isset($gpoverride->timeopen)) {
                $opens[] = $gpoverride->timeopen;
            }
            if (isset($gpoverride->timeclose)) {
                $closes[] = $gpoverride->timeclose;
            }
            if (isset($gpoverride->timelimit)) {
                $limits[] = $gpoverride->timelimit;
            }
            if (isset($gpoverride->attempts)) {
                $attempts[] = $gpoverride->attempts;
            }
            if (isset($gpoverride->password)) {
                $passwords[] = $gpoverride->password;
            }
        }
        // If there is a user override for a setting, ignore the group override.
        if (is_null($override->timeopen) && count($opens)) {
            $override->timeopen = min($opens);
        }
        if (is_null($override->timeclose) && count($closes)) {
            if (in_array(0, $closes)) {
                $override->timeclose = 0;
            } else {
                $override->timeclose = max($closes);
            }
        }
        if (is_null($override->timelimit) && count($limits)) {
            if (in_array(0, $limits)) {
                $override->timelimit = 0;
            } else {
                $override->timelimit = max($limits);
            }
        }
        if (is_null($override->attempts) && count($attempts)) {
            if (in_array(0, $attempts)) {
                $override->attempts = 0;
            } else {
                $override->attempts = max($attempts);
            }
        }
        if (is_null($override->password) && count($passwords)) {
            $override->password = array_shift($passwords);
            if (count($passwords)) {
                $override->extrapasswords = $passwords;
            }
        }

    }

    // Merge with quiz defaults.
    $keys = array('timeopen', 'timeclose', 'timelimit', 'attempts', 'password', 'extrapasswords');
    foreach ($keys as $key) {
        if (isset($override->{$key})) {
            $quiz->{$key} = $override->{$key};
        }
    }

    return $quiz;
}

/**
 * Delete all the attempts belonging to a quiz.
 *
 * @param object $quiz The quiz object.
 */
function quiz_delete_all_attempts($quiz) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/mod/quiz/locallib.php');
    question_engine::delete_questions_usage_by_activities(new qubaids_for_quiz($quiz->id));
    $DB->delete_records('quiz_attempts', array('quiz' => $quiz->id));
    $DB->delete_records('quiz_grades', array('quiz' => $quiz->id));
}

/**
 * Delete all the attempts belonging to a user in a particular quiz.
 *
 * @param object $quiz The quiz object.
 * @param object $user The user object.
 */
function quiz_delete_user_attempts($quiz, $user) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/mod/quiz/locallib.php');
    question_engine::delete_questions_usage_by_activities(new qubaids_for_quiz_user($quiz->get_quizid(), $user->id));
    $params = [
        'quiz' => $quiz->get_quizid(),
        'userid' => $user->id,
    ];
    $DB->delete_records('quiz_attempts', $params);
    $DB->delete_records('quiz_grades', $params);
}

/**
 * Get the best current grade for a particular user in a quiz.
 *
 * @param object $quiz the quiz settings.
 * @param int $userid the id of the user.
 * @return float the user's current grade for this quiz, or null if this user does
 * not have a grade on this quiz.
 */
function quiz_get_best_grade($quiz, $userid) {
    global $DB;
    $grade = $DB->get_field('quiz_grades', 'grade',
            array('quiz' => $quiz->id, 'userid' => $userid));

    // Need to detect errors/no result, without catching 0 grades.
    if ($grade === false) {
        return null;
    }

    return $grade + 0; // Convert to number.
}

/**
 * Is this a graded quiz? If this method returns true, you can assume that
 * $quiz->grade and $quiz->sumgrades are non-zero (for example, if you want to
 * divide by them).
 *
 * @param object $quiz a row from the quiz table.
 * @return bool whether this is a graded quiz.
 */
function quiz_has_grades($quiz) {
    return $quiz->grade >= 0.000005 && $quiz->sumgrades >= 0.000005;
}

/**
 * Does this quiz allow multiple tries?
 *
 * @return bool
 */
function quiz_allows_multiple_tries($quiz) {
    $bt = question_engine::get_behaviour_type($quiz->preferredbehaviour);
    return $bt->allows_multiple_submitted_responses();
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $quiz
 * @return object|null
 */
function quiz_user_outline($course, $user, $mod, $quiz) {
    global $DB, $CFG;
    require_once($CFG->libdir . '/gradelib.php');
    $grades = grade_get_grades($course->id, 'mod', 'quiz', $quiz->id, $user->id);

    if (empty($grades->items[0]->grades)) {
        return null;
    } else {
        $grade = reset($grades->items[0]->grades);
    }

    $result = new stdClass();
    // If the user can't see hidden grades, don't return that information.
    $gitem = grade_item::fetch(array('id' => $grades->items[0]->id));
    if (!$gitem->hidden || has_capability('moodle/grade:viewhidden', context_course::instance($course->id))) {
        $result->info = get_string('grade') . ': ' . $grade->str_long_grade;
    } else {
        $result->info = get_string('grade') . ': ' . get_string('hidden', 'grades');
    }

    // Datesubmitted == time created. dategraded == time modified or time overridden
    // if grade was last modified by the user themselves use date graded. Otherwise use
    // date submitted.
    // TODO: move this copied & pasted code somewhere in the grades API. See MDL-26704.
    if ($grade->usermodified == $user->id || empty($grade->datesubmitted)) {
        $result->time = $grade->dategraded;
    } else {
        $result->time = $grade->datesubmitted;
    }

    return $result;
}

/**
 * Print a detailed representation of what a  user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $quiz
 * @return bool
 */
function quiz_user_complete($course, $user, $mod, $quiz) {
    global $DB, $CFG, $OUTPUT;
    require_once($CFG->libdir . '/gradelib.php');
    require_once($CFG->dirroot . '/mod/quiz/locallib.php');

    $grades = grade_get_grades($course->id, 'mod', 'quiz', $quiz->id, $user->id);
    if (!empty($grades->items[0]->grades)) {
        $grade = reset($grades->items[0]->grades);
        // If the user can't see hidden grades, don't return that information.
        $gitem = grade_item::fetch(array('id' => $grades->items[0]->id));
        if (!$gitem->hidden || has_capability('moodle/grade:viewhidden', context_course::instance($course->id))) {
            echo $OUTPUT->container(get_string('grade').': '.$grade->str_long_grade);
            if ($grade->str_feedback) {
                echo $OUTPUT->container(get_string('feedback').': '.$grade->str_feedback);
            }
        } else {
            echo $OUTPUT->container(get_string('grade') . ': ' . get_string('hidden', 'grades'));
            if ($grade->str_feedback) {
                echo $OUTPUT->container(get_string('feedback').': '.get_string('hidden', 'grades'));
            }
        }
    }

    if ($attempts = $DB->get_records('quiz_attempts',
            array('userid' => $user->id, 'quiz' => $quiz->id), 'attempt')) {
        foreach ($attempts as $attempt) {
            echo get_string('attempt', 'quiz', $attempt->attempt) . ': ';
            if ($attempt->state != quiz_attempt::FINISHED) {
                echo quiz_attempt_state_name($attempt->state);
            } else {
                if (!isset($gitem)) {
                    if (!empty($grades->items[0]->grades)) {
                        $gitem = grade_item::fetch(array('id' => $grades->items[0]->id));
                    } else {
                        $gitem = new stdClass();
                        $gitem->hidden = true;
                    }
                }
                if (!$gitem->hidden || has_capability('moodle/grade:viewhidden', context_course::instance($course->id))) {
                    echo quiz_format_grade($quiz, $attempt->sumgrades) . '/' . quiz_format_grade($quiz, $quiz->sumgrades);
                } else {
                    echo get_string('hidden', 'grades');
                }
            }
            echo ' - '.userdate($attempt->timemodified).'<br />';
        }
    } else {
        print_string('noattempts', 'quiz');
    }

    return true;
}

/**
 * Quiz periodic clean-up tasks.
 */
function quiz_cron() {
    global $CFG;

    require_once($CFG->dirroot . '/mod/quiz/cronlib.php');
    mtrace('');

    $timenow = time();
    $overduehander = new mod_quiz_overdue_attempt_updater();

    $processto = $timenow - get_config('quiz', 'graceperiodmin');

    mtrace('  Looking for quiz overdue quiz attempts...');

    list($count, $quizcount) = $overduehander->update_overdue_attempts($timenow, $processto);

    mtrace('  Considered ' . $count . ' attempts in ' . $quizcount . ' quizzes.');

    // Run cron for our sub-plugin types.
    cron_execute_plugin_type('quiz', 'quiz reports');
    cron_execute_plugin_type('quizaccess', 'quiz access rules');

    return true;
}

/**
 * @param int|array $quizids A quiz ID, or an array of quiz IDs.
 * @param int $userid the userid.
 * @param string $status 'all', 'finished' or 'unfinished' to control
 * @param bool $includepreviews
 * @return an array of all the user's attempts at this quiz. Returns an empty
 *      array if there are none.
 */
function quiz_get_user_attempts($quizids, $userid, $status = 'finished', $includepreviews = false) {
    global $DB, $CFG;
    // TODO MDL-33071 it is very annoying to have to included all of locallib.php
    // just to get the quiz_attempt::FINISHED constants, but I will try to sort
    // that out properly for Moodle 2.4. For now, I will just do a quick fix for
    // MDL-33048.
    require_once($CFG->dirroot . '/mod/quiz/locallib.php');

    $params = array();
    switch ($status) {
        case 'all':
            $statuscondition = '';
            break;

        case 'finished':
            $statuscondition = ' AND state IN (:state1, :state2)';
            $params['state1'] = quiz_attempt::FINISHED;
            $params['state2'] = quiz_attempt::ABANDONED;
            break;

        case 'unfinished':
            $statuscondition = ' AND state IN (:state1, :state2)';
            $params['state1'] = quiz_attempt::IN_PROGRESS;
            $params['state2'] = quiz_attempt::OVERDUE;
            break;
    }

    $quizids = (array) $quizids;
    list($insql, $inparams) = $DB->get_in_or_equal($quizids, SQL_PARAMS_NAMED);
    $params += $inparams;
    $params['userid'] = $userid;

    $previewclause = '';
    if (!$includepreviews) {
        $previewclause = ' AND preview = 0';
    }

    return $DB->get_records_select('quiz_attempts',
            "quiz $insql AND userid = :userid" . $previewclause . $statuscondition,
            $params, 'quiz, attempt ASC');
}

/**
 * Return grade for given user or all users.
 *
 * @param int $quizid id of quiz
 * @param int $userid optional user id, 0 means all users
 * @return array array of grades, false if none. These are raw grades. They should
 * be processed with quiz_format_grade for display.
 */
function quiz_get_user_grades($quiz, $userid = 0) {
    global $CFG, $DB;

    $params = array($quiz->id);
    $usertest = '';
    if ($userid) {
        $params[] = $userid;
        $usertest = 'AND u.id = ?';
    }
    return $DB->get_records_sql("
            SELECT
                u.id,
                u.id AS userid,
                qg.grade AS rawgrade,
                qg.timemodified AS dategraded,
                MAX(qa.timefinish) AS datesubmitted

            FROM {user} u
            JOIN {quiz_grades} qg ON u.id = qg.userid
            JOIN {quiz_attempts} qa ON qa.quiz = qg.quiz AND qa.userid = u.id

            WHERE qg.quiz = ?
            $usertest
            GROUP BY u.id, qg.grade, qg.timemodified", $params);
}

/**
 * Round a grade to to the correct number of decimal places, and format it for display.
 *
 * @param object $quiz The quiz table row, only $quiz->decimalpoints is used.
 * @param float $grade The grade to round.
 * @return float
 */
function quiz_format_grade($quiz, $grade) {
    if (is_null($grade)) {
        return get_string('notyetgraded', 'quiz');
    }
    return format_float($grade, $quiz->decimalpoints);
}

/**
 * Determine the correct number of decimal places required to format a grade.
 *
 * @param object $quiz The quiz table row, only $quiz->decimalpoints is used.
 * @return integer
 */
function quiz_get_grade_format($quiz) {
    if (empty($quiz->questiondecimalpoints)) {
        $quiz->questiondecimalpoints = -1;
    }

    if ($quiz->questiondecimalpoints == -1) {
        return $quiz->decimalpoints;
    }

    return $quiz->questiondecimalpoints;
}

/**
 * Round a grade to the correct number of decimal places, and format it for display.
 *
 * @param object $quiz The quiz table row, only $quiz->decimalpoints is used.
 * @param float $grade The grade to round.
 * @return float
 */
function quiz_format_question_grade($quiz, $grade) {
    return format_float($grade, quiz_get_grade_format($quiz));
}

/**
 * Update grades in central gradebook
 *
 * @category grade
 * @param object $quiz the quiz settings.
 * @param int $userid specific user only, 0 means all users.
 * @param bool $nullifnone If a single user is specified and $nullifnone is true a grade item with a null rawgrade will be inserted
 */
function quiz_update_grades($quiz, $userid = 0, $nullifnone = true) {
    global $CFG, $DB;
    require_once($CFG->libdir . '/gradelib.php');

    if ($quiz->grade == 0) {
        quiz_grade_item_update($quiz);

    } else if ($grades = quiz_get_user_grades($quiz, $userid)) {
        quiz_grade_item_update($quiz, $grades);

    } else if ($userid && $nullifnone) {
        $grade = new stdClass();
        $grade->userid = $userid;
        $grade->rawgrade = null;
        quiz_grade_item_update($quiz, $grade);

    } else {
        quiz_grade_item_update($quiz);
    }
}

/**
 * Create or update the grade item for given quiz
 *
 * @category grade
 * @param object $quiz object with extra cmidnumber
 * @param mixed $grades optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int 0 if ok, error code otherwise
 */
function quiz_grade_item_update($quiz, $grades = null) {
    global $CFG, $OUTPUT;
    require_once($CFG->dirroot . '/mod/quiz/locallib.php');
    require_once($CFG->libdir . '/gradelib.php');

    if (array_key_exists('cmidnumber', $quiz)) { // May not be always present.
        $params = array('itemname' => $quiz->name, 'idnumber' => $quiz->cmidnumber);
    } else {
        $params = array('itemname' => $quiz->name);
    }

    if ($quiz->grade > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $quiz->grade;
        $params['grademin']  = 0;

    } else {
        $params['gradetype'] = GRADE_TYPE_NONE;
    }

    // What this is trying to do:
    // 1. If the quiz is set to not show grades while the quiz is still open,
    //    and is set to show grades after the quiz is closed, then create the
    //    grade_item with a show-after date that is the quiz close date.
    // 2. If the quiz is set to not show grades at either of those times,
    //    create the grade_item as hidden.
    // 3. If the quiz is set to show grades, create the grade_item visible.
    $openreviewoptions = mod_quiz_display_options::make_from_quiz($quiz,
            mod_quiz_display_options::LATER_WHILE_OPEN);
    $closedreviewoptions = mod_quiz_display_options::make_from_quiz($quiz,
            mod_quiz_display_options::AFTER_CLOSE);
    if ($openreviewoptions->marks < question_display_options::MARK_AND_MAX &&
            $closedreviewoptions->marks < question_display_options::MARK_AND_MAX) {
        $params['hidden'] = 1;

    } else if ($openreviewoptions->marks < question_display_options::MARK_AND_MAX &&
            $closedreviewoptions->marks >= question_display_options::MARK_AND_MAX) {
        if ($quiz->timeclose) {
            $params['hidden'] = $quiz->timeclose;
        } else {
            $params['hidden'] = 1;
        }

    } else {
        // Either
        // a) both open and closed enabled
        // b) open enabled, closed disabled - we can not "hide after",
        //    grades are kept visible even after closing.
        $params['hidden'] = 0;
    }

    if (!$params['hidden']) {
        // If the grade item is not hidden by the quiz logic, then we need to
        // hide it if the quiz is hidden from students.
        if (property_exists($quiz, 'visible')) {
            // Saving the quiz form, and cm not yet updated in the database.
            $params['hidden'] = !$quiz->visible;
        } else {
            $cm = get_coursemodule_from_instance('quiz', $quiz->id);
            $params['hidden'] = !$cm->visible;
        }
    }

    if ($grades  === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }

    $gradebook_grades = grade_get_grades($quiz->course, 'mod', 'quiz', $quiz->id);
    if (!empty($gradebook_grades->items)) {
        $grade_item = $gradebook_grades->items[0];
        if ($grade_item->locked) {
            // NOTE: this is an extremely nasty hack! It is not a bug if this confirmation fails badly. --skodak.
            $confirm_regrade = optional_param('confirm_regrade', 0, PARAM_INT);
            if (!$confirm_regrade) {
                if (!AJAX_SCRIPT) {
                    $message = get_string('gradeitemislocked', 'grades');
                    $back_link = $CFG->wwwroot . '/mod/quiz/report.php?q=' . $quiz->id .
                            '&amp;mode=overview';
                    $regrade_link = qualified_me() . '&amp;confirm_regrade=1';
                    echo $OUTPUT->box_start('generalbox', 'notice');
                    echo '<p>'. $message .'</p>';
                    echo $OUTPUT->container_start('buttons');
                    echo $OUTPUT->single_button($regrade_link, get_string('regradeanyway', 'grades'));
                    echo $OUTPUT->single_button($back_link,  get_string('cancel'));
                    echo $OUTPUT->container_end();
                    echo $OUTPUT->box_end();
                }
                return GRADE_UPDATE_ITEM_LOCKED;
            }
        }
    }

    return grade_update('mod/quiz', $quiz->course, 'mod', 'quiz', $quiz->id, 0, $grades, $params);
}

/**
 * Delete grade item for given quiz
 *
 * @category grade
 * @param object $quiz object
 * @return object quiz
 */
function quiz_grade_item_delete($quiz) {
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');

    return grade_update('mod/quiz', $quiz->course, 'mod', 'quiz', $quiz->id, 0,
            null, array('deleted' => 1));
}

/**
 * This standard function will check all instances of this module
 * and make sure there are up-to-date events created for each of them.
 * If courseid = 0, then every quiz event in the site is checked, else
 * only quiz events belonging to the course specified are checked.
 * This function is used, in its new format, by restore_refresh_events()
 *
 * @param int $courseid
 * @param int|stdClass $instance Quiz module instance or ID.
 * @param int|stdClass $cm Course module object or ID (not used in this module).
 * @return bool
 */
function quiz_refresh_events($courseid = 0, $instance = null, $cm = null) {
    global $DB;

    // If we have instance information then we can just update the one event instead of updating all events.
    if (isset($instance)) {
        if (!is_object($instance)) {
            $instance = $DB->get_record('quiz', array('id' => $instance), '*', MUST_EXIST);
        }
        quiz_update_events($instance);
        return true;
    }

    if ($courseid == 0) {
        if (!$quizzes = $DB->get_records('quiz')) {
            return true;
        }
    } else {
        if (!$quizzes = $DB->get_records('quiz', array('course' => $courseid))) {
            return true;
        }
    }

    foreach ($quizzes as $quiz) {
        quiz_update_events($quiz);
    }

    return true;
}

/**
 * Returns all quiz graded users since a given time for specified quiz
 */
function quiz_get_recent_mod_activity(&$activities, &$index, $timestart,
        $courseid, $cmid, $userid = 0, $groupid = 0) {
    global $CFG, $USER, $DB;
    require_once($CFG->dirroot . '/mod/quiz/locallib.php');

    $course = get_course($courseid);
    $modinfo = get_fast_modinfo($course);

    $cm = $modinfo->cms[$cmid];
    $quiz = $DB->get_record('quiz', array('id' => $cm->instance));

    if ($userid) {
        $userselect = "AND u.id = :userid";
        $params['userid'] = $userid;
    } else {
        $userselect = '';
    }

    if ($groupid) {
        $groupselect = 'AND gm.groupid = :groupid';
        $groupjoin   = 'JOIN {groups_members} gm ON  gm.userid=u.id';
        $params['groupid'] = $groupid;
    } else {
        $groupselect = '';
        $groupjoin   = '';
    }

    $params['timestart'] = $timestart;
    $params['quizid'] = $quiz->id;

    $ufields = user_picture::fields('u', null, 'useridagain');
    if (!$attempts = $DB->get_records_sql("
              SELECT qa.*,
                     {$ufields}
                FROM {quiz_attempts} qa
                     JOIN {user} u ON u.id = qa.userid
                     $groupjoin
               WHERE qa.timefinish > :timestart
                 AND qa.quiz = :quizid
                 AND qa.preview = 0
                     $userselect
                     $groupselect
            ORDER BY qa.timefinish ASC", $params)) {
        return;
    }

    $context         = context_module::instance($cm->id);
    $accessallgroups = has_capability('moodle/site:accessallgroups', $context);
    $viewfullnames   = has_capability('moodle/site:viewfullnames', $context);
    $grader          = has_capability('mod/quiz:viewreports', $context);
    $groupmode       = groups_get_activity_groupmode($cm, $course);

    $usersgroups = null;
    $aname = format_string($cm->name, true);
    foreach ($attempts as $attempt) {
        if ($attempt->userid != $USER->id) {
            if (!$grader) {
                // Grade permission required.
                continue;
            }

            if ($groupmode == SEPARATEGROUPS and !$accessallgroups) {
                $usersgroups = groups_get_all_groups($course->id,
                        $attempt->userid, $cm->groupingid);
                $usersgroups = array_keys($usersgroups);
                if (!array_intersect($usersgroups, $modinfo->get_groups($cm->groupingid))) {
                    continue;
                }
            }
        }

        $options = quiz_get_review_options($quiz, $attempt, $context);

        $tmpactivity = new stdClass();

        $tmpactivity->type       = 'quiz';
        $tmpactivity->cmid       = $cm->id;
        $tmpactivity->name       = $aname;
        $tmpactivity->sectionnum = $cm->sectionnum;
        $tmpactivity->timestamp  = $attempt->timefinish;

        $tmpactivity->content = new stdClass();
        $tmpactivity->content->attemptid = $attempt->id;
        $tmpactivity->content->attempt   = $attempt->attempt;
        if (quiz_has_grades($quiz) && $options->marks >= question_display_options::MARK_AND_MAX) {
            $tmpactivity->content->sumgrades = quiz_format_grade($quiz, $attempt->sumgrades);
            $tmpactivity->content->maxgrade  = quiz_format_grade($quiz, $quiz->sumgrades);
        } else {
            $tmpactivity->content->sumgrades = null;
            $tmpactivity->content->maxgrade  = null;
        }

        $tmpactivity->user = user_picture::unalias($attempt, null, 'useridagain');
        $tmpactivity->user->fullname  = fullname($tmpactivity->user, $viewfullnames);

        $activities[$index++] = $tmpactivity;
    }
}

function quiz_print_recent_mod_activity($activity, $courseid, $detail, $modnames) {
    global $CFG, $OUTPUT;

    echo '<table border="0" cellpadding="3" cellspacing="0" class="forum-recent">';

    echo '<tr><td class="userpicture" valign="top">';
    echo $OUTPUT->user_picture($activity->user, array('courseid' => $courseid));
    echo '</td><td>';

    if ($detail) {
        $modname = $modnames[$activity->type];
        echo '<div class="title">';
        echo $OUTPUT->image_icon('icon', $modname, $activity->type);
        echo '<a href="' . $CFG->wwwroot . '/mod/quiz/view.php?id=' .
                $activity->cmid . '">' . $activity->name . '</a>';
        echo '</div>';
    }

    echo '<div class="grade">';
    echo  get_string('attempt', 'quiz', $activity->content->attempt);
    if (isset($activity->content->maxgrade)) {
        $grades = $activity->content->sumgrades . ' / ' . $activity->content->maxgrade;
        echo ': (<a href="' . $CFG->wwwroot . '/mod/quiz/review.php?attempt=' .
                $activity->content->attemptid . '">' . $grades . '</a>)';
    }
    echo '</div>';

    echo '<div class="user">';
    echo '<a href="' . $CFG->wwwroot . '/user/view.php?id=' . $activity->user->id .
            '&amp;course=' . $courseid . '">' . $activity->user->fullname .
            '</a> - ' . userdate($activity->timestamp);
    echo '</div>';

    echo '</td></tr></table>';

    return;
}

/**
 * Pre-process the quiz options form data, making any necessary adjustments.
 * Called by add/update instance in this file.
 *
 * @param object $quiz The variables set on the form.
 */
function quiz_process_options($quiz) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/quiz/locallib.php');
    require_once($CFG->libdir . '/questionlib.php');

    $quiz->timemodified = time();

    // Quiz name.
    if (!empty($quiz->name)) {
        $quiz->name = trim($quiz->name);
    }

    // Password field - different in form to stop browsers that remember passwords
    // getting confused.
    $quiz->password = $quiz->quizpassword;
    unset($quiz->quizpassword);

    // Quiz feedback.
    if (isset($quiz->feedbacktext)) {
        // Clean up the boundary text.
        for ($i = 0; $i < count($quiz->feedbacktext); $i += 1) {
            if (empty($quiz->feedbacktext[$i]['text'])) {
                $quiz->feedbacktext[$i]['text'] = '';
            } else {
                $quiz->feedbacktext[$i]['text'] = trim($quiz->feedbacktext[$i]['text']);
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
                if (!empty($quiz->feedbackboundaries[$i]) &&
                        trim($quiz->feedbackboundaries[$i]) != '') {
                    return get_string('feedbackerrorjunkinboundary', 'quiz', $i + 1);
                }
            }
        }
        for ($i = $numboundaries + 1; $i < count($quiz->feedbacktext); $i += 1) {
            if (!empty($quiz->feedbacktext[$i]['text']) &&
                    trim($quiz->feedbacktext[$i]['text']) != '') {
                return get_string('feedbackerrorjunkinfeedback', 'quiz', $i + 1);
            }
        }
        // Needs to be bigger than $quiz->grade because of '<' test in quiz_feedback_for_grade().
        $quiz->feedbackboundaries[-1] = $quiz->grade + 1;
        $quiz->feedbackboundaries[$numboundaries] = 0;
        $quiz->feedbackboundarycount = $numboundaries;
    } else {
        $quiz->feedbackboundarycount = -1;
    }

    // Combing the individual settings into the review columns.
    $quiz->reviewattempt = quiz_review_option_form_to_db($quiz, 'attempt');
    $quiz->reviewcorrectness = quiz_review_option_form_to_db($quiz, 'correctness');
    $quiz->reviewmarks = quiz_review_option_form_to_db($quiz, 'marks');
    $quiz->reviewspecificfeedback = quiz_review_option_form_to_db($quiz, 'specificfeedback');
    $quiz->reviewgeneralfeedback = quiz_review_option_form_to_db($quiz, 'generalfeedback');
    $quiz->reviewrightanswer = quiz_review_option_form_to_db($quiz, 'rightanswer');
    $quiz->reviewoverallfeedback = quiz_review_option_form_to_db($quiz, 'overallfeedback');
    $quiz->reviewattempt |= mod_quiz_display_options::DURING;
    $quiz->reviewoverallfeedback &= ~mod_quiz_display_options::DURING;
}

/**
 * Helper function for {@link quiz_process_options()}.
 * @param object $fromform the sumbitted form date.
 * @param string $field one of the review option field names.
 */
function quiz_review_option_form_to_db($fromform, $field) {
    static $times = array(
        'during' => mod_quiz_display_options::DURING,
        'immediately' => mod_quiz_display_options::IMMEDIATELY_AFTER,
        'open' => mod_quiz_display_options::LATER_WHILE_OPEN,
        'closed' => mod_quiz_display_options::AFTER_CLOSE,
    );

    $review = 0;
    foreach ($times as $whenname => $when) {
        $fieldname = $field . $whenname;
        if (isset($fromform->$fieldname)) {
            $review |= $when;
            unset($fromform->$fieldname);
        }
    }

    return $review;
}

/**
 * This function is called at the end of quiz_add_instance
 * and quiz_update_instance, to do the common processing.
 *
 * @param object $quiz the quiz object.
 */
function quiz_after_add_or_update($quiz) {
    global $DB;
    $cmid = $quiz->coursemodule;

    // We need to use context now, so we need to make sure all needed info is already in db.
    $DB->set_field('course_modules', 'instance', $quiz->id, array('id'=>$cmid));
    $context = context_module::instance($cmid);

    // Save the feedback.
    $DB->delete_records('quiz_feedback', array('quizid' => $quiz->id));

    for ($i = 0; $i <= $quiz->feedbackboundarycount; $i++) {
        $feedback = new stdClass();
        $feedback->quizid = $quiz->id;
        $feedback->feedbacktext = $quiz->feedbacktext[$i]['text'];
        $feedback->feedbacktextformat = $quiz->feedbacktext[$i]['format'];
        $feedback->mingrade = $quiz->feedbackboundaries[$i];
        $feedback->maxgrade = $quiz->feedbackboundaries[$i - 1];
        $feedback->id = $DB->insert_record('quiz_feedback', $feedback);
        $feedbacktext = file_save_draft_area_files((int)$quiz->feedbacktext[$i]['itemid'],
                $context->id, 'mod_quiz', 'feedback', $feedback->id,
                array('subdirs' => false, 'maxfiles' => -1, 'maxbytes' => 0),
                $quiz->feedbacktext[$i]['text']);
        $DB->set_field('quiz_feedback', 'feedbacktext', $feedbacktext,
                array('id' => $feedback->id));
    }

    // Store any settings belonging to the access rules.
    quiz_access_manager::save_settings($quiz);

    // Update the events relating to this quiz.
    quiz_update_events($quiz);
    $completionexpected = (!empty($quiz->completionexpected)) ? $quiz->completionexpected : null;
    \core_completion\api::update_completion_date_event($quiz->coursemodule, 'quiz', $quiz->id, $completionexpected);

    // Update related grade item.
    quiz_grade_item_update($quiz);
}

/**
 * This function updates the events associated to the quiz.
 * If $override is non-zero, then it updates only the events
 * associated with the specified override.
 *
 * @uses QUIZ_MAX_EVENT_LENGTH
 * @param object $quiz the quiz object.
 * @param object optional $override limit to a specific override
 */
function quiz_update_events($quiz, $override = null) {
    global $DB;

    // Load the old events relating to this quiz.
    $conds = array('modulename'=>'quiz',
                   'instance'=>$quiz->id);
    if (!empty($override)) {
        // Only load events for this override.
        if (isset($override->userid)) {
            $conds['userid'] = $override->userid;
        } else {
            $conds['groupid'] = $override->groupid;
        }
    }
    $oldevents = $DB->get_records('event', $conds, 'id ASC');

    // Now make a to-do list of all that needs to be updated.
    if (empty($override)) {
        // We are updating the primary settings for the quiz, so we need to add all the overrides.
        $overrides = $DB->get_records('quiz_overrides', array('quiz' => $quiz->id), 'id ASC');
        // It is necessary to add an empty stdClass to the beginning of the array as the $oldevents
        // list contains the original (non-override) event for the module. If this is not included
        // the logic below will end up updating the wrong row when we try to reconcile this $overrides
        // list against the $oldevents list.
        array_unshift($overrides, new stdClass());
    } else {
        // Just do the one override.
        $overrides = array($override);
    }

    // Get group override priorities.
    $grouppriorities = quiz_get_group_override_priorities($quiz->id);

    foreach ($overrides as $current) {
        $groupid   = isset($current->groupid)?  $current->groupid : 0;
        $userid    = isset($current->userid)? $current->userid : 0;
        $timeopen  = isset($current->timeopen)?  $current->timeopen : $quiz->timeopen;
        $timeclose = isset($current->timeclose)? $current->timeclose : $quiz->timeclose;

        // Only add open/close events for an override if they differ from the quiz default.
        $addopen  = empty($current->id) || !empty($current->timeopen);
        $addclose = empty($current->id) || !empty($current->timeclose);

        if (!empty($quiz->coursemodule)) {
            $cmid = $quiz->coursemodule;
        } else {
            $cmid = get_coursemodule_from_instance('quiz', $quiz->id, $quiz->course)->id;
        }

        $event = new stdClass();
        $event->type = !$timeclose ? CALENDAR_EVENT_TYPE_ACTION : CALENDAR_EVENT_TYPE_STANDARD;
        $event->description = format_module_intro('quiz', $quiz, $cmid);
        // Events module won't show user events when the courseid is nonzero.
        $event->courseid    = ($userid) ? 0 : $quiz->course;
        $event->groupid     = $groupid;
        $event->userid      = $userid;
        $event->modulename  = 'quiz';
        $event->instance    = $quiz->id;
        $event->timestart   = $timeopen;
        $event->timeduration = max($timeclose - $timeopen, 0);
        $event->timesort    = $timeopen;
        $event->visible     = instance_is_visible('quiz', $quiz);
        $event->eventtype   = QUIZ_EVENT_TYPE_OPEN;
        $event->priority    = null;

        // Determine the event name and priority.
        if ($groupid) {
            // Group override event.
            $params = new stdClass();
            $params->quiz = $quiz->name;
            $params->group = groups_get_group_name($groupid);
            if ($params->group === false) {
                // Group doesn't exist, just skip it.
                continue;
            }
            $eventname = get_string('overridegroupeventname', 'quiz', $params);
            // Set group override priority.
            if ($grouppriorities !== null) {
                $openpriorities = $grouppriorities['open'];
                if (isset($openpriorities[$timeopen])) {
                    $event->priority = $openpriorities[$timeopen];
                }
            }
        } else if ($userid) {
            // User override event.
            $params = new stdClass();
            $params->quiz = $quiz->name;
            $eventname = get_string('overrideusereventname', 'quiz', $params);
            // Set user override priority.
            $event->priority = CALENDAR_EVENT_USER_OVERRIDE_PRIORITY;
        } else {
            // The parent event.
            $eventname = $quiz->name;
        }

        if ($addopen or $addclose) {
            // Separate start and end events.
            $event->timeduration  = 0;
            if ($timeopen && $addopen) {
                if ($oldevent = array_shift($oldevents)) {
                    $event->id = $oldevent->id;
                } else {
                    unset($event->id);
                }
                $event->name = get_string('quizeventopens', 'quiz', $eventname);
                // The method calendar_event::create will reuse a db record if the id field is set.
                calendar_event::create($event, false);
            }
            if ($timeclose && $addclose) {
                if ($oldevent = array_shift($oldevents)) {
                    $event->id = $oldevent->id;
                } else {
                    unset($event->id);
                }
                $event->type      = CALENDAR_EVENT_TYPE_ACTION;
                $event->name      = get_string('quizeventcloses', 'quiz', $eventname);
                $event->timestart = $timeclose;
                $event->timesort  = $timeclose;
                $event->eventtype = QUIZ_EVENT_TYPE_CLOSE;
                if ($groupid && $grouppriorities !== null) {
                    $closepriorities = $grouppriorities['close'];
                    if (isset($closepriorities[$timeclose])) {
                        $event->priority = $closepriorities[$timeclose];
                    }
                }
                calendar_event::create($event, false);
            }
        }
    }

    // Delete any leftover events.
    foreach ($oldevents as $badevent) {
        $badevent = calendar_event::load($badevent);
        $badevent->delete();
    }
}

/**
 * Calculates the priorities of timeopen and timeclose values for group overrides for a quiz.
 *
 * @param int $quizid The quiz ID.
 * @return array|null Array of group override priorities for open and close times. Null if there are no group overrides.
 */
function quiz_get_group_override_priorities($quizid) {
    global $DB;

    // Fetch group overrides.
    $where = 'quiz = :quiz AND groupid IS NOT NULL';
    $params = ['quiz' => $quizid];
    $overrides = $DB->get_records_select('quiz_overrides', $where, $params, '', 'id, timeopen, timeclose');
    if (!$overrides) {
        return null;
    }

    $grouptimeopen = [];
    $grouptimeclose = [];
    foreach ($overrides as $override) {
        if ($override->timeopen !== null && !in_array($override->timeopen, $grouptimeopen)) {
            $grouptimeopen[] = $override->timeopen;
        }
        if ($override->timeclose !== null && !in_array($override->timeclose, $grouptimeclose)) {
            $grouptimeclose[] = $override->timeclose;
        }
    }

    // Sort open times in ascending manner. The earlier open time gets higher priority.
    sort($grouptimeopen);
    // Set priorities.
    $opengrouppriorities = [];
    $openpriority = 1;
    foreach ($grouptimeopen as $timeopen) {
        $opengrouppriorities[$timeopen] = $openpriority++;
    }

    // Sort close times in descending manner. The later close time gets higher priority.
    rsort($grouptimeclose);
    // Set priorities.
    $closegrouppriorities = [];
    $closepriority = 1;
    foreach ($grouptimeclose as $timeclose) {
        $closegrouppriorities[$timeclose] = $closepriority++;
    }

    return [
        'open' => $opengrouppriorities,
        'close' => $closegrouppriorities
    ];
}

/**
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function quiz_get_view_actions() {
    return array('view', 'view all', 'report', 'review');
}

/**
 * List the actions that correspond to a post of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 *       will be considered as post action.
 *
 * @return array
 */
function quiz_get_post_actions() {
    return array('attempt', 'close attempt', 'preview', 'editquestions',
            'delete attempt', 'manualgrade');
}

/**
 * @param array $questionids of question ids.
 * @return bool whether any of these questions are used by any instance of this module.
 */
function quiz_questions_in_use($questionids) {
    global $DB, $CFG;
    require_once($CFG->libdir . '/questionlib.php');
    list($test, $params) = $DB->get_in_or_equal($questionids);
    return $DB->record_exists_select('quiz_slots',
            'questionid ' . $test, $params) || question_engine::questions_in_use(
            $questionids, new qubaid_join('{quiz_attempts} quiza',
            'quiza.uniqueid', 'quiza.preview = 0'));
}

/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the quiz.
 *
 * @param $mform the course reset form that is being built.
 */
function quiz_reset_course_form_definition($mform) {
    $mform->addElement('header', 'quizheader', get_string('modulenameplural', 'quiz'));
    $mform->addElement('advcheckbox', 'reset_quiz_attempts',
            get_string('removeallquizattempts', 'quiz'));
    $mform->addElement('advcheckbox', 'reset_quiz_user_overrides',
            get_string('removealluseroverrides', 'quiz'));
    $mform->addElement('advcheckbox', 'reset_quiz_group_overrides',
            get_string('removeallgroupoverrides', 'quiz'));
}

/**
 * Course reset form defaults.
 * @return array the defaults.
 */
function quiz_reset_course_form_defaults($course) {
    return array('reset_quiz_attempts' => 1,
                 'reset_quiz_group_overrides' => 1,
                 'reset_quiz_user_overrides' => 1);
}

/**
 * Removes all grades from gradebook
 *
 * @param int $courseid
 * @param string optional type
 */
function quiz_reset_gradebook($courseid, $type='') {
    global $CFG, $DB;

    $quizzes = $DB->get_records_sql("
            SELECT q.*, cm.idnumber as cmidnumber, q.course as courseid
            FROM {modules} m
            JOIN {course_modules} cm ON m.id = cm.module
            JOIN {quiz} q ON cm.instance = q.id
            WHERE m.name = 'quiz' AND cm.course = ?", array($courseid));

    foreach ($quizzes as $quiz) {
        quiz_grade_item_update($quiz, 'reset');
    }
}

/**
 * Actual implementation of the reset course functionality, delete all the
 * quiz attempts for course $data->courseid, if $data->reset_quiz_attempts is
 * set and true.
 *
 * Also, move the quiz open and close dates, if the course start date is changing.
 *
 * @param object $data the data submitted from the reset course.
 * @return array status array
 */
function quiz_reset_userdata($data) {
    global $CFG, $DB;
    require_once($CFG->libdir . '/questionlib.php');

    $componentstr = get_string('modulenameplural', 'quiz');
    $status = array();

    // Delete attempts.
    if (!empty($data->reset_quiz_attempts)) {
        question_engine::delete_questions_usage_by_activities(new qubaid_join(
                '{quiz_attempts} quiza JOIN {quiz} quiz ON quiza.quiz = quiz.id',
                'quiza.uniqueid', 'quiz.course = :quizcourseid',
                array('quizcourseid' => $data->courseid)));

        $DB->delete_records_select('quiz_attempts',
                'quiz IN (SELECT id FROM {quiz} WHERE course = ?)', array($data->courseid));
        $status[] = array(
            'component' => $componentstr,
            'item' => get_string('attemptsdeleted', 'quiz'),
            'error' => false);

        // Remove all grades from gradebook.
        $DB->delete_records_select('quiz_grades',
                'quiz IN (SELECT id FROM {quiz} WHERE course = ?)', array($data->courseid));
        if (empty($data->reset_gradebook_grades)) {
            quiz_reset_gradebook($data->courseid);
        }
        $status[] = array(
            'component' => $componentstr,
            'item' => get_string('gradesdeleted', 'quiz'),
            'error' => false);
    }

    // Remove user overrides.
    if (!empty($data->reset_quiz_user_overrides)) {
        $DB->delete_records_select('quiz_overrides',
                'quiz IN (SELECT id FROM {quiz} WHERE course = ?) AND userid IS NOT NULL', array($data->courseid));
        $status[] = array(
            'component' => $componentstr,
            'item' => get_string('useroverridesdeleted', 'quiz'),
            'error' => false);
    }
    // Remove group overrides.
    if (!empty($data->reset_quiz_group_overrides)) {
        $DB->delete_records_select('quiz_overrides',
                'quiz IN (SELECT id FROM {quiz} WHERE course = ?) AND groupid IS NOT NULL', array($data->courseid));
        $status[] = array(
            'component' => $componentstr,
            'item' => get_string('groupoverridesdeleted', 'quiz'),
            'error' => false);
    }

    // Updating dates - shift may be negative too.
    if ($data->timeshift) {
        $DB->execute("UPDATE {quiz_overrides}
                         SET timeopen = timeopen + ?
                       WHERE quiz IN (SELECT id FROM {quiz} WHERE course = ?)
                         AND timeopen <> 0", array($data->timeshift, $data->courseid));
        $DB->execute("UPDATE {quiz_overrides}
                         SET timeclose = timeclose + ?
                       WHERE quiz IN (SELECT id FROM {quiz} WHERE course = ?)
                         AND timeclose <> 0", array($data->timeshift, $data->courseid));

        // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
        // See MDL-9367.
        shift_course_mod_dates('quiz', array('timeopen', 'timeclose'),
                $data->timeshift, $data->courseid);

        $status[] = array(
            'component' => $componentstr,
            'item' => get_string('openclosedatesupdated', 'quiz'),
            'error' => false);
    }

    return $status;
}

/**
 * Prints quiz summaries on MyMoodle Page
 *
 * @deprecated since 3.3
 * @todo The final deprecation of this function will take place in Moodle 3.7 - see MDL-57487.
 * @param array $courses
 * @param array $htmlarray
 */
function quiz_print_overview($courses, &$htmlarray) {
    global $USER, $CFG;

    debugging('The function quiz_print_overview() is now deprecated.', DEBUG_DEVELOPER);

    // These next 6 Lines are constant in all modules (just change module name).
    if (empty($courses) || !is_array($courses) || count($courses) == 0) {
        return array();
    }

    if (!$quizzes = get_all_instances_in_courses('quiz', $courses)) {
        return;
    }

    // Get the quizzes attempts.
    $attemptsinfo = [];
    $quizids = [];
    foreach ($quizzes as $quiz) {
        $quizids[] = $quiz->id;
        $attemptsinfo[$quiz->id] = ['count' => 0, 'hasfinished' => false];
    }
    $attempts = quiz_get_user_attempts($quizids, $USER->id);
    foreach ($attempts as $attempt) {
        $attemptsinfo[$attempt->quiz]['count']++;
        $attemptsinfo[$attempt->quiz]['hasfinished'] = true;
    }
    unset($attempts);

    // Fetch some language strings outside the main loop.
    $strquiz = get_string('modulename', 'quiz');
    $strnoattempts = get_string('noattempts', 'quiz');

    // We want to list quizzes that are currently available, and which have a close date.
    // This is the same as what the lesson does, and the dabate is in MDL-10568.
    $now = time();
    foreach ($quizzes as $quiz) {
        if ($quiz->timeclose >= $now && $quiz->timeopen < $now) {
            $str = '';

            // Now provide more information depending on the uers's role.
            $context = context_module::instance($quiz->coursemodule);
            if (has_capability('mod/quiz:viewreports', $context)) {
                // For teacher-like people, show a summary of the number of student attempts.
                // The $quiz objects returned by get_all_instances_in_course have the necessary $cm
                // fields set to make the following call work.
                $str .= '<div class="info">' . quiz_num_attempt_summary($quiz, $quiz, true) . '</div>';

            } else if (has_any_capability(array('mod/quiz:reviewmyattempts', 'mod/quiz:attempt'), $context)) { // Student
                // For student-like people, tell them how many attempts they have made.

                if (isset($USER->id)) {
                    if ($attemptsinfo[$quiz->id]['hasfinished']) {
                        // The student's last attempt is finished.
                        continue;
                    }

                    if ($attemptsinfo[$quiz->id]['count'] > 0) {
                        $str .= '<div class="info">' .
                            get_string('numattemptsmade', 'quiz', $attemptsinfo[$quiz->id]['count']) . '</div>';
                    } else {
                        $str .= '<div class="info">' . $strnoattempts . '</div>';
                    }

                } else {
                    $str .= '<div class="info">' . $strnoattempts . '</div>';
                }

            } else {
                // For ayone else, there is no point listing this quiz, so stop processing.
                continue;
            }

            // Give a link to the quiz, and the deadline.
            $html = '<div class="quiz overview">' .
                    '<div class="name">' . $strquiz . ': <a ' .
                    ($quiz->visible ? '' : ' class="dimmed"') .
                    ' href="' . $CFG->wwwroot . '/mod/quiz/view.php?id=' .
                    $quiz->coursemodule . '">' .
                    $quiz->name . '</a></div>';
            $html .= '<div class="info">' . get_string('quizcloseson', 'quiz',
                    userdate($quiz->timeclose)) . '</div>';
            $html .= $str;
            $html .= '</div>';
            if (empty($htmlarray[$quiz->course]['quiz'])) {
                $htmlarray[$quiz->course]['quiz'] = $html;
            } else {
                $htmlarray[$quiz->course]['quiz'] .= $html;
            }
        }
    }
}

/**
 * Return a textual summary of the number of attempts that have been made at a particular quiz,
 * returns '' if no attempts have been made yet, unless $returnzero is passed as true.
 *
 * @param object $quiz the quiz object. Only $quiz->id is used at the moment.
 * @param object $cm the cm object. Only $cm->course, $cm->groupmode and
 *      $cm->groupingid fields are used at the moment.
 * @param bool $returnzero if false (default), when no attempts have been
 *      made '' is returned instead of 'Attempts: 0'.
 * @param int $currentgroup if there is a concept of current group where this method is being called
 *         (e.g. a report) pass it in here. Default 0 which means no current group.
 * @return string a string like "Attempts: 123", "Attemtps 123 (45 from your groups)" or
 *          "Attemtps 123 (45 from this group)".
 */
function quiz_num_attempt_summary($quiz, $cm, $returnzero = false, $currentgroup = 0) {
    global $DB, $USER;
    $numattempts = $DB->count_records('quiz_attempts', array('quiz'=> $quiz->id, 'preview'=>0));
    if ($numattempts || $returnzero) {
        if (groups_get_activity_groupmode($cm)) {
            $a = new stdClass();
            $a->total = $numattempts;
            if ($currentgroup) {
                $a->group = $DB->count_records_sql('SELECT COUNT(DISTINCT qa.id) FROM ' .
                        '{quiz_attempts} qa JOIN ' .
                        '{groups_members} gm ON qa.userid = gm.userid ' .
                        'WHERE quiz = ? AND preview = 0 AND groupid = ?',
                        array($quiz->id, $currentgroup));
                return get_string('attemptsnumthisgroup', 'quiz', $a);
            } else if ($groups = groups_get_all_groups($cm->course, $USER->id, $cm->groupingid)) {
                list($usql, $params) = $DB->get_in_or_equal(array_keys($groups));
                $a->group = $DB->count_records_sql('SELECT COUNT(DISTINCT qa.id) FROM ' .
                        '{quiz_attempts} qa JOIN ' .
                        '{groups_members} gm ON qa.userid = gm.userid ' .
                        'WHERE quiz = ? AND preview = 0 AND ' .
                        "groupid $usql", array_merge(array($quiz->id), $params));
                return get_string('attemptsnumyourgroups', 'quiz', $a);
            }
        }
        return get_string('attemptsnum', 'quiz', $numattempts);
    }
    return '';
}

/**
 * Returns the same as {@link quiz_num_attempt_summary()} but wrapped in a link
 * to the quiz reports.
 *
 * @param object $quiz the quiz object. Only $quiz->id is used at the moment.
 * @param object $cm the cm object. Only $cm->course, $cm->groupmode and
 *      $cm->groupingid fields are used at the moment.
 * @param object $context the quiz context.
 * @param bool $returnzero if false (default), when no attempts have been made
 *      '' is returned instead of 'Attempts: 0'.
 * @param int $currentgroup if there is a concept of current group where this method is being called
 *         (e.g. a report) pass it in here. Default 0 which means no current group.
 * @return string HTML fragment for the link.
 */
function quiz_attempt_summary_link_to_reports($quiz, $cm, $context, $returnzero = false,
        $currentgroup = 0) {
    global $CFG;
    $summary = quiz_num_attempt_summary($quiz, $cm, $returnzero, $currentgroup);
    if (!$summary) {
        return '';
    }

    require_once($CFG->dirroot . '/mod/quiz/report/reportlib.php');
    $url = new moodle_url('/mod/quiz/report.php', array(
            'id' => $cm->id, 'mode' => quiz_report_default_report($context)));
    return html_writer::link($url, $summary);
}

/**
 * @param string $feature FEATURE_xx constant for requested feature
 * @return bool True if quiz supports feature
 */
function quiz_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:                    return true;
        case FEATURE_GROUPINGS:                 return true;
        case FEATURE_MOD_INTRO:                 return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:   return true;
        case FEATURE_COMPLETION_HAS_RULES:      return true;
        case FEATURE_GRADE_HAS_GRADE:           return true;
        case FEATURE_GRADE_OUTCOMES:            return true;
        case FEATURE_BACKUP_MOODLE2:            return true;
        case FEATURE_SHOW_DESCRIPTION:          return true;
        case FEATURE_CONTROLS_GRADE_VISIBILITY: return true;
        case FEATURE_USES_QUESTIONS:            return true;

        default: return null;
    }
}

/**
 * @return array all other caps used in module
 */
function quiz_get_extra_capabilities() {
    global $CFG;
    require_once($CFG->libdir . '/questionlib.php');
    $caps = question_get_all_capabilities();
    $caps[] = 'moodle/site:accessallgroups';
    return $caps;
}

/**
 * This function extends the settings navigation block for the site.
 *
 * It is safe to rely on PAGE here as we will only ever be within the module
 * context when this is called
 *
 * @param settings_navigation $settings
 * @param navigation_node $quiznode
 * @return void
 */
function quiz_extend_settings_navigation($settings, $quiznode) {
    global $PAGE, $CFG;

    // Require {@link questionlib.php}
    // Included here as we only ever want to include this file if we really need to.
    require_once($CFG->libdir . '/questionlib.php');

    // We want to add these new nodes after the Edit settings node, and before the
    // Locally assigned roles node. Of course, both of those are controlled by capabilities.
    $keys = $quiznode->get_children_key_list();
    $beforekey = null;
    $i = array_search('modedit', $keys);
    if ($i === false and array_key_exists(0, $keys)) {
        $beforekey = $keys[0];
    } else if (array_key_exists($i + 1, $keys)) {
        $beforekey = $keys[$i + 1];
    }

    if (has_capability('mod/quiz:manageoverrides', $PAGE->cm->context)) {
        $url = new moodle_url('/mod/quiz/overrides.php', array('cmid'=>$PAGE->cm->id));
        $node = navigation_node::create(get_string('groupoverrides', 'quiz'),
                new moodle_url($url, array('mode'=>'group')),
                navigation_node::TYPE_SETTING, null, 'mod_quiz_groupoverrides');
        $quiznode->add_node($node, $beforekey);

        $node = navigation_node::create(get_string('useroverrides', 'quiz'),
                new moodle_url($url, array('mode'=>'user')),
                navigation_node::TYPE_SETTING, null, 'mod_quiz_useroverrides');
        $quiznode->add_node($node, $beforekey);
    }

    if (has_capability('mod/quiz:manage', $PAGE->cm->context)) {
        $node = navigation_node::create(get_string('editquiz', 'quiz'),
                new moodle_url('/mod/quiz/edit.php', array('cmid'=>$PAGE->cm->id)),
                navigation_node::TYPE_SETTING, null, 'mod_quiz_edit',
                new pix_icon('t/edit', ''));
        $quiznode->add_node($node, $beforekey);
    }

    if (has_capability('mod/quiz:preview', $PAGE->cm->context)) {
        $url = new moodle_url('/mod/quiz/startattempt.php',
                array('cmid'=>$PAGE->cm->id, 'sesskey'=>sesskey()));
        $node = navigation_node::create(get_string('preview', 'quiz'), $url,
                navigation_node::TYPE_SETTING, null, 'mod_quiz_preview',
                new pix_icon('i/preview', ''));
        $quiznode->add_node($node, $beforekey);
    }

    if (has_any_capability(array('mod/quiz:viewreports', 'mod/quiz:grade'), $PAGE->cm->context)) {
        require_once($CFG->dirroot . '/mod/quiz/report/reportlib.php');
        $reportlist = quiz_report_list($PAGE->cm->context);

        $url = new moodle_url('/mod/quiz/report.php',
                array('id' => $PAGE->cm->id, 'mode' => reset($reportlist)));
        $reportnode = $quiznode->add_node(navigation_node::create(get_string('results', 'quiz'), $url,
                navigation_node::TYPE_SETTING,
                null, null, new pix_icon('i/report', '')), $beforekey);

        foreach ($reportlist as $report) {
            $url = new moodle_url('/mod/quiz/report.php',
                    array('id' => $PAGE->cm->id, 'mode' => $report));
            $reportnode->add_node(navigation_node::create(get_string($report, 'quiz_'.$report), $url,
                    navigation_node::TYPE_SETTING,
                    null, 'quiz_report_' . $report, new pix_icon('i/item', '')));
        }
    }

    question_extend_settings_navigation($quiznode, $PAGE->cm->context)->trim_if_empty();
}

/**
 * Serves the quiz files.
 *
 * @package  mod_quiz
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - justsend the file
 */
function quiz_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG, $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_login($course, false, $cm);

    if (!$quiz = $DB->get_record('quiz', array('id'=>$cm->instance))) {
        return false;
    }

    // The 'intro' area is served by pluginfile.php.
    $fileareas = array('feedback');
    if (!in_array($filearea, $fileareas)) {
        return false;
    }

    $feedbackid = (int)array_shift($args);
    if (!$feedback = $DB->get_record('quiz_feedback', array('id'=>$feedbackid))) {
        return false;
    }

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/mod_quiz/$filearea/$feedbackid/$relativepath";
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }
    send_stored_file($file, 0, 0, true, $options);
}

/**
 * Called via pluginfile.php -> question_pluginfile to serve files belonging to
 * a question in a question_attempt when that attempt is a quiz attempt.
 *
 * @package  mod_quiz
 * @category files
 * @param stdClass $course course settings object
 * @param stdClass $context context object
 * @param string $component the name of the component we are serving files for.
 * @param string $filearea the name of the file area.
 * @param int $qubaid the attempt usage id.
 * @param int $slot the id of a question in this quiz attempt.
 * @param array $args the remaining bits of the file path.
 * @param bool $forcedownload whether the user must be forced to download the file.
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - justsend the file
 */
function quiz_question_pluginfile($course, $context, $component,
        $filearea, $qubaid, $slot, $args, $forcedownload, array $options=array()) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/quiz/locallib.php');

    $attemptobj = quiz_attempt::create_from_usage_id($qubaid);
    require_login($attemptobj->get_course(), false, $attemptobj->get_cm());

    if ($attemptobj->is_own_attempt() && !$attemptobj->is_finished()) {
        // In the middle of an attempt.
        if (!$attemptobj->is_preview_user()) {
            $attemptobj->require_capability('mod/quiz:attempt');
        }
        $isreviewing = false;

    } else {
        // Reviewing an attempt.
        $attemptobj->check_review_capability();
        $isreviewing = true;
    }

    if (!$attemptobj->check_file_access($slot, $isreviewing, $context->id,
            $component, $filearea, $args, $forcedownload)) {
        send_file_not_found();
    }

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/$component/$filearea/$relativepath";
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        send_file_not_found();
    }

    send_stored_file($file, 0, 0, $forcedownload, $options);
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function quiz_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array(
        'mod-quiz-*'       => get_string('page-mod-quiz-x', 'quiz'),
        'mod-quiz-view'    => get_string('page-mod-quiz-view', 'quiz'),
        'mod-quiz-attempt' => get_string('page-mod-quiz-attempt', 'quiz'),
        'mod-quiz-summary' => get_string('page-mod-quiz-summary', 'quiz'),
        'mod-quiz-review'  => get_string('page-mod-quiz-review', 'quiz'),
        'mod-quiz-edit'    => get_string('page-mod-quiz-edit', 'quiz'),
        'mod-quiz-report'  => get_string('page-mod-quiz-report', 'quiz'),
    );
    return $module_pagetype;
}

/**
 * @return the options for quiz navigation.
 */
function quiz_get_navigation_options() {
    return array(
        QUIZ_NAVMETHOD_FREE => get_string('navmethod_free', 'quiz'),
        QUIZ_NAVMETHOD_SEQ  => get_string('navmethod_seq', 'quiz')
    );
}

/**
 * Obtains the automatic completion state for this quiz on any conditions
 * in quiz settings, such as if all attempts are used or a certain grade is achieved.
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not. (If no conditions, then return
 *   value depends on comparison type)
 */
function quiz_get_completion_state($course, $cm, $userid, $type) {
    global $DB;
    global $CFG;

    $quiz = $DB->get_record('quiz', array('id' => $cm->instance), '*', MUST_EXIST);
    if (!$quiz->completionattemptsexhausted && !$quiz->completionpass) {
        return $type;
    }

    // Check if the user has used up all attempts.
    if ($quiz->completionattemptsexhausted) {
        $attempts = quiz_get_user_attempts($quiz->id, $userid, 'finished', true);
        if ($attempts) {
            $lastfinishedattempt = end($attempts);
            $context = context_module::instance($cm->id);
            $quizobj = quiz::create($quiz->id, $userid);
            $accessmanager = new quiz_access_manager($quizobj, time(),
                    has_capability('mod/quiz:ignoretimelimits', $context, $userid, false));
            if ($accessmanager->is_finished(count($attempts), $lastfinishedattempt)) {
                return true;
            }
        }
    }

    // Check for passing grade.
    if ($quiz->completionpass) {
        require_once($CFG->libdir . '/gradelib.php');
        $item = grade_item::fetch(array('courseid' => $course->id, 'itemtype' => 'mod',
                'itemmodule' => 'quiz', 'iteminstance' => $cm->instance, 'outcomeid' => null));
        if ($item) {
            $grades = grade_grade::fetch_users_grades($item, array($userid), false);
            if (!empty($grades[$userid])) {
                return $grades[$userid]->is_passed($item);
            }
        }
    }
    return false;
}

/**
 * Check if the module has any update that affects the current user since a given time.
 *
 * @param  cm_info $cm course module data
 * @param  int $from the time to check updates from
 * @param  array $filter  if we need to check only specific updates
 * @return stdClass an object with the different type of areas indicating if they were updated or not
 * @since Moodle 3.2
 */
function quiz_check_updates_since(cm_info $cm, $from, $filter = array()) {
    global $DB, $USER, $CFG;
    require_once($CFG->dirroot . '/mod/quiz/locallib.php');

    $updates = course_check_module_updates_since($cm, $from, array(), $filter);

    // Check if questions were updated.
    $updates->questions = (object) array('updated' => false);
    $quizobj = quiz::create($cm->instance, $USER->id);
    $quizobj->preload_questions();
    $quizobj->load_questions();
    $questionids = array_keys($quizobj->get_questions());
    if (!empty($questionids)) {
        list($questionsql, $params) = $DB->get_in_or_equal($questionids, SQL_PARAMS_NAMED);
        $select = 'id ' . $questionsql . ' AND (timemodified > :time1 OR timecreated > :time2)';
        $params['time1'] = $from;
        $params['time2'] = $from;
        $questions = $DB->get_records_select('question', $select, $params, '', 'id');
        if (!empty($questions)) {
            $updates->questions->updated = true;
            $updates->questions->itemids = array_keys($questions);
        }
    }

    // Check for new attempts or grades.
    $updates->attempts = (object) array('updated' => false);
    $updates->grades = (object) array('updated' => false);
    $select = 'quiz = ? AND userid = ? AND timemodified > ?';
    $params = array($cm->instance, $USER->id, $from);

    $attempts = $DB->get_records_select('quiz_attempts', $select, $params, '', 'id');
    if (!empty($attempts)) {
        $updates->attempts->updated = true;
        $updates->attempts->itemids = array_keys($attempts);
    }
    $grades = $DB->get_records_select('quiz_grades', $select, $params, '', 'id');
    if (!empty($grades)) {
        $updates->grades->updated = true;
        $updates->grades->itemids = array_keys($grades);
    }

    // Now, teachers should see other students updates.
    if (has_capability('mod/quiz:viewreports', $cm->context)) {
        $select = 'quiz = ? AND timemodified > ?';
        $params = array($cm->instance, $from);

        if (groups_get_activity_groupmode($cm) == SEPARATEGROUPS) {
            $groupusers = array_keys(groups_get_activity_shared_group_members($cm));
            if (empty($groupusers)) {
                return $updates;
            }
            list($insql, $inparams) = $DB->get_in_or_equal($groupusers);
            $select .= ' AND userid ' . $insql;
            $params = array_merge($params, $inparams);
        }

        $updates->userattempts = (object) array('updated' => false);
        $attempts = $DB->get_records_select('quiz_attempts', $select, $params, '', 'id');
        if (!empty($attempts)) {
            $updates->userattempts->updated = true;
            $updates->userattempts->itemids = array_keys($attempts);
        }

        $updates->usergrades = (object) array('updated' => false);
        $grades = $DB->get_records_select('quiz_grades', $select, $params, '', 'id');
        if (!empty($grades)) {
            $updates->usergrades->updated = true;
            $updates->usergrades->itemids = array_keys($grades);
        }
    }
    return $updates;
}

/**
 * Get icon mapping for font-awesome.
 */
function mod_quiz_get_fontawesome_icon_map() {
    return [
        'mod_quiz:navflagged' => 'fa-flag',
    ];
}

/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @param calendar_event $event
 * @param \core_calendar\action_factory $factory
 * @return \core_calendar\local\event\entities\action_interface|null
 */
function mod_quiz_core_calendar_provide_event_action(calendar_event $event,
                                                     \core_calendar\action_factory $factory) {
    global $CFG, $USER;

    require_once($CFG->dirroot . '/mod/quiz/locallib.php');

    $cm = get_fast_modinfo($event->courseid)->instances['quiz'][$event->instance];
    $quizobj = quiz::create($cm->instance, $USER->id);
    $quiz = $quizobj->get_quiz();

    // Check they have capabilities allowing them to view the quiz.
    if (!has_any_capability(array('mod/quiz:reviewmyattempts', 'mod/quiz:attempt'), $quizobj->get_context())) {
        return null;
    }

    quiz_update_effective_access($quiz, $USER->id);

    // Check if quiz is closed, if so don't display it.
    if (!empty($quiz->timeclose) && $quiz->timeclose <= time()) {
        return null;
    }

    $attempts = quiz_get_user_attempts($quizobj->get_quizid(), $USER->id);
    if (!empty($attempts)) {
        // The student's last attempt is finished.
        return null;
    }

    $name = get_string('attemptquiznow', 'quiz');
    $url = new \moodle_url('/mod/quiz/view.php', [
        'id' => $cm->id
    ]);
    $itemcount = 1;
    $actionable = true;

    // Check if the quiz is not currently actionable.
    if (!empty($quiz->timeopen) && $quiz->timeopen > time()) {
        $actionable = false;
    }

    return $factory->create_instance(
        $name,
        $url,
        $itemcount,
        $actionable
    );
}

/**
 * Add a get_coursemodule_info function in case any quiz type wants to add 'extra' information
 * for the course (see resource).
 *
 * Given a course_module object, this function returns any "extra" information that may be needed
 * when printing this activity in a course listing.  See get_array_of_activities() in course/lib.php.
 *
 * @param stdClass $coursemodule The coursemodule object (record).
 * @return cached_cm_info An object on information that the courses
 *                        will know about (most noticeably, an icon).
 */
function quiz_get_coursemodule_info($coursemodule) {
    global $DB;

    $dbparams = ['id' => $coursemodule->instance];
    $fields = 'id, name, intro, introformat, completionattemptsexhausted, completionpass';
    if (!$quiz = $DB->get_record('quiz', $dbparams, $fields)) {
        return false;
    }

    $result = new cached_cm_info();
    $result->name = $quiz->name;

    if ($coursemodule->showdescription) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $result->content = format_module_intro('quiz', $quiz, $coursemodule->id, false);
    }

    // Populate the custom completion rules as key => value pairs, but only if the completion mode is 'automatic'.
    if ($coursemodule->completion == COMPLETION_TRACKING_AUTOMATIC) {
        $result->customdata['customcompletionrules']['completionattemptsexhausted'] = $quiz->completionattemptsexhausted;
        $result->customdata['customcompletionrules']['completionpass'] = $quiz->completionpass;
    }

    return $result;
}

/**
 * Callback which returns human-readable strings describing the active completion custom rules for the module instance.
 *
 * @param cm_info|stdClass $cm object with fields ->completion and ->customdata['customcompletionrules']
 * @return array $descriptions the array of descriptions for the custom rules.
 */
function mod_quiz_get_completion_active_rule_descriptions($cm) {
    // Values will be present in cm_info, and we assume these are up to date.
    if (empty($cm->customdata['customcompletionrules'])
        || $cm->completion != COMPLETION_TRACKING_AUTOMATIC) {
        return [];
    }

    $descriptions = [];
    foreach ($cm->customdata['customcompletionrules'] as $key => $val) {
        switch ($key) {
            case 'completionattemptsexhausted':
                if (empty($val)) {
                    continue;
                }
                $descriptions[] = get_string('completionattemptsexhausteddesc', 'quiz');
                break;
            case 'completionpass':
                if (empty($val)) {
                    continue;
                }
                $descriptions[] = get_string('completionpassdesc', 'quiz', format_time($val));
                break;
            default:
                break;
        }
    }
    return $descriptions;
}

/**
 * Returns the min and max values for the timestart property of a quiz
 * activity event.
 *
 * The min and max values will be the timeopen and timeclose properties
 * of the quiz, respectively, if they are set.
 *
 * If either value isn't set then null will be returned instead to
 * indicate that there is no cutoff for that value.
 *
 * If the vent has no valid timestart range then [false, false] will
 * be returned. This is the case for overriden events.
 *
 * A minimum and maximum cutoff return value will look like:
 * [
 *     [1505704373, 'The date must be after this date'],
 *     [1506741172, 'The date must be before this date']
 * ]
 *
 * @throws \moodle_exception
 * @param \calendar_event $event The calendar event to get the time range for
 * @param stdClass $quiz The module instance to get the range from
 * @return array
 */
function mod_quiz_core_calendar_get_valid_event_timestart_range(\calendar_event $event, \stdClass $quiz) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/mod/quiz/locallib.php');

    // Overrides do not have a valid timestart range.
    if (quiz_is_overriden_calendar_event($event)) {
        return [false, false];
    }

    $mindate = null;
    $maxdate = null;

    if ($event->eventtype == QUIZ_EVENT_TYPE_OPEN) {
        if (!empty($quiz->timeclose)) {
            $maxdate = [
                $quiz->timeclose,
                get_string('openafterclose', 'quiz')
            ];
        }
    } else if ($event->eventtype == QUIZ_EVENT_TYPE_CLOSE) {
        if (!empty($quiz->timeopen)) {
            $mindate = [
                $quiz->timeopen,
                get_string('closebeforeopen', 'quiz')
            ];
        }
    }

    return [$mindate, $maxdate];
}

/**
 * This function will update the quiz module according to the
 * event that has been modified.
 *
 * It will set the timeopen or timeclose value of the quiz instance
 * according to the type of event provided.
 *
 * @throws \moodle_exception
 * @param \calendar_event $event A quiz activity calendar event
 * @param \stdClass $quiz A quiz activity instance
 */
function mod_quiz_core_calendar_event_timestart_updated(\calendar_event $event, \stdClass $quiz) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/mod/quiz/locallib.php');

    if (!in_array($event->eventtype, [QUIZ_EVENT_TYPE_OPEN, QUIZ_EVENT_TYPE_CLOSE])) {
        // This isn't an event that we care about so we can ignore it.
        return;
    }

    $courseid = $event->courseid;
    $modulename = $event->modulename;
    $instanceid = $event->instance;
    $modified = false;
    $closedatechanged = false;

    // Something weird going on. The event is for a different module so
    // we should ignore it.
    if ($modulename != 'quiz') {
        return;
    }

    if ($quiz->id != $instanceid) {
        // The provided quiz instance doesn't match the event so
        // there is nothing to do here.
        return;
    }

    // We don't update the activity if it's an override event that has
    // been modified.
    if (quiz_is_overriden_calendar_event($event)) {
        return;
    }

    $coursemodule = get_fast_modinfo($courseid)->instances[$modulename][$instanceid];
    $context = context_module::instance($coursemodule->id);

    // The user does not have the capability to modify this activity.
    if (!has_capability('moodle/course:manageactivities', $context)) {
        return;
    }

    if ($event->eventtype == QUIZ_EVENT_TYPE_OPEN) {
        // If the event is for the quiz activity opening then we should
        // set the start time of the quiz activity to be the new start
        // time of the event.
        if ($quiz->timeopen != $event->timestart) {
            $quiz->timeopen = $event->timestart;
            $modified = true;
        }
    } else if ($event->eventtype == QUIZ_EVENT_TYPE_CLOSE) {
        // If the event is for the quiz activity closing then we should
        // set the end time of the quiz activity to be the new start
        // time of the event.
        if ($quiz->timeclose != $event->timestart) {
            $quiz->timeclose = $event->timestart;
            $modified = true;
            $closedatechanged = true;
        }
    }

    if ($modified) {
        $quiz->timemodified = time();
        $DB->update_record('quiz', $quiz);

        if ($closedatechanged) {
            quiz_update_open_attempts(array('quizid' => $quiz->id));
        }

        // Delete any previous preview attempts.
        quiz_delete_previews($quiz);
        quiz_update_events($quiz);
        $event = \core\event\course_module_updated::create_from_cm($coursemodule, $context);
        $event->trigger();
    }
}
