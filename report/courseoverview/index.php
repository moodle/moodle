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
 * Course overview report
 *
 * @package    report
 * @subpackage courseoverview
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/lib/statslib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/report/courseoverview/locallib.php');

$report     = optional_param('report', STATS_REPORT_ACTIVE_COURSES, PARAM_INT);
$time       = optional_param('time', 0, PARAM_INT);
$numcourses = optional_param('numcourses', 20, PARAM_INT);
$systemcontext = context_system::instance();

if (empty($CFG->enablestats)) {
    if (has_capability('moodle/site:config', $systemcontext)) {
        redirect("$CFG->wwwroot/$CFG->admin/search.php?query=enablestats", get_string('mustenablestats', 'admin'), 3);
    } else {
        throw new \moodle_exception('statsdisable');
    }
}

admin_externalpage_setup('reportcourseoverview', '', null, '', array('pagelayout'=>'report'));
echo $OUTPUT->header();

$course = get_site();
stats_check_uptodate($course->id);

$strreports = get_string('reports');
$strcourseoverview = get_string('courseoverview');

$reportoptions = stats_get_report_options($course->id,STATS_MODE_RANKED);

$earliestday = $DB->get_field_sql('SELECT MIN(timeend) FROM {stats_daily}');
$earliestweek = $DB->get_field_sql('SELECT MIN(timeend) FROM {stats_weekly}');
$earliestmonth = $DB->get_field_sql('SELECT MIN(timeend) FROM {stats_monthly}');

if (empty($earliestday)) $earliestday = time();
if (empty($earliestweek)) $earliestweek = time();
if (empty($earliestmonth)) $earliestmonth = time();

$now = stats_get_base_daily();
$lastweekend = stats_get_base_weekly();
$lastmonthend = stats_get_base_monthly();

$timeoptions = stats_get_time_options($now,$lastweekend,$lastmonthend,$earliestday,$earliestweek,$earliestmonth);

if (empty($timeoptions)) {
    throw new \moodle_exception('nostatstodisplay', 'error', $CFG->wwwroot.'/course/view.php?id='.$course->id);
}

echo html_writer::start_tag('form', array('action' => 'index.php', 'method' => 'post', 'class' => 'form-inline'));
echo html_writer::start_tag('div');

$table = new html_table();
$table->width = '*';
$table->align = array('left','left','left','left','left','left');

$reporttypemenu = html_writer::label(get_string('statsreporttype'), 'menureport', false, array('class' => 'accesshide'));
$reporttypemenu .= html_writer::select($reportoptions,'report',$report, false);
$timeoptionsmenu = html_writer::label(get_string('time'), 'menutime', false, array('class' => 'accesshide'));
$timeoptionsmenu .= html_writer::select($timeoptions,'time',$time, false);

$table->data[] = array(get_string('statsreporttype'),$reporttypemenu,
                       get_string('statstimeperiod'),$timeoptionsmenu,
                       html_writer::label(get_string('numberofcourses'), 'numcourses', false, array('class' => 'accesshide')) .
                       html_writer::empty_tag('input', array('type' => 'text', 'class' => 'form-control',
                           'id' => 'numcourses', 'name' => 'numcourses', 'size' => '3', 'maxlength' => '2',
                           'value' => $numcourses)),
                       html_writer::empty_tag('input', array('type' => 'submit', 'class' => 'btn btn-secondary',
                           'value' => get_string('view'))));

echo html_writer::table($table);
echo html_writer::end_tag('div');
echo html_writer::end_tag('form');

echo $OUTPUT->heading($reportoptions[$report]);


if (!empty($report) && !empty($time)) {
    $param = stats_get_parameters($time,$report,SITEID,STATS_MODE_RANKED);
    if (!empty($param->sql)) {
        $sql = $param->sql;
    } else {
        $sql = "SELECT courseid,".$param->fields."
                  FROM {".'stats_'.$param->table."}
                 WHERE timeend >= $param->timeafter AND stattype = 'activity' AND roleid = 0
              GROUP BY courseid
                       $param->extras
              ORDER BY $param->orderby";
    }

    $courses = $DB->get_records_sql($sql, $param->params, 0, $numcourses);

    if (empty($courses)) {
        echo $OUTPUT->notification(get_string('statsnodata'));
        echo '</td></tr></table>';

    } else {
        require_capability('report/courseoverview:view', $systemcontext);
        echo html_writer::start_div();
        report_courseoverview_print_chart($report, $time, $numcourses);
        echo html_writer::end_div();

        $table = new html_table();
        $table->align = array('left','center','center','center');
        $table->head = array(get_string('course'),$param->line1);
        if (!empty($param->line2)) {
            $table->head[] = $param->line2;
        }
        if (!empty($param->line3)) {
            $table->head[] = $param->line3;
        }

        foreach  ($courses as $c) {
            $a = array();
            $a[] = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$c->courseid.'">'.$DB->get_field('course', 'shortname', array('id'=>$c->courseid)).'</a>';

            $a[] = $c->line1;
            if (isset($c->line2)) {
                $a[] = $c->line2;
            }
            if (isset($c->line3)) {
                $a[] = round($c->line3,2);
            }
            $table->data[] = $a;
        }
        echo html_writer::table($table);
    }
}
echo $OUTPUT->footer();
