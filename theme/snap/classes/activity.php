<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace theme_snap;

use dml_exception;
use grade_item;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/assign/locallib.php');

/**
 * Activity functions.
 * These functions are in a class purely for auto loading convenience.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity {

    public static $phpunitallowcaching = false;

    /**
     * @param \cm_info $mod
     * @return activity_meta
     */
    public static function module_meta(\cm_info $mod) {
        $methodname = $mod->modname . '_meta';
        if (method_exists('theme_snap\\activity', $methodname)) {
            $meta = call_user_func('theme_snap\\activity::' . $methodname, $mod);
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
        $meta->submittedstr = get_string($submitstrkey, 'theme_snap');
        $meta->notsubmittedstr = get_string('not'.$submitstrkey, 'theme_snap');
        if (get_string_manager()->string_exists($mod->modname.'draft', 'theme_snap')) {
            $meta->draftstr = get_string($mod->modname.'draft', 'theme_snap');
        } else {
            $meta->draftstr = get_string('draft', 'theme_snap');
        }

        if (get_string_manager()->string_exists($mod->modname.'reopened', 'theme_snap')) {
            $meta->reopenedstr = get_string($mod->modname.'reopened', 'theme_snap');
        } else {
            $meta->reopenedstr = get_string('reopened', 'theme_snap');
        }

        // If module is not visible to the user then don't bother getting meta data.
        if (!$mod->visibleoncoursepage) {
            return $meta;
        }

        $activitydates = self::instance_activity_dates($courseid, $mod, $timeopenfld, $timeclosefld);
        $meta->timeopen = $activitydates->timeopen;
        $meta->timeclose = $activitydates->timeclose;
        $meta->timesfromcache = !empty($activitydates->fromcache);

        if (isset($activitydates->extension)) {
            $meta->extension = $activitydates->extension;
        }

        // TODO: use activity specific "teacher" capabilities.
        if (has_capability('mod/assign:grade', $mod->context)) {
            $meta->isteacher = true;

            // Teacher - useful teacher meta data.
            $methodnsubmissions = $mod->modname.'_num_submissions';
            $methodnungraded = $mod->modname.'_num_submissions_ungraded';

            if (method_exists('theme_snap\\activity', $methodnsubmissions)) {
                $meta->numsubmissions = call_user_func('theme_snap\\activity::'.$methodnsubmissions, $courseid, $mod->instance);
            }
            if (method_exists('theme_snap\\activity', $methodnungraded)) {
                $meta->numrequiregrading = call_user_func('theme_snap\\activity::'.$methodnungraded, $courseid, $mod->instance);
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
                // Looking for a visible grade.
                $gradeitems = grade_item::fetch_all([
                    'courseid'     => $courseid,
                    'itemtype'     => 'mod',
                    'itemmodule'   => $mod->modname,
                    'iteminstance' => $mod->instance,
                ]);

                $coursecontext = \context_course::instance($courseid);
                foreach ($gradeitems as $gradeitem) {
                    $grade = new \grade_grade(['itemid' => $gradeitem->id, 'userid' => $USER->id]);
                    $canviewhiddengrade = has_capability('moodle/grade:viewhidden', $coursecontext);
                    if (!$grade->is_hidden() || $canviewhiddengrade) {
                        $meta->grade = true; // Found a visible grade, item is graded.
                        break;
                    }
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
        static $coursequeried;

        $courseid = $modinst->course;

        // Get count of enabled submission plugins grouped by assignment id.
        // Note, under normal circumstances we only run this once but with PHP unit tests, assignments are being
        // created one after the other and so this needs to be run each time during a PHP unit test.
        if (empty($submissionsenabled) || $coursequeried !== $courseid || PHPUNIT_TEST) {
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
            $coursequeried = $courseid;
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
        $meta->submissionnotrequired = true;
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
     * Get forum module meta data
     *
     * @param cm_info $modinst - module instance
     * @return string
     */
    public static function forum_meta(\cm_info $modinst) {
        $meta = new activity_meta();
        $courseid = $modinst->course;
        $activitydates = self::instance_activity_dates($courseid, $modinst, 'timemodified', 'duedate');
        $meta->timeopen = $activitydates->timeopen;
        $meta->timeclose = $activitydates->timeclose;
        $meta->timesfromcache = !empty($activitydates->fromcache);
        return $meta;
    }

    /**
     * Get scorm module meta data
     *
     * @param cm_info $modinst - module instance
     * @return string
     */
    public static function scorm_meta(\cm_info $modinst) {
        $meta = new activity_meta();
        $courseid = $modinst->course;
        $activitydates = self::instance_activity_dates($courseid, $modinst, 'timeopen', 'timeclose');
        $meta->timeopen = $activitydates->timeopen;
        $meta->timeclose = $activitydates->timeclose;
        $meta->timesfromcache = !empty($activitydates->fromcache);
        return $meta;
    }

    /**
     * Get workshop module meta data
     *
     * @param cm_info $modinst - module instance
     * @return string
     */
    public static function workshop_meta(\cm_info $modinst) {
        $meta = new activity_meta();
        $courseid = $modinst->course;
        $activitydates = self::instance_activity_dates($courseid, $modinst, 'submissionstart', 'submissionend');
        $meta->timeopen = $activitydates->timeopen;
        $meta->timeclose = $activitydates->timeclose;
        $meta->timesfromcache = !empty($activitydates->fromcache);
        return $meta;
    }


    /**
     * Get all assignments (for all courses) waiting to be graded.
     *
     * @param array $courseids
     * @param int $since
     * @return array $ungraded
     */
    public static function assign_ungraded($courseids, $since = null) {
        global $DB, $CFG;

        $ungraded = array();

        if ($since === null) {
            $since = time() - (12 * WEEKSECS);
        }
        if (!empty($CFG->theme_snap_grading_cache)) {
            $cache = \cache::make('theme_snap', 'course_users_assign_ungraded');
        }
        // Limit to assignments with grades.
        $gradetypelimit = 'AND gi.gradetype NOT IN (' . GRADE_TYPE_NONE . ',' . GRADE_TYPE_TEXT . ')';
        foreach ($courseids as $courseid) {

            if (!empty($CFG->theme_snap_grading_cache)) {
                $users = $cache->get($courseid);
                if (empty($users)) {
                    // Get the assignments that need grading.
                    [$esql, $params] = get_enrolled_sql(\context_course::instance($courseid), 'mod/assign:submit', 0, true);
                    $users = array_keys($DB->get_records_sql_menu($esql, $params));
                    $cache->set($courseid, !empty($users) ? $users : []);
                }

                if (empty($users)) {
                    continue;
                }
                $esql = '';
                list($usersql, $userparams) = $DB->get_in_or_equal($users, SQL_PARAMS_NAMED);
                $usersql = " AND sb.userid $usersql ";
            } else {
                [$esql, $params] = get_enrolled_sql(\context_course::instance($courseid), 'mod/assign:submit', 0, true);
                $esql = " JOIN ($esql) e ON e.id = sb.userid ";
                $usersql = '';
                $userparams = [];
            }

            $params['courseid'] = $courseid;

            [$sqlgroupsjoin, $sqlgroupswhere, $groupparams] = self::get_groups_sql($courseid);

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
                      $esql  
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
                       $sqlgroupsjoin

-- End of join required to make assignments classed as graded when done via gradebook

                     WHERE sb.status = 'submitted'
                       AND a.course = :courseid
                       $usersql
                       AND (
                           sb.timemodified > gg.timemodified
                           OR gg.finalgrade IS NULL
                       )

                       AND (a.duedate = 0 OR a.duedate > $since)
                       $sqlgroupswhere
                 $gradetypelimit
                 GROUP BY instanceid, a.course, opentime, closetime, coursemoduleid ORDER BY a.duedate ASC";
            $rs = $DB->get_records_sql($sql, array_merge($params, $groupparams, $userparams));
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
        global $DB, $CFG;

        if ($since === null) {
            $since = time() - (12 * WEEKSECS);
        }

        $ungraded = array();
        if (!empty($CFG->theme_snap_grading_cache)) {
            $cache = \cache::make('theme_snap', 'course_users_quiz_ungraded');
        }

        foreach ($courseids as $courseid) {
            if (!empty($CFG->theme_snap_grading_cache)) {
                $users = $cache->get($courseid);
                if (empty($users)) {
                    // Get the assignments that need grading.
                    [$esql, $params] = get_enrolled_sql(\context_course::instance($courseid), 'moodle/grade:viewall');
                    $users = array_keys($DB->get_records_sql_menu($esql, $params));
                    $cache->set($courseid, !empty($users) ? $users : []);
                }

                if (empty($users)) {
                    continue;
                }
                list($gradersql, $params) = $DB->get_in_or_equal($users, SQL_PARAMS_NAMED, 'param', false);
            } else {
                // Get people who are typically not students (people who can view grader report) so that we can exclude them!
                [$gradersql, $params] = get_enrolled_sql(\context_course::instance($courseid), 'moodle/grade:viewall');
                $gradersql = "NOT IN ($gradersql)";
            }
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
					   AND qa.preview = 0

-- Exclude those people who can grade quizzes and suspended users

          		      JOIN {enrol} en ON en.courseid = q.course
                      JOIN {user_enrolments} ue ON en.id = ue.enrolid
                       AND qa.userid = ue.userid
                     WHERE ue.status = 0
                       AND qa.userid $gradersql
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
        if (!empty($totalsbyid) && !local::duringtesting()) {
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

        $sql = 'SELECT DISTINCT iteminstance
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
        list($course, $cm) = (get_course_and_cm_from_instance($modid, 'assign', $courseid));
        $currentgroup = groups_get_activity_group($cm, true);

        // Get grading information for remaining of assigns.
        $coursecontext = \context_course::instance($courseid);
        [$esql, $params] = get_enrolled_sql($coursecontext, 'mod/assign:submit', $currentgroup, true);

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
            [$graderids, $params] = get_enrolled_sql(\context_course::instance($courseid), 'moodle/grade:viewall');
            $params['courseid'] = $courseid;

            if ($maintable == 'quiz') {
                $quizvalidation = "AND sb.preview = 0";
            } else {
                $quizvalidation = "";
            }
            // Get the number of submissions for all $maintable activities in this course.
            $sql = "-- Snap sql
                    SELECT m.id, COUNT(DISTINCT sb.userid) as totalsubmitted
                      FROM {".$maintable."} m
                      JOIN {".$submittable."} sb ON m.id = sb.$mainkey
                      JOIN {enrol} en ON en.courseid = m.course
                      JOIN {user_enrolments} ue ON en.id = ue.enrolid
                       AND sb.userid = ue.userid
                     WHERE ue.status = 0
                       AND m.course = :courseid
                           AND sb.userid NOT IN ($graderids)
                           $quizvalidation
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

            [$esql, $params] = get_enrolled_sql(\context_course::instance($courseid), 'mod/assign:submit', 0, true);
            $params['courseid'] = $courseid;
            $params['submitted'] = ASSIGN_SUBMISSION_STATUS_SUBMITTED;

            [$sqlgroupsjoin, $sqlgroupswhere, $groupparams] = self::get_groups_sql($courseid);

            // Get the number of submissions for all assign activities in this course.
            $sql = "-- Snap sql
                SELECT m.id, COUNT(sb.userid) as totalsubmitted
                  FROM {assign} m
                  JOIN {assign_submission} sb
                    ON m.id = sb.assignment
                   AND sb.latest = 1

                  JOIN ($esql) e
                    ON e.id = sb.userid
                       $sqlgroupsjoin

                 WHERE m.course = :courseid
                       AND sb.status = :submitted
                       $sqlgroupswhere
                 GROUP by m.id";
            $modtotalsbyid['assign'][$courseid] = $DB->get_records_sql($sql, array_merge($params, $groupparams));
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
     * Get number of contributors to the database
     *
     * @param int $courseid
     * @param int $modid
     * @return int
     */
    public static function data_num_submissions($courseid, $modid) {
        return self::std_num_submissions($courseid, $modid, 'data', 'dataid', 'data_records');
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
        [$graderids, $params] = get_enrolled_sql($coursecontext, 'moodle/grade:viewall');
        $params['courseid'] = $courseid;

        if (!isset($totalsbyquizid)) {
            // Results are not cached.
            $sql = "-- Snap sql
                    SELECT q.id, count(DISTINCT qa.userid) as total
                      FROM {quiz} q

-- Get ALL ungraded attempts for this quiz

					  JOIN {quiz_attempts} qa ON qa.quiz = q.id
					   AND qa.sumgrades IS NULL
					   AND qa.preview = 0

-- Exclude those people who can grade quizzes and suspended users

                      JOIN {enrol} en ON en.courseid = q.course
                      JOIN {user_enrolments} ue ON en.id = ue.enrolid
                       AND qa.userid = ue.userid
                     WHERE ue.status = 0
                       AND qa.userid NOT IN ($graderids)
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

        if ($mod->modname === 'assign') {
            $parameter = [$courseid];
            $sqlgrps = "-- Snap sql
                SELECT st.*
                    FROM {".$submissiontable."} st

                    JOIN {".$mod->modname."} a
                      ON a.id = st.$modfield

                   WHERE NOT st.groupid = 0
                     AND a.course = ?
                     AND st.latest = 1
                     AND a.teamsubmission = 1
                ORDER BY $modfield DESC, st.id DESC";
            $grpssubmissions = $DB->get_records_sql($sqlgrps, $parameter);

            foreach ($grpssubmissions as $grpssub) {
                if (groups_is_member($grpssub->groupid, $USER->id)) {
                    if (array_key_exists($grpssub->assignment, $result)) {
                        $result[$grpssub->assignment]->status = $grpssub->status;
                    }
                }
            }
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
     * Take events array and rehash by modulename instance
     * @param array $events
     * @return array
     */
    protected static function hash_events_by_module_instance(array $events) {
        $tmparr = [];
        foreach ($events as $event) {

            if (!isset($tmparr[$event->modulename])) {
                $tmparr[$event->modulename] = [];
            }

            if (!isset($tmparr[$event->modulename][$event->instance])) {
                $tmparr[$event->modulename][$event->instance] = [];
            }

            $tmparr[$event->modulename][$event->instance][] = $event;
        }
        return $tmparr;
    }

    /**
     * Get the activity open from date for a specific module instance
     *
     * @param $courseid
     * @param \cm_info $mod
     * @param string $timeopenfld
     * @param string $timeclosefld
     *
     * @return bool|stdClass
     */
    public static function instance_activity_dates($courseid, \cm_info $mod, $timeopenfld = '', $timeclosefld = '') {
        global $DB, $USER, $COURSE;

        // Note: Caches all moduledates to minimise database transactions.
        static $moddates = [];

        // Did we use the MUC to get the events from the calendar?
        static $eventsfromcache = false;

        // Note: Caches all moduledates by instance to minimise db transactions.
        static $eventsbymodinst = [];

        $modname = $mod->modname;
        $modinst = $mod->instance;

        $phpunittest = defined('PHPUNIT_TEST') && PHPUNIT_TEST;

        if (!empty($moddates[$courseid.'_'.$modname][$modinst]) && !$phpunittest) {
            return $moddates[$courseid.'_'.$modname][$modinst];
        }

        if ($modname === 'quiz') {
            $timeopenfld = 'timeopen';
            $timeclosefld = 'timeclose';
        } else if ($modname === 'lesson') {
            $timeopenfld = 'available';
            $timeclosefld = 'deadline';
        }

        if ($mod->modname != 'assign') {
            // Get moddates WITHOUT overrides.
            $sql = "-- Snap sql
                    SELECT id, $timeopenfld AS timeopen, $timeclosefld as timeclose
                        FROM {" . $modname . "}
                    WHERE course = ?";
            $params = [$courseid];
        } else {
            // Get assignment moddates + time opening overrides.
            // Assignment doesn't put opening time overrides in the calendar so we need to get them here.
            $groups = groups_get_user_groups($courseid);

            if ($groups[0]) {
                [$groupsql, $params] = $DB->get_in_or_equal($groups[0]);
                $sortorder = count($groups[0]) > 1 ? 'AND ma.id = ? ORDER BY maog.sortorder LIMIT 1' : "";

                $sql = "-- Snap sql
                    SELECT ma.id,
                      CASE
                      WHEN mao.allowsubmissionsfromdate IS NOT NULL
                      THEN mao.allowsubmissionsfromdate
                      ELSE CASE WHEN maog.allowsubmissionsfromdate IS NOT NULL
                      THEN maog.allowsubmissionsfromdate
                      ELSE ma.allowsubmissionsfromdate
                      END
                      END AS timeopen,
                      CASE
                      WHEN mao.duedate IS NOT NULL
                      THEN mao.duedate
                      ELSE CASE WHEN maog.duedate IS NOT NULL
                      THEN maog.duedate
                      ELSE ma.duedate
                      END
                      END AS timeclose
                    FROM {assign} ma

                LEFT JOIN {assign_overrides} mao ON mao.assignid = ma.id AND mao.userid = ? AND mao.groupid IS NULL
                LEFT JOIN {assign_overrides} maog ON maog.assignid = ma.id AND maog.groupid $groupsql 
                WHERE course = ? $sortorder";

                array_unshift($params, $USER->id);
                $params[] = $courseid;
                if (count($groups[0]) > 1) {
                    $params[] = $modinst;
                }

            } else {

                $sql = "-- Snap sql
                    SELECT ma.id,
                      CASE
                      WHEN mao.allowsubmissionsfromdate IS NOT NULL
                      THEN mao.allowsubmissionsfromdate
                      ELSE ma.allowsubmissionsfromdate
                      END AS timeopen,
                      CASE
                      WHEN mao.duedate IS NOT NULL
                      THEN mao.duedate
                      ELSE ma.duedate
                      END AS timeclose
                     FROM {assign} ma

                LEFT JOIN {assign_overrides} mao ON mao.assignid = ma.id AND mao.userid = ? AND mao.groupid IS NULL

                    WHERE course = ?";

                $params = [$USER->id, $courseid];
            }

        }
        $moddates[$courseid . '_' . $modname] = $DB->get_records_sql($sql, $params);

        // Override moddates with calendar dates.
        // Note - we only get 1 years of dates to use for overrides, etc.
        // This means 6 months after an override date expires it will show the default date.
        $tz = new \DateTimeZone(\core_date::get_user_timezone($USER));
        $today = new \DateTime('today', $tz);
        $todayts = $today->getTimestamp();
        $tstart = $todayts - (YEARSECS / 2);
        $tend = $todayts + (YEARSECS / 2);

        if ($phpunittest || !isset($eventsbymodinst[$courseid])) {
            if ($COURSE->id == $courseid) {
                $coursesparam = [$courseid => $COURSE];
            } else {
                $coursesparam = [$courseid => get_course($courseid)];
            }
            $eventsobj = self::user_activity_events($coursesparam, $tstart, $tend, 'incourse', 1000);
            $events = $eventsobj->events;
            $eventsfromcache = $eventsobj->fromcache;
            $eventsbymodinst[$courseid] = self::hash_events_by_module_instance($events);
        }

        if ($mod->modname == 'assign') {
            foreach ($moddates[$courseid . '_' . $modname] as $assign) {
                $assigninstance = $assign->id;
                $dates = $moddates[$courseid . '_' . $modname][$assigninstance];
                // Check if there is any extension.
                $flags = $DB->get_record('assign_user_flags', array('assignment' => $assigninstance, 'userid' => $USER->id));
                if (!empty($flags->extensionduedate)) {
                    // If there is an extension, then assign the duedate of the extension.
                    $timeclose = $flags->extensionduedate;
                } else {
                    $timeclose = $dates->timeclose;
                }
                $timeopen = $dates->timeopen;
                $instdates = (object)[
                    'timeopen' => $timeopen,
                    'timeclose' => $timeclose,
                    'fromcache' => false,
                ];
                $moddates[$courseid . '_' . $modname][$assigninstance] = $instdates;
            }
            return $moddates[$courseid.'_'.$modname][$modinst];
        }

        // Extract opening time and closing time from events.

        if (!empty($eventsbymodinst[$courseid][$modname])) {
            foreach ($eventsbymodinst[$courseid][$modname] as $modinstevents) {
                $timeopen = null;
                $timeclose = null;
                foreach ($modinstevents as $event) {
                    if ($event->timestart === null) {
                        continue;
                    }

                    if ($event->eventtype === 'open') {
                        $timeopen = $event->timestart;
                    } else if (($event->eventtype === 'close' || $event->eventtype === 'due')) {
                        $timeclose = $event->timestart + $event->timeduration;
                    }

                }

                // If we have a null time open or close, use initial dates gotten from module query.
                $initialdates = null;
                if (!empty($moddates[$courseid . '_' . $modname][$event->instance])) {
                    $initialdates = $moddates[$courseid . '_' . $modname][$event->instance];
                }
                if ($timeopen === null && !empty($initialdates)) {
                    $timeopen = $initialdates->timeopen;
                }
                if ($timeclose === null && !empty($initialdates)) {
                    $timeclose = $initialdates->timeclose;
                }

                $instdates = (object)[
                    'timeopen' => $timeopen,
                    'timeclose' => $timeclose,
                    'fromcache' => $eventsfromcache,
                ];

                if ($event->modulename === $modname) {
                    // Only statically cache for the current module type we are requesting.
                    $moddates[$courseid . '_' . $modname][$event->instance] = $instdates;
                }
            }
        }

        return $moddates[$courseid.'_'.$modname][$modinst];

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
                  SELECT DISTINCT m.id AS instanceid

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
            'userid' => $USER->id,
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
            [$coursesql, $params] = $DB->get_in_or_equal($courseids);
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
     * Note: This function is not optimised for usage in big loops but it does have the advantage of using core logic
     * for evaluating override priority.
     * Get the most appropriate due date, including overrides and extensions.
     * @param int $assignid
     * @param stdClass | int $userid
     * @return stdClass
     * @throws \coding_exception
     */
    public static function assignment_due_date_info($assignid, $userid) {
        global $CFG;

        require_once($CFG->dirroot.'/mod/assign/locallib.php');

        $duedateinfo = (object) ['duedate' => null, 'extended' => false];

        [$course, $cminfo] = get_course_and_cm_from_instance($assignid, 'assign');
        unset($course);

        // Check overrides.
        $assign = new \assign($cminfo->context, $cminfo, false);
        $overrides = $assign->override_exists($userid);
        if (!empty($overrides->duedate)) {
            $duedate = $overrides->duedate;
        } else {
            $duedate = $assign->get_instance()->duedate;
        }

        // Check deadline extensions.
        $flags = $assign->get_user_flags($userid, true);
        if (!empty($flags->extensionduedate)) {
            // Extension always overwrites duedate, even if it's less than due date or overridden due date.
            $duedate = $flags->extensionduedate;
            $duedateinfo->extended = true;
        }

        $duedateinfo->duedate = $duedate;
        return $duedateinfo;
    }

    /**
     * Get all events restricted by various parameters, taking in to account user and group overrides.
     * Copied from calendar/classes/local/api.php.
     * Uses
     *
     * @param int|null      $timestartfrom         Events with timestart from this value (inclusive).
     * @param int|null      $timestartto           Events with timestart until this value (inclusive).
     * @param int|null      $timesortfrom          Events with timesort from this value (inclusive).
     * @param int|null      $timesortto            Events with timesort until this value (inclusive).
     * @param int|null      $timestartaftereventid Restrict the events in the timestart range to ones after this ID.
     * @param int|null      $timesortaftereventid  Restrict the events in the timesort range to ones after this ID.
     * @param int           $limitnum              Return at most this number of events.
     * @param int|null      $type                  Return only events of this type.
     * @param array|null    $usersfilter           Return only events for these users.
     * @param array|null    $groupsfilter          Return only events for these groups.
     * @param array|null    $coursesfilter         Return only events for these courses.
     * @param bool          $withduration          If true return only events starting within specified
     *                                             timestart otherwise return in progress events as well.
     * @param bool          $ignorehidden          If true don't return hidden events.
     * @return \core_calendar\local\event\entities\event_interface[] Array of event_interfaces.
     */
    public static function get_events(
        $timestartfrom = null,
        $timestartto = null,
        $timesortfrom = null,
        $timesortto = null,
        $timestartaftereventid = null,
        $timesortaftereventid = null,
        $limitnum = 20,
        $type = null,
        array $usersfilter = null,
        array $groupsfilter = null,
        array $coursesfilter = null,
        $withduration = true,
        $ignorehidden = true,
        callable $filter = null,
        ?string $searchvalue = null
    ) {

        \theme_snap\calendar\event\container::ovd_init();
        $vault = \theme_snap\calendar\event\container::get_event_vault();

        $timestartafterevent = null;
        $timesortafterevent = null;

        if ($timestartaftereventid && $event = $vault->get_event_by_id($timestartaftereventid)) {
            $timestartafterevent = $event;
        }

        if ($timesortaftereventid && $event = $vault->get_event_by_id($timesortaftereventid)) {
            $timesortafterevent = $event;
        }

        return $vault->get_events(
            $timestartfrom,
            $timestartto,
            $timesortfrom,
            $timesortto,
            $timestartafterevent,
            $timesortafterevent,
            $limitnum,
            $type,
            $usersfilter,
            $groupsfilter,
            $coursesfilter,
            null,
            $withduration,
            $ignorehidden
        );
    }

    /**
     * Get calendar activity events for specific date range and array of courses.
     * Note - only deals with due, open, close event types.
     * @param int $tstart
     * @param int $tend
     * @param stdClass[] $courses
     * @param string $cachesuffix
     * @param int $limit
     * @return object
     * @throws dml_exception
     */
    public static function get_calendar_activity_events($tstart, $tend, array $courses, $cachesuffix = '', $limit = 200) {
        global $DB, $USER;
        $retobj = (object) [
            'timestamp' => null,
            'events' => [],
            'courses' => [],
            'fromcache' => false,
        ];

        if (empty($courses)) {
            return $retobj;
        }

        // The cache key includes the start and end dates rounded to a day.
        $dstart = strtotime(date('Y-m-d', $tstart));
        $dend = strtotime(date('Y-m-d', $tend));

        // Cache key HAS to have courses.
        $cachekey = self::get_id_indexed_array_cache_key($courses);

        // It also can have group ids for this user within the courses.
        $groupkey = self::get_user_group_cache_key($USER, $courses);
        if (!empty($groupkey)) {
            $cachekey .= '_' . $groupkey;
        }

        // And an optional suffix.
        if (!empty($cachesuffix)) {
            $cachekey .= '_' . $cachesuffix;
        }
        $freshkey = $cachekey.'_'.($dstart + $dend).'_'.$limit;

        if (self::$phpunitallowcaching || !(defined('PHPUNIT_TEST') && PHPUNIT_TEST)) {
            $muc = \cache::make('theme_snap', 'activity_deadlines');
            $cached = $muc->get($cachekey);
            $cachefresh = false;
            if ($cached && $cached->key !== $freshkey) {
                $cachefresh = false;
            } else if ($cached && $cached->timestamp >= time() - HOURSECS) {
                $cachestamps = local::get_calendar_change_stamps();
                $activitiesstamp = $cached->timestamp;
                $cachefresh = true; // Until proven otherwise.
                $coursecache = [];
                foreach ($courses as $courseid => $course) {
                    $coursecache[$courseid] = $course->shortname;

                    if (!isset($cached->courses[$courseid])) {
                        $cachefresh = false;
                    }
                    if (isset($cachestamps[$courseid])) {
                        $stamp = $cachestamps[$courseid];
                        if ($stamp > $activitiesstamp) {
                            $cachefresh = false;
                        }
                    }
                }
                $cmids = [];
                foreach ($cached->events as $event) {
                    if (!empty($event->actionurl)) {
                        $cmids[] = $event->actionurl->get_param("id");
                    }
                    if (!isset($courses[$event->courseid])) {
                        $cachefresh = false;
                    }
                }
                if (!empty($cmids)) {
                    [$insql, $params] = $DB->get_in_or_equal($cmids);
                    $sql = "SELECT deletioninprogress
                          FROM {course_modules}
                         WHERE id $insql
                           AND deletioninprogress = 1";
                    $deletioninprogress = $DB->get_record_sql($sql, $params);
                }
                if (!empty($deletioninprogress)) {
                    $cachefresh = false;
                }
            }

            if ($cachefresh) {
                $cached->fromcache = true; // Useful for debugging and unit testing.
                return $cached;
            }
        }

        $calendar = new \calendar_information(0, 0, 0, $tstart);
        $course = get_course(SITEID);
        $calendar->set_sources($course, $courses);

        $withduration = true;
        $ignorehidden = true;
        $mapper = \core_calendar\local\event\container::get_event_mapper();

        // Normalise the users, groups and courses parameters so that they are compliant with
        // the calendar apis get_events method.
        // Existing functions that were using the old calendar_get_events() were passing a mixture of array, int,
        // boolean for these parameters, but with the new API method, only null and arrays are accepted.
        [$userparam, $groupparam, $courseparam] = array_map(function($param) {
            // If parameter is true, return null.
            if ($param === true) {
                return null;
            }

            // If parameter is false, return an empty array.
            if ($param === false) {
                return [];
            }

            // If the parameter is a scalar value, enclose it in an array.
            if (!is_array($param)) {
                return [$param];
            }

            // No normalisation required.
            return $param;
        }, [$calendar->users, $calendar->groups, $calendar->courses]);

        // This will keep count of the amount of concurrent queries made to the events table.
        // The amount of queries can be stated using the flag $CFG->theme_snap_max_concurrent_deadline_queries.
        // If the amount is reached, an exception will be thrown.
        // It's default to INF, i.e., it's disabled.
        self::start_deadline_query();

        // I present to you, the events query.
        $events = self::get_events(
            $tstart,
            $tend,
            null,
            null,
            null,
            null,
            $limit,
            null,
            $userparam,
            $groupparam,
            $courseparam,
            $withduration,
            $ignorehidden
        );

        // Quickly reduce query count to allow other users to run the query.
        self::end_deadline_query();

        $events = array_reduce($events, function($carry, $event) use ($mapper) {
            return $carry + [$event->get_id() => $mapper->from_event_to_stdclass($event)];
        }, []);

        if (!isset($coursecache)) {
            foreach ($courses as $courseid => $course) {
                $coursecache[$courseid] = $course->shortname;
            }
        }

        $retobj->timestamp = microtime(true);
        $retobj->events = $events;

        if (self::$phpunitallowcaching || !(defined('PHPUNIT_TEST') && PHPUNIT_TEST)) {
            $retobj->courses = $coursecache;
            $retobj->key = $freshkey;
            $muc->set($cachekey, $retobj);
        }

        return $retobj;
    }

    /**
     * Return deadlines from calendar associated to a set of courses.
     *
     * @param stdClass[] $courses array of courses hashed by course id.
     * @param int $tstart
     * @param int $tend
     * @param string $cachesuffix
     * @param int $limit
     * @param boolean $skipcmchecks Skip course module checks
     * @return object
     */
    public static function user_activity_events(array $courses, $tstart, $tend, $cachesuffix = '', $limit = 500,
                                                $skipcmchecks = false) {
        $retobj = (object) [
            'timestamp' => null,
            'events' => [],
            'courses' => [],
            'fromcache' => false,
        ];

        if (empty($courses)) {
            return $retobj;
        }

        $retobj = self::get_calendar_activity_events($tstart, $tend, $courses, $cachesuffix, $limit);

        if ($skipcmchecks) {
            // Skip CM checks. This should only be done for populating caches or getting data that does not need formatting.
            return $retobj;
        }

        // Filter down array and also modify event name if necessary;
        // Note, filter_array cannot be used here as we need to modify the event name, not just filter.
        $tmparr = [];
        foreach ($retobj->events as $event) {

            // Validation added to prevent array offset.
            $courseid = array_key_exists($event->courseid, $courses) ? $courses[$event->courseid] : 0;

            [$course, $cminfo] = get_course_and_cm_from_instance(
                    $event->instance, $event->modulename,  $courseid, $event->userid);
            unset($course);

            // We are only interested in modules with valid instances.
            if (empty($cminfo)) {
                continue;
            }

            if (!$cminfo->uservisible) {
                continue;
            }
            if ($event->eventtype === 'close') {
                // Revert the addition of e.g. "(Quiz closes)" to the event name.
                $event->name = $cminfo->name;
            }

            if (isset($courses[$event->courseid])) {
                $course = $courses[$event->courseid];
                $event->coursefullname = format_string($course->fullname);
            }

            $tmparr[$event->id] = $event;

        }
        $retobj->events = $tmparr;
        unset($tmparr);

        return $retobj;
    }

    /**
     * Return user's upcoming activity deadlines from the calendar.
     *
     * All deadlines from today, then any from the next 6 months up to the
     * max requested.
     * @param stdClass|integer $userorid
     * @param integer $maxdeadlines
     * @param stdClass|integer $courseorid
     * @param boolean $skipcmchecks Skip course module checks
     * @return stdClass
     */
    public static function upcoming_deadlines($userorid, $maxdeadlines = 500, $courseorid = 0, $skipcmchecks = false) {
        global $USER;
        $origuser = $USER;

        // The user is set here to:
        // * Assign categories to the calendar that the user has access to
        // * Assign groups to the calendar that the user belongs to
        // See: \calendar_information::set_sources.
        // It is safe to assume that b/c the user is enrolled in the courses specified,
        // all enrolled users would need to be able to access the corresponding categories, hence,
        // we can cache deadlines for courses without worrying about categories.
        //
        // However, we do need to worry about groups. Which is why we cache the group ids too.
        // See \theme_snap\activity::user_activity_events.
        $user = local::get_user($userorid);
        $USER = $user;

        $tz = new \DateTimeZone(\core_date::get_user_timezone($user));
        $today = new \DateTime('today', $tz);
        $todayts = $today->getTimestamp();
        $tomorrow = new \DateTime('tomorrow', $tz);
        $tomorrowts = $tomorrow->getTimestamp();

        $courses = enrol_get_users_courses($user->id, true);
        $courses = local::remove_hidden_courses($courses);
        if ($courseorid !== 0) {
            $course = local::get_course($courseorid);
            if ($course !== false &&
                (is_siteadmin() || isset($courses[$course->id]))) {
                $courses = [$course->id => $course];
            } else {
                // The user is not enrolled on this course, let's clean up the course lists.
                $courses = [];
            }
        }

        $eventsobj = self::user_activity_events(
            $courses, $todayts, $todayts + (YEARSECS / 2), 'deadlines', $maxdeadlines, $skipcmchecks);

        $events = $eventsobj->events;
        uasort($events, function($e1, $e2) {
            if ($e1->timestart === $e2->timestart) {
                return 0;
            }
            return ($e1->timestart < $e2->timestart) ? -1 : 1;
        });

        $counteventstoday = 0;

        $tmparr = [];
        foreach ($events as $event) {
            if ($event->timestart >= $todayts) {
                if ($event->eventtype != 'close' && $event->eventtype != 'due' && $event->eventtype != 'expectcompletionon') {
                    continue;
                }

                $tmparr[] = $event;

                if ($event->timestart < $tomorrowts) {
                    $counteventstoday++;
                }
            }
        }
        $events = $tmparr;

        // We have unlimited events for today but a maximum of five events for everything passed today.
        // If we have 10 events today then we will see 10 events, if we have 3 events for today then we will see
        // a maximum of 5 events including all the events that happen beyond today's date.
        $maxevents = $counteventstoday > $maxdeadlines ? $counteventstoday : $maxdeadlines;

        $eventsobj->events = array_slice($events, 0, $maxevents);

        $USER = $origuser;

        return $eventsobj;
    }

    /**
     * Returns the join and where statements required to validate the assignment submissions by groups on a course.
     * @param integer $courseid
     * @return array
     */
    private static function get_groups_sql($courseid) {
        global $USER;

        $sqlgroupsjoin = '';
        $sqlgroupswhere = '';
        $groupparams = array();

        $course = get_course($courseid);
        $groupmode = groups_get_course_groupmode($course);
        $context = \context_course::instance($courseid);

        if ($groupmode == SEPARATEGROUPS && !has_capability('moodle/site:accessallgroups', $context)) {
            $groupparams['userid'] = $USER->id;
            $groupparams['courseid2'] = $courseid;

            $sqlgroupsjoin = "
                    JOIN {groups_members} gm
                      ON gm.userid = sb.userid
                    JOIN {groups} g
                      ON gm.groupid = g.id";
            $sqlgroupswhere = "
                     AND gm.groupid
                      IN (SELECT g.id
                    FROM {groups} g
                    JOIN {groups_members} gm ON gm.groupid = g.id
                   WHERE g.courseid = :courseid2
                     AND gm.userid = :userid)";
        }
        return array($sqlgroupsjoin, $sqlgroupswhere, $groupparams);
    }

    /**
     * Generates a hash key from an 'id' indexed array.
     * @param array $idindexedarray
     * @return string
     */
    public static function get_id_indexed_array_cache_key(array $idindexedarray) {
        ksort($idindexedarray);
        return !empty($idindexedarray) ? sha1(implode(',', array_keys($idindexedarray))) : '';
    }

    /**
     * Get the cache key associated to the user's group ids in the specified courses.
     * @param mixed $user
     * @param \stdClass[] $courses Array of courses indexed by id.
     * @return string
     */
    public static function get_user_group_cache_key($user, array $courses) {
        $groupids = array_reduce(array_keys($courses), function($carry, $courseid) use ($user) {
            $groupings = groups_get_user_groups($courseid, $user->id);
            // Grouping 0 is all groups.
            return array_merge($carry, $groupings[0]);
        }, []);
        return self::get_id_indexed_array_cache_key(array_flip($groupids));
    }

    private static function start_deadline_query() {
        $locktype = 'theme_snap_activity_deadlines_cache';
        $resource = 'query_count';
        self::run_within_lock($locktype, $resource, function($lock) {
            global $CFG;
            $muc = \cache::make('theme_snap', 'activity_deadlines');
            $querycount = $muc->get('query_count');

            if ($querycount !== false) {
                $maxqueries = !empty($CFG->theme_snap_max_concurrent_deadline_queries) ?
                    $CFG->theme_snap_max_concurrent_deadline_queries : INF;
                if ($querycount >= $maxqueries) {
                    $lock->release();
                    throw new \moodle_exception(
                        'retryfeed', 'theme_snap', '', get_string('deadlines', 'theme_snap'));
                }
            } else {
                $querycount = 0;
            }
            $muc->set('query_count', $querycount + 1);
        });
    }

    private static function end_deadline_query() {
        $locktype = 'theme_snap_activity_deadlines_cache';
        $resource = 'query_count';
        self::run_within_lock($locktype, $resource, function($lock) {
            $muc = \cache::make('theme_snap', 'activity_deadlines');
            $querycount = $muc->get('query_count');

            if ($querycount !== false) {
                if ($querycount > 0) {
                    $muc->set('query_count', $querycount - 1);
                } else {
                    // We won't fail if the query count is 0 or negative. Just reset it.
                    // There could be locking issues.
                    $muc->set('query_count', 0);
                }
            }
        });
    }

    private static function run_within_lock($locktype, $resource, $callable) {
        $timeout = 5;
        $lockfactory = \core\lock\lock_config::get_lock_factory($locktype);
        if ($lock = $lockfactory->get_lock($resource, $timeout)) {
            $callable($lock);
            $lock->release();
        } else {
            // We did not get access to the resource in time, give up.
            throw new \moodle_exception(
                'retryfeed', 'theme_snap', '', get_string('deadlines', 'theme_snap'));
        }
    }

    public static function reset_deadline_query_count() {
        $muc = \cache::make('theme_snap', 'activity_deadlines');
        $muc->set('query_count', 0);
    }
}
