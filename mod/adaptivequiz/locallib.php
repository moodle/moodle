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
 * Some utility functions for the adaptive quiz activity.
 *
 * @copyright  2013 onwards Remote-Learner {@link http://www.remote-learner.ca/}
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/adaptivequiz/lib.php');
require_once($CFG->dirroot . '/question/editlib.php');
require_once($CFG->dirroot . '/lib/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');

use core_question\local\bank\question_edit_contexts;
use mod_adaptivequiz\event\attempt_completed;
use mod_adaptivequiz\local\attempt\attempt_state;
use mod_adaptivequiz\local\catalgo;
use qbank_managecategories\helper as qbank_managecategories_helper;

// Default tagging used.
define('ADAPTIVEQUIZ_QUESTION_TAG', 'adpq_');

// Number of attempts to display on the reporting page.
define('ADAPTIVEQUIZ_REC_PER_PAGE', 30);
// Number of questions to display for review on the page at one time.
define('ADAPTIVEQUIZ_REV_QUEST_PER_PAGE', 10);

// Attempt stopping criteria.
// The maximum number of question, defined by the adaptive parameters was achieved.
define('ADAPTIVEQUIZ_STOPCRI_MAXQUEST', 'maxqest');
// The standard error value, defined by the adaptive parameters, was achieved.
define('ADAPTIVEQUIZ_STOPCRI_STANDERR', 'stderr');
// Unable to retrieve a question, because the user either answered all of the questions in the level or no questions were found.
define('ADAPTIVEQUIZ_STOPCRI_NOQUESTFOUND', 'noqest');
// The user achieved the maximum difficulty level defined by the adaptive parameters, unable to retrieve another question.
define('ADAPTIVEQUIZ_STOPCRI_MAXLEVEL', 'maxlevel');
// The user achieved the minimum difficulty level defined by the adaptive parameters, unable to retrieve another question.
define('ADAPTIVEQUIZ_STOPCRI_MINLEVEL', 'minlevel');

/**
 * This function returns an array of question bank categories accessible to the
 * current user in the given context
 * @param context $context A context object
 * @return array An array whose keys are the question category ids and values
 * are the name of the question category
 */
function adaptivequiz_get_question_categories(context $context) {
    if (empty($context)) {
        return array();
    }

    $options      = array();
    $qesteditctx  = new question_edit_contexts($context);
    $contexts     = $qesteditctx->having_one_edit_tab_cap('editq');
    $questioncats = qbank_managecategories_helper::question_category_options($contexts);

    if (!empty($questioncats)) {
        foreach ($questioncats as $questioncatcourse) {
            foreach ($questioncatcourse as $key => $questioncat) {
                // Key format is [question cat id, question cat context id], we need to explode it.
                $questidcontext = explode(',', $key);
                $questid = array_shift($questidcontext);
                $options[$questid] = $questioncat;
            }
        }
    }

    return $options;
}

/**
 * This function is healper method to create default
 * @param object $context A context object
 * @return mixed The default category in the course context or false
 */
function adaptivequiz_make_default_categories($context) {
    if (empty($context)) {
        return false;
    }

    // Create default question categories.
    $defaultcategoryobj = question_make_default_categories(array($context));

    return $defaultcategoryobj;
}

/**
 * This function returns an array of question categories that were
 * selected for use for the activity instance
 * @param int $instance Instance id
 * @return array an array of question category ids
 */
function adaptivequiz_get_selected_question_cateogires($instance) {
    global $DB;

    $selquestcat = array();

    if (empty($instance)) {
        return array();
    }

    $records = $DB->get_records('adaptivequiz_question', array('instance' => $instance));

    if (empty($records)) {
        return array();
    }

    foreach ($records as $record) {
        $selquestcat[] = $record->questioncategory;
    }

    return $selquestcat;
}

/**
 * This function returns a count of the user's previous attempts that have been marked
 * as completed
 * @param int $instanceid activity instance id
 * @param int $userid user id
 * @return int a count of the user's previous attempts
 */
function adaptivequiz_count_user_previous_attempts($instanceid = 0, $userid = 0) {
    global $DB;

    if (empty($instanceid) || empty($userid)) {
        return 0;
    }

    $param = array('instance' => $instanceid, 'userid' => $userid, 'attemptstate' => attempt_state::COMPLETED);
    $count = $DB->count_records('adaptivequiz_attempt', $param);

    return $count;
}

/**
 * This function determins if the user has used up all of their attempts
 * @param int $maxattempts The maximum allowed attempts, 0 denotes unlimited attempts
 * @param int $attempts The number of attempts taken thus far
 * @return bool true if the attempt is allowed, otherwise false
 */
function adaptivequiz_allowed_attempt($maxattempts = 0, $attempts = 0) {
    if (0 == $maxattempts || $maxattempts > $attempts) {
        return true;
    } else {
        return false;
    }
}

/**
 * This functions validates that the unique id belongs to a user attempt of the activity instance
 * @param int $uniqueid uniqueid value of the adaptivequiz_attempt record
 * @param int $instance instance value of the adaptivequiz_attempt record
 * @param int $userid unerid value of the adaptivequiz_attempt record
 * @return bool true if the unique is part of an attempt of the activity instance, otherwise false
 */
function adaptivequiz_uniqueid_part_of_attempt($uniqueid, $instance, $userid) {
    global $DB;

    $param = array('uniqueid' => $uniqueid, 'instance' => $instance, 'userid' => $userid);
    return $DB->record_exists('adaptivequiz_attempt', $param);
}

/**
 * This function increments the difficultysum value and the number of questions attempted for the adaptivequiz_attempt record
 * @throws dml_exception A DML specific exception
 * @param int $uniqueid uniqueid value of the adaptivequiz_attempt record
 * @param int $instance instance value of the adaptivequiz_attempt record
 * @param int $userid unerid value of the adaptivequiz_attempt record
 * @param float $level the logit of the difficulty level
 * @param float $standarderror the standard error of the user's attempt
 * @param float $measure the measure of ability for the attempt
 * @return bool true of update successful, otherwise false
 */
function adaptivequiz_update_attempt_data($uniqueid, $instance, $userid, $level, $standarderror, $measure) {
    global $DB;

    // Check if the is an infinity.
    if (is_infinite($level)) {
        return false;
    }

    $param = array('uniqueid' => $uniqueid, 'instance' => $instance, 'userid' => $userid);
    try {
        $fields = 'id,difficultysum,questionsattempted,timemodified,standarderror,measure';
        $attempt = $DB->get_record('adaptivequiz_attempt', $param, $fields, MUST_EXIST);
    } catch (dml_exception $e) {
        $debuginfo = '';

        if (!empty($e->debuginfo)) {
            $debuginfo = $e->debuginfo;
        }

        throw new moodle_exception('updateattempterror', 'adaptivequiz', '', $e->getMessage(), $debuginfo);
    }

    $attempt->difficultysum = (float) $attempt->difficultysum + (float) $level;
    $attempt->questionsattempted = (int) $attempt->questionsattempted + 1;
    $attempt->standarderror = (float) $standarderror;
    $attempt->measure = (float) $measure;
    $attempt->timemodified = time();

    $DB->update_record('adaptivequiz_attempt', $attempt);

    return true;
}

/**
 * This function sets the complete status for an attempt.
 *
 * @throws dml_exception
 * @throws coding_exception
 */
function adaptivequiz_complete_attempt(
    int $uniqueid,
    stdClass $adaptivequiz,
    context_module $context,
    int $userid,
    string $standarderror,
    string $statusmessage
): void {
    global $DB;

    $attempt = $DB->get_record('adaptivequiz_attempt',
        ['uniqueid' => $uniqueid, 'instance' => $adaptivequiz->id, 'userid' => $userid], '*', MUST_EXIST);

    // Need to keep the record as it is before triggering the event below.
    $attemptrecordsnapshot = clone $attempt;

    $attempt->attemptstate = attempt_state::COMPLETED;
    $attempt->attemptstopcriteria = $statusmessage;
    $attempt->timemodified = time();
    $attempt->standarderror = $standarderror;
    $DB->update_record('adaptivequiz_attempt', $attempt);

    adaptivequiz_update_grades($adaptivequiz, $userid);

    $event = attempt_completed::create([
        'objectid' => $attempt->id,
        'context' => $context,
        'userid' => $userid
    ]);
    $event->add_record_snapshot('adaptivequiz_attempt', $attemptrecordsnapshot);
    $event->add_record_snapshot('adaptivequiz', $adaptivequiz);
    $event->trigger();
}

/**
 * This function checks whether the minimum number of attmepts have been achieved for an attempt
 * @param int $uniqueid uniqueid value of the adaptivequiz_attempt record
 * @param int $instance instance value of the adaptivequiz_attempt record
 * @param int $userid unerid value of the adaptivequiz_attempt record
 * @return bool true of record exists, otherwise false
 */
function adaptivequiz_min_attempts_reached($uniqueid, $instance, $userid) {
    global $DB;

    $sql = "SELECT adpq.id
             FROM {adaptivequiz} adpq
             JOIN {adaptivequiz_attempt} adpqa ON adpq.id = adpqa.instance
            WHERE adpqa.uniqueid = :uniqueid
                  AND adpqa.instance = :instance
                  AND adpqa.userid = :userid
                  AND adpq.minimumquestions <= adpqa.questionsattempted
         ORDER BY adpq.id ASC";

    $param = array('uniqueid' => $uniqueid, 'instance' => $instance, 'userid' => $userid);
    $exists = $DB->record_exists_sql($sql, $param);

    return $exists;
}

/**
 * This checks if the session property, needed to beging an attempt with a password, has been initialized
 * @param int $instance the activity instance id
 * @return bool true
 */
function adaptivequiz_user_entered_password($instance) {
    global $SESSION;

    $conditions = isset($SESSION->passwordcheckedadpq) && is_array($SESSION->passwordcheckedadpq) &&
            array_key_exists($instance, $SESSION->passwordcheckedadpq) && true === $SESSION->passwordcheckedadpq[$instance];
    return $conditions;
}

/**
 * Given a list of tags on a question, answer the question's difficulty.
 *
 * @param array $tags the tags on a question.
 * @return int|null the difficulty level or null if unknown.
 */
function adaptivequiz_get_difficulty_from_tags(array $tags) {
    foreach ($tags as $tag) {
        if (preg_match('/^'.ADAPTIVEQUIZ_QUESTION_TAG.'([0-9]+)$/', $tag, $matches)) {
            return (int) $matches[1];
        }
    }
    return null;
}


/**
 * @return array int => lang string the options for calculating the quiz grade
 *      from the individual attempt grades.
 */
function adaptivequiz_get_grading_options() {
    return array(
        ADAPTIVEQUIZ_GRADEHIGHEST => get_string('gradehighest', 'adaptivequiz'),
        ADAPTIVEQUIZ_ATTEMPTFIRST => get_string('attemptfirst', 'adaptivequiz'),
        ADAPTIVEQUIZ_ATTEMPTLAST  => get_string('attemptlast', 'adaptivequiz')
    );
}

/**
 * Return grade for given user or all users.
 *
 * @param stdClass $adaptivequiz The adaptivequiz
 * @param int $userid optional user id, 0 means all users
 * @return array array of grades, false if none. These are raw grades. They should
 * be processed with adaptivequiz_format_grade for display.
 */
function adaptivequiz_get_user_grades($adaptivequiz, $userid = 0) {
    global $CFG, $DB;

    $params = array(
        'instance' => $adaptivequiz->id,
        'attemptstate' => attempt_state::COMPLETED,
    );
    $userwhere = '';
    if ($userid) {
        $params['userid'] = $userid;
        $userwhere = 'AND aa.userid = :userid';
    }
    $sql = "SELECT aa.uniqueid, aa.userid, aa.measure, aa.timemodified, aa.timecreated, a.highestlevel,
               a.lowestlevel
          FROM {adaptivequiz_attempt} aa
          JOIN {adaptivequiz} a ON aa.instance = a.id
         WHERE aa.instance = :instance
               AND aa.attemptstate = :attemptstate
               $userwhere";
    $records = $DB->get_records_sql($sql, $params);

    $grades = array();
    foreach ($records as $grade) {
        $grade->rawgrade = catalgo::map_logit_to_scale($grade->measure,
            $grade->highestlevel, $grade->lowestlevel);

        if (empty($grades[$grade->userid])) {
            // Store the first attempt.
            $grades[$grade->userid] = $grade;
        } else {
            // If additional attempts are recorded, uses the settings to determine
            // which one to report.
            if ($adaptivequiz->grademethod == ADAPTIVEQUIZ_ATTEMPTFIRST) {
                if ($grade->timemodified < $grades[$grade->userid]->timemodified) {
                    $grades[$grade->userid] = $grade;
                }
            } else if ($adaptivequiz->grademethod == ADAPTIVEQUIZ_ATTEMPTLAST) {
                if ($grade->timemodified > $grades[$grade->userid]->timemodified) {
                    $grades[$grade->userid] = $grade;
                }
            } else {
                // By default, use the highst grade.
                if ($grade->rawgrade > $grades[$grade->userid]->rawgrade) {
                    $grades[$grade->userid] = $grade;
                }
            }
        }
    }
    return $grades;
}
