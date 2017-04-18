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
 * Reports implementation
 *
 * @package    report
 * @subpackage stats
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once(dirname(__FILE__).'/lib.php');
require_once($CFG->dirroot.'/lib/statslib.php');

function report_stats_mode_menu($course, $mode, $time, $url) {
    global $CFG, $OUTPUT;
    /*
    $reportoptions = stats_get_report_options($course->id, $mode);
    $timeoptions = report_stats_timeoptions($mode);
    if (empty($timeoptions)) {
        print_error('nostatstodisplay', '', $CFG->wwwroot.'/course/view.php?id='.$course->id);
    }
    */

    $options = array();
    $options[STATS_MODE_GENERAL] = get_string('statsmodegeneral');
    $options[STATS_MODE_DETAILED] = get_string('statsmodedetailed');
    if (has_capability('report/stats:view', context_system::instance())) {
        $options[STATS_MODE_RANKED] = get_string('reports');
    }
    $popupurl = $url."?course=$course->id&time=$time";
    $select = new single_select(new moodle_url($popupurl), 'mode', $options, $mode, null);
    $select->set_label(get_string('reports'), array('class' => 'accesshide'));
    $select->formid = 'switchmode';
    return $OUTPUT->render($select);
}

function report_stats_timeoptions($mode) {
    global $CFG, $DB;

    if ($mode == STATS_MODE_DETAILED) {
        $earliestday = $DB->get_field_sql('SELECT MIN(timeend) FROM {stats_user_daily}');
        $earliestweek = $DB->get_field_sql('SELECT MIN(timeend) FROM {stats_user_weekly}');
        $earliestmonth = $DB->get_field_sql('SELECT MIN(timeend) FROM {stats_user_monthly}');
    } else {
        $earliestday = $DB->get_field_sql('SELECT MIN(timeend) FROM {stats_daily}');
        $earliestweek = $DB->get_field_sql('SELECT MIN(timeend) FROM {stats_weekly}');
        $earliestmonth = $DB->get_field_sql('SELECT MIN(timeend) FROM {stats_monthly}');
    }


    if (empty($earliestday)) $earliestday = time();
    if (empty($earliestweek)) $earliestweek = time();
    if (empty($earliestmonth)) $earliestmonth = time();

    $now = stats_get_base_daily();
    $lastweekend = stats_get_base_weekly();
    $lastmonthend = stats_get_base_monthly();

    return stats_get_time_options($now,$lastweekend,$lastmonthend,$earliestday,$earliestweek,$earliestmonth);
}

function report_stats_report($course, $report, $mode, $user, $roleid, $time) {
    global $CFG, $DB, $OUTPUT;

    if ($user) {
        $userid = $user->id;
    } else {
        $userid = 0;
    }

    $fields = 'c.id,c.shortname,c.visible';
    $ccselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
    $ccjoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)";
    $sortstatement = 'ORDER BY c.shortname';

    $sql = "SELECT $fields $ccselect FROM {course} c $ccjoin $sortstatement";

    $params = array();
    $params['contextlevel'] = CONTEXT_COURSE;

    $courses = $DB->get_recordset_sql($sql, $params);

    $courseoptions = array();

    foreach ($courses as $c) {
        context_helper::preload_from_record($c);
        $context = context_course::instance($c->id);

        if (has_capability('report/stats:view', $context)) {
            if (isset($c->visible) && $c->visible <= 0) {
                // For hidden courses, require visibility check.
                if (!has_capability('moodle/course:viewhiddencourses', $context)) {
                    continue;
                }
            }
            $courseoptions[$c->id] = format_string($c->shortname, true, array('context' => $context));
        }
    }

    $courses->close();

    $reportoptions = stats_get_report_options($course->id, $mode);
    $timeoptions = report_stats_timeoptions($mode);
    if (empty($timeoptions)) {
        print_error('nostatstodisplay', '', $CFG->wwwroot.'/course/view.php?id='.$course->id);
    }

    $users = array();
    $table = new html_table();
    $table->width = 'auto';

    if ($mode == STATS_MODE_DETAILED) {
        $param = stats_get_parameters($time, null, $course->id, $mode); // we only care about the table and the time string (if we have time)

        list($sort, $moreparams) = users_order_by_sql('u');
        $moreparams['courseid'] = $course->id;
        $fields = user_picture::fields('u', array('idnumber'));
        $sql = "SELECT DISTINCT $fields
                  FROM {stats_user_{$param->table}} s
                  JOIN {user} u ON u.id = s.userid
                 WHERE courseid = :courseid";
        if (!empty($param->stattype)) {
            $sql .= " AND stattype = :stattype";
            $moreparams['stattype'] = $param->stattype;
        }
        if (!empty($time)) {
            $sql .= " AND timeend >= :timeafter";
            $moreparams['timeafter'] = $param->timeafter;
        }
        $sql .= " ORDER BY {$sort}";

        if (!$us = $DB->get_records_sql($sql, array_merge($param->params, $moreparams))) {
            print_error('nousers');
        }
        foreach ($us as $u) {
            $users[$u->id] = fullname($u, true);
        }

        $table->align = array('left','left','left','left','left','left','left','left');
        $table->data[] = array(html_writer::label(get_string('course'), 'menucourse'), html_writer::select($courseoptions, 'course', $course->id, false),
                               html_writer::label(get_string('users'), 'menuuserid'), html_writer::select($users, 'userid', $userid, false),
                               html_writer::label(get_string('statsreporttype'), 'menureport'), html_writer::select($reportoptions, 'report', ($report == 5) ? $report.$roleid : $report, false),
                               html_writer::label(get_string('statstimeperiod'), 'menutime'), html_writer::select($timeoptions, 'time', $time, false),
                               '<input type="submit" value="'.get_string('view').'" />') ;
    } else if ($mode == STATS_MODE_RANKED) {
        $table->align = array('left','left','left','left','left','left');
        $table->data[] = array(html_writer::label(get_string('statsreporttype'), 'menureport'), html_writer::select($reportoptions, 'report', ($report == 5) ? $report.$roleid : $report, false),
                               html_writer::label(get_string('statstimeperiod'), 'menutime'), html_writer::select($timeoptions, 'time', $time, false),
                               '<input type="submit" value="'.get_string('view').'" />') ;
    } else if ($mode == STATS_MODE_GENERAL) {
        $table->align = array('left','left','left','left','left','left','left');
        $table->data[] = array(html_writer::label(get_string('course'), 'menucourse'), html_writer::select($courseoptions, 'course', $course->id, false),
                               html_writer::label(get_string('statsreporttype'), 'menureport'), html_writer::select($reportoptions, 'report', ($report == 5) ? $report.$roleid : $report, false),
                               html_writer::label(get_string('statstimeperiod'), 'menutime'), html_writer::select($timeoptions, 'time', $time, false),
                               '<input type="submit" value="'.get_string('view').'" />') ;
    }

    echo '<form action="index.php" method="post">'."\n"
        .'<div>'."\n"
        .'<input type="hidden" name="mode" value="'.$mode.'" />'."\n";

    echo html_writer::table($table);

    echo '</div>';
    echo '</form>';

    // Display the report if:
    //  - A report has been selected.
    //  - A time frame has been provided
    //  - If the mode is not detailed OR a valid user has been selected.
    if (!empty($report) && !empty($time) && ($mode !== STATS_MODE_DETAILED || !empty($userid))) {
        if ($report == STATS_REPORT_LOGINS && $course->id != SITEID) {
            print_error('reportnotavailable');
        }

        $param = stats_get_parameters($time,$report,$course->id,$mode);

        if ($mode == STATS_MODE_DETAILED) {
            $param->table = 'user_'.$param->table;
        }

        if (!empty($param->sql)) {
            $sql = $param->sql;
        } else {
            //TODO: lceanup this ugly mess
            $sql = 'SELECT '.((empty($param->fieldscomplete)) ? 'id,roleid,timeend,' : '').$param->fields
                .' FROM {stats_'.$param->table.'} WHERE '
                .(($course->id == SITEID) ? '' : ' courseid = '.$course->id.' AND ')
                .((!empty($userid)) ? ' userid = '.$userid.' AND ' : '')
                .((!empty($roleid)) ? ' roleid = '.$roleid.' AND ' : '')
                . ((!empty($param->stattype)) ? ' stattype = \''.$param->stattype.'\' AND ' : '')
                .' timeend >= '.$param->timeafter
                .' '.$param->extras
                .' ORDER BY timeend DESC';
        }

        $stats = $DB->get_records_sql($sql);

        if (empty($stats)) {
            echo $OUTPUT->notification(get_string('statsnodata'));

        } else {

            $stats = stats_fix_zeros($stats,$param->timeafter,$param->table,(!empty($param->line2)));

            echo $OUTPUT->heading(format_string($course->shortname).' - '.get_string('statsreport'.$report)
                    .((!empty($user)) ? ' '.get_string('statsreportforuser').' ' .fullname($user,true) : '')
                    .((!empty($roleid)) ? ' '.$DB->get_field('role','name', array('id'=>$roleid)) : ''));


            if ($mode == STATS_MODE_DETAILED) {
                echo '<div class="graph"><img src="'.$CFG->wwwroot.'/report/stats/graph.php?mode='.$mode.'&amp;course='.$course->id.'&amp;time='.$time.'&amp;report='.$report.'&amp;userid='.$userid.'" alt="'.get_string('statisticsgraph').'" /></div>';
            } else {
                echo '<div class="graph"><img src="'.$CFG->wwwroot.'/report/stats/graph.php?mode='.$mode.'&amp;course='.$course->id.'&amp;time='.$time.'&amp;report='.$report.'&amp;roleid='.$roleid.'" alt="'.get_string('statisticsgraph').'" /></div>';
            }

            $table = new html_table();
            $table->align = array('left','center','center','center');
            $param->table = str_replace('user_','',$param->table);
            switch ($param->table) {
                case 'daily'  : $period = get_string('day'); break;
                case 'weekly' : $period = get_string('week'); break;
                case 'monthly': $period = get_string('month', 'form'); break;
                default : $period = '';
            }
            $table->head = array(get_string('periodending','moodle',$period));
            if (empty($param->crosstab)) {
                $table->head[] = $param->line1;
                if (!empty($param->line2)) {
                    $table->head[] = $param->line2;
                }
            }
            if (!file_exists($CFG->dirroot.'/report/log/index.php')) {
                // bad luck, we can not link other report
            } else if (empty($param->crosstab)) {
                foreach  ($stats as $stat) {
                    $a = array(userdate($stat->timeend-(60*60*24),get_string('strftimedate'),$CFG->timezone),$stat->line1);
                    if (isset($stat->line2)) {
                        $a[] = $stat->line2;
                    }
                    if (empty($CFG->loglifetime) || ($stat->timeend-(60*60*24)) >= (time()-60*60*24*$CFG->loglifetime)) {
                        if (has_capability('report/log:view', context_course::instance($course->id))) {
                            $a[] = '<a href="'.$CFG->wwwroot.'/report/log/index.php?id='.
                                $course->id.'&amp;chooselog=1&amp;showusers=1&amp;showcourses=1&amp;user='
                                .$userid.'&amp;date='.usergetmidnight($stat->timeend-(60*60*24)).'">'
                                .get_string('course').' ' .get_string('logs').'</a>&nbsp;';
                        } else {
                            $a[] = '';
                        }
                    }
                    $table->data[] = $a;
                }
            } else {
                $data = array();
                $roles = array();
                $times = array();
                $missedlines = array();
                $coursecontext = context_course::instance($course->id);
                $rolenames = role_fix_names(get_all_roles($coursecontext), $coursecontext, ROLENAME_ALIAS, true);
                foreach ($stats as $stat) {
                    if (!empty($stat->zerofixed)) {
                        $missedlines[] = $stat->timeend;
                    }
                    $data[$stat->timeend][$stat->roleid] = $stat->line1;
                    if ($stat->roleid != 0) {
                        if (!array_key_exists($stat->roleid,$roles)) {
                            $roles[$stat->roleid] = $rolenames[$stat->roleid];
                        }
                    } else {
                        if (!array_key_exists($stat->roleid,$roles)) {
                            $roles[$stat->roleid] = get_string('all');
                        }
                    }
                    if (!array_key_exists($stat->timeend,$times)) {
                        $times[$stat->timeend] = userdate($stat->timeend,get_string('strftimedate'),$CFG->timezone);
                    }
                }

                foreach ($data as $time => $rolesdata) {
                    if (in_array($time,$missedlines)) {
                        $rolesdata = array();
                        foreach ($roles as $roleid => $guff) {
                            $rolesdata[$roleid] = 0;
                        }
                    }
                    else {
                        foreach (array_keys($roles) as $r) {
                            if (!array_key_exists($r, $rolesdata)) {
                                $rolesdata[$r] = 0;
                            }
                        }
                    }
                    krsort($rolesdata);
                    $row = array_merge(array($times[$time]),$rolesdata);
                    if (empty($CFG->loglifetime) || ($stat->timeend-(60*60*24)) >= (time()-60*60*24*$CFG->loglifetime)) {
                        if (has_capability('report/log:view', context_course::instance($course->id))) {
                            $row[] = '<a href="'.$CFG->wwwroot.'/report/log/index.php?id='
                                .$course->id.'&amp;chooselog=1&amp;showusers=1&amp;showcourses=1&amp;user='.$userid
                                .'&amp;date='.usergetmidnight($time-(60*60*24)).'">'
                                .get_string('course').' ' .get_string('logs').'</a>&nbsp;';
                        } else {
                            $row[] = '';
                        }
                    }
                    $table->data[] = $row;
                }
                krsort($roles);
                $table->head = array_merge($table->head,$roles);
            }
            $table->head[] = get_string('logs');
            //if (!empty($lastrecord)) {
                //$lastrecord[] = $lastlink;
                //$table->data[] = $lastrecord;
            //}
            echo html_writer::table($table);
        }
    }
}
