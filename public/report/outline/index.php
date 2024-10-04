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
 * Display user activity reports for a course (totals)
 *
 * @package    report
 * @subpackage outline
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\report_helper;

require('../../config.php');
require_once($CFG->dirroot.'/report/outline/locallib.php');

$id = required_param('id',PARAM_INT);       // course id
$startdate = optional_param('startdate', null, PARAM_INT);
$enddate = optional_param('enddate', null, PARAM_INT);

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

$pageparams = array('id' => $id);
if ($startdate) {
    $pageparams['startdate'] = $startdate;
}
if ($enddate) {
    $pageparams['enddate'] = $enddate;
}

$PAGE->set_url('/report/outline/index.php', $pageparams);
$PAGE->set_pagelayout('report');

require_login($course);
$context = context_course::instance($course->id);
require_capability('report/outline:view', $context);

// Handle form to filter access logs by date.
$filterform = new \report_outline\filter_form();
$filterform->set_data(['id' => $course->id, 'filterstartdate' => $startdate, 'filterenddate' => $enddate]);
if ($filterform->is_cancelled()) {
    $redir = $PAGE->url;
    $redir->remove_params(['startdate', 'enddate']);
    redirect($redir);
}
if ($filter = $filterform->get_data()) {
    $redir = $PAGE->url;
    if ($filter->filterstartdate) {
        $redir->param('startdate', $filter->filterstartdate);
    }
    if ($filter->filterenddate) {
        $redir->param('enddate', $filter->filterenddate);
    }
    redirect($redir);
}

// Trigger an activity report viewed event.
$event = \report_outline\event\activity_report_viewed::create(array('context' => $context));
$event->trigger();

$showlastaccess = true;
$showblogs = !empty($CFG->enableblogs) && $CFG->useblogassociations;
$hiddenfields = explode(',', $CFG->hiddenuserfields);

if (array_search('lastaccess', $hiddenfields) !== false and !has_capability('moodle/user:viewhiddendetails', $context)) {
    $showlastaccess = false;
}

$stractivityreport = get_string('pluginname', 'report_outline');
$PAGE->set_title($course->shortname .': '. $stractivityreport);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

// Print selector drop down.
$pluginname = get_string('pluginname', 'report_outline');
report_helper::print_report_selector($pluginname);

list($uselegacyreader, $useinternalreader, $minloginternalreader, $logtable) = report_outline_get_common_log_variables();

// If no legacy and no internal log then don't proceed.
if (!$uselegacyreader && !$useinternalreader) {
    echo $OUTPUT->box_start('generalbox', 'notice');
    echo $OUTPUT->notification(get_string('nologreaderenabled', 'report_outline'));
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    die();
}

// We want to display the time we are beginning to get logs from in the heading.
// If we are using the legacy reader check the minimum time in that log table.
if ($uselegacyreader) {
    $minlog = $DB->get_field_sql('SELECT min(time) FROM {log}');
}

// If we are using the internal reader check the minimum time in that table.
if ($useinternalreader) {
    // If new log table has older data then don't use the minimum time obtained from the legacy table.
    if (empty($minlog) || ($minloginternalreader <= $minlog)) {
        $minlog = $minloginternalreader;
    }
}

$filterform->display();

$modinfo = get_fast_modinfo($course);

// If using legacy log then get users from old table.
if ($uselegacyreader) {
    // If we are going to use the internal (not legacy) log table, we should only get records
    // from the legacy table that exist before we started adding logs to the new table.
    $params = array('courseid' => $course->id, 'action' => 'view%', 'visible' => 1);
    $limittime = '';
    if (!empty($minloginternalreader)) {
        $limittime = ' AND time < :timeto ';
        $params['timeto'] = $minloginternalreader;
    }
    if ($startdate) {
        $limittime .= ' AND time >= :startdate ';
        $params['startdate'] = $startdate;
    }
    if ($enddate) {
        $limittime .= ' AND time < :enddate ';
        $params['enddate'] = $enddate;
    }
    // Check if we need to show the last access.
    $sqllasttime = '';
    if ($showlastaccess) {
        $sqllasttime = ", MAX(time) AS lasttime";
    }
    $logactionlike = $DB->sql_like('l.action', ':action');
    $sql = "SELECT cm.id, COUNT('x') AS numviews, COUNT(DISTINCT userid) AS distinctusers $sqllasttime
              FROM {course_modules} cm
              JOIN {modules} m
                ON m.id = cm.module
              JOIN {log} l
                ON l.cmid = cm.id
             WHERE cm.course = :courseid
               AND $logactionlike
               AND m.visible = :visible $limittime
          GROUP BY cm.id";
    $views = $DB->get_records_sql($sql, $params);
}

// Get record from sql_internal_table_reader and merge with records obtained from legacy log (if needed).
if ($useinternalreader) {
    // Check if we need to show the last access.
    $sqllasttime = '';
    if ($showlastaccess) {
        $sqllasttime = ", MAX(timecreated) AS lasttime";
    }
    $params = array('courseid' => $course->id, 'contextmodule' => CONTEXT_MODULE);
    $limittime = '';
    if ($startdate) {
        $limittime .= ' AND timecreated >= :startdate ';
        $params['startdate'] = $startdate;
    }
    if ($enddate) {
        $limittime .= ' AND timecreated < :enddate ';
        $params['enddate'] = $enddate;
    }
    $sql = "SELECT contextinstanceid as cmid, COUNT('x') AS numviews, COUNT(DISTINCT userid) AS distinctusers $sqllasttime
              FROM {" . $logtable . "} l
             WHERE courseid = :courseid
               AND anonymous = 0
               AND crud = 'r'
               AND contextlevel = :contextmodule
               $limittime
          GROUP BY contextinstanceid";
    $v = $DB->get_records_sql($sql, $params);

    if (empty($views)) {
        $views = $v;
    } else {
        // Merge two view arrays.
        foreach ($v as $key => $value) {
            if (isset($views[$key]) && !empty($views[$key]->numviews)) {
                $views[$key]->numviews += $value->numviews;
                if ($value->lasttime > $views[$key]->lasttime) {
                    $views[$key]->lasttime = $value->lasttime;
                }
            } else {
                $views[$key] = $value;
            }
        }
    }
}

$activitieslist = new report_outline\output\activitieslist($modinfo, $views, $showlastaccess, $minlog, $showblogs);
echo $OUTPUT->render_from_template('report_outline/report', $activitieslist->export_for_template($OUTPUT));
echo $OUTPUT->footer();



