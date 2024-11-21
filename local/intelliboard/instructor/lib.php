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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2017 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

use local_intelliboard\repositories\user_settings;
use local_intelliboard\repositories\category_repositoriy;

defined('MOODLE_INTERNAL') || die();

function intelliboard_instructor_access()
{
    global $USER;

    if(!get_config('local_intelliboard', 'n10')){
        throw new moodle_exception('invalidaccessparameter', 'error');
    }
    $access = check_intelliboard_instructor_access();
    if (!$access) {
        throw new moodle_exception('invalidaccess', 'error');
    }
}
function check_intelliboard_instructor_access()
{
    global $USER;

    $access = false;
    $instructor_roles = get_config('local_intelliboard', 'filter10');
    if (!empty($instructor_roles)) {
        $roles = explode(',', $instructor_roles);
        if (!empty($roles)) {
            foreach ($roles as $role) {
                if ($role and user_has_role_assignment($USER->id, $role)){
                    $access = true;
                    break;
                }
            }
        }
    }

    return $access;
}
function intelliboard_course_learners_total($courseid)
{
    global $DB, $USER;

    $params = array('courseid' => $courseid);
    list($sql_roles, $sql_params) = $DB->get_in_or_equal(explode(',', get_config('local_intelliboard', 'filter11')), SQL_PARAMS_NAMED, 'r');
    $params = array_merge($params,$sql_params);
    $grade_avg = intelliboard_grade_sql(true);
    $join_group_sql = intelliboard_group_aggregation_sql('ra.userid', $USER->id, 'e.instanceid');
    $join_group_sql .= intelliboard_instructor_hide_suspended_enrollments_joinsql('e.instanceid', 'ra.userid');
    $join_group_sql2 = intelliboard_group_aggregation_sql('g.userid', $USER->id, 'gi.courseid');
    $join_group_sql2 .= intelliboard_instructor_hide_suspended_enrollments_joinsql('gi.courseid', 'g.userid');
    $userjoin = '';

    $sql44 = intelliboard_instructor_getcourses('gi.courseid', false, 'g.userid', false, false);
    $sql55 = intelliboard_instructor_getcourses('c.id', false, 'ra.userid', false, false);

    if (get_filter_usersql("u.")) {
        $userjoin = 'JOIN {user} u ON u.id = ra.userid';
        $sql55 .= get_filter_usersql("u.");
    }

    $cache = cache::make('local_intelliboard', 'instructor_course_data');

    if ($cache->has("total_{$courseid}")) {
        $data = json_decode($cache->get("total_{$courseid}"));
    } else {
        $data = $DB->get_record_sql(
            "SELECT c.id,c.fullname, c.startdate, c.enablecompletion,
                    (SELECT name FROM {course_categories} WHERE id = c.category) AS category,
                    (SELECT COUNT(id) FROM {course_sections} WHERE visible = 1 AND course = c.id) AS sections,
                    COUNT(DISTINCT ra.userid) as learners,
                    COUNT(DISTINCT cc.id) as learners_completed,
                    (SELECT {$grade_avg}
                       FROM {grade_items} gi
                       JOIN {grade_grades} g ON g.itemid = gi.id AND g.finalgrade IS NOT NULL
                            {$join_group_sql2}
                      WHERE gi.itemtype = 'course' AND gi.courseid = c.id {$sql44}
                    ) AS grade,
                    SUM(l.timespend) as timespend,
                    SUM(l.visits) as visits
               FROM {role_assignments} ra
               JOIN {context} e ON e.id = ra.contextid AND e.contextlevel = 50
                    {$userjoin}
          LEFT JOIN {course} c ON c.id = e.instanceid
          LEFT JOIN {course_completions} cc ON cc.course = c.id AND cc.userid = ra.userid AND cc.timecompleted > 0
          LEFT JOIN (SELECT t.userid,t.courseid, SUM(t.timespend) as timespend, SUM(t.visits) as visits
                       FROM {local_intelliboard_tracking} t
                   GROUP BY t.courseid, t.userid
                    ) l ON l.courseid = c.id AND l.userid = ra.userid
                    {$join_group_sql}
              WHERE ra.roleid {$sql_roles} AND e.instanceid = :courseid {$sql55}
           GROUP BY c.id
              LIMIT 1",
            $params
        );

        $cache->set("total_{$courseid}", json_encode($data));
    }

    return $data;
}

function intelliboard_learner_data($userid, $courseid)
{
    global $DB;

    $params = array(
        'c1' => $courseid,
        'c2' => $courseid,
        'c3' => $courseid,
        'u1' => $userid,
        'u2' => $userid,
        'u3' => $userid
    );
    $grade_single = intelliboard_grade_sql();
    $completion = intelliboard_compl_sql("cmc.");

    return  $DB->get_record_sql("SELECT
        DISTINCT(u.id) AS userid,
        u.email,
        ul.timeaccess,
        ue.timemodified as enrolled,
        CONCAT(u.firstname, ' ', u.lastname) AS learner,
        e.courseid,
        c.fullname AS course,
        cc.timecompleted,
        $grade_single AS grade, cmc.progress,
        l.timespend, l.visits
     FROM
        {user_enrolments} ue
        LEFT JOIN {user} u ON u.id = ue.userid
        LEFT JOIN {enrol} e ON e.id = ue.enrolid AND e.courseid = :c3
        LEFT JOIN {course} c ON c.id = e.courseid
        LEFT JOIN {grade_items} gi ON gi.itemtype = 'course' AND gi.courseid = c.id
        LEFT JOIN {grade_grades} g ON g.userid = ue.userid AND g.itemid = gi.id AND g.finalgrade IS NOT NULL
        LEFT JOIN {course_completions} cc ON cc.course = c.id AND cc.userid = ue.userid
        LEFT JOIN {user_lastaccess} ul ON ul.courseid = c.id AND ul.userid = u.id
        LEFT JOIN (SELECT courseid, userid, SUM(timespend) as timespend, SUM(visits) as visits FROM
            {local_intelliboard_tracking} WHERE courseid = :c1 AND userid = :u1 GROUP BY courseid, userid) l ON l.courseid = c.id AND l.userid = ue.userid
        LEFT JOIN (SELECT cmc.userid, COUNT(DISTINCT cmc.id) as progress FROM {course_modules_completion} cmc, {course_modules} cm WHERE cm.visible = 1 AND cmc.coursemoduleid = cm.id $completion AND cm.completion > 0 AND cm.course = :c2 AND cmc.userid = :u2 GROUP BY cmc.userid) cmc ON cmc.userid = u.id
    WHERE ue.userid = :u3 LIMIT 1", $params);
}
function intelliboard_activities_data($courseid)
{
    global $DB, $USER;
    $params = array(
        'courseid' => $courseid,
        'courseid2' => $courseid,
        'courseid3' => $courseid,
        'courseid4' => $courseid
    );
    list($sql1, $sql_params) = $DB->get_in_or_equal(explode(',', get_config('local_intelliboard', 'filter11')), SQL_PARAMS_NAMED, 'r');
    $params = array_merge($params,$sql_params);
    $grade_avg = intelliboard_grade_sql(true);
    $completion = intelliboard_compl_sql("cmc.");
    $join_group_sql = intelliboard_group_aggregation_sql('cmc.userid', $USER->id, 'cm.course');
    $join_group_sql2 = intelliboard_group_aggregation_sql('g.userid', $USER->id, 'gi.courseid');
    $join_group_sql3 = intelliboard_group_aggregation_sql('ra.userid', $USER->id, 'ctx.instanceid');
    return $DB->get_record_sql("
        SELECT
            c.id AS courseid,
            c.fullname,
            c.startdate,
            c.enablecompletion,
            l.visits,
            l.timespend,
            (SELECT name FROM {course_categories} WHERE id = c.category) AS category,
            (SELECT COUNT(id) FROM {course_sections} WHERE visible = 1 AND course = c.id) AS sections,
            (SELECT COUNT(id) FROM {course_modules} WHERE visible = 1 AND course = c.id) AS modules,
            (SELECT COUNT(cmc.id)
              FROM {course_modules} cm
               JOIN {course_modules_completion} cmc ON cmc.coursemoduleid = cm.id
               $join_group_sql
              WHERE cm.visible = 1 AND cm.course = c.id $completion ) AS completed,
            (SELECT $grade_avg
              FROM {grade_items} gi
                JOIN {grade_grades} g ON g.itemid = gi.id AND g.finalgrade IS NOT NULL
                $join_group_sql2
              WHERE gi.itemtype = 'course' AND gi.courseid = c.id) AS grade
        FROM {course} c
            LEFT JOIN (
                SELECT l.courseid, SUM(l.visits) AS visits, SUM(l.timespend) AS timespend
                FROM {local_intelliboard_tracking} l
                WHERE l.courseid = :courseid2 AND l.userid IN (SELECT DISTINCT ra.userid
                                                                FROM {context} ctx
                                                                  JOIN {role_assignments} ra ON ctx.id = ra.contextid
                                                                  $join_group_sql3
                                                                WHERE ctx.instanceid = :courseid4 AND ctx.contextlevel = 50 AND ra.roleid $sql1)
                GROUP BY l.courseid) l ON l.courseid=c.id
        WHERE c.id = :courseid", $params);
}
function intelliboard_activity_data($cmid, $courseid)
{
    global $DB, $USER;
    $params = array('cmid' => $cmid);
    $cm = $DB->get_record_sql("SELECT cm.id, cm.instance, m.name FROM {course_modules} cm, {modules} m WHERE cm.id = :cmid AND m.id = cm.module", $params);
    list($sql1, $sql_params) = $DB->get_in_or_equal(explode(',', get_config('local_intelliboard', 'filter11')), SQL_PARAMS_NAMED, 'r');
    $params = array_merge($params,$sql_params);
    $params['instance'] = $cm->instance;
    $params['instance2'] = $cm->instance;
    $params['module'] = $cm->name;
    $params['courseid'] = $courseid;
    $params['cmid2'] = $cmid;
    $params['cmid3'] = $cmid;
    $grade_avg = intelliboard_grade_sql(true);
    $completion = intelliboard_compl_sql("cmc.", false);
    $join_group_sql = intelliboard_group_aggregation_sql('cmc.userid', $USER->id, 'cm.course');
    $join_group_sql2 = intelliboard_group_aggregation_sql('g.userid', $USER->id, 'gi.courseid');
    $join_group_sql3 = intelliboard_group_aggregation_sql('ra.userid', $USER->id, 'ctx.instanceid');
    return $DB->get_record_sql("
        SELECT
            cm.id, c.id AS courseid,
            c.fullname as course,
            i.name,
            ca.name AS category,
            cs.section,
            m.name as module, l.visits, l.timespend,
            (SELECT COUNT(DISTINCT cmc.id)
              FROM {course_modules_completion} cmc
                JOIN {course_modules} cm ON cm.id=coursemoduleid
                $join_group_sql
              WHERE $completion AND cmc.coursemoduleid=:cmid) AS completed,
            (SELECT $grade_avg
              FROM {grade_items} gi
                JOIN {grade_grades} g ON g.itemid = gi.id AND g.finalgrade IS NOT NULL
                $join_group_sql2
              WHERE gi.itemtype = 'mod' AND gi.itemmodule = :module AND gi.iteminstance = :instance2) AS grade
        FROM {course_modules} cm
            LEFT JOIN {modules} m ON m.id = cm.module
            LEFT JOIN {course} c ON c.id = cm.course
            LEFT JOIN {course_sections} cs ON cs.id = cm.section AND cs.course = c.id
            LEFT JOIN {course_categories} ca ON ca.id = c.category
            LEFT JOIN {".$cm->name."} i ON i.id = :instance
            LEFT JOIN (SELECT l.param, SUM(l.visits) AS visits, SUM(l.timespend) AS timespend
                        FROM {local_intelliboard_tracking} l
                        WHERE l.page='module' AND l.param = :cmid2 AND l.userid IN (SELECT DISTINCT ra.userid
                                                                                      FROM {context} ctx
                                                                                        JOIN {role_assignments} ra ON ctx.id = ra.contextid
                                                                                        $join_group_sql3
                                                                                      WHERE ctx.instanceid = :courseid AND ctx.contextlevel = 50 AND ra.roleid $sql1)
                        GROUP BY l.param) l ON l.param=cm.id
        WHERE cm.id = :cmid3", $params);
}


function intelliboard_instructor_correlations($page, $length, $timestart, $timefinish)
{
    global $DB, $USER;

    $params = array(
        'timestart' => $timestart,
        'timefinish' => $timefinish,
    );

    $teacher_roles = get_config('local_intelliboard', 'filter10');
    $learner_roles = get_config('local_intelliboard', 'filter11');
    $grade_avg = intelliboard_grade_sql(true);
    $grade_avg_percent = intelliboard_grade_sql(true,null, 'g.',0, 'gi.',true);
    $join_sql = intelliboard_group_aggregation_sql('g.userid', $USER->id, 'c.id');
    $join_sql .= intelliboard_instructor_hide_suspended_enrollments_joinsql('c.id', 'g.userid');
    $sql = intelliboard_instructor_getcourses('c.id', false, 'g.userid');

    $items = $DB->get_records_sql("
          SELECT
                c.id,
                c.fullname,
                $grade_avg_percent AS grade,
                $grade_avg AS grade_real,
                SUM(l.duration) as duration, '0' AS duration_calc
            FROM {course} c
       LEFT JOIN {grade_items} gi ON gi.courseid = c.id AND gi.itemtype = 'course'
       LEFT JOIN {grade_grades} g ON g.itemid = gi.id AND g.finalgrade IS NOT NULL
       LEFT JOIN (
           SELECT lit.userid, lit.courseid, SUM(lil.timespend) AS duration
             FROM {local_intelliboard_tracking} lit  
             JOIN {local_intelliboard_logs} lil ON lil.trackid = lit.id AND lit.courseid > 0 
                    AND lil.timepoint BETWEEN :timestart AND :timefinish  
         GROUP BY lit.userid, lit.courseid
       ) l ON l.userid = g.userid AND l.courseid = c.id
       $join_sql
           WHERE c.id > 0 $sql 
        GROUP BY c.id", $params, $page, $length);

    $d = 0;
    foreach($items as $c){
        $d = ($c->duration > $d)?$c->duration:$d;
    }
    if($d){
        foreach($items as $c){
            $c->duration_calc =  (intval($c->duration)/$d)*100;
        }
    }
    $data = array();
    foreach($items as $item){
        $l = intval($item->duration_calc);
        $d = seconds_to_time(intval($item->duration));

        $tooltip = "<div class=\"chart-tooltip\">";
        $tooltip .= "<div class=\"chart-tooltip-header\">". strip_tags($item->fullname) ."</div>";
        $tooltip .= "<div class=\"chart-tooltip-body clearfix\">";
        $tooltip .= "<div class=\"chart-tooltip-left\">".get_string('grade','local_intelliboard').": <span>". $item->grade_real."</span></div>";
        $tooltip .= "<div class=\"chart-tooltip-right\">".get_string('time_spent','local_intelliboard').": <span>". $d."</span></div>";
        $tooltip .= "</div>";
        $tooltip .= "</div>";
        $data[] = array($l, round($item->grade ?? 0, 2), $tooltip);
    }

    return $data;
}
function intelliboard_instructor_modules($timestart, $timefinish)
{
    global $DB, $USER;

    $params = array(
        'timestart' => $timestart,
        'timefinish' => $timefinish,
    );

    $learner_roles = get_config('local_intelliboard', 'filter11');
    $join_sql1 = intelliboard_group_aggregation_sql('ra.userid', $USER->id, 'c.id');
    $join_sql1 .= intelliboard_instructor_hide_suspended_enrollments_joinsql('ctx.instanceid', 'ra.userid');
    $sql = intelliboard_instructor_getcourses('c.id', false, 'ra.userid');
    list($sql2, $params) = intelliboard_filter_in_sql($learner_roles, "ra.roleid", $params);

    $items = $DB->get_records_sql("
            SELECT
                m.id,
                m.name,
                SUM(lil.visits) as visits,
                SUM(lil.timespend) as timespend
              FROM {role_assignments} ra
         LEFT JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = 50
         LEFT JOIN {course} c ON c.id = ctx.instanceid
         LEFT JOIN {course_modules} cm ON cm.course = c.id
         LEFT JOIN {modules} m ON m.id = cm.module
         LEFT JOIN {local_intelliboard_tracking} lit ON lit.page = 'module' AND lit.userid = ra.userid AND lit.param = cm.id
         LEFT JOIN {local_intelliboard_logs} lil ON lil.trackid = lit.id AND lil.timepoint BETWEEN :timestart AND :timefinish
         $join_sql1
             WHERE c.id > 0 AND m.name IS NOT NULL $sql $sql2 
          GROUP BY m.id", $params);

    $data = array(array(get_string('in6', 'local_intelliboard'), get_string('time_spent', 'local_intelliboard')));
    foreach($items as $item){
        $inner = new stdClass();
        $inner->v = (int)$item->timespend;
        $inner->f = seconds_to_time(intval($item->timespend));
        $data[] = array(get_string('modulename', $item->name), $inner);
    }
    return $data;
}
function get_filter_usersql($prefix = '')
{
    $filter = (get_config('local_intelliboard', 'filter1')) ? "" : " AND {$prefix}deleted = 0";
    $filter .= (get_config('local_intelliboard', 'filter2')) ? "" : " AND {$prefix}suspended = 0";
    $filter .= (get_config('local_intelliboard', 'guest')) ? "" : " AND {$prefix}username <> 'guest'";
    return $filter;
}

function intelliboard_instructor_stats()
{
    global $DB, $USER;

    $learner_roles = get_config('local_intelliboard', 'filter11');
    $grade_avg = intelliboard_grade_sql(true);
    $join_sql1 = intelliboard_group_aggregation_sql('ra.userid', $USER->id, 'ctx.instanceid');
    $join_sql1 .= intelliboard_instructor_hide_suspended_enrollments_joinsql('c.id', 'u.id');
    $sql = intelliboard_instructor_getcourses('c.id', false, 'ra.userid');
    list($sql2, $params) = intelliboard_filter_in_sql($learner_roles, "ra.roleid", []);
    $sqluserfilter = get_filter_usersql("u.");

    return $DB->get_record_sql(
        "SELECT COUNT(DISTINCT c.id) as courses,
                COUNT(DISTINCT ra.userid) as enrolled,
                COUNT(DISTINCT cc.userid) as completed,
                COUNT(DISTINCT g.id) as grades,
                {$grade_avg} AS grade
           FROM {course} c
           JOIN {context} ctx ON ctx.contextlevel = 50 AND c.id = ctx.instanceid
      LEFT JOIN {role_assignments} ra ON ctx.id = ra.contextid {$sql2}
      LEFT JOIN {user} u ON u.id = ra.userid {$sqluserfilter}
      LEFT JOIN {course_completions} cc ON cc.course = c.id AND cc.timecompleted > 0 AND cc.userid = ra.userid
      LEFT JOIN {grade_items} gi ON gi.itemtype = 'course' AND gi.courseid = c.id
      LEFT JOIN {grade_grades} g ON g.userid = ra.userid AND g.itemid = gi.id AND g.finalgrade IS NOT NULL
                {$join_sql1}
          WHERE c.id > 0 {$sql}",
        $params
    );
}

function intelliboard_instructor_courses($view, $page, $length, $courseid = 0, $daterange = '')
{
    global $DB, $USER;

    $learner_roles = get_config('local_intelliboard', 'filter11');
    $grade_avg = intelliboard_grade_sql(true);
    $grade_avg_percent = intelliboard_grade_sql(true,null, 'g.',0,'gi.',true);
    $completion = intelliboard_compl_sql("cmc.");

    list($sql2, $params) = intelliboard_filter_in_sql($learner_roles, "ra.roleid", []);

    if($view == 'grades'){
        $sql = intelliboard_instructor_getcourses('c.id', false, 'g.userid');

        $timerange_sql = '';
        if (!empty($daterange)) {
            $range = preg_split("/ (.)+ /", $daterange);

            if($range[0] && $range[1]){
                $timerange_sql .= ' AND g.timemodified BETWEEN :timestart AND :timefinish';
                $params['timestart'] = intelliboard_str_to_timestamp(trim($range[0]));
                $params['timefinish'] = intelliboard_str_to_timestamp(trim($range[1]), true);
            }
        }
        $join_sql = intelliboard_group_aggregation_sql('g.userid', $USER->id, 'c.id');
        $join_sql .= intelliboard_instructor_hide_suspended_enrollments_joinsql('c.id', 'g.userid');
        $courses = $DB->get_records_sql(
            "SELECT c.id,
                    c.fullname,
                    {$grade_avg} AS data1,
                    {$grade_avg_percent} AS data1_percent,
                    (SELECT cc.gradepass FROM {course_completion_criteria} cc WHERE cc.course = c.id AND cc.criteriatype = 6 ) as data2
               FROM {course} c
          LEFT JOIN {grade_items} gi ON gi.courseid = c.id AND gi.itemtype = 'course'
          LEFT JOIN {grade_grades} g ON g.itemid = gi.id AND g.finalgrade IS NOT NULL {$timerange_sql}
                    {$join_sql}
              WHERE c.id > 0 {$sql}
           GROUP BY c.id, c.fullname",
            $params, $page, $length
        );
    }elseif($view == 'activities'){
        $sql = intelliboard_instructor_getcourses('ctx.instanceid', false, 'ra.userid');
        $sql0 = intelliboard_instructor_getcourses('ctx.instanceid', false);

        if (!empty($daterange)) {
            $range = preg_split("/ (.)+ /", $daterange);

            if($range[0] && $range[1]){
                $completion .= ' AND cmc.timemodified BETWEEN :timestart AND :timefinish';
                $params['timestart'] = intelliboard_str_to_timestamp(trim($range[0]));
                $params['timefinish'] = intelliboard_str_to_timestamp(trim($range[1]), true);
            }
        }
        $join_sql1 = intelliboard_group_aggregation_sql('ra.userid', $USER->id, 'ctx.instanceid');
        $join_sql1 .= intelliboard_instructor_hide_suspended_enrollments_joinsql('ctx.instanceid', 'ra.userid');
        $join_sql2 = intelliboard_group_aggregation_sql('cmc.userid', $USER->id, 'c.id');

        $courses = $DB->get_records_sql(
            "SELECT c.id,
                    c.fullname,
                    (SELECT COUNT(DISTINCT ra.userid) as learners
                       FROM {role_assignments} ra
                       JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = 50
                            {$join_sql1}
                      WHERE ctx.instanceid = c.id {$sql} {$sql2}
                    ) AS learners,
                    COUNT(DISTINCT cmc.id) as data1,
                    (SELECT COUNT(cm.id) FROM {course_modules} cm WHERE cm.course = c.id AND cm.visible = 1 AND cm.completion > 0) AS data2
               FROM {course} c
          LEFT JOIN {course_modules} cm ON cm.course = c.id AND cm.visible = 1 AND cm.completion > 0
          LEFT JOIN {course_modules_completion} cmc ON cmc.coursemoduleid = cm.id {$completion}
          LEFT JOIN {context} ctx ON ctx.instanceid = c.id AND ctx.contextlevel = 50
                    {$join_sql2}
              WHERE c.id > 0 {$sql0}
           GROUP BY c.id",
            $params, $page, $length
        );

        foreach($courses as $course){
            if ($course->learners * $course->data2 == 0) {
                $course->data1 = 0;
            } else {
                $course->data1 = ($course->data2) ? ($course->data1 / ($course->learners * $course->data2)) * 100 : 0;
            }
        }
    }elseif($view == 'course_overview'){
        return array();
    }else{
        $sql = intelliboard_instructor_getcourses('c.id', false, 'ra.userid');

        if (!empty($daterange)) {
            $range = preg_split("/ (.)+ /", $daterange);

            if($range[0] && $range[1]){
                $sql2 .= ' AND (cc.timecompleted BETWEEN :timestart1 AND :timefinish1 OR ra.timemodified BETWEEN :timestart2 AND :timefinish2)';
                $params['timestart1'] = intelliboard_str_to_timestamp(trim($range[0]));
                $params['timefinish1'] = intelliboard_str_to_timestamp(trim($range[1]), true);
                $params['timestart2'] = intelliboard_str_to_timestamp(trim($range[0]));
                $params['timefinish2'] = intelliboard_str_to_timestamp(trim($range[1]), true);
            }
        }
        $join_sql = intelliboard_group_aggregation_sql('ra.userid', $USER->id, 'c.id');

        $courses = $DB->get_records_sql(
            "SELECT c.id,
                    c.fullname,
                    COUNT(DISTINCT ra.userid) as data1,
                    COUNT(distinct cc.userid) as data2
               FROM {role_assignments} ra
          LEFT JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = 50
          LEFT JOIN {course} c ON c.id = ctx.instanceid
          LEFT JOIN {course_completions} cc ON cc.course = c.id AND cc.timecompleted > 0 AND cc.userid = ra.userid
                    {$join_sql}
              WHERE c.id > 0 {$sql} {$sql2}
           GROUP BY c.id",
            $params, $page, $length
        );

        if(empty($courses)){
            $row = new stdClass();
            $row->id = 0;
            $row->fullname = get_string('no_data','local_intelliboard');
            $row->data1 = 0;
            $row->data2 = 0;

            $courses = array($row);
        }
    }
    return $courses;
}

function intelliboard_instructor_course_access($course)
{
  $access = get_config('local_intelliboard', 'instructor_mode_access');

  if (can_access_course($course)) {
    if ($access) {
      if (!has_capability('moodle/course:update', context_course::instance($course->id))) {
        return false;
      }
    }
    return true;
  }
  return false;
}

function intelliboard_instructor_getcourses(
    $column = 'c.id', $list = false, $col_user = '', $onlyusers = false, $filtercoursesbysettings = true
) {
    global $DB, $USER, $CFG;

    $mode = get_config('local_intelliboard', 'instructor_mode');
    $visibility = get_config('local_intelliboard', 'instructor_course_visibility');
    $roles = get_config('local_intelliboard', 'filter10');
    $instructor_custom_groups = get_config('local_intelliboard', 'instructor_custom_groups');
    $coursefilter = [" > 0", []];
    $coursecontainer_filter = "";
    if (get_config('local_intelliboard', 'coursecontainer_available') && get_config('local_intelliboard', 'coursecontainer_filter')) {
        $coursecontainer_filter =  " AND c.containertype = 'container_course'";
    }

    if($filtercoursesbysettings) {
        $usrcourses = array_keys(user_settings::getInstructorDashboardCourses($USER->id));

        if($usrcourses) {
            $coursefilter = $DB->get_in_or_equal($usrcourses, SQL_PARAMS_NAMED, 'usrcrs');
        }
    }

    if ($onlyusers and !$instructor_custom_groups) {
        return 0;
    }
    if ($instructor_custom_groups) {
        $sql_data = get_filter_usersql("u.");
        $data = $DB->get_record_sql(
            "SELECT d.data AS codea
               FROM {user_info_field} f, {user_info_data} d
              WHERE d.fieldid = f.id AND d.userid = ? and f.shortname= 'codea'",
            [$USER->id]
        );
        $result = (isset($data->codea)) ? $DB->get_records_sql(
            "SELECT DISTINCT d.userid
               FROM {user_info_field} f, {user_info_data} d, {user} u
              WHERE d.fieldid = f.id AND d.data = ? AND u.id = d.userid $sql_data AND
                    f.shortname IN ('codsm', 'coddm', 'codam')",
            [$data->codea]
        ) : false;

        $debug = get_config('local_intelliboard', 'debug');
      	$debugmode = optional_param('debug', '', PARAM_RAW);
        if ($debug and $debugmode) {
          var_dump([$data, $result]);
          die();
        }

        if ($result) {
            $users = [];

            foreach ($result as $row) {
              $users[] = $row->userid;
            }

            $result = new stdClass();
            $result->users = implode(',', $users);
        } else {
            $result = new stdClass();
            $result->users = 0;
        }

        if ($onlyusers) {
            return ($result->users) ? count(explode(",", $result->users)) : 0;
        }
        if ($result->users) {
            list($sql, $params) = intelliboard_filter_in_sql($result->users, "ra.userid", []);
            $courses = $DB->get_records_sql(
                "SELECT c.*
                   FROM {role_assignments} ra, {context} ctx, {course} c
                  WHERE ctx.id = ra.contextid AND ctx.contextlevel = 50 AND c.id=ctx.instanceid $sql AND
                        c.id {$coursefilter[0]} {$coursecontainer_filter}
               GROUP BY c.id",
                array_merge($params, $coursefilter[1])
            );
            $users = $result->users;
        } else {
            $courses = [];
            $users = '0,0';
        }
    } elseif ($mode) {
        $courses = $DB->get_records_sql(
            "SELECT * FROM {course} c WHERE c.category > 0 AND c.id {$coursefilter[0]} {$coursecontainer_filter}",
            $coursefilter[1]
        );
    } else {
        $params = ['userid' => $USER->id];
        list($sql, $params) = intelliboard_filter_in_sql($roles, "ra.roleid", $params);
        $courses = $DB->get_records_sql(
            "SELECT DISTINCT c.*
               FROM {role_assignments} ra, {context} ctx, {course} c
              WHERE ctx.id = ra.contextid AND ctx.contextlevel = 50 AND
                    c.id=ctx.instanceid AND ra.userid = :userid $sql AND
                    c.id {$coursefilter[0]} {$coursecontainer_filter}",
            array_merge($params, $coursefilter[1])
        );
    }

    // category enrollments
    $categories = category_repositoriy::get_categories_with_subcategories(
        category_repositoriy::catgories_enrollments($USER->id, $roles)
    );

    if($categories) {
        list($categoriesfilter, $params) = intelliboard_filter_in_sql(array_keys($categories), "c.category", []);

        $catcourses = $DB->get_records_sql(
            "SELECT c.*
               FROM {course} c
              WHERE c.id > 0 {$categoriesfilter} {$coursecontainer_filter}",
            $params
        );

        foreach ($catcourses as $course) {
            if(!isset($courses[$course->id])) {
                $courses[$course->id] = $course;
            }
        }
    }

    foreach ($courses as $key=>$course) {
        if (!intelliboard_instructor_course_access($course)) {
            unset($courses[$key]);
        }
        if (!$visibility and !$course->visible) {
          unset($courses[$key]);
        }
    }
    if ($list) {
        return (!$courses) ? [] : $courses;
    } else {
        $courses = (!$courses) ? '0,0' : implode(",", array_keys($courses));
        return ($col_user and $instructor_custom_groups) ? " AND ($column IN ($courses) and $col_user IN ($users))" : " AND $column IN ($courses)";
    }
}

function intelliboard_get_widget($id, $data, $params) {
    global $DB;

    $error = false;
    $widget_name = get_string('widget_name'.$id, 'local_intelliboard');
    $no_data = get_string('no_data', 'local_intelliboard');

    if($id == 27){
        if (isset($data->data) and $data->data) {
            $json_data = array();
            $data = explode(',', $data->data);
            foreach($data as $key => $item){
                if($item) {
                    $item = explode('.',  $item);
                    $item[0] = strtotime(date('m/d/Y 00:00:00', $item[0]));
                    $d = date("j", $item[0]);
                    $m = date("n", $item[0]) - 1;
                    $y = date("Y", $item[0]);
                    $l = $item[1];
                    $json_data[] = "[new Date($y, $m, $d), $l]";
                }
            }
            if (count($json_data) < 2) {
                    $d = date("j", strtotime('+1 day'));
                    $m = date("n") - 1;
                    $y = date("Y");
                    $json_data[] = "[new Date($y, $m, $d), 0]";
            }
        }else{
            $error = true;
        }
        $html = getWidgetFilters($id, array('daterange', 'cohort'), $params);
        if (!$error){
            $html .= lineWidgetSetup($json_data, array("date"=>get_string('time', 'local_intelliboard'),"number"=>get_string('users', 'local_intelliboard')), array('id'=>$id, 'format'=>'MMMM'), 300);
        }else{
            $html .= '<div class="alert alert-warning">'.$no_data.'</div>';
        }
        return htmlWidgetSetup($html, array('style'=>'grid', 'name'=> $widget_name));
    }elseif($id == 28){
        if (isset($data->data) and $data->data) {
            $json_data = array();
            $data = explode(',', $data->data);
            foreach($data as $key => $item){
                $item = explode('.',  $item);
                $item[0] = strtotime(date('m/d/Y 00:00:00', $item[0]));
                $d = date("j", $item[0]);
                $m = date("n", $item[0]) - 1;
                $y = date("Y", $item[0]);
                $l = $item[1];
                $json_data[] = "[new Date($y, $m, $d), $l]";
            }
            if (count($json_data) < 2) {
                    $d = date("j", strtotime('+1 day'));
                    $m = date("n") - 1;
                    $y = date("Y");
                    $json_data[] = "[new Date($y, $m, $d), 0]";
            }
        }else{
            $error = true;
        }

       $html = getWidgetFilters($id, array('daterange', 'profilefields'), $params);
        if (!$error){
            $html .= lineWidgetSetup($json_data, array("date"=>get_string('time', 'local_intelliboard'),"number"=>get_string('users', 'local_intelliboard')), array( 'id'=>$id, 'format'=>'MMMM'), 300);
        }else{
            $html .= '<div class="alert alert-warning">'.$no_data.'</div>';
        }
        return htmlWidgetSetup($html, array('style'=>'grid', 'name'=>$widget_name));
    }elseif($id == 29){
        $json_data = array();
        if (isset($data->data) and $data->data) {
            $data = explode(',', $data->data);
            foreach($data as $key => $item){
                $item = explode('.',  $item);
                $time = date('M', $item[0]);
                $users = (int)$item[1];
                $json_data[] = "['".$time."', $users]";
            }
        }else{
            $error = true;
        }
        $html = getWidgetFilters($id, array('daterange', 'profilefields'), $params);
        if (!$error){
            $html .= barWidgetSetup($json_data, array( 'id'=>$id, 'labels'=>'["'.get_string('time', 'local_intelliboard').'", "'.get_string('users', 'local_intelliboard').'"]', 'options'=>''), 300);
        }else{
            $html .= '<div class="alert alert-warning">'.$no_data.'</div>';
        }
        return htmlWidgetSetup($html, array('style'=>'grid', 'name'=>$widget_name));
    }elseif($id == 30){
        $json_data = array();
        $month4 = date("F", strtotime("-4 month"));
        $month3 = date("F", strtotime("-2 month"));
        $month2 = date("F", strtotime("-1 month"));
        $month1 = date("F");

        if($data){
            $courses = array();
            foreach($data as $item){
                $courses[str_replace('"',"'", $item->fullname)][$item->timepointval] = $item;
            }
            foreach ($courses as $key => $value) {
                $month_val1 = 0;
                $month_val2 = 0;
                $month_val3 = 0;
                $month_val4 = 0;

                foreach ($value as $time => $course) {
                    if ($month1 == date("F", $time)) {
                        $month_val1 = $course->enrolled;
                    }if ($month2 == date("F", $time)) {
                        $month_val2 = $course->enrolled;
                    }if ($month3 == date("F", $time)) {
                        $month_val3 = $course->enrolled;
                    }if ($month4 == date("F", $time)) {
                        $month_val4 = $course->enrolled;
                    }
                }
                $json_data[] = "['".$key."', $month_val1, $month_val2, $month_val3, $month_val4]";
            }
        }else{
            $error = true;
        }
        $html = getWidgetFilters($id, array('enrols', 'system'), $params);
        if (!$error){
            $html .= barHWidgetSetup($json_data, array( 'id'=>$id, 'labels'=>'["'.get_string('course').'","'.$month1.'","'.$month2.'","'.$month3.'","'.$month4.'"]', 'options'=>''), 300);
        }else{
            $html .= '<div class="alert alert-warning">'.$no_data.'</div>';
        }
        return htmlWidgetSetup($html, array('style'=>'grid', 'name'=>$widget_name));
    }elseif($id == 31){
        $json_data = array();
        if($data){
            $times = array();
            foreach($data as $item){
                $times[$item->timepointval][] = $item;
            }
            ksort($times);

            $role11 = 0;
            $role22 = 0;
            list($param1, $param2) = (isset($params->custom2) and !empty($params->custom2)) ? explode(",", $params->custom2) : array();
            $result = $DB->get_records_sql("SELECT id, data FROM {user_info_data} WHERE id IN (?, ?)",
                array(
                    intval($param1),
                    intval($param2)
                )
            );
            foreach ($result as $item){
                if ($param1 == $item->id) {
                    $role11 = $item->data;
                }
                if ($param2 == $item->id) {
                    $role22 = $item->data;
                }
            }

            foreach($times as $key => $val){
                $role1 = 0;
                $role2 = 0;
                foreach ($val as $time) {
                    if ($time->data == $role11) {
                        $role1 = $time->users;
                    }
                    if ($time->data == $role22) {
                        $role2 = $time->users;
                    }
                }
                $json_data[] = "['".date('M/d', $key)."', $role1, $role2]";
            }
        }else{
            $error = true;
        }
        $html = getWidgetFilters($id, array('profilefields'), $params);
        if (!$error){
            $html .= barWidgetSetup($json_data, array( 'id'=>$id, 'labels'=>'["'.get_string('date').'", "'.get_string('role1', 'local_intelliboard').'", "'.get_string('role2', 'local_intelliboard').'"]', 'options'=>''), 300);
        }else{
            $html .= '<div class="alert alert-warning">'.$no_data.'</div>';
        }
        return htmlWidgetSetup($html, array('style'=>'grid', 'name'=> $widget_name));
    }
}
function htmlWidgetSetup($data, $params = array())
{
    return '<div class="'.$params['style'].'">
        <h3>'.$params['name'].'</h3>
        <div class="scroly">'.$data.'</div>
    </div>';
}
function barWidgetSetup($data, $params = array(), $height = 300){
    return '<script type="text/javascript">
        function drawChart'.$params['id'].'() {
            var data = google.visualization.arrayToDataTable(['.$params['labels'].', '.implode(",", $data).']);
            var options = {backgroundColor:{fill:"transparent"},title: "", '.$params['options'].' chartArea: {width: "99%",left:40}};
            var chart = new google.visualization.ColumnChart(document.getElementById("widget'.$params['id'].'"));
            chart.draw(data, options);
        }
        drawChart'.$params['id'].'();
    </script>
    <div id="widget'.$params['id'].'" style="width: 100%; height:'.$height.'px;"></div>';
}
function barHWidgetSetup($data, $params = array(), $height = 300){
    return '<script type="text/javascript">
        function drawChart'.$params['id'].'() {
            var data = google.visualization.arrayToDataTable(['.$params['labels'].', '.implode(",", $data).']);
            var options = {backgroundColor:{fill:"transparent"},title: "", '.$params['options'].' legend: { position: "bottom" }, chartArea:  {width: "99%",left:100}, bars: "horizontal", hAxis: {format: "decimal"}, colors: ["#a0b757", "#577bbb", "#93615f", "#10971d"]};
            var chart = new google.visualization.BarChart(document.getElementById("widget'.$params['id'].'"));
            chart.draw(data, options);
        }
       drawChart'.$params['id'].'();
    </script>
    <div id="widget'.$params['id'].'" style="width: 100%; height:'.$height.'px;"></div>';
}

function lineWidgetSetup($data, $colums, $params = array(), $height = 250){
    $options = '';
    foreach($colums as $key=>$val){
        $options .= 'data.addColumn("'.$key.'", "'.$val.'");';
    }
    return '<script type="text/javascript">
            function drawChart'.$params['id'].'() {
                var data = new google.visualization.DataTable();
                '.$options.'
                data.addRows(['.implode(",", $data).']);

                var options = {
                    chartArea: {width: "99%",left:40},
                    height: '.$height.',
                    hAxis: {format: "'.$params['format'].'",gridlines: {color: "none"}},
                    vAxis: {gridlines: {count: 5},minValue: 0},
                    backgroundColor:{fill:"transparent"},
                    legend: { position: "bottom" }
                };
                var chart = new google.visualization.LineChart(document.getElementById("widget'.$params['id'].'"));
                chart.draw(data, options);
            }
            drawChart'.$params['id'].'();
            </script><div id="widget'.$params['id'].'"></div>';
}
function getWidgetFilters($id, $filters, $params)
{
    global $DB;

    $html = '<form class="widget-filters clearfix" id="widgetform'.$id.'">';
    $html .= '<input type="hidden" name="widget" value="'.$id.'"/>';
    $html .= '<input type="hidden" name="trigger" value="1"/>';
    $filters = !empty($filters) ? $filters : array();
    foreach ($filters as $filter) {
        if($filter == 'daterange'){
            if($params->timestart and $params->timefinish){
                $daterange = ', defaultDate: ["'.date("Y-m-d", $params->timestart).'", "'.date("Y-m-d", $params->timefinish).'"]';
                $daterange_text = date("Y-m-d", $params->timestart) . ' to ' . date("Y-m-d", $params->timefinish);
            } else{
                $daterange_text = '';
            }

            $html .= '<input class="widget-daterange" name="daterange" id="widget-daterange'.$id.'" type="text" value="'.$daterange_text.'" placeholder="'.get_string('select_date', 'local_intelliboard').'" />';
            $html .= '<script type="text/javascript">';
            $html .= '$("#widget-daterange'.$id.'").flatpickr({ mode: "range", locale: "'.current_language().'" ,dateFormat: "Y-m-d", onClose: function(selectedDates, dateStr, instance){ updatewidget('.$id.'); }'.$daterange.'});';
            $html .= '</script>';
        }
        if($filter == 'system'){
            $html .= '<select name="custom2[]" id="custom2'.$id.'" class="widget-select custom2'.$id.'" multiple="multiple">';
            $html .= '<option value="0" '.((!$params->custom2) ? 'selected="selected"' : "").'>'.get_string('moodle', 'local_intelliboard').'</option>';
            $html .= '<option value="1" '.(($params->custom2) ? 'selected="selected"' : "").'>'.get_string('totara', 'local_intelliboard').'</option>';
            $html .= '</select>';
            $html .= '<script type="text/javascript">';
            $html .= '$("#custom2'.$id.'").multipleSelect({minimumCountSelected:1, filter:true, placeholder:"'.get_string('select', 'local_intelliboard').'", selectAllText:"'.get_string('selectall', 'local_intelliboard').'", single:true, onClose: function() {updatewidget('.$id.');}});';
            $html .= 'jQuery(".custom2'.$id.' .ms-drop").append(\'<div class="actions"><button type="button" class="custom2-close'.$id.'">'.get_string('ok', 'local_intelliboard').'</button></div>\');';
            $html .= 'jQuery(".custom2-close'.$id.'").click(function(){updatewidget('.$id.'); jQuery("#custom2'.$id.'").multipleSelect("close"); });';
            $html .= '</script>';
        }
        if($filter == 'enrols'){
            $enrolments = $DB->get_records_sql("SELECT e.id, e.enrol FROM {enrol} e GROUP BY e.enrol");

            $html .= '<select name="custom[]" id="custom'.$id.'" class="widget-select custom'.$id.'" multiple="multiple">';
            foreach($enrolments as $enrol){
                $custom = (isset($params->custom) and !empty($params->custom)) ? explode(",", $params->custom) : array();
                $html .= '<option value="'.$enrol->id.'" '.((in_array($enrol->id, $custom)) ? 'selected="selected"' : "").'>'.$enrol->enrol .'</option>';
            }
            $html .= '</select>';
            $html .= '<script type="text/javascript">';
            $html .= '$("#custom'.$id.'").multipleSelect({minimumCountSelected:1, filter:true, placeholder:"'.get_string('select', 'local_intelliboard').'", selectAllText:"'.get_string('selectall', 'local_intelliboard').'", onClose: function() {}});';
            $html .= 'jQuery(".custom'.$id.' .ms-drop").append(\'<div class="actions"><button type="button" class="custom-close'.$id.'">'.get_string('ok', 'local_intelliboard').'</button></div>\');';
            $html .= 'jQuery(".custom-close'.$id.'").click(function(){updatewidget('.$id.'); jQuery("#custom'.$id.'").multipleSelect("close"); });';
            $html .= '</script>';
        }
        if($filter == 'profilefields'){
            $fields = $DB->get_records_sql("SELECT uif.id, uif.name FROM {user_info_field} uif ORDER BY uif.name");
            $custom = (isset($params->custom) and !empty($params->custom)) ? explode(",", $params->custom) : array();
            $custom2 = (isset($params->custom2) and !empty($params->custom2)) ? explode(",", $params->custom2) : array();

            $html .= '<select name="custom[]" id="custom'.$id.'" class="widget-select  custom'.$id.'" multiple="multiple">';
            foreach($fields as $item){
                $html .= '<option value="'.$item->id.'" '.((in_array($item->id, $custom)) ? 'selected="selected"' : "").'>'.$item->name .'</option>';
            }
            $html .= '</select>';
            $html .= '<select name="custom2[]" id="custom2'.$id.'" class="widget-select custom2'.$id.'" multiple="multiple">';

            if($custom){
                $result = $DB->get_records_sql("
                    SELECT id, fieldid, data, count(id) as items
                    FROM {user_info_data}
                    WHERE fieldid = ?
                    GROUP BY data
                    ORDER BY data ASC", array($params->custom));

                foreach($result as $item){
                    if(!empty($item->data)){
                        $html .= '<option value="'.$item->id.'"  '.((in_array($item->id, $custom2)) ? 'selected="selected"' : "").'>'. $item->data .'</option>';
                    }
                }
            }
            $html .= '</select>';
            $html .= '<script type="text/javascript">';
            $html .= '$("#custom'.$id.'").multipleSelect({minimumCountSelected:1, filter:true, placeholder:"'.get_string('select', 'local_intelliboard').'", selectAllText:"'.get_string('selectall', 'local_intelliboard').'", single:true, onClose: function() { info_fields('.$id.'); }});';

            $html .= '$("#custom2'.$id.'").multipleSelect({minimumCountSelected:1, filter:true, placeholder:"'.get_string('select', 'local_intelliboard').'", selectAllText:"'.get_string('selectall', 'local_intelliboard').'", onClose: function() { }});';

            $html .= 'jQuery(".custom'.$id.' .ms-drop").append(\'<div class="actions"><button type="button" class="custom-close'.$id.'">'.get_string('ok', 'local_intelliboard').'</button></div>\');';
            $html .= 'jQuery(".custom-close'.$id.'").click(function(){jQuery("#custom'.$id.'").multipleSelect("close"); });';

            $html .= 'jQuery(".custom2'.$id.' .ms-drop").append(\'<div class="actions"><button type="button" class="custom2-close'.$id.'">'.get_string('ok', 'local_intelliboard').'</button></div>\');';
            $html .= 'jQuery(".custom2-close'.$id.'").click(function(){updatewidget('.$id.'); jQuery("#custom2'.$id.'").multipleSelect("close"); });';

            $html .= '</script>';
        }
        if($filter == 'cohort'){
            $cohorts = $DB->get_records_sql("SELECT id, name FROM {cohort} ORDER BY name");

            $html .= '<select name="cohortid[]" id="cohort'.$id.'" class="widget-select cohort'.$id.'" multiple="multiple">';
            foreach($cohorts as $cohort){
                if(!empty($cohort->name)){
                    $cohortids = (isset($params->cohortid) and !empty($params->cohortid)) ? explode(",", $params->cohortid) : array();
                    $name = preg_replace("/[^\w_ ]+/u", "", $cohort->name);
                    $html .= '<option value="'.$cohort->id.'" '.((in_array($cohort->id, $cohortids)) ? 'selected="selected"' : "").'>'.$name .'</option>';
                }
            }
            $html .= '</select>';
            $html .= '<script type="text/javascript">';
            $html .= '$("#cohort'.$id.'").multipleSelect({minimumCountSelected:1, filter:true, placeholder:"'.get_string('cohorts', 'local_intelliboard').'", selectAllText:"'.get_string('selectall', 'local_intelliboard').'", onClose: function() {}});';
            $html .= 'jQuery(".cohort'.$id.' .ms-drop").append(\'<div class="actions"><button type="button" class="cohort-close'.$id.'">'.get_string('ok', 'local_intelliboard').'</button></div>\');';
            $html .= 'jQuery(".cohort-close'.$id.'").click(function(){updatewidget('.$id.'); jQuery("#cohort'.$id.'").multipleSelect("close"); });';
            $html .= '</script>';
        }

    }
    $html .= '</form>';
    $html .= '<div class="clear"></div>';


    return $html;
}

function intelliboard_instructor_have_access($userid){
    $instructor_roles = get_config('local_intelliboard', 'filter10');
    if (!empty($instructor_roles)) {
        $roles = explode(',', $instructor_roles);
        if (!empty($roles)) {
            foreach ($roles as $role) {
                if ($role and user_has_role_assignment($userid, $role)){
                    return true;
                }
            }
        }
    }
    return false;
}

function intelliboard_group_aggregation_sql($user_alias, $user_id, $course_alias){
    $user_id = (int)$user_id;
    if(get_config('local_intelliboard', 'group_aggregation')){
            return " JOIN (SELECT gm.userid,c.id AS courseid
                        FROM {course} c
                          LEFT JOIN {groups} g ON g.courseid=c.id
                          LEFT JOIN {groups_members} gm ON g.id=gm.groupid AND gm.groupid IN (SELECT groupid FROM {groups_members} WHERE userid = $user_id)
                          LEFT JOIN (SELECT g.courseid FROM {groups} g WHERE g.id IN (SELECT groupid FROM {groups_members} WHERE userid = $user_id)) cg ON cg.courseid=c.id
                        WHERE cg.courseid IS NULL OR gm.userid IS NOT NULL
                        GROUP BY gm.userid,c.id
                       ) group_user ON (group_user.userid=$user_alias AND group_user.courseid=$course_alias) OR (group_user.userid IS NULL AND group_user.courseid=$course_alias) ";
    }else{
        return '';
    }
}

function intelliboard_course_session_learners_total($sessionid, $courseid)
{
    global $DB;

    $params = array('sessionid' => $sessionid, 'courseid' => $courseid);
    list($sql_roles, $sql_params) = $DB->get_in_or_equal(explode(',', get_config('local_intelliboard', 'filter11')), SQL_PARAMS_NAMED, 'r');
    $params = array_merge($params,$sql_params);
    $grade_avg = intelliboard_grade_sql(true);

    return $DB->get_record_sql("
        SELECT c.id,c.fullname, c.startdate, c.enablecompletion,
            (SELECT name FROM {course_categories} WHERE id = c.category) AS category,
            (SELECT COUNT(id) FROM {course_sections} WHERE visible = 1 AND course = c.id) AS sections,
            COUNT(DISTINCT ra.userid) as learners,
            COUNT(DISTINCT g.userid) as learners_graduated,
            COUNT(DISTINCT cc.id) as learners_completed,
            $grade_avg AS grade,
            SUM(l.timespend) as timespend,
            SUM(l.visits) as visits
        FROM {role_assignments} ra
        LEFT JOIN {context} e ON e.id = ra.contextid AND e.contextlevel = 50
        LEFT JOIN {course} c ON c.id = e.instanceid
        LEFT JOIN {course_completions} cc ON cc.course = c.id AND cc.userid = ra.userid AND cc.timecompleted > 0
        LEFT JOIN {grade_items} gi ON gi.itemtype = 'course' AND gi.courseid = c.id
        LEFT JOIN {grade_grades} g ON g.userid = ra.userid AND g.itemid = gi.id AND g.finalgrade IS NOT NULL
        LEFT JOIN (SELECT t.userid,t.courseid, SUM(t.timespend) as timespend, SUM(t.visits) as visits FROM
            {local_intelliboard_tracking} t GROUP BY t.courseid, t.userid) l ON l.courseid = c.id AND l.userid = ra.userid
        LEFT JOIN {enrol_intellicart} ei ON ei.courseid = c.id AND ei.userid = ra.userid AND ei.unenroldate = 0
            LEFT JOIN {local_intellicart_logs} il ON il.checkoutid = ei.checkoutid AND il.userid = ei.userid AND il.type = 'product'
        WHERE ra.roleid $sql_roles AND e.instanceid = :courseid AND il.sessionid = :sessionid GROUP BY c.id LIMIT 1", $params);
}

function intelliboard_course_session_activities_data($sessionid, $courseid)
{
    global $DB;

    $params = array(
            'courseid' => $courseid,
            'courseid2' => $courseid,
            'courseid3' => $courseid,
            'courseid4' => $courseid,
            'sid1' => $sessionid,
            'csid1' => $courseid,
            'sid2' => $sessionid,
            'csid2' => $courseid,
            'sid3' => $sessionid,
            'csid3' => $courseid
    );

    list($sql1, $sql_params) = $DB->get_in_or_equal(explode(',', get_config('local_intelliboard', 'filter11')), SQL_PARAMS_NAMED, 'r');
    $params = array_merge($params,$sql_params);
    $grade_avg = intelliboard_grade_sql(true);
    $completion = intelliboard_compl_sql("cmc.");

    return $DB->get_record_sql("
        SELECT
            c.id AS courseid,
            c.fullname,
            c.startdate,
            l.visits,
            l.timespend,
            (SELECT name FROM {course_categories} WHERE id = c.category) AS category,
            (SELECT COUNT(id) FROM {course_sections} WHERE visible = 1 AND course = c.id) AS sections,
            (SELECT COUNT(id) FROM {course_modules} WHERE visible = 1 AND course = c.id) AS modules,
            (SELECT COUNT(cmc.id) FROM {course_modules} cm, {course_modules_completion} cmc
                WHERE cm.visible = 1 AND cm.course = c.id $completion AND cmc.coursemoduleid = cm.id AND cmc.userid IN (SELECT ei.userid
                      FROM {local_intellicart_logs} il
                 LEFT JOIN {enrol_intellicart} ei ON il.checkoutid = ei.checkoutid AND il.userid = ei.userid
                     WHERE il.type = 'product' AND ei.unenroldate = 0 AND ei.courseid = :csid1 AND il.sessionid = :sid1)) AS completed,
            (SELECT $grade_avg FROM {grade_items} gi, {grade_grades} g
                WHERE gi.itemtype = 'course' AND g.itemid = gi.id AND g.finalgrade IS NOT NULL AND gi.courseid = c.id AND g.userid IN (SELECT ei.userid
                      FROM {local_intellicart_logs} il
                 LEFT JOIN {enrol_intellicart} ei ON il.checkoutid = ei.checkoutid AND il.userid = ei.userid
                     WHERE il.type = 'product' AND ei.unenroldate = 0 AND ei.courseid = :csid2 AND il.sessionid = :sid2)) AS grade
        FROM {course} c
            LEFT JOIN (
                SELECT l.courseid, SUM(l.visits) AS visits, SUM(l.timespend) AS timespend
                FROM {local_intelliboard_tracking} l WHERE l.courseid = :courseid2 AND l.userid IN (SELECT DISTINCT ra.userid FROM {role_assignments} ra, {context} ctx WHERE ctx.id = ra.contextid AND ctx.instanceid = :courseid4 AND ctx.contextlevel = 50 AND ra.userid IN (SELECT ei.userid
                      FROM {local_intellicart_logs} il
                 LEFT JOIN {enrol_intellicart} ei ON il.checkoutid = ei.checkoutid AND il.userid = ei.userid
                     WHERE il.type = 'product' AND ei.unenroldate = 0 AND ei.courseid = :csid3 AND il.sessionid = :sid3) AND ra.roleid $sql1)
                GROUP BY l.courseid) l ON l.courseid=c.id
        WHERE c.id = :courseid GROUP BY c.id", $params);
}

function intelliboard_course_session_activity_data($cmid, $sessionid, $courseid)
{
    global $DB;

    $params = array('cmid' => $cmid);
    $cm = $DB->get_record_sql("SELECT cm.id, cm.instance, m.name FROM {course_modules} cm, {modules} m WHERE cm.id = :cmid AND m.id = cm.module", $params);

    list($sql1, $sql_params) = $DB->get_in_or_equal(explode(',', get_config('local_intelliboard', 'filter11')), SQL_PARAMS_NAMED, 'r');
    $params = array_merge($params,$sql_params);

    $params['instance'] = $cm->instance;
    $params['instance2'] = $cm->instance;
    $params['module'] = $cm->name;
    $params['courseid'] = $courseid;
    $params['cmid2'] = $cmid;
    $params['cmid3'] = $cmid;
    $params['csid1'] = $courseid;
    $params['sid1'] = $sessionid;
    $params['csid2'] = $courseid;
    $params['sid2'] = $sessionid;
    $grade_avg = intelliboard_grade_sql(true);
    $completion = intelliboard_compl_sql("", false);

    return $DB->get_record_sql("
        SELECT
            cm.id, c.id AS courseid,
            c.fullname as course,
            i.name,
            ca.name AS category,
            cs.section,
            m.name as module, l.visits, l.timespend,
            (SELECT COUNT(id) FROM {course_modules_completion} WHERE $completion AND coursemoduleid=:cmid AND userid IN (SELECT ei.userid FROM {local_intellicart_logs} il LEFT JOIN {enrol_intellicart} ei ON il.checkoutid = ei.checkoutid AND il.userid = ei.userid WHERE il.type = 'product' AND ei.unenroldate = 0 AND ei.courseid = :csid1 AND il.sessionid = :sid1)) AS completed,
            (SELECT $grade_avg FROM {grade_items} gi, {grade_grades} g WHERE gi.itemtype = 'mod' AND g.itemid = gi.id AND g.finalgrade IS NOT NULL AND gi.itemmodule = :module AND gi.iteminstance = :instance2 AND userid IN (SELECT ei.userid FROM {local_intellicart_logs} il LEFT JOIN {enrol_intellicart} ei ON il.checkoutid = ei.checkoutid AND il.userid = ei.userid WHERE il.type = 'product' AND ei.unenroldate = 0 AND ei.courseid = :csid2 AND il.sessionid = :sid2)) AS grade
        FROM {course_modules} cm
            LEFT JOIN {modules} m ON m.id = cm.module
            LEFT JOIN {course} c ON c.id = cm.course
            LEFT JOIN {course_sections} cs ON cs.id = cm.section AND cs.course = c.id
            LEFT JOIN {course_categories} ca ON ca.id = c.category
            LEFT JOIN {".$cm->name."} i ON i.id = :instance
            LEFT JOIN (SELECT l.param, SUM(l.visits) AS visits, SUM(l.timespend) AS timespend FROM {local_intelliboard_tracking} l WHERE l.page='module' AND l.param = :cmid2 AND l.userid IN (SELECT DISTINCT ra.userid FROM {role_assignments} ra, {context} ctx WHERE ctx.id = ra.contextid AND ctx.instanceid = :courseid AND ctx.contextlevel = 50 AND ra.roleid $sql1) GROUP BY l.param) l ON l.param=cm.id
        WHERE cm.id = :cmid3", $params);
}

function intelliboard_instructor_hide_suspended_enrollments_joinsql($courseColumn, $userColumn, $whereCondition = '')
{
    $sql = "";
    if (get_config('local_intelliboard', 'instructor_hide_suspended_enrollments')) {
        $onCondition = !empty($courseColumn) ? "enr.courseid = {$courseColumn}" : "";
        $onCondition .= (!empty($onCondition) && !empty($userColumn)) ? " AND " : "";
        $onCondition .= !empty($userColumn) ? "enr.userid = {$userColumn}" : "";
        $onCondition = !empty($onCondition) ? " ON {$onCondition}" : "";
        $sql_filter = intelliboard_instructor_getcourses('e.courseid', false, 'ue.userid');
        $sql_filter .= !(empty($whereCondition)) ? " AND {$whereCondition}" : "";
        $sql = "
            JOIN (
                  SELECT ue.userid, e.courseid
                    FROM {user_enrolments} ue
                    JOIN {enrol} e ON ue.enrolid = e.id
                    WHERE ue.status = 0 {$sql_filter}
                GROUP BY ue.userid, e.courseid
           ) enr {$onCondition}
        ";
    }
    return $sql;
}

function graded_activities_overview_export($course, $format)
{
    global $USER, $DB, $CFG;

    if($course && $format) {
        $data = [[
            get_string('activity', 'local_intelliboard'),
            get_string('grade', 'local_intelliboard'),
        ]];
        $join_senrollments = intelliboard_instructor_hide_suspended_enrollments_joinsql('ga.courseid', 'gg.userid');
        $activities = $DB->get_records_sql(
            "SELECT ga.id, ga.itemname, AVG(gg.finalgrade) as grade
                   FROM {grade_items} ga
              LEFT JOIN {grade_grades} gg ON gg.itemid = ga.id
                        $join_senrollments
                  WHERE ga.courseid = :course AND ga.itemtype = 'mod'
               GROUP BY ga.id, ga.itemname",
            ['course' => $course]
        );
        foreach ($activities as $activity) {
            $data[] = [
                intellitext($activity->itemname),
                round($activity->grade, 2)
            ];
        }
        $header = [];
        $head1 = new \stdClass();
        $head1->name = get_string('activity', 'local_intelliboard');
        $header[] = $head1;

        $head2 = new \stdClass();
        $head2->name = get_string('grade', 'local_intelliboard');
        $header[] = $head2;

        $json = new \stdClass();
        $json->header = $header;
        $json->body = array_slice($data, 1);
        return intelliboard_export_report(
            $json, get_string('grade_activities_overview', 'local_intelliboard'), $format
        );
    }
}

function intelliboard_str_to_timestamp($datestring, $dayEnd = null)
{
        $date = date_create_from_format(
                    intelli_date_format(),
                    trim($datestring)
                );

        if ($dayEnd) {
           return $date->setTime(23,59,59)->getTimestamp();
        }
        return $date->setTime(0,0,0) ->getTimestamp();
}
