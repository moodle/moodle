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

namespace theme_adaptable;

defined('MOODLE_INTERNAL') || die();

use \theme_adaptable\activity_meta;

require_once($CFG->dirroot.'/mod/assign/locallib.php');

/**
 * Activity functions.
 * These functions are in a class purely for auto loading convenience.
 *
 * @package   theme_adaptable
 * @copyright Copyright (c) 2015 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @copyright Copyright (c) 2017 Manoj Solanki (Coventry University)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity {

    /**
     * @param \cm_info $mod
     * @return activity_meta
     */
    public static function module_meta(\cm_info $mod) {
        $methodname = $mod->modname . '_meta';
        if (method_exists('theme_adaptable\\activity', $methodname)) {
            $meta = call_user_func('theme_adaptable\\activity::' . $methodname, $mod);
        } else {
            $meta = new activity_meta(); // Return empty activity meta.
        }
        return $meta;
    }

    /**
     * Return standard meta data for module
     *
     * @param cm_info $mod
     * @param string $timeopenfld
     * @param string $timeclosefld
     * @param string $keyfield
     * @param string $submissiontable
     * @param string $submittedonfld
     * @param string $submitstrkey
     * @param bool $isgradeable
     * @param string $submitselect - sql to further filter submission row select statement - e.g. st.status='finished'
     * @param bool $submissionnotrequired
     * @return activity_meta
     */
    protected static function std_meta(\cm_info $mod,
            $timeopenfld,
            $timeclosefld,
            $keyfield,
            $submissiontable,
            $submittedonfld,
            $submitstrkey,
            $isgradeable = false,
            $submitselect = '',
            $submissionnotrequired = false
            ) {
        global $USER;

        $courseid = $mod->course;

        // Create meta data object.
        $meta = new activity_meta();
        $meta->submissionnotrequired = $submissionnotrequired;
        $meta->submitstrkey = $submitstrkey;
        $meta->submittedstr = get_string($submitstrkey, 'theme_adaptable');
        $meta->notsubmittedstr = get_string('not'.$submitstrkey, 'theme_adaptable');
        if (get_string_manager()->string_exists($mod->modname.'draft', 'theme_adaptable')) {
            $meta->draftstr = get_string($mod->modname.'draft', 'theme_adaptable');
        } else {
            $meta->drafstr = get_string('draft', 'theme_adaptable');
        }

        if (get_string_manager()->string_exists($mod->modname.'reopened', 'theme_adaptable')) {
            $meta->reopenedstr = get_string($mod->modname.'reopened', 'theme_adaptable');
        } else {
            $meta->reopenedstr = get_string('reopened', 'theme_adaptable');
        }

        // If module is not visible to the user then don't bother getting meta data.
        if (!$mod->uservisible) {
            return $meta;
        }

        $activitydates = self::instance_activity_dates($courseid, $mod, $timeopenfld, $timeclosefld);
        $meta->timeopen = $activitydates->timeopen;
        $meta->timeclose = $activitydates->timeclose;
        if (isset($activitydates->extension)) {
            $meta->extension = $activitydates->extension;
        }

        // If role has specific "teacher" capabilities.
        if (has_capability('mod/assign:grade', $mod->context)) {
            $meta->isteacher = true;

            // Teacher - useful teacher meta data.
            $methodnsubmissions = $mod->modname.'_num_submissions';
            $methodnungraded = $mod->modname.'_num_submissions_ungraded';

            if (method_exists('theme_adaptable\\activity', $methodnsubmissions)) {
                $meta->numsubmissions = call_user_func('theme_adaptable\\activity::'.
                    $methodnsubmissions, $courseid, $mod->instance);
            }
            if (method_exists('theme_adaptable\\activity', $methodnungraded)) {
                $meta->numrequiregrading = call_user_func('theme_adaptable\\activity::'.
                    $methodnungraded, $courseid, $mod->instance);
            }

            // If data module, get number of contributions.
            if ($mod->modname == 'data') {
                $meta->numsubmissions = self::data_num_contributions($courseid, $mod->instance);
            }
        } else {
            // Student - useful student meta data - only display if activity is available.
            if (empty($activitydates->timeopen) || $activitydates->timeopen <= time()) {

                $submissionrow = self::get_submission_row($courseid, $mod, $submissiontable, $keyfield, $submitselect);

                if (!empty($submissionrow)) {
                    if ($mod->modname === 'assign' && !empty($submissionrow->status)) {
                        switch ($submissionrow->status) {
                            case ASSIGN_SUBMISSION_STATUS_DRAFT:
                                $meta->draft = true;
                                break;

                            case ASSIGN_SUBMISSION_STATUS_REOPENED:
                                $meta->reopened = true;
                                break;

                            case ASSIGN_SUBMISSION_STATUS_SUBMITTED:
                                $meta->submitted = true;
                                break;
                        }
                    } else {
                        $meta->submitted = true;
                        $meta->timesubmitted = !empty($submissionrow->$submittedonfld) ? $submissionrow->$submittedonfld : null;
                    }
                    // If submitted on field uses modified field then fall back to timecreated if modified is 0.
                    if (empty($meta->timesubmitted) && $submittedonfld = 'timemodified') {
                        if (isset($submissionrow->timemodified)) {
                            $meta->timesubmitted = $submissionrow->timemodified;
                        } else {
                            $meta->timesubmitted = $submissionrow->timecreated;
                        }
                    }
                }
            }

            $graderow = false;
            if ($isgradeable) {
                $graderow = self::grade_row($courseid, $mod);
            }

            if ($graderow) {
                $gradeitem = \grade_item::fetch(array(
                        'itemtype' => 'mod',
                        'itemmodule' => $mod->modname,
                        'iteminstance' => $mod->instance,
                ));

                $grade = new \grade_grade(array('itemid' => $gradeitem->id, 'userid' => $USER->id));

                $coursecontext = \context_course::instance($courseid);
                $canviewhiddengrade = has_capability('moodle/grade:viewhidden', $coursecontext);

                if (!$grade->is_hidden() || $canviewhiddengrade) {
                    $meta->grade = true;
                }
            }
        }

        if (!empty($meta->timeclose)) {
            // Submission required?
            $subreqd = empty($meta->submissionnotrequired);

            // Overdue?
            $meta->overdue = $subreqd && empty($meta->submitted) && (time() > $meta->timeclose);
        }

        return $meta;
    }

    /**
     * Get assignment meta data
     *
     * @param cm_info $modinst - module instance
     * @return activity_meta
     */
    public static function assign_meta(\cm_info $modinst) {
        global $DB;
        static $submissionsenabled;

        $courseid = $modinst->course;

        // Get count of enabled submission plugins grouped by assignment id.
        // Note, under normal circumstances we only run this once but with PHP unit tests, assignments are being
        // created one after the other and so this needs to be run each time during a PHP unit test.
        if (empty($submissionsenabled) || PHPUNIT_TEST) {
            $sql = "SELECT a.id, count(1) AS submissionsenabled
                      FROM {assign} a
                      JOIN {assign_plugin_config} ac ON ac.assignment = a.id
                     WHERE a.course = ?
                       AND ac.name='enabled'
                       AND ac.value = '1'
                       AND ac.subtype='assignsubmission'
                       AND plugin!='comments'
                  GROUP BY a.id;";
            $submissionsenabled = $DB->get_records_sql($sql, array($courseid));
        }

        $submitselect = '';

        // If there aren't any submission plugins enabled for this module, then submissions are not required.
        if (empty($submissionsenabled[$modinst->instance])) {
            $submissionnotrequired = true;
        } else {
            $submissionnotrequired = false;
        }

        $meta = self::std_meta($modinst, 'allowsubmissionsfromdate', 'duedate', 'assignment', 'submission',
                'timemodified', 'submitted', true, $submitselect, $submissionnotrequired);

        return ($meta);
    }

    /**
     * Get choice module meta data
     *
     * @param cm_info $modinst - module instance
     * @return string
     */
    public static function choice_meta(\cm_info $modinst) {
        return  self::std_meta($modinst, 'timeopen', 'timeclose', 'choiceid', 'answers', 'timeseen', 'answered');
    }

    /**
     * Get database module meta data
     *
     * @param cm_info $modinst - module instance
     * @return string
     */
    public static function data_meta(\cm_info $modinst) {
        return self::std_meta($modinst, 'timeavailablefrom', 'timeavailableto', 'dataid', 'records', 'timemodified', 'contributed');
    }

    /**
     * Get feedback module meta data
     *
     * @param cm_info $modinst - module instance
     * @return string
     */
    public static function feedback_meta(\cm_info $modinst) {
        return self::std_meta($modinst, 'timeopen', 'timeclose', 'feedback', 'completed', 'timemodified', 'submitted');
    }

    /**
     * Get lesson module meta data
     *
     * @param cm_info $modinst - module instance
     * @return string
     */
    public static function lesson_meta(\cm_info $modinst) {
        $meta = self::std_meta($modinst, 'available', 'deadline', 'lessonid', 'attempts', 'timeseen', 'attempted', true);
        // TO BE DELETED: $meta->submissionnotrequired = true; ..........
        return $meta;
    }

    /**
     * Get quiz module meta data
     *
     * @param cm_info $modinst - module instance
     * @return string
     */
    public static function quiz_meta(\cm_info $modinst) {
        return self::std_meta($modinst, 'timeopen', 'timeclose', 'quiz',
                'attempts', 'timemodified', 'attempted', true, 'AND st.state=\'finished\'');
    }

    /**
     * Get all assignments (for all courses) waiting to be graded.
     *
     * @param array $courseids
     * @param int $since
     * @return array $ungraded
     */
    public static function assign_ungraded($courseids, $since = null) {
        global $DB;

        $ungraded = array();

        if ($since === null) {
            $since = time() - (12 * WEEKSECS);
        }

        // Limit to assignments with grades.
        $gradetypelimit = 'AND gi.gradetype NOT IN (' . GRADE_TYPE_NONE . ',' . GRADE_TYPE_TEXT . ')';

        foreach ($courseids as $courseid) {

            // Get the assignments that need grading.
            list($esql, $params) = get_enrolled_sql(\context_course::instance($courseid), 'mod/assign:submit', 0, true);
            $params['courseid'] = $courseid;

            $sql = "-- Snap sql
            SELECT cm.id AS coursemoduleid, a.id AS instanceid, a.course,
            a.allowsubmissionsfromdate AS opentime, a.duedate AS closetime,
            count(DISTINCT sb.userid) AS ungraded
            FROM {assign} a
            JOIN {course} c ON c.id = a.course
            JOIN {modules} m ON m.name = 'assign'

            JOIN {course_modules} cm
            ON cm.module = m.id
            AND cm.instance = a.id

            JOIN {assign_submission} sb
            ON sb.assignment = a.id
            AND sb.latest = 1

            JOIN ($esql) e
            ON e.id = sb.userid

            -- Start of join required to make assignments marked via gradebook not show as requiring grading
            -- Note: This will lead to disparity between the assignment page (mod/assign/view.php[questionmark]id=[id])
            -- and the module page will still say that 1 item requires grading.

            LEFT JOIN {assign_grades} ag
            ON ag.assignment = sb.assignment
            AND ag.userid = sb.userid
            AND ag.attemptnumber = sb.attemptnumber

            LEFT JOIN {grade_items} gi
            ON gi.courseid = a.course
            AND gi.itemtype = 'mod'
            AND gi.itemmodule = 'assign'
            AND gi.itemnumber = 0
            AND gi.iteminstance = cm.instance

            LEFT JOIN {grade_grades} gg
            ON gg.itemid = gi.id
            AND gg.userid = sb.userid

            -- End of join required to make assignments classed as graded when done via gradebook

            WHERE sb.status = 'submitted'
            AND a.course = :courseid

            AND (
            sb.timemodified > gg.timemodified
            OR gg.finalgrade IS NULL
            )

            AND (a.duedate = 0 OR a.duedate > $since)
            $gradetypelimit
            GROUP BY instanceid, a.course, opentime, closetime, coursemoduleid ORDER BY a.duedate ASC";
            $rs = $DB->get_records_sql($sql, $params);
            $ungraded = array_merge($ungraded, $rs);
        }

        return $ungraded;
    }

    /**
     * Get Quizzes waiting to be graded.
     *
     * @param array $courseids
     * @param int $since
     * @return array $ungraded
     */
    public static function quiz_ungraded($courseids, $since = null) {
        global $DB;

        if ($since === null) {
            $since = time() - (12 * WEEKSECS);
        }

        $ungraded = array();

        foreach ($courseids as $courseid) {

            // Get people who are typically not students (people who can view grader report) so that we can exclude them!
            list($graderids, $params) = get_enrolled_sql(\context_course::instance($courseid), 'moodle/grade:viewall');
            $params['courseid'] = $courseid;

            $sql = "-- Snap SQL
            SELECT cm.id AS coursemoduleid, q.id AS instanceid, q.course,
            q.timeopen AS opentime, q.timeclose AS closetime,
            count(DISTINCT qa.userid) AS ungraded
            FROM {quiz} q
            JOIN {course} c ON c.id = q.course AND q.course = :courseid
            JOIN {modules} m ON m.name = 'quiz'
            JOIN {course_modules} cm ON cm.module = m.id AND cm.instance = q.id

            -- Get ALL ungraded attempts for this quiz

            JOIN {quiz_attempts} qa ON qa.quiz = q.id
            AND qa.sumgrades IS NULL

            -- Exclude those people who can grade quizzes

            WHERE qa.userid NOT IN ($graderids)
            AND qa.state = 'finished'
            AND (q.timeclose = 0 OR q.timeclose > $since)
            GROUP BY instanceid, q.course, opentime, closetime, coursemoduleid
            ORDER BY q.timeclose ASC";

            $rs = $DB->get_records_sql($sql, $params);
            $ungraded = array_merge($ungraded, $rs);
        }

        return $ungraded;
    }

    // The lesson_ungraded function has been removed as it was very tricky to implement.
    // This was because it creates a grade record as soon as a student finishes the lesson.

    /**
     * Get number of ungraded submissions for specific assignment
     * Based on count_submissions_need_grading() in mod/assign/locallib.php
     *
     * @param int $courseid
     * @param int $modid
     * @return int
     */
    public static function assign_num_submissions_ungraded($courseid, $modid) {
        global $DB;

        static $hasgrades = null;
        static $totalsbyid;

        // Use cache to see if assign has grades.
        if ($hasgrades != null && !isset($hasgrades[$modid])) {
            return 0;
        }

        // Use cache to return number of assigns yet to be graded.
        if (!empty($totalsbyid)) {
            if (isset($totalsbyid[$modid])) {
                return intval($totalsbyid[$modid]->total);
            } else {
                return 0;
            }
        }

        // Check to see if this assign is graded.
        $params = array(
                'courseid'      => $courseid,
                'itemtype'      => 'mod',
                'itemmodule'    => 'assign',
                'gradetypenone' => GRADE_TYPE_NONE,
                'gradetypetext' => GRADE_TYPE_TEXT,
        );

        $sql = 'SELECT iteminstance
                FROM {grade_items}
                WHERE courseid = ?
                AND itemtype = ?
                AND itemmodule = ?
                AND gradetype <> ?
                AND gradetype <> ?';

        $hasgrades = $DB->get_records_sql($sql, $params);

        if (!isset($hasgrades[$modid])) {
            return 0;
        }

        // Get grading information for remaining of assigns.
        $coursecontext = \context_course::instance($courseid);
        list($esql, $params) = get_enrolled_sql($coursecontext, 'mod/assign:submit', 0, true);

        $params['submitted'] = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $params['courseid'] = $courseid;

        $sql = "-- Snap sql
        SELECT sb.assignment, count(sb.userid) AS total
        FROM {assign_submission} sb

        JOIN {assign} an
        ON sb.assignment = an.id

        LEFT JOIN {assign_grades} ag
        ON sb.assignment = ag.assignment
        AND sb.userid = ag.userid
        AND sb.attemptnumber = ag.attemptnumber

        -- Start of join required to make assignments marked via gradebook not show as requiring grading
        -- Note: This will lead to disparity between the assignment page (mod/assign/view.php[questionmark]id=[id])
        -- and the module page will still say that 1 item requires grading.

        LEFT JOIN {grade_items} gi
        ON gi.courseid = an.course
        AND gi.itemtype = 'mod'
        AND gi.itemmodule = 'assign'
        AND gi.itemnumber = 0
        AND gi.iteminstance = an.id

        LEFT JOIN {grade_grades} gg
        ON gg.itemid = gi.id
        AND gg.userid = sb.userid

        -- End of join required to make assignments classed as graded when done via gradebook

        -- Start of enrolment join to make sure we only include students that are allowed to submit. Note this causes an ALL
        -- join on mysql!
        JOIN ($esql) e
        ON e.id = sb.userid
        -- End of enrolment join

        WHERE an.course = :courseid
        AND sb.timemodified IS NOT NULL
        AND sb.status = :submitted
        AND sb.latest = 1

        AND (
        sb.timemodified > gg.timemodified
        OR gg.finalgrade IS NULL
        )

        GROUP BY sb.assignment
        ";

        $totalsbyid = $DB->get_records_sql($sql, $params);
        return isset($totalsbyid[$modid]) ? intval($totalsbyid[$modid]->total) : 0;
    }

    /**
     * Standard function for getting number of submissions (where sql is not complicated and pretty much standard)
     *
     * @param int $courseid
     * @param int $modid
     * @param string $maintable
     * @param string $mainkey
     * @param string $submittable
     * @return int
     */
    protected static function std_num_submissions($courseid,
            $modid,
            $maintable,
            $mainkey,
            $submittable,
            $extraselect = '') {
        global $DB;

        static $modtotalsbyid = array();

        if (!isset($modtotalsbyid[$maintable][$courseid])) {
            // Results are not cached, so lets get them.

            // Get people who are typically not students (people who can view grader report) so that we can exclude them!
            list($graderids, $params) = get_enrolled_sql(\context_course::instance($courseid), 'moodle/grade:viewall');
            $params['courseid'] = $courseid;

            // Get the number of submissions for all $maintable activities in this course.
            $sql = "-- Snap sql
            SELECT m.id, COUNT(DISTINCT sb.userid) as totalsubmitted
              FROM {".$maintable."} m
              JOIN {".$submittable."} sb ON m.id = sb.$mainkey
              WHERE m.course = :courseid
              AND sb.userid NOT IN ($graderids)
              $extraselect
              GROUP BY m.id";
              $modtotalsbyid[$maintable][$courseid] = $DB->get_records_sql($sql, $params);
        }
        $totalsbyid = $modtotalsbyid[$maintable][$courseid];

        if (!empty($totalsbyid)) {
            if (isset($totalsbyid[$modid])) {
                return intval($totalsbyid[$modid]->totalsubmitted);
            }
        }
        return 0;
    }

    /**
     * Assign module function for getting number of submissions
     *
     * @param int $courseid
     * @param int $modid
     * @return int
     */
    public static function assign_num_submissions($courseid, $modid) {
        global $DB;

        static $modtotalsbyid = array();

        if (!isset($modtotalsbyid['assign'][$courseid])) {
            // Results are not cached, so lets get them.

            list($esql, $params) = get_enrolled_sql(\context_course::instance($courseid), 'mod/assign:submit', 0, true);
            $params['courseid'] = $courseid;
            $params['submitted'] = ASSIGN_SUBMISSION_STATUS_SUBMITTED;

            // Get the number of submissions for all assign activities in this course.
            $sql = "-- Snap sql
            SELECT m.id, COUNT(sb.userid) as totalsubmitted
            FROM {assign} m
            JOIN {assign_submission} sb
            ON m.id = sb.assignment
            AND sb.latest = 1

            JOIN ($esql) e
            ON e.id = sb.userid

            WHERE m.course = :courseid
            AND sb.status = :submitted
            GROUP by m.id";
            $modtotalsbyid['assign'][$courseid] = $DB->get_records_sql($sql, $params);
        }
        $totalsbyid = $modtotalsbyid['assign'][$courseid];

        if (!empty($totalsbyid)) {
            if (isset($totalsbyid[$modid])) {
                return intval($totalsbyid[$modid]->totalsubmitted);
            }
        }
        return 0;
    }

    /**
     * Data module function for getting number of contributions
     *
     * @param int $courseid
     * @param int $modid
     * @return int
     */
    public static function data_num_contributions($courseid, $modid) {
        global $DB;

        static $modtotalsbyid = array();

        if (!isset($modtotalsbyid['data'][$modid])) {
            $params['dataid'] = $modid;

            // Get the number of contributions for this data activity.
            $sql = '
             SELECT d.id, count(dataid) as total FROM {data_records} r, {data} d
                WHERE r.dataid = d.id AND r.dataid = :dataid';

            $modtotalsbyid['data'][$modid] = $DB->get_records_sql($sql, $params);
        }
        $totalsbyid = $modtotalsbyid['data'][$modid];
        // TO BE DELETED echo '<br>' . print_r($totalsbyid, 1) . '<br>'; ....
        if (!empty($totalsbyid)) {
            if (isset($totalsbyid[$modid])) {
                return intval($totalsbyid[$modid]->total);
            }
        }
        return 0;
    }

    /**
     * Get number of answers for specific choice
     *
     * @param int $courseid
     * @param int $choiceid
     * @return int
     */
    public static function choice_num_submissions($courseid, $modid) {
        return self::std_num_submissions($courseid, $modid, 'choice', 'choiceid', 'choice_answers');
    }

    /**
     * Get number of submissions for feedback activity
     *
     * @param int $courseid
     * @param int $feedbackid
     * @return int
     */
    public static function feedback_num_submissions($courseid, $modid) {
        return self::std_num_submissions($courseid, $modid, 'feedback', 'feedback', 'feedback_completed');
    }

    /**
     * Get number of submissions for lesson activity
     *
     * @param int $courseid
     * @param int $feedbackid
     * @return int
     */
    public static function lesson_num_submissions($courseid, $modid) {
        return self::std_num_submissions($courseid, $modid, 'lesson', 'lessonid', 'lesson_attempts');
    }

    /**
     * Get number of attempts for specific quiz
     *
     * @param int $courseid
     * @param int $quizid
     * @return int
     */
    public static function quiz_num_submissions($courseid, $modid) {
        return self::std_num_submissions($courseid, $modid, 'quiz', 'quiz', 'quiz_attempts');
    }

    /**
     * Get number of ungraded quiz attempts for specific quiz
     *
     * @param int $courseid
     * @param int $quizid
     * @return int
     */
    public static function quiz_num_submissions_ungraded($courseid, $quizid) {
        global $DB;

        static $totalsbyquizid;

        $coursecontext = \context_course::instance($courseid);
        // Get people who are typically not students (people who can view grader report) so that we can exclude them!
        list($graderids, $params) = get_enrolled_sql($coursecontext, 'moodle/grade:viewall');
        $params['courseid'] = $courseid;

        if (!isset($totalsbyquizid)) {
            // Results are not cached.
            $sql = "-- Snap sql
            SELECT q.id, count(DISTINCT qa.userid) as total
            FROM {quiz} q

            -- Get ALL ungraded attempts for this quiz

            JOIN {quiz_attempts} qa ON qa.quiz = q.id
            AND qa.sumgrades IS NULL

            -- Exclude those people who can grade quizzes

            WHERE qa.userid NOT IN ($graderids)
            AND qa.state = 'finished'
            AND q.course = :courseid
            GROUP BY q.id";
            $totalsbyquizid = $DB->get_records_sql($sql, $params);
        }

        if (!empty($totalsbyquizid)) {
            if (isset($totalsbyquizid[$quizid])) {
                return intval($totalsbyquizid[$quizid]->total);
            }
        }

        return 0;
    }

    /**
     * Get activity submission row
     *
     * @param $mod
     * @param $submissiontable
     * @param $modfield
     * @param $tabrow
     * @return mixed
     */
    public static function get_submission_row($courseid, $mod, $submissiontable, $modfield, $extraselect='') {
        global $DB, $USER;

        // Note: Caches all submissions to minimise database transactions.
        static $submissions = array();

        // Pull from cache?
        if (!PHPUNIT_TEST) {
            if (isset($submissions[$courseid.'_'.$mod->modname])) {
                if (isset($submissions[$courseid.'_'.$mod->modname][$mod->instance])) {
                    return $submissions[$courseid.'_'.$mod->modname][$mod->instance];
                } else {
                    return false;
                }
            }
        }

        $submissiontable = $mod->modname.'_'.$submissiontable;

        if ($mod->modname === 'assign') {
            $params = [$courseid, $USER->id];
            $sql = "-- Snap sql
                SELECT a.id AS instanceid, st.*
                    FROM {".$submissiontable."} st

                    JOIN {".$mod->modname."} a
                    ON a.id = st.$modfield

                    WHERE a.course = ?
                    AND st.latest = 1
                    AND st.userid = ? $extraselect
                    ORDER BY $modfield DESC, st.id DESC";
        } else {
            // Less effecient general purpose for other module types.
            $params = [$USER->id, $courseid, $USER->id];
            $sql = "-- Snap sql
                SELECT a.id AS instanceid, st.*
                    FROM {".$submissiontable."} st

                    JOIN {".$mod->modname."} a
                    ON a.id = st.$modfield

                    -- Get only the most recent submission.
                    JOIN (SELECT $modfield AS modid, MAX(id) AS maxattempt
                    FROM {".$submissiontable."}
                    WHERE userid = ?
                    GROUP BY modid) AS smx
                    ON smx.modid = st.$modfield
                    AND smx.maxattempt = st.id

                    WHERE a.course = ?
                    AND st.userid = ? $extraselect
                    ORDER BY $modfield DESC, st.id DESC";
        }

        // Not every activity has a status field...
        // Add one if it is missing so code assuming there is a status property doesn't explode.
        $result = $DB->get_records_sql($sql, $params);
        if (!$result) {
            unset($submissions[$courseid.'_'.$mod->modname]);
            return false;
        }

        foreach ($result as $r) {
            if (!isset($r->status)) {
                $r->status = null;
            }
        }

        $submissions[$courseid.'_'.$mod->modname] = $result;

        if (isset($submissions[$courseid.'_'.$mod->modname][$mod->instance])) {
            return $submissions[$courseid.'_'.$mod->modname][$mod->instance];
        } else {
            return false;
        }
    }

    /**
     * Get the activity dates for a specific module instance
     *
     * @param $courseid
     * @param stdClass $mod
     * @param string $timeopenfld
     * @param string $timeclosefld
     *
     * @return bool|stdClass
     */
    public static function instance_activity_dates($courseid, $mod, $timeopenfld = '', $timeclosefld = '') {
        global $DB, $USER;
        // Note: Caches all moduledates to minimise database transactions.
        static $moddates = array();
        if (!isset($moddates[$courseid . '_' . $mod->modname][$mod->instance]) || PHPUNIT_TEST) {
            $timeopenfld = $mod->modname === 'quiz' ? 'timeopen' : ($mod->modname === 'lesson' ? 'available' : $timeopenfld);
            $timeclosefld = $mod->modname === 'quiz' ? 'timeclose' : ($mod->modname === 'lesson' ? 'deadline' : $timeclosefld);
            $sql = "-- Snap sql
            SELECT
            module.id,
            module.$timeopenfld AS timeopen,
            module.$timeclosefld AS timeclose";
            if ($mod->modname === 'assign') {
                $sql .= ",
                    auf.extensionduedate AS extension
                ";
            }
            if ($mod->modname === 'quiz' || $mod->modname === 'lesson') {
                $id = $mod->modname === 'quiz' ? $mod->modname : 'lessonid';
                $groups = groups_get_user_groups($courseid);
                $groupbysql = '';
                $params = array();
                if ($groups[0]) {
                    list ($groupsql, $params) = $DB->get_in_or_equal($groups[0]);
                    if ($DB->get_dbfamily() === 'mysql') {
                        $sql .= ",
                        CASE
                        WHEN ovrd1.$timeopenfld IS NULL
                        THEN MIN(ovrd2.$timeopenfld)
                        ELSE ovrd1.$timeopenfld
                        END AS timeopenover,
                        CASE
                        WHEN ovrd1.$timeclosefld IS NULL
                        THEN MAX(ovrd2.$timeclosefld)
                        ELSE ovrd1.$timeclosefld
                        END AS timecloseover
                        FROM {" . $mod->modname . "} module";
                    } else {
                        $sql .= ",
                        MIN (
                        CASE
                        WHEN ovrd1.$timeopenfld IS NULL
                        THEN ovrd2.$timeopenfld
                        ELSE ovrd1.$timeopenfld
                        END
                        ) AS timeopenover,
                        MAX (
                        CASE
                        WHEN ovrd1.$timeclosefld IS NULL
                        THEN ovrd2.$timeclosefld
                        ELSE ovrd1.$timeclosefld
                        END
                        ) AS timecloseover
                        FROM {" . $mod->modname . "} module";
                    }
                    array_unshift($params, $USER->id); // Add userid to start of params.
                    $sql .= "
                        LEFT JOIN {" . $mod->modname . "_overrides} ovrd1
                        ON module.id=ovrd1.$id
                        AND ovrd1.userid = ?
                        LEFT JOIN {" . $mod->modname . "_overrides} ovrd2
                        ON module.id=ovrd2.$id
                        AND ovrd2.groupid $groupsql";
                    $groupbysql = "
                    GROUP BY module.id, module.$timeopenfld, module.$timeclosefld";

                } else {
                    $params[] = $USER->id;
                    $sql .= ", ovrd1.$timeopenfld AS timeopenover, ovrd1.$timeclosefld AS timecloseover
                    FROM {" . $mod->modname . "} module
                             LEFT JOIN {" . $mod->modname . "_overrides} ovrd1
                             ON module.id=ovrd1.$id AND ovrd1.userid = ?";
                }
                $sql .= " WHERE module.course = ?";
                $sql .= $groupbysql;
                $params[] = $courseid;
                $result = $DB->get_records_sql($sql, $params);
            } else {
                $params = [];
                $sql .= "  FROM {" . $mod->modname . "} module";
                if ($mod->modname === 'assign') {
                    $params[] = $USER->id;
                    $sql .= "
                      LEFT JOIN {assign_user_flags} auf
                             ON module.id = auf.assignment
                            AND auf.userid = ?
                     ";
                }
                $params[] = $courseid;
                $sql .= " WHERE module.course = ?";
                $result = $DB->get_records_sql($sql, $params);
            }
            $moddates[$courseid . '_' . $mod->modname] = $result;
        }
        $modinst = $moddates[$courseid.'_'.$mod->modname][$mod->instance];
        if (!empty($modinst->timecloseover)) {
            $modinst->timeclose = $modinst->timecloseover;
            if ($modinst->timeopenover) {
                $modinst->timeopen = $modinst->timeopenover;
            }
        }
        return $modinst;

    }

    /**
     * Return grade row for specific module instance.
     *
     * @param $courseid
     * @param $mod
     * @param $modfield
     * @return bool
     */
    public static function grade_row($courseid, $mod) {
        global $DB, $USER;

        static $grades = array();

        if (isset($grades[$courseid.'_'.$mod->modname])
            && isset($grades[$courseid.'_'.$mod->modname][$mod->instance])
            ) {
                return $grades[$courseid.'_'.$mod->modname][$mod->instance];
        }

        $sql = "-- Snap sql
        SELECT m.id AS instanceid, gg.*

            FROM {".$mod->modname."} m

            JOIN {grade_items} gi
              ON m.id = gi.iteminstance
             AND gi.itemtype = 'mod'
             AND gi.itemmodule = :modname
             AND gi.courseid = :courseid1

            JOIN {grade_grades} gg
              ON gi.id = gg.itemid

           WHERE m.course = :courseid2
             AND gg.userid = :userid
             AND (
                 gg.rawgrade IS NOT NULL
                 OR gg.finalgrade IS NOT NULL
                 OR gg.feedback IS NOT NULL
             )
             ";
        $params = array(
                'modname' => $mod->modname,
                'courseid1' => $courseid,
                'courseid2' => $courseid,
                'userid' => $USER->id
        );
        $grades[$courseid.'_'.$mod->modname] = $DB->get_records_sql($sql, $params);

        if (isset($grades[$courseid.'_'.$mod->modname][$mod->instance])) {
            return $grades[$courseid.'_'.$mod->modname][$mod->instance];
        } else {
            return false;
        }
    }

    /**
     * Get everything graded from a specific date to the current date.
     *
     * @param bool $onlyactive - only show grades in courses actively enrolled on if true.
     * @param null|int $showfrom - timestamp to show grades from. Note if not set defaults to 1 month ago.
     * @return mixed
     */
    public static function events_graded($onlyactive = true, $showfrom = null) {
        global $DB, $USER;

        $params = [];
        $coursesql = '';
        if ($onlyactive) {
            $courses = enrol_get_my_courses();
            $courseids = array_keys($courses);
            $courseids[] = SITEID;
            list ($coursesql, $params) = $DB->get_in_or_equal($courseids);
            $coursesql = 'AND gi.courseid '.$coursesql;
        }

        $onemonthago = time() - (DAYSECS * 31);
        $showfrom = $showfrom !== null ? $showfrom : $onemonthago;

        $sql = "-- Snap sql
        SELECT gg.*, gi.itemmodule, gi.iteminstance, gi.courseid, gi.itemtype
        FROM {grade_grades} gg
        JOIN {grade_items} gi
        ON gg.itemid = gi.id $coursesql
        WHERE gg.userid = ?
        AND (gg.timemodified > ?
        OR gg.timecreated > ?)
        AND (gg.finalgrade IS NOT NULL
        OR gg.rawgrade IS NOT NULL
        OR gg.feedback IS NOT NULL)
        AND gi.itemtype = 'mod'
        ORDER BY timemodified DESC";

        $params = array_merge($params, [$USER->id, $showfrom, $showfrom]);
        $grades = $DB->get_records_sql($sql, $params, 0, 5);

        $eventdata = array();
        foreach ($grades as $grade) {
            $eventdata[] = $grade;
        }

        return $eventdata;
    }

    /**
     * Return extension date for user on assignment.
     * @param int $assignmentid
     * @return int | bool
     */
    public static function assignment_user_extension_date($assignmentid) {
        global $USER, $DB;
        $vars = array('assignment' => $assignmentid, 'userid' => $USER->id);
        $row = $DB->get_record('assign_user_flags', $vars, 'extensionduedate');
        return $row ? $row->extensionduedate : false;
    }
}
