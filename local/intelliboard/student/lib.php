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
 * @website    http://intelliboard.net/
 */

defined('MOODLE_INTERNAL') || die();

function intelliboard_data($type, $userid, $showing_user) {
    global $DB;

    $data = array();
    $page = optional_param($type.'_page', 0, PARAM_INT);
    $search = clean_raw(optional_param('search', '', PARAM_RAW));
    $t = optional_param('type', '', PARAM_ALPHANUMEXT);
    $perpage = 10;
    $start = $page * $perpage;
    $params = array();
    $query = "";

    if($type == 'assignment'){
        $sql = "";
        if($search and $t == 'assignment'){
            $sql .= " AND (" . $DB->sql_like('c.fullname', ":fullname", false, false);
            $sql .= " OR " . $DB->sql_like('a.name', ":name", false, false);
            $sql .= ")";
            $params['fullname'] = "%$search%";
            $params['name'] = "%$search%";
        }
        if($showing_user->activity_courses){
            $sql .= " AND c.id = :activity_courses";
            $params['activity_courses'] = intval($showing_user->activity_courses);
        }else{
            $sql .= " AND c.id IN (SELECT e.courseid FROM {user_enrolments} ue, {enrol} e WHERE ue.userid = :userid1 AND e.id = ue.enrolid AND ue.status = 0)";
            $params['userid1'] = $showing_user->id;
        }
        if($showing_user->activity_time !== -1){
            list($timestart, $timefinish) = get_timerange($showing_user->activity_time);
            $sql .= " AND a.duedate BETWEEN :timestart AND :timefinish";
            $params['timestart'] = $timestart;
            $params['timefinish'] = $timefinish;
        }

        $sql .= (get_config('local_intelliboard', 'student_course_visibility')) ? "" : " AND c.visible = 1";

        if (get_config('local_intelliboard', 'coursecontainer_available') && get_config('local_intelliboard', 'coursecontainer_filter')) {
            $sql .=  " AND c.containertype = 'container_course'";
        }

        $grade_single = intelliboard_grade_sql(false, null, 'g.', clean_param(get_config('local_intelliboard', 'scale_percentage_round'), PARAM_INT));
        $query = "SELECT a.id, a.name, a.duedate, c.id AS course_id, c.fullname, $grade_single AS grade, cmc.completionstate, cm.id as cmid
                    FROM {course} c, {assign} a
                        LEFT JOIN {modules} m ON m.name = 'assign'
                        LEFT JOIN {course_modules} cm ON cm.module = m.id AND cm.instance = a.id AND cm.instance > 0
                        LEFT JOIN {course_modules_completion} cmc ON cmc.coursemoduleid = cm.id AND cmc.userid = :userid2
                        LEFT JOIN {grade_items} gi ON gi.itemmodule = m.name AND gi.iteminstance = a.id AND gi.hidden = 0
                        LEFT JOIN {grade_grades} g ON g.itemid = gi.id AND g.userid = :userid3
                    WHERE c.id = a.course AND cm.instance > 0 AND cm.visible = 1 $sql ORDER BY cm.added ASC";
        $params['userid2'] = $userid;
        $params['userid3'] = $userid;

        $data = $DB->get_records_sql($query, $params, $start, $perpage);

        $data = array_map(function ($item) use ($userid) {
            $modinfo = get_fast_modinfo($item->course_id, $userid);
            $cm = $modinfo->get_cm($item->cmid);
            $item->hide = !$cm->uservisible && !$cm->availableinfo;
            $item->availableinfo = (bool) $cm->availableinfo;
            return $item;
        }, $data);

        $data = array_filter($data, function($item) {
            return !$item->hide;
        });
    }elseif ($type == 'quiz') {
        $sql = "";
        if($search and $t == 'quiz'){
            $sql .= " AND (" . $DB->sql_like('c.fullname', ":fullname", false, false);
            $sql .= " OR " . $DB->sql_like('a.name', ":name", false, false);
            $sql .= ")";
            $params['fullname'] = "%$search%";
            $params['name'] = "%$search%";
        }

        if($showing_user->activity_courses){
            $sql .= " AND c.id = :activity_courses";
            $params['activity_courses'] = intval($showing_user->activity_courses);
        }else{
            $sql .= " AND c.id IN (SELECT e.courseid FROM {user_enrolments} ue, {enrol} e WHERE ue.userid = :userid AND e.id = ue.enrolid AND ue.status = 0)";
            $params['userid'] = $showing_user->id;
        }
        if($showing_user->activity_time !== -1){
            list($timestart, $timefinish) = get_timerange($showing_user->activity_time);
            $sql .= " AND a.timeclose BETWEEN :timestart AND :timefinish";
            $params['timestart'] = $timestart;
            $params['timefinish'] = $timefinish;
        }
        $sql .= (get_config('local_intelliboard', 'student_course_visibility')) ? "" : " AND c.visible = 1";

        if (get_config('local_intelliboard', 'coursecontainer_available') && get_config('local_intelliboard', 'coursecontainer_filter')) {
            $sql .=  " AND c.containertype = 'container_course'";
        }

        $grade_single = intelliboard_grade_sql(false, null, 'g.', clean_param(get_config('local_intelliboard', 'scale_percentage_round'), PARAM_INT));

        $query = "SELECT gi.id, a.name, a.timeclose, c.fullname, $grade_single AS grade, cmc.completionstate, cm.id as cmid
                  FROM {course} c, {quiz} a
                    LEFT JOIN {modules} m ON m.name = 'quiz'
                    LEFT JOIN {course_modules} cm ON cm.module = m.id AND cm.instance = a.id
                    LEFT JOIN {course_modules_completion} cmc ON cmc.coursemoduleid = cm.id AND cmc.userid = :userid2
                    LEFT JOIN {grade_items} gi ON gi.itemmodule = m.name AND gi.iteminstance = a.id AND gi.hidden <> 1
                    LEFT JOIN {grade_grades} g ON g.itemid = gi.id AND g.userid = :userid3
                  WHERE c.id = a.course AND cm.instance > 0 AND cm.visible = 1 $sql ORDER BY cm.added ASC";
        $params['userid2'] = $userid;
        $params['userid3'] = $userid;

        $data = $DB->get_records_sql($query, $params, $start, $perpage);
    } elseif ($type == 'course') {
        $sql = "";
        if ($search and $t == 'course') {
            $sql .= " AND " . $DB->sql_like('c.fullname', ":fullname", false, false);
            $params['fullname'] = "%$search%";
        }
        $params['userid1'] = $userid;
        $params['userid2'] = $userid;
        $params['userid3'] = $userid;
        $params['userid4'] = $userid;
        $params['userid5'] = $userid;

        $grade_single = intelliboard_grade_sql(
            false, null, 'g.',
            clean_param(get_config('local_intelliboard', 'scale_percentage_round'), PARAM_INT), 'gi.',
            !get_config('local_intelliboard', 'scale_real')
        );

        $completion = intelliboard_compl_sql("cmc.");

        $order_by = 'ORDER BY c.sortorder';

        if (get_config('local_intelliboard', 't52')) {
            $order_by = 'ORDER BY c.sortorder,c.category';
        }

        $sql .= (get_config('local_intelliboard', 'student_course_visibility')) ? "" : " AND c.visible = 1";

        if (get_config('local_intelliboard', 'coursecontainer_available') && get_config('local_intelliboard', 'coursecontainer_filter')) {
            $sql .=  " AND c.containertype = 'container_course'";
        }

        $query = "SELECT MAX(c.id) AS id, c.fullname,
                         MIN(ue.timemodified) AS timemodified,
                         c.category, 'course' AS type,
                         (SELECT $grade_single
                            FROM {grade_items} gi, {grade_grades} g
                           WHERE gi.itemtype = 'course' AND g.itemid = gi.id AND
                                 g.finalgrade IS NOT NULL AND gi.courseid = c.id AND
                                 g.userid = :userid1
                         ) AS grade,
                         (SELECT COUNT(cmc.id)
                            FROM {course_modules} cm, {course_modules_completion} cmc
                           WHERE cm.id = cmc.coursemoduleid $completion AND
                                 cm.visible = 1 AND cm.instance > 0 AND cm.course = c.id AND
                                 cmc.userid = :userid4
                         ) AS completedmodules,
                         (SELECT SUM(timespend)
                            FROM {local_intelliboard_tracking}
                           WHERE userid = :userid3 AND courseid = c.id
                         ) AS duration,
                         (SELECT COUNT(id)
                            FROM {course_modules}
                           WHERE visible = 1 AND instance > 0 AND completion > 0 AND course = c.id
                         ) AS modules,
                         (SELECT timecompleted
                            FROM {course_completions}
                           WHERE course = c.id AND userid = :userid5
                         ) AS timecompleted
                    FROM {user_enrolments} ue
               LEFT JOIN {enrol} e ON e.id = ue.enrolid
               LEFT JOIN {course_completions} cc ON cc.course = e.courseid AND
                                                    cc.userid = ue.userid
               LEFT JOIN {course} c ON c.id = e.courseid
                   WHERE ue.userid = :userid2 AND ue.status = 0 $sql
                GROUP BY c.id
                         $order_by";

        $data = $DB->get_records_sql($query, $params, $start, $perpage);

        if (get_config('local_intelliboard', 't52')) {
            $categories = array();
            foreach ($data as $item) {
                $categories[$item->category] = true;
            }
            $inner_sql = (!empty($categories))?"AND c.category IN(".implode(',', array_keys($categories)).")":"";

            $grade_avg = intelliboard_grade_sql(true);
            $grade_category = $DB->get_records_sql(
                "SELECT c.category, cc.name, $grade_avg AS grade
                   FROM {course} c
                   JOIN {course_categories} cc ON cc.id=c.category
              LEFT JOIN {grade_items} gi ON gi.courseid = c.id AND
                                            gi.itemtype = 'course'
              LEFT JOIN {grade_grades} g ON g.itemid = gi.id AND
                                            g.finalgrade IS NOT NULL
                  WHERE gi.courseid NOT IN (SELECT DISTINCT courseid
                                              FROM {grade_items}
                                             WHERE hidden = 1) $inner_sql
               GROUP BY c.category"
            );

            $i=0;
            $inserted_cats=0;
            $old_cat = 0;
            foreach ($data as $record) {
                if (($i == 1 && $old_cat == 0) || $old_cat != $record->category) {
                    $cat = $grade_category[$record->category];
                    $cat_row = new stdClass();
                    $cat_row->type = 'category';
                    $cat_row->fullname = $cat->name;
                    $cat_row->grade = $cat->grade;

                    array_splice( $data, $i+$inserted_cats, 0, array($cat_row) );
                    $inserted_cats++;
                }
                $old_cat = $record->category;
                $i++;
            }
        }
    } elseif ($type == 'courses') {
        $sql = "";
        if ($search) {
            $sql .= " AND " . $DB->sql_like('c.fullname', ":fullname", false, false);
            $params['fullname'] = "%$search%";
        }

        $res = $DB->get_record_sql("SELECT COUNT(cm.id) as certificates FROM {course_modules} cm, {modules} m WHERE m.name = 'certificate' AND cm.module = m.id AND cm.visible = 1 AND cm.instance > 0");
        $sql_select = "";
        $sql_join = "";
        if($res->certificates){
            $sql_select = ", (SELECT COUNT(ci.id) FROM {certificate} c, {certificate_issues} ci WHERE c.id = ci.certificateid AND ci.userid = :userid1 AND c.course = c.id) AS certificates";
        }else{
            $sql_select = ",'' as certificates";
        }
        $params['userid1'] = $userid;
        $params['userid2'] = $userid;
        $params['userid3'] = $userid;
        $params['userid4'] = $userid;
        $params['userid5'] = $userid;
        $params['userid6'] = $userid;

        $grade_single = intelliboard_grade_sql(false, null, 'g.', clean_param(get_config('local_intelliboard', 'scale_percentage_round'), PARAM_INT));
        $grade_avg = intelliboard_grade_sql(true, null, 'g.', clean_param(get_config('local_intelliboard', 'scale_percentage_round'), PARAM_INT));
        $completion = intelliboard_compl_sql("cmc.");

        $teacher_roles = get_config('local_intelliboard', 'filter10');
        list($sql_teacher_roles, $params) = intelliboard_filter_in_sql($teacher_roles, "ra.roleid", $params);
        $sql .= (get_config('local_intelliboard', 'student_course_visibility')) ? "" : " AND c.visible = 1";
        if (get_config('local_intelliboard', 'coursecontainer_available') && get_config('local_intelliboard', 'coursecontainer_filter')) {
            $sql .=  " AND c.containertype = 'container_course'";
        }

        $query = "SELECT c.id, c.fullname, MIN(ue.timemodified) AS timemodified,
                (SELECT $grade_single FROM {grade_items} gi, {grade_grades} g WHERE gi.itemtype = 'course' AND g.itemid = gi.id AND g.finalgrade IS NOT NULL AND gi.courseid = c.id AND g.userid = :userid6 AND gi.hidden <> 1) AS grade,
                (SELECT $grade_avg FROM {grade_items} gi, {grade_grades} g WHERE gi.itemtype = 'course' AND g.itemid = gi.id AND g.finalgrade IS NOT NULL AND gi.courseid = c.id AND gi.hidden <> 1) AS average,
                (SELECT SUM(timespend) FROM {local_intelliboard_tracking} WHERE userid = :userid2 AND courseid = c.id) AS duration,
                (SELECT name FROM {course_categories} WHERE id = c.category) AS category,
                (SELECT COUNT(cmc.id) FROM {course_modules} cm, {course_modules_completion} cmc WHERE cm.id = cmc.coursemoduleid $completion AND cm.instance > 0 AND cm.visible = 1 AND cm.course = c.id AND cmc.userid = :userid4) AS completedmodules,
                (SELECT COUNT(id) FROM {course_modules} WHERE instance > 0 AND visible = 1 AND completion > 0 AND course = c.id) AS modules,
                (SELECT timecompleted FROM {course_completions} WHERE course = c.id AND userid = :userid5) AS timecompleted,
                (SELECT DISTINCT u.id
                    FROM {role_assignments} AS ra
                        JOIN {user} u ON ra.userid = u.id
                        JOIN {context} AS ctx ON ctx.id = ra.contextid
                    WHERE ctx.instanceid = c.id AND ctx.contextlevel = 50 $sql_teacher_roles LIMIT 1
                ) AS teacher
                $sql_select
            FROM {user_enrolments} ue
                LEFT JOIN {enrol} e ON e.id = ue.enrolid
                LEFT JOIN {course} c ON c.id = e.courseid
                    $sql_join
                  WHERE ue.userid = :userid3 AND ue.status = 0 $sql GROUP BY c.id ORDER BY c.sortorder";


        $data = $DB->get_records_sql($query, $params, $start, $perpage);
    }

    $count = $DB->count_records_sql("SELECT COUNT(*) FROM ($query) AS x", $params);
    $pagination = get_pagination($count, $page, $perpage, $type);

    return array("pagination"=>$pagination, "data"=>$data);
}

function get_timerange($time){
    global $DB;

    if($time == 0) {
        $timestart = strtotime(date('01/01/Y', strtotime('-10 year')));
        $timefinish = time();
    }elseif($time == 1){
        $timestart = strtotime('-1 week');
        $timefinish = time();
    }elseif($time == 2){
        $timestart = strtotime('-1 month');
        $timefinish = time();
    }elseif($time == 3){
        $timestart = strtotime('-4 month');
        $timefinish = time();
    }elseif($time == 4){
        $timestart = strtotime('-6 month');
        $timefinish = time();
    }elseif($time == 5){
        $timestart = strtotime(date('01/01/Y'));
        $timefinish = time();
    }elseif($time == 6){
        $timestart = strtotime(date('01/01/Y', strtotime('-1 year')));
        $timefinish = strtotime(date('01/01/Y'));
    }else{
        $timestart = strtotime('-14 days');
        $timefinish = strtotime('+14 days');
    }
    return array($timestart,$timefinish);
}
function get_pagination($count = 0, $page = 0, $perpage = 15, $type = 'intelliboard') {
    global $OUTPUT, $PAGE;

    $pages = (int)ceil($count/$perpage);
    if ($pages == 1 || $pages == 0) {
        return '';
    }
    $link = new moodle_url($PAGE->url, array());
    return $OUTPUT->paging_bar($count, $page, $perpage, $link, $type.'_page');
}

function intelliboard_learner_course_progress($courseid, $userid){
    global $DB;

    $timestart = 0;
    $timefinish = time();

    $data = array();
    $params = array();
    $params['userid'] = $userid;
    $params['timestart'] = $timestart;
    $params['timefinish'] = $timefinish;
    $params['courseid'] = $courseid;

    $grade_avg = intelliboard_grade_sql(true);
    $grade_avg_percent = intelliboard_grade_sql(true, null, 'g.', 0, 'gi.', true);

    $data[] = $DB->get_records_sql("SELECT floor(g.timemodified / 86400) * 86400 as timepoint, $grade_avg AS grade, $grade_avg_percent AS grade_percent
                                    FROM {grade_items} gi, {grade_grades} g
                                    WHERE gi.id = g.itemid AND g.userid = :userid AND gi.itemtype = 'mod' AND g.finalgrade IS NOT NULL AND g.timemodified BETWEEN :timestart AND :timefinish AND gi.courseid = :courseid
                                    GROUP BY timepoint ORDER BY timepoint", $params);

    $data[] = $DB->get_records_sql("SELECT floor(g.timemodified / 86400) * 86400 as timepoint, $grade_avg AS grade, $grade_avg_percent AS grade_percent
                                    FROM {grade_items} gi, {grade_grades} g
                                    WHERE gi.id = g.itemid AND g.userid <> :userid AND gi.itemtype = 'mod' AND g.finalgrade IS NOT NULL AND g.timemodified BETWEEN :timestart AND :timefinish AND gi.courseid = :courseid
                                    GROUP BY timepoint ORDER BY timepoint", $params);
    return $data;
}
function intelliboard_learner_progress($time, $userid){
    global $DB;

    list($timestart, $timefinish) = get_timerange($time);

    $data = array();
    $params = array();
    $params['userid'] = $userid;
    $params['userid2'] = $userid;
    $params['timestart'] = $timestart;
    $params['timefinish'] = $timefinish;

    $grade_avg = intelliboard_grade_sql(true);
    $grade_avg_percent = intelliboard_grade_sql(true, null, 'g.', 0, 'gi.', true);

    $data[] = $DB->get_records_sql(
        "SELECT floor(g.timemodified / 86400) * 86400 AS timepoint, $grade_avg as grade, $grade_avg_percent AS grade_percent
               FROM {grade_items} gi, {grade_grades} g
              WHERE gi.courseid NOT IN (SELECT DISTINCT courseid FROM {grade_items} WHERE hidden = 1 AND gi.itemtype = 'course') AND
                    gi.id = g.itemid AND g.userid = :userid AND gi.itemtype = 'mod' AND g.finalgrade IS NOT NULL AND
                    g.timemodified BETWEEN :timestart AND :timefinish
           GROUP BY timepoint ORDER BY timepoint",
        $params
    );

    if(get_config('local_intelliboard', 't53')){
        $data[] = $DB->get_records_sql("SELECT floor(g.timemodified / 86400) * 86400 AS timepoint, $grade_avg as grade, $grade_avg_percent AS grade_percent
                                    FROM {grade_items} gi, {grade_grades} g
                                    WHERE
                                      gi.courseid NOT IN (SELECT DISTINCT courseid FROM {grade_items} WHERE hidden = 1 AND gi.itemtype = 'course')
                                      AND gi.courseid IN (SELECT DISTINCT e.courseid FROM {user_enrolments} ue, {enrol} e WHERE ue.enrolid=e.id AND ue.status=0 AND ue.userid=:userid2 AND ue.status = 0)
                                      AND gi.id = g.itemid
                                      AND g.userid <> :userid
                                      AND gi.itemtype = 'mod'
                                      AND g.finalgrade IS NOT NULL
                                      AND g.timemodified BETWEEN :timestart AND :timefinish
                                    GROUP BY timepoint ORDER BY timepoint", $params);
    }else{
        $data[] = array();
    }
    return $data;
}

function intelliboard_learner_courses($userid, $time = null){
    global $DB;

    $params = array();
    $params['userid1'] = $userid;
    $params['userid2'] = $userid;
    $params['userid3'] = $userid;

    if ($time !== null) {
        list($timestart, $timefinish) = get_timerange($time);
    } else {
        $timestart = 0;
        $timefinish = strtotime('+1 year');
    }

    $grade_single = intelliboard_grade_sql(false);
    $grade_avg = intelliboard_grade_sql(true);

    $grade_single_percent = intelliboard_grade_sql(false,null, 'g.', 0, 'gi.',true);
    $grade_avg_percent = intelliboard_grade_sql(true,null, 'g.', 0, 'gi.',true);

    $scale_real = get_config('local_intelliboard', 'scale_real');
    $sql = (get_config('local_intelliboard', 'student_course_visibility')) ? "" : " AND c.visible = 1";
    if (get_config('local_intelliboard', 'coursecontainer_available') && get_config('local_intelliboard', 'coursecontainer_filter')) {
        $sql .=  " AND c.containertype = 'container_course'";
    }
    if($scale_real){
        $params['userid4'] = $userid;
        $params = array_merge([
            'timestart1' => $timestart,
            'timefinish1' => $timefinish,
            'timestart2' => $timestart,
            'timefinish2' => $timefinish,
            'timestart3' => $timestart,
            'timefinish3' => $timefinish,
            'timestart4' => $timestart,
            'timefinish4' => $timefinish,
            'timestart5' => $timestart,
            'timefinish5' => $timefinish,
        ], $params);
        $data = $DB->get_records_sql(
            "SELECT c.id, c.fullname, '0' AS duration_calc,
                    (SELECT $grade_single_percent
                       FROM {grade_items} gi, {grade_grades} g
                      WHERE gi.hidden <> 1 AND
                            gi.itemtype = 'course' AND g.itemid = gi.id AND g.finalgrade IS NOT NULL AND
                            gi.courseid = c.id AND g.userid = :userid3 AND g.timemodified BETWEEN :timestart1 AND :timefinish1
                    ) AS grade,
                    (SELECT $grade_avg_percent
                       FROM {grade_items} gi, {grade_grades} g
                      WHERE gi.hidden <> 1 AND
                            gi.itemtype = 'course' AND g.itemid = gi.id AND g.finalgrade IS NOT NULL AND
                            gi.courseid = c.id AND g.timemodified BETWEEN :timestart2 AND :timefinish2
                    ) AS average,
                    (SELECT $grade_single
                       FROM {grade_items} gi, {grade_grades} g
                      WHERE gi.hidden <> 1 AND
                            gi.itemtype = 'course' AND g.itemid = gi.id AND g.finalgrade IS NOT NULL AND gi.courseid = c.id AND
                            g.userid = :userid4 AND g.timemodified BETWEEN :timestart3 AND :timefinish3
                    ) AS grade_real,
                    (SELECT $grade_avg
                       FROM {grade_items} gi, {grade_grades} g
                      WHERE gi.hidden <> 1 AND
                            gi.itemtype = 'course' AND g.itemid = gi.id AND g.finalgrade IS NOT NULL AND
                            gi.courseid = c.id AND g.timemodified BETWEEN :timestart4 AND :timefinish4
                    ) AS average_real,
                    (SELECT SUM(lol.timespend)
                       FROM {local_intelliboard_tracking} lit
                       JOIN {local_intelliboard_logs} lol ON lol.trackid = lit.id AND lol.timepoint BETWEEN :timestart5 AND :timefinish5
                      WHERE lit.userid = :userid1 AND lit.courseid = c.id
                   GROUP BY lit.courseid
                    ) AS duration
               FROM {user_enrolments} ue, {enrol} e, {course} c
              WHERE e.id = ue.enrolid AND c.id = e.courseid AND ue.userid = :userid2 AND ue.status = 0 $sql
           GROUP BY c.id
           ORDER BY c.sortorder ASC",
            $params
        );
    }else{
        $params = array_merge([
            'timestart1' => $timestart,
            'timefinish1' => $timefinish,
            'timestart2' => $timestart,
            'timefinish2' => $timefinish,
            'timestart3' => $timestart,
            'timefinish3' => $timefinish,
        ], $params);
        $data = $DB->get_records_sql(
            "SELECT c.id, c.fullname, '0' AS duration_calc,
                    (SELECT $grade_single
                       FROM {grade_items} gi, {grade_grades} g
                      WHERE gi.hidden <> 1 AND
                            gi.itemtype = 'course' AND g.itemid = gi.id AND g.finalgrade IS NOT NULL AND
                            gi.courseid = c.id AND g.userid = :userid3 AND g.timemodified BETWEEN :timestart1 AND :timefinish1
                    ) AS grade,
                    (SELECT $grade_avg
                       FROM {grade_items} gi, {grade_grades} g
                      WHERE gi.hidden <> 1 AND
                            gi.itemtype = 'course' AND g.itemid = gi.id AND g.finalgrade IS NOT NULL AND
                            gi.courseid = c.id AND g.timemodified BETWEEN :timestart2 AND :timefinish2
                    ) AS average,
                    (SELECT SUM(lol.timespend)
                       FROM {local_intelliboard_tracking} lit
                       JOIN {local_intelliboard_logs} lol ON lol.trackid = lit.id AND lol.timepoint BETWEEN :timestart3 AND :timefinish3
                      WHERE lit.userid = :userid1 AND lit.courseid = c.id
                    ) AS duration
               FROM {user_enrolments} ue, {enrol} e, {course} c
              WHERE e.id = ue.enrolid AND c.id = e.courseid AND ue.userid = :userid2 AND ue.status = 0 $sql
           GROUP BY c.id
           ORDER BY c.sortorder ASC",
            $params
        );
    }

    $d = 0;
    foreach($data as $c){
        $d = ($c->duration > $d)?$c->duration:$d;
    }
    if($d){
        foreach($data as $c){
            $c->duration_calc =  (intval($c->duration)/$d)*100;
        }
    }
    return $data;
}

function intelliboard_learner_totals($userid){
    global $DB, $CFG;

    $params = array();
    $params['userid1'] = $userid;
    $params['userid2'] = $userid;
    $params['userid3'] = $userid;
    $params['userid4'] = $userid;
    $params['userid5'] = $userid;
    $params['userid6'] = $userid;
    $params['userid7'] = $userid;
    $params['userid8'] = $userid;
    $params['userid9'] = $userid;
    $params['userid10'] = $userid;

    $grade_avg = intelliboard_grade_sql(true);

    $data = $DB->get_record_sql("SELECT
                                    (SELECT count(distinct e.courseid) FROM {user_enrolments} ue, {enrol} e WHERE e.status = 0 AND ue.status = 0 AND ue.userid = :userid1 AND e.id = ue.enrolid) AS enrolled,
                                    (SELECT count(id) FROM {message} WHERE useridto = :userid2) AS messages,
                                    (SELECT count(distinct cc.course)
                                       FROM {user_enrolments} ue
                                       JOIN {enrol} e ON e.id = ue.enrolid
                                       JOIN {course_completions} cc ON cc.userid = ue.userid AND cc.course = e.courseid
                                      WHERE ue.userid = :userid3 AND cc.timecompleted IS NOT NULL AND ue.status = 0
                                    ) AS completed,
                                    (SELECT count(distinct e.courseid) 
                                       FROM {user_enrolments} ue
                                       JOIN {enrol} e ON e.id = ue.enrolid
                                       JOIN {course_completions} cc ON cc.course = e.courseid AND cc.userid = ue.userid
                                      WHERE ue.status = 0 AND ue.userid = :userid4 AND cc.timecompleted IS NULL
                                    ) as inprogress,
                                    (SELECT $grade_avg FROM {grade_items} gi, {grade_grades} g WHERE gi.hidden <> 1 AND gi.itemtype = 'course' AND g.userid <> :userid8 AND g.finalgrade IS NOT NULL AND gi.id = g.itemid AND gi.courseid IN (
                                        SELECT e.courseid FROM {user_enrolments} ue, {enrol} e WHERE e.status = 0 AND ue.status = 0 AND ue.userid = :userid9 AND e.id = ue.enrolid)) as average,
                                    (SELECT $grade_avg FROM {grade_items} gi, {grade_grades} g WHERE gi.hidden <> 1 AND gi.itemtype = 'course' AND g.userid = :userid10 AND g.finalgrade IS NOT NULL AND gi.id = g.itemid) as grade", $params);

    if(get_config('local_intelliboard', 't08')){
        if ($CFG->dbtype == 'pgsql') {
            $group_concat = "string_agg(g.feedback, '; ')";
            $substring_index = "split_part(scale,',', cast(ROUND(AVG(g.finalgrade)) as int))";
            $case_round = "cast(ROUND(SUM(g.finalgrade), 0) as text)";
            $cast_as_int_start = 'cast(';
            $cast_as_int_end = ' as int)';
        } else {
            $group_concat = "GROUP_CONCAT(DISTINCT g.feedback ORDER BY g.feedback ASC SEPARATOR '; ')";
            $substring_index = "SUBSTRING_INDEX(SUBSTRING_INDEX(scale, ',', ROUND(AVG(g.finalgrade))), ',', -1)";
            $case_round = "ROUND(SUM(g.finalgrade), 0)";
            $cast_as_int_start = '';
            $cast_as_int_end = '';
        }
        $sum_courses = get_user_preferences('enabeled_sum_courses_'.$userid, '');
        $where = '';
        if(!empty($sum_courses)){
            $where = " AND c.id IN($sum_courses)";
        }
        $where .= (get_config('local_intelliboard', 'student_course_visibility')) ? "" : " AND c.visible = 1";
        if (get_config('local_intelliboard', 'coursecontainer_available') && get_config('local_intelliboard', 'coursecontainer_filter')) {
            $where .=  " AND c.containertype = 'container_course'";
        }
        $sum_grade = $DB->get_record_sql("SELECT
                          (CASE WHEN (AVG({$cast_as_int_start}(SELECT value FROM {grade_settings} WHERE courseid=gi.courseid AND name='displaytype'){$cast_as_int_end})=MIN({$cast_as_int_start}(SELECT value FROM {grade_settings} WHERE courseid=gi.courseid AND name='displaytype'){$cast_as_int_end}))
                                     OR (AVG({$cast_as_int_start}(SELECT value FROM {grade_settings} WHERE courseid=gi.courseid AND name='displaytype'){$cast_as_int_end}) IS NULL AND MIN({$cast_as_int_start}(SELECT value FROM {grade_settings} WHERE courseid=gi.courseid AND name='displaytype'){$cast_as_int_end}) IS NULL)
                            THEN (CASE MIN({$cast_as_int_start}(SELECT value FROM {grade_settings} WHERE courseid=gi.courseid AND name='displaytype'){$cast_as_int_end})
                                  WHEN 1 THEN {$case_round}
                                  WHEN 12 THEN {$case_round}
                                  WHEN 13 THEN {$case_round}
                                  WHEN 2 THEN CONCAT(ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0),'%')
                                  WHEN 21 THEN CONCAT(ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0),
                                                      '% (',CASE MIN(gi.gradetype)
                                                            WHEN 1 THEN {$case_round}
                                                            WHEN 2 THEN (SELECT
																		{$substring_index}
                                                                         FROM {scale} s WHERE s.id=MIN(gi.scaleid))
                                                            WHEN 3 THEN {$group_concat}
                                                            END,')')
                                  WHEN 23 THEN CONCAT(ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0),
                                                      '% (',CASE WHEN (SELECT gl.letter
                                                                       FROM {grade_letters} gl, {context} ctx
                                                                                                WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN(gi.courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0)
                                                                       ORDER BY gl.lowerboundary
                                                                       LIMIT 1) IS NOT NULL
                                                            THEN (SELECT gl.letter
                                                                  FROM {grade_letters} gl, {context} ctx
                                                                                           WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN(gi.courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0)
                                                                  ORDER BY gl.lowerboundary
                                                                  LIMIT 1)
                                                            ELSE
                                                              CASE
                                                              WHEN ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 93 THEN 'A'
                                                              WHEN ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 90 THEN 'A-'
                                                              WHEN ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 87 THEN 'B+'
                                                              WHEN ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 83 THEN 'B'
                                                              WHEN ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 80 THEN 'B-'
                                                              WHEN ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 77 THEN 'C+'
                                                              WHEN ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 73 THEN 'C'
                                                              WHEN ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 70 THEN 'C-'
                                                              WHEN ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 67 THEN 'D+'
                                                              WHEN ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 60 THEN 'D'
                                                              WHEN ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 0 THEN 'F'
                                                              ELSE ''
                                                              END
                                                            END,')')
                                  WHEN 3 THEN CASE WHEN (SELECT gl.letter
                                                         FROM {grade_letters} gl, {context} ctx
                                                                                  WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN(gi.courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0)
                                                         ORDER BY gl.lowerboundary
                                                         LIMIT 1) IS NOT NULL
                                              THEN (SELECT gl.letter
                                                    FROM {grade_letters} gl, {context} ctx
                                                                             WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN(gi.courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0)
                                                    ORDER BY gl.lowerboundary
                                                    LIMIT 1)
                                              ELSE
                                                CASE
                                                WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 93 THEN 'A'
                                                WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 90 THEN 'A-'
                                                WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 87 THEN 'B+'
                                                WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 83 THEN 'B'
                                                WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 80 THEN 'B-'
                                                WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 77 THEN 'C+'
                                                WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 73 THEN 'C'
                                                WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 70 THEN 'C-'
                                                WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 67 THEN 'D+'
                                                WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 60 THEN 'D'
                                                WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 0 THEN 'F'
                                                ELSE ''
                                                END
                                              END
                                  WHEN 31 THEN CONCAT(CASE WHEN (SELECT gl.letter
                                                                 FROM {grade_letters} gl, {context} ctx
                                                                                          WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN(gi.courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0)
                                                                 ORDER BY gl.lowerboundary
                                                                 LIMIT 1) IS NOT NULL
                                                      THEN (SELECT gl.letter
                                                            FROM {grade_letters} gl, {context} ctx
                                                                                     WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN(gi.courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0)
                                                            ORDER BY gl.lowerboundary
                                                            LIMIT 1)
                                                      ELSE
                                                        CASE
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 93 THEN 'A'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 90 THEN 'A-'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 87 THEN 'B+'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 83 THEN 'B'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 80 THEN 'B-'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 77 THEN 'C+'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 73 THEN 'C'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 70 THEN 'C-'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 67 THEN 'D+'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 60 THEN 'D'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 0 THEN 'F'
                                                        ELSE ''
                                                        END
                                                      END ,' (', CASE MIN(gi.gradetype)
                                                                 WHEN 1 THEN {$case_round}
                                                                 WHEN 2 THEN (SELECT
																				{$substring_index}
                                                                              FROM {scale} s WHERE s.id=MIN(gi.scaleid))
                                                                 WHEN 3 THEN {$group_concat}
                                                                 END, ')')
                                  WHEN 32 THEN CONCAT(CASE WHEN (SELECT gl.letter
                                                                 FROM {grade_letters} gl, {context} ctx
                                                                                          WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN(gi.courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0)
                                                                 ORDER BY gl.lowerboundary
                                                                 LIMIT 1) IS NOT NULL
                                                      THEN (SELECT gl.letter
                                                            FROM {grade_letters} gl, {context} ctx
                                                                                     WHERE ctx.contextlevel=50 AND ctx.instanceid=MIN(gi.courseid) AND gl.contextid=ctx.id AND gl.lowerboundary>=ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0)
                                                            ORDER BY gl.lowerboundary
                                                            LIMIT 1)
                                                      ELSE
                                                        CASE
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 93 THEN 'A'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 90 THEN 'A-'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 87 THEN 'B+'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 83 THEN 'B'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 80 THEN 'B-'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 77 THEN 'C+'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 73 THEN 'C'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 70 THEN 'C-'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 67 THEN 'D+'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 60 THEN 'D'
                                                        WHEN ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0) >= 0 THEN 'F'
                                                        ELSE ''
                                                        END
                                                      END ,' (', ROUND(MIN(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0), '%)')
                                  ELSE CONCAT(ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0),'%')
                                  END)
                           ELSE CONCAT(ROUND(AVG(CASE WHEN (g.rawgrademax-g.rawgrademin) > 0 THEN ((g.finalgrade-g.rawgrademin)/(g.rawgrademax-g.rawgrademin))*100 ELSE g.finalgrade END), 0),'%')
                           END ) AS grade

                        FROM {user_enrolments} ue
                          JOIN {enrol} e ON e.id = ue.enrolid
                          JOIN {course} c ON c.id = e.courseid
                          JOIN {grade_items} gi ON gi.courseid=c.id AND gi.itemtype='course' AND gi.hidden <> 1
                          JOIN {grade_grades} g ON g.itemid=gi.id AND g.userid=ue.userid
                        WHERE ue.status = 0 AND ue.userid = :userid1 $where", $params);

        $data->sum_grade = (!empty($sum_grade->grade))?$sum_grade->grade:'-';
    }

    return $data;
}
function intelliboard_learner_course($userid, $courseid){
    global $DB;

    $params = array();
    $params['userid1'] = $userid;
    $params['userid2'] = $userid;
    $params['userid3'] = $userid;
    $params['courseid'] = $courseid;

    $grade_single = intelliboard_grade_sql();

    return $DB->get_record_sql("SELECT c.id, c.fullname, ul.timeaccess, c.enablecompletion, cc.timecompleted, $grade_single AS grade
                                FROM {course} c
                                  LEFT JOIN {user_lastaccess} ul ON ul.courseid = c.id AND ul.userid = :userid1
                                  LEFT JOIN {course_completions} cc ON cc.course = c.id AND cc.userid = :userid2
                                  LEFT JOIN {grade_items} gi ON gi.courseid = c.id AND gi.itemtype = 'course' AND gi.hidden <> 1
                                  LEFT JOIN {grade_grades} g ON g.itemid = gi.id AND g.userid = :userid3
                                WHERE c.id = :courseid ORDER BY c.sortorder ASC", $params);
}
function intelliboard_learner_modules($userid){
    global $DB;

    $params = array();
    $params['userid1'] = $userid;
    $params['userid2'] = $userid;
    $params['userid3'] = $userid;
    $completion = intelliboard_compl_sql("cmc.");

    return $DB->get_records_sql("SELECT m.id, m.name, count(distinct cm.id) as modules, count(distinct cmc.id) as completed_modules, count(distinct l.id) as start_modules, sum(l.timespend) as duration
                                  FROM {modules} m, {course_modules} cm
                                    LEFT JOIN {course_modules_completion} cmc ON cmc.coursemoduleid = cm.id AND cmc.userid = :userid1 $completion
                                    LEFT JOIN {local_intelliboard_tracking} l ON l.page = 'module' AND l.userid = :userid2 AND l.param = cm.id
                                  WHERE cm.instance > 0 AND cm.visible = 1 AND cm.module = m.id and cm.course IN (
                                    SELECT distinct e.courseid FROM {enrol} e, {user_enrolments} ue WHERE ue.userid = :userid3 AND e.id = ue.enrolid AND ue.status = 0) GROUP BY m.id", $params);
}
function intelliboard_learner_access()
{
    if(!get_config('local_intelliboard', 't1')){
        throw new moodle_exception('invalidaccessparameter', 'error');
    }
    $access = check_intelliboard_learner_access();
    if (!$access) {
        throw new moodle_exception('invalidaccess', 'error');
    }
}
function check_intelliboard_learner_access()
{
    global $USER;

    $learner_menu = get_config('local_intelliboard', 'learner_menu');
    $access = false;
    $learner_roles = get_config('local_intelliboard', 'filter11');
    if (!empty($learner_roles) && $learner_menu) {
        $roles = explode(',', $learner_roles);
        if (!empty($roles)) {
            foreach ($roles as $role) {
                if ($role and user_has_role_assignment($USER->id, $role)){
                    $access = true;
                    break;
                }
            }
        }
    } elseif (has_capability('local/intelliboard:students', context_system::instance())) {
        $access = true;
    }

    return $access;
}
