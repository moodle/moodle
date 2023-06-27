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
 * Course summary view report.
 *
 * @package    report_trainingsessions
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/selector_form.php');
require_once($CFG->dirroot.'/report/trainingsessions/renderers/htmlrenderers.php');

// Parameters.
$selform = new SelectorForm($id, 'course');
if (!$data = $selform->get_data()) {
    $data = new StdClass;
    $data->from = optional_param('from', -1, PARAM_NUMBER);
    $data->to = optional_param('to', -1, PARAM_NUMBER);
    $data->userid = optional_param('userid', $USER->id, PARAM_INT);
    $data->fromstart = optional_param('fromstart', 0, PARAM_BOOL);
    $data->tonow = optional_param('tonow', 0, PARAM_BOOL);
    $data->output = optional_param('output', 'html', PARAM_ALPHA);
    $data->groupid = optional_param('group', '0', PARAM_ALPHA);
    $data->asxls = optional_param('asxls', '0', PARAM_BOOL); // Obsolete.
}

if (!$course = $DB->get_record('course', array('id' => $id))) {
    print_error('coursemisconf');
}

// Require appropriate rights.
$context = context_course::instance($course->id);
if (!has_capability('report/trainingsessions:viewother', $context, $USER->id)) {
    throw new Exception("User doesn't have rights to see this view");
}
$config = get_config('report_trainingsessions');

report_trainingsessions_process_bounds($data, $course);

// Compute target group.

$allgroupsaccess = has_capability('moodle/site:accessallgroups', $context);

if (!$allgroupsaccess) {
    $mygroups = groups_get_my_groups();

    $allowedgroupids = array();
    if ($mygroups) {
        foreach ($mygroups as $g) {
            $allowedgroupids[] = $g->id;
        }
        if (empty($data->groupid) || !in_array($data->groupid, $allowedgroupids)) {
            $data->groupid = $allowedgroupids[0];
        }
    } else {
        echo $OUTPUT->notification(get_string('errornotingroups', 'report_trainingsessions'));
        echo $OUTPUT->footer($course);
        die;
    }
} else {
    if ($allowedgroups = groups_get_all_groups($COURSE->id, $USER->id, 0, 'g.id,g.name')) {
        $allowedgroupids = array_keys($allowedgroups);
    }
}

if ($data->groupid) {
    $targetusers = get_enrolled_users($context, '', $data->groupid, 'u.*', 'u.lastname,u.firstname', 0, 0, $config->disablesuspendedenrolments);
} else {
    $targetusers = get_enrolled_users($context, '', 0, 'u.*', 'u.lastname,u.firstname', 0, 0, $config->disablesuspendedenrolments);
}

// Filter out non compiling users.
report_trainingsessions_filter_unwanted_users($targetusers, $course);

// Note: targetusers shoud default to array() if empty. Emptyness is considered later.

// Setup column list.
$namedcols = report_trainingsessions_get_summary_cols();
$durationcols = array('activitytime',
                      'equlearningtime',
                      'elapsed',
                      'extelapsed',
                      'extelapsedlastweek',
                      'extother',
                      'extotherlastweek',
                      'coursetime',
                      'elapsedlastweek',
                      'extelapsedlastweek');

// Get base data from moodle and bake it into a local format.
$courseid = $course->id;
$coursestructure = report_trainingsessions_get_course_structure($courseid, $items);
$coursename = $course->fullname;

// Initialize summary cols.
$colskeys = report_trainingsessions_get_summary_cols();
$colstitles = report_trainingsessions_get_summary_cols('title');
$colsformats = report_trainingsessions_get_summary_cols('format');

// Add potential additional grading cols.
report_trainingsessions_add_graded_columns($colskeys, $colstitles, $colsformats);

$summarizedusers = array();
foreach ($targetusers as $user) {

    // Get data from moodle.
    $logs = use_stats_extract_logs($data->from, $data->to, $user->id, $courseid);
    $aggregate = use_stats_aggregate_logs($logs, $data->from, $data->to);

    $weeklogs = use_stats_extract_logs($data->to - DAYSECS * 7, $data->to, array($user->id), $courseid);
    $weekaggregate = use_stats_aggregate_logs($weeklogs, $data->to - DAYSECS * 7, $data->to);

    @$aggregate['coursetotal'][$courseid]->items = $items;

    $elapsed = 0 + @$aggregate['coursetotal'][$course->id]->elapsed;

    $colsdata = report_trainingsessions_map_summary_cols($colskeys, $user, $aggregate, $weekaggregate, $courseid);

    // Fetch and add eventual additional score columns.
    report_trainingsessions_add_graded_data($colsdata, $user->id, $aggregate);

    // Assemble keys and data.
    if (!empty($colskeys)) {
        $userrow = array_combine($colskeys, $colsdata);
        $summarizedusers[] = $userrow;
    }
}

echo $OUTPUT->header();
echo $OUTPUT->container_start();
echo $renderer->tabs($course, $view, $data->from, $data->to);
echo $OUTPUT->container_end();

echo $OUTPUT->box_start('block');
$data->view = $view;
$selform->set_data($data);
$selform->display();
echo $OUTPUT->box_end();

echo get_string('from', 'report_trainingsessions')." : ".userdate($data->from);
echo ' '.get_string('to', 'report_trainingsessions')."  : ".userdate($data->to);

$config = get_config('report_trainingsessions');
if (!empty($config->showseconds)) {
    $durationformat = 'htmlds';
} else {
    $durationformat = 'htmld';
}

// Time and group period form.
echo '<br/>';

if (!empty($summarizedusers)) {
    echo '<table class="coursesummary" width="100%">';
    // Add a table header row.
    echo '<tr>';
    echo '<th></th>';

    foreach ($colstitles as $title) {
        echo '<th>'.$title.'</th>';
    }
    echo '</tr>';

    // Add a row for each user.
    $line = 1;
    foreach ($summarizedusers as $auser) {
        echo '<tr>';
        echo '<td>'.$line.'</td>';
        foreach ($auser as $fieldname => $field) {
            if (in_array($fieldname, $durationcols)) {
                $cssclass = 'report-col-right';
                echo '<td class="'.$cssclass.'">'.report_trainingsessions_format_time($field, $durationformat).'</td>';
            } else if (in_array($fieldname, $colskeys)) {
                // Those may come from grade columns.
                echo '<td>'.$field.'</td>';
            }
        }
        echo '</tr>';
        ++$line;
    }

    echo '</table>';
    echo '<br/>';

    echo '<center>';

    $params = array('id' => $course->id,
                    'groupid' => $data->groupid,
                    'from' => $data->from,
                    'to' => $data->to);
    $label = get_string('generatecsv', 'report_trainingsessions');
    $buttonurl = new moodle_url('/report/trainingsessions/tasks/groupcsvreportsummary_batch_task.php', $params);
    echo $OUTPUT->single_button($buttonurl, $label);

    // Add a 'generate XLS' button after the table.
    $params = array('id' => $course->id,
                    'groupid' => $data->groupid,
                    'from' => $data->from,
                    'to' => $data->to);
    $label = get_string('generatexls', 'report_trainingsessions');
    $buttonurl = new moodle_url('/report/trainingsessions/tasks/groupxlsreportsummary_batch_task.php', $params);
    echo $OUTPUT->single_button($buttonurl, $label);

    echo '</center>';
    echo '<br/>';
} else {
    echo $OUTPUT->notification(get_string('nothing', 'report_trainingsessions'));
}

